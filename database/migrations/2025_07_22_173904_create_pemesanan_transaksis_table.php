<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemesananTransaksisTable extends Migration
{
    public function up()
    {
        Schema::create('pemesanan_transaksis', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('pemesanan_id');

            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('transaksi_id')
                ->references('id')
                ->on('transaksis')
                ->onDelete('cascade');

            $table->foreign('pemesanan_id')
                ->references('id')
                ->on('pemesanans')
                ->onDelete('cascade');

            // Hindari duplikat entri
            $table->unique(['transaksi_id', 'pemesanan_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemesanan_transaksis');
    }
}
