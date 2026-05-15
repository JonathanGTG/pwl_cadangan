<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;

class StockController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $stocks = BranchStock::with('menu')
                             ->where('branch_id', $branchId)
                             ->latest()
                             ->get();

        return view('admin.stocks.index', compact('stocks'));
    }
}