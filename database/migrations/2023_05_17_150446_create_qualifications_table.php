<?php

use Illuminate\Database\Migrations\Migration;
use Jenssegers\Mongodb\Schema\Blueprint;
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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('institution');
            $table->string('course');
            $table->string('qualification');
            $table->string('start_date');
            $table->string('end_date');
            $table->mediumText('featured_image')->nullable();
            $table->unsignedTinyInteger("importance")->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger("status")->default(1);
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
        Schema::dropIfExists('qualifications');
    }
};
