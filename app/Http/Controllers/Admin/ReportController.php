<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\BranchStock;
use Illuminate\Http\Request;
use DateTime;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);

        // ── Grafik Pemasukan & Pengeluaran 12 Bulan ──────────
        $incomeChart  = [];
        $expenseChart = [];
        $labels       = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = \DateTime::createFromFormat('!m', $m)->format('M');

            $incomeChart[] = (float) Transaction::where('branch_id', $branchId)
                                                ->where('status', 'completed')
                                                ->whereYear('created_at', $year)
                                                ->whereMonth('created_at', $m)
                                                ->sum('total');

            $expenseChart[] = (float) Expense::where('branch_id', $branchId)
                                             ->where('status', 'verified')
                                             ->whereYear('expense_date', $year)
                                             ->whereMonth('expense_date', $m)
                                             ->sum('amount');
        }

        // ── Summary Bulan Ini ─────────────────────────────────
        $totalIncome = Transaction::where('branch_id', $branchId)
                                  ->where('status', 'completed')
                                  ->whereMonth('created_at', $month)
                                  ->whereYear('created_at', $year)
                                  ->sum('total');

        $totalExpense = Expense::where('branch_id', $branchId)
                               ->where('status', 'verified')
                               ->whereMonth('expense_date', $month)
                               ->whereYear('expense_date', $year)
                               ->sum('amount');

        $totalProfit      = $totalIncome - $totalExpense;
        $totalTransaction = Transaction::where('branch_id', $branchId)
                                       ->where('status', 'completed')
                                       ->whereMonth('created_at', $month)
                                       ->whereYear('created_at', $year)
                                       ->count();

        // ── Transaksi Bulan Ini ───────────────────────────────
        $transactions = Transaction::where('branch_id', $branchId)
                                   ->where('status', 'completed')
                                   ->whereMonth('created_at', $month)
                                   ->whereYear('created_at', $year)
                                   ->with('kasir', 'items')
                                   ->latest()
                                   ->get();

        // ── Pengeluaran Per Kategori ──────────────────────────
        $expenseByCategory = Expense::where('branch_id', $branchId)
                                    ->where('status', 'verified')
                                    ->whereMonth('expense_date', $month)
                                    ->whereYear('expense_date', $year)
                                    ->selectRaw('category, SUM(amount) as total')
                                    ->groupBy('category')
                                    ->get();

        // ── Stok Kritis ───────────────────────────────────────
        $criticalStocks = BranchStock::where('branch_id', $branchId)
                                     ->where('stock', '<=', 5)
                                     ->with('menu')
                                     ->get();

        return view('admin.reports.index', compact(
            'month', 'year', 'branchId',
            'labels', 'incomeChart', 'expenseChart',
            'totalIncome', 'totalExpense', 'totalProfit', 'totalTransaction',
            'transactions', 'expenseByCategory', 'criticalStocks'
        ));
    }

    public function export(\Illuminate\Http\Request $request)
    {
        $branchId  = auth()->user()->branch_id;
        $month     = $request->get('month', now()->month);
        $year      = $request->get('year', now()->year);
        $monthName = \DateTime::createFromFormat('!m', $month)->format('F');

        $transactions = \App\Models\Transaction::where('branch_id', $branchId)
                        ->where('status', 'completed')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->with('kasir')
                        ->latest()
                        ->get();

        $expenses = \App\Models\Expense::where('branch_id', $branchId)
                    ->whereMonth('expense_date', $month)
                    ->whereYear('expense_date', $year)
                    ->with('createdBy')
                    ->latest()
                    ->get();

        $filename = "Laporan_ELCO_{$monthName}_{$year}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($transactions, $expenses, $monthName, $year) {
            $file = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca
            fputs($file, "\xEF\xBB\xBF");

            // ── PEMASUKAN ─────────────────────────────────────────
            fputcsv($file, ["LAPORAN PEMASUKAN ELCO - {$monthName} {$year}"]);
            fputcsv($file, ['No', 'Invoice', 'Kasir', 'Subtotal', 'Diskon', 'Total', 'Metode Bayar', 'Tanggal']);

            $no          = 1;
            $totalIncome = 0;

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $no++,
                    $trx->invoice_number,
                    $trx->kasir->name ?? '-',
                    $trx->subtotal,
                    $trx->discount_amount,
                    $trx->total,
                    strtoupper($trx->payment_method),
                    $trx->created_at->format('d/m/Y H:i'),
                ]);
                $totalIncome += $trx->total;
            }

            fputcsv($file, ['', '', '', '', 'TOTAL', $totalIncome, '', '']);
            fputcsv($file, []); // baris kosong pemisah

            // ── PENGELUARAN ───────────────────────────────────────
            fputcsv($file, ["LAPORAN PENGELUARAN ELCO - {$monthName} {$year}"]);
            fputcsv($file, ['No', 'Judul', 'Kategori', 'Jumlah', 'Tanggal', 'Status', 'Dicatat Oleh']);

            $no           = 1;
            $totalExpense = 0;

            foreach ($expenses as $exp) {
                fputcsv($file, [
                    $no++,
                    $exp->title,
                    ucfirst($exp->category),
                    $exp->amount,
                    $exp->expense_date->format('d/m/Y'),
                    ucfirst($exp->status),
                    $exp->createdBy->name ?? '-',
                ]);
                $totalExpense += $exp->amount;
            }

            fputcsv($file, ['', '', 'TOTAL', $totalExpense, '', '', '']);
            fputcsv($file, []); // baris kosong

            // ── RINGKASAN ─────────────────────────────────────────
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Pemasukan', $totalIncome]);
            fputcsv($file, ['Total Pengeluaran', $totalExpense]);
            fputcsv($file, ['Laba Bersih', $totalIncome - $totalExpense]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}