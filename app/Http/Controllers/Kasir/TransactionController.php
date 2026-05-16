<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\BranchStock;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ── Halaman POS utama ─────────────────────────────────────────────────────

    public function index()
    {
        $branchId = auth()->user()->branch_id;

        /**
         * Untuk minuman (bahan_baku): tampilkan menu yang bahan-bahannya
         * masih mencukupi minimal 1 porsi.
         * Untuk makanan/snack (kuantitas_jadi): tampilkan yang stok pcs > 0.
         * BranchStock.stock untuk minuman dikosongkan (tidak dipakai),
         * untuk makanan dipakai sebagai jumlah pcs.
         */
        $stocks = BranchStock::with(['menu.ingredients.ingredient'])
            ->where('branch_id', $branchId)
            ->whereHas('menu', fn($q) => $q->where('is_available', true))
            ->get()
            ->filter(function (BranchStock $bs) use ($branchId) {
                $menu = $bs->menu;

                if ($menu->isQuantityBased()) {
                    // Makanan/snack: cukup cek stok pcs
                    return $bs->stock > 0;
                }

                // Minuman: cek ketersediaan bahan baku (minimal 1 porsi)
                return $menu->checkIngredients($branchId, 1)['ok'];
            });

        // Promo aktif (global + cabang)
        $promotions = Promotion::where('is_active', true)
                               ->where('start_date', '<=', today())
                               ->where('end_date', '>=', today())
                               ->where(function ($q) use ($branchId) {
                                   $q->where('type', 'global')
                                     ->orWhere('branch_id', $branchId);
                               })
                               ->get();

        // Riwayat transaksi hari ini
        $todayTransactions = Transaction::where('branch_id', $branchId)
                                         ->whereDate('created_at', today())
                                         ->with('items')
                                         ->latest()
                                         ->get();

        return view('kasir.transactions.index',
            compact('stocks', 'promotions', 'todayTransactions'));
    }

    // ── Proses transaksi ──────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.menu_stock_id' => 'required|exists:branch_stocks,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'payment_method'        => 'required|in:cash,transfer,qris',
            'promotion_id'          => 'nullable|exists:promotions,id',
        ]);

        $branchId          = auth()->user()->branch_id;
        $transactionResult = null;

        try {
            DB::transaction(function () use ($request, $branchId, &$transactionResult) {
                $subtotal  = 0;
                $itemsData = [];

                foreach ($request->items as $item) {
                    $branchStock = BranchStock::with(['menu.ingredients.ingredient'])
                        ->findOrFail($item['menu_stock_id']);

                    if ((int) $branchStock->branch_id !== (int) $branchId) {
                        throw new \Exception('Menu tidak tersedia untuk cabang ini.');
                    }

                    $menu = $branchStock->menu;
                    $qty  = $item['quantity'];

                    // ── Cek ketersediaan stok ──────────────────────────────
                    if ($menu->isQuantityBased()) {
                        // Makanan / snack → cek stok pcs
                        if ($branchStock->stock < $qty) {
                            throw new \Exception(
                                "Stok {$menu->name} tidak cukup! Tersisa: {$branchStock->stock} pcs"
                            );
                        }
                    } else {
                        // Minuman → cek bahan baku
                        $check = $menu->checkIngredients($branchId, $qty);
                        if (!$check['ok']) {
                            $detail = collect($check['kekurangan'])
                                ->map(fn($k) => "{$k['bahan']}: butuh {$k['dibutuhkan']} {$k['satuan']}, tersedia {$k['tersedia']} {$k['satuan']}")
                                ->implode('; ');
                            throw new \Exception(
                                "Bahan baku {$menu->name} tidak cukup! {$detail}"
                            );
                        }
                    }

                    $price        = $branchStock->custom_price ?? $menu->base_price;
                    $itemSubtotal = $price * $qty;
                    $subtotal    += $itemSubtotal;

                    $itemsData[] = [
                        'branch_stock' => $branchStock,
                        'menu'         => $menu,
                        'quantity'     => $qty,
                        'price'        => $price,
                        'subtotal'     => $itemSubtotal,
                    ];
                }

                // ── Promo ─────────────────────────────────────────────────
                $discountAmount = 0;
                $promotionId    = null;

                if ($request->promotion_id) {
                    $promo = Promotion::find($request->promotion_id);
                    if ($promo && $promo->is_valid) {
                        $discountAmount = $promo->calculateDiscount($subtotal);
                        $promotionId    = $promo->id;
                    }
                }

                $total         = $subtotal - $discountAmount;
                $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' .
                    str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

                $transaction = Transaction::create([
                    'invoice_number'  => $invoiceNumber,
                    'branch_id'       => $branchId,
                    'kasir_id'        => auth()->id(),
                    'promotion_id'    => $promotionId,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total'           => $total,
                    'payment_method'  => $request->payment_method,
                    'status'          => 'completed',
                ]);

                foreach ($itemsData as $itemData) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'menu_id'        => $itemData['menu']->id,
                        'menu_name'      => $itemData['menu']->name,
                        'price'          => $itemData['price'],
                        'quantity'       => $itemData['quantity'],
                        'subtotal'       => $itemData['subtotal'],
                    ]);

                    $menu = $itemData['menu'];
                    $qty  = $itemData['quantity'];

                    if ($menu->isQuantityBased()) {
                        // Makanan/snack: kurangi stok pcs
                        $itemData['branch_stock']->decrement('stock', $qty);
                    } else {
                        // Minuman: kurangi bahan baku
                        $menu->deductIngredients($branchId, $qty);
                    }
                }

                $transactionResult = $transaction;
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Transaksi berhasil!',
            'invoice'        => $transactionResult->invoice_number,
            'transaction_id' => $transactionResult->id,
        ]);
    }

    // ── Detail transaksi ──────────────────────────────────────────────────────

    public function show(Transaction $transaction)
    {
        $transaction->load('items', 'promotion', 'kasir');
        return view('kasir.transactions.show', compact('transaction'));
    }

    // ── Request pembatalan oleh kasir ─────────────────────────────────────────

    public function requestCancel(Request $request, Transaction $transaction)
    {
        $request->validate([
            'cancel_reason' => 'required|string|min:5',
        ]);

        if ($transaction->kasir_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak bisa dibatalkan!',
            ], 422);
        }

        if ($transaction->created_at->diffInMinutes(now()) > 30) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pembatalan (30 menit) telah lewat!',
            ], 422);
        }

        $transaction->update([
            'cancel_reason' => '[REQUEST CANCEL] ' . $request->cancel_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pembatalan dikirim ke Admin!',
        ]);
    }
}
