<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {

            // Tambahkan unique constraint baru pada kolom yang sama dengan nama index yang sama atau berbeda jika diinginkan
            $table->unique(['pemesanans_id', 'tanggal_transaksi'], 'transaksis_2_unique');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {

            // Hapus unique constraint baru saat rollback
            $table->drorpUnique(['pemesanans_id', 'tanggal_transaksi'], 'transaksis_2_unique');
        });
    }
};
