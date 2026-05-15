@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.expenses.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Catat Pengeluaran</h2>
            <p class="text-sm text-gray-500">Data akan dikirim ke Manager untuk verifikasi</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('admin.expenses.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Judul Pengeluaran <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                    placeholder="contoh: Tagihan Listrik Mei"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('title') border-red-400 @enderror">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori & Tanggal --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white
                        @error('category') border-red-400 @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="operasional" {{ old('category') === 'operasional' ? 'selected' : '' }}>⚡ Operasional</option>
                        <option value="bahan_baku"  {{ old('category') === 'bahan_baku'  ? 'selected' : '' }}>☕ Bahan Baku</option>
                        <option value="peralatan"   {{ old('category') === 'peralatan'   ? 'selected' : '' }}>🔧 Peralatan</option>
                        <option value="gaji"        {{ old('category') === 'gaji'        ? 'selected' : '' }}>👤 Gaji</option>
                        <option value="lainnya"     {{ old('category') === 'lainnya'     ? 'selected' : '' }}>📋 Lainnya</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition
                        @error('expense_date') border-red-400 @enderror">
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                    <input type="number" name="amount" value="{{ old('amount') }}"
                        placeholder="0" min="1"
                        class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition
                        @error('amount') border-red-400 @enderror">
                </div>
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="description" rows="3"
                    placeholder="Detail pengeluaran..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Upload Bukti --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Bukti Pengeluaran <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 text-center cursor-pointer hover:border-elco-mocha smooth-transition"
                     onclick="document.getElementById('receiptInput').click()">
                    <i class="ph ph-file-image text-3xl text-gray-300 block mb-1"></i>
                    <p class="text-sm text-gray-400">Klik untuk upload foto struk/nota</p>
                    <p id="fileName" class="text-xs text-elco-coffee mt-1 hidden"></p>
                    <input type="file" id="receiptInput" name="receipt" accept="image/*" class="hidden"
                           onchange="document.getElementById('fileName').textContent = this.files[0].name;
                                     document.getElementById('fileName').classList.remove('hidden');">
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.expenses.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-floppy-disk mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection