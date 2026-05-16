<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\BranchStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);

        $transactions = Transaction::where('branch_id', $branchId)
                                   ->whereMonth('created_at', $month)
                                   ->whereYear('created_at', $year)
                                   ->with('kasir', 'items', 'promotion')
                                   ->latest()
                                   ->get();

        $summary = [
            'total_income'       => $transactions->where('status', 'completed')->sum('total'),
            'total_transactions' => $transactions->where('status', 'completed')->count(),
            'cancelled'          => $transactions->where('status', 'cancelled')->count(),
        ];

        return view('admin.transactions.index',
            compact('transactions', 'summary', 'month', 'year'));
    }

    /**
     * Batalkan transaksi — disetujui oleh Admin Cabang.
     *
     * Logika pengembalian stok:
     *  - Minuman (bahan_baku)   → kembalikan bahan baku ke ingredient_stocks
     *  - Makanan/snack (qty)    → kembalikan pcs ke branch_stocks
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        $request->validate([
            'cancel_reason' => 'required|string|min:5',
        ]);

        if ($transaction->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $canCancel = $transaction->status === 'pending' ||
            ($transaction->status === 'completed' &&
             str_starts_with($transaction->cancel_reason ?? '', '[REQUEST CANCEL]'));

        if (!$canCancel) {
            return back()->with('error', 'Transaksi tidak dapat dibatalkan!');
        }

        DB::transaction(function () use ($request, $transaction) {
            foreach ($transaction->items as $item) {
                // Muat menu beserta resepnya
                $menu = $item->menu()->with('ingredients.ingredient')->first();

                if (!$menu) {
                    continue;
                }

                if ($menu->isQuantityBased()) {
                    // Makanan / snack: kembalikan stok pcs
                    BranchStock::where('branch_id', $transaction->branch_id)
                        ->where('menu_id', $item->menu_id)
                        ->increment('stock', $item->quantity);
                } else {
                    // Minuman: kembalikan bahan baku
                    $menu->restoreIngredients($transaction->branch_id, $item->quantity);
                }
            }

            $transaction->update([
                'status'       => 'cancelled',
                'cancel_reason' => $request->cancel_reason,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
            ]);
        });

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan!');
    }

    public function rejectCancel(Transaction $transaction)
    {
        if ($transaction->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $isRequestCancel = $transaction->status === 'completed' &&
            str_starts_with($transaction->cancel_reason ?? '', '[REQUEST CANCEL]');

        if (!$isRequestCancel) {
            return back()->with('error', 'Permintaan pembatalan tidak ditemukan!');
        }

        $transaction->update([
            'cancel_reason' => null,
        ]);

        return back()->with('success', 'Permintaan pembatalan ditolak. Transaksi tetap selesai.');
    }
}
