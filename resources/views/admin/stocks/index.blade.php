@extends('layouts.admin')

@section('content')

@php
    $branchId = auth()->user()->branch_id;
    $ingredientStockMap = $ingredientStocks->keyBy('ingredient_id');
    $stockRows = $stocks->filter(fn ($stock) => $stock->menu);
    $normalCount = 0;
    $criticalCount = 0;

    foreach ($stockRows as $row) {
        if ($row->menu->isIngredientBased()) {
            $minPortions = null;
            $isCriticalIngredient = false;

            foreach ($row->menu->ingredients as $mi) {
                $ingStock = $ingredientStockMap->get($mi->ingredient_id);
                $available = (float) ($ingStock?->stok_sekarang ?? 0);
                $perServing = (float) $mi->jumlah_per_sajian;
                $porsi = $perServing > 0 ? (int) floor($available / $perServing) : 0;

                $minPortions = is_null($minPortions) ? $porsi : min($minPortions, $porsi);

                if (!$ingStock || $ingStock->stok_sekarang <= $ingStock->stok_minimum) {
                    $isCriticalIngredient = true;
                }
            }

            $minPortions = $minPortions ?? 0;

            if ($minPortions >= 1 && !$isCriticalIngredient) {
                $normalCount++;
            } else {
                $criticalCount++;
            }
        } else {
            if ($row->stock > 5) {
                $normalCount++;
            } else {
                $criticalCount++;
            }
        }
    }
@endphp

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
        <p class="text-2xl font-bold text-gray-800">{{ $stocks->count() + $ingredientStocks->count() }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Stok Normal</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $normalCount }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Stok Kritis</p>
        <p class="text-2xl font-bold text-red-500">{{ $criticalCount }}</p>
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
            @continue(!$stock->menu)
            @php
                $isIngredientBased = $stock->menu->isIngredientBased();
                $ingredientDetails = [];
                $shortages = [];
                $minPortions = null;
                $isCriticalIngredient = false;

                if ($isIngredientBased) {
                    foreach ($stock->menu->ingredients as $mi) {
                        $ingStock = $ingredientStockMap->get($mi->ingredient_id);
                        $available = (float) ($ingStock?->stok_sekarang ?? 0);
                        $minimum = (float) ($ingStock?->stok_minimum ?? 0);
                        $perServing = (float) $mi->jumlah_per_sajian;
                        $needed = $perServing;
                        $porsi = $perServing > 0 ? (int) floor($available / $perServing) : 0;

                        $minPortions = is_null($minPortions) ? $porsi : min($minPortions, $porsi);

                        if ($available < $needed) {
                            $shortages[] = ($mi->ingredient?->nama_bahan ?? '—');
                        }

                        if (!$ingStock || $ingStock->stok_sekarang <= $ingStock->stok_minimum) {
                            $isCriticalIngredient = true;
                        }

                        $ingredientDetails[] = [
                            'nama' => $mi->ingredient?->nama_bahan ?? '—',
                            'jumlah' => $perServing,
                            'satuan' => $mi->ingredient?->satuan ?? '—',
                            'stok' => $available,
                            'min' => $minimum,
                            'porsi' => $porsi,
                        ];
                    }
                }

                $minPortions = $minPortions ?? 0;
                $isAvailable = $isIngredientBased ? $minPortions >= 1 : $stock->stock > 0;
                $isCritical = $isIngredientBased ? $isCriticalIngredient : ($stock->stock > 0 && $stock->stock <= 5);
            @endphp
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        @if($stock->menu->image)
                            <img src="{{ Storage::disk('public')->url($stock->menu->image) }}"
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
                    @if($isIngredientBased)
                        <div class="space-y-1">
                            <span class="text-sm font-bold {{ $isAvailable ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $minPortions }} porsi
                            </span>
                            @if(count($shortages) > 0)
                                <p class="text-xs text-red-400">Kurang: {{ implode(', ', array_slice($shortages, 0, 3)) }}{{ count($shortages) > 3 ? '...' : '' }}</p>
                            @endif
                            <button type="button" onclick="toggleIngredientDetail({{ $stock->id }})"
                                class="text-xs text-elco-coffee hover:underline">
                                Lihat detail bahan
                            </button>
                        </div>
                    @else
                        <span class="text-sm font-bold
                            {{ $stock->stock <= 0 ? 'text-red-500' : ($stock->stock <= 5 ? 'text-yellow-600' : 'text-gray-800') }}">
                            {{ $stock->stock }}
                        </span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm font-semibold text-elco-coffee">
                    Rp {{ number_format($stock->custom_price ?? $stock->menu->base_price, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6">
                    @if(!$isAvailable)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Habis</span>
                    @elseif($isCritical)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">Kritis</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-600">Normal</span>
                    @endif
                </td>
            </tr>
            @if($isIngredientBased)
                <tr id="detail-{{ $stock->id }}" class="hidden bg-gray-50 border-b border-gray-100">
                    <td colspan="5" class="px-6 pb-6">
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-800">Detail Bahan</p>
                                <button type="button" onclick="toggleIngredientDetail({{ $stock->id }})"
                                    class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center hover:bg-gray-200">
                                    <i class="ph ph-x"></i>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                                            <th class="py-3 px-4 font-medium">Bahan</th>
                                            <th class="py-3 px-4 font-medium">Per Sajian</th>
                                            <th class="py-3 px-4 font-medium">Stok</th>
                                            <th class="py-3 px-4 font-medium">Minimum</th>
                                            <th class="py-3 px-4 font-medium">Estimasi Porsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ingredientDetails as $d)
                                            <tr class="border-b border-gray-50 last:border-0">
                                                <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $d['nama'] }}</td>
                                                <td class="py-3 px-4 text-sm text-gray-600">
                                                    {{ number_format($d['jumlah'], 3, ',', '.') }} {{ $d['satuan'] }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-bold text-elco-coffee">
                                                    {{ number_format($d['stok'], 2, ',', '.') }} {{ $d['satuan'] }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-600">
                                                    {{ number_format($d['min'], 2, ',', '.') }} {{ $d['satuan'] }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-semibold text-gray-700">{{ $d['porsi'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
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

{{-- Tabel Stok Bahan Baku --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">Stok Bahan Baku</h3>
        <p class="text-xs text-gray-400 mt-1">Dipakai untuk menu minuman</p>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Bahan</th>
                <th class="py-4 px-6 font-medium">Kategori</th>
                <th class="py-4 px-6 font-medium">Stok</th>
                <th class="py-4 px-6 font-medium">Minimum</th>
                <th class="py-4 px-6 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingredientStocks as $stock)
            @continue(!$stock->ingredient)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6 text-sm font-semibold text-gray-800">
                    {{ $stock->ingredient->nama_bahan }}
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    {{ $stock->ingredient->kategori ?? '-' }}
                </td>
                <td class="py-4 px-6 text-sm font-bold text-elco-coffee">
                    {{ number_format($stock->stok_sekarang, 2, ',', '.') }} {{ $stock->ingredient->satuan }}
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    {{ number_format($stock->stok_minimum, 2, ',', '.') }} {{ $stock->ingredient->satuan }}
                </td>
                <td class="py-4 px-6">
                    @if($stock->stok_sekarang <= $stock->stok_minimum)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">Perlu Restock</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-600">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-12 text-center text-gray-400">
                    <i class="ph ph-flask text-4xl block mb-2"></i>
                    Belum ada stok bahan baku di cabang ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
function toggleIngredientDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('hidden');
}
</script>
@endpush

@endsection
