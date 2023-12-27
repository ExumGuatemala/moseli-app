<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });

        Schema::create('product_features', function (Blueprint $table) {
            $table->id();
            //agregar referencia a producto
            $table->string("name");
            $table->string("measure");
            $table->unsignedBigInteger('size_id');
            $table->timestamps();

            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('products_sizes', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('size_id');
            $table->decimal('price', 9, 2);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::update("INSERT INTO sizes VALUES (1,'2');");
        DB::update("INSERT INTO sizes VALUES (2,'4');");
        DB::update("INSERT INTO sizes VALUES (3,'6');");
        DB::update("INSERT INTO sizes VALUES (4,'8');");
        DB::update("INSERT INTO sizes VALUES (5,'10');");
        DB::update("INSERT INTO sizes VALUES (6,'12');");
        DB::update("INSERT INTO sizes VALUES (7,'14');");
        DB::update("INSERT INTO sizes VALUES (8,'XS');");
        DB::update("INSERT INTO sizes VALUES (9,'S');");
        DB::update("INSERT INTO sizes VALUES (10,'M');");
        DB::update("INSERT INTO sizes VALUES (11,'L');");
        DB::update("INSERT INTO sizes VALUES (12,'XL');");
        DB::update("INSERT INTO sizes VALUES (13,'XXL');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_sizes');
        Schema::dropIfExists('product_features');
        Schema::dropIfExists('sizes');
    }
};
