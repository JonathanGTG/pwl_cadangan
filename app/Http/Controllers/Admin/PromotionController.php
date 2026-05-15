<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $branchPromos = Promotion::where('branch_id', $branchId)
                                ->with('createdBy')
                                ->latest()->get();

        $globalPromos = Promotion::where('type', 'global')
                                ->where('is_active', true)
                                ->where('review_status', 'approved')
                                ->latest()->get();

        return view('admin.promotions.index', compact('branchPromos', 'globalPromos'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase'   => 'nullable|numeric|min:0',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        if ($request->discount_type === 'percentage' && $request->discount_value > 100) {
            return back()->withErrors(['discount_value' => 'Diskon persentase tidak boleh lebih dari 100%'])
                        ->withInput();
        }

        Promotion::create([
            'branch_id'      => auth()->user()->branch_id,
            'created_by'     => auth()->id(),
            'name'           => $request->name,
            'description'    => $request->description,
            'type'           => 'branch',
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_purchase'   => $request->min_purchase ?? 0,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => false,        // ← nonaktif dulu
            'review_status'  => 'pending',    // ← menunggu review
        ]);

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promo dikirim ke Manager Pusat untuk ditinjau!');
    }

    public function destroy(Promotion $promotion)
    {
        // Admin hanya bisa hapus promo cabangnya sendiri
        if ($promotion->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $promotion->delete();
        return back()->with('success', 'Promo berhasil dihapus!');
    }
}