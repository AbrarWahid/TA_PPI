<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('penilaian', 'kelas_id')) {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropColumn('kelas_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('penilaian', 'kelas_id')) {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->unsignedBigInteger('kelas_id')->nullable()->after('id');
            });
        }
    }
};