@extends('layouts.manager')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('manager.menus.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Resep Menu</h2>
            <p class="text-sm text-gray-500">{{ $menu->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="ph ph-flask text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Bahan per Sajian</p>
                    <p class="text-xs text-gray-400">Dipakai untuk stok bahan baku saat transaksi</p>
                </div>
            </div>
            <a href="{{ route('manager.menus.edit', $menu) }}"
               class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                <i class="ph ph-pencil mr-1"></i> Edit Resep
            </a>
        </div>

        @if(!$menu->isIngredientBased())
            <div class="p-8 text-center text-gray-400">
                <i class="ph ph-package text-4xl block mb-2"></i>
                <p class="text-sm">Menu ini tidak menggunakan bahan baku</p>
            </div>
        @elseif($menuIngredients->count() === 0)
            <div class="p-8 text-center text-gray-400">
                <i class="ph ph-flask text-4xl block mb-2"></i>
                <p class="text-sm">Resep belum diisi</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                            <th class="py-4 px-6 font-medium">Bahan</th>
                            <th class="py-4 px-6 font-medium">Jumlah</th>
                            <th class="py-4 px-6 font-medium">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menuIngredients as $mi)
                            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                                <td class="py-4 px-6 text-sm font-semibold text-gray-800">
                                    {{ $mi->ingredient?->nama_bahan ?? '—' }}
                                </td>
                                <td class="py-4 px-6 text-sm font-bold text-elco-coffee">
                                    {{ number_format($mi->jumlah_per_sajian, 3, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    {{ $mi->ingredient?->satuan ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
