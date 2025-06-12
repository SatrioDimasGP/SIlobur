<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rename kolom jika bloks_id masih ada
        if (Schema::hasColumn('blok_gantangans', 'bloks_id')) {
            Schema::table('blok_gantangans', function (Blueprint $table) {
                $table->renameColumn('bloks_id', 'blok_id');
            });
        }

        Schema::table('blok_gantangans', function (Blueprint $table) {
            // Drop foreign key jika constraint masih ada
            try {
                $table->dropForeign('blok_gantangans_bloks_id_foreign');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropForeign('blok_gantangans_gantangans_id_foreign');
            } catch (\Throwable $e) {
            }

            // Drop index unik lama jika ada
            try {
                $table->dropUnique('blok_gantangans_2_unique');
            } catch (\Throwable $e) {
            }
        });

        Schema::table('blok_gantangans', function (Blueprint $table) {
            // Pastikan tipe kolom sudah unsigned
            DB::statement('ALTER TABLE blok_gantangans MODIFY blok_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE blok_gantangans MODIFY gantangan_id BIGINT UNSIGNED NOT NULL');

            // Tambahkan kembali index dan FK
            $table->unique(['blok_id', 'gantangan_id'], 'blok_gantangans_2_unique');
            $table->foreign('blok_id')->references('id')->on('bloks')->onDelete('cascade');
            $table->foreign('gantangan_id')->references('id')->on('gantangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->dropForeign(['blok_id']);
            $table->dropForeign(['gantangan_id']);
            $table->dropUnique('blok_gantangans_2_unique');
        });

        if (Schema::hasColumn('blok_gantangans', 'blok_id')) {
            Schema::table('blok_gantangans', function (Blueprint $table) {
                $table->renameColumn('blok_id', 'bloks_id');
            });
        }
    }
};
