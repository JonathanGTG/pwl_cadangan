@extends('layouts.kasir')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('kasir.transactions.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Detail Transaksi</h2>
            <p class="text-sm text-gray-500">{{ $transaction->invoice_number }}</p>
        </div>
    </div>

    {{-- Struk --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">

        {{-- Header Struk --}}
        <div class="text-center mb-6 pb-4 border-b border-dashed border-gray-200">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white mx-auto mb-2">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <h3 class="font-display font-bold text-elco-coffee text-lg">ELCO</h3>
            <p class="text-xs text-gray-500">{{ $transaction->branch->name ?? '' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
        </div>

        {{-- Status --}}
        <div class="flex justify-between items-center mb-4">
            <span class="text-sm text-gray-500">Status</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $transaction->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>

        {{-- Item List --}}
        <div class="space-y-2 mb-4">
            @foreach($transaction->items as $item)
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $item->menu_name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                    </p>
                </div>
                <span class="text-sm font-semibold text-gray-800">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="space-y-2 pt-3 border-t border-dashed border-gray-200">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaction->discount_amount > 0)
            <div class="flex justify-between text-sm text-red-500">
                <span>Diskon ({{ $transaction->promotion->name ?? '' }})</span>
                <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-display font-bold text-gray-800 text-lg pt-2">
                <span>Total</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>Pembayaran</span>
                <span class="capitalize font-medium">{{ $transaction->payment_method }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>Kasir</span>
                <span>{{ $transaction->kasir->name }}</span>
            </div>
        </div>
    </div>
</div>
@endsection