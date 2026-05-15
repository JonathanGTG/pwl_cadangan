@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-x-circle text-xl"></i> {{ session('error') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Riwayat Transaksi</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor & kelola transaksi cabang</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-soft p-4 mb-6">
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
            <select name="month" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none bg-white">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
            <select name="year" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none bg-white">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit"
            class="px-5 py-2 bg-elco-coffee text-white text-sm font-semibold rounded-xl hover:bg-elco-mocha smooth-transition">
            <i class="ph ph-funnel mr-1"></i> Filter
        </button>
    </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Pemasukan</p>
        <p class="text-xl font-bold text-emerald-600">
            Rp {{ number_format($summary['total_income'], 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Transaksi Selesai</p>
        <p class="text-2xl font-bold text-gray-800">{{ $summary['total_transactions'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Dibatalkan</p>
        <p class="text-2xl font-bold text-red-500">{{ $summary['cancelled'] }}</p>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Invoice</th>
                <th class="py-4 px-6 font-medium">Kasir</th>
                <th class="py-4 px-6 font-medium">Item</th>
                <th class="py-4 px-6 font-medium">Total</th>
                <th class="py-4 px-6 font-medium">Metode</th>
                <th class="py-4 px-6 font-medium">Status</th>
                <th class="py-4 px-6 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <p class="text-sm font-semibold text-gray-800">{{ $trx->invoice_number }}</p>
                    <p class="text-xs text-gray-400">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->kasir->name }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->items->count() }} item</td>
                <td class="py-4 px-6">
                    <span class="text-sm font-bold text-emerald-600">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-600 capitalize font-medium">
                        {{ $trx->payment_method }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    @php
                    $isRequestCancel = str_starts_with($trx->cancel_reason ?? '', '[REQUEST CANCEL]');
                    $canCancel = $trx->status === 'pending' ||
                                ($trx->status === 'completed' && $isRequestCancel);
                @endphp

                {{-- Kolom Status --}}
                <td class="py-4 px-6">
                    @if($isRequestCancel && $trx->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Minta Batal</span>
                        @php $reason = str_replace('[REQUEST CANCEL] ', '', $trx->cancel_reason); @endphp
                        <p class="text-xs text-gray-400 italic mt-1">"{{ $reason }}"</p>
                    @elseif($trx->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Selesai</span>
                    @elseif($trx->status === 'cancelled')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dibatalkan</span>
                    @elseif($trx->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                    @endif
                </td>

                {{-- Kolom Aksi --}}
                <td class="py-4 px-6">
                    @if($canCancel)
                    <button onclick="openCancelModal({{ $trx->id }})"
                        class="text-xs font-medium text-red-500 bg-red-50 px-3 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                        <i class="ph ph-x-circle"></i>
                        {{ $isRequestCancel ? 'Konfirmasi Batal' : 'Batalkan' }}
                    </button>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400">
                    <i class="ph ph-receipt text-4xl block mb-2"></i>
                    Tidak ada transaksi pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Batalkan --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-2">Batalkan Transaksi</h3>
        <p class="text-sm text-gray-500 mb-2">⚠️ Stok akan dikembalikan otomatis setelah pembatalan.</p>
        <form id="cancelForm" method="POST" class="space-y-4">
            @csrf
            <textarea name="cancel_reason" rows="3" required
                placeholder="Alasan pembatalan..."
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 smooth-transition">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCancelModal(id) {
    document.getElementById('cancelForm').action = `/admin/transactions/${id}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}
</script>
<script>
// Toggle field nama item berdasarkan tipe
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const isStock = this.value === 'stock';
        document.getElementById('stockSelect').classList.toggle('hidden', !isStock);
        document.getElementById('operationalInput').classList.toggle('hidden', isStock);

        // Sync nilai ke field yang aktif
        document.getElementById('itemNameSelect').required = isStock;
        document.getElementById('itemNameOps').required    = !isStock;
    });
});

// Sebelum submit, satukan nilai item_name
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    if (type === 'operational') {
        // Buat hidden input dengan nama item_name
        const val = document.getElementById('itemNameOps').value;
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'item_name';
        hidden.value = val;
        this.appendChild(hidden);
    }
});
</script>
@endpush