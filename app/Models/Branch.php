<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'phone', 'status'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }
    public function branchStocks() {
        return $this->hasMany(BranchStock::class);
    }
    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
    public function expenses() {
        return $this->hasMany(Expense::class);
    }
    public function stockRequests() {
        return $this->hasMany(StockRequest::class);
    }
    public function promotions() {
        return $this->hasMany(Promotion::class);
    }
}