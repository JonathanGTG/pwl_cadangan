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

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Pengeluaran Cabang</h2>
        <p class="text-sm text-gray-500 mt-1">Catat & pantau pengeluaran operasional</p>
    </div>
    <a href="{{ route('admin.expenses.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Catat Pengeluaran
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl shadow-soft">
        <p class="text-xs text-gray-500 mb-1">Total Bulan Ini</p>
        <p class="text-xl font-bold text-gray-800">
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
                    <p class="text-xs text-gray-400 mt-0.5">{{ $expense->description }}</p>
                </td>
                <td class="py-4 px-6">
                    <span class="text-xs font-medium text-gray-600">
                        {{ $expense->category_label }}
                    </span>
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
                    <form id="form-hapus-{{ $expense->id }}" method="POST" action="{{ route('admin.expenses.destroy', $expense->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                            onclick="elcoConfirm({
                                title: 'Hapus Data?',
                                text: 'Data yang dihapus tidak dapat dikembalikan.',
                                confirmText: 'Ya, Hapus',
                                confirmColor: '#ef4444',
                                onConfirm: () => document.getElementById('form-hapus-{{ $expense->id }}').submit()
                            })"
                            class="...">
                            <i class="ph ph-trash"></i> Hapus
                        </button>
                    </form>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-12 text-center text-gray-400">
                    <i class="ph ph-receipt text-4xl block mb-2"></i>
                    Belum ada pengeluaran dicatat
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection