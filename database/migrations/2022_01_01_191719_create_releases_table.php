<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->string('spotify_id');
            $table->string('name');
            $table->string('album_type');
            $table->string('release_date');
            $table->string('label');
            $table->string('artist_id');
            $table->string('release_image');

            $table->foreign('artist_id')->references('spotify_id')->on('artists')->onDelete('cascade');
            $table->primary('spotify_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('releases');
    }
}
