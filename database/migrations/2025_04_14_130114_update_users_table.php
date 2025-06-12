<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable()->after('updated_at');
            $table->bigInteger('created_by')->nullable()->after('deleted_at');
            $table->bigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'created_by', 'updated_by']);
        });
    }
};
