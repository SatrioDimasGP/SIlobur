<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            // Rename kolom terlebih dahulu
            $table->renameColumn('user_id', 'users_id');
            $table->renameColumn('lomba_id', 'lombas_id');
            $table->renameColumn('blok_gantangan_id', 'blok_gantangans_id');
            $table->renameColumn('bendera_id', 'benderas_id');
            $table->renameColumn('tahap_id', 'tahaps_id');
        });

        Schema::table('penilaians', function (Blueprint $table) {
            // Pastikan semua kolom bertipe unsigned jika berelasi ke kolom `id` tabel lain
            $table->unsignedBigInteger('users_id')->change();
            $table->unsignedBigInteger('lombas_id')->change();
            $table->unsignedBigInteger('blok_gantangans_id')->change();
            $table->unsignedBigInteger('benderas_id')->change();
            $table->unsignedBigInteger('tahaps_id')->change();

            // Tambahkan foreign key
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lombas_id')->references('id')->on('lombas')->onDelete('cascade');
            $table->foreign('blok_gantangans_id')->references('id')->on('blok_gantangans')->onDelete('cascade');
            $table->foreign('benderas_id')->references('id')->on('benderas')->onDelete('cascade');
            $table->foreign('tahaps_id')->references('id')->on('tahaps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['users_id']);
            $table->dropForeign(['lombas_id']);
            $table->dropForeign(['blok_gantangans_id']);
            $table->dropForeign(['benderas_id']);
            $table->dropForeign(['tahaps_id']);
        });

        Schema::table('penilaians', function (Blueprint $table) {
            // Rename kembali nama kolom ke semula
            $table->renameColumn('users_id', 'user_id');
            $table->renameColumn('lombas_id', 'lomba_id');
            $table->renameColumn('blok_gantangans_id', 'blok_gantangan_id');
            $table->renameColumn('benderas_id', 'bendera_id');
            $table->renameColumn('tahaps_id', 'tahap_id');
        });
    }
};
