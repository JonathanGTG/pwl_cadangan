<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    protected $fillable = [
        'branch_id', 'requested_by', 'verified_by',
        'type', 'item_name', 'unit', 'quantity',
        'reason', 'status', 'rejection_note', 'verified_at',
        'delivery_status', 'delivery_note', 'delivery_photo',
        'delivered_at', 'delivered_by',
    ];

    protected $casts = [
        'verified_at'  => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function deliveredBy() {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function requestedBy() {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function verifiedBy() {
        return $this->belongsTo(User::class, 'verified_by');
    }
}