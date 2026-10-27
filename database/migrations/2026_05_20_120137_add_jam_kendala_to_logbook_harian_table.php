<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_harian', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable()->after('tanggal');
            $table->time('jam_keluar')->nullable()->after('jam_masuk');
            $table->text('kendala')->nullable()->after('aktivitas');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_harian', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'jam_keluar', 'kendala']);
        });
    }
};