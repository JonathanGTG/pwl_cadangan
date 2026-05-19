@extends('layouts.kasir')

@section('content')
<div class="mx-auto max-w-lg">
    <div class="mb-6 flex items-center gap-4">
        <a
            href="{{ route('kasir.transactions.index') }}"
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-gray-500 shadow-soft smooth-transition hover:text-elco-coffee hover:shadow-hover"
        >
            <i class="ph ph-arrow-left"></i>
        </a>

        <div class="min-w-0">
            <h2 class="font-display text-xl font-bold text-gray-800">Detail Transaksi</h2>
            <p class="truncate text-sm text-gray-500">{{ $transaction->invoice_number }}</p>
        </div>
    </div>

    {{-- Struk --}}
    <div class="rounded-3xl bg-white p-6 shadow-soft">
        {{-- Header Struk --}}
        <div class="mb-6 border-b border-dashed border-gray-200 pb-4 text-center">
            <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha text-white">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <h3 class="font-display text-lg font-bold text-elco-coffee">ELCO</h3>
            <p class="text-xs text-gray-500">{{ $transaction->branch->name ?? '' }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
        </div>

        {{-- Status --}}
        <div class="mb-4 flex items-center justify-between">
            <span class="text-sm text-gray-500">Status</span>
            <span class="rounded-full px-3 py-1 text-xs font-semibold
                {{ $transaction->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>

        {{-- Item List --}}
        <div class="mb-4 space-y-2">
            @foreach($transaction->items as $item)
                <div class="flex items-center justify-between gap-4 border-b border-gray-50 py-2">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-800">{{ $item->menu_name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <span class="flex-shrink-0 text-sm font-semibold text-gray-800">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="space-y-2 border-t border-dashed border-gray-200 pt-3">
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

            <div class="flex justify-between pt-2 font-display text-lg font-bold text-gray-800">
                <span>Total</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between text-sm text-gray-500">
                <span>Pembayaran</span>
                <span class="font-medium capitalize">{{ $transaction->payment_method }}</span>
            </div>

            <div class="flex justify-between text-sm text-gray-500">
                <span>Kasir</span>
                <span>{{ $transaction->kasir->name }}</span>
            </div>
        </div>
    </div>

    @php
        $isRequestCancel = str_starts_with($transaction->cancel_reason ?? '', '[REQUEST CANCEL]');
    @endphp

    @if($transaction->status === 'completed' && ! $isRequestCancel)
        <button
            type="button"
            onclick="requestCancel({{ $transaction->id }})"
            class="mt-4 w-full rounded-2xl bg-orange-500 py-3 text-sm font-semibold text-white smooth-transition hover:bg-orange-600"
        >
            <i class="ph ph-x-circle mr-1"></i>
            Minta Pembatalan Pesanan
        </button>
    @elseif($isRequestCancel)
        <div class="mt-4 rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm text-orange-700">
            Permintaan pembatalan sudah dikirim dan menunggu admin cabang.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
async function requestCancel(id) {
    const { value: reason } = await Swal.fire({
        title: 'Alasan Pembatalan',
        input: 'textarea',
        inputPlaceholder: 'Jelaskan alasan pembatalan...',
        showCancelButton: true,
        confirmButtonText: 'Kirim',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#5C3D2E',
        customClass: {
            popup: 'swal-elco-popup',
            confirmButton: 'swal-elco-confirm',
            cancelButton: 'swal-elco-cancel',
        },
        inputValidator: value => {
            if (!value || value.length < 5) return 'Alasan minimal 5 karakter!';
        },
    });

    if (!reason) return;

    const response = await fetch(`/kasir/transactions/${id}/request-cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ cancel_reason: reason }),
    });

    const data = await response.json();

    if (data.success) {
        Swal.fire('Terkirim', data.message, 'success').then(() => location.reload());
    } else {
        Swal.fire('Gagal', data.message, 'error');
    }
}
</script>
@endpush
