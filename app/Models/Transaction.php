<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number', 'branch_id', 'kasir_id',
        'promotion_id', 'subtotal', 'discount_amount',
        'total', 'payment_method', 'status',
        'cancel_reason', 'cancelled_by', 'cancelled_at'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'subtotal'     => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }
    public function kasir() {
        return $this->belongsTo(User::class, 'kasir_id');
    }
    public function items() {
        return $this->hasMany(TransactionItem::class);
    }
    public function promotion() {
        return $this->belongsTo(Promotion::class);
    }
    public function cancelledBy() {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopeInMonth($query, $month, $year) {
        return $query->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year);
    }

    // Badge warna status
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'completed'  => 'emerald',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }
}