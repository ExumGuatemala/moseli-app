<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')
                ->constrained('orders_products')
                ->cascadeOnDelete();
            $table->foreignId('product_part_id')
                ->constrained('product_parts')
                ->cascadeOnDelete();
            $table->string('size', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_parts');
    }
};
