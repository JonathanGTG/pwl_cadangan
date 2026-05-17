@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Promo</h2>
        <p class="text-sm text-gray-500 mt-1">Promo cabang & promo global aktif</p>
    </div>
    <a href="{{ route('admin.promotions.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Buat Promo Cabang
    </a>
</div>

{{-- Promo Cabang --}}
<h3 class="font-display font-semibold text-gray-700 mb-3">Promo Cabang Saya</h3>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
    @forelse($branchPromos as $promo)
    <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-purple-600 to-purple-800">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-white/80">Promo Cabang</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full
                    {{ $promo->review_status === 'pending'  ? 'bg-yellow-400/30 text-white' : '' }}
                    {{ $promo->review_status === 'approved' ? 'bg-emerald-400/30 text-white' : '' }}
                    {{ $promo->review_status === 'rejected' ? 'bg-red-400/30 text-white' : '' }}">
                    {{ $promo->review_status === 'pending'  ? 'Menunggu Review' : '' }}
                    {{ $promo->review_status === 'approved' ? 'Disetujui' : '' }}
                    {{ $promo->review_status === 'rejected' ? 'Ditolak' : '' }}
                </span>
            </div>
            @if($promo->review_status === 'rejected' && $promo->review_note)
            <div class="px-4 pb-3">
                <div class="bg-red-50 rounded-xl p-3">
                    <p class="text-xs text-red-600 font-medium">Alasan Penolakan:</p>
                    <p class="text-xs text-red-500 mt-1">{{ $promo->review_note }}</p>
                </div>
            </div>
            @endif
            <h3 class="font-display font-bold text-white mt-2">{{ $promo->name }}</h3>
        </div>
        <div class="p-4 space-y-2">
            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Diskon</span>
                <span class="font-bold text-elco-coffee">{{ $promo->discount_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Periode</span>
                <span class="text-xs text-gray-600">
                    {{ $promo->start_date->format('d M') }} — {{ $promo->end_date->format('d M Y') }}
                </span>
            </div>
            <div class="pt-2 border-t border-gray-100">
                <form id="form-hapus-{{ $promo->id }}" method="POST" action="...">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                        onclick="elcoConfirm({
                            title: 'Hapus Data?',
                            text: 'Data yang dihapus tidak dapat dikembalikan.',
                            confirmText: 'Ya, Hapus',
                            confirmColor: '#ef4444',
                            onConfirm: () => document.getElementById('form-hapus-{{ $promo->id }}').submit()
                        })"
                        class="...">
                        <i class="ph ph-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-8 text-center bg-white rounded-3xl shadow-soft">
        <i class="ph ph-tag text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">Belum ada promo cabang</p>
    </div>
    @endforelse
</div>

{{-- Promo Global --}}
<h3 class="font-display font-semibold text-gray-700 mb-3">Promo Global (dari Manager)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($globalPromos as $promo)
    <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-elco-coffee to-elco-mocha">
            <div class="flex justify-between">
                <span class="text-xs font-semibold text-white/80">Global</span>
                <span class="text-xs text-emerald-300 font-semibold">● Aktif</span>
            </div>
            <h3 class="font-display font-bold text-white mt-2">{{ $promo->name }}</h3>
            <p class="text-white/70 text-xs mt-1">{{ $promo->description }}</p>
        </div>
        <div class="p-4 space-y-2">
            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Diskon</span>
                <span class="font-bold text-elco-coffee">{{ $promo->discount_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Min. Beli</span>
                <span class="text-xs text-gray-600">
                    {{ $promo->min_purchase > 0 ? 'Rp ' . number_format($promo->min_purchase, 0, ',', '.') : '-' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Periode</span>
                <span class="text-xs text-gray-600">
                    {{ $promo->start_date->format('d M') }} — {{ $promo->end_date->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-8 text-center bg-white rounded-3xl shadow-soft">
        <p class="text-gray-400 text-sm">Tidak ada promo global aktif saat ini</p>
    </div>
    @endforelse
</div>

@endsection
