<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpaCommandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpa_command', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_rpa')->comment('Llave de RPA');
            $table->string('step')->comment('ID del comando');
            $table->string('command')->comment('Texto a escribir en el Shell')->nullable();
            $table->string('type')->comment('key, normal, contains, no_contains, count_lines');
            $table->string('properties')->comment('JSON de propiedades que usa el RPA para ejecutar el comando');
            $table->string('nextYes')->comment('Ejecucion normal o validado correctamente (Nombre step del paso siguiente)')->nullable();
            $table->string('nextNo')->comment('Cuando no se encuentra la validacion (Nombre step del paso siguiente)')->nullable();
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
        Schema::dropIfExists('rpa_command');
    }
}
