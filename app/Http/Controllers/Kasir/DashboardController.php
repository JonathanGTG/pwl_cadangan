<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $data = [
            // Menu tersedia di cabang ini
            'available_menus' => BranchStock::where('branch_id', $branchId)
                                            ->where('stock', '>', 0)
                                            ->with('menu')
                                            ->get(),

            // Transaksi hari ini oleh kasir ini
            'today_transactions' => Transaction::where('kasir_id', auth()->id())
                                               ->whereDate('created_at', today())
                                               ->count(),

            // Total penjualan hari ini oleh kasir ini
            'today_total'     => Transaction::where('kasir_id', auth()->id())
                                            ->where('status', 'completed')
                                            ->whereDate('created_at', today())
                                            ->sum('total'),
        ];

        return view('kasir.dashboard', compact('data'));
    }
}