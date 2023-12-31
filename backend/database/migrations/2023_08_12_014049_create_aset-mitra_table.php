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
        Schema::create('aset_mitras', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idmitra');
            $table->string('namaaset');
            $table->string('deskripsi');
            $table->float('hargasewaaset');
            $table->float('hargadiskonsewa');
            $table->float('maxjamsewa');
            $table->float('kapasitas');
            $table->boolean('statusaset');
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
