<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Ubah nama kolom pemesanans_id → pemesanan_id
            $table->renameColumn('pemesanans_id', 'pemesanan_id');

            // Hapus kolom status_transaksi lama (VARCHAR)
            $table->dropColumn('status_transaksi');

            // Tambahkan kolom status_transaksi_id baru (BIGINT)
            $table->unsignedBigInteger('status_transaksi_id')->after('metode_pembayaran');

            // (Opsional) Tambahkan foreign key kalau kamu mau
            $table->foreign('status_transaksi_id')->references('id')->on('status_transaksis');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Revert perubahan
            $table->renameColumn('pemesanan_id', 'pemesanans_id');
            $table->dropForeign(['status_transaksi_id']);
            $table->dropColumn('status_transaksi_id');
            $table->string('status_transaksi', 50)->after('metode_pembayaran');
        });
    }
};
