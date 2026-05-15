@extends('layouts.kasir')

@section('content')

@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
    <i class="ph-fill ph-x-circle text-xl"></i> {{ session('error') }}
</div>
@endif

<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-display font-bold text-gray-800">Shift Kasir</h2>
        <p class="text-sm text-gray-500 mt-1">{{ now()->translatedFormat('l, j F Y') }}</p>
    </div>

    {{-- Status Shift Aktif --}}
    @if($activeShift)
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl">
                <i class="ph-fill ph-clock"></i>
            </div>
            <div>
                <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">Shift Aktif</p>
                <h3 class="text-xl font-display font-bold text-gray-800">{{ $activeShift->shift_label }}</h3>
            </div>
            <span class="ml-auto px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full animate-pulse">
                ● AKTIF
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-2xl">
                <p class="text-xs text-gray-500 mb-1">Clock In</p>
                <p class="text-lg font-bold text-gray-800">{{ $activeShift->clock_in }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-2xl">
                <p class="text-xs text-gray-500 mb-1">Sisa Waktu Shift</p>
                <p class="text-lg font-bold text-elco-coffee" id="duration">—</p>
            </div>
        </div>

        <form id="clockOutForm" 
              action="{{ route('kasir.shifts.clock-out', $activeShift) }}" 
              method="POST">
            @csrf
            <button type="button"
                onclick="elcoConfirm({
                    title: 'Selesaikan Shift?',
                    text: 'Pastikan semua transaksi sudah diselesaikan.',
                    confirmText: 'Ya, Selesai',
                    confirmColor: '#5C3D2E',
                    icon: 'question',
                    onConfirm: () => document.getElementById('clockOutForm').submit()
                })"
                class="w-full py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white font-semibold text-sm shadow-md hover:shadow-hover smooth-transition active:scale-95">
                <i class="ph ph-sign-out mr-2"></i> Selesaikan Shift
            </button>
        </form>
    </div>

    @else

    {{-- Form Mulai Shift --}}
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-semibold text-gray-800 mb-5">Mulai Shift Hari Ini</h3>
        <form action="{{ route('kasir.shifts.clock-in') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Shift</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['pagi' => ['🌅', 'Pagi', '07.00 - 15.00'], 'siang' => ['☀️', 'Siang', '15.00 - 22.00'], 'malam' => ['🌙', 'Malam', '22.00 - 07.00']] as $val => $info)
                    <label class="cursor-pointer">
                        <input type="radio" name="shift" value="{{ $val }}" class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-2xl peer-checked:border-elco-coffee peer-checked:bg-elco-cream smooth-transition text-center">
                            <p class="text-2xl mb-1">{{ $info[0] }}</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $info[1] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $info[2] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('shift')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3 rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha text-white font-semibold text-sm shadow-md hover:shadow-hover smooth-transition active:scale-95">
                <i class="ph ph-play-circle mr-2"></i> Mulai Shift
            </button>
        </form>
    </div>
    @endif

    {{-- Riwayat Shift --}}
    <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-display font-semibold text-gray-800">Riwayat Shift</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($shiftHistory as $shift)
            <div class="flex items-center justify-between p-4 hover:bg-gray-50 smooth-transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $shift->status === 'active' ? 'bg-emerald-50 text-emerald-500' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center">
                        <i class="ph-fill ph-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $shift->shift_label }}</p>
                        <p class="text-xs text-gray-500">{{ $shift->shift_date->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">
                        {{ $shift->clock_in }} — {{ $shift->clock_out ?? 'Aktif' }}
                    </p>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {{ $shift->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $shift->status === 'active' ? 'Aktif' : 'Selesai' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-400 text-sm">Belum ada riwayat shift</div>
            @endforelse
        </div>
    </div>
    {{-- Jadwal Shift Saya --}}
    @php
        $mySchedules = \App\Models\ShiftSchedule::where('user_id', auth()->id())
                        ->where('shift_date', '>=', today())
                        ->orderBy('shift_date')
                        ->orderBy('start_time')
                        ->take(7)
                        ->get();
    @endphp

    @if($mySchedules->count() > 0)
    <div class="bg-white rounded-3xl shadow-soft overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-display font-semibold text-gray-800">Jadwal Shift Saya</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($mySchedules as $schedule)
            <div class="flex items-center justify-between p-4 hover:bg-gray-50 smooth-transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl
                        {{ $schedule->shift === 'pagi' ? 'bg-yellow-50' : '' }}
                        {{ $schedule->shift === 'siang' ? 'bg-orange-50' : '' }}
                        {{ $schedule->shift === 'malam' ? 'bg-blue-50' : '' }}
                        flex items-center justify-center text-2xl">
                        {{ $schedule->shift === 'pagi' ? '🌅' : ($schedule->shift === 'siang' ? '☀️' : '🌙') }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ ucfirst($schedule->shift) }}
                            @if($schedule->shift_date->isToday())
                                <span class="ml-1 text-xs bg-elco-coffee text-white px-2 py-0.5 rounded-full">Hari Ini</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $schedule->shift_date->translatedFormat('l, d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    @if($schedule->is_active_now)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 animate-pulse block mb-1">
                        ● Sedang Berlangsung
                    </span>
                    @elseif($schedule->shift_date->isFuture() || ($schedule->shift_date->isToday() && now()->format('H:i:s') < $schedule->start_time))
                    <p class="text-xs text-gray-500 mb-1">Mulai dalam</p>
                    <p class="text-sm font-bold text-elco-coffee countdown-timer"
                    data-start="{{ $schedule->shift_date->format('Y-m-d') }} {{ $schedule->start_time }}"
                    data-end="{{ $schedule->shift_date->format('Y-m-d') }} {{ $schedule->end_time }}">
                        —
                    </p>
                    @else
                    <span class="text-xs text-gray-400">Selesai</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
@if($activeShift)
const shiftEndTimes = {
    'pagi':  '15:00:00',
    'siang': '22:00:00',
    'malam': '07:00:00', // hari berikutnya
};

const shiftType = '{{ $activeShift->shift }}';
const endTimeStr = shiftEndTimes[shiftType];
const [eh, em, es] = endTimeStr.split(':').map(Number);

function getShiftEnd() {
    const end = new Date();
    end.setHours(eh, em, es, 0);
    // Jika shift malam dan sudah lewat tengah malam
    if (shiftType === 'malam' && new Date().getHours() < 12) {
        // sudah pagi, end sudah benar
    } else if (shiftType === 'malam') {
        // masih malam, end = besok jam 7
        end.setDate(end.getDate() + 1);
    }
    return end;
}

function updateDuration() {
    const now  = new Date();
    const end  = getShiftEnd();
    const diff = Math.max(0, Math.floor((end - now) / 1000));

    if (diff <= 0) {
        document.getElementById('duration').textContent = 'Shift Selesai';
        return;
    }

    const hrs  = Math.floor(diff / 3600);
    const mins = Math.floor((diff % 3600) / 60);
    const secs = diff % 60;
    document.getElementById('duration').textContent =
        `${String(hrs).padStart(2,'0')}:${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
}
setInterval(updateDuration, 1000);
updateDuration();
@endif
</script>
@endpush