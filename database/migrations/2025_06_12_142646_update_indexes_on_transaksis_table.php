<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ambil semua index dan kolom terkait
        $indexes = DB::select("SHOW INDEX FROM transaksis");

        // Kelompokkan berdasarkan nama index
        $indexGroups = collect($indexes)->groupBy('Key_name');

        // Hapus jika ada index lama
        if ($indexGroups->has('transaksis_pemesanan_id_tanggal_transaksi_unique')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropUnique('transaksis_pemesanan_id_tanggal_transaksi_unique');
            });
        }

        // Cek apakah transaksis_2_unique sudah sesuai
        $current = $indexGroups->get('transaksis_2_unique');

        $isCorrect =
            $current &&
            $current->count() === 2 &&
            $current[0]->Column_name === 'pemesanan_id' &&
            $current[1]->Column_name === 'tanggal_transaksi';

        // Jika belum sesuai, drop dan buat ulang
        if (!$isCorrect) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropUnique('transaksis_2_unique');
            });

            Schema::table('transaksis', function (Blueprint $table) {
                $table->unique(['pemesanan_id', 'tanggal_transaksi'], 'transaksis_2_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropUnique('transaksis_2_unique');
            $table->unique('tanggal_transaksi', 'transaksis_pemesanan_id_tanggal_transaksi_unique');
        });
    }
};
