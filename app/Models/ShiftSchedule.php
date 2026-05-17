<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'created_by',
        'shift', 'shift_date', 'start_time', 'end_time', 'note'
    ];

    protected $casts = [
        'shift_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    // Cek apakah shift ini yang sedang aktif sekarang
    public function getIsActiveNowAttribute(): bool
    {
        $start = $this->shift_date->copy()->setTimeFromTimeString($this->start_time);
        $end   = $this->shift_date->copy()->setTimeFromTimeString($this->end_time);

        if ($this->end_time <= $this->start_time) {
            $end->addDay();
        }

        return now()->betweenIncluded($start, $end);
    }

    // Countdown sampai shift mulai
    public function getCountdownAttribute(): string
    {
        $startDateTime = $this->shift_date->setTimeFromTimeString($this->start_time);
        if (now()->greaterThan($startDateTime)) return 'Sedang berlangsung';
        $diff = now()->diff($startDateTime);
        return sprintf('%02d:%02d:%02d', $diff->h + ($diff->days * 24), $diff->i, $diff->s);
    }
}
