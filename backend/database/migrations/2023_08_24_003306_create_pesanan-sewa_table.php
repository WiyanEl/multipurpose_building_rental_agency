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
        Schema::create('pesanan_sewas', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idpenyewa');
            $table->integer('idmitra');
            $table->integer('idaset');
            $table->string('nosewa');
            $table->float('hargasewa');
            $table->float('hargadibayar');
            $table->float('sisatagihansewa');
            $table->float('totaltagihan');
            $table->timestamp('tglawalsewa');
            $table->timestamp('tglakhirsewa');
            $table->timestamp('tgljatuhtempo');
            $table->boolean('statuspesanan');
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
