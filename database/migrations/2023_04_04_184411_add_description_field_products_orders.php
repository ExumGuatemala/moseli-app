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
        Schema::table('products', function (Blueprint $table) {
            $table->string('description')->nullable();
            // $table->string('description')->nullable()->after('embroidery');
        });

        Schema::table('orders', function (Blueprint $table) {
            // $table->string('description')->nullable()->after('total');
            $table->string('description')->nullable()->after('total');
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
            $table->dropColumn('description');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
