@extends('layouts.manager')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Master Bahan Baku</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola bahan baku yang digunakan dalam resep minuman</p>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
        class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Tambah Bahan
    </button>
</div>

{{-- Info --}}
<div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-6">
    <i class="ph ph-info text-amber-500 text-xl mt-0.5"></i>
    <p class="text-sm text-amber-700">
        Bahan baku digunakan sebagai resep menu minuman. Stok bahan dikelola per cabang.
        Satu bahan bisa digunakan di banyak resep menu.
    </p>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Kode</th>
                <th class="py-4 px-6 font-medium">Nama Bahan</th>
                <th class="py-4 px-6 font-medium">Kategori</th>
                <th class="py-4 px-6 font-medium">Satuan</th>
                <th class="py-4 px-6 font-medium">Dipakai di Resep</th>
                <th class="py-4 px-6 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingredients as $ing)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                        {{ $ing->kode_bahan }}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm font-semibold text-gray-800">{{ $ing->nama_bahan }}</td>
                <td class="py-4 px-6">
                    <span class="text-xs px-2 py-1 rounded-lg bg-elco-cream text-elco-coffee font-medium">
                        {{ $ing->kategori ?? '—' }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">
                        {{ $ing->satuan }}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    {{ $ing->menu_ingredients_count }} menu
                </td>
                <td class="py-4 px-6">
                    <button onclick="openEditModal({{ $ing->id }}, '{{ addslashes($ing->nama_bahan) }}', '{{ $ing->kategori }}', '{{ $ing->satuan }}')"
                        class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-1.5 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                        <i class="ph ph-pencil"></i> Edit
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-12 text-center text-gray-400">
                    <i class="ph ph-flask text-4xl block mb-2"></i>
                    Belum ada bahan baku. Tambahkan bahan pertama!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-5">
            <i class="ph ph-plus-circle mr-2 text-elco-coffee"></i>Tambah Bahan Baku
        </h3>
        <form action="{{ route('manager.menus.ingredients.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Bahan *</label>
                    <input type="text" name="kode_bahan" placeholder="BHN-026"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan *</label>
                    <select name="satuan"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bahan *</label>
                <input type="text" name="nama_bahan" placeholder="contoh: Biji Kopi Arabika"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                <input type="text" name="kategori" placeholder="contoh: kopi, susu, sirup"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold smooth-transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-5">
            <i class="ph ph-pencil mr-2 text-elco-coffee"></i>Edit Bahan Baku
        </h3>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bahan *</label>
                <input type="text" id="editNama" name="nama_bahan"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                    <input type="text" id="editKategori" name="kategori"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan *</label>
                    <select id="editSatuan" name="satuan"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold smooth-transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditModal(id, nama, kategori, satuan) {
    document.getElementById('editForm').action = `/manager/menus/ingredients/${id}`;
    document.getElementById('editNama').value    = nama;
    document.getElementById('editKategori').value = kategori ?? '';
    document.getElementById('editSatuan').value  = satuan;
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>
@endpush
