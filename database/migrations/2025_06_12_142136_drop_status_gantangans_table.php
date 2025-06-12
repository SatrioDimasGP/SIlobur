<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('status_gantangans');
    }

    public function down(): void
    {
        Schema::create('status_gantangans', function ($table) {
            $table->id(); // Primary key
            $table->string('nama', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }
};
