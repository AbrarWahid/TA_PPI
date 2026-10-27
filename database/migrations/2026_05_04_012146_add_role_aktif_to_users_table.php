<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_aktif')->nullable()->after('role');
        });

        // Set role_aktif untuk dosen yang sudah ada
        DB::table('users')
            ->where('role', 'dosen')
            ->update(['role_aktif' => 'dosen_pengampu']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_aktif');
        });
    }
};