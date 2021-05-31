<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->integer('size_id');
            $table->string('product_name');
            $table->string('product_code');
            $table->string('product_metatitle');
            $table->text('product_description');
            $table->longText('product_more_image');
            $table->text('product_image');
            $table->integer('product_promotion');
            $table->tinyInteger('product_includedvat');
            $table->integer('product_price');
            $table->integer('product_quantity');
            $table->integer('product_category_id');
            $table->string('product_detail');
            $table->tinyInteger('product_status');
            $table->integer('product_viewcount');
            $table->double('product_rate');
            $table->string('product_material');
            $table->longText('product_size');
            $table->integer('sex');
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
        Schema::dropIfExists('products');
    }
}
