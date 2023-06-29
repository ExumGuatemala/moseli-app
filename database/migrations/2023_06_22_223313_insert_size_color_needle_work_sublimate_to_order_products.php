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
        Schema::table('orders_products', function (Blueprint $table) {
            $table->json('colors')->nullable();
            $table->string('size')->nullable();
            $table->string('has_embroidery')->nullable();
            $table->string('embroidery')->nullable();
            $table->string('has_sublimate')->nullable();
            $table->string('sublimate')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('order_products', function (Blueprint $table) {
        //     //
        // });
    }
};
