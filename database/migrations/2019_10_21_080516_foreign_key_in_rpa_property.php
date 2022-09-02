<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeyInRpaProperty extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('rpa_property', function (Blueprint $table) {
            $table->foreign('id_rpa')
                    ->references('id')
                    ->on('rpa')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rpa_property', function (Blueprint $table) {
            
        });
    }

}
