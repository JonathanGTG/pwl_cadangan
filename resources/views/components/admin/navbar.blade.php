<header class="h-24 px-8 flex items-center justify-between z-10">
    <div>
        <h1 class="text-2xl font-display font-bold text-elco-coffee">Dashboard Admin</h1>
        <p class="text-sm text-gray-500 mt-1">{{ now()->translatedFormat('l, j F Y') }}</p>
    </div>

    <div class="flex items-center gap-6">

        {{-- Bell Notifikasi --}}
        <div class="relative" id="notifWrapper">
            <button onclick="toggleNotif()"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-soft hover:shadow-hover smooth-transition active:scale-95 text-gray-500 hover:text-elco-coffee relative">
                <i class="ph ph-bell text-lg"></i>
                @php
                    $pendingStocks  = \App\Models\StockRequest::where('branch_id', auth()->user()->branch_id)
                                    ->where('status', 'pending')->latest()->take(3)->get();
                    $pendingTrx     = \App\Models\Transaction::where('branch_id', auth()->user()->branch_id)
                                    ->where('status', 'pending')->latest()->take(3)->get();
                    $cancelRequests = \App\Models\Transaction::where('branch_id', auth()->user()->branch_id)
                                    ->where('status', 'completed')
                                    ->where('cancel_reason', 'like', '[REQUEST CANCEL]%')
                                    ->latest()->take(3)->get();
                    $pendingCount   = $pendingStocks->count() + $cancelRequests->count();
                @endphp
                @if($pendingCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
                @endif
            </button>

            {{-- Dropdown Notif --}}
            <div id="notifDropdown"
                 class="hidden absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-hover z-50 overflow-hidden border border-gray-100">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <p class="font-semibold text-sm text-gray-800">Notifikasi</p>
                    <span class="text-xs text-gray-400">{{ $pendingCount }} belum dibaca</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    @php
                        $pendingStocks  = \App\Models\StockRequest::where('branch_id', auth()->user()->branch_id)
                                        ->where('status', 'pending')->latest()->take(3)->get();
                        $pendingTrx     = \App\Models\Transaction::where('branch_id', auth()->user()->branch_id)
                                        ->where('status', 'pending')->latest()->take(3)->get();
                        $cancelRequests = \App\Models\Transaction::where('branch_id', auth()->user()->branch_id)
                                        ->where('status', 'completed')
                                        ->where('cancel_reason', 'like', '[REQUEST CANCEL]%')
                                        ->latest()->take(3)->get();
                        $pendingCount   = $pendingStocks->count() + $cancelRequests->count();
                    @endphp

                    @forelse($pendingStocks as $req)
                    <a href="{{ route('admin.stock-requests.index') }}"
                       class="flex items-center gap-3 p-4 hover:bg-gray-50 smooth-transition border-b border-gray-50">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-package text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Pengajuan: {{ $req->item_name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @empty
                    @endforelse

                    @forelse($cancelRequests as $trx)
                    <a href="{{ route('admin.transactions.index') }}"
                    class="flex items-center gap-3 p-4 hover:bg-red-50 smooth-transition border-b border-gray-50">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-x-circle text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Minta Batal: {{ $trx->invoice_number }}</p>
                            <p class="text-xs text-gray-400">{{ $trx->updated_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @empty
                    @endforelse

                    @forelse($pendingTrx as $trx)
                    <a href="{{ route('admin.transactions.index') }}"
                       class="flex items-center gap-3 p-4 hover:bg-gray-50 smooth-transition border-b border-gray-50">
                        <div class="w-9 h-9 rounded-xl bg-yellow-50 text-yellow-500 flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill ph-receipt text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Transaksi: {{ $trx->invoice_number }}</p>
                            <p class="text-xs text-gray-400">{{ $trx->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @empty
                    @endforelse

                    @if($pendingCount === 0)
                    <div class="py-8 text-center text-gray-400 text-sm">
                        <i class="ph ph-check-circle text-3xl text-emerald-400 block mb-2"></i>
                        Tidak ada notifikasi baru
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" id="profileWrapper">
            <div class="flex items-center gap-3 pl-4 border-l border-gray-200 cursor-pointer group"
                onclick="toggleProfile()">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-elco-coffee to-elco-mocha flex items-center justify-center text-white font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">Admin Cabang</p>
                </div>
                <i class="ph ph-caret-down text-gray-400"></i>
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
function toggleNotif() {
    const dd = document.getElementById('notifDropdown');
    dd.classList.toggle('hidden');
    document.getElementById('profileDropdown')?.classList.add('hidden');
}

function toggleProfile() {
    const dd = document.getElementById('profileDropdown');
    dd.classList.toggle('hidden');
    document.getElementById('notifDropdown')?.classList.add('hidden');
}
// Tutup jika klik di luar
document.addEventListener('click', function(e) {
    const notifWrapper = document.getElementById('notifWrapper');
    const profileWrapper = document.getElementById('profileWrapper');
    if (notifWrapper && !notifWrapper.contains(e.target)) {
        document.getElementById('notifDropdown')?.classList.add('hidden');
    }
    if (profileWrapper && !profileWrapper.contains(e.target)) {
        document.getElementById('profileDropdown')?.classList.add('hidden');
    }
});
</script>
@endpush
