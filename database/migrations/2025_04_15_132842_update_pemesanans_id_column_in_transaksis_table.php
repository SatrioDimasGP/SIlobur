<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Drop foreign key & column lama jika sudah terhubung
            if (Schema::hasColumn('transaksis', 'pemesanan_id')) {
                $table->dropColumn('pemesanan_id');
            }

            // Tambahkan kolom baru
            $table->unsignedBigInteger('pemesanans_id')->after('id'); // sesuaikan posisinya jika perlu
            $table->foreign('pemesanans_id')->references('id')->on('pemesanans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['pemesanans_id']);
            $table->dropColumn('pemesanans_id');

            // Tambahkan kembali kolom lama jika ingin rollback
            $table->unsignedBigInteger('pemesanan_id')->after('id');
        });
    }
};
