@extends('layouts.manager')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('manager.branches.index') }}"
           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition text-gray-500 hover:text-elco-coffee">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-display font-bold text-gray-800">Edit Cabang</h2>
            <p class="text-sm text-gray-500">{{ $branch->name }}</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-3xl shadow-soft p-8">
        <form action="{{ route('manager.branches.update', $branch) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nama Cabang --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Cabang <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea name="address" rows="3"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition resize-none
                    @error('address') border-red-400 @enderror">{{ old('address', $branch->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Telepon --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition bg-white">
                    <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('manager.branches.index') }}"
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
    {{-- Daftar Admin & Kasir --}}
    <div class="mt-6 space-y-4">

        {{-- Admin --}}
        <div class="bg-white rounded-3xl shadow-soft p-6">
            <h3 class="font-display font-semibold text-gray-800 mb-4">
                <i class="ph ph-user-circle mr-2 text-blue-500"></i>Admin Cabang
            </h3>
            @forelse($admins as $admin)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $admin->name }}</p>
                        <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada admin</p>
            @endforelse
        </div>

        {{-- Kasir --}}
        <div class="bg-white rounded-3xl shadow-soft p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-display font-semibold text-gray-800">
                    <i class="ph ph-users mr-2 text-emerald-500"></i>Kasir Cabang
                </h3>
                <button onclick="document.getElementById('addKasirForm').classList.toggle('hidden')"
                    class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                    <i class="ph ph-plus"></i> Tambah Kasir
                </button>
            </div>

            @forelse($kasirs as $kasir)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($kasir->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $kasir->name }}</p>
                        <p class="text-xs text-gray-500">{{ $kasir->email }}</p>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-2">Belum ada kasir</p>
            @endforelse

            {{-- Form Tambah Kasir --}}
            <div id="addKasirForm" class="hidden mt-4 p-4 bg-gray-50 rounded-2xl">
                <form action="{{ route('manager.branches.add-kasir', $branch) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="kasir_name" placeholder="Nama Kasir"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                    <input type="email" name="kasir_email" placeholder="Email Kasir"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                    <input type="password" name="kasir_password" placeholder="Password (min 8 karakter)"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                    <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 smooth-transition">
                        Simpan Kasir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection