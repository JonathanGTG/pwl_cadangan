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
        <p class="text-sm text-gray-500 mt-1">Monitor dan kelola transaksi cabang</p>
    </div>
</div>

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

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Pemasukan</p>
        <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</p>
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
            @php
                $isRequestCancel = str_starts_with($trx->cancel_reason ?? '', '[REQUEST CANCEL]');
                $canCancel = $trx->status === 'pending' || ($trx->status === 'completed' && $isRequestCancel);
            @endphp
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <p class="text-sm font-semibold text-gray-800">{{ $trx->invoice_number }}</p>
                    <p class="text-xs text-gray-400">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->kasir->name ?? '-' }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->items->count() }} item</td>
                <td class="py-4 px-6 text-sm font-bold text-emerald-600">
                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6">
                    <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-600 capitalize font-medium">
                        {{ $trx->payment_method }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    @if($isRequestCancel && $trx->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Minta Batal</span>
                        <p class="text-xs text-gray-400 italic mt-1">"{{ str_replace('[REQUEST CANCEL] ', '', $trx->cancel_reason) }}"</p>
                    @elseif($trx->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Selesai</span>
                    @elseif($trx->status === 'cancelled')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dibatalkan</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                    @endif
                </td>
                <td class="py-4 px-6">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openDetailModal({{ $trx->id }})"
                            class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                            <i class="ph ph-eye"></i> Detail
                        </button>
                        @if($canCancel)
                        <button onclick="openCancelModal({{ $trx->id }})"
                            class="text-xs font-medium text-red-500 bg-red-50 px-3 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                            <i class="ph ph-x-circle"></i> {{ $isRequestCancel ? 'Konfirmasi Batal' : 'Batalkan' }}
                        </button>
                        @endif
                        @if($isRequestCancel && $trx->status === 'completed')
                        <form id="reject-cancel-{{ $trx->id }}" action="{{ route('admin.transactions.reject-cancel', $trx) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        <button onclick="rejectCancelRequest({{ $trx->id }})"
                            class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                            <i class="ph ph-check-circle"></i> Tolak Batal
                        </button>
                        @endif
                    </div>
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

<div id="detailModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-lg mx-4">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-display font-bold text-gray-800 text-lg">Detail Pesanan</h3>
                <p id="detailInvoice" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('detailModal').classList.add('hidden')"
                class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center hover:bg-gray-200">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <div id="detailItems" class="space-y-3 max-h-72 overflow-y-auto mb-5"></div>
        <div class="border-t border-gray-100 pt-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span id="detailSubtotal" class="font-semibold text-gray-800"></span>
            </div>
            <div id="detailDiscountRow" class="flex justify-between text-sm hidden">
                <span class="text-gray-500">Diskon</span>
                <span id="detailDiscount" class="font-semibold text-red-500"></span>
            </div>
            <div class="flex justify-between text-sm font-bold border-t border-gray-100 pt-2 mt-1">
                <span class="text-gray-800">Total</span>
                <span id="detailTotal" class="text-elco-coffee text-base"></span>
            </div>
        </div>
    </div>
</div>

<div id="cancelModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-2">Batalkan Transaksi</h3>
        <p class="text-sm text-gray-500 mb-4">Stok akan dikembalikan otomatis setelah pembatalan.</p>
        <form id="cancelForm" method="POST" class="space-y-4">
            @csrf
            <textarea name="cancel_reason" rows="3" required
                placeholder="Alasan pembatalan..."
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Tutup
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 smooth-transition">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@php
    $transactionMap = $transactions->mapWithKeys(fn($trx) => [
        $trx->id => [
            'invoice'  => $trx->invoice_number,
            'subtotal' => (float) $trx->subtotal,
            'discount' => (float) $trx->discount_amount,
            'total'    => (float) $trx->total,
            'items'    => $trx->items->map(fn($item) => [
                'name'     => $item->menu_name,
                'quantity' => (float) $item->quantity,
                'price'    => (float) $item->price,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
        ],
    ]);
@endphp
@push('scripts')
<script>
const transactionDetails = @json($transactionMap);

const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

function openDetailModal(id) {
    const trx = transactionDetails[id];
    if (!trx) return;

    document.getElementById('detailInvoice').textContent = trx.invoice;
    document.getElementById('detailSubtotal').textContent = rupiah(trx.subtotal);
    document.getElementById('detailTotal').textContent = rupiah(trx.total);

    const discountRow = document.getElementById('detailDiscountRow');
    if (trx.discount > 0) {
        document.getElementById('detailDiscount').textContent = '- ' + rupiah(trx.discount);
        discountRow.classList.remove('hidden');
    } else {
        discountRow.classList.add('hidden');
    }

    document.getElementById('detailItems').innerHTML = trx.items.map(item => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
            <div>
                <p class="text-sm font-semibold text-gray-800">${item.name}</p>
                <p class="text-xs text-gray-400">${item.quantity} x ${rupiah(item.price)}</p>
            </div>
            <span class="text-sm font-bold text-elco-coffee">${rupiah(item.subtotal)}</span>
        </div>
    `).join('');

    document.getElementById('detailModal').classList.remove('hidden');
}

function openCancelModal(id) {
    document.getElementById('cancelForm').action = `/admin/transactions/${id}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function rejectCancelRequest(id) {
    elcoConfirm({
        title: 'Tolak Pembatalan?',
        text: 'Transaksi akan tetap berstatus selesai.',
        confirmText: 'Tolak Request',
        confirmColor: '#10b981',
        icon: 'question',
        onConfirm: () => document.getElementById(`reject-cancel-${id}`).submit()
    });
}
</script>
@endpush
