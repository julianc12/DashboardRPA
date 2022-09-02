<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeyInTableOrqRpa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orq_rpa', function (Blueprint $table) {
              $table->foreign('idrpa')
            ->references('id')
            ->on('rpa')
            ->onDelete('cascade');
           
           $table->foreign('idorquestacion')
            ->references('id')
            ->on('rpa_orquestacion')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_orq_rpa', function (Blueprint $table) {
            //
        });
    }
}
