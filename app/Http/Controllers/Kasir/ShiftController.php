<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\KasirShift;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';

    public function index()
    {
        $userId = auth()->id();
        $today  = $this->localToday();

        $activeShift = KasirShift::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        $shiftHistory = KasirShift::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $mySchedules = ShiftSchedule::where('user_id', $userId)
            ->where('shift_date', '>=', $today->copy()->subDay()->toDateString())
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->take(7)
            ->get();

        return view('kasir.shifts.index', compact(
            'activeShift',
            'shiftHistory',
            'mySchedules'
        ));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:pagi,siang,malam',
        ]);

        $existing = KasirShift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki shift aktif!');
        }

        $range = $this->shiftRanges()[$request->shift];

        if (! $this->isWithinShiftTime($request->shift)) {
            return back()->with('error', "Sekarang belum masuk jam shift {$request->shift} ({$range['start']} - {$range['end']}).");
        }

        $schedule = $this->resolveScheduleForClockIn($request->shift);

        if ($schedule && ! $this->isWithinScheduleTime($schedule)) {
            return back()->with('error', "Shift {$request->shift} hanya bisa dimulai pada jam {$schedule->start_time} - {$schedule->end_time}.");
        }

        KasirShift::create([
            'user_id'    => auth()->id(),
            'branch_id'  => auth()->user()->branch_id,
            'shift'      => $request->shift,
            'shift_date' => $schedule?->shift_date ?? $this->shiftDateForClockIn($request->shift),
            'clock_in'   => $this->localNow()->format('H:i:s'),
            'status'     => 'active',
        ]);

        return back()->with('success', 'Shift dimulai! Selamat bekerja');
    }

    public function clockOut(KasirShift $shift)
    {
        if ($shift->user_id !== auth()->id()) {
            abort(403);
        }

        $shift->update([
            'clock_out' => $this->localNow()->format('H:i:s'),
            'status'    => 'finished',
        ]);

        return back()->with('success', 'Shift selesai! Terima kasih atas kerja kerasmu');
    }

    private function resolveScheduleForClockIn(string $shift): ?ShiftSchedule
    {
        $today = $this->localToday();

        $candidates = ShiftSchedule::where('user_id', auth()->id())
            ->where('shift', $shift)
            ->whereBetween('shift_date', [
                $today->copy()->subDay()->toDateString(),
                $today->toDateString(),
            ])
            ->orderByDesc('shift_date')
            ->get();

        foreach ($candidates as $schedule) {
            if ($this->isWithinScheduleTime($schedule)) {
                return $schedule;
            }
        }

        return null;
    }

    private function isWithinScheduleTime(ShiftSchedule $schedule): bool
    {
        $date = $schedule->shift_date->format('Y-m-d');

        $start = Carbon::parse($date . ' ' . $schedule->start_time, self::TIMEZONE);
        $end   = Carbon::parse($date . ' ' . $schedule->end_time, self::TIMEZONE);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return $this->localNow()->betweenIncluded($start, $end);
    }

    private function isWithinShiftTime(string $shift): bool
    {
        $range = $this->shiftRanges();
        $shiftRange = $range[$shift];

        $now = $this->localNow()->format('H:i');

        if ($shiftRange['end'] < $shiftRange['start']) {
            return $now >= $shiftRange['start'] || $now < $shiftRange['end'];
        }

        return $now >= $shiftRange['start'] && $now < $shiftRange['end'];
    }

    private function shiftDateForClockIn(string $shift): string
    {
        $now = $this->localNow();

        if ($shift === 'malam' && $now->format('H:i') < '07:00') {
            return $now->copy()->subDay()->toDateString();
        }

        return $now->toDateString();
    }

    private function localNow(): Carbon
    {
        return Carbon::now(self::TIMEZONE);
    }

    private function localToday(): Carbon
    {
        return $this->localNow()->startOfDay();
    }

    private function shiftRanges(): array
    {
        return [
            'pagi'  => ['start' => '07:00', 'end' => '15:00'],
            'siang' => ['start' => '15:00', 'end' => '22:00'],
            'malam' => ['start' => '22:00', 'end' => '07:00'],
        ];
    }
}