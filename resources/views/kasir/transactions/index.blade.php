@extends('layouts.kasir')

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl">
    <i class="ph-fill ph-x-circle text-xl"></i> {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ═══ KIRI: Daftar Menu ═══ --}}
    <div class="xl:col-span-2 space-y-4">

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-display font-bold text-gray-800">Menu Tersedia</h2>
            {{-- Filter Kategori --}}
            <div class="flex gap-2">
                @foreach(['semua' => 'Semua', 'minuman' => '☕ Minuman', 'makanan_snack' => '🍱 Makanan & Snack'] as $val => $label)
                <button onclick="filterMenu('{{ $val }}')" id="cat-{{ $val }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-medium smooth-transition
                    {{ $val === 'semua' ? 'bg-elco-coffee text-white' : 'bg-white text-gray-500 shadow-soft' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Grid Menu --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="menuGrid">
            @forelse($stocks as $stock)
            @php
                $isIngredientBased = $stock->menu->isIngredientBased();
                $cartLimit = $isIngredientBased ? 999 : (int) $stock->stock;
            @endphp
            <div class="menu-item bg-white rounded-2xl shadow-soft overflow-hidden cursor-pointer smooth-transition hover:-translate-y-1 hover:shadow-hover active:scale-95"
                 data-category="{{ $stock->menu->category }}"
                 onclick="addToCart({{ $stock->id }}, '{{ addslashes($stock->menu->name) }}', {{ $stock->custom_price ?? $stock->menu->base_price }}, {{ $cartLimit }}, {{ $isIngredientBased ? 'true' : 'false' }})">

                {{-- Gambar --}}
                <div class="h-32 bg-gradient-to-br from-elco-cream to-orange-50 relative">
                    @if($stock->menu->image)
                        <img src="{{ Storage::url($stock->menu->image) }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ph-fill ph-coffee text-4xl text-elco-latte/50"></i>
                        </div>
                    @endif
                    <span class="absolute bottom-2 right-2 bg-white/80 backdrop-blur-sm text-xs font-semibold text-gray-600 px-2 py-0.5 rounded-lg">
                        {{ $isIngredientBased ? 'Bahan tersedia' : 'Stok: '.$stock->stock }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="p-3">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $stock->menu->name }}</p>
                    <p class="text-xs font-bold text-elco-coffee mt-1">
                        Rp {{ number_format($stock->custom_price ?? $stock->menu->base_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="col-span-3 py-12 text-center">
                <i class="ph ph-coffee text-5xl text-gray-300 block mb-2"></i>
                <p class="text-gray-400">Tidak ada menu tersedia</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ═══ KANAN: Keranjang & Checkout ═══ --}}
    <div class="space-y-4">

        {{-- Keranjang --}}
        <div class="bg-white rounded-3xl shadow-soft p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-gray-800">Pesanan</h2>
                <button onclick="clearCart()"
                    class="text-xs text-red-400 hover:text-red-600 smooth-transition">
                    Hapus Semua
                </button>
            </div>

            {{-- List Item Keranjang --}}
            <div id="cartItems" class="space-y-3 max-h-64 overflow-y-auto hide-scrollbar">
                <div id="emptyCart" class="py-8 text-center text-gray-400">
                    <i class="ph ph-shopping-cart text-3xl block mb-2"></i>
                    <p class="text-sm">Pilih menu untuk ditambahkan</p>
                </div>
            </div>
        </div>

        {{-- Promo --}}
        <div class="bg-white rounded-3xl shadow-soft p-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="ph ph-tag mr-1"></i> Pilih Promo
            </label>
            <select id="promoSelect" onchange="applyPromo()"
                class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white">
                <option value="">— Tanpa Promo —</option>
                @foreach($promotions as $promo)
                <option value="{{ $promo->id }}"
                    data-type="{{ $promo->discount_type }}"
                    data-value="{{ $promo->discount_value }}"
                    data-min="{{ $promo->min_purchase }}">
                    {{ $promo->name }} ({{ $promo->discount_label }})
                </option>
                @endforeach
            </select>
            <p id="promoInfo" class="text-xs text-elco-coffee mt-2 hidden"></p>
        </div>

        {{-- Summary & Checkout --}}
        <div class="bg-white rounded-3xl shadow-soft p-5">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-red-500" id="discountRow" style="display:none!important">
                    <span>Diskon</span>
                    <span id="discountDisplay">- Rp 0</span>
                </div>
                <div class="flex justify-between font-display font-bold text-gray-800 text-lg border-t border-gray-100 pt-2">
                    <span>Total</span>
                    <span id="totalDisplay">Rp 0</span>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 mb-2">Metode Pembayaran</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['cash' => '💵 Cash', 'transfer' => '🏦 Transfer', 'qris' => '📱 QRIS'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $val }}"
                               class="sr-only peer" {{ $val === 'cash' ? 'checked' : '' }}>
                        <div class="text-center p-2 border-2 border-gray-200 rounded-xl text-xs font-medium text-gray-600
                                    peer-checked:border-elco-coffee peer-checked:bg-elco-cream peer-checked:text-elco-coffee smooth-transition">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <button onclick="processTransaction()" id="processBtn"
                class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white font-semibold text-sm shadow-md hover:shadow-hover smooth-transition active:scale-95">
                <i class="ph ph-check-circle mr-1"></i> Proses Transaksi
            </button>
        </div>

        {{-- Riwayat Hari Ini --}}
        <div class="bg-white rounded-3xl shadow-soft p-5">
            <h3 class="font-display font-semibold text-gray-700 mb-3 text-sm">
                Transaksi Hari Ini
                <span class="text-xs text-gray-400 font-normal ml-1">({{ $todayTransactions->count() }} transaksi)</span>
            </h3>
            <div class="space-y-2 max-h-48 overflow-y-auto hide-scrollbar">
                @forelse($todayTransactions as $trx)
                <a href="{{ route('kasir.transactions.show', $trx) }}"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 smooth-transition">
                    <div>
                        <p class="text-xs font-semibold text-gray-700">{{ $trx->invoice_number }}</p>
                        <p class="text-xs text-gray-400">{{ $trx->items->count() }} item</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-elco-coffee">
                            Rp {{ number_format($trx->total, 0, ',', '.') }}
                        </p>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                </a>
                {{-- Riwayat Transaksi Hari Ini --}}
@if($todayTransactions->count() > 0)
<div class="mt-6 bg-white rounded-3xl shadow-soft overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">
            Transaksi Hari Ini
            <span class="text-sm text-gray-400 font-normal ml-2">
                {{ $todayTransactions->count() }} transaksi
            </span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-3 px-5 font-medium">Invoice</th>
                    <th class="py-3 px-5 font-medium">Item</th>
                    <th class="py-3 px-5 font-medium">Total</th>
                    <th class="py-3 px-5 font-medium">Status</th>
                    <th class="py-3 px-5 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todayTransactions as $trx)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                    <td class="py-3 px-5">
                        <p class="text-sm font-semibold text-gray-800">{{ $trx->invoice_number }}</p>
                        <p class="text-xs text-gray-400">{{ $trx->created_at->format('H:i') }}</p>
                    </td>
                    <td class="py-3 px-5 text-sm text-gray-600">
                        {{ $trx->items->count() }} item
                    </td>
                    <td class="py-3 px-5 text-sm font-bold text-elco-coffee">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-5">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $trx->status === 'pending'   ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $trx->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-5">
                        <div class="flex gap-2">
                            {{-- Selesaikan --}}
                            @if($trx->status === 'completed' && !str_starts_with($trx->cancel_reason ?? '', '[REQUEST CANCEL]'))
                                <button onclick="requestCancel({{ $trx->id }})"
                                    class="text-xs font-medium text-orange-500 bg-orange-50 px-3 py-1.5 rounded-xl hover:bg-orange-100 smooth-transition">
                                    <i class="ph ph-x-circle"></i> Minta Batal
                                </button>
                                @elseif(str_starts_with($trx->cancel_reason ?? '', '[REQUEST CANCEL]'))
                                <span class="text-xs text-orange-400 font-medium">⏳ Menunggu Admin</span>
                            @endif

                            {{-- Lihat Struk --}}
                            <a href="{{ route('kasir.transactions.show', $trx) }}"
                               class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-1.5 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                                <i class="ph ph-receipt"></i> Struk
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Belum ada transaksi hari ini</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Hidden Form untuk Submit --}}
<form id="transactionForm" action="{{ route('kasir.transactions.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="payment_method" id="paymentMethod" value="cash">
    <input type="hidden" name="promotion_id" id="promotionId" value="">
    <div id="formItems"></div>
</form>

@endsection

{{-- Modal Sukses --}}
<div id="successModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm mx-4 text-center">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-500 flex items-center justify-center text-3xl mx-auto mb-4">
            <i class="ph-fill ph-check-circle"></i>
        </div>
        <h3 class="font-display font-bold text-gray-800 text-xl mb-1">Transaksi Berhasil!</h3>
        <p class="text-sm text-gray-500 mb-2" id="invoiceDisplay"></p>
        <p class="text-2xl font-display font-bold text-elco-coffee mb-6" id="totalFinal"></p>
        <div class="flex gap-3">
            <button onclick="closeSuccessModal()"
                class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                Tutup
            </button>
            <a href="" id="receiptLink"
               class="flex-1 py-3 rounded-2xl bg-elco-coffee text-white text-sm font-semibold text-center">
                <i class="ph ph-receipt mr-1"></i> Struk
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── State Keranjang ──────────────────────────────────────
let cart = {};
const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');

// ── Tambah ke Keranjang ──────────────────────────────────
function addToCart(stockId, name, price, maxStock, ingredientBased = false) {
    maxStock = parseInt(maxStock);
    if (!ingredientBased && maxStock <= 0) {
        elcoError('Stok Habis', `Stok ${name} kosong`);
        return;
    }

    if (cart[stockId]) {
        if (!cart[stockId].ingredientBased && cart[stockId].qty >= maxStock) {
            elcoError('Stok Habis', `Stok ${name} hanya tersisa ${maxStock}`);
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
            ingredientBased
        };
    }
    renderCart();
}

// ── Render Keranjang ─────────────────────────────────────
function renderCart() {
    const container = document.getElementById('cartItems');
    const keys      = Object.keys(cart);

    if (keys.length === 0) {
        container.innerHTML = `
            <div class="py-8 text-center text-gray-400">
                <i class="ph ph-shopping-cart text-3xl block mb-2"></i>
                <p class="text-sm">Pilih menu untuk ditambahkan</p>
            </div>`;
        updateTotals();
        return;
    }

    container.innerHTML = keys.map(id => {
        const item = cart[id];
        return `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">${item.name}</p>
                <p class="text-xs text-elco-coffee">${rupiah(item.price)} / item</p>
            </div>
            <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                <button onclick="changeQty(${id}, -1)"
                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-600
                           flex items-center justify-center hover:bg-red-50 hover:text-red-500
                           smooth-transition font-bold text-sm">−</button>
                <span class="text-sm font-bold text-gray-800 w-6 text-center">${item.qty}</span>
                <button onclick="changeQty(${id}, 1)"
                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-600
                           flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-500
                           smooth-transition font-bold text-sm">+</button>
            </div>
        </div>`;
    }).join('');

    updateTotals();
}

// ── Ubah Qty ─────────────────────────────────────────────
function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    else if (!cart[id].ingredientBased && cart[id].qty > cart[id].maxStock) cart[id].qty = cart[id].maxStock;
    renderCart();
}

// ── Hapus Semua ──────────────────────────────────────────
function clearCart() {
    cart = {};
    renderCart();
}

// ── Update Total ─────────────────────────────────────────
function updateTotals() {
    const subtotal = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
    applyPromo(subtotal);
    const hasItems = Object.keys(cart).length > 0;
    const btn = document.getElementById('processBtn');
    if (btn) btn.disabled = !hasItems;
}

// ── Apply Promo ──────────────────────────────────────────
function applyPromo(subtotalOverride) {
    const subtotal = subtotalOverride !== undefined
        ? subtotalOverride
        : Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);

    const select = document.getElementById('promoSelect');
    const opt    = select?.options[select.selectedIndex];
    let discount = 0;

    if (opt?.value) {
        const type  = opt.dataset.type;
        const value = parseFloat(opt.dataset.value);
        const min   = parseFloat(opt.dataset.min || 0);
        if (subtotal >= min) {
            discount = type === 'percentage'
                ? Math.round(subtotal * value / 100)
                : Math.min(value, subtotal);
        }
    }

    const subtotalEl  = document.getElementById('subtotalDisplay');
    const discountEl  = document.getElementById('discountDisplay');
    const totalEl     = document.getElementById('totalDisplay');
    const discountRow = document.getElementById('discountRow');

    if (subtotalEl)  subtotalEl.textContent  = rupiah(subtotal);
    if (discountEl)  discountEl.textContent  = '- ' + rupiah(discount);
    if (totalEl)     totalEl.textContent     = rupiah(subtotal - discount);
    if (discountRow) discountRow.style.display = discount > 0 ? 'flex' : 'none';

    return { subtotal, discount };
}

// ── Filter Kategori ──────────────────────────────────────
function filterMenu(category) {
    document.querySelectorAll('[id^="cat-"]').forEach(btn => {
        btn.classList.remove('bg-elco-coffee', 'text-white');
        btn.classList.add('bg-white', 'text-gray-500', 'shadow-soft');
    });
    const btn = document.getElementById('cat-' + category);
    if (btn) {
        btn.classList.add('bg-elco-coffee', 'text-white');
        btn.classList.remove('bg-white', 'text-gray-500', 'shadow-soft');
    }
    document.querySelectorAll('.menu-item').forEach(item => {
        const cat  = item.dataset.category;
        const show = category === 'semua'
            || category === cat
            || (category === 'makanan_snack' && (cat === 'makanan' || cat === 'snack'));
        item.style.display = show ? '' : 'none';
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
    const paymentLabels = { cash: '💵 Cash', transfer: '🏦 Transfer', qris: '📱 QRIS' };

    // Buat ringkasan pesanan untuk konfirmasi
    const itemList = Object.values(cart)
        .map(i => `• ${i.name} x${i.qty} = Rp ${(i.price * i.qty).toLocaleString('id-ID')}`)
        .join('\n');

    const confirmed = await Swal.fire({
        title: 'Konfirmasi Pesanan',
        html: `
            <div class="text-left text-sm space-y-2">
                <div class="bg-gray-50 rounded-xl p-3 text-xs font-mono whitespace-pre-line">${itemList}</div>
                <div class="flex justify-between pt-2 border-t">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-semibold">Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
                ${discount > 0 ? `<div class="flex justify-between text-emerald-600">
                    <span>Diskon</span>
                    <span>- Rp ${discount.toLocaleString('id-ID')}</span>
                </div>` : ''}
                <div class="flex justify-between text-base font-bold text-gray-800 pt-1 border-t">
                    <span>Total</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 pt-1">
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
            popup:         'swal-elco-popup',
            confirmButton: 'swal-elco-confirm',
            cancelButton:  'swal-elco-cancel',
        }
    });

    if (!confirmed.isConfirmed) return;

    const btn = document.getElementById('processBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-spinner animate-spin mr-2"></i> Memproses...';

    const items = keys.map(id => ({
        menu_stock_id: parseInt(id),
        quantity:      cart[id].qty,
    }));

    const promotionId = document.getElementById('promoSelect')?.value || null;

    try {
        const res = await fetch('{{ route("kasir.transactions.store") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ items, payment_method: paymentMethod, promotion_id: promotionId }),
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('invoiceDisplay').textContent = data.invoice ?? '';
            document.getElementById('totalFinal').textContent     = rupiah(total);
            if (data.transaction_id) {
                document.getElementById('receiptLink').href = `/kasir/transactions/${data.transaction_id}`;
            }
            document.getElementById('successModal').classList.remove('hidden');
            clearCart();
        } else {
            elcoError('Transaksi Gagal', data.message ?? 'Terjadi kesalahan!');
        }
    } catch (e) {
        elcoError('Error', 'Gagal terhubung ke server. Coba lagi.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check-circle mr-2"></i> Proses Transaksi';
    }
}

// ── Request Pembatalan ───────────────────────────────────
async function requestCancel(id) {
    const { value: reason } = await Swal.fire({
        title:            'Alasan Pembatalan',
        input:            'textarea',
        inputPlaceholder: 'Jelaskan alasan pembatalan...',
        showCancelButton: true,
        confirmButtonText:'Kirim',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#5C3D2E',
        customClass: {
            popup:         'swal-elco-popup',
            confirmButton: 'swal-elco-confirm'
        },
        inputValidator: value => {
            if (!value || value.length < 5) return 'Alasan minimal 5 karakter!'
        }
    });

    if (reason) {
        const res = await fetch(`/kasir/transactions/${id}/request-cancel`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cancel_reason: reason })
        });
        const data = await res.json();
        if (data.success) {
            elcoSuccess('Permintaan Terkirim!', 'Admin akan memproses pembatalan segera.');
            setTimeout(() => location.reload(), 1500);
        } else {
            elcoError('Gagal', data.message);
        }
    }
}

// ── Tutup Modal Sukses ───────────────────────────────────
function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
}

// ── Jam Realtime ─────────────────────────────────────────
function updateTime() {
    const el = document.getElementById('realtimeClock');
    if (el) {
        el.textContent = new Date().toLocaleTimeString('id-ID', {
            hour:   '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}
setInterval(updateTime, 1000);
updateTime();
</script>
@endpush
