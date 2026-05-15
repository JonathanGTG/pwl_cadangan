@extends('layouts.manager')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('manager.menus.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Edit Menu</h2>
            <p class="text-sm text-gray-500">{{ $menu->name }}</p>
        </div>
    </div>

    {{-- Form Edit --}}
    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('manager.menus.update', $menu) }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Preview Gambar --}}
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl p-6 cursor-pointer hover:border-elco-mocha smooth-transition"
                 onclick="document.getElementById('imageInput').click()">
                @if($menu->image)
                    <img id="previewImg" src="{{ Storage::url($menu->image) }}"
                         class="h-32 rounded-xl object-cover mb-2">
                @else
                    <img id="previewImg" src="" class="h-32 rounded-xl object-cover mb-2 hidden">
                    <i class="ph ph-image text-4xl text-gray-300 block text-center mb-2" id="uploadIcon"></i>
                @endif
                <p class="text-xs text-gray-400">Klik untuk ganti foto</p>
                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden"
                       onchange="previewImage(this)">
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Menu <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $menu->name) }}"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition resize-none">{{ old('description', $menu->description) }}</textarea>
            </div>

            {{-- Kategori & Harga --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="categorySelect" onchange="handleCategoryChange()"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition bg-white">
                        <option value="minuman" {{ old('category', $menu->category) === 'minuman' ? 'selected' : '' }}>☕ Minuman</option>
                        <option value="makanan" {{ old('category', $menu->category) === 'makanan' ? 'selected' : '' }}>🍱 Makanan</option>
                        <option value="snack"   {{ old('category', $menu->category) === 'snack'   ? 'selected' : '' }}>🍪 Snack</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga Dasar (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="base_price" value="{{ old('base_price', $menu->base_price) }}"
                        min="0"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition">
                </div>
            </div>

            {{-- ══ SECTION MINUMAN: Resep Bahan Baku ══ --}}
            <div id="sectionMinuman" class="{{ old('category', $menu->category) === 'minuman' ? '' : 'hidden' }} space-y-4">
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                    <i class="ph ph-flask text-amber-500 text-xl mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Resep Bahan Baku</p>
                        <p class="text-xs text-amber-600 mt-0.5">
                            Perubahan resep berlaku untuk transaksi selanjutnya.
                            Transaksi yang sudah selesai tidak terpengaruh.
                        </p>
                    </div>
                </div>

                <div id="ingredientList" class="space-y-3">
                    @forelse($menuIngredients as $idx => $mi)
                    <div class="ingredient-row grid grid-cols-[1fr_auto_auto_auto] gap-2 items-center">
                        <select name="ingredients[{{ $idx }}][ingredient_id]"
                            onchange="updateSatuan(this)"
                            class="px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                            <option value="">— Pilih Bahan —</option>
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}"
                                {{ $mi->ingredient_id == $ing->id ? 'selected' : '' }}>
                                {{ $ing->nama_bahan }} ({{ $ing->satuan }})
                            </option>
                            @endforeach
                        </select>
                        <input type="number" name="ingredients[{{ $idx }}][jumlah]"
                            value="{{ $mi->jumlah_per_sajian }}"
                            placeholder="Jumlah" min="0.001" step="0.001"
                            class="w-28 px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                        <span class="ingredient-satuan text-xs text-gray-400 w-10 text-center">
                            {{ $mi->ingredient->satuan ?? '—' }}
                        </span>
                        <button type="button" onclick="removeIngredient(this)"
                            class="w-8 h-8 rounded-lg bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-100 smooth-transition flex-shrink-0">
                            <i class="ph ph-trash text-sm"></i>
                        </button>
                    </div>
                    @empty
                    {{-- Minimal 1 baris kosong jika belum ada resep --}}
                    <div class="ingredient-row grid grid-cols-[1fr_auto_auto_auto] gap-2 items-center">
                        <select name="ingredients[0][ingredient_id]"
                            onchange="updateSatuan(this)"
                            class="px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                            <option value="">— Pilih Bahan —</option>
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->nama_bahan }} ({{ $ing->satuan }})</option>
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
                    @endforelse
                </div>

                <button type="button" onclick="addIngredient()"
                    class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-sm text-gray-500 hover:border-elco-mocha hover:text-elco-coffee smooth-transition flex items-center justify-center gap-2">
                    <i class="ph ph-plus"></i> Tambah Bahan
                </button>
            </div>

            {{-- ══ SECTION MAKANAN/SNACK ══ --}}
            <div id="sectionMakanan" class="{{ in_array(old('category', $menu->category), ['makanan','snack']) ? '' : 'hidden' }} space-y-4">
                <div class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-2xl p-4">
                    <i class="ph ph-package text-purple-500 text-xl mt-0.5 flex-shrink-0"></i>
                    <p class="text-sm text-purple-700">
                        <strong>Stok Produk Jadi</strong> — dihitung per pcs. Stok per cabang bisa diperbarui
                        melalui halaman Stok atau pengajuan dari Admin Cabang.
                    </p>
                </div>
            </div>

            {{-- Toggle Ketersediaan --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Ketersediaan Menu</p>
                    <p class="text-xs text-gray-500 mt-0.5">Menu akan tampil/disembunyikan di kasir</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" class="sr-only peer"
                        {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-elco-mocha/30 rounded-full peer peer-checked:bg-elco-coffee smooth-transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('manager.menus.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-floppy-disk mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Stok Per Cabang --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-1">
            Stok & Harga Per Cabang
        </h3>
        @if($menu->isIngredientBased())
        <p class="text-xs text-amber-600 mb-4">
            <i class="ph ph-info"></i>
            Menu minuman — stok ditampilkan berdasarkan ketersediaan bahan baku di masing-masing cabang.
        </p>
        @else
        <p class="text-xs text-purple-600 mb-4">
            <i class="ph ph-info"></i>
            Menu makanan/snack — stok dihitung per pcs produk jadi.
        </p>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs text-gray-400 border-b border-gray-100">
                        <th class="pb-3 px-4 font-medium">Cabang</th>
                        <th class="pb-3 px-4 font-medium">
                            {{ $menu->isIngredientBased() ? 'Keterangan' : 'Stok (pcs)' }}
                        </th>
                        <th class="pb-3 px-4 font-medium">Harga Custom</th>
                        <th class="pb-3 px-4 font-medium">Harga Efektif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branchStocks->filter(fn($bs) => $bs->branch) as $bs)
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-400 flex items-center justify-center">
                                    <i class="ph-fill ph-storefront text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $bs->branch->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($menu->isIngredientBased())
                                <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                                    Dikontrol via bahan baku
                                </span>
                            @else
                                <span class="text-sm font-semibold
                                    {{ $bs->stock <= 0 ? 'text-red-500' : ($bs->stock <= 5 ? 'text-yellow-600' : 'text-gray-800') }}">
                                    {{ $bs->stock }} pcs
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            {{ $bs->custom_price ? 'Rp ' . number_format($bs->custom_price, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm font-semibold text-elco-coffee">
                                Rp {{ number_format($bs->custom_price ?? $menu->base_price, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-sm text-gray-400">
                            Belum ada cabang yang memiliki menu ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
const ingredientsData = @json($ingredientsJson);

let ingIndex = {{ $menuIngredients->count() ?: 1 }};

function handleCategoryChange() {
    const cat = document.getElementById('categorySelect').value;
    document.getElementById('sectionMinuman').classList.add('hidden');
    document.getElementById('sectionMakanan').classList.add('hidden');
    if (cat === 'minuman') {
        document.getElementById('sectionMinuman').classList.remove('hidden');
    } else if (cat === 'makanan' || cat === 'snack') {
        document.getElementById('sectionMakanan').classList.remove('hidden');
    }
}

function addIngredient() {
    const list    = document.getElementById('ingredientList');
    const div     = document.createElement('div');
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

function removeIngredient(btn) {
    const rows = document.querySelectorAll('.ingredient-row');
    if (rows.length <= 1) return;
    btn.closest('.ingredient-row').remove();
}

function updateSatuan(select) {
    const id   = parseInt(select.value);
    const ing  = ingredientsData.find(i => i.id === id);
    const span = select.closest('.ingredient-row').querySelector('.ingredient-satuan');
    span.textContent = ing ? ing.satuan : '—';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('previewImg');
            img.src = e.target.result;
            img.classList.remove('hidden');
            const icon = document.getElementById('uploadIcon');
            if (icon) icon.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
