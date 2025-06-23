<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBurungIdToBloksTable extends Migration
{
    public function up()
    {
        Schema::table('bloks', function (Blueprint $table) {
            // Tambahkan kolom burung_id setelah lomba_id
            $table->unsignedBigInteger('burung_id')->after('lomba_id');

            // Tambahkan foreign key ke tabel burungs
            $table->foreign('burung_id')->references('id')->on('burungs')->onDelete('cascade');

            // Hapus unique lama (jika ada) lalu tambahkan unique baru untuk nama, lomba_id, burung_id
            $table->dropUnique('bloks_nama_lomba_id_unique');
            $table->unique(['nama', 'lomba_id', 'burung_id'], 'bloks_nama_lomba_id_burung_id_unique');
        });
    }

    public function down()
    {
        Schema::table('bloks', function (Blueprint $table) {
            // Drop unique index baru
            $table->dropUnique('bloks_nama_lomba_id_burung_id_unique');

            // Kembalikan unique lama
            $table->unique(['nama', 'lomba_id'], 'bloks_nama_lomba_id_unique');

            // Hapus foreign key dan kolom
            $table->dropForeign(['burung_id']);
            $table->dropColumn('burung_id');
        });
    }
}
