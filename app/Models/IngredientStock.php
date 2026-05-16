<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientStock extends Model
{
    protected $fillable = [
        'branch_id', 'ingredient_id', 'stok_sekarang', 'stok_minimum',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    /** True jika stok di bawah minimum */
    public function isLow(): bool
    {
        return $this->stok_sekarang <= $this->stok_minimum;
    }
}