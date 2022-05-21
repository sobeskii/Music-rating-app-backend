<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModerationRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moderation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(false);
            $table->string('strategy');
            $table->string('code');
            $table->string('reason');
            $table->integer('detect_after_count')->nullable();
            $table->integer('mute_minutes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moderation_rules');
    }
}
