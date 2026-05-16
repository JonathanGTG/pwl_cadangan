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
                                  ->whereDate('shift_date', today())
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
                               ->whereDate('shift_date', today())
                               ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki shift aktif hari ini!');
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
}