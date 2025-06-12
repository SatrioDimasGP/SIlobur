<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blok_gantangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('blok_id');
            $table->unsignedBigInteger('gantangan_id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();

            // Unique index gabungan
            $table->unique(['blok_id', 'gantangan_id'], 'blok_gantangans_2_unique');

            // Index foreign key untuk gantangan
            $table->foreign('gantangan_id', 'blok_gantangans_gantangans_id_foreign')
                  ->references('id')->on('gantangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blok_gantangans');
    }
};

