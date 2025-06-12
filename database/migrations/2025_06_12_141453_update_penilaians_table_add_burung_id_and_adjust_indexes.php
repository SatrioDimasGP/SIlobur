<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            // Tambah kolom burung_id
            $table->unsignedBigInteger('burung_id')->after('blok_gantangan_id');

            // Ubah tipe kolom
            $table->unsignedBigInteger('bendera_id')->nullable()->change();
            $table->unsignedBigInteger('status_penilaian_id')->nullable(false)->change();

            // Tambah FK baru
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lomba_id')->references('id')->on('lombas')->onDelete('cascade');
            $table->foreign('blok_gantangan_id')->references('id')->on('blok_gantangans')->onDelete('cascade');
            $table->foreign('burung_id')->references('id')->on('burungs')->onDelete('cascade');
            $table->foreign('bendera_id')->references('id')->on('benderas')->onDelete('set null');
            $table->foreign('tahap_id')->references('id')->on('tahaps')->onDelete('cascade');
            $table->foreign('status_penilaian_id')->references('id')->on('status_penilaians')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            // Drop FK baru
            $table->dropForeign(['user_id']);
            $table->dropForeign(['lomba_id']);
            $table->dropForeign(['blok_gantangan_id']);
            $table->dropForeign(['burung_id']);
            $table->dropForeign(['bendera_id']);
            $table->dropForeign(['tahap_id']);
            $table->dropForeign(['status_penilaian_id']);

            // Hapus kolom burung_id
            $table->dropColumn('burung_id');

            // Ubah tipe kembali
            $table->unsignedBigInteger('bendera_id')->nullable(false)->change();
            $table->unsignedBigInteger('status_penilaian_id')->nullable()->change();
        });
    }
};
