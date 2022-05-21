<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('spotify_id')->unique();
            $table->string('spotify_token');
            $table->string('spotify_refresh_token');
            $table->string('spotify_avatar')->nullable();
            $table->boolean('muted')->default(false);
            $table->dateTime('muted_until')->nullable();
            $table->dateTime('spotify_token_expiresin');
            $table->string('mute_reason')->nullable();
            $table->unsignedBigInteger('role_id')->default(1);
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
