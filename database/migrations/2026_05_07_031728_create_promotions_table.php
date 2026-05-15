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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                ->nullable()            // null = promo global
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('created_by')
                ->constrained('users');
            $table->string('name');                         // "Promo Ramadan"
            $table->text('description')->nullable();
            $table->enum('type', [
                'global',   // berlaku semua cabang
                'branch'    // hanya cabang tertentu
            ]);
            $table->enum('discount_type', [
                'percentage',   // diskon %, misal 20%
                'fixed'         // diskon nominal, misal Rp5.000
            ]);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
