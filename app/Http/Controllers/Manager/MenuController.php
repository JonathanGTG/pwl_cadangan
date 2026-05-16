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

<<<<<<< HEAD
        DB::transaction(function () use ($request, $menu, $stockType) {
            $menu->update([
                'name'         => $request->name,
                'description'  => $request->description,
                'category'     => $request->category,
                'stock_type'   => $stockType,
                'base_price'   => $request->base_price,
                'image'        => $menu->image,
                'is_available' => $request->has('is_available'),
            ]);

            if ($stockType === 'bahan_baku') {
                $menu->ingredients()->delete();

                foreach ($request->ingredients ?? [] as $ing) {
                    if (!empty($ing['ingredient_id']) && !empty($ing['jumlah'])) {
                        MenuIngredient::create([
                            'menu_id'           => $menu->id,
                            'ingredient_id'     => $ing['ingredient_id'],
                            'jumlah_per_sajian' => $ing['jumlah'],
                        ]);
                    }
                }

                BranchStock::where('menu_id', $menu->id)->update(['stock' => 0]);

                $branches = Branch::where('status', 'active')->get();
                foreach ($branches as $branch) {
                    BranchStock::firstOrCreate(
                        ['branch_id' => $branch->id, 'menu_id' => $menu->id],
                        ['stock' => 0, 'custom_price' => null]
                    );

                    foreach ($request->ingredients ?? [] as $ing) {
                        if (empty($ing['ingredient_id'])) continue;
                        IngredientStock::firstOrCreate(
                            ['branch_id' => $branch->id, 'ingredient_id' => $ing['ingredient_id']],
                            ['stok_sekarang' => 0, 'stok_minimum' => 0]
                        );
                    }
                }
            } else {
                $menu->ingredients()->delete();

                foreach (Branch::where('status', 'active')->get() as $branch) {
                    BranchStock::firstOrCreate(
                        ['branch_id' => $branch->id, 'menu_id' => $menu->id],
                        ['stock' => 0, 'custom_price' => null]
                    );
                }
            }
        });

        return redirect()->route('manager.menus.index')
                         ->with('success', 'Menu berhasil diperbarui!');
    }

    // Soft delete menu
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('manager.menus.index')
                         ->with('success', 'Menu berhasil dinonaktifkan!');
    }

    // Restore menu
    public function restore($id)
    {
        Menu::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('manager.menus.index')
                         ->with('success', 'Menu berhasil dipulihkan!');
    }

    // ── CRUD Ingredient (bahan baku) ─────────────────────────────────────────

    public function ingredients()
    {
        $ingredients = Ingredient::withCount('menuIngredients')->latest()->get();
        return view('manager.menus.ingredients', compact('ingredients'));
    }

    public function storeIngredient(Request $request)
    {
        $request->validate([
            'kode_bahan'  => 'required|string|unique:ingredients,kode_bahan',
            'nama_bahan'  => 'required|string|max:100',
            'kategori'    => 'nullable|string|max:50',
            'satuan'      => 'required|in:gram,ml,pcs',
=======
        $expense->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
>>>>>>> 543e73fdda09999701ee2972dad8b0b554fefeef
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