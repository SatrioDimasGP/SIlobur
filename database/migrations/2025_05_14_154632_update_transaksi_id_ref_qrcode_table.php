<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ref_qrcode', function (Blueprint $table) {
            $table->unsignedBigInteger('transaksi_id')->after('user_id');
            $table->foreign('transaksi_id')->references('id')->on('transaksis')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ref_qrcode', function (Blueprint $table) {
            $table->dropColumn('transaksi_id');
        });
    }
};
