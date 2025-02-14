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
        Schema::table('sales', function (Blueprint $table) {
            $table->date('uploaded_for_month')->nullable()->after('period');
        });

        Schema::table('open_orders', function (Blueprint $table) {
            $table->date('uploaded_for_month')->nullable()->after('period');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->date('uploaded_for_month')->nullable()->after('fiscal_period');
        });

        Schema::table('account_receivables', function (Blueprint $table) {
            $table->date('uploaded_for_month')->nullable()->after('fiscal_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('uploaded_for_month');
        });

        Schema::table('open_orders', function (Blueprint $table) {
            $table->dropColumn('uploaded_for_month');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('uploaded_for_month');
        });

        Schema::table('account_receivables', function (Blueprint $table) {
            $table->dropColumn('uploaded_for_month');
        });
    }
};
