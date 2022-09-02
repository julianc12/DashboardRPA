<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCgunoDocumentosConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cguno_documentos_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('center')->comment('Centro de operaciones');
            $table->string('lapse')->comment('Lapso en formato YYMM');
            $table->string('number')->comment('Numero de consecutivo del documento');
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
        Schema::dropIfExists('cguno_documentos_config');
    }
}
