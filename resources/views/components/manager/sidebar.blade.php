<aside
    x-data="{
        open: JSON.parse(localStorage.getItem('elcoSidebarOpen') ?? 'false'),
        toggle() { this.open = !this.open; localStorage.setItem('elcoSidebarOpen', JSON.stringify(this.open)); }
    }"
    :class="open ? 'w-64' : 'w-20'"
    class="bg-white h-full flex flex-col shadow-soft z-20 flex-shrink-0 relative smooth-transition">
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
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Manager</span></span>
        </div>
    </div>

    <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider"
         x-show="open" x-transition.opacity.duration.200ms>Menu Utama</div>
    
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
        <a href="{{ route('manager.dashboard') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold smooth-transition
           {{ request()->routeIs('manager.dashboard') ? 'bg-[#F6F3F0] text-elco-coffee' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
            <i class="ph-fill ph-squares-four text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Dashboard</span>
            @if(request()->routeIs('manager.dashboard'))
                <i class="ph ph-arrow-right ml-auto" x-show="open" x-transition.opacity.duration.150ms></i>
            @endif
        </a>
        
        <a href="{{ route('manager.branches.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.branches*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-storefront text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Cabang</span>
        </a>

        <a href="{{ route('manager.stock-requests.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.stock-requests*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-package text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Manajemen Stok</span>
        </a>

        <a href="{{ route('manager.transactions.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.transactions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-receipt text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Transaksi</span>
        </a>

        <a href="{{ route('manager.expenses.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.expenses*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-money text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Pengeluaran</span>
        </a>

        <a href="{{ route('manager.reports.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.reports*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-chart-line-up text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Laporan Keuangan</span>
        </a>

        <a href="{{ route('manager.menus.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.menus*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-coffee text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Menu</span>
        </a>

        <a href="{{ route('manager.promotions.index') }}"
           :class="open ? '' : 'justify-center'"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('manager.promotions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-coffee-bean text-xl"></i>
            <span class="overflow-hidden whitespace-nowrap smooth-transition"
                  :class="open ? 'opacity-100 max-w-[180px]' : 'opacity-0 max-w-0'">Promo</span>
        </a>

        <div x-show="open" x-transition.opacity.duration.200ms class="mt-8 mb-6 p-5 rounded-3xl bg-gradient-to-br from-elco-dark via-elco-coffee to-elco-mocha text-white shadow-lg relative overflow-hidden group smooth-transition hover:-translate-y-1">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 smooth-transition"></div>
            <div class="flex -space-x-2 mb-3 relative z-10">
                <img class="w-8 h-8 rounded-full border-2 border-elco-coffee" src="https://i.pravatar.cc/100?img=1" alt="avatar">
                <img class="w-8 h-8 rounded-full border-2 border-elco-coffee" src="https://i.pravatar.cc/100?img=2" alt="avatar">
                <div class="w-8 h-8 rounded-full border-2 border-elco-coffee bg-white/20 backdrop-blur-sm flex items-center justify-center text-[10px]">+3</div>
            </div>
            <p class="text-sm font-medium mb-4 relative z-10 text-elco-cream">Laporan laba rugi bulan ini membutuhkan review Anda.</p>
            <div class="flex gap-2 relative z-10">
                <button class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white text-xs py-2 rounded-xl smooth-transition active:scale-95">Nanti</button>
                <button class="flex-1 bg-white text-elco-coffee font-semibold text-xs py-2 rounded-xl shadow-md smooth-transition hover:shadow-lg active:scale-95">Review</button>
            </div>
        </div>
    </nav>
</aside>
