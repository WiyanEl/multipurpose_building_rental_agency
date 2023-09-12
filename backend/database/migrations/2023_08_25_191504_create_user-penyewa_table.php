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
        Schema::create('user_penyewas', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('iduser');
            $table->string('namalengkap');
            $table->string('email');
            $table->string('alamat');
            $table->string('notelp');
            $table->string('norekening');
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
