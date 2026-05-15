@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.promotions.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Buat Promo Cabang</h2>
            <p class="text-sm text-gray-500">Hanya berlaku untuk cabang kamu</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('admin.promotions.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Promo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="contoh: Promo Weekend Cabang"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition
                    @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Tipe & Nilai --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Diskon <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="discountType" onchange="updateDiscountLabel()"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white">
                        <option value="percentage">% Persentase</option>
                        <option value="fixed">Rp Nominal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai Diskon <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span id="discountPrefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">%</span>
                        <input type="number" name="discount_value" value="{{ old('discount_value') }}"
                            placeholder="0" min="0"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                    </div>
                    @error('discount_value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Min Pembelian --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Pembelian (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                    <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" min="0"
                        class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
            </div>

            {{-- Periode --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berakhir <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.promotions.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-purple-600 to-purple-800 text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-tag mr-1"></i> Buat Promo
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