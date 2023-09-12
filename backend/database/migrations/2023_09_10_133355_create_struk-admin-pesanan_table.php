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
        Schema::create('struk_admin_pesanans', function (Blueprint $table) {
            $table->id();
            $table->boolean('statusenabled');
            $table->integer('idpesanan');
            $table->integer('idstrukpembayaran');
            $table->timestamp('tglbayar');
            $table->float('totaltagihan');
            $table->float('hargaadmin');
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
