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
        Schema::table('blok_gantangans', function (Blueprint $table) {
            // Drop foreign key & column lama jika sudah terhubung
            if (Schema::hasColumn('blok_gantangans', 'blok_id')) {
                $table->dropColumn('blok_id');
            }

            // Tambahkan kolom baru
            $table->unsignedBigInteger('bloks_id')->after('id'); // sesuaikan posisinya jika perlu
            $table->foreign('bloks_id')->references('id')->on('bloks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('bloks', function (Blueprint $table) {
            $table->dropForeign(['bloks_id']);
            $table->dropColumn('bloks_id');

            // Tambahkan kembali kolom lama jika ingin rollback
            $table->unsignedBigInteger('blok_id')->after('id');
        });
    }
};
