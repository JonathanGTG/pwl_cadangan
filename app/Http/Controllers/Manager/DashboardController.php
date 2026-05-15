<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\StockRequest;
use App\Models\Expense;
use App\Models\Promotion;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        $data = [
            'total_branches'   => Branch::where('status', 'active')->count(),
            'total_menus'      => \App\Models\Menu::where('is_available', true)->count(),
            'pending_requests' => StockRequest::where('status', 'pending')->count(),
            'total_expense'    => Expense::whereMonth('expense_date', $now->month)
                                         ->whereYear('expense_date', $now->year)
                                         ->sum('amount'),
            'total_income'     => Transaction::where('status', 'completed')
                                             ->whereMonth('created_at', $now->month)
                                             ->whereYear('created_at', $now->year)
                                             ->sum('total'),

            // Pengajuan stok pending terbaru
            'latest_requests'  => StockRequest::with('branch', 'requestedBy')
                                              ->where('status', 'pending')
                                              ->latest()
                                              ->take(5)
                                              ->get(),

            // Aktivitas cabang dari database
            'branch_activities' => Branch::where('status', 'active')
                                         ->get()
                                         ->map(function ($branch) use ($now) {
                                             return [
                                                 'id'     => $branch->id,
                                                 'name'   => $branch->name,
                                                 'address'=> $branch->address,
                                                 'income' => Transaction::where('branch_id', $branch->id)
                                                                        ->where('status', 'completed')
                                                                        ->whereDate('created_at', today())
                                                                        ->sum('total'),
                                                 'trx'    => Transaction::where('branch_id', $branch->id)
                                                                        ->whereDate('created_at', today())
                                                                        ->count(),
                                             ];
                                         }),

            // Promo aktif
            'active_promos' => Promotion::where('is_active', true)
                                        ->where('start_date', '<=', today())
                                        ->where('end_date', '>=', today())
                                        ->latest()
                                        ->take(3)
                                        ->get(),
        ];

        return view('manager.dashboard', compact('data'));
    }
}