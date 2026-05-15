<header class="h-24 px-8 flex items-center justify-between z-10">
    <div>
        <h1 class="text-2xl font-display font-bold text-elco-coffee">
            @yield('page_title', 'Dashboard')
        </h1>
        <p class="text-sm text-gray-500 mt-1">{{ now()->translatedFormat('l, j F Y') }}</p>
    </div>

    <div class="flex items-center gap-4">

        {{-- Notifikasi --}}
        @php
            $pendingRequests = \App\Models\StockRequest::where('status', 'pending')->count();
            $pendingExpenses = \App\Models\Expense::where('status', 'pending')->count();
            $pendingPromos   = \App\Models\Promotion::where('type', 'branch')
                                ->where('is_active', false)->count();
            $notifCount      = $pendingRequests + $pendingExpenses + $pendingPromos;
        @endphp

        <div class="relative" id="managerNotifWrapper">
            <button onclick="toggleManagerNotif()"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition active:scale-95 text-gray-500 hover:text-elco-coffee relative">
                <i class="ph ph-bell text-lg"></i>
                @if($notifCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ $notifCount > 9 ? '9+' : $notifCount }}
                </span>
                @endif
            </button>

            <div id="managerNotifDropdown"
                 class="hidden absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-hover z-50 overflow-hidden border border-gray-100">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <p class="font-semibold text-sm text-gray-800">Notifikasi</p>
                    <span class="text-xs text-gray-400">{{ $notifCount }} item</span>
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">

                    @if($pendingRequests > 0)
                    <a href="{{ route('manager.stock-requests.index') }}"
                       class="flex items-center gap-3 p-4 hover:bg-gray-50 smooth-transition">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-package text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $pendingRequests }} Pengajuan Stok Pending</p>
                            <p class="text-xs text-gray-400">Menunggu verifikasi kamu</p>
                        </div>
                        <span class="ml-auto bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingRequests }}</span>
                    </a>
                    @endif

                    @if($pendingExpenses > 0)
                    <a href="{{ route('manager.expenses.index') }}"
                       class="flex items-center gap-3 p-4 hover:bg-gray-50 smooth-transition">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-receipt text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $pendingExpenses }} Pengeluaran Pending</p>
                            <p class="text-xs text-gray-400">Menunggu verifikasi keuangan</p>
                        </div>
                        <span class="ml-auto bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingExpenses }}</span>
                    </a>
                    @endif

                    @if($pendingPromos > 0)
                    <a href="{{ route('manager.promotions.index') }}"
                       class="flex items-center gap-3 p-4 hover:bg-gray-50 smooth-transition">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-tag text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $pendingPromos }} Promo Cabang Pending</p>
                            <p class="text-xs text-gray-400">Menunggu tinjauan kamu</p>
                        </div>
                        <span class="ml-auto bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingPromos }}</span>
                    </a>
                    @endif

                    @if($notifCount === 0)
                    <div class="py-8 text-center text-gray-400">
                        <i class="ph ph-check-circle text-3xl text-emerald-400 block mb-2"></i>
                        <p class="text-sm">Semua sudah diproses!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" id="profileWrapper">
            <div class="flex items-center gap-3 pl-4 border-l border-gray-200 cursor-pointer"
                 onclick="toggleProfile()">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">Manager Pusat</p>
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
    </div>
</header>

@push('scripts')
<script>
function toggleManagerNotif() {
    document.getElementById('managerNotifDropdown').classList.toggle('hidden');
    document.getElementById('profileDropdown')?.classList.add('hidden');
}
function toggleProfile() {
    document.getElementById('profileDropdown').classList.toggle('hidden');
    document.getElementById('managerNotifDropdown')?.classList.add('hidden');
}
document.addEventListener('click', function(e) {
    const notifW   = document.getElementById('managerNotifWrapper');
    const profileW = document.getElementById('profileWrapper');
    if (notifW && !notifW.contains(e.target))
        document.getElementById('managerNotifDropdown')?.classList.add('hidden');
    if (profileW && !profileW.contains(e.target))
        document.getElementById('profileDropdown')?.classList.add('hidden');
});
</script>
@endpush