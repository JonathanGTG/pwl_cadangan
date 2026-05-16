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
        Schema::table('stock_requests', function (Blueprint $table) {
            // Status baru: pending → approved → delivered → completed/rejected
            $table->enum('delivery_status', [
                'waiting',    // belum dikirim
                'delivered',  // admin konfirmasi sudah sampai
                'confirmed',  // manager final approve → stok bertambah
            ])->default('waiting')->after('status');

            $table->text('delivery_note')->nullable()->after('delivery_status');
            $table->string('delivery_photo')->nullable()->after('delivery_note');
            $table->timestamp('delivered_at')->nullable()->after('delivery_photo');
            $table->foreignId('delivered_by')->nullable()->constrained('users')->after('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropForeign(['delivered_by']);
            $table->dropColumn(['delivery_status', 'delivery_note', 'delivery_photo', 'delivered_at', 'delivered_by']);
        });
    }
};
