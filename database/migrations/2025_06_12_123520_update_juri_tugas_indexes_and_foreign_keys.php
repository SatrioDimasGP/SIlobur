<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('juri_tugas', function (Blueprint $table) {
            // Ubah kolom jadi unsigned agar kompatibel dengan FK
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('lomba_id')->change();
            $table->unsignedBigInteger('blok_id')->change();
        });

        Schema::table('juri_tugas', function (Blueprint $table) {
            // Hapus index lama
            $table->dropUnique('juri_tugas_user_id_lomba_id_blok_id_unique');

            // Tambah index sesuai standar lama
            $table->unique(['user_id', 'lomba_id', 'blok_id'], 'juri_tugas_2_unique');

            // Tambah foreign key constraint
            $table->foreign('lomba_id', 'juri_tugas_lombas_id_foreign')
                ->references('id')->on('lombas')->onDelete('cascade');

            $table->foreign('blok_id', 'juri_tugas_bloks_id_foreign')
                ->references('id')->on('bloks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('juri_tugas', function (Blueprint $table) {
            // Drop FK constraints
            $table->dropForeign('juri_tugas_lombas_id_foreign');
            $table->dropForeign('juri_tugas_bloks_id_foreign');

            // Drop index baru
            $table->dropUnique('juri_tugas_2_unique');

            // Restore index lama
            $table->unique(['user_id', 'lomba_id', 'blok_id'], 'juri_tugas_user_id_lomba_id_blok_id_unique');

            // Kembalikan ke signed (opsional, jika benar-benar perlu restore ke awal)
            $table->bigInteger('user_id')->change();
            $table->bigInteger('lomba_id')->change();
            $table->bigInteger('blok_id')->change();
        });
    }
};
