@extends('layouts.manager')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="mb-6">
    <h2 class="text-xl font-display font-bold text-gray-800">Pengeluaran Semua Cabang</h2>
    <p class="text-sm text-gray-500 mt-1">Verifikasi pengeluaran operasional cabang</p>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-soft p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
            <select name="month" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
            <select name="year" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cabang</label>
            <select name="branch_id" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 bg-white">
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
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Pengeluaran</p>
        <p class="text-xl font-bold text-red-600">
            Rp {{ number_format($summary['total'], 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Menunggu Verifikasi</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Terverifikasi</p>
        <p class="text-xl font-bold text-emerald-600">
            Rp {{ number_format($summary['verified'], 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Judul</th>
                <th class="py-4 px-6 font-medium">Cabang</th>
                <th class="py-4 px-6 font-medium">Kategori</th>
                <th class="py-4 px-6 font-medium">Jumlah</th>
                <th class="py-4 px-6 font-medium">Tanggal</th>
                <th class="py-4 px-6 font-medium">Status</th>
                <th class="py-4 px-6 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <p class="text-sm font-semibold text-gray-800">{{ $expense->title }}</p>
                    <p class="text-xs text-gray-400">{{ $expense->createdBy?->name ?? '—' }}</p>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    {{ $expense->branch?->name ?? '—' }}
                </td>
                <td class="py-4 px-6 text-xs text-gray-600">
                    {{ $expense->category_label }}
                </td>
                <td class="py-4 px-6">
                    <span class="text-sm font-bold text-red-600">
                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm text-gray-500">
                    {{ $expense->expense_date->format('d M Y') }}
                </td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $expense->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $expense->status === 'verified' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $expense->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ $expense->status === 'pending' ? '⏳ Pending'
                            : ($expense->status === 'verified' ? '✅ Verified' : '❌ Ditolak') }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    @if($expense->status === 'pending')
                    <div class="flex gap-2">
                        {{-- Verifikasi --}}
                        <form id="verify-{{ $expense->id }}"
                            action="{{ route('manager.expenses.verify', $expense) }}" method="POST">
                            @csrf
                            <button type="button"
                                onclick="elcoConfirm({
                                    title: 'Verifikasi Pengeluaran?',
                                    text: 'Rp {{ number_format($expense->amount, 0, ',', '.') }} akan dicatat sebagai pengeluaran terverifikasi.',
                                    confirmText: 'Ya, Verifikasi',
                                    confirmColor: '#10b981',
                                    icon: 'question',
                                    onConfirm: () => document.getElementById('verify-{{ $expense->id }}').submit()
                                })"
                                class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                                <i class="ph ph-check"></i> Verifikasi
                            </button>
                        </form>

                        {{-- Tolak --}}
                        <form id="reject-exp-{{ $expense->id }}"
                            action="{{ route('manager.expenses.reject', $expense) }}" method="POST">
                            @csrf
                            <input type="hidden" name="rejection_note" value="Tidak sesuai kebijakan">
                            <button type="button"
                                onclick="elcoConfirm({
                                    title: 'Tolak Pengeluaran?',
                                    text: 'Pengeluaran ini akan ditolak.',
                                    confirmText: 'Ya, Tolak',
                                    confirmColor: '#ef4444',
                                    icon: 'warning',
                                    onConfirm: () => document.getElementById('reject-exp-{{ $expense->id }}').submit()
                                })"
                                class="text-xs font-medium text-red-500 bg-red-50 px-3 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                                <i class="ph ph-x"></i> Tolak
                            </button>
                        </form>
                    </div>
                    @else
                        <span class="text-xs text-gray-400">Sudah diproses</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400">
                    <i class="ph ph-receipt text-4xl block mb-2"></i>
                    Tidak ada pengeluaran pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection