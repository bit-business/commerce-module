<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('skijasi.database.prefix').'order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained(config('skijasi.database.prefix').'orders');
            $table->foreignId('product_detail_id')->constrained(config('skijasi.database.prefix').'product_details');
            $table->foreignId('discount_id')->nullable()->constrained(config('skijasi.database.prefix').'discounts')->onDelete('set null');
            $table->double('price');
            $table->double('discounted');
            $table->bigInteger('quantity');
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
        Schema::dropIfExists(config('skijasi.database.prefix').'order_details');
    }
}
