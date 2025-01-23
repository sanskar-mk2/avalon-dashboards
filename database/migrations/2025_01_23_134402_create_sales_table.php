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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('company')->nullable();
            $table->string('location')->nullable();
            $table->integer('order_no')->nullable();
            $table->boolean('backorder')->nullable();
            $table->date('order_date')->nullable();
            $table->string('order_type')->nullable();
            $table->string('customer_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_class')->nullable();
            $table->string('brand')->nullable();
            $table->string('flag')->nullable();
            $table->integer('salesperson')->nullable();
            $table->integer('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('item_no')->nullable();
            $table->string('item_desc')->nullable();
            $table->integer('item_division')->nullable();
            $table->integer('inv_class')->nullable();
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('ext_sales', 12, 2)->nullable();
            $table->decimal('ext_cost', 12, 2)->nullable();
            $table->integer('period')->nullable();
            $table->string('order_status')->nullable();
            $table->string('advertising_source')->nullable();
            $table->decimal('finance_co_rate', 8, 2)->nullable();
            $table->string('price_matrix')->nullable();
            $table->string('price_list_applied')->nullable();
            $table->decimal('price_after_disc', 12, 4)->nullable();
            $table->string('ship_to_no')->nullable();
            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_city')->nullable();
            $table->string('ship_to_state', 2)->nullable();
            $table->date('requested_ship_date')->nullable();
            $table->date('customer_desire_date')->nullable();
            $table->string('mfg_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
