<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pemesanan_id');
            $table->dateTime('tanggal_transaksi');
            $table->integer('total');
            $table->string('metode_pembayaran', 50);
            $table->string('status_transaksi', 50);
            $table->unique(['pemesanan_id', 'tanggal_transaksi']);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
