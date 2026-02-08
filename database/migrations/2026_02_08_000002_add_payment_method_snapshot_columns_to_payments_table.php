<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method_name')) {
                $table->string('payment_method_name')->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_method_name');
            }
            if (!Schema::hasColumn('payments', 'account_number')) {
                $table->string('account_number')->nullable()->after('payment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'account_number')) {
                $table->dropColumn('account_number');
            }
            if (Schema::hasColumn('payments', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('payments', 'payment_method_name')) {
                $table->dropColumn('payment_method_name');
            }
        });
    }
};
