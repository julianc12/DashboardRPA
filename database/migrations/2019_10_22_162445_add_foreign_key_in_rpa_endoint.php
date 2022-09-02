<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyInRpaEndoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rpa_endpoint', function (Blueprint $table) {
                 $table->foreign('tipo_rpa')
                    ->references('id')
                    ->on('rpa_type')
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
        Schema::table('rpa_endoint', function (Blueprint $table) {
            //
        });
    }
}
