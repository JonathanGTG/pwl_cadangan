@extends('layouts.manager')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT COLUMN -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Stats Cards -->
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-display font-semibold text-gray-800">
                Ringkasan Operasional <span class="text-gray-400 text-sm font-normal">/ Bulan Ini</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-wallet"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Pendapatan</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    Rp {{ number_format($data['total_income'], 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-storefront"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Cabang Aktif</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    {{ $data['total_branches'] }} <span class="text-sm text-gray-400 font-normal">cabang</span>
                </h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-soft smooth-transition hover:-translate-y-1 hover:shadow-hover">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-2xl mb-4">
                    <i class="ph-fill ph-package"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pengajuan Pending</p>
                <h3 class="text-2xl font-display font-bold text-gray-800">
                    {{ $data['pending_requests'] }} <span class="text-sm text-gray-400 font-normal">permintaan</span>
                </h3>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded-3xl shadow-soft">
            <h2 class="text-lg font-display font-semibold text-gray-800 mb-6">Grafik Pendapatan Bulan Ini</h2>
            <div class="h-64 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        
    </div>
    

    <!-- RIGHT COLUMN -->
    <div class="space-y-6 mt-12">

        <!-- Request Update Stok (ganti Performa Cabang) -->
        <div class="bg-white p-6 rounded-3xl shadow-soft">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-display font-semibold text-gray-800">Request Stok</h2>
                    <p class="text-xs text-gray-500 mt-1">Pengajuan menunggu verifikasi</p>
                </div>
                <a href="{{ route('manager.stock-requests.index') }}"
                   class="text-xs text-elco-coffee hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @forelse($data['latest_requests'] as $req)
                <div class="flex items-start justify-between p-3 rounded-2xl bg-gray-50 hover:bg-elco-cream smooth-transition">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl {{ $req->type === 'stock' ? 'bg-blue-50 text-blue-500' : 'bg-purple-50 text-purple-500' }} flex items-center justify-center flex-shrink-0">
                            <i class="ph-fill {{ $req->type === 'stock' ? 'ph-package' : 'ph-wrench' }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $req->item_name }}</p>
                            <p class="text-xs text-gray-500">{{ $req->branch->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $req->quantity }} {{ $req->unit }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1 ml-2 flex-shrink-0">
                        {{-- Approve --}}
                        <form id="approve-{{ $req->id }}" 
                              action="{{ route('manager.stock-requests.approve', $req) }}" method="POST">
                            @csrf
                            <button type="button"
                                onclick="elcoConfirm({
                                    title: 'Setujui Pengajuan?',
                                    text: 'Stok akan bertambah otomatis setelah disetujui.',
                                    confirmText: 'Ya, Setujui',
                                    confirmColor: '#10b981',
                                    icon: 'question',
                                    onConfirm: () => document.getElementById('approve-{{ $req->id }}').submit()
                                })"
                                class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 smooth-transition text-sm">
                                <i class="ph ph-check"></i>
                            </button>
                        </form>
                        {{-- Reject --}}
                        <button type="button"
                            onclick="openRejectModal({{ $req->id }})"
                            class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 smooth-transition text-sm">
                            <i class="ph ph-x"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400">
                    <i class="ph ph-check-circle text-3xl text-emerald-400 block mb-2"></i>
                    <p class="text-sm">Tidak ada pengajuan pending</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Promo Aktif -->
        <div class="p-6 rounded-3xl relative overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-[#3E2723] via-[#5D4037] to-[#8D6E63]"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/20 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-display font-semibold text-white">Promo Aktif</h3>
                    <a href="{{ route('manager.promotions.index') }}"
                       class="bg-white/20 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full hover:bg-white/30 smooth-transition">
                        Kelola
                    </a>
                </div>

                @if($data['active_promos']->count() > 0)
                    @foreach($data['active_promos'] as $promo)
                    <div class="mb-3 p-3 bg-white/10 rounded-2xl">
                        <p class="text-sm font-semibold text-white">{{ $promo->name }}</p>
                        <p class="text-xs text-white/70 mt-0.5">
                            Diskon {{ $promo->discount_label }} •
                            s/d {{ $promo->end_date->format('d M Y') }}
                        </p>
                    </div>
                    @endforeach
                    <a href="{{ route('manager.promotions.create') }}"
                       class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 bg-white text-elco-coffee font-semibold text-xs rounded-xl shadow-md smooth-transition hover:shadow-lg active:scale-95">
                        <i class="ph ph-plus"></i> Tambah Promo
                    </a>
                @else
                    <p class="text-sm text-white/70 mb-4">Belum ada promo aktif saat ini.</p>
                    <a href="{{ route('manager.promotions.create') }}"
                       class="w-full flex items-center justify-center gap-2 py-2.5 bg-white text-elco-coffee font-semibold text-xs rounded-xl shadow-md smooth-transition hover:shadow-lg active:scale-95">
                        <i class="ph ph-plus"></i> Buat Promo Sekarang
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="xl col-span 1 space-y-6 mt-6">
        <!-- Aktivitas Cabang dari Database -->
        <div class="bg-white p-6 rounded-3xl shadow-soft mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-display font-semibold text-gray-800">Aktivitas Cabang Hari Ini</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-medium px-4">Nama Cabang</th>
                            <th class="pb-3 font-medium px-4">Alamat</th>
                            <th class="pb-3 font-medium px-4">Transaksi</th>
                            <th class="pb-3 font-medium px-4">Omset Hari Ini</th>
                            <th class="pb-3 font-medium px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['branch_activities'] as $branch)
                        <tr class="group hover:bg-gray-50 smooth-transition border-b border-gray-50 last:border-0">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-400 flex items-center justify-center">
                                        <i class="ph-fill ph-storefront text-sm"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $branch['name'] }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $branch['address'] }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">
                                {{ $branch['trx'] }} transaksi
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm font-semibold text-emerald-600">
                                    Rp {{ number_format($branch['income'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <a href="{{ route('manager.reports.index') }}?branch_id={{ $branch['id'] }}"
                                   class="text-xs font-medium text-elco-coffee bg-elco-cream px-4 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                Belum ada cabang aktif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{{-- Modal Reject --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <h3 class="font-display font-bold text-gray-800 text-lg mb-2">Tolak Pengajuan</h3>
        <p class="text-sm text-gray-500 mb-5">Berikan alasan penolakan.</p>
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <textarea name="rejection_note" rows="3" required
                placeholder="Alasan penolakan..."
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Modal reject
function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/manager/stock-requests/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Chart pendapatan 12 bulan
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Pendapatan',
            data: [0,0,0,0,0,0,0,0,0,0,0, {{ $data['total_income'] }}],
            borderColor: '#5C3D2E',
            backgroundColor: '#5C3D2E15',
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#5C3D2E',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
<script>
// Toggle field nama item berdasarkan tipe
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const isStock = this.value === 'stock';
        document.getElementById('stockSelect').classList.toggle('hidden', !isStock);
        document.getElementById('operationalInput').classList.toggle('hidden', isStock);

        // Sync nilai ke field yang aktif
        document.getElementById('itemNameSelect').required = isStock;
        document.getElementById('itemNameOps').required    = !isStock;
    });
});

// Sebelum submit, satukan nilai item_name
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    if (type === 'operational') {
        // Buat hidden input dengan nama item_name
        const val = document.getElementById('itemNameOps').value;
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'item_name';
        hidden.value = val;
        this.appendChild(hidden);
    }
});
</script>
@endpush