<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    protected $fillable = [
        'branch_id', 'menu_id', 'stock', 'custom_price'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function menu() {
        return $this->belongsTo(Menu::class);
    }

    // Harga efektif: custom_price jika ada, fallback ke base_price menu
    public function getEffectivePriceAttribute(): float
    {
        return $this->custom_price ?? $this->menu->base_price;
    }
}