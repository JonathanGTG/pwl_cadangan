<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'branch_id', 'created_by', 'name', 'description',
        'type', 'discount_type', 'discount_value',
        'min_purchase', 'start_date', 'end_date', 'is_active',
        'review_status', 'review_note', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'discount_value' => 'decimal:2',
        'min_purchase'   => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Cek apakah promo masih aktif & belum kadaluarsa
    public function getIsValidAttribute(): bool
    {
        return $this->is_active
            && Carbon::today()->between($this->start_date, $this->end_date);
    }

    // Hitung nilai diskon dari subtotal
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->is_valid) return 0;
        if ($subtotal < $this->min_purchase) return 0;

        if ($this->discount_type === 'percentage') {
            return round($subtotal * ($this->discount_value / 100), 2);
        }

        return min($this->discount_value, $subtotal);
    }

    // Label diskon
    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percentage'
            ? $this->discount_value . '%'
            : 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }
}