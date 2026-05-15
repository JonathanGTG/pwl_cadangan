<aside class="w-64 bg-white h-full flex flex-col shadow-soft z-20 flex-shrink-0">
    <!-- Logo -->
    <div class="h-24 flex items-center px-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center shadow-lg text-white">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <span class="font-display font-bold text-xl text-elco-coffee tracking-wide">
                ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Admin Cabang</span>
            </span>
        </div>
    </div>

    <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-[#F6F3F0] text-elco-coffee rounded-2xl font-semibold smooth-transition">
            <i class="ph-fill ph-squares-four text-xl"></i>
            Dashboard
            <i class="ph ph-arrow-right ml-auto"></i>
        </a>
        <a href="{{ route('admin.stocks.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.stocks*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-package text-xl"></i> Stok Cabang
        </a>

        <a href="{{ route('admin.stock-requests.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.stock-requests*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-arrow-circle-up text-xl"></i> Pengajuan Kebutuhan
        </a>
        <a href="{{ route('admin.expenses.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.expenses*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-money text-xl"></i> Pengeluaran
        </a>
        <a href="{{ route('admin.promotions.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.promotions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-tag text-xl"></i> Promo Cabang
        </a>
        <a href="{{ route('admin.reports.index') }}"
        class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
        {{ request()->routeIs('admin.reports*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
            <i class="ph ph-chart-bar text-xl"></i> Laporan Bulanan
        </a>
        <a href="{{ route('admin.transactions.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition
            {{ request()->routeIs('admin.transactions*') ? 'bg-[#F6F3F0] !text-elco-coffee font-semibold' : '' }}">
                <i class="ph ph-receipt text-xl"></i> Transaksi
        </a>

        <div class="px-2 mb-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</div>
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition">
                <i class="ph ph-gear text-xl"></i> Pengaturan
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-2xl font-medium smooth-transition mt-2">
                <i class="ph ph-sign-out text-xl"></i> Keluar
            </button>
        </form>
    </nav>
</aside>