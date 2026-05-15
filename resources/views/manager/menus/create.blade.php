@extends('layouts.manager')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manager.menus.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Tambah Menu Baru</h2>
            <p class="text-sm text-gray-500">Menu akan otomatis terdistribusi ke semua cabang aktif</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('manager.menus.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Preview Gambar --}}
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl p-6 cursor-pointer hover:border-elco-mocha smooth-transition"
                 onclick="document.getElementById('imageInput').click()">
                <div id="imagePreview" class="hidden mb-3">
                    <img id="previewImg" src="" class="h-32 rounded-xl object-cover">
                </div>
                <div id="uploadPlaceholder">
                    <i class="ph ph-image text-4xl text-gray-300 block text-center mb-2"></i>
                    <p class="text-sm text-gray-400 text-center">Klik untuk upload foto menu</p>
                    <p class="text-xs text-gray-300 text-center mt-1">JPG, PNG, WEBP — Maks 2MB</p>
                </div>
                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden"
                       onchange="previewImage(this)">
            </div>
            @error('image')
                <p class="text-red-500 text-xs -mt-4">{{ $message }}</p>
            @enderror

            {{-- Nama Menu --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Menu <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="contoh: Kopi Arabika Gayo"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                    placeholder="Deskripsi singkat menu..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Kategori & Harga --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="categorySelect" onchange="handleCategoryChange()"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition bg-white
                        @error('category') border-red-400 @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="minuman" {{ old('category') === 'minuman' ? 'selected' : '' }}>☕ Minuman</option>
                        <option value="makanan" {{ old('category') === 'makanan' ? 'selected' : '' }}>🍱 Makanan</option>
                        <option value="snack"   {{ old('category') === 'snack'   ? 'selected' : '' }}>🍪 Snack</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga Dasar (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="base_price" value="{{ old('base_price') }}"
                        placeholder="contoh: 25000"
                        min="0"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                        @error('base_price') border-red-400 @enderror">
                    @error('base_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 SECTION MINUMAN: Resep Bahan Baku
                 Muncul hanya jika kategori = minuman
            ══════════════════════════════════════════════════════════ --}}
            <div id="sectionMinuman" class="hidden space-y-4">
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                    <i class="ph ph-flask text-amber-500 text-xl mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Resep Bahan Baku</p>
                        <p class="text-xs text-amber-600 mt-0.5">
                            Setiap bahan akan dikurangi dari stok cabang saat transaksi berhasil.
                            Stok dikembalikan jika transaksi dibatalkan.
                        </p>
                    </div>
                </div>

                {{-- List Bahan --}}
                <div id="ingredientList" class="space-y-3">
                    {{-- Baris bahan awal --}}
                    <div class="ingredient-row grid grid-cols-[1fr_auto_auto_auto] gap-2 items-center">
                        <select name="ingredients[0][ingredient_id]"
                            class="px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                            <option value="">— Pilih Bahan —</option>
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}">
                                {{ $ing->nama_bahan }} ({{ $ing->satuan }})
                            </option>
                            @endforeach
                        </select>
                        <input type="number" name="ingredients[0][jumlah]"
                            placeholder="Jumlah" min="0.001" step="0.001"
                            class="w-28 px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                        <span class="ingredient-satuan text-xs text-gray-400 w-10 text-center">—</span>
                        <button type="button" onclick="removeIngredient(this)"
                            class="w-8 h-8 rounded-lg bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-100 smooth-transition flex-shrink-0">
                            <i class="ph ph-trash text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Tombol tambah bahan --}}
                <button type="button" onclick="addIngredient()"
                    class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-sm text-gray-500 hover:border-elco-mocha hover:text-elco-coffee smooth-transition flex items-center justify-center gap-2">
                    <i class="ph ph-plus"></i> Tambah Bahan
                </button>

                @error('ingredients')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 SECTION MAKANAN/SNACK: Stok Awal Produk Jadi
                 Muncul hanya jika kategori = makanan / snack
            ══════════════════════════════════════════════════════════ --}}
            <div id="sectionMakanan" class="hidden space-y-4">
                <div class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-2xl p-4">
                    <i class="ph ph-package text-purple-500 text-xl mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-purple-800">Stok Produk Jadi</p>
                        <p class="text-xs text-purple-600 mt-0.5">
                            Makanan & Snack dihitung per pcs produk jadi, bukan bahan baku.
                            Stok berkurang 1 per item terjual.
                        </p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Stok Awal per Cabang (pcs)
                    </label>
                    <input type="number" name="stok_awal" value="{{ old('stok_awal', 0) }}"
                        placeholder="contoh: 10" min="0"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition">
                    <p class="text-xs text-gray-400 mt-1">
                        Stok ini akan didistribusikan ke semua cabang aktif.
                    </p>
                </div>
            </div>

            {{-- Info distribusi (default, saat kategori belum dipilih) --}}
            <div id="infoDefault" class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <i class="ph ph-info text-blue-500 text-xl mt-0.5"></i>
                <p class="text-sm text-blue-600">
                    Pilih kategori menu untuk melihat konfigurasi stok yang sesuai.
                </p>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('manager.menus.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-plus mr-1"></i> Simpan Menu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@php
    $ingredientsJson = $ingredients->map(function($i) {
        return ['id' => $i->id, 'nama' => $i->nama_bahan, 'satuan' => $i->satuan];
    });
@endphp

@push('scripts')
<script>
// ── Data bahan dari server ────────────────────────────────────────────────────
const ingredientsData = @json($ingredientsJson);

let ingIndex = 1; // mulai dari 1 karena index 0 sudah ada

// ── Handle perubahan kategori ─────────────────────────────────────────────────
function handleCategoryChange() {
    const cat = document.getElementById('categorySelect').value;

    document.getElementById('sectionMinuman').classList.add('hidden');
    document.getElementById('sectionMakanan').classList.add('hidden');
    document.getElementById('infoDefault').classList.add('hidden');

    if (cat === 'minuman') {
        document.getElementById('sectionMinuman').classList.remove('hidden');
    } else if (cat === 'makanan' || cat === 'snack') {
        document.getElementById('sectionMakanan').classList.remove('hidden');
    } else {
        document.getElementById('infoDefault').classList.remove('hidden');
    }
}

// ── Tambah baris bahan ────────────────────────────────────────────────────────
function addIngredient() {
    const list = document.getElementById('ingredientList');
    const div  = document.createElement('div');
    div.className = 'ingredient-row grid grid-cols-[1fr_auto_auto_auto] gap-2 items-center';

    const options = ingredientsData.map(i =>
        `<option value="${i.id}">${i.nama} (${i.satuan})</option>`
    ).join('');

    div.innerHTML = `
        <select name="ingredients[${ingIndex}][ingredient_id]"
            onchange="updateSatuan(this)"
            class="px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
            <option value="">— Pilih Bahan —</option>
            ${options}
        </select>
        <input type="number" name="ingredients[${ingIndex}][jumlah]"
            placeholder="Jumlah" min="0.001" step="0.001"
            class="w-28 px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
        <span class="ingredient-satuan text-xs text-gray-400 w-10 text-center">—</span>
        <button type="button" onclick="removeIngredient(this)"
            class="w-8 h-8 rounded-lg bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-100 smooth-transition flex-shrink-0">
            <i class="ph ph-trash text-sm"></i>
        </button>
    `;
    list.appendChild(div);
    ingIndex++;
}

// ── Hapus baris bahan ─────────────────────────────────────────────────────────
function removeIngredient(btn) {
    const rows = document.querySelectorAll('.ingredient-row');
    if (rows.length <= 1) return; // minimal 1 bahan
    btn.closest('.ingredient-row').remove();
}

// ── Update label satuan saat bahan dipilih ────────────────────────────────────
function updateSatuan(select) {
    const id  = parseInt(select.value);
    const ing = ingredientsData.find(i => i.id === id);
    const row = select.closest('.ingredient-row');
    const span = row.querySelector('.ingredient-satuan');
    span.textContent = ing ? ing.satuan : '—';
}

// Pasang listener satuan untuk row pertama
document.querySelector('select[name="ingredients[0][ingredient_id]"]')
    ?.addEventListener('change', function() { updateSatuan(this); });

// ── Preview gambar ────────────────────────────────────────────────────────────
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('uploadPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Init: handle old() nilai kategori saat ada validation error
document.addEventListener('DOMContentLoaded', () => {
    const cat = document.getElementById('categorySelect').value;
    if (cat) handleCategoryChange();
});
</script>
@endpush
