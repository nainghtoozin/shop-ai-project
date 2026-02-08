<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_types')) return;

        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('charge_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('extra_charge', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_types');
    }
};
