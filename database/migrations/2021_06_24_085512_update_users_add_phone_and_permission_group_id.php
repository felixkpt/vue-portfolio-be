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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->integer('permission_group_id')->nullable();
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->string('phone')->nullable();
            $table->string('avatar')->default('images/users/default.png');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('permission_group_id');
            $table->dropColumn('role');
            $table->dropColumn('phone');
            $table->dropColumn('avatar');
        });
    }
};
