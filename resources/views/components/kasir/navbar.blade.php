<header class="z-10 flex h-24 items-center justify-between px-4 md:px-8">
    <div class="min-w-0">
        <h1 class="truncate font-display text-2xl font-bold text-elco-coffee">Kasir ELCO</h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ now()->translatedFormat('l, j F Y') }} •
            <span id="realtimeClock" class="font-medium text-elco-coffee"></span>
        </p>
    </div>

    <div class="relative" id="profileWrapper">
        <button
            type="button"
            onclick="toggleProfile()"
            class="flex items-center gap-3 rounded-2xl px-2 py-2 text-left hover:bg-white/70 smooth-transition"
        >
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-elco-coffee to-elco-mocha font-bold text-white shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>

            <span class="hidden text-right md:block">
                <span class="block text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</span>
                <span class="block text-xs text-gray-500">Kasir</span>
            </span>

            <i class="ph ph-caret-down text-sm text-gray-400"></i>
        </button>

        <div
            id="profileDropdown"
            class="absolute right-0 top-14 z-50 hidden w-52 overflow-hidden rounded-2xl border border-gray-100 bg-white py-1 shadow-hover"
        >
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="truncate text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 smooth-transition"
            >
                <i class="ph ph-user-circle text-gray-400"></i>
                Edit Profil
            </a>

            <a
                href="{{ route('kasir.shifts.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 smooth-transition"
            >
                <i class="ph ph-clock text-gray-400"></i>
                Shift Saya
            </a>

            <div class="mt-1 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 smooth-transition"
                    >
                        <i class="ph ph-sign-out"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
function toggleProfile() {
    document.getElementById('profileDropdown')?.classList.toggle('hidden');
}

document.addEventListener('click', function (event) {
    const wrapper = document.getElementById('profileWrapper');
    const dropdown = document.getElementById('profileDropdown');

    if (wrapper && dropdown && !wrapper.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endpush
