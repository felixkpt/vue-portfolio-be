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
        Schema::create('skills_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
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
        Schema::dropIfExists('skills_categories');
    }
};
