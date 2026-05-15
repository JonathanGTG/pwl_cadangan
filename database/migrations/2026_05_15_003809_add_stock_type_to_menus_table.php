<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // bahan_baku  = minuman (stok dihitung dari ingredient per transaksi)
            // kuantitas_jadi = makanan/snack (stok dihitung per pcs produk jadi)
            $table->enum('stock_type', ['bahan_baku', 'kuantitas_jadi'])
                  ->default('kuantitas_jadi')
                  ->after('category');
        });

        // Set default: minuman → bahan_baku, makanan/snack → kuantitas_jadi
        DB::table('menus')->where('category', 'minuman')
            ->update(['stock_type' => 'bahan_baku']);
        DB::table('menus')->whereIn('category', ['makanan', 'snack'])
            ->update(['stock_type' => 'kuantitas_jadi']);
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('stock_type');
        });
    }
};
