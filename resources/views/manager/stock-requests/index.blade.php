@extends('layouts.manager')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="mb-6">
    <h2 class="text-xl font-display font-bold text-gray-800">Verifikasi Pengajuan</h2>
    <p class="text-sm text-gray-500 mt-1">Pengajuan stok & alat dari semua cabang</p>
</div>

{{-- Filter Status --}}
<div class="flex gap-2 mb-6 flex-wrap">
    @foreach(['semua' => 'Semua', 'pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $val => $label)
    <button onclick="filterStatus('{{ $val }}')" id="flt-{{ $val }}"
        class="px-4 py-2 rounded-xl text-sm font-medium smooth-transition
        {{ $val === 'semua' ? 'bg-elco-coffee text-white shadow-md' : 'bg-white text-gray-500 shadow-soft hover:bg-gray-50' }}">
        {{ $label }}
    </button>
    @endforeach
</div>

<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[900px]">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-4 px-5 font-medium">Item</th>
                    <th class="py-4 px-5 font-medium">Cabang</th>
                    <th class="py-4 px-5 font-medium">Tipe</th>
                    <th class="py-4 px-5 font-medium">Jumlah</th>
                    <th class="py-4 px-5 font-medium">Status</th>
                    <th class="py-4 px-5 font-medium">Pengiriman</th>
                    <th class="py-4 px-5 font-medium">Bukti</th>
                    <th class="py-4 px-5 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr class="request-row border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition align-top"
                    data-status="{{ $req->status }}">

                    {{-- Item --}}
                    <td class="py-4 px-5">
                        <p class="text-sm font-semibold text-gray-800">{{ $req->item_name }}</p>
                        <p class="text-xs text-gray-400">{{ $req->created_at->format('d M Y') }}</p>
                        @if($req->reason)
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $req->reason }}"</p>
                        @endif
                    </td>

                    {{-- Cabang --}}
                    <td class="py-4 px-5 text-sm text-gray-600">{{ $req->branch?->name ?? '—' }}</td>

                    {{-- Tipe --}}
                    <td class="py-4 px-5">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $req->type === 'stock' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                            {{ $req->type === 'stock' ? 'Stok' : 'Operasional' }}
                        </span>
                    </td>

                    {{-- Jumlah --}}
                    <td class="py-4 px-5 text-sm font-medium text-gray-700">
                        {{ $req->quantity }} {{ $req->unit }}
                    </td>

                    {{-- Status Pengajuan --}}
                    <td class="py-4 px-5">
                        <span class="px-2 py-1 rounded-full text-xs font-medium block w-fit
                            {{ $req->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $req->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $req->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $req->status === 'pending' ? 'Pending'
                                : ($req->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                        </span>
                        @if($req->status === 'approved')
                        <span class="px-2 py-1 rounded-full text-xs font-medium block w-fit mt-1
                            {{ $req->delivery_status === 'waiting'   ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $req->delivery_status === 'delivered' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $req->delivery_status === 'confirmed' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ $req->delivery_status === 'waiting'   ? 'Menunggu Kirim' : '' }}
                            {{ $req->delivery_status === 'delivered' ? 'Barang Sampai' : '' }}
                            {{ $req->delivery_status === 'confirmed' ? 'Stok Bertambah' : '' }}
                        </span>
                        @endif
                    </td>

                    {{-- Kolom Pengiriman: Catatan --}}
                    <td class="py-4 px-5">
                        @if($req->delivery_status === 'delivered' || $req->delivery_status === 'confirmed')
                            <p class="text-xs text-gray-700 font-medium mb-1">Catatan:</p>
                            <p class="text-xs text-gray-500 max-w-[180px]">{{ $req->delivery_note ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $req->delivered_at ? $req->delivered_at->format('d M Y H:i') : '' }}
                            </p>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Kolom Bukti Foto --}}
                    <td class="py-4 px-5">
                        @if($req->delivery_photo)
                            <a href="{{ Storage::url($req->delivery_photo) }}" target="_blank"
                               class="block group">
                                <img src="{{ Storage::url($req->delivery_photo) }}"
                                     class="w-16 h-16 rounded-xl object-cover border border-gray-200 group-hover:opacity-80 smooth-transition"
                                     alt="Bukti">
                                <p class="text-xs text-elco-coffee mt-1 group-hover:underline">Lihat</p>
                            </a>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="py-4 px-5">
                        @if($req->status === 'pending')
                        <div class="flex flex-col gap-2">
                            <form id="app-{{ $req->id }}"
                                  action="{{ route('manager.stock-requests.approve', $req) }}" method="POST">
                                @csrf
                                <button type="button"
                                    onclick="elcoConfirm({
                                        title: 'Setujui Pengajuan?',
                                        text: '{{ addslashes($req->item_name) }} ({{ $req->quantity }} {{ $req->unit }}) untuk {{ addslashes($req->branch?->name ?? "-") }}',
                                        confirmColor: '#10b981',
                                        icon: 'question',
                                        onConfirm: () => document.getElementById('app-{{ $req->id }}').submit()
                                    })"
                                    class="w-full text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                                    <i class="ph ph-check"></i> Setujui
                                </button>
                            </form>
                            <button onclick="openRejectModal({{ $req->id }})"
                                class="text-xs font-medium text-red-500 bg-red-50 px-3 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                                <i class="ph ph-x"></i> Tolak
                            </button>
                        </div>
                        @elseif($req->status === 'approved' && $req->delivery_status === 'delivered')
                        <form id="confirm-{{ $req->id }}"
                              action="{{ route('manager.stock-requests.confirm-delivery', $req) }}" method="POST">
                            @csrf
                            <button type="button"
                                onclick="elcoConfirm({
                                    title: 'Konfirmasi Penerimaan?',
                                    text: 'Stok akan bertambah otomatis setelah dikonfirmasi.',
                                    confirmText: 'Konfirmasi',
                                    confirmColor: '#10b981',
                                    icon: 'question',
                                    onConfirm: () => document.getElementById('confirm-{{ $req->id }}').submit()
                                })"
                                class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition whitespace-nowrap">
                                <i class="ph ph-check-circle"></i> Konfirmasi & Tambah Stok
                            </button>
                        </form>
                        @elseif($req->status === 'approved' && $req->delivery_status === 'waiting')
                        <span class="text-xs text-orange-500 font-medium">Menunggu konfirmasi admin</span>
                        @elseif($req->delivery_status === 'confirmed')
                        <span class="text-xs text-purple-600 font-medium">Selesai</span>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-gray-400">
                        <i class="ph ph-clipboard-text text-4xl block mb-2"></i>
                        Tidak ada pengajuan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Reject --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-2">Tolak Pengajuan</h3>
        <p class="text-sm text-gray-500 mb-5">Berikan alasan agar admin cabang memahami keputusan ini.</p>
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <textarea name="rejection_note" rows="4" required
                placeholder="Tulis alasan penolakan..."
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterStatus(status) {
    document.querySelectorAll('[id^="flt-"]').forEach(btn => {
        btn.classList.remove('bg-elco-coffee', 'text-white', 'shadow-md');
        btn.classList.add('bg-white', 'text-gray-500', 'shadow-soft');
    });
    document.getElementById('flt-' + status).classList.add('bg-elco-coffee', 'text-white', 'shadow-md');
    document.getElementById('flt-' + status).classList.remove('bg-white', 'text-gray-500');
    document.querySelectorAll('.request-row').forEach(row => {
        row.style.display = (status === 'semua' || row.dataset.status === status) ? '' : 'none';
    });
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/manager/stock-requests/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
