@extends('layouts.kasir')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="font-display text-lg font-semibold text-gray-800">Ringkasan Hari Ini</h2>
            <p class="text-sm text-gray-400">{{ now()->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-transparent bg-white p-6 shadow-soft smooth-transition hover:-translate-y-1 hover:border-elco-latte/30 hover:shadow-hover">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-2xl text-orange-500">
                <i class="ph-fill ph-coffee"></i>
            </div>
            <p class="mb-1 text-sm text-gray-500">Menu Tersedia</p>
            <h3 class="font-display text-2xl font-bold text-gray-800">
                {{ $data['available_menus']->count() }}
                <span class="text-sm font-normal text-gray-400">item</span>
            </h3>
        </div>

        <div class="rounded-3xl border border-transparent bg-white p-6 shadow-soft smooth-transition hover:-translate-y-1 hover:border-elco-latte/30 hover:shadow-hover">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-2xl text-emerald-600">
                <i class="ph-fill ph-receipt"></i>
            </div>
            <p class="mb-1 text-sm text-gray-500">Transaksi Hari Ini</p>
            <h3 class="font-display text-2xl font-bold text-gray-800">
                {{ $data['today_transactions'] }}
                <span class="text-sm font-normal text-gray-400">struk</span>
            </h3>
        </div>

        <div class="rounded-3xl border border-transparent bg-white p-6 shadow-soft smooth-transition hover:-translate-y-1 hover:border-elco-latte/30 hover:shadow-hover">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-500">
                <i class="ph-fill ph-wallet"></i>
            </div>
            <p class="mb-1 text-sm text-gray-500">Total Penjualan</p>
            <h3 class="font-display text-xl font-bold text-gray-800">
                Rp {{ number_format($data['today_total'], 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- Menu Tersedia --}}
    <div class="rounded-3xl bg-white p-6 shadow-soft">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-display text-lg font-semibold text-gray-800">Menu Tersedia Hari Ini</h2>
            <a
                href="{{ route('kasir.transactions.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#F6F3F0] px-4 py-2 text-xs font-medium text-elco-coffee hover:bg-elco-latte/50 smooth-transition"
            >
                <i class="ph ph-shopping-cart"></i>
                Transaksi Baru
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @forelse($data['available_menus'] as $item)
                <div class="group cursor-pointer rounded-2xl border border-gray-100 p-4 smooth-transition hover:border-elco-latte/50 hover:shadow-soft">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-xl text-orange-400 smooth-transition group-hover:bg-elco-cream">
                        <i class="ph-fill ph-coffee"></i>
                    </div>
                    <p class="truncate text-sm font-semibold text-gray-800">{{ $item->menu->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">Stok: {{ $item->stock }}</p>
                    <p class="mt-1 text-xs font-semibold text-elco-coffee">
                        Rp {{ number_format($item->effective_price, 0, ',', '.') }}
                    </p>
                </div>
            @empty
                <div class="col-span-2 py-8 text-center text-gray-400 md:col-span-4">
                    <i class="ph ph-coffee mb-2 block text-4xl"></i>
                    Tidak ada menu tersedia saat ini
                </div>
            @endforelse
        </div>
    </div>

    {{-- Quick Action --}}
    <div class="relative overflow-hidden rounded-3xl p-6 shadow-lg">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3E2723] via-[#5D4037] to-[#8D6E63]"></div>
        <div class="absolute -bottom-10 -right-10 h-40 w-40 rounded-full bg-white/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-display text-lg font-semibold text-white">Siap melayani pelanggan?</h3>
                <p class="mt-1 text-sm text-white/80">Mulai transaksi baru sekarang</p>
            </div>

            <a
                href="{{ route('kasir.transactions.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-elco-coffee shadow-md smooth-transition hover:shadow-lg active:scale-95"
            >
                <i class="ph ph-plus"></i>
                Transaksi Baru
            </a>
        </div>
    </div>
</div>
@endsection
