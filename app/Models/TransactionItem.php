<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'menu_id',
        'menu_name', 'price', 'quantity', 'subtotal', 'recipe_snapshot'
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
        'recipe_snapshot' => 'array',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function menu() {
        return $this->belongsTo(Menu::class);
    }
}
