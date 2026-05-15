@extends('layouts.manager')

@section('content')

{{-- Alert Success --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i>
    {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Manajemen Cabang</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola seluruh cabang ELCO</p>
    </div>
    <a href="{{ route('manager.branches.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Tambah Cabang
    </a>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 bg-gray-50">
                    <th class="py-4 px-6 font-medium">Nama Cabang</th>
                    <th class="py-4 px-6 font-medium">Alamat</th>
                    <th class="py-4 px-6 font-medium">Telepon</th>
                    <th class="py-4 px-6 font-medium">Status</th>
                    <th class="py-4 px-6 font-medium">Dibuat</th>
                    <th class="py-4 px-6 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr class="group border-b border-gray-50 last:border-0
                    {{ $branch->trashed() ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50' }}
                    smooth-transition">

                    {{-- Nama --}}
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl
                                {{ $branch->trashed() ? 'bg-gray-100 text-gray-400' : 'bg-orange-50 text-orange-500' }}
                                flex items-center justify-center">
                                <i class="ph-fill ph-storefront"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $branch->name }}</p>
                                @if($branch->trashed())
                                    <span class="text-xs text-red-400">Dihapus</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Alamat --}}
                    <td class="py-4 px-6 text-sm text-gray-600 max-w-xs truncate">
                        {{ $branch->address }}
                    </td>

                    {{-- Telepon --}}
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $branch->phone ?? '-' }}
                    </td>

                    {{-- Status --}}
                    <td class="py-4 px-6">
                        @if($branch->trashed())
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                Dihapus
                            </span>
                        @elseif($branch->status === 'active')
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                ● Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                ● Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Dibuat --}}
                    <td class="py-4 px-6 text-sm text-gray-500">
                        {{ $branch->created_at->format('d M Y') }}
                    </td>

                    {{-- Aksi --}}
                    <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manager.branches.edit', $branch) }}"
                                class="text-xs font-medium text-elco-coffee bg-elco-cream px-3 py-2 rounded-xl hover:bg-elco-latte/30 smooth-transition">
                                    <i class="ph ph-pencil"></i> Edit
                                </a>
                                <form action="{{ route('manager.branches.destroy', $branch->id) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Hapus cabang {{ addslashes($branch->name) }} secara permanen? Tindakan ini tidak dapat dibatalkan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-medium text-red-500 bg-red-50 px-3 py-2 rounded-xl hover:bg-red-100 smooth-transition">
                                        <i class="ph ph-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <i class="ph ph-storefront text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">Belum ada cabang. Tambahkan cabang pertama!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Modal Hapus Cabang --}}
<div id="deleteBranchModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-3xl mx-auto mb-4">
            <i class="ph-fill ph-warning"></i>
        </div>
        <h3 class="font-display font-bold text-gray-800 text-lg text-center mb-1">Hapus Cabang?</h3>
        <p class="text-sm text-gray-500 text-center mb-6">Cabang <strong id="deleteBranchName"></strong> akan dinonaktifkan.</p>

        <form id="deleteBranchForm" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Penghapusan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="delete_reason" rows="3"
                    placeholder="Tuliskan alasan penghapusan cabang ini..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteBranchModal()"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 smooth-transition">
                    <i class="ph ph-trash mr-1"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
<!-- 
@push('scripts')
<script>
function openDeleteBranchModal(id, name) {
    document.getElementById('deleteBranchName').textContent = name;
    document.getElementById('deleteBranchForm').action = `/manager/branches/${id}`;
    document.getElementById('deleteBranchModal').classList.remove('hidden');
}
function closeDeleteBranchModal() {
    document.getElementById('deleteBranchModal').classList.add('hidden');
}
</script>
@endpush -->