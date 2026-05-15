<header class="h-24 px-8 flex items-center justify-between z-10">
    <div>
        <h1 class="text-2xl font-display font-bold text-elco-coffee">Kasir ELCO</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ now()->translatedFormat('l, j F Y') }} •
            <span id="realtimeClock" class="font-medium text-elco-coffee"></span>
        </p>
    </div>

    <div class="relative" id="profileWrapper">
        <div class="flex items-center gap-3 cursor-pointer" onclick="toggleProfile()">
            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white font-bold shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="hidden md:block text-right">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Kasir</p>
            </div>
            <i class="ph ph-caret-down text-gray-400 text-sm"></i>
        </div>

        <div id="profileDropdown"
             class="hidden absolute right-0 top-14 w-52 bg-white rounded-2xl shadow-hover z-50 overflow-hidden border border-gray-100 py-1">
            <div class="px-4 py-3 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 smooth-transition">
                <i class="ph ph-user-circle text-gray-400"></i> Edit Profil
            </a>
            <a href="{{ route('kasir.shifts.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 smooth-transition">
                <i class="ph ph-clock text-gray-400"></i> Shift Saya
            </a>
            <div class="border-t border-gray-100 mt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 smooth-transition">
                        <i class="ph ph-sign-out"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
function toggleProfile() {
    document.getElementById('profileDropdown').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const w = document.getElementById('profileWrapper');
    if (w && !w.contains(e.target)) {
        document.getElementById('profileDropdown')?.classList.add('hidden');
    }
});
</script>
@endpush