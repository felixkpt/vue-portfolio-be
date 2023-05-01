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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug");
            $table->longText("content");
            $table->text("content_short")->nullable();
            $table->string("source_uri")->nullable();
            $table->boolean("comment_disabled")->default(0)->nullable();
            $table->unsignedTinyInteger("featured_image")->nullable();
            $table->string("status")->default('published')->nullable();
            $table->dateTime("display_time")->nullable();
            $table->unsignedTinyInteger("importance");
            $table->unsignedBigInteger("user_id");
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
        Schema::dropIfExists('posts');
    }
};
