@extends('layouts.manager')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-display font-bold text-gray-800">Riwayat Transaksi</h2>
    <p class="text-sm text-gray-500 mt-1">Semua transaksi dari seluruh cabang</p>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-soft p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
            <select name="month" class="px-3 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-elco-mocha/30">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
            <select name="year" class="px-3 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-elco-mocha/30">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cabang</label>
            <select name="branch_id" class="px-3 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-elco-mocha/30">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2 bg-elco-coffee text-white text-sm font-semibold rounded-xl hover:bg-elco-mocha smooth-transition">
            <i class="ph ph-funnel mr-1"></i> Filter
        </button>
    </form>
</div>

{{-- Summary --}}
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

{{-- Tabel --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Invoice</th>
                <th class="py-4 px-6 font-medium">Cabang</th>
                <th class="py-4 px-6 font-medium">Kasir</th>
                <th class="py-4 px-6 font-medium">Item</th>
                <th class="py-4 px-6 font-medium">Detail</th>
                <th class="py-4 px-6 font-medium">Total</th>
                <th class="py-4 px-6 font-medium">Bayar</th>
                <th class="py-4 px-6 font-medium">Status</th>
                <th class="py-4 px-6 font-medium">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition"
                data-id="{{ $trx->id }}"
                data-invoice="{{ $trx->invoice_number }}"
                data-subtotal="{{ $trx->subtotal }}"
                data-discount="{{ $trx->discount_amount }}"
                data-total="{{ $trx->total }}"
                data-payment="{{ $trx->payment_method }}"
                data-items="{{ $trx->items->map(function($i){ return ['nama' => $i->menu_name, 'qty' => $i->quantity, 'harga' => $i->price, 'subtotal' => $i->subtotal]; })->toJson() }}">
                <td class="py-4 px-6 text-sm font-semibold text-gray-800">{{ $trx->invoice_number }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->branch->name }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->kasir->name }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $trx->items->count() }} item</td>
                <td class="py-4 px-6">
                    <button onclick="openDetailModal({{ $trx->id }})"
                        class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                        <i class="ph ph-eye"></i> Lihat
                    </button>
                </td>
                <td class="py-4 px-6 text-sm font-bold text-elco-coffee">
                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6 text-xs font-medium uppercase text-gray-500">{{ $trx->payment_method }}</td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $trx->status === 'pending'   ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $trx->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($trx->status) }}
                    </span>
                </td>
                <td class="py-4 px-6 text-xs text-gray-500">
                    {{ $trx->created_at->format('d M Y H:i') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-12 text-center text-gray-400">
                    <i class="ph ph-receipt text-4xl block mb-2"></i>
                    Belum ada transaksi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $transactions->links() }}</div>
</div>
{{-- Modal Detail Transaksi --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-lg mx-4">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-display font-bold text-gray-800 text-lg">Detail Transaksi</h3>
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
            <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>Metode Bayar</span>
                <span id="detailPayment" class="uppercase font-medium"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const trxRows = {};
document.querySelectorAll('tr[data-id]').forEach(row => {
    trxRows[row.dataset.id] = row.dataset;
});

function openDetailModal(id) {
    const d = trxRows[id];
    if (!d) return;

    document.getElementById('detailInvoice').textContent = d.invoice;
    document.getElementById('detailSubtotal').textContent = 'Rp ' + parseInt(d.subtotal).toLocaleString('id-ID');
    document.getElementById('detailTotal').textContent    = 'Rp ' + parseInt(d.total).toLocaleString('id-ID');
    document.getElementById('detailPayment').textContent  = d.payment;

    const disc = parseInt(d.discount);
    const discRow = document.getElementById('detailDiscountRow');
    if (disc > 0) {
        document.getElementById('detailDiscount').textContent = '- Rp ' + disc.toLocaleString('id-ID');
        discRow.classList.remove('hidden');
    } else {
        discRow.classList.add('hidden');
    }

    const items = JSON.parse(d.items);
    document.getElementById('detailItems').innerHTML = items.map(item => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
            <div>
                <p class="text-sm font-semibold text-gray-800">${item.nama}</p>
                <p class="text-xs text-gray-400">${item.qty} × Rp ${parseInt(item.harga).toLocaleString('id-ID')}</p>
            </div>
            <span class="text-sm font-bold text-elco-coffee">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</span>
        </div>
    `).join('');

    document.getElementById('detailModal').classList.remove('hidden');
}
</script>
@endpush