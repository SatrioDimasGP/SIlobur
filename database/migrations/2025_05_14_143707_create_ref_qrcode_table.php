<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_qrcode', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('file_qrcode', 100)->nullable();
            $table->unsignedBigInteger('status_qr_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('status_qr_id')->references('id')->on('status_qr')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_qrcode');
    }
};
