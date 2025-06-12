<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Tambahkan timestamps dulu
            if (!Schema::hasColumn('menus', 'created_at') && !Schema::hasColumn('menus', 'updated_at')) {
                $table->timestamps(); // Ini harus ditaruh sebelum penggunaan 'after updated_at'
            }

            // Baru tambahkan audit trail setelah timestamps ada
            if (!Schema::hasColumn('menus', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('menus', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }

            // Soft deletes
            if (!Schema::hasColumn('menus', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }


    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Menghapus kolom dengan aman saat rollback migrasi.
            if (Schema::hasColumn('menus', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('menus', 'created_by')) {
                $table->dropColumn(['created_by']);
            }

            if (Schema::hasColumn('menus', 'updated_by')) {
                $table->dropColumn(['updated_by']);
            }

            if (Schema::hasColumn('menus', 'created_at') && Schema::hasColumn("menus", "updated_at")) {
                $table->dropTimestamps();  // Ini akan menghapus created_at dan updated_at secara bersamaan.
            }
        });
    }
};
