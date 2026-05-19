@extends('layouts.kasir')

@section('content')
@if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
        <i class="ph-fill ph-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
        <i class="ph-fill ph-x-circle text-xl"></i>
        {{ session('error') }}
    </div>
@endif

<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h2 class="font-display text-xl font-bold text-gray-800">Shift Kasir</h2>
        <p class="mt-1 text-sm text-gray-500">{{ now()->translatedFormat('l, j F Y') }}</p>
    </div>

    {{-- Status Shift Aktif --}}
    @if($activeShift)
        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="mb-6 flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-3xl text-emerald-500">
                    <i class="ph-fill ph-clock"></i>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Shift Aktif</p>
                    <h3 class="font-display text-xl font-bold text-gray-800">{{ $activeShift->shift_label }}</h3>
                </div>

                <span class="ml-auto rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 animate-pulse">
                    ● AKTIF
                </span>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="mb-1 text-xs text-gray-500">Clock In</p>
                    <p class="text-lg font-bold text-gray-800">{{ $activeShift->clock_in }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="mb-1 text-xs text-gray-500">Sisa Waktu Shift</p>
                    <p class="text-lg font-bold text-elco-coffee" id="duration">—</p>
                </div>
            </div>

            <form id="clockOutForm" action="{{ route('kasir.shifts.clock-out', $activeShift) }}" method="POST">
                @csrf
                <button
                    type="button"
                    onclick="elcoConfirm({
                        title: 'Selesaikan Shift?',
                        text: 'Pastikan semua transaksi sudah diselesaikan.',
                        confirmText: 'Ya, Selesai',
                        confirmColor: '#5C3D2E',
                        icon: 'question',
                        onConfirm: () => document.getElementById('clockOutForm').submit()
                    })"
                    class="w-full rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha py-3 text-sm font-semibold text-white shadow-md smooth-transition hover:shadow-hover active:scale-95"
                >
                    <i class="ph ph-sign-out mr-2"></i>
                    Selesaikan Shift
                </button>
            </form>
        </div>
    @else
        {{-- Form Mulai Shift --}}
        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <h3 class="mb-5 font-display font-semibold text-gray-800">Mulai Shift Hari Ini</h3>

            <form action="{{ route('kasir.shifts.clock-in') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Pilih Shift</label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        @foreach(['pagi' => ['ph-sun-horizon', 'Pagi', '07.00 - 15.00'], 'siang' => ['ph-sun', 'Siang', '15.00 - 22.00'], 'malam' => ['ph-moon', 'Malam', '22.00 - 07.00']] as $val => $info)
                            <label class="cursor-pointer">
                                <input type="radio" name="shift" value="{{ $val }}" class="sr-only peer">
                                <div class="rounded-2xl border-2 border-gray-200 p-4 text-center smooth-transition peer-checked:border-elco-coffee peer-checked:bg-elco-cream">
                                    <i class="ph {{ $info[0] }} mb-1 block text-2xl text-elco-coffee"></i>
                                    <p class="text-sm font-semibold text-gray-700">{{ $info[1] }}</p>
                                    <p class="mt-0.5 text-xs text-gray-400">{{ $info[2] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('shift')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-gradient-to-r from-elco-coffee to-elco-mocha py-3 text-sm font-semibold text-white shadow-md smooth-transition hover:shadow-hover active:scale-95"
                >
                    <i class="ph ph-play-circle mr-2"></i>
                    Mulai Shift
                </button>
            </form>
        </div>
    @endif

    {{-- Riwayat Shift --}}
    <div class="overflow-hidden rounded-3xl bg-white shadow-soft">
        <div class="border-b border-gray-100 p-5">
            <h3 class="font-display font-semibold text-gray-800">Riwayat Shift</h3>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($shiftHistory as $shift)
                <div class="flex items-center justify-between gap-4 p-4 hover:bg-gray-50 smooth-transition">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl {{ $shift->status === 'active' ? 'bg-emerald-50 text-emerald-500' : 'bg-gray-100 text-gray-400' }}">
                            <i class="ph-fill ph-clock"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-800">{{ $shift->shift_label }}</p>
                            <p class="text-xs text-gray-500">{{ $shift->shift_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="flex-shrink-0 text-right">
                        <p class="text-xs text-gray-500">{{ $shift->clock_in }} — {{ $shift->clock_out ?? 'Aktif' }}</p>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $shift->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $shift->status === 'active' ? 'Aktif' : 'Selesai' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-gray-400">Belum ada riwayat shift</div>
            @endforelse
        </div>
    </div>

    {{-- Jadwal Shift Saya --}}
    @if($mySchedules->count() > 0)
        <div class="overflow-hidden rounded-3xl bg-white shadow-soft">
            <div class="border-b border-gray-100 p-5">
                <h3 class="font-display font-semibold text-gray-800">Jadwal Shift Saya</h3>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach($mySchedules as $schedule)
                    <div class="flex items-center justify-between gap-4 p-4 hover:bg-gray-50 smooth-transition">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-2xl
                                {{ $schedule->shift === 'pagi' ? 'bg-yellow-50' : '' }}
                                {{ $schedule->shift === 'siang' ? 'bg-orange-50' : '' }}
                                {{ $schedule->shift === 'malam' ? 'bg-blue-50' : '' }}">
                                <i class="ph {{ $schedule->shift === 'pagi' ? 'ph-sunrise' : ($schedule->shift === 'siang' ? 'ph-sun' : 'ph-moon') }} text-elco-coffee"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ ucfirst($schedule->shift) }}
                                    @if($schedule->shift_date->isToday())
                                        <span class="ml-1 rounded-full bg-elco-coffee px-2 py-0.5 text-xs text-white">Hari Ini</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $schedule->shift_date->translatedFormat('l, d M Y') }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 text-right">
                            @if($schedule->is_active_now)
                                <span class="mb-1 block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 animate-pulse">
                                    ● Sedang Berlangsung
                                </span>
                            @elseif($schedule->shift_date->isFuture() || ($schedule->shift_date->isToday() && now()->format('H:i:s') < $schedule->start_time))
                                <p class="mb-1 text-xs text-gray-500">Mulai dalam</p>
                                <p
                                    class="countdown-timer text-sm font-bold text-elco-coffee"
                                    data-start="{{ $schedule->shift_date->format('Y-m-d') }} {{ $schedule->start_time }}"
                                    data-end="{{ $schedule->shift_date->format('Y-m-d') }} {{ $schedule->end_time }}"
                                >
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
    pagi:  '15:00:00',
    siang: '22:00:00',
    malam: '07:00:00',
};

const shiftType  = @js($activeShift->shift);
const endTimeStr = shiftEndTimes[shiftType];
const [eh, em, es] = endTimeStr.split(':').map(Number);

function getShiftEnd() {
    const end = new Date();
    end.setHours(eh, em, es, 0);

    if (shiftType === 'malam' && new Date().getHours() >= 12) {
        end.setDate(end.getDate() + 1);
    }

    return end;
}

function updateDuration() {
    const durationEl = document.getElementById('duration');
    if (!durationEl) return;

    const now  = new Date();
    const end  = getShiftEnd();
    const diff = Math.max(0, Math.floor((end - now) / 1000));

    if (diff <= 0) {
        durationEl.textContent = 'Shift Selesai';
        return;
    }

    const hrs  = Math.floor(diff / 3600);
    const mins = Math.floor((diff % 3600) / 60);
    const secs = diff % 60;

    durationEl.textContent = `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}

setInterval(updateDuration, 1000);
updateDuration();
@endif

function updateScheduleCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const start = new Date(timer.dataset.start.replace(' ', 'T'));
        const now   = new Date();
        const diff  = Math.max(0, Math.floor((start - now) / 1000));

        if (diff <= 0) {
            timer.textContent = 'Mulai sekarang';
            return;
        }

        const days = Math.floor(diff / 86400);
        const hrs  = Math.floor((diff % 86400) / 3600);
        const mins = Math.floor((diff % 3600) / 60);

        timer.textContent = days > 0
            ? `${days} hari ${hrs} jam`
            : `${String(hrs).padStart(2, '0')}j ${String(mins).padStart(2, '0')}m`;
    });
}

setInterval(updateScheduleCountdowns, 60000);
updateScheduleCountdowns();
</script>
@endpush
