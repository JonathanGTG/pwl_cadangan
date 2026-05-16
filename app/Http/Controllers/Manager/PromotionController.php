<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('branch', 'createdBy')
                               ->latest()
                               ->get();

        return view('manager.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('manager.promotions.create');
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

        Promotion::create([
            'branch_id'      => null,           // null = global
            'created_by'     => auth()->id(),
            'name'           => $request->name,
            'description'    => $request->description,
            'type'           => 'global',
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_purchase'   => $request->min_purchase ?? 0,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => true,
        ]);

        return redirect()->route('manager.promotions.index')
                         ->with('success', 'Promo global berhasil dibuat!');
    }

    public function edit(Promotion $promotion)
    {
        return view('manager.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
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

        $promotion->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_purchase'   => $request->min_purchase ?? 0,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => $request->has('is_active'),
        ]);

        return redirect()->route('manager.promotions.index')
                         ->with('success', 'Promo berhasil diperbarui!');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return back()->with('success', 'Promo berhasil dihapus!');
    }
}