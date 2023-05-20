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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug");
            $table->longText("content");
            $table->text("content_short")->nullable();
            $table->string("source_uri")->nullable();
            $table->boolean("comment_disabled")->default(0)->nullable();
            $table->mediumText("featured_image")->nullable();
            $table->string("status")->default('published')->nullable();
            $table->dateTime("display_time")->nullable();
            $table->unsignedTinyInteger("importance")->default(0);
            $table->unsignedBigInteger("user_id");
            $table->string("project_url")->nullable();
            $table->string("github_url")->nullable();
            $table->unsignedBigInteger("company_id");
            $table->string("start_date");
            $table->string("end_date")->nullable();
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
        Schema::dropIfExists('projects');
    }
};
