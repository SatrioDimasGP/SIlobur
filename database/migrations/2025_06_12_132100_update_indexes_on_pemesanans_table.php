<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateIndexesOnPemesanansTable extends Migration
{
    public function up()
    {
        // Step 1: Drop foreign key constraints
        Schema::table('pemesanans', function (Blueprint $table) {
            // Gunakan kolom agar tidak tergantung nama FK
            $table->dropForeign(['user_id']);
            $table->dropForeign(['burung_id']);
            $table->dropForeign(['status_pemesanan_id']);
            $table->dropForeign(['lomba_id']);
            $table->dropForeign(['gantangan_id']);
        });

        // Step 2: Drop indexes (try-catch kalau tidak ada)
        try {
            DB::statement('ALTER TABLE pemesanans DROP INDEX pemesanans_gantangan_id_unique');
        } catch (\Throwable $e) {
            // Index might not exist
        }

        try {
            DB::statement('ALTER TABLE pemesanans DROP INDEX pemesanans_gantangan_burung_unique');
        } catch (\Throwable $e) {
            // Index might not exist
        }

        // Step 3: Tambah kembali index dan FK
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->unique(['gantangan_id', 'burung_id'], 'pemesanans_gantangan_burung_unique');

            $table->foreign('user_id', 'pemesanans_user_id_foreign')->references('id')->on('users');
            $table->foreign('burung_id', 'pemesanans_burung_id_foreign')->references('id')->on('burungs');
            $table->foreign('status_pemesanan_id', 'pemesanans_status_pemesanan_id_foreign')->references('id')->on('status_pemesanans');
            $table->foreign('lomba_id', 'pemesanans_lomba_id_foreign')->references('id')->on('lombas');
            $table->foreign('gantangan_id', 'pemesanans_gantangan_id_foreign')->references('id')->on('gantangans');
        });
    }

    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['burung_id']);
            $table->dropForeign(['status_pemesanan_id']);
            $table->dropForeign(['lomba_id']);
            $table->dropForeign(['gantangan_id']);

            $table->dropUnique('pemesanans_gantangan_burung_unique');

            // Tambah kembali index & FK dasar
            $table->unique('gantangan_id', 'pemesanans_gantangan_id_unique');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('burung_id')->references('id')->on('burungs');
            $table->foreign('status_pemesanan_id')->references('id')->on('status_pemesanans');
            $table->foreign('lomba_id')->references('id')->on('lombas');
            $table->foreign('gantangan_id')->references('id')->on('gantangans');
        });
    }
}
