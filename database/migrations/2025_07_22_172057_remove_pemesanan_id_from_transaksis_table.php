<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePemesananIdFromTransaksisTable extends Migration
{
    public function up()
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign('transaksis_pemesanans_id_foreign');

            // Hapus unique key gabungan
            $table->dropUnique('transaksis_2_unique');

            // Hapus kolom pemesanan_id
            $table->dropColumn('pemesanan_id');
        });
    }

    public function down()
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Tambah kembali kolom
            $table->unsignedBigInteger('pemesanan_id')->after('id');

            // Tambah kembali foreign key
            $table->foreign('pemesanan_id')
                  ->references('id')
                  ->on('pemesanans')
                  ->onDelete('cascade');

            // Tambah kembali unique key gabungan
            $table->unique(['pemesanan_id', 'tanggal_transaksi'], 'transaksis_2_unique');
        });
    }
}
