<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Ubah tipe kolom menjadi unsigned
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('gantangan_id')->change();
            $table->unsignedBigInteger('status_pemesanans_id')->change();
            $table->unsignedBigInteger('lomba_id')->change();

            // Ubah nama kolom status_pemesanans_id ➝ status_pemesanan_id
            $table->renameColumn('status_pemesanans_id', 'status_pemesanan_id');

            // Tambahkan kolom burung_id jika belum ada
            if (!Schema::hasColumn('pemesanans', 'burung_id')) {
                $table->unsignedBigInteger('burung_id')->after('gantangan_id');
            }

            // Hapus index lama jika perlu
            if (Schema::hasColumn('pemesanans', 'gantangan_id')) {
                Schema::dropIfExists('pemesanans_gantangan_id_unique');
            }

            // Tambahkan unique gabungan baru
            $table->unique(['gantangan_id', 'burung_id'], 'pemesanans_gantangan_burung_unique');

            // Tambahkan foreign key
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('gantangan_id')->references('id')->on('gantangans');
            $table->foreign('burung_id')->references('id')->on('burungs');
            $table->foreign('status_pemesanan_id')->references('id')->on('status_pemesanans');
            $table->foreign('lomba_id')->references('id')->on('lombas');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['gantangan_id']);
            $table->dropForeign(['burung_id']);
            $table->dropForeign(['status_pemesanan_id']);
            $table->dropForeign(['lomba_id']);

            // Drop unique gabungan
            $table->dropUnique('pemesanans_gantangan_burung_unique');

            // Hapus kolom burung_id
            $table->dropColumn('burung_id');

            // Rename kembali kolom status_pemesanan_id
            $table->renameColumn('status_pemesanan_id', 'status_pemesanans_id');
        });
    }
};
