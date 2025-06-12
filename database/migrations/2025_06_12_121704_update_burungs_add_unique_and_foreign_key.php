<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('burungs', function (Blueprint $table) {
            // Ubah kolom menjadi unsigned
            $table->unsignedBigInteger('jenis_burung_id')->change();
            $table->unsignedBigInteger('kelas_id')->change();

            // Tambah unique index gabungan
            // $table->unique(['jenis_burung_id', 'kelas_id'], 'burungs_jenis_burung_id_kelas_id_unique');

            // Tambah foreign key (jika jenis_burung_id perlu relasi, bisa ditambahkan juga)
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('burungs', function (Blueprint $table) {
            // $table->dropUnique('burungs_jenis_burung_id_kelas_id_unique');
            $table->dropForeign(['kelas_id']);
        });
    }
};
