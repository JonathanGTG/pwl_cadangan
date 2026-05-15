@extends('layouts.manager')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Manajemen Promo</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola promo global & tinjau promo cabang</p>
    </div>
    <a href="{{ route('manager.promotions.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Buat Promo Global
    </a>
</div>

{{-- ═══ PROMO CABANG (perlu ditinjau) ═══ --}}
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <h3 class="font-display font-semibold text-gray-800">🏪 Promo Cabang</h3>
        @php $pendingCount = $branchPromos->where('review_status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
            {{ $pendingCount }} menunggu
        </span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($branchPromos as $promo)
        <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
            {{-- Header warna berdasarkan status --}}
            <div class="p-4
                {{ $promo->review_status === 'pending'  ? 'bg-gradient-to-r from-orange-500 to-amber-500' : '' }}
                {{ $promo->review_status === 'approved' ? 'bg-gradient-to-r from-purple-600 to-purple-800' : '' }}
                {{ $promo->review_status === 'rejected' ? 'bg-gradient-to-r from-gray-500 to-gray-700' : '' }}">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-white/80">🏪 {{ $promo->branch->name ?? '-' }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full
                        {{ $promo->review_status === 'pending'  ? 'bg-white/20 text-white' : '' }}
                        {{ $promo->review_status === 'approved' ? 'bg-emerald-400/30 text-white' : '' }}
                        {{ $promo->review_status === 'rejected' ? 'bg-red-400/30 text-white' : '' }}">
                        {{ $promo->review_status === 'pending'  ? '⏳ Menunggu' : '' }}
                        {{ $promo->review_status === 'approved' ? '✅ Disetujui' : '' }}
                        {{ $promo->review_status === 'rejected' ? '❌ Ditolak' : '' }}
                    </span>
                </div>
                <h3 class="font-display font-bold text-white mt-2 text-base">{{ $promo->name }}</h3>
                @if($promo->description)
                <p class="text-white/70 text-xs mt-1">{{ $promo->description }}</p>
                @endif
            </div>

            <div class="p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 text-xs">Diskon</span>
                    <span class="font-bold text-elco-coffee">{{ $promo->discount_label }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Min. Beli</span>
                    <span class="text-gray-700">
                        {{ $promo->min_purchase > 0 ? 'Rp '.number_format($promo->min_purchase,0,',','.') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Periode</span>
                    <span class="text-gray-700">
                        {{ $promo->start_date->format('d M') }} – {{ $promo->end_date->format('d M Y') }}
                    </span>
                </div>

                @if($promo->review_note)
                <div class="bg-red-50 rounded-xl p-2 mt-2">
                    <p class="text-xs text-red-600 italic">"{{ $promo->review_note }}"</p>
                </div>
                @endif

                {{-- Aksi Review --}}
                @if($promo->review_status === 'pending')
                <div class="flex gap-2 pt-2 border-t border-gray-100">
                    <form id="approve-promo-{{ $promo->id }}"
                          action="{{ route('manager.promotions.approve', $promo) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="button"
                            onclick="elcoConfirm({
                                title: 'Setujui Promo?',
                                text: '{{ addslashes($promo->name) }} akan aktif di cabang.',
                                confirmText: 'Setujui',
                                confirmColor: '#10b981',
                                icon: 'question',
                                onConfirm: () => document.getElementById('approve-promo-{{ $promo->id }}').submit()
                            })"
                            class="w-full text-xs font-medium text-emerald-600 bg-emerald-50 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                            <i class="ph ph-check"></i> Setujui
                        </button>
                    </form>
                    <button onclick="openRejectPromoModal({{ $promo->id }})"
                        class="flex-1 text-xs font-medium text-red-500 bg-red-50 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                        <i class="ph ph-x"></i> Tolak
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 py-8 text-center bg-white rounded-3xl shadow-soft">
            <i class="ph ph-tag text-4xl text-gray-300 block mb-2"></i>
            <p class="text-gray-400 text-sm">Belum ada promo dari cabang</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ═══ PROMO GLOBAL ═══ --}}
<div>
    <h3 class="font-display font-semibold text-gray-800 mb-4">🌐 Promo Global</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($globalPromos as $promo)
        <div class="bg-white rounded-3xl shadow-soft overflow-hidden smooth-transition hover:-translate-y-1 hover:shadow-hover">
            <div class="p-4 bg-gradient-to-r from-elco-coffee to-elco-mocha">
                <div class="flex justify-between">
                    <span class="text-xs font-semibold text-white/80">🌐 Global</span>
                    @if($promo->is_valid)
                    <span class="text-xs text-emerald-300 font-semibold">● Aktif</span>
                    @else
                    <span class="text-xs text-white/40">● Nonaktif</span>
                    @endif
                </div>
                <h3 class="font-display font-bold text-white mt-2">{{ $promo->name }}</h3>
                @if($promo->description)
                <p class="text-white/70 text-xs mt-1">{{ $promo->description }}</p>
                @endif
            </div>
            <div class="p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-xs text-gray-500">Diskon</span>
                    <span class="font-bold text-elco-coffee">{{ $promo->discount_label }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Periode</span>
                    <span class="text-gray-700">
                        {{ $promo->start_date->format('d M') }} – {{ $promo->end_date->format('d M Y') }}
                    </span>
                </div>
                <div class="flex gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('manager.promotions.edit', $promo) }}"
                       class="flex-1 text-center text-xs font-medium text-elco-coffee bg-elco-cream py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                        <i class="ph ph-pencil"></i> Edit
                    </a>
                    <form id="del-promo-{{ $promo->id }}"
                          action="{{ route('manager.promotions.destroy', $promo) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            onclick="elcoConfirm({
                                title: 'Hapus Promo?',
                                text: 'Promo {{ addslashes($promo->name) }} akan dihapus.',
                                confirmText: 'Hapus',
                                confirmColor: '#ef4444',
                                icon: 'warning',
                                onConfirm: () => document.getElementById('del-promo-{{ $promo->id }}').submit()
                            })"
                            class="w-full text-xs font-medium text-red-500 bg-red-50 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                            <i class="ph ph-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-8 text-center bg-white rounded-3xl shadow-soft">
            <p class="text-gray-400 text-sm">Belum ada promo global</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Reject Promo --}}
<div id="rejectPromoModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-2">Tolak Promo Cabang</h3>
        <p class="text-sm text-gray-500 mb-5">Berikan alasan penolakan agar admin cabang dapat memperbaiki.</p>
        <form id="rejectPromoForm" method="POST" class="space-y-4">
            @csrf
            <textarea name="review_note" rows="3" required
                placeholder="Alasan penolakan..."
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectPromoModal()"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600">
                    Tolak Promo
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRejectPromoModal(id) {
    document.getElementById('rejectPromoForm').action = `/manager/promotions/${id}/reject`;
    document.getElementById('rejectPromoModal').classList.remove('hidden');
}
function closeRejectPromoModal() {
    document.getElementById('rejectPromoModal').classList.add('hidden');
}
</script>
@endpush