<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirShift extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'shift',
        'shift_date', 'clock_in', 'clock_out', 'status'
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

    public function getShiftLabelAttribute(): string
    {
        return match($this->shift) {
            'pagi'  => '🌅 Pagi (07.00 - 15.00)',
            'siang' => '☀️ Siang (15.00 - 22.00)',
            'malam' => '🌙 Malam (22.00 - 07.00)',
        };
    }
}