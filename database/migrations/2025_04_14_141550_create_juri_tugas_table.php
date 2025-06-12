<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juri_tugas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id'); // Juri
            $table->bigInteger('lomba_id');
            $table->bigInteger('blok_id');
            $table->unique(['user_id', 'lomba_id', 'blok_id']);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juri_tugas');
    }
};
