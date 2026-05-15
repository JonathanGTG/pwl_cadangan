@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Pengajuan Kebutuhan</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat pengajuan stok & alat operasional</p>
    </div>
    <a href="{{ route('admin.stock-requests.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Buat Pengajuan
    </a>
</div>

<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                <th class="py-4 px-6 font-medium">Item</th>
                <th class="py-4 px-6 font-medium">Tipe</th>
                <th class="py-4 px-6 font-medium">Jumlah</th>
                <th class="py-4 px-6 font-medium">Tanggal</th>
                <th class="py-4 px-6 font-medium">Status</th>
                <th class="py-4 px-6 font-medium">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 smooth-transition">
                <td class="py-4 px-6">
                    <p class="text-sm font-semibold text-gray-800">{{ $req->item_name }}</p>
                    <p class="text-xs text-gray-400">{{ $req->unit }}</p>
                </td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $req->type === 'stock' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                        {{ $req->type === 'stock' ? '📦 Stok' : '🔧 Operasional' }}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm text-gray-700 font-medium">
                    {{ $req->quantity }} {{ $req->unit }}
                </td>
                <td class="py-4 px-6 text-sm text-gray-500">
                    {{ $req->created_at->format('d M Y') }}
                </td>
                <td class="py-4 px-6">
                    <div class="space-y-1">
                        {{-- Status Pengajuan --}}
                        <span class="px-3 py-1 rounded-full text-xs font-medium block w-fit
                            {{ $req->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $req->status === 'approved' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $req->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $req->status === 'pending' ? '⏳ Pending'
                                : ($req->status === 'approved' ? '✅ Disetujui' : '❌ Ditolak') }}
                        </span>
                        {{-- Status Pengiriman --}}
                        @if($req->status === 'approved')
                        <span class="px-3 py-1 rounded-full text-xs font-medium block w-fit
                            {{ $req->delivery_status === 'waiting'   ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $req->delivery_status === 'delivered' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $req->delivery_status === 'confirmed' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ $req->delivery_status === 'waiting'   ? '🚚 Menunggu Barang' : '' }}
                            {{ $req->delivery_status === 'delivered' ? '📦 Menunggu Konfirmasi' : '' }}
                            {{ $req->delivery_status === 'confirmed' ? '✅ Stok Bertambah' : '' }}
                        </span>
                        @endif
                    </div>
                </td>

                {{-- Aksi Konfirmasi Kedatangan --}}
                <td class="py-4 px-6">
                    @if($req->status === 'approved' && $req->delivery_status === 'waiting')
                    <button onclick="openDeliveryModal({{ $req->id }})"
                        class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                        <i class="ph ph-package"></i> Konfirmasi Terima
                    </button>
                    @elseif($req->delivery_status === 'delivered')
                    <span class="text-xs text-blue-600 font-medium">📤 Terkirim ke Manager</span>
                    @elseif($req->delivery_status === 'confirmed')
                    <span class="text-xs text-emerald-600 font-medium">✅ Selesai</span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm text-gray-500 max-w-xs">
                    {{ $req->rejection_note ?? '-' }}
                </td>
                
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-12 text-center text-gray-400">
                    <i class="ph ph-clipboard-text text-4xl block mb-2"></i>
                    Belum ada pengajuan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{-- Modal Konfirmasi Barang Sampai --}}
    `<div id="deliveryModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
            <h3 class="font-display font-bold text-gray-800 text-lg mb-2">
                <i class="ph ph-package mr-2 text-emerald-500"></i>Konfirmasi Barang Sampai
            </h3>
            <p class="text-sm text-gray-500 mb-5">Upload foto bukti dan catatan kondisi barang yang diterima.</p>

            <form id="deliveryForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- Upload Foto --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Bukti Penerimaan <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center cursor-pointer hover:border-elco-mocha smooth-transition"
                        onclick="document.getElementById('deliveryPhotoInput').click()">
                        <img id="deliveryPhotoPreview" class="hidden mx-auto h-32 rounded-xl object-cover mb-2">
                        <div id="deliveryUploadPlaceholder">
                            <i class="ph ph-camera text-3xl text-gray-300 block mb-1"></i>
                            <p class="text-sm text-gray-400">Klik untuk foto barang</p>
                        </div>
                        <input type="file" id="deliveryPhotoInput" name="delivery_photo"
                            accept="image/*" class="hidden"
                            onchange="previewDeliveryPhoto(this)">
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Kondisi Barang <span class="text-red-500">*</span>
                    </label>
                    <textarea name="delivery_note" rows="3" required
                        placeholder="contoh: Barang diterima dalam kondisi baik, jumlah sesuai..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeliveryModal()"
                        class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-2xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 smooth-transition">
                        <i class="ph ph-paper-plane-right mr-1"></i> Kirim ke Manager
                    </button>
                </div>
            </form>
        </div>
    </div>`
</div>

@endsection

<script>
    function openDeliveryModal(id) {
    document.getElementById('deliveryForm').action = `/admin/stock-requests/${id}/confirm-delivery`;
    document.getElementById('deliveryModal').classList.remove('hidden');
}
function closeDeliveryModal() {
    document.getElementById('deliveryModal').classList.add('hidden');
}
function previewDeliveryPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('deliveryPhotoPreview').src = e.target.result;
            document.getElementById('deliveryPhotoPreview').classList.remove('hidden');
            document.getElementById('deliveryUploadPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>