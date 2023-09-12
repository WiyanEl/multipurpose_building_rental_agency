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
        Schema::create('mitra_pengelola_gedungs', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->foreignId('userid');
            $table->string('namalengkap');
            $table->string('namamitra');
            $table->string('email');
            $table->string('alamat');
            $table->string('linkmapsalamat');
            $table->string('notelp');
            $table->string('norekening');
            $table->string('gambar');
            $table->string('path');
            $table->float('makskapasitas');
            $table->string('fasilitas');
            $table->boolean('statusmitra');
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
        Schema::dropIfExists('gambar');
    }
};
