<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // "Kopi Arabika"
            $table->text('description')->nullable();
            $table->enum('category', [
                'minuman',
                'makanan',
                'snack'
            ]);
            $table->decimal('base_price', 10, 2);           // harga dasar dari pusat
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('menus', function (Blueprint $table) {
            // bahan_baku  = minuman (stok dihitung dari ingredient per transaksi)
            // kuantitas_jadi = makanan/snack (stok dihitung per pcs produk jadi)
            $table->enum('stock_type', ['bahan_baku', 'kuantitas_jadi'])
                  ->default('kuantitas_jadi')
                  ->after('category');
        });
        DB::table('menus')->where('category', 'minuman')
            ->update(['stock_type' => 'bahan_baku']);
        DB::table('menus')->whereIn('category', ['makanan', 'snack'])
            ->update(['stock_type' => 'kuantitas_jadi']);
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('stock_type');
        });
    }
};
