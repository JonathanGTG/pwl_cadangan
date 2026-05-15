<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $expenses = Expense::where('branch_id', $branchId)
                           ->with('createdBy', 'verifiedBy')
                           ->latest()
                           ->get();

        $summary = [
            'total'    => Expense::where('branch_id', $branchId)
                                 ->whereMonth('expense_date', now()->month)
                                 ->sum('amount'),
            'pending'  => Expense::where('branch_id', $branchId)
                                 ->where('status', 'pending')->count(),
            'verified' => Expense::where('branch_id', $branchId)
                                 ->where('status', 'verified')->count(),
        ];

        return view('admin.expenses.index', compact('expenses', 'summary'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:100',
            'description'  => 'nullable|string',
            'category'     => 'required|in:operasional,bahan_baku,peralatan,gaji,lainnya',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'receipt'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create([
            'branch_id'    => auth()->user()->branch_id,
            'created_by'   => auth()->id(),
            'title'        => $request->title,
            'description'  => $request->description,
            'category'     => $request->category,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt'      => $receiptPath,
            'status'       => 'pending',
        ]);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Pengeluaran yang sudah diverifikasi tidak dapat dihapus!');
        }

        $expense->delete();
        return back()->with('success', 'Pengeluaran berhasil dihapus!');
    }
}