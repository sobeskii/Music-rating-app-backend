<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlaggedReviewReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flagged_review_reasons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('rating_id');
            $table->unsignedBigInteger('moderation_rule_id');
            $table->string('flagged_part')->nullable();

            $table->foreign('moderation_rule_id')->references('id')->on('moderation_rules')->onDelete('cascade');
            $table->foreign('rating_id')->references('id')->on('user_ratings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flagged_review_reasons');
    }
}
