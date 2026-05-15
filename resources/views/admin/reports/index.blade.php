@extends('layouts.admin')

@section('content')

{{-- Header + Tombol Export --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Laporan Bulanan</h2>
        <p class="text-sm text-gray-500 mt-1">Ringkasan operasional cabang kamu</p>
    </div>
    <a href="{{ route('admin.reports.export', ['month' => $month, 'year' => $year]) }}"
       class="flex items-center gap-2 bg-emerald-600 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:bg-emerald-700 smooth-transition active:scale-95">
        <i class="ph ph-download-simple"></i> Download Laporan
    </a>
</div>

<div class="bg-white rounded-2xl shadow-soft p-4 mb-6">
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
            <select name="month"
                class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
            <select name="year"
                class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
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

{{-- Summary Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-3">
            <i class="ph-fill ph-trend-up text-xl"></i>
        </div>
        <p class="text-xs text-gray-500 mb-1">Total Pemasukan</p>
        <p class="text-lg font-display font-bold text-emerald-600">
            Rp {{ number_format($totalIncome, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center mb-3">
            <i class="ph-fill ph-trend-down text-xl"></i>
        </div>
        <p class="text-xs text-gray-500 mb-1">Total Pengeluaran</p>
        <p class="text-lg font-display font-bold text-red-500">
            Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <div class="w-10 h-10 rounded-xl {{ $totalProfit >= 0 ? 'bg-blue-50 text-blue-500' : 'bg-orange-50 text-orange-500' }} flex items-center justify-center mb-3">
            <i class="ph-fill ph-wallet text-xl"></i>
        </div>
        <p class="text-xs text-gray-500 mb-1">Laba Bersih</p>
        <p class="text-lg font-display font-bold {{ $totalProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
            Rp {{ number_format($totalProfit, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center mb-3">
            <i class="ph-fill ph-receipt text-xl"></i>
        </div>
        <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
        <p class="text-lg font-display font-bold text-purple-600">
            {{ $totalTransaction }} struk
        </p>
    </div>
</div>

{{-- Grafik + Stok Kritis --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

    {{-- Grafik Pemasukan vs Pengeluaran --}}
    <div class="xl:col-span-2 bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">
            Pemasukan vs Pengeluaran {{ $year }}
        </h3>
        <div class="h-64">
            <canvas id="branchChart"></canvas>
        </div>
    </div>

    {{-- Stok Kritis --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">
            ⚠️ Stok Kritis
        </h3>
        @if($criticalStocks->count() > 0)
        <div class="space-y-3">
            @foreach($criticalStocks as $stock)
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-2xl">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $stock->menu->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($stock->menu->category) }}</p>
                </div>
                <span class="text-sm font-bold text-red-600">
                    {{ $stock->stock }} sisa
                </span>
            </div>
            @endforeach
        </div>
        <a href="{{ route('admin.stock-requests.create') }}"
           class="mt-4 w-full flex items-center justify-center gap-2 py-2.5 bg-elco-coffee text-white text-xs font-semibold rounded-xl hover:bg-elco-mocha smooth-transition">
            <i class="ph ph-arrow-circle-up"></i> Ajukan Pengisian Stok
        </a>
        @else
        <div class="py-8 text-center text-gray-400">
            <i class="ph ph-check-circle text-4xl text-emerald-400 block mb-2"></i>
            <p class="text-sm">Semua stok dalam kondisi aman</p>
        </div>
        @endif
    </div>
</div>

{{-- Pengeluaran Per Kategori --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">Pengeluaran Per Kategori</h3>
        @forelse($expenseByCategory as $cat)
        <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
            <span class="text-sm text-gray-700">
                {{ match($cat->category) {
                    'operasional' => '⚡ Operasional',
                    'bahan_baku'  => '☕ Bahan Baku',
                    'peralatan'   => '🔧 Peralatan',
                    'gaji'        => '👤 Gaji',
                    default       => '📋 Lainnya'
                } }}
            </span>
            <span class="text-sm font-bold text-red-600">
                Rp {{ number_format($cat->total, 0, ',', '.') }}
            </span>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-6">Belum ada pengeluaran</p>
        @endforelse
    </div>

    {{-- Grafik Laba --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">Laba Bersih {{ $year }}</h3>
        <div class="h-52">
            <canvas id="profitChart"></canvas>
        </div>
    </div>
</div>

{{-- Tabel Transaksi Bulan Ini --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-display font-semibold text-gray-800">
            Transaksi Bulan Ini
            <span class="text-sm font-normal text-gray-400 ml-2">
                ({{ $transactions->count() }} transaksi)
            </span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-3 px-6 font-medium">Invoice</th>
                    <th class="py-3 px-6 font-medium">Kasir</th>
                    <th class="py-3 px-6 font-medium">Item</th>
                    <th class="py-3 px-6 font-medium">Total</th>
                    <th class="py-3 px-6 font-medium">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions->take(10) as $trx)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                    <td class="py-3 px-6 text-sm font-semibold text-gray-800">
                        {{ $trx->invoice_number }}
                    </td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $trx->kasir->name }}</td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $trx->items->count() }} item</td>
                    <td class="py-3 px-6 text-sm font-bold text-elco-coffee">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-6 text-sm text-gray-500">
                        {{ $trx->created_at->format('d M Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-400">
                        Belum ada transaksi bulan ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<script>
const labels  = @json($labels);
const income  = @json($incomeChart);
const expense = @json($expenseChart);
const profit  = income.map((v, i) => v - expense[i]);

// ── Chart Pemasukan vs Pengeluaran ────────────────────────
new Chart(document.getElementById('branchChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Pemasukan',
                data: income,
                backgroundColor: '#10b98133',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 8,
            },
            {
                label: 'Pengeluaran',
                data: expense,
                backgroundColor: '#ef444433',
                borderColor: '#ef4444',
                borderWidth: 2,
                borderRadius: 8,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                },
                grid: { color: '#f3f4f6' }
            },
            x: { grid: { display: false } }
        }
    }
});

// ── Chart Laba ────────────────────────────────────────────
new Chart(document.getElementById('profitChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Laba Bersih',
            data: profit,
            borderColor: '#5C3D2E',
            backgroundColor: '#5C3D2E15',
            borderWidth: 2.5,
            pointBackgroundColor: '#5C3D2E',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                },
                grid: { color: '#f3f4f6' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush