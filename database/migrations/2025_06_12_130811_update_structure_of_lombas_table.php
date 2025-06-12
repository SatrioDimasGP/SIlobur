<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Ubah tipe kolom
            $table->string('lokasi', 255)->change();

            // Tambah kolom baru
            $table->unsignedBigInteger('status_lomba_id')->nullable()->after('deskripsi');

            // Hapus kolom tidak sesuai
            if (Schema::hasColumn('lombas', 'jenis_burung_id')) {
                $table->dropColumn('jenis_burung_id');
            }
            if (Schema::hasColumn('lombas', 'kelas_id')) {
                $table->dropColumn('kelas_id');
            }

            // Tambahkan foreign key (jika belum ada)
            $table->foreign('status_lomba_id', 'lombas_status_lomba_id_foreign')
                ->references('id')->on('status_lombas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Kembalikan lokasi ke TEXT
            $table->text('lokasi')->change();

            // Hapus FK dan kolom
            $table->dropForeign('lombas_status_lomba_id_foreign');
            $table->dropColumn('status_lomba_id');

            // Tambahkan kembali kolom lama
            $table->bigInteger('jenis_burung_id')->after('deskripsi');
            $table->bigInteger('kelas_id')->after('jenis_burung_id');
        });
    }
};
