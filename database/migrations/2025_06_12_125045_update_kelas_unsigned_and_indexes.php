<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Drop FK dari tabel burungs -> kelas_id
        Schema::table('burungs', function (Blueprint $table) {
            try {
                $table->dropForeign(['kelas_id']);
            } catch (\Throwable $e) {
                // Abaikan error jika belum ada
            }
        });

        // Step 2: Ubah kolom-kolom kelas
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change();
            $table->unsignedInteger('harga')->change();
            $table->unsignedBigInteger('lomba_id')->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
        });

        // Step 3: Drop FK jika sudah ada, lalu buat ulang
        Schema::table('kelas', function (Blueprint $table) {
            // Drop FK jika sudah ada
            try {
                $table->dropForeign('kelas_lomba_id_foreign');
            } catch (\Throwable $e) {
                // Abaikan error jika belum ada
            }

            // Drop index unik lama jika perlu
            try {
                $table->dropUnique('kelas_nama_unique');
            } catch (\Throwable $e) {
                // Abaikan jika tidak ada
            }

            // Tambahkan FK kembali
            $table->foreign('lomba_id', 'kelas_lomba_id_foreign')
                ->references('id')->on('lombas')->onDelete('cascade');
        });

        // Step 4: Tambahkan FK kembali di burungs -> kelas_id
        Schema::table('burungs', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('burungs', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
        });

        Schema::table('kelas', function (Blueprint $table) {
            try {
                $table->dropForeign('kelas_lomba_id_foreign');
            } catch (\Throwable $e) {
                //
            }

            $table->bigInteger('id')->change();
            $table->integer('harga')->change();
            $table->bigInteger('lomba_id')->change();
            $table->bigInteger('created_by')->nullable()->change();
            $table->bigInteger('updated_by')->nullable()->change();

            $table->unique('nama', 'kelas_nama_unique');
        });

        Schema::table('burungs', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }
};
