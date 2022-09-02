<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpaPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpa_property', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_rpa')->index()->comment('Llave de RPA');
            $table->string('name')->comment('Key de la propiedad');
            $table->string('value')->comment('Valor de la propiedad');
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
        Schema::dropIfExists('rpa_property');
    }
}
