<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);
        $branchId = $request->get('branch_id');

        $query = Expense::with('branch', 'createdBy', 'verifiedBy')
                        ->whereMonth('expense_date', $month)
                        ->whereYear('expense_date', $year);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $expenses = $query->latest()->get();
        $branches = Branch::where('status', 'active')->get();

        $summary = [
            'total'    => $expenses->sum('amount'),
            'pending'  => $expenses->where('status', 'pending')->count(),
            'verified' => $expenses->where('status', 'verified')->sum('amount'),
        ];

        return view('manager.expenses.index',
            compact('expenses', 'branches', 'summary', 'month', 'year', 'branchId'));
    }

    public function verify(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Pengeluaran ini sudah diproses!');
        }

        $expense->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil diverifikasi!');
    }

    public function reject(Request $request, Expense $expense)
    {
        $request->validate([
            'rejection_note' => 'required|string|min:5',
        ]);

        $expense->update([
            'status'      => 'rejected',
            'verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil ditolak!');
    }
}