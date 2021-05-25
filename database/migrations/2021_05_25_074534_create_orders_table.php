<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('orders_id');
            $table->integer('product_size_id');
            $table->integer('user_id');
            $table->integer('shipplace_id');
            $table->tinyInteger('orders_status');
            $table->integer('orders_quantity');
            $table->integer('product_price');
            $table->bigInteger('orders_type');
            $table->integer('product_cost');
            $table->integer('orders_access');
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
        Schema::dropIfExists('orders');
    }
}
