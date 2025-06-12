<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('harga_burungs');
    }

    public function down(): void
    {
        Schema::create('harga_burungs', function ($table) {
            $table->id();
            $table->integer('harga');
            $table->unsignedBigInteger('burung_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->unique(['harga', 'burung_id'], 'harga_burungs_harga_burung_id_unique');
        });
    }
};
