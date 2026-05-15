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
        $now = now()->format('H:i:s');
        return today()->equalTo($this->shift_date)
            && $now >= $this->start_time
            && $now <= $this->end_time;
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