<aside
    x-data="{
        open: JSON.parse(localStorage.getItem('elcoSidebarOpen') ?? 'false'),
        toggle() {
            this.open = !this.open;
            localStorage.setItem('elcoSidebarOpen', JSON.stringify(this.open));
        }
    }"
    :class="open ? 'w-64' : 'w-20'"
    class="relative z-20 flex h-full flex-shrink-0 flex-col bg-white shadow-soft smooth-transition"
>
    <button
        type="button"
        @click="toggle()"
        class="absolute -right-3 top-1/2 flex h-16 w-6 -translate-y-1/2 items-center justify-center"
        aria-label="Toggle sidebar"
    >
        <span
            class="h-0 w-0 border-y-transparent border-l-elco-coffee smooth-transition"
            :class="open ? 'border-y-[14px] border-l-[14px]' : 'border-y-[10px] border-l-[10px]'"
        ></span>
    </button>

    <div class="flex h-24 items-center" :class="open ? 'px-8' : 'justify-center px-5'">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha text-white shadow-lg">
                <i class="ph ph-coffee text-2xl"></i>
            </div>

            <span
                class="overflow-hidden font-display text-xl font-bold tracking-wide text-elco-coffee smooth-transition"
                :class="open ? 'max-w-[180px] opacity-100' : 'max-w-0 opacity-0'"
            >
                ELCO
                <span class="-mt-1 block text-sm font-medium text-elco-mocha">Kasir</span>
            </span>
        </div>
    </div>

    <div
        class="mb-2 px-6 text-xs font-semibold uppercase tracking-wider text-gray-400"
        x-show="open"
        x-transition.opacity.duration.200ms
    >
        Menu Utama
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 hide-scrollbar">
        <a
            href="{{ route('kasir.transactions.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 rounded-2xl px-4 py-3 font-medium smooth-transition
                {{ request()->routeIs('kasir.transactions*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}"
        >
            <i class="{{ request()->routeIs('kasir.transactions*') ? 'ph-fill' : 'ph' }} ph-shopping-cart text-xl"></i>
            <span
                class="overflow-hidden whitespace-nowrap smooth-transition"
                :class="open ? 'max-w-[180px] opacity-100' : 'max-w-0 opacity-0'"
            >
                Transaksi
            </span>
            @if(request()->routeIs('kasir.transactions*'))
                <i class="ph ph-arrow-right ml-auto" x-show="open" x-transition.opacity.duration.150ms></i>
            @endif
        </a>

        <a
    href="{{ route('kasir.shifts.index') }}"
    :class="open
        ? 'justify-start px-4 py-3'
        : 'mx-auto h-12 w-12 justify-center px-0 py-0'"
    class="flex items-center gap-3 rounded-2xl font-medium smooth-transition
        {{ request()->routeIs('kasir.shifts*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}"
>
    <i class="{{ request()->routeIs('kasir.shifts*') ? 'ph-fill' : 'ph' }} ph-clock text-xl leading-none"></i>
    <span
        class="overflow-hidden whitespace-nowrap smooth-transition"
        :class="open ? 'max-w-[180px] opacity-100' : 'max-w-0 opacity-0'"
    >
        Shift Saya
    </span>
    @if(request()->routeIs('kasir.shifts*'))
        <i class="ph ph-arrow-right ml-auto" x-show="open" x-transition.opacity.duration.150ms></i>
    @endif
</a>
        <div
            class="px-2 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400"
            x-show="open"
            x-transition.opacity.duration.200ms
        >
            Lainnya
        </div>

        <a
            href="{{ route('profile.edit') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 rounded-2xl px-4 py-3 font-medium text-gray-500 hover:bg-gray-50 hover:text-elco-coffee smooth-transition"
        >
            <i class="ph ph-gear text-xl"></i>
            <span
                class="overflow-hidden whitespace-nowrap smooth-transition"
                :class="open ? 'max-w-[180px] opacity-100' : 'max-w-0 opacity-0'"
            >
                Pengaturan
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                :class="open ? '' : 'justify-center'"
                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 font-medium text-red-500 hover:bg-red-50 smooth-transition"
            >
                <i class="ph ph-sign-out text-xl"></i>
                <span
                    class="overflow-hidden whitespace-nowrap smooth-transition"
                    :class="open ? 'max-w-[180px] opacity-100' : 'max-w-0 opacity-0'"
                >
                    Keluar
                </span>
            </button>
        </form>
    </nav>
</aside>
