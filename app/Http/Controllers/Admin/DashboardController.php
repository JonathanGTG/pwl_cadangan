<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Transaction;
use App\Models\StockRequest;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $data = [
            // Total stok menu di cabang ini
            'total_stocks'     => BranchStock::where('branch_id', $branchId)->count(),

            // Stok yang hampir habis (di bawah 5)
            'low_stocks'       => BranchStock::where('branch_id', $branchId)
                                             ->where('stock', '<=', 5)
                                             ->count(),

            // Pengajuan pending cabang ini
            'pending_requests' => StockRequest::where('branch_id', $branchId)
                                              ->where('status', 'pending')
                                              ->count(),

            // Pengeluaran cabang bulan ini
            'total_expense'    => Expense::where('branch_id', $branchId)
                                         ->whereMonth('expense_date', now()->month)
                                         ->whereYear('expense_date', now()->year)
                                         ->sum('amount'),

            // Pemasukan cabang bulan ini
            'total_income'     => Transaction::where('branch_id', $branchId)
                                             ->where('status', 'completed')
                                             ->whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year)
                                             ->sum('total'),

            // 5 transaksi terbaru cabang
            'latest_transactions' => Transaction::where('branch_id', $branchId)
                                                ->with('kasir')
                                                ->latest()
                                                ->take(5)
                                                ->get(),
        ];

        return view('admin.dashboard', compact('data'));
    }
}