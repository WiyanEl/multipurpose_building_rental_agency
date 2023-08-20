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
        Schema::create('aset_mitra', function (Blueprint $table) {
            $table->id();
            $table->integer('idmitra');
            $table->string('namaaset');
            $table->string('deskripsi');
            $table->float('hargasewaaset');
            $table->float('hargadiskonsewa');
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
