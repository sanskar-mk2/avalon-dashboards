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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->integer('company_no')->nullable();
            $table->date('fiscal_period')->nullable();
            $table->string('customer_no')->nullable();
            $table->decimal('balance_due_amount', 15, 2)->nullable();
            $table->decimal('balance_age_1', 15, 2)->nullable();
            $table->decimal('balance_age_2', 15, 2)->nullable();
            $table->decimal('balance_age_3', 15, 2)->nullable();
            $table->decimal('balance_age_4', 15, 2)->nullable();
            $table->decimal('balance_age_5', 15, 2)->nullable();
            $table->decimal('balance_age_6', 15, 2)->nullable();
            $table->string('credit_manager')->nullable();
            $table->string('location')->nullable();
            $table->date('as_of_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
