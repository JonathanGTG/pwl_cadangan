<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Branch;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);
        $branchId = $request->get('branch_id');

        $query = Transaction::with('branch', 'kasir', 'items', 'promotion')
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', $year);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $transactions = $query->latest()->paginate(15);
        $branches     = Branch::where('status', 'active')->get();

        $summary = [
            'total_income'       => $query->clone()->where('status', 'completed')->sum('total'),
            'total_transactions' => $query->clone()->where('status', 'completed')->count(),
            'cancelled'          => $query->clone()->where('status', 'cancelled')->count(),
        ];

        return view('manager.transactions.index',
            compact('transactions', 'branches', 'summary', 'month', 'year', 'branchId'));
    }
}