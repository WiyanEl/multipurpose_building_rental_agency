<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanan_tamabahan_sewas', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idpesanan');
            $table->integer('idaset');
            $table->integer('idtambahan');
            $table->float('hargatambahan');
            $table->float('jumlah');
            $table->float('totalhargatambahan');
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
        //
    }
};
