<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Yang perlu tetap unsigned
            $table->unsignedBigInteger('id')->change();
            $table->unsignedBigInteger('lomba_id')->change();

            // Ubah ke signed
            $table->integer('harga')->change();
            $table->bigInteger('created_by')->nullable()->change();
            $table->bigInteger('updated_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Kembalikan ke unsigned
            $table->unsignedInteger('harga')->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
        });
    }
};
