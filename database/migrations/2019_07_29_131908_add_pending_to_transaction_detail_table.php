<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingToTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->integer('id_transaction_pending')->nullable(true);
            $table->foreign('id_transaction_pending')->references('id')->on('transaction_pending')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            //
        });
    }
}
