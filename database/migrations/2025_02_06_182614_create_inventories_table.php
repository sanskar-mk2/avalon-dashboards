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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('company_no')->nullable();
            $table->date('fiscal_period')->nullable();
            $table->string('location')->nullable();
            $table->string('item_no')->nullable();
            $table->decimal('qty_on_hand', 15, 2)->nullable();
            $table->decimal('average_cost', 15, 4)->nullable();
            $table->decimal('quantity_committed', 15, 2)->nullable();
            $table->decimal('quantity_open_order', 15, 2)->nullable();
            $table->decimal('quantity_backorder', 15, 2)->nullable();
            $table->string('board_material')->nullable();
            $table->string('board_thickness')->nullable();
            $table->string('laminate_material')->nullable();
            $table->string('laminate_color')->nullable();
            $table->date('as_of_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
