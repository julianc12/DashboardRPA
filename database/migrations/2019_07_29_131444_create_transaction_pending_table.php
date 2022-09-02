<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionPendingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_pending', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_transaction')->index()->comment('Transaccion maestro');
            $table->string('criteria')->comment('Numero de centro de operacion');
            $table->string('value')->comment('Valor del criterio')->nullable();
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
        Schema::dropIfExists('transaction_pending');
    }
}
