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
        Schema::create('diskon_mitras', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idmitra');
            $table->string('namadiskon');
            $table->string('persendiskon');
            $table->string('hargadiskon');
            $table->string('statusdiskon');
            $table->timestamp('tglawaldiskon');
            $table->timestamp('tglakhirdiskon');
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
