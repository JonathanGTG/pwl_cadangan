<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\IngredientStock;

class StockController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $stocks = BranchStock::with('menu')
                             ->where('branch_id', $branchId)
                             ->whereHas('menu', fn($q) => $q->where('stock_type', 'kuantitas_jadi'))
                             ->latest()
                             ->get();

        $ingredientStocks = IngredientStock::with('ingredient')
            ->where('branch_id', $branchId)
            ->latest()
            ->get();

        return view('admin.stocks.index', compact('stocks', 'ingredientStocks'));
    }
}
