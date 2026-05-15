@extends('layouts.manager')

@section('content')

{{-- Alert --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i>
    {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Manajemen Menu</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola menu & harga dasar ELCO</p>
    </div>
    <a href="{{ route('manager.menus.create') }}"
       class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Tambah Menu
    </a>
</div>

{{-- Filter Kategori --}}
<div class="flex gap-2 mb-6">
    @foreach(['semua', 'minuman', 'makanan', 'snack'] as $cat)
    <button onclick="filterMenu('{{ $cat }}')"
        id="btn-{{ $cat }}"
        class="px-4 py-2 rounded-xl text-sm font-medium smooth-transition
        {{ $cat === 'semua' ? 'bg-elco-coffee text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-50 shadow-soft' }}">
        {{ ucfirst($cat) }}
    </button>
    @endforeach
</div>

{{-- Grid Menu --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="menu-grid">
    @forelse($menus as $menu)
    <div class="menu-card bg-white rounded-3xl shadow-soft overflow-hidden smooth-transition
        {{ $menu->trashed() ? 'opacity-50' : 'hover:-translate-y-1 hover:shadow-hover' }}"
        data-category="{{ $menu->category }}">

        {{-- Gambar Menu --}}
        <div class="h-44 bg-gradient-to-br from-elco-cream to-orange-50 relative overflow-hidden">
            @if($menu->image)
                <img src="{{ Storage::url($menu->image) }}"
                     alt="{{ $menu->name }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="ph-fill ph-coffee text-6xl text-elco-latte/50"></i>
                </div>
            @endif

            {{-- Badge Kategori --}}
            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold bg-white/80 backdrop-blur-sm text-elco-coffee">
                {{ ucfirst($menu->category) }}
            </span>

            {{-- Badge Status --}}
            @if($menu->trashed())
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white">
                    Dihapus
                </span>
            @elseif(!$menu->is_available)
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500 text-white">
                    Nonaktif
                </span>
            @endif
        </div>

        {{-- Info Menu --}}
        <div class="p-5">
            <h3 class="font-display font-bold text-gray-800 text-lg">{{ $menu->name }}</h3>
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                {{ $menu->description ?? 'Tidak ada deskripsi' }}
            </p>

            {{-- Harga --}}
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Harga Dasar</p>
                    <p class="text-lg font-display font-bold text-elco-coffee">
                        Rp {{ number_format($menu->base_price, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Aksi --}}
                @if($menu->trashed())
                    <form action="{{ route('manager.menus.restore', $menu->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 smooth-transition">
                            <i class="ph ph-arrow-counter-clockwise"></i> Pulihkan
                        </button>
                    </form>
                @else
                    <div class="flex gap-2">
                        <a href="{{ route('manager.menus.edit', $menu) }}"
                           class="w-9 h-9 rounded-xl bg-elco-cream text-elco-coffee flex items-center justify-center hover:bg-elco-latte/30 smooth-transition">
                            <i class="ph ph-pencil"></i>
                        </a>
                        <form id="form-hapus-{{ $menu->id }}" method="POST"
                            action="{{ route('manager.menus.destroy', $menu->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                onclick="elcoConfirm({
                                    title: 'Hapus Menu?',
                                    text: 'Menu {{ addslashes($menu->name) }} akan dinonaktifkan.',
                                    confirmText: 'Ya, Hapus',
                                    confirmColor: '#ef4444',
                                    icon: 'warning',
                                    onConfirm: () => document.getElementById('form-hapus-{{ $menu->id }}').submit()
                                })"
                                class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 smooth-transition">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-16 text-center">
        <i class="ph ph-coffee text-5xl text-gray-300 block mb-3"></i>
        <p class="text-gray-400">Belum ada menu. Tambahkan menu pertama!</p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function filterMenu(category) {
    // Update button style
    document.querySelectorAll('[id^="btn-"]').forEach(btn => {
        btn.classList.remove('bg-elco-coffee', 'text-white', 'shadow-md');
        btn.classList.add('bg-white', 'text-gray-500', 'shadow-soft');
    });
    document.getElementById('btn-' + category).classList.add('bg-elco-coffee', 'text-white', 'shadow-md');
    document.getElementById('btn-' + category).classList.remove('bg-white', 'text-gray-500');

    // Filter cards
    document.querySelectorAll('.menu-card').forEach(card => {
        if (category === 'semua' || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
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