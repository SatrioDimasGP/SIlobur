<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Menambahkan kolom 'deskripsi' dengan tipe VARCHAR(255) setelah 'lokasi'
            $table->string('deskripsi', 255)->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Menghapus kolom 'deskripsi'
            $table->dropColumn('deskripsi');
        });
    }
};
