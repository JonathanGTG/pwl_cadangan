<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->foreignId('ingredient_id')
                ->nullable()
                ->after('stock_item_type')
                ->constrained('ingredients')
                ->nullOnDelete();

            $table->foreignId('menu_id')
                ->nullable()
                ->after('ingredient_id')
                ->constrained('menus')
                ->nullOnDelete();
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->json('recipe_snapshot')->nullable()->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn('recipe_snapshot');
        });

        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropForeign(['menu_id']);
            $table->dropColumn(['ingredient_id', 'menu_id']);
        });
    }
};
