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
        Schema::create('order_states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 9, 2);
            $table->decimal('balance', 9, 2)->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('order_states');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::create('orders_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('orders_products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_states');
    }
};
