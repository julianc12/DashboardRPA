<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarColumnaEnRpa extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
       Schema::table('rpa', function (Blueprint $table) {
            $table->integer('id_orquestacion')->nullable()->after('name');
        });
            
//            $table->foreign('id_orquestacion')
//                    ->references('id')
//                    ->on('rpa_orquestacion')
//                    ->onDelete('cascade');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
