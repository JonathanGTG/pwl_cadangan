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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('menu_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('menu_name');                    // snapshot nama menu
            $table->decimal('price', 10, 2);                // snapshot harga saat transaksi
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2);             // price × quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
