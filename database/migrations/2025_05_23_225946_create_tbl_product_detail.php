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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string('fabric_type')->nullable();
            $table->string('fabric_code')->nullable();
            $table->string('lining_color')->nullable();
            $table->string('lining_type')->nullable();
            $table->string('pocket_type')->nullable();
            $table->string('pocket_quantity')->nullable();
            $table->string('sleeve_type')->nullable();
            $table->string('hood_type')->nullable();
            $table->string('neckline_type')->nullable();
            $table->string('elastic_waist')->nullable();
            $table->string('buttons_neckline')->nullable();
            $table->string('ziper_position')->nullable();
            $table->string('ziper_color')->nullable();
            $table->string('resort_color')->nullable();
            $table->string('elastic')->nullable();
            $table->string('special_stitching')->nullable();
            $table->string('rivets')->nullable();
            $table->string('buttons_color')->nullable();
            $table->string('strap_color')->nullable();
            $table->string('thread_color')->nullable();
            $table->string('reflective_color')->nullable();
            $table->string('reflective_position')->nullable();
            $table->string('reflective_width')->nullable();
            $table->string('reflective_velcro')->nullable();
            $table->string('collar_cuff')->nullable();
            $table->string('general_observations')->nullable();
            $table->string('sewing_observations')->nullable();
            $table->string('personalization_type')->nullable();
            $table->string('personalization_size')->nullable();
            $table->json('logos')->nullable();
            $table->string('fabric_background_color')->nullable();
            $table->string('monogram')->nullable();
            $table->string('personalization_observations')->nullable();

            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('institution_id')->references('id')->on('institutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_detail');
    }
};
