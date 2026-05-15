<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master bahan baku (untuk minuman)
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan')->unique();         // BHN-001, BHN-002, dst
            $table->string('nama_bahan');
            $table->string('kategori')->nullable();         // kopi, susu, sirup, dst
            $table->enum('satuan', ['gram', 'ml', 'pcs']); // satuan pengukuran
            $table->timestamps();
        });

        // Stok bahan baku per cabang
        Schema::create('ingredient_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('stok_sekarang', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'ingredient_id']);
        });

        // Resep menu minuman (bahan-bahan per sajian)
        Schema::create('menu_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('jumlah_per_sajian', 10, 3);   // misal 18.000 gram
            $table->timestamps();

            $table->unique(['menu_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_ingredients');
        Schema::dropIfExists('ingredient_stocks');
        Schema::dropIfExists('ingredients');
    }
};