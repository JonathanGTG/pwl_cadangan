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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('requested_by')
                ->constrained('users');
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users');
            $table->enum('type', [
                'stock',        // pengajuan bahan/stok
                'operational'   // pengajuan alat operasional
            ]);
            $table->string('item_name');
            $table->string('unit')->nullable();             // kg, pcs, liter, dll
            $table->decimal('quantity', 10, 2);
            $table->text('reason')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');
            $table->text('rejection_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
