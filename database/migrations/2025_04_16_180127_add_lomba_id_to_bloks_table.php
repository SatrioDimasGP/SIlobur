<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLombaIdToBloksTable extends Migration
{
    public function up(): void
    {
        Schema::table('bloks', function (Blueprint $table) {
            if (!Schema::hasColumn('bloks', 'lomba_id')) {
                $table->unsignedBigInteger('lomba_id')->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bloks', function (Blueprint $table) {
            $table->dropColumn('lomba_id');
        });
    }
}
