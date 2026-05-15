@extends('layouts.manager')

@section('content')

{{-- Filter --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Laporan Keuangan</h2>
        <p class="text-sm text-gray-500 mt-1">Ringkasan operasional semua cabang</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-soft p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
            <select name="month"
                class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
            <select name="year"
                class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cabang</label>
            <select name="branch_id"
                class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
                @endforeach
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
            {{ number_format($totalTransaction) }} struk
        </p>
    </div>
</div>

{{-- Grafik --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

    {{-- Grafik Pemasukan vs Pengeluaran --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">
            Pemasukan vs Pengeluaran {{ $year }}
        </h3>
        <div class="h-64">
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>

    {{-- Grafik Laba Per Bulan --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-4">
            Laba Bersih Per Bulan {{ $year }}
        </h3>
        <div class="h-64">
            <canvas id="profitChart"></canvas>
        </div>
    </div>
</div>

{{-- Performa Per Cabang --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">
            Performa Per Cabang —
            {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-4 px-6 font-medium">Cabang</th>
                    <th class="py-4 px-6 font-medium">Pemasukan</th>
                    <th class="py-4 px-6 font-medium">Pengeluaran</th>
                    <th class="py-4 px-6 font-medium">Laba Bersih</th>
                    <th class="py-4 px-6 font-medium">Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branchPerformance as $perf)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-400 flex items-center justify-center">
                                <i class="ph-fill ph-storefront"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $perf['name'] }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm font-semibold text-emerald-600">
                        Rp {{ number_format($perf['income'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-6 text-sm font-semibold text-red-500">
                        Rp {{ number_format($perf['expense'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm font-bold {{ $perf['profit'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                            Rp {{ number_format($perf['profit'], 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $perf['trx'] }} transaksi
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-400">
                        <i class="ph ph-chart-bar text-4xl block mb-2"></i>
                        Belum ada data pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel Pemasukan Per Cabang --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">
            Detail Pemasukan — {{ \DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-3 px-6 font-medium">Invoice</th>
                    <th class="py-3 px-6 font-medium">Cabang</th>
                    <th class="py-3 px-6 font-medium">Kasir</th>
                    <th class="py-3 px-6 font-medium">Total</th>
                    <th class="py-3 px-6 font-medium">Metode</th>
                    <th class="py-3 px-6 font-medium">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $incomeQuery = \App\Models\Transaction::where('status', 'completed')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->with('branch', 'kasir');
                    if ($branchId) $incomeQuery->where('branch_id', $branchId);
                    $incomeList = $incomeQuery->latest()->take(20)->get();
                @endphp
                @forelse($incomeList as $trx)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                    <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ $trx->invoice_number }}</td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $trx->branch->name }}</td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $trx->kasir->name }}</td>
                    <td class="py-3 px-6 text-sm font-bold text-emerald-600">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-6 text-xs font-medium uppercase text-gray-500">{{ $trx->payment_method }}</td>
                    <td class="py-3 px-6 text-xs text-gray-500">{{ $trx->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada pemasukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Tabel Detail Pengeluaran --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">
            Detail Pengeluaran — {{ \DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-3 px-6 font-medium">Judul</th>
                    <th class="py-3 px-6 font-medium">Cabang</th>
                    <th class="py-3 px-6 font-medium">Kategori</th>
                    <th class="py-3 px-6 font-medium">Jumlah</th>
                    <th class="py-3 px-6 font-medium">Diajukan Oleh</th>
                    <th class="py-3 px-6 font-medium">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $expenseQuery = \App\Models\Expense::where('status', 'verified')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->with('branch', 'createdBy');
                    if ($branchId) $expenseQuery->where('branch_id', $branchId);
                    $expenseList = $expenseQuery->latest()->take(20)->get();
                @endphp
                @forelse($expenseList as $exp)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                    <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ $exp->title }}</td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $exp->branch?->name ?? '—' }}</td>
                    <td class="py-3 px-6">
                        <span class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-600 font-medium">
                            {{ $exp->category_label ?? $exp->category }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm font-bold text-red-500">
                        Rp {{ number_format($exp->amount, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ $exp->createdBy?->name ?? '—' }}</td>
                    <td class="py-3 px-6 text-xs text-gray-500">{{ $exp->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">Belum ada pengeluaran</td>
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
const labels   = @json($labels);
const income   = @json($incomeChart);
const expense  = @json($expenseChart);
const profit   = income.map((v, i) => v - expense[i]);

// Warna ELCO
const coffeeColor  = '#5C3D2E';
const mochaColor   = '#8B5E3C';
const emeraldColor = '#10b981';
const redColor     = '#ef4444';
const blueColor    = '#3b82f6';

// ── Chart 1: Pemasukan vs Pengeluaran ────────────────────
new Chart(document.getElementById('incomeExpenseChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Pemasukan',
                data: income,
                backgroundColor: emeraldColor + '33',
                borderColor: emeraldColor,
                borderWidth: 2,
                borderRadius: 8,
            },
            {
                label: 'Pengeluaran',
                data: expense,
                backgroundColor: redColor + '33',
                borderColor: redColor,
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
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID'),
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

// ── Chart 2: Laba Per Bulan ───────────────────────────────
new Chart(document.getElementById('profitChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Laba Bersih',
            data: profit,
            borderColor: coffeeColor,
            backgroundColor: coffeeColor + '15',
            borderWidth: 2.5,
            pointBackgroundColor: coffeeColor,
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
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID'),
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