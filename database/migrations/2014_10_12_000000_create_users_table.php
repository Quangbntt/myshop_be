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
            $table->increments('id');
            $table->string('uuid');
            $table->string('username');
            $table->string('password');
            $table->integer('groupid');
            $table->string('name');
            $table->string('address');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->date('birthday');
            $table->string('about_me');
            $table->string('background_image');
            $table->integer('hash');
            $table->string('phone');
            $table->string('token');
            $table->tinyInteger('status');
            $table->tinyInteger('like_web');
            $table->string('user_image');
            $table->integer('sex');
            $table->string('province_id');
            $table->string('district_id');
            $table->string('ward_id');
            // $table->rememberToken();
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
