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
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('active');
            $table->timestamps();
        });

        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable()->after('sale_price');
            $table->unsignedBigInteger('type_id')->nullable()->after('color_id');

            $table->foreign('color_id')->references('id')->on('product_colors');
            $table->foreign('type_id')->references('id')->on('product_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['type_id']);
            $table->dropColumn('color_id');
            $table->dropColumn('type_id');
        });
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_types');
    }
};
