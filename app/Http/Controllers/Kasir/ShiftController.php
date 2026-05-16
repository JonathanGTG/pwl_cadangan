<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\KasirShift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $branchId = auth()->user()->branch_id;

        $activeShift = KasirShift::where('user_id', $userId)
                                  ->where('status', 'active')
                                  ->first();

        $shiftHistory = KasirShift::where('user_id', $userId)
                                   ->latest()
                                   ->take(10)
                                   ->get();

        return view('kasir.shifts.index', compact('activeShift', 'shiftHistory'));
    }

    // Mulai shift
    public function clockIn(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:pagi,siang,malam',
        ]);

        $existing = KasirShift::where('user_id', auth()->id())
                               ->where('status', 'active')
                               ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki shift aktif hari ini!');
        }

        if (! $this->isWithinShiftTime($request->shift)) {
            $range = $this->shiftRanges()[$request->shift];

            return back()->with(
                'error',
                "Shift {$request->shift} hanya bisa dimulai pada jam {$range['start']} - {$range['end']}."
            );
        }

        KasirShift::create([
            'user_id'    => auth()->id(),
            'branch_id'  => auth()->user()->branch_id,
            'shift'      => $request->shift,
            'shift_date' => today(),
            'clock_in'   => now()->format('H:i:s'),
            'status'     => 'active',
        ]);

        return back()->with('success', 'Shift dimulai! Selamat bekerja 💪');
    }

    // Selesai shift
    public function clockOut(KasirShift $shift)
    {
        if ($shift->user_id !== auth()->id()) abort(403);

        $shift->update([
            'clock_out' => now()->format('H:i:s'),
            'status'    => 'finished',
        ]);

        return back()->with('success', 'Shift selesai! Terima kasih atas kerja kerasmu ☕');
    }

    private function isWithinShiftTime(string $shift): bool
    {
        $range = $this->shiftRanges()[$shift];
        $now = now()->format('H:i');

        if ($range['end'] < $range['start']) {
            return $now >= $range['start'] || $now < $range['end'];
        }

        return $now >= $range['start'] && $now < $range['end'];
    }

    private function shiftRanges(): array
    {
        return [
            'pagi' => ['start' => '07:00', 'end' => '15:00'],
            'siang' => ['start' => '15:00', 'end' => '22:00'],
            'malam' => ['start' => '22:00', 'end' => '07:00'],
        ];
    }
}
