<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_transaction')->index()->comment('Transaccion maestro');
            $table->string('step')->comment('Nombre del paso');
            $table->string('command')->comment('Texto a escribir en el Shell')->nullable();
            $table->string('type')->comment('key, normal, contains, count_lines, wait_contains');
            $table->string('properties')->comment('JSON de propiedades que usa el RPA para ejecutar el comando');
            $table->string('nextYes')->comment('Ejecucion normal o validado correctamente (Nombre del paso siguiente)');
            $table->string('nextNo')->comment('Cuando no se encuentra la validacion (Nombre del paso siguiente)');
            $table->string('status')->comment('ok => Validacion ejecuta NextYes, fail => Validacion ejecuta NextNo')->nullable();
            $table->string('output')->comment('Puede ser NULL dependiendo del parametro sendOutput del RPA')->nullable();
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
        Schema::dropIfExists('transaction_detail');
    }
}
