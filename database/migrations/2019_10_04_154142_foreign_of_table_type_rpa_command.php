<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignOfTableTypeRpaCommand extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('type_rpa_command', function (Blueprint $table) {
           $table->foreign('type_command')
            ->references('id')
            ->on('type_commands')
            ->onDelete('cascade');
           
           $table->foreign('type_rpa')
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
    public function down() {       
    }

}
