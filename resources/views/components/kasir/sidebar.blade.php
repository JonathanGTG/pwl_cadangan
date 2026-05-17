<aside
    x-data="{
        open: JSON.parse(localStorage.getItem('elcoSidebarOpen') ?? 'false'),
        toggle() { this.open = !this.open; localStorage.setItem('elcoSidebarOpen', JSON.stringify(this.open)); }
    }"
    :class="open ? 'w-64' : 'w-20'"
    class="bg-white h-full flex flex-col shadow-soft z-20 flex-shrink-0 relative smooth-transition"
>
    <button type="button" @click="toggle()" class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-16 flex items-center justify-center">
        <div class="w-0 h-0 border-y-transparent border-l-elco-coffee smooth-transition"
             :class="open ? 'border-y-[14px] border-l-[14px]' : 'border-y-[10px] border-l-[10px]'"></div>
    </button>

    <div class="h-24 flex items-center"
         :class="open ? 'px-8' : 'px-5 justify-center'">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center shadow-lg text-white flex-shrink-0">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <span class="font-display font-bold text-xl text-elco-coffee tracking-wide overflow-hidden smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">
                ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Kasir</span>
            </span>
        </div>
    </div>

    <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider"
         x-show="open" x-transition.opacity.duration.200ms>Menu Utama</div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
        <a href="{{ route('kasir.transactions.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('kasir.transactions*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
            <i class="ph {{ request()->routeIs('kasir.transactions*') ? 'ph-fill' : 'ph' }}-shopping-cart text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Transaksi</span>
            @if(request()->routeIs('kasir.transactions*'))
                <i class="ph ph-arrow-right ml-auto" x-show="open" x-transition.opacity.duration.150ms></i>
            @endif
        </a>

        <a href="{{ route('kasir.shifts.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('kasir.shifts*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
            <i class="ph ph-clock text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Shift Saya</span>
        </a>

        <div class="px-2 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider"
             x-show="open" x-transition.opacity.duration.200ms>Lainnya</div>

        <a href="{{ route('profile.edit') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition">
            <i class="ph ph-gear text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Pengaturan</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                :class="open ? '' : 'justify-center'"
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-2xl font-medium smooth-transition">
                <i class="ph ph-sign-out text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Keluar</span>
            </button>
        </form>
    </nav>
</aside>
