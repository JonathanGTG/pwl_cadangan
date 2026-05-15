@extends('layouts.manager')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manager.branches.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Tambah Cabang Baru</h2>
            <p class="text-sm text-gray-500">Data cabang + akun admin akan dibuat sekaligus</p>
        </div>
    </div>

    {{-- Satu Card untuk Semua --}}
    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('manager.branches.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- ── SECTION 1: Data Cabang ── --}}
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center text-sm">
                    <i class="ph-fill ph-storefront"></i>
                </div>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-wide">Data Cabang</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Cabang <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="contoh: ELCO Banda Aceh"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea name="address" rows="2"
                    placeholder="Alamat lengkap cabang..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition resize-none
                    @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="0811-xxxx-xxxx"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition bg-white">
                        <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- ── DIVIDER ── --}}
            <div class="border-t border-gray-100 pt-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center text-sm">
                        <i class="ph-fill ph-user-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-700 uppercase tracking-wide">Akun Admin Cabang</p>
                        <p class="text-xs text-gray-400">Akan dibuat otomatis bersama cabang</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Admin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                            placeholder="contoh: Budi Santoso"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                            @error('admin_name') border-red-400 @enderror">
                        @error('admin_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Admin <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                            placeholder="admin@elco.com"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                            @error('admin_email') border-red-400 @enderror">
                        @error('admin_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password Admin <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="admin_password" id="adminPass"
                                placeholder="Minimal 8 karakter"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition pr-12
                                @error('admin_password') border-red-400 @enderror">
                            <button type="button" onclick="toggleAdminPass()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="ph ph-eye" id="adminPassIcon"></i>
                            </button>
                        </div>
                        @error('admin_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('manager.branches.index') }}"
                   class="flex-1 text-center py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 smooth-transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                    <i class="ph ph-plus mr-1"></i> Simpan Cabang & Admin
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleAdminPass() {
    const input = document.getElementById('adminPass');
    const icon  = document.getElementById('adminPassIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ph-eye', 'ph-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('ph-eye-slash', 'ph-eye');
    }
}
</script>
@endpush