<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('categories_id');
            $table->string('categories_name');
            $table->string('categories_metatitle');
            $table->integer('categories_parentid');
            $table->integer('categories_displayorder');
            $table->tinyInteger('categories_showonhome');
            $table->tinyInteger('categories_status');
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
        Schema::dropIfExists('categories');
    }
}
