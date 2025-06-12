<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gantangans', function (Blueprint $table) {
            // Drop unique index jika ada (pastikan nama index benar)
            $table->dropUnique('gantangans_nomor_harga_burung_id_unique');

            // Drop kolom jika ada
            if (Schema::hasColumn('gantangans', 'harga_burung_id')) {
                $table->dropColumn('harga_burung_id');
            }

            if (Schema::hasColumn('gantangans', 'status_gantangan_id')) {
                $table->dropColumn('status_gantangan_id');
            }

            // Tambahkan unique constraint ke kolom nomor
            $table->unique('nomor', 'gantangans_nomor_unique');
        });
    }

    public function down(): void
    {
        Schema::table('gantangans', function (Blueprint $table) {
            // Tambah kembali kolom yang dihapus
            $table->unsignedBigInteger('harga_burung_id')->after('nomor');
            $table->unsignedBigInteger('status_gantangan_id')->after('harga_burung_id');

            // Tambah kembali unique gabungan
            $table->unique(['nomor', 'harga_burung_id'], 'gantangans_nomor_harga_burung_id_unique');

            // Hapus unique `nomor` tunggal
            $table->dropUnique('gantangans_nomor_unique');
        });
    }
};
