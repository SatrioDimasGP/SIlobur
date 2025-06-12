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
        Schema::create('status_lombas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();  // Misal: 'Aktif' dan 'Nonaktif'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('status_lombas');
    }
};
