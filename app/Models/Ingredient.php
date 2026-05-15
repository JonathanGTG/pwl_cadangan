<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'kode_bahan', 'nama_bahan', 'kategori', 'satuan',
    ];

    /** Relasi ke resep menu yang menggunakan bahan ini */
    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class);
    }

    /** Relasi ke stok per cabang */
    public function stocks()
    {
        return $this->hasMany(IngredientStock::class);
    }
}