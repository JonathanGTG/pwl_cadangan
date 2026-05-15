@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT COLUMN -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-display font-semibold text-gray-800">
                Ringkasan Cabang <span class="text-gray-400 text-sm font-normal">/ Bulan Ini</span>
            </h2>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-wallet"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pemasukan Bulan Ini</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    Rp {{ number_format($data['total_income'], 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-money"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pengeluaran Bulan Ini</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    Rp {{ number_format($data['total_expense'], 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
                <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-package"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Stok Hampir Habis</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    {{ $data['low_stocks'] }} <span class="text-sm text-gray-400 font-normal">item</span>
                </h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover border border-transparent hover:border-elco-latte/30">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-arrow-circle-up"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pengajuan Pending</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    {{ $data['pending_requests'] }} <span class="text-sm text-gray-400 font-normal">permintaan</span>
                </h3>
            </div>
        </div>

        <!-- Tabel Transaksi Terbaru -->
        <div class="bg-white p-6 rounded-3xl shadow-soft">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-display font-semibold text-gray-800">Transaksi Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-medium px-4">Invoice</th>
                            <th class="pb-3 font-medium px-4">Kasir</th>
                            <th class="pb-3 font-medium px-4">Total</th>
                            <th class="pb-3 font-medium px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['latest_transactions'] as $trx)
                        <tr class="group hover:bg-gray-50 smooth-transition border-b border-gray-50 last:border-0">
                            <td class="py-4 px-4 text-sm font-medium text-gray-800">{{ $trx->invoice_number }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ $trx->kasir->name ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm font-semibold text-emerald-600">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $trx->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $trx->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 text-sm">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="space-y-6">

        <!-- Info Cabang -->
        <div class="bg-white p-6 rounded-3xl shadow-soft">
            <h2 class="text-lg font-display font-semibold text-gray-800 mb-4">Info Cabang</h2>
            <div class="flex items-center gap-4 p-4 bg-[#F6F3F0] rounded-2xl">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white">
                    <i class="ph-fill ph-storefront text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ auth()->user()->branch->name ?? 'Cabang ELCO' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->branch->address ?? '-' }}</p>
                    <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium mt-1 inline-block">Aktif</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Stok -->
        <div class="bg-white p-6 rounded-3xl shadow-soft">
            <h2 class="text-lg font-display font-semibold text-gray-800 mb-4">Ringkasan Stok</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 rounded-2xl bg-gray-50">
                    <span class="text-sm text-gray-600">Total Item Stok</span>
                    <span class="font-bold text-gray-800">{{ $data['total_stocks'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-2xl bg-red-50">
                    <span class="text-sm text-red-600">Stok Kritis (≤5)</span>
                    <span class="font-bold text-red-600">{{ $data['low_stocks'] }}</span>
                </div>
            </div>
            @if($data['low_stocks'] > 0)
            <a href="#" class="mt-4 w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
                <i class="ph ph-arrow-circle-up"></i> Ajukan Pengisian Stok
            </a>
            @endif
        </div>

        <!-- Promo Aktif -->
        <div class="p-6 rounded-3xl relative overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-[#3E2723] via-[#5D4037] to-[#8D6E63]"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/20 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <h3 class="text-lg font-display font-semibold text-white mb-2">Promo Cabang</h3>
                <p class="text-sm text-white/80 leading-relaxed mb-4">
                    Buat promo khusus untuk menarik pelanggan di cabang kamu.
                </p>
                <div class="flex gap-2">
                    <a href="{{ route('admin.promotions.index') }}"
                    class="flex-1 text-center bg-white/20 text-white text-xs font-medium py-2 rounded-xl hover:bg-white/30 smooth-transition">
                        Lihat Promo
                    </a>
                    <a href="{{ route('admin.promotions.create') }}"
                    class="flex-1 text-center bg-white text-elco-coffee font-semibold text-xs py-2 rounded-xl shadow-md smooth-transition hover:shadow-lg active:scale-95">
                        + Buat Promo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection