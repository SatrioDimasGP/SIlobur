<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename kolom dengan aman
        if (Schema::hasColumn('transaksis', 'pemesanan_id')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->renameColumn('pemesanan_id', 'pemesanans_id');
            });
        }

        // Tambahkan FK baru jika perlu
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'pemesanans_id')) return;

            // Cek apakah FK belum ada (gunakan try-catch agar tidak crash)
            try {
                $table->foreign('pemesanans_id')->references('id')->on('pemesanans')->onDelete('cascade');
            } catch (\Throwable $e) {
                // FK mungkin sudah ada — abaikan
            }
        });
    }

    public function down()
    {
        // Rollback rename
        if (Schema::hasColumn('transaksis', 'pemesanans_id')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropForeign(['pemesanans_id']);
                $table->renameColumn('pemesanans_id', 'pemesanan_id');
            });
        }
    }
};
