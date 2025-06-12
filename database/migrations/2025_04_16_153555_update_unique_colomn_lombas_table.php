<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Menambahkan unique constraint pada kolom 'nama' dan 'tanggal' secara bersamaan
            $table->unique(['nama', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('lombas', function (Blueprint $table) {
            // Menghapus unique constraint pada kolom 'nama' dan 'tanggal'
            $table->dropUnique(['nama', 'tanggal']);
        });
    }
};
