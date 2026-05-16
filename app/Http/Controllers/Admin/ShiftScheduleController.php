<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $date     = $request->get('date', today()->format('Y-m-d'));

        $kasirs = User::where('branch_id', $branchId)
                      ->where('role', 'kasir')
                      ->get();

        $schedules = ShiftSchedule::where('branch_id', $branchId)
                                   ->whereDate('shift_date', $date)
                                   ->with('user')
                                   ->orderBy('start_time')
                                   ->get();

        // Jadwal minggu ini
        $weekSchedules = ShiftSchedule::where('branch_id', $branchId)
                                       ->whereBetween('shift_date', [today(), today()->addDays(6)])
                                       ->with('user')
                                       ->orderBy('shift_date')
                                       ->orderBy('start_time')
                                       ->get()
                                       ->groupBy(fn($s) => $s->shift_date->format('Y-m-d'));

        return view('admin.shifts.index', compact('kasirs', 'schedules', 'date', 'weekSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'shift'      => 'required|in:pagi,siang,malam',
            'shift_date' => 'required|date|after_or_equal:today',
            'note'       => 'nullable|string',
        ]);

        $times = match($request->shift) {
            'pagi'  => ['07:00:00', '15:00:00'],
            'siang' => ['15:00:00', '22:00:00'],
            'malam' => ['22:00:00', '07:00:00'],
        };

        ShiftSchedule::updateOrCreate(
            [
                'user_id'    => $request->user_id,
                'shift_date' => $request->shift_date,
                'shift'      => $request->shift,
            ],
            [
                'branch_id'  => auth()->user()->branch_id,
                'created_by' => auth()->id(),
                'start_time' => $times[0],
                'end_time'   => $times[1],
                'note'       => $request->note,
            ]
        );

        return back()->with('success', 'Jadwal shift berhasil disimpan!');
    }

    public function destroy(ShiftSchedule $shiftSchedule)
    {
        $shiftSchedule->delete();
        return back()->with('success', 'Jadwal shift dihapus!');
    }
}