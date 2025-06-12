<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up()
    {
        // Rename kolom jika ada
        if (Schema::hasColumn('transaksis', 'pemesanan_id')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->renameColumn('pemesanan_id', 'pemesanans_id');
            });
        }

        // Pastikan kolom bertipe BIGINT UNSIGNED NOT NULL
        DB::statement('ALTER TABLE transaksis MODIFY pemesanans_id BIGINT UNSIGNED NOT NULL');

        // Tambahkan foreign key
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreign('pemesanans_id')
                ->references('id')
                ->on('pemesanans')
                ->onDelete('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down()
    {
        // Drop FK dan rename kembali
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['pemesanans_id']);
            $table->renameColumn('pemesanans_id', 'pemesanan_id');
        });

        // (Opsional) Pastikan tipe ulang
        DB::statement('ALTER TABLE transaksis MODIFY pemesanan_id BIGINT UNSIGNED NOT NULL');
    }
};
