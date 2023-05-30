<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('skijasi.database.prefix').'carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(config('skijasi.database.prefix').'users')->onDelete('cascade');
            $table->foreignId('product_detail_id')->constrained(config('skijasi.database.prefix').'product_details')->onDelete('cascade');
            $table->integer('quantity');
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
        Schema::dropIfExists(config('skijasi.database.prefix').'carts');
    }
}
