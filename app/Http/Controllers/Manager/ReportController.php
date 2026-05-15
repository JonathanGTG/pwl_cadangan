<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\StockRequest;
use Illuminate\Http\Request;
use DateTime;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);
        $branchId = $request->get('branch_id');

        // ── Data Grafik Pemasukan 12 Bulan Terakhir ──────────
        $incomeChart = [];
        $expenseChart = [];
        $labels = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = \DateTime::createFromFormat('!m', $m)->format('M');

            $q = Transaction::where('status', 'completed')
                            ->whereYear('created_at', $year)
                            ->whereMonth('created_at', $m);

            $qe = Expense::where('status', 'verified')
                         ->whereYear('expense_date', $year)
                         ->whereMonth('expense_date', $m);

            if ($branchId) {
                $q->where('branch_id', $branchId);
                $qe->where('branch_id', $branchId);
            }

            $incomeChart[]  = (float) $q->sum('total');
            $expenseChart[] = (float) $qe->sum('amount');
        }

        // ── Summary Bulan Ini ─────────────────────────────────
        $incomeQuery = Transaction::where('status', 'completed')
                                  ->whereMonth('created_at', $month)
                                  ->whereYear('created_at', $year);

        $expenseQuery = Expense::where('status', 'verified')
                               ->whereMonth('expense_date', $month)
                               ->whereYear('expense_date', $year);

        if ($branchId) {
            $incomeQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
        }

        $totalIncome      = $incomeQuery->sum('total');
        $totalExpense     = $expenseQuery->sum('amount');
        $totalProfit      = $totalIncome - $totalExpense;
        $totalTransaction = $incomeQuery->count();

        // ── Performa Per Cabang ───────────────────────────────
        $branchPerformance = Branch::where('status', 'active')
            ->get()
            ->map(function ($branch) use ($month, $year) {
                $income  = Transaction::where('branch_id', $branch->id)
                                      ->where('status', 'completed')
                                      ->whereMonth('created_at', $month)
                                      ->whereYear('created_at', $year)
                                      ->sum('total');

                $expense = Expense::where('branch_id', $branch->id)
                                  ->where('status', 'verified')
                                  ->whereMonth('expense_date', $month)
                                  ->whereYear('expense_date', $year)
                                  ->sum('amount');

                $trxCount = Transaction::where('branch_id', $branch->id)
                                       ->where('status', 'completed')
                                       ->whereMonth('created_at', $month)
                                       ->whereYear('created_at', $year)
                                       ->count();

                return [
                    'name'    => $branch->name,
                    'income'  => $income,
                    'expense' => $expense,
                    'profit'  => $income - $expense,
                    'trx'     => $trxCount,
                ];
            })
            ->sortByDesc('income');

        $branches = Branch::where('status', 'active')->get();

        return view('manager.reports.index', compact(
            'month', 'year', 'branchId', 'branches',
            'labels', 'incomeChart', 'expenseChart',
            'totalIncome', 'totalExpense', 'totalProfit', 'totalTransaction',
            'branchPerformance'
        ));
    }
}