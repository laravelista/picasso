<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePIcassoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picasso', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('original_image');
            $table->string('dimension_name');
            // optimized images can only have unique filenames
            $table->string('optimized_image')->unique();

            // cannot have two optimized images with
            // same original image and same dimension name
            $table->unique(['original_image', 'dimension_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picasso');
    }
}
