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
        Schema::create('harga_tambahan_asets', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idaset');
            $table->string('namatambahan');
            $table->float('hargatambahan');
            $table->float('sedia');
            $table->boolean('statustambahan');
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
