@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.stock-requests.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Buat Pengajuan</h2>
            <p class="text-sm text-gray-500">Pengajuan akan dikirim ke Manager Pusat</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('admin.stock-requests.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Tipe Pengajuan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Tipe Pengajuan <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="stock"
                               class="sr-only peer" {{ old('type', 'stock') === 'stock' ? 'checked' : '' }}
                               onchange="handleTypeChange()">
                        <div class="p-4 border-2 border-gray-200 rounded-2xl peer-checked:border-elco-coffee peer-checked:bg-elco-cream smooth-transition text-center">
                            <i class="ph-fill ph-package text-2xl text-gray-400 block mb-2"></i>
                            <p class="text-sm font-semibold text-gray-700">Pengajuan Stok</p>
                            <p class="text-xs text-gray-400 mt-1">Bahan baku & produk jadi</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="operational"
                               class="sr-only peer" {{ old('type') === 'operational' ? 'checked' : '' }}
                               onchange="handleTypeChange()">
                        <div class="p-4 border-2 border-gray-200 rounded-2xl peer-checked:border-elco-coffee peer-checked:bg-elco-cream smooth-transition text-center">
                            <i class="ph-fill ph-wrench text-2xl text-gray-400 block mb-2"></i>
                            <p class="text-sm font-semibold text-gray-700">Alat Operasional</p>
                            <p class="text-xs text-gray-400 mt-1">Peralatan & perlengkapan</p>
                        </div>
                    </label>
                </div>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ══ SECTION STOK ══ --}}
            <div id="sectionStok" class="space-y-5">

                {{-- Sub-tipe: Bahan Baku atau Produk Jadi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Jenis Stok <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="stock_item_type" value="bahan_baku"
                                   class="sr-only peer" {{ old('stock_item_type', 'bahan_baku') === 'bahan_baku' ? 'checked' : '' }}
                                   onchange="handleStockItemTypeChange()">
                            <div class="p-3 border-2 border-gray-200 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-50 smooth-transition text-center">
                                <i class="ph ph-flask text-xl text-gray-400 block mb-1"></i>
                                <p class="text-xs font-semibold text-gray-700">Bahan Baku</p>
                                <p class="text-xs text-gray-400">Untuk minuman</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="stock_item_type" value="produk_jadi"
                                   class="sr-only peer" {{ old('stock_item_type') === 'produk_jadi' ? 'checked' : '' }}
                                   onchange="handleStockItemTypeChange()">
                            <div class="p-3 border-2 border-gray-200 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 smooth-transition text-center">
                                <i class="ph ph-package text-xl text-gray-400 block mb-1"></i>
                                <p class="text-xs font-semibold text-gray-700">Produk Jadi</p>
                                <p class="text-xs text-gray-400">Makanan & Snack</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pilih Bahan Baku --}}
                <div id="fieldBahanBaku">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bahan Baku <span class="text-red-500">*</span>
                    </label>
                    <select name="item_name_bahan" id="selectBahan"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white"
                        onchange="updateSatuanFromBahan()">
                        <option value="">— Pilih Bahan Baku —</option>
                        @foreach($ingredients as $ing)
                        <option value="{{ $ing->nama_bahan }}"
                            data-satuan="{{ $ing->satuan }}"
                            {{ old('item_name') === $ing->nama_bahan ? 'selected' : '' }}>
                            {{ $ing->nama_bahan }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        Pilih bahan baku yang perlu diisi stoknya di cabang ini.
                    </p>
                </div>

                {{-- Pilih Produk Jadi --}}
                <div id="fieldProdukJadi" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Menu (Makanan/Snack) <span class="text-red-500">*</span>
                    </label>
                    <select name="item_name_produk" id="selectProduk"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white">
                        <option value="">— Pilih Menu —</option>
                        @foreach($produkJadi as $menu)
                        <option value="{{ $menu->name }}" {{ old('item_name') === $menu->name ? 'selected' : '' }}>
                            {{ $menu->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- ══ SECTION OPERASIONAL ══ --}}
            <div id="sectionOperasional" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Alat/Kebutuhan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="item_name_ops" id="itemNameOps"
                    value="{{ old('item_name_ops') }}"
                    placeholder="contoh: Mesin Espresso, Grinder, dll"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
            </div>

            {{-- Jumlah & Satuan --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}"
                        placeholder="contoh: 50" min="1"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                        @error('quantity') border-red-400 @enderror">
                    @error('quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                    <input type="text" name="unit" id="unitInput" value="{{ old('unit') }}"
                        placeholder="gram / pcs / liter / unit"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition">
                </div>
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Pengajuan
                </label>
                <textarea name="reason" rows="3"
                    placeholder="Jelaskan alasan pengajuan ini..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition resize-none">{{ old('reason') }}</textarea>
            </div>

            <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <i class="ph ph-info text-amber-500 text-xl mt-0.5"></i>
                <p class="text-sm text-amber-700">
                    Pengajuan akan diproses oleh <strong>Manager Pusat</strong>.
                    Stok akan diperbarui setelah disetujui dan barang diterima.
                </p>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.stock-requests.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-paper-plane-right mr-1"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function handleTypeChange() {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    document.getElementById('sectionStok').classList.toggle('hidden', type !== 'stock');
    document.getElementById('sectionOperasional').classList.toggle('hidden', type !== 'operational');
}

function handleStockItemTypeChange() {
    const t = document.querySelector('input[name="stock_item_type"]:checked')?.value;
    document.getElementById('fieldBahanBaku').classList.toggle('hidden', t !== 'bahan_baku');
    document.getElementById('fieldProdukJadi').classList.toggle('hidden', t !== 'produk_jadi');
    updateSatuanFromBahan();
}

function updateSatuanFromBahan() {
    const select = document.getElementById('selectBahan');
    const opt    = select?.options[select.selectedIndex];
    const satuan = opt?.dataset?.satuan ?? '';
    const input  = document.getElementById('unitInput');
    if (satuan && input) {
        input.value = satuan;
    }
}

// Sebelum submit: satukan item_name dari field yang aktif
document.querySelector('form').addEventListener('submit', function () {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    if (type === 'stock') {
        const stockType = document.querySelector('input[name="stock_item_type"]:checked')?.value;
        let val = '';
        if (stockType === 'bahan_baku') {
            val = document.getElementById('selectBahan').value;
        } else {
            val = document.getElementById('selectProduk').value;
        }
        // Override item_name
        let hidden = document.querySelector('input[name="item_name"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'item_name';
            this.appendChild(hidden);
        }
        hidden.value = val;
    } else {
        // Operasional
        const val    = document.getElementById('itemNameOps').value;
        let hidden   = this.querySelector('input[name="item_name"]');
        if (!hidden) {
            hidden       = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'item_name';
            this.appendChild(hidden);
        }
        hidden.value = val;
    }
});

// Init
document.addEventListener('DOMContentLoaded', () => {
    handleTypeChange();
    handleStockItemTypeChange();
});
</script>
@endpush
