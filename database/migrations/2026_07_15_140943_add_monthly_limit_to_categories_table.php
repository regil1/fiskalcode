<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah kolom sudah ada, jika belum, tambahkan
        if (!Schema::hasColumn('categories', 'monthly_limit')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->decimal('monthly_limit', 15, 2)->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'monthly_limit')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('monthly_limit');
            });
        }
    }
};
