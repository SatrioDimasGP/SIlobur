<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Rename kolom bloks_id -> blok_id
        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->renameColumn('bloks_id', 'blok_id');
        });

        // Drop foreign key dan index lama
        Schema::table('blok_gantangans', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign('blok_gantangans_bloks_id_foreign');
            $table->dropForeign('blok_gantangans_gantangans_id_foreign');

            // Drop unique index lama (hanya di gantangan_id)
            $table->dropUnique('blok_gantangans_2_unique');
        });

        // Ubah tipe kolom dan buat foreign key & index baru
        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->unsignedBigInteger('blok_id')->change();
            $table->unsignedBigInteger('gantangan_id')->change();

            // Unique gabungan blok_id + gantangan_id
            $table->unique(['blok_id', 'gantangan_id'], 'blok_gantangans_2_unique');

            // Foreign key baru
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

        Schema::table('blok_gantangans', function (Blueprint $table) {
            $table->renameColumn('blok_id', 'bloks_id');
        });
    }
};
