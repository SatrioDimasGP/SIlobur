<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaAndLombaIdToKelasTable extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->integer('harga')->after('nama');
            $table->unsignedBigInteger('lomba_id')->after('harga');

            // Tambahkan foreign key
            $table->foreign('lomba_id')->references('id')->on('lombas')->onDelete('cascade');

            // Tambahkan unique constraint gabungan
            $table->unique(['nama', 'harga'], 'kelas_nama_harga_unique');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_nama_harga_unique');
            $table->dropForeign(['lomba_id']);
            $table->dropColumn(['harga', 'lomba_id']);
        });
    }
}
