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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->references('id')->on('users');
            $table->string('title')->nullable()->default(null);
            $table->string('description')->nullable()->default(null);
            $table->float('gross_price')->nullable()->default(null);
            $table->float('net_price')->nullable()->default(null);
            $table->float('vat')->nullable()->default(null);
            $table->float('shipping_cost')->nullable()->default(null);

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
};
