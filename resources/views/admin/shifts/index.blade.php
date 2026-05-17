@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Jadwal Shift Kasir</h2>
        <p class="text-sm text-gray-500 mt-1">Atur jadwal shift untuk kasir cabang</p>
    </div>
    <button onclick="document.getElementById('addShiftModal').classList.remove('hidden')"
        class="flex items-center gap-2 bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-md hover:shadow-hover smooth-transition active:scale-95">
        <i class="ph ph-plus"></i> Tambah Jadwal
    </button>
</div>

{{-- Jadwal Minggu Ini --}}
<div class="bg-white rounded-3xl shadow-soft overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-800">Jadwal 7 Hari Ke Depan</h3>
    </div>
    @forelse($weekSchedules as $date => $daySchedules)
    <div class="border-b border-gray-50 last:border-0">
        <div class="px-5 py-3 bg-gray-50">
            <p class="text-sm font-semibold text-gray-700">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                @if($date === today()->format('Y-m-d'))
                    <span class="ml-2 px-2 py-0.5 bg-elco-coffee text-white text-xs rounded-full">Hari Ini</span>
                @endif
            </p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($daySchedules as $schedule)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 smooth-transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl
                        {{ $schedule->shift === 'pagi' ? 'bg-yellow-50 text-yellow-500' : '' }}
                        {{ $schedule->shift === 'siang' ? 'bg-orange-50 text-orange-500' : '' }}
                        {{ $schedule->shift === 'malam' ? 'bg-blue-50 text-blue-500' : '' }}
                        flex items-center justify-center text-xl">
                        <i class="ph {{ $schedule->shift === 'pagi' ? 'ph-sunrise' : ($schedule->shift === 'siang' ? 'ph-sun' : 'ph-moon') }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $schedule->user->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ ucfirst($schedule->shift) }} •
                            {{ substr($schedule->start_time, 0, 5) }} –
                            {{ substr($schedule->end_time, 0, 5) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($schedule->is_active_now)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 animate-pulse">
                        ● Aktif
                    </span>
                    @endif
                    <form id="del-{{ $schedule->id }}"
                          action="{{ route('admin.shifts.destroy', $schedule) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            onclick="elcoConfirm({
                                title: 'Hapus Jadwal?',
                                text: 'Jadwal shift ini akan dihapus.',
                                confirmText: 'Ya, Hapus',
                                confirmColor: '#ef4444',
                                onConfirm: () => document.getElementById('del-{{ $schedule->id }}').submit()
                            })"
                            class="w-8 h-8 rounded-xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-100 smooth-transition">
                            <i class="ph ph-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="py-12 text-center text-gray-400">
        <i class="ph ph-calendar text-4xl block mb-2"></i>
        <p class="text-sm">Belum ada jadwal shift minggu ini</p>
    </div>
    @endforelse
</div>

{{-- Modal Tambah Jadwal --}}
<div id="addShiftModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-display font-bold text-gray-800 text-lg">Tambah Jadwal Shift</h3>
            <button onclick="document.getElementById('addShiftModal').classList.add('hidden')"
                class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center hover:bg-gray-200">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kasir <span class="text-red-500">*</span></label>
                <select name="user_id" required
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                    <option value="">Pilih Kasir</option>
                    @foreach($kasirs as $kasir)
                    <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shift <span class="text-red-500">*</span></label>
                    <select name="shift" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm bg-white">
                        <option value="pagi">Pagi (07–15)</option>
                        <option value="siang">Siang (15–22)</option>
                        <option value="malam">Malam (22–07)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="shift_date" required
                        min="{{ today()->format('Y-m-d') }}"
                        value="{{ today()->format('Y-m-d') }}"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea name="note" rows="2" placeholder="Catatan tambahan..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addShiftModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white text-sm font-semibold shadow-md hover:shadow-hover smooth-transition">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
