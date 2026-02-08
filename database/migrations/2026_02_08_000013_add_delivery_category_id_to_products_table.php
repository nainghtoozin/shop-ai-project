<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) return;

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'delivery_category_id')) {
                $table->foreignId('delivery_category_id')
                    ->nullable()
                    ->after('unit_id')
                    ->constrained('delivery_categories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) return;

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'delivery_category_id')) {
                $table->dropConstrainedForeignId('delivery_category_id');
            }
        });
    }
};
