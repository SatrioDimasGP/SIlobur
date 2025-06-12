<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusPemesanansColumnInPemesanansTable extends Migration
{
    public function up()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Drop foreign key & column lama jika sudah terhubung
            if (Schema::hasColumn('pemesanans', 'status_pesanan_id')) {
                $table->dropColumn('status_pesanan_id');
            }

            // Tambahkan kolom baru
            $table->unsignedBigInteger('status_pemesanans_id')->after('gantangan_id'); // sesuaikan posisinya jika perlu
            $table->foreign('status_pemesanans_id')->references('id')->on('status_pemesanans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['status_pemesanans_id']);
            $table->dropColumn('status_pemesanans_id');

            // Tambahkan kembali kolom lama jika ingin rollback
            $table->unsignedBigInteger('status_pesanan_id')->after('id');
            $table->foreign('status_pesanan_id')->references('id')->on('status_pemesanans')->onDelete('cascade');
        });
    }
}
