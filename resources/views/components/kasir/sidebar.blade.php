<aside class="w-64 bg-white h-full flex flex-col shadow-soft z-20 flex-shrink-0">
    <div class="h-24 flex items-center px-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center shadow-lg text-white">
                <i class="ph ph-coffee text-2xl"></i>
            </div>
            <span class="font-display font-bold text-xl text-elco-coffee tracking-wide">
                ELCO<span class="text-elco-mocha text-sm font-medium block -mt-1">Kasir</span>
            </span>
        </div>
    </div>

    <div class="px-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto hide-scrollbar">
        <a href="{{ route('kasir.transactions.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('kasir.transactions*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
            <i class="ph {{ request()->routeIs('kasir.transactions*') ? 'ph-fill' : 'ph' }}-shopping-cart text-xl"></i>
            Transaksi
            @if(request()->routeIs('kasir.transactions*'))
                <i class="ph ph-arrow-right ml-auto"></i>
            @endif
        </a>

        <a href="{{ route('kasir.shifts.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium smooth-transition
           {{ request()->routeIs('kasir.shifts*') ? 'bg-[#F6F3F0] text-elco-coffee font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-elco-coffee' }}">
            <i class="ph ph-clock text-xl"></i>
            Shift Saya
        </a>

        <div class="px-2 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-elco-coffee rounded-2xl font-medium smooth-transition">
            <i class="ph ph-gear text-xl"></i> Pengaturan
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-2xl font-medium smooth-transition">
                <i class="ph ph-sign-out text-xl"></i> Keluar
            </button>
        </form>
    </nav>
</aside>