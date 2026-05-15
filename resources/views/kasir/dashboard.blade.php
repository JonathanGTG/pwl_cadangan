@extends('layouts.kasir')

@section('content')
<div class="space-y-6">

    <!-- Stat Cards -->
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-display font-semibold text-gray-800">
            Ringkasan Hari Ini <span class="text-gray-400 text-sm font-normal">/ {{ now()->format('d M Y') }}</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-4">
                <i class="ph-fill ph-coffee"></i>
            </div>
            <p class="text-sm text-gray-500 mb-1">Menu Tersedia</p>
            <h3 class="text-2xl font-display font-bold text-gray-800">
                {{ $data['available_menus']->count() }} <span class="text-sm text-gray-400 font-normal">item</span>
            </h3>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4">
                <i class="ph-fill ph-receipt"></i>
            </div>
            <p class="text-sm text-gray-500 mb-1">Transaksi Hari Ini</p>
            <h3 class="text-2xl font-display font-bold text-gray-800">
                {{ $data['today_transactions'] }} <span class="text-sm text-gray-400 font-normal">struk</span>
            </h3>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-4">
                <i class="ph-fill ph-wallet"></i>
            </div>
            <p class="text-sm text-gray-500 mb-1">Total Penjualan</p>
            <h3 class="text-xl font-display font-bold text-gray-800">
                Rp {{ number_format($data['today_total'], 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- Menu Tersedia -->
    <div class="bg-white p-6 rounded-3xl shadow-soft">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-display font-semibold text-gray-800">Menu Tersedia Hari Ini</h2>
            <a href="#" class="flex items-center gap-2 text-xs font-medium text-elco-coffee bg-[#F6F3F0] px-4 py-2 rounded-xl hover:bg-elco-latte/50 smooth-transition">
                <i class="ph ph-shopping-cart"></i> Transaksi Baru
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($data['available_menus'] as $item)
            <div class="p-4 rounded-2xl border border-gray-100 hover:border-elco-latte/50 hover:shadow-soft smooth-transition cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-400 flex items-center justify-center text-xl mb-3 group-hover:bg-elco-cream smooth-transition">
                    <i class="ph-fill ph-coffee"></i>
                </div>
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->menu->name }}</p>
                <p class="text-xs text-gray-500 mt-1">Stok: {{ $item->stock }}</p>
                <p class="text-xs font-semibold text-elco-coffee mt-1">
                    Rp {{ number_format($item->effective_price, 0, ',', '.') }}
                </p>
            </div>
            @empty
            <div class="col-span-4 py-8 text-center text-gray-400">
                <i class="ph ph-coffee text-4xl mb-2 block"></i>
                Tidak ada menu tersedia saat ini
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Action -->
    <div class="p-6 rounded-3xl relative overflow-hidden shadow-lg">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3E2723] via-[#5D4037] to-[#8D6E63]"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/20 blur-3xl rounded-full"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-display font-semibold text-white">Siap melayani pelanggan?</h3>
                <p class="text-sm text-white/80 mt-1">Mulai transaksi baru sekarang</p>
            </div>
            <a href="#" class="bg-white text-elco-coffee font-semibold text-sm px-6 py-3 rounded-2xl shadow-md smooth-transition hover:shadow-lg active:scale-95 flex items-center gap-2">
                <i class="ph ph-plus"></i> Transaksi Baru
            </a>
        </div>
    </div>

</div>
@endsection