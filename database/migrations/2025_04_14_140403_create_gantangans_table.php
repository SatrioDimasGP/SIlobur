<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gantangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor', 10);
            $table->bigInteger('harga_burung_id');
            $table->bigInteger('status_gantangan_id');
            $table->unique(['nomor', 'harga_burung_id']);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gantangans');
    }
};

