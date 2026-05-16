<aside class="w-64 bg-white h-full flex flex-col shadow-soft z-20 flex-shrink-0">
        <!-- Logo -->
        <div class="h-24 flex items-center px-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center shadow-lg text-white">
                    <i class="ph ph-coffee text-2xl"></i>
                </div>
                <span class="font-display font-bold text-xl text-elco-coffee tracking-wide">ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Manager</span></span>
            </div>
        </div>

        <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
            <!-- Active Menu -->
            <a href="{{ route('manager.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold smooth-transition
            {{ request()->routeIs('manager.dashboard') ? 'bg-[#F6F3F0] text-elco-coffee' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
                <i class="ph-fill ph-squares-four text-xl"></i>
                Dashboard
                <i class="ph ph-arrow-right ml-auto"></i>
            </a>
            
            <a href="{{ route('manager.branches.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
                {{ request()->routeIs('manager.branches*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-storefront text-xl"></i> Cabang
            </a>
            <a href="{{ route('manager.stock-requests.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
                {{ request()->routeIs('manager.stock-requests*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                    <i class="ph ph-package text-xl"></i> Manajemen Stok
            </a>
            <a href="{{ route('manager.transactions.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('manager.transactions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-receipt text-xl"></i> Transaksi
            </a>
            <a href="{{ route('manager.expenses.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('manager.expenses*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-money text-xl"></i> Pengeluaran
            </a>
            <a href="{{ route('manager.reports.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('manager.reports*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-chart-line-up text-xl"></i> Laporan Keuangan
            </a>
            <a href="{{ route('manager.menus.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('manager.menus*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-coffee text-xl"></i> Menu
            </a>
            <a href="{{ route('manager.promotions.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
                {{ request()->routeIs('manager.promotions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                    <i class="ph ph-coffee-bean text-xl"></i>Promo
            </a>

            <!-- Gradient Card in Sidebar (Matching Reference) -->
            <div class="mt-8 mb-6 p-5 rounded-3xl bg-gradient-to-br from-elco-dark via-elco-coffee to-elco-mocha text-white shadow-lg relative overflow-hidden group smooth-transition hover:-translate-y-1">
                <!-- Decorative background shapes -->
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
