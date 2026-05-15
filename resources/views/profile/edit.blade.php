@php
    $layout = match(auth()->user()->role) {
        'manager' => 'layouts.manager',
        'admin'   => 'layouts.admin',
        default   => 'layouts.kasir',
    };
@endphp

@extends($layout)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Pengaturan Profil</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun kamu</p>
    </div>

    {{-- Alert --}}
    @if(session('status') === 'profile-updated')
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
        <i class="ph-fill ph-check-circle text-xl"></i> Profil berhasil diperbarui!
    </div>
    @endif
    @if(session('status') === 'password-updated')
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
        <i class="ph-fill ph-check-circle text-xl"></i> Password berhasil diubah!
    </div>
    @endif

    {{-- Avatar & Info --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white text-3xl font-bold shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-display font-bold text-gray-800">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                <span class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold
                    {{ auth()->user()->role === 'manager' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ auth()->user()->role === 'admin'   ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ auth()->user()->role === 'kasir'   ? 'bg-emerald-100 text-emerald-700' : '' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Form Update Profil --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-5">
            <i class="ph ph-user mr-2 text-elco-coffee"></i>Informasi Profil
        </h3>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    required autocomplete="name"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    required autocomplete="username"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 focus:border-elco-mocha text-sm smooth-transition
                    @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition active:scale-95">
                <i class="ph ph-floppy-disk mr-1"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Form Ganti Password --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-5">
            <i class="ph ph-lock mr-2 text-elco-coffee"></i>Ganti Password
        </h3>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" name="current_password" id="currentPass"
                        autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition pr-12
                        @error('current_password', 'updatePassword') border-red-400 @enderror">
                    <button type="button" onclick="togglePass('currentPass')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="ph ph-eye" id="currentPass-icon"></i>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="newPass"
                        autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition pr-12
                        @error('password', 'updatePassword') border-red-400 @enderror">
                    <button type="button" onclick="togglePass('newPass')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="ph ph-eye" id="newPass-icon"></i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="confirmPass"
                        autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm smooth-transition pr-12">
                    <button type="button" onclick="togglePass('confirmPass')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="ph ph-eye" id="confirmPass-icon"></i>
                    </button>
                </div>
            </div>

            {{-- Indikator Kekuatan Password --}}
            <div id="passwordStrength" class="hidden">
                <div class="flex gap-1 mt-1">
                    <div class="h-1 flex-1 rounded-full bg-gray-200" id="str1"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-200" id="str2"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-200" id="str3"></div>
                    <div class="h-1 flex-1 rounded-full bg-gray-200" id="str4"></div>
                </div>
                <p class="text-xs mt-1" id="strLabel"></p>
            </div>

            <button type="submit"
                class="w-full py-3 rounded-2xl bg-elco-coffee text-white text-sm font-semibold shadow-md hover:bg-elco-mocha smooth-transition active:scale-95">
                <i class="ph ph-lock-key mr-1"></i> Ganti Password
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle show/hide password
function togglePass(id) {
    const input = document.getElementById(id);
    const icon  = document.getElementById(id + '-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ph-eye', 'ph-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('ph-eye-slash', 'ph-eye');
    }
}

// Password strength checker
document.getElementById('newPass')?.addEventListener('input', function() {
    const val = this.value;
    const strengthDiv = document.getElementById('passwordStrength');
    strengthDiv.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8)             score++;
    if (/[A-Z]/.test(val))          score++;
    if (/[0-9]/.test(val))          score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;

    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-500'];
    const labels = ['Sangat Lemah', 'Lemah', 'Cukup Kuat', 'Kuat'];
    const textColors = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-emerald-600'];

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('str' + i);
        bar.className = 'h-1 flex-1 rounded-full ' + (i <= score ? colors[score - 1] : 'bg-gray-200');
    }
    const label = document.getElementById('strLabel');
    label.textContent = score > 0 ? labels[score - 1] : '';
    label.className = 'text-xs mt-1 ' + (score > 0 ? textColors[score - 1] : '');
});
</script>
@endpush