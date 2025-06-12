<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rename kolom jika masih bernama 'bloks_id'
        if (Schema::hasColumn('blok_gantangans', 'bloks_id')) {
            Schema::table('blok_gantangans', function (Blueprint $table) {
                $table->renameColumn('bloks_id', 'blok_id');
            });
        }

        // Pastikan kolom bertipe BIGINT UNSIGNED
        DB::statement('ALTER TABLE blok_gantangans MODIFY blok_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE blok_gantangans MODIFY gantangan_id BIGINT UNSIGNED NOT NULL');

        // Drop index unik jika ada
        try {
            Schema::table('blok_gantangans', function (Blueprint $table) {
                $table->dropUnique('blok_gantangans_2_unique');
            });
        } catch (\Throwable $e) {}

        // Tambah index & FK baru
        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->unique(['blok_id', 'gantangan_id'], 'blok_gantangans_2_unique');
            $table->foreign('blok_id')->references('id')->on('bloks')->onDelete('cascade');
            $table->foreign('gantangan_id')->references('id')->on('gantangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop FK & index
        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->dropForeign(['blok_id']);
            $table->dropForeign(['gantangan_id']);
            $table->dropUnique('blok_gantangans_2_unique');
        });

        // Rename kembali jika ada
        if (Schema::hasColumn('blok_gantangans', 'blok_id')) {
            Schema::table('blok_gantangans', function (Blueprint $table) {
                $table->renameColumn('blok_id', 'bloks_id');
            });
        }
    }
};
