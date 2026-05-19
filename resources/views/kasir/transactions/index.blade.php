@extends('layouts.kasir')

@section('content')
@if(session('success'))
    <div class="mb-4 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-emerald-700">
        <i class="ph-fill ph-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-red-700">
        <i class="ph-fill ph-x-circle text-xl"></i>
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- KIRI: Daftar Menu --}}
    <div class="space-y-4 xl:col-span-2">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="font-display text-lg font-bold text-gray-800">Menu Tersedia</h2>

            {{-- Filter Kategori --}}
            <div class="flex flex-wrap gap-2 md:justify-end">
                @foreach(['semua' => 'Semua', 'minuman' => 'Minuman', 'makanan_snack' => 'Makanan & Snack'] as $val => $label)
                    <button
                        type="button"
                        onclick="filterMenu('{{ $val }}')"
                        id="cat-{{ $val }}"
                        class="rounded-xl px-4 py-2 text-sm font-medium smooth-transition {{ $val === 'semua' ? 'bg-elco-coffee text-white' : 'bg-white text-gray-500 shadow-soft' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Grid Menu --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3" id="menuGrid">
            @forelse($stocks as $stock)
                @php
                    $isIngredientBased = $stock->menu->isIngredientBased();
                    $available = (int) ($stock->available_portions ?? ($isIngredientBased ? 0 : $stock->stock));
                    $cartLimit = $available;
                @endphp

                <div
                    class="menu-item cursor-pointer overflow-hidden rounded-2xl bg-white shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover active:scale-95"
                    data-category="{{ $stock->menu->category }}"
                    onclick="addToCart({{ $stock->id }}, @js($stock->menu->name), {{ $stock->custom_price ?? $stock->menu->base_price }}, {{ $cartLimit }}, {{ $isIngredientBased ? 'true' : 'false' }})"
                >
                    {{-- Gambar --}}
                    <div class="relative h-32 bg-gradient-to-br from-elco-cream to-orange-50">
                        @if($stock->menu->image)
                            <img
                                src="{{ Storage::disk('public')->url($stock->menu->image) }}"
                                alt="{{ $stock->menu->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <i class="ph-fill ph-coffee text-4xl text-elco-latte/50"></i>
                            </div>
                        @endif

                        <span class="absolute bottom-2 right-2 rounded-lg bg-white/80 px-2 py-0.5 text-xs font-semibold text-gray-600 backdrop-blur-sm">
                            {{ $isIngredientBased ? 'Sisa: '.$available.' porsi' : 'Stok: '.$available }}
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="p-3">
                        <p class="truncate text-sm font-semibold text-gray-800">{{ $stock->menu->name }}</p>
                        <p class="mt-1 text-xs font-bold text-elco-coffee">
                            Rp {{ number_format($stock->custom_price ?? $stock->menu->base_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-12 text-center md:col-span-3">
                    <i class="ph ph-coffee mb-2 block text-5xl text-gray-300"></i>
                    <p class="text-gray-400">Tidak ada menu tersedia</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- KANAN: Keranjang & Checkout --}}
    <div class="space-y-4">
        {{-- Keranjang --}}
        <div class="rounded-3xl bg-white p-5 shadow-soft">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-display font-bold text-gray-800">Pesanan</h2>
                <button
                    type="button"
                    onclick="clearCart()"
                    class="text-xs text-red-400 hover:text-red-600 smooth-transition"
                >
                    Hapus Semua
                </button>
            </div>

            <div id="cartItems" class="max-h-64 space-y-3 overflow-y-auto hide-scrollbar">
                <div id="emptyCart" class="py-8 text-center text-gray-400">
                    <i class="ph ph-shopping-cart mb-2 block text-3xl"></i>
                    <p class="text-sm">Pilih menu untuk ditambahkan</p>
                </div>
            </div>
        </div>

        {{-- Promo --}}
        <div class="rounded-3xl bg-white p-5 shadow-soft">
            <label class="mb-2 block text-sm font-semibold text-gray-700">
                <i class="ph ph-tag mr-1"></i>
                Pilih Promo
            </label>

            <select
                id="promoSelect"
                onchange="applyPromo()"
                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm smooth-transition focus:outline-none focus:ring-2 focus:ring-elco-mocha/30"
            >
                <option value="">— Tanpa Promo —</option>
                @foreach($promotions as $promo)
                    <option
                        value="{{ $promo->id }}"
                        data-type="{{ $promo->discount_type }}"
                        data-value="{{ $promo->discount_value }}"
                        data-min="{{ $promo->min_purchase }}"
                    >
                        {{ $promo->name }} ({{ $promo->discount_label }})
                    </option>
                @endforeach
            </select>

            <p id="promoInfo" class="mt-2 hidden text-xs text-elco-coffee"></p>
        </div>

        {{-- Summary & Checkout --}}
        <div class="rounded-3xl bg-white p-5 shadow-soft">
            <div class="mb-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay">Rp 0</span>
                </div>

                <div class="hidden justify-between text-sm text-red-500" id="discountRow">
                    <span>Diskon</span>
                    <span id="discountDisplay">- Rp 0</span>
                </div>

                <div class="flex justify-between border-t border-gray-100 pt-2 font-display text-lg font-bold text-gray-800">
                    <span>Total</span>
                    <span id="totalDisplay">Rp 0</span>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-4">
                <p class="mb-2 text-sm font-semibold text-gray-500">Metode Pembayaran</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS'] as $val => $label)
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="payment_method"
                                value="{{ $val }}"
                                class="peer sr-only"
                                {{ $val === 'cash' ? 'checked' : '' }}
                            >
                            <div class="rounded-xl border-2 border-gray-200 p-3 text-center text-sm font-medium text-gray-600 smooth-transition peer-checked:border-elco-coffee peer-checked:bg-elco-cream peer-checked:text-elco-coffee">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <button
                type="button"
                onclick="processTransaction()"
                id="processBtn"
                disabled
                class="w-full rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha py-3.5 text-sm font-semibold text-white shadow-md smooth-transition hover:shadow-hover active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <i class="ph ph-check-circle mr-1"></i>
                Proses Transaksi
            </button>
        </div>
    </div>
</div>

{{-- Riwayat Hari Ini --}}
<div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-soft">
    <div class="flex flex-col gap-2 border-b border-gray-100 p-6 sm:flex-row sm:items-end sm:justify-between">
        <h3 class="font-display text-lg font-bold text-gray-800">Transaksi Hari Ini</h3>
        <p class="text-sm text-gray-500">{{ $todayTransactions->count() }} transaksi</p>
    </div>

    @if($todayTransactions->count() > 0)
        <div class="max-h-[520px] overflow-x-auto overflow-y-auto hide-scrollbar">
            <table class="w-full min-w-[720px] text-left">
                <thead>
                    <tr class="sticky top-0 border-b border-gray-100 bg-gray-50 text-xs text-gray-400">
                        <th class="px-6 py-4 font-medium">Invoice</th>
                        <th class="px-6 py-4 font-medium">Item</th>
                        <th class="px-6 py-4 font-medium">Total</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayTransactions as $trx)
                        @php
                            $isRequestCancel = str_starts_with($trx->cancel_reason ?? '', '[REQUEST CANCEL]');
                        @endphp

                        <tr class="border-b border-gray-50 smooth-transition last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="text-base font-semibold text-gray-800">{{ $trx->invoice_number }}</p>
                                <p class="text-xs text-gray-400">{{ $trx->created_at->format('H:i') }}</p>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">{{ $trx->items->count() }} item</td>

                            <td class="px-6 py-4 text-base font-bold text-elco-coffee">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4">
                                @if($isRequestCancel)
                                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                                        Menunggu Admin
                                    </span>
                                @else
                                    <span class="rounded-full px-3 py-1 text-xs font-medium
                                        {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $trx->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $trx->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($trx->status === 'completed' && ! $isRequestCancel)
                                        <button
                                            type="button"
                                            onclick="requestCancel({{ $trx->id }})"
                                            class="rounded-xl bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-600 smooth-transition hover:bg-orange-100 whitespace-nowrap"
                                        >
                                            <i class="ph ph-x-circle mr-1"></i>
                                            Minta Batal
                                        </button>
                                    @endif

                                    <a
                                        href="{{ route('kasir.transactions.show', $trx) }}"
                                        class="rounded-xl bg-elco-cream px-4 py-2 text-sm font-semibold text-elco-coffee smooth-transition hover:bg-elco-latte/30 whitespace-nowrap"
                                    >
                                        <i class="ph ph-receipt mr-1"></i>
                                        Struk
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-10 text-center text-sm text-gray-400">Belum ada transaksi hari ini</div>
    @endif
</div>

{{-- Hidden Form untuk Submit --}}
<form id="transactionForm" action="{{ route('kasir.transactions.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="payment_method" id="paymentMethod" value="cash">
    <input type="hidden" name="promotion_id" id="promotionId" value="">
    <div id="formItems"></div>
</form>

{{-- Modal Sukses --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="mx-4 w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-500">
            <i class="ph-fill ph-check-circle"></i>
        </div>

        <h3 class="mb-1 font-display text-xl font-bold text-gray-800">Transaksi Berhasil!</h3>
        <p class="mb-2 text-sm text-gray-500" id="invoiceDisplay"></p>
        <p class="mb-6 font-display text-2xl font-bold text-elco-coffee" id="totalFinal"></p>

        <div class="flex gap-3">
            <button
                type="button"
                onclick="closeSuccessModal()"
                class="flex-1 rounded-2xl border border-gray-200 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50"
            >
                Tutup
            </button>

            <a
                href=""
                id="receiptLink"
                class="flex-1 rounded-2xl bg-elco-coffee py-3 text-center text-sm font-semibold text-white"
            >
                <i class="ph ph-receipt mr-1"></i>
                Struk
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── State Keranjang ──────────────────────────────────────
let cart = {};
const rupiah = number => 'Rp ' + Number(number).toLocaleString('id-ID');

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}

// ── Tambah ke Keranjang ──────────────────────────────────
function addToCart(stockId, name, price, maxStock, ingredientBased = false) {
    maxStock = parseInt(maxStock);

    if (maxStock <= 0) {
        elcoError('Stok Habis', ingredientBased ? `Sisa porsi ${name} kosong` : `Stok ${name} kosong`);
        return;
    }

    if (cart[stockId]) {
        if (cart[stockId].qty >= maxStock) {
            elcoError('Stok Habis', ingredientBased ? `Sisa porsi ${name} hanya ${maxStock}` : `Stok ${name} hanya tersisa ${maxStock}`);
            return;
        }

        cart[stockId].qty++;
    } else {
        cart[stockId] = {
            stockId,
            name,
            price: parseFloat(price),
            qty: 1,
            maxStock,
            ingredientBased,
        };
    }

    renderCart();
}

// ── Render Keranjang ─────────────────────────────────────
function renderCart() {
    const container = document.getElementById('cartItems');
    const keys = Object.keys(cart);

    if (keys.length === 0) {
        container.innerHTML = `
            <div class="py-8 text-center text-gray-400">
                <i class="ph ph-shopping-cart mb-2 block text-3xl"></i>
                <p class="text-sm">Pilih menu untuk ditambahkan</p>
            </div>`;
        updateTotals();
        return;
    }

    container.innerHTML = keys.map(id => {
        const item = cart[id];

        return `
            <div class="flex items-center justify-between rounded-2xl bg-gray-50 p-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-gray-800">${escapeHtml(item.name)}</p>
                    <p class="text-xs text-elco-coffee">${rupiah(item.price)} / item</p>
                </div>

                <div class="ml-3 flex flex-shrink-0 items-center gap-2">
                    <button
                        type="button"
                        onclick="changeQty(${id}, -1)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-base font-bold text-gray-600 smooth-transition hover:bg-red-50 hover:text-red-500"
                    >−</button>
                    <span class="w-6 text-center text-sm font-bold text-gray-800">${item.qty}</span>
                    <button
                        type="button"
                        onclick="changeQty(${id}, 1)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-base font-bold text-gray-600 smooth-transition hover:bg-emerald-50 hover:text-emerald-500"
                    >+</button>
                </div>
            </div>`;
    }).join('');

    updateTotals();
}

// ── Ubah Qty ─────────────────────────────────────────────
function changeQty(id, delta) {
    if (!cart[id]) return;

    cart[id].qty += delta;

    if (cart[id].qty <= 0) {
        delete cart[id];
    } else if (cart[id].qty > cart[id].maxStock) {
        cart[id].qty = cart[id].maxStock;
    }

    renderCart();
}

// ── Hapus Semua ──────────────────────────────────────────
function clearCart() {
    cart = {};
    renderCart();
}

// ── Update Total ─────────────────────────────────────────
function updateTotals() {
    const subtotal = Object.values(cart).reduce((sum, item) => sum + item.price * item.qty, 0);
    applyPromo(subtotal);

    const hasItems = Object.keys(cart).length > 0;
    const button = document.getElementById('processBtn');

    if (button) {
        button.disabled = !hasItems;
    }
}

// ── Apply Promo ──────────────────────────────────────────
function applyPromo(subtotalOverride) {
    const subtotal = subtotalOverride !== undefined
        ? subtotalOverride
        : Object.values(cart).reduce((sum, item) => sum + item.price * item.qty, 0);

    const select = document.getElementById('promoSelect');
    const option = select?.options[select.selectedIndex];
    let discount = 0;

    if (option?.value) {
        const type = option.dataset.type;
        const value = parseFloat(option.dataset.value);
        const minPurchase = parseFloat(option.dataset.min || 0);

        if (subtotal >= minPurchase) {
            discount = type === 'percentage'
                ? Math.round(subtotal * value / 100)
                : Math.min(value, subtotal);
        }
    }

    const subtotalEl = document.getElementById('subtotalDisplay');
    const discountEl = document.getElementById('discountDisplay');
    const totalEl = document.getElementById('totalDisplay');
    const discountRow = document.getElementById('discountRow');

    if (subtotalEl) subtotalEl.textContent = rupiah(subtotal);
    if (discountEl) discountEl.textContent = '- ' + rupiah(discount);
    if (totalEl) totalEl.textContent = rupiah(subtotal - discount);

    if (discountRow) {
        discountRow.classList.toggle('hidden', discount <= 0);
        discountRow.classList.toggle('flex', discount > 0);
    }

    return { subtotal, discount };
}

// ── Filter Kategori ──────────────────────────────────────
function filterMenu(category) {
    document.querySelectorAll('[id^="cat-"]').forEach(button => {
        button.classList.remove('bg-elco-coffee', 'text-white');
        button.classList.add('bg-white', 'text-gray-500', 'shadow-soft');
    });

    const activeButton = document.getElementById('cat-' + category);

    if (activeButton) {
        activeButton.classList.add('bg-elco-coffee', 'text-white');
        activeButton.classList.remove('bg-white', 'text-gray-500', 'shadow-soft');
    }

    document.querySelectorAll('.menu-item').forEach(item => {
        const itemCategory = item.dataset.category;
        const shouldShow = category === 'semua'
            || category === itemCategory
            || (category === 'makanan_snack' && (itemCategory === 'makanan' || itemCategory === 'snack'));

        item.style.display = shouldShow ? '' : 'none';
    });
}

// ── Proses Transaksi ─────────────────────────────────────
async function processTransaction() {
    const keys = Object.keys(cart);

    if (keys.length === 0) {
        elcoError('Keranjang Kosong', 'Pilih menu terlebih dahulu!');
        return;
    }

    const { subtotal, discount } = applyPromo();
    const total = subtotal - discount;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
    const paymentLabels = { cash: 'Cash', transfer: 'Transfer', qris: 'QRIS' };

    const itemList = Object.values(cart)
        .map(item => `• ${escapeHtml(item.name)} x${item.qty} = Rp ${(item.price * item.qty).toLocaleString('id-ID')}`)
        .join('\n');

    const confirmed = await Swal.fire({
        title: 'Konfirmasi Pesanan',
        html: `
            <div class="space-y-2 text-left text-sm">
                <div class="whitespace-pre-line rounded-xl bg-gray-50 p-3 font-mono text-xs">${itemList}</div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-semibold">Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
                ${discount > 0 ? `<div class="flex justify-between text-emerald-600">
                    <span>Diskon</span>
                    <span>- Rp ${discount.toLocaleString('id-ID')}</span>
                </div>` : ''}
                <div class="flex justify-between border-t pt-1 text-base font-bold text-gray-800">
                    <span>Total</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
                <div class="flex justify-between pt-1 text-xs text-gray-500">
                    <span>Metode Bayar</span>
                    <span class="font-medium">${paymentLabels[paymentMethod]}</span>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Proses Sekarang',
        cancelButtonText: 'Periksa Lagi',
        confirmButtonColor: '#5C3D2E',
        customClass: {
            popup: 'swal-elco-popup',
            confirmButton: 'swal-elco-confirm',
            cancelButton: 'swal-elco-cancel',
        },
    });

    if (!confirmed.isConfirmed) return;

    const button = document.getElementById('processBtn');
    button.disabled = true;
    button.innerHTML = '<i class="ph ph-spinner animate-spin mr-2"></i> Memproses...';

    const items = keys.map(id => ({
        menu_stock_id: parseInt(id),
        quantity: cart[id].qty,
    }));

    const promotionId = document.getElementById('promoSelect')?.value || null;

    try {
        const response = await fetch('{{ route("kasir.transactions.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ items, payment_method: paymentMethod, promotion_id: promotionId }),
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('invoiceDisplay').textContent = data.invoice ?? '';
            document.getElementById('totalFinal').textContent = rupiah(total);

            if (data.transaction_id) {
                document.getElementById('receiptLink').href = `/kasir/transactions/${data.transaction_id}`;
            }

            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            clearCart();
        } else {
            elcoError('Transaksi Gagal', data.message ?? 'Terjadi kesalahan!');
        }
    } catch (error) {
        elcoError('Error', 'Gagal terhubung ke server. Coba lagi.');
    } finally {
        button.disabled = false;
        button.innerHTML = '<i class="ph ph-check-circle mr-2"></i> Proses Transaksi';
        updateTotals();
    }
}

// ── Request Pembatalan ───────────────────────────────────
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
        elcoSuccess('Permintaan Terkirim!', 'Admin akan memproses pembatalan segera.');
        setTimeout(() => location.reload(), 1500);
    } else {
        elcoError('Gagal', data.message);
    }
}

// ── Tutup Modal Sukses ───────────────────────────────────
function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    location.reload();
}

// ── Jam Realtime ─────────────────────────────────────────
function updateTime() {
    const el = document.getElementById('realtimeClock');

    if (el) {
        el.textContent = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    }
}

setInterval(updateTime, 1000);
updateTime();
</script>
@endpush
