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

    <!-- Logo -->
    <div class="h-24 flex items-center"
         :class="open ? 'px-8' : 'px-5 justify-center'">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center shadow-lg text-white flex-shrink-0">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <span class="font-display font-bold text-xl text-elco-coffee tracking-wide overflow-hidden smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">
                ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Admin Cabang</span>
            </span>
        </div>
    </div>

    <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider"
         x-show="open" x-transition.opacity.duration.200ms>Menu Utama</div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
        <a href="{{ route('admin.dashboard') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 bg-[#F6F3F0] text-elco-coffee rounded-2xl font-semibold smooth-transition">
            <i class="ph-fill ph-squares-four text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Dashboard</span>
            <i class="ph ph-arrow-right ml-auto" x-show="open" x-transition.opacity.duration.150ms></i>
        </a>
        <a href="{{ route('admin.stocks.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.stocks*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-package text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Stok Cabang</span>
        </a>

        <a href="{{ route('admin.stock-requests.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.stock-requests*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-arrow-circle-up text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Pengajuan Kebutuhan</span>
        </a>
        <a href="{{ route('admin.expenses.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.expenses*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-money text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Pengeluaran</span>
        </a>
        <a href="{{ route('admin.promotions.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.promotions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-tag text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Promo Cabang</span>
        </a>
        <a href="{{ route('admin.reports.index') }}"
        :class="open ? '' : 'justify-center'"
        class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
        {{ request()->routeIs('admin.reports*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-chart-bar text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Laporan Bulanan</span>
        </a>
        <a href="{{ route('admin.transactions.index') }}"
            :class="open ? '' : 'justify-center'"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.transactions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-receipt text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Transaksi</span>
        </a>

        <div class="px-2 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider"
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
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-2xl font-medium smooth-transition mt-2">
                <i class="ph ph-sign-out text-xl"></i>
                <span class="overflow-hidden whitespace-nowrap smooth-transition"
                      :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Keluar</span>
            </button>
        </form>
    </nav>
</aside>
