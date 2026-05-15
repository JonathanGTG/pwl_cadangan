<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\Ingredient;
use App\Models\Menu;
use Illuminate\Http\Request;

class StockRequestController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $requests = StockRequest::where('branch_id', $branchId)
                                ->with('requestedBy', 'verifiedBy')
                                ->latest()
                                ->get();

        return view('admin.stock-requests.index', compact('requests'));
    }

    public function create()
    {
        // Bahan baku untuk minuman (bahan_baku based)
        $ingredients = Ingredient::orderBy('nama_bahan')->get();

        // Menu makanan & snack (kuantitas_jadi based)
        $produkJadi = Menu::whereIn('category', ['makanan', 'snack'])
                          ->where('is_available', true)
                          ->orderBy('name')
                          ->get();

        return view('admin.stock-requests.create', compact('ingredients', 'produkJadi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'            => 'required|in:stock,operational',
            'item_name'       => 'required|string|max:150',
            'unit'            => 'nullable|string|max:20',
            'quantity'        => 'required|numeric|min:1',
            'reason'          => 'nullable|string',
            'stock_item_type' => 'nullable|in:bahan_baku,produk_jadi',
        ]);

        StockRequest::create([
            'branch_id'       => auth()->user()->branch_id,
            'requested_by'    => auth()->id(),
            'type'            => $request->type,
            'stock_item_type' => $request->type === 'stock' ? $request->stock_item_type : null,
            'item_name'       => $request->item_name,
            'unit'            => $request->unit,
            'quantity'        => $request->quantity,
            'reason'          => $request->reason,
            'status'          => 'pending',
        ]);

        return redirect()->route('admin.stock-requests.index')
                         ->with('success', 'Pengajuan berhasil dikirim ke Manager Pusat!');
    }

    /**
     * Admin konfirmasi barang sampai + upload foto.
     * Setelah dikonfirmasi manager, stok di cabang diupdate otomatis.
     */
    public function confirmDelivery(Request $request, StockRequest $stockRequest)
    {
        $request->validate([
            'delivery_note'  => 'required|string|min:5',
            'delivery_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        if ($stockRequest->status !== 'approved') {
            return back()->with('error', 'Pengajuan belum disetujui manager!');
        }

        if ($stockRequest->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $photoPath = null;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('deliveries', 'public');
        }

        $stockRequest->update([
            'delivery_status' => 'delivered',
            'delivery_note'   => $request->delivery_note,
            'delivery_photo'  => $photoPath,
            'delivered_at'    => now(),
            'delivered_by'    => auth()->id(),
        ]);

        return back()->with('success', 'Konfirmasi pengiriman berhasil dikirim ke Manager Pusat!');
    }
}
