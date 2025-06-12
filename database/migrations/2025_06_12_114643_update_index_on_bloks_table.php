<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIndexOnBloksTable extends Migration
{
    public function up(): void
    {
        Schema::table('bloks', function (Blueprint $table) {
            // Ubah dulu kolom agar unsigned
            $table->unsignedBigInteger('lomba_id')->change();

            // Hapus index unik lama (jika ada)
            $table->dropUnique('bloks_nama_lomba_id_unique');

            // Tambah ulang index unik kombinasi
            $table->unique(['nama', 'lomba_id'], 'bloks_nama_lomba_id_unique');

            // Tambahkan foreign key
            $table->foreign('lomba_id')
                ->references('id')
                ->on('lombas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bloks', function (Blueprint $table) {
            // Drop foreign key dan index
            $table->dropForeign(['lomba_id']);
            $table->dropUnique('bloks_nama_lomba_id_unique');

            // Ubah kembali jadi signed (jika perlu rollback)
            $table->bigInteger('lomba_id')->change();
        });
    }
}
