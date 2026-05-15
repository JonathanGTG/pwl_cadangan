@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Stok Cabang</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor stok menu di cabang kamu</p>
    </div>
    <a href="{{ route('admin.stock-requests.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-arrow-circle-up"></i> Ajukan Kebutuhan
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Item</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stocks->count() }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Stok Normal</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stocks->where('stock', '>', 5)->count() }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Stok Kritis (≤5)</p>
        <p class="text-2xl font-bold text-red-500">{{ $stocks->where('stock', '<=', 5)->count() }}</p>
    </div>
</div>

{{-- Tabel Stok --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Menu</th>
                <th class="py-4 px-6 font-medium">Kategori</th>
                <th class="py-4 px-6 font-medium">Stok</th>
                <th class="py-4 px-6 font-medium">Harga Efektif</th>
                <th class="py-4 px-6 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        @if($stock->menu->image)
                            <img src="{{ Storage::url($stock->menu->image) }}"
                                 class="w-10 h-10 rounded-xl object-cover">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-400 flex items-center justify-center">
                                <i class="ph-fill ph-coffee"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $stock->menu->name }}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-600">
                        {{ ucfirst($stock->menu->category) }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <span class="text-sm font-bold
                        {{ $stock->stock <= 0 ? 'text-red-500' : ($stock->stock <= 5 ? 'text-yellow-600' : 'text-gray-800') }}">
                        {{ $stock->stock }}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm font-semibold text-elco-coffee">
                    Rp {{ number_format($stock->custom_price ?? $stock->menu->base_price, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6">
                    @if($stock->stock <= 0)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Habis</span>
                    @elseif($stock->stock <= 5)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">Kritis</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-600">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-12 text-center text-gray-400">
                    <i class="ph ph-package text-4xl block mb-2"></i>
                    Belum ada stok di cabang ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection