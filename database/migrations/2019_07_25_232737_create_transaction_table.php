<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->string('checksum')->nullable();
            $table->string('nombrearchivoorigen')->nullable();
            $table->string('rpa')->nullable();
            $table->string('sistemaorigen')->nullable();
            $table->string('sistemadestino')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('uuid')->nullable();
            $table->string('estado')->nullable();
            $table->string('nombrearchivodestino')->nullable();
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
        Schema::dropIfExists('transaction');
    }
}
