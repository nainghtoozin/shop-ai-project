<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'city_name')) {
                $table->string('city_name')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'delivery_type')) {
                $table->string('delivery_type')->nullable()->after('city_name');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
            if (Schema::hasColumn('orders', 'city_name')) {
                $table->dropColumn('city_name');
            }
        });
    }
};
