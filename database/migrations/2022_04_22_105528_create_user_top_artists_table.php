<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTopArtistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_top_artists', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->string('artist_id');
            $table->integer('position');
            $table->primary(['artist_id', 'user_id']);

            $table->foreign('artist_id')->references('spotify_id')->on('artists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_top_artists');
    }
}
