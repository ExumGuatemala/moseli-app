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
        Schema::create('sizes_by_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')
                ->constrained('product_types')
                ->cascadeOnDelete();

            $table->string('name'); // XS, S, M...
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Evita duplicados de talla dentro del mismo tipo
            $table->unique(['product_type_id', 'name']);
            // Para que ordenar por sort sea rápido
            $table->index(['product_type_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sizes_by_product');
    }
};
