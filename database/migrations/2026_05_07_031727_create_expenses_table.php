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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('created_by')
                ->constrained('users');
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'operasional',  // listrik, air, internet
                'bahan_baku',   // pembelian bahan
                'peralatan',    // beli/servis alat
                'gaji',
                'lainnya'
            ]);
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('status', [
                'pending',
                'verified',
                'rejected'
            ])->default('pending');
            $table->string('receipt')->nullable();          // foto bukti pengeluaran
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
