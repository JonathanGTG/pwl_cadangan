@extends('layouts.manager')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manager.promotions.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Edit Promo</h2>
            <p class="text-sm text-gray-500">{{ $promotion->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('manager.promotions.update', $promotion) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Promo <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $promotion->name) }}"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition resize-none">{{ old('description', $promotion->description) }}</textarea>
            </div>

            {{-- Tipe & Nilai Diskon --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Diskon</label>
                    <select name="discount_type" id="discountType" onchange="updateDiscountLabel()"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white">
                        <option value="percentage" {{ old('discount_type', $promotion->discount_type) === 'percentage' ? 'selected' : '' }}>% Persentase</option>
                        <option value="fixed"      {{ old('discount_type', $promotion->discount_type) === 'fixed'      ? 'selected' : '' }}>Rp Nominal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai Diskon</label>
                    <div class="relative">
                        <span id="discountPrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium"></span>
                        <input type="number" name="discount_value"
                            value="{{ old('discount_value', $promotion->discount_value) }}"
                            min="0"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                    </div>
                </div>
            </div>

            {{-- Min Pembelian --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Pembelian (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                    <input type="number" name="min_purchase"
                        value="{{ old('min_purchase', $promotion->min_purchase) }}"
                        min="0"
                        class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
            </div>

            {{-- Periode --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                        value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berakhir</label>
                    <input type="date" name="end_date"
                        value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
            </div>

            {{-- Toggle Aktif --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Status Promo</p>
                    <p class="text-xs text-gray-500 mt-0.5">Aktifkan/nonaktifkan promo ini</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-elco-coffee smooth-transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('manager.promotions.index') }}"
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
</div>
@endsection

@push('scripts')
<script>
function updateDiscountLabel() {
    const type = document.getElementById('discountType').value;
    document.getElementById('discountPrefix').textContent = type === 'percentage' ? '%' : 'Rp';
}
updateDiscountLabel();
</script>
<script>
// Toggle field nama item berdasarkan tipe
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const isStock = this.value === 'stock';
        document.getElementById('stockSelect').classList.toggle('hidden', !isStock);
        document.getElementById('operationalInput').classList.toggle('hidden', isStock);

        // Sync nilai ke field yang aktif
        document.getElementById('itemNameSelect').required = isStock;
        document.getElementById('itemNameOps').required    = !isStock;
    });
});

// Sebelum submit, satukan nilai item_name
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    if (type === 'operational') {
        // Buat hidden input dengan nama item_name
        const val = document.getElementById('itemNameOps').value;
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'item_name';
        hidden.value = val;
        this.appendChild(hidden);
    }
});
</script>
@endpush