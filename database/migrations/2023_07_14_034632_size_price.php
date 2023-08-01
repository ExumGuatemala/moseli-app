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
        Schema::create('size_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 9, 2);
            $table->unsignedBigInteger('product_type_id');
            $table->timestamps();

            $table->foreign('product_type_id')->references('id')->on('product_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('size_prices', function (Blueprint $table) {
        //     $table->dropColumn('product_type_id');
        // });
        Schema::dropIfExists('size_prices');
    }
};
