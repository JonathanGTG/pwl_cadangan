<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id', 'created_by', 'verified_by',
        'title', 'description', 'category',
        'amount', 'expense_date', 'status', 'receipt'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy() {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'operasional' => '⚡ Operasional',
            'bahan_baku'  => '☕ Bahan Baku',
            'peralatan'   => '🔧 Peralatan',
            'gaji'        => '👤 Gaji',
            default       => '📋 Lainnya',
        };
    }
}