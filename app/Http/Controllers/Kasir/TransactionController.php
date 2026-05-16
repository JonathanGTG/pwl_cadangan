<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\IngredientStock;
use App\Models\Promotion;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $stocks = BranchStock::with(['menu.ingredients.ingredient'])
            ->where('branch_id', $branchId)
            ->whereHas('menu', fn($q) => $q->where('is_available', true))
            ->get()
            ->filter(function (BranchStock $bs) use ($branchId) {
                $menu = $bs->menu;

                if ($menu->isQuantityBased()) {
                    return $bs->stock > 0;
                }

                return $menu->checkIngredients($branchId, 1)['ok'];
            });

        $promotions = Promotion::where('is_active', true)
            ->where('review_status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->where(function ($q) use ($branchId) {
                $q->where('type', 'global')
                    ->orWhere('branch_id', $branchId);
            })
            ->get();

        $todayTransactions = Transaction::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->with('items')
            ->latest()
            ->get();

        return view('kasir.transactions.index',
            compact('stocks', 'promotions', 'todayTransactions'));
    }

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
                $subtotal = 0;
                $itemsData = [];
                $stockRequirements = [];
                $ingredientRequirements = [];

                foreach ($request->items as $item) {
                    $branchStock = BranchStock::with(['menu.ingredients.ingredient'])
                        ->where('branch_id', $branchId)
                        ->whereKey($item['menu_stock_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $menu = $branchStock->menu;
                    $qty = (int) $item['quantity'];

                    if (!$menu || !$menu->is_available) {
                        throw new \Exception('Menu tidak tersedia untuk cabang ini.');
                    }

                    if ($menu->isQuantityBased()) {
                        $stockRequirements[$branchStock->id]['stock'] = $branchStock;
                        $stockRequirements[$branchStock->id]['qty'] =
                            ($stockRequirements[$branchStock->id]['qty'] ?? 0) + $qty;
                    } else {
                        if ($menu->ingredients->isEmpty()) {
                            throw new \Exception("Resep bahan baku {$menu->name} belum diatur.");
                        }

                        foreach ($menu->ingredients as $menuIngredient) {
                            $ingredientId = $menuIngredient->ingredient_id;
                            $ingredientRequirements[$ingredientId]['ingredient'] = $menuIngredient->ingredient;
                            $ingredientRequirements[$ingredientId]['amount'] =
                                ($ingredientRequirements[$ingredientId]['amount'] ?? 0)
                                + ($menuIngredient->jumlah_per_sajian * $qty);
                        }
                    }

                    $price = $branchStock->custom_price ?? $menu->base_price;
                    $itemSubtotal = $price * $qty;
                    $subtotal += $itemSubtotal;

                    $itemsData[] = [
                        'menu' => $menu,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                        'recipe_snapshot' => $menu->isIngredientBased()
                            ? $menu->ingredients->map(fn($mi) => [
                                'ingredient_id' => $mi->ingredient_id,
                                'ingredient_name' => $mi->ingredient?->nama_bahan,
                                'unit' => $mi->ingredient?->satuan,
                                'amount' => (float) $mi->jumlah_per_sajian,
                            ])->values()->all()
                            : null,
                    ];
                }

                foreach ($stockRequirements as $requirement) {
                    if ($requirement['stock']->stock < $requirement['qty']) {
                        throw new \Exception(
                            "Stok {$requirement['stock']->menu->name} tidak cukup! Tersisa: {$requirement['stock']->stock} pcs"
                        );
                    }
                }

                $lockedIngredientStocks = [];
                $kekurangan = [];

                foreach ($ingredientRequirements as $ingredientId => $requirement) {
                    $stock = IngredientStock::where('branch_id', $branchId)
                        ->where('ingredient_id', $ingredientId)
                        ->lockForUpdate()
                        ->first();

                    $available = $stock?->stok_sekarang ?? 0;
                    if ($available < $requirement['amount']) {
                        $ingredient = $requirement['ingredient'];
                        $kekurangan[] = "{$ingredient?->nama_bahan}: butuh {$requirement['amount']} {$ingredient?->satuan}, tersedia {$available} {$ingredient?->satuan}";
                    }

                    if ($stock) {
                        $lockedIngredientStocks[$ingredientId] = $stock;
                    }
                }

                if (!empty($kekurangan)) {
                    throw new \Exception('Bahan baku tidak cukup! ' . implode('; ', $kekurangan));
                }

                $discountAmount = 0;
                $promotionId = null;

                if ($request->promotion_id) {
                    $promo = Promotion::whereKey($request->promotion_id)
                        ->where('is_active', true)
                        ->where('review_status', 'approved')
                        ->where('start_date', '<=', today())
                        ->where('end_date', '>=', today())
                        ->where(function ($q) use ($branchId) {
                            $q->where('type', 'global')
                                ->orWhere('branch_id', $branchId);
                        })
                        ->first();

                    if ($promo && $promo->is_valid) {
                        $discountAmount = $promo->calculateDiscount($subtotal);
                        $promotionId = $promo->id;
                    }
                }

                $total = max(0, $subtotal - $discountAmount);
                $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' .
                    str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

                $transaction = Transaction::create([
                    'invoice_number' => $invoiceNumber,
                    'branch_id' => $branchId,
                    'kasir_id' => auth()->id(),
                    'promotion_id' => $promotionId,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                ]);

                foreach ($itemsData as $itemData) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'menu_id' => $itemData['menu']->id,
                        'menu_name' => $itemData['menu']->name,
                        'price' => $itemData['price'],
                        'quantity' => $itemData['quantity'],
                        'subtotal' => $itemData['subtotal'],
                        'recipe_snapshot' => $itemData['recipe_snapshot'],
                    ]);
                }

                foreach ($stockRequirements as $requirement) {
                    $requirement['stock']->decrement('stock', $requirement['qty']);
                }

                foreach ($ingredientRequirements as $ingredientId => $requirement) {
                    $lockedIngredientStocks[$ingredientId]->decrement('stok_sekarang', $requirement['amount']);
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
            'success' => true,
            'message' => 'Transaksi berhasil!',
            'invoice' => $transactionResult->invoice_number,
            'transaction_id' => $transactionResult->id,
        ]);
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $transaction->load('items', 'promotion', 'kasir');
        return view('kasir.transactions.show', compact('transaction'));
    }

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
