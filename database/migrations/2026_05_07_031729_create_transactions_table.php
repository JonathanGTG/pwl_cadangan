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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();     // INV-20240501-001
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('kasir_id')
                ->constrained('users');
            $table->foreignId('promotion_id')
                ->nullable()
                ->constrained();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('payment_method', [
                'cash',
                'transfer',
                'qris'
            ])->default('cash');
            $table->enum('status', [
                'pending',      // baru dibuat kasir
                'processing',   // sedang diolah — TIDAK BISA DIBATALKAN
                'completed',    // selesai
                'cancelled'     // dibatalkan admin cabang
            ])->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
