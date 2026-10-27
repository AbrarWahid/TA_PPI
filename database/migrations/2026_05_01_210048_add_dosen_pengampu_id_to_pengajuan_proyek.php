<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_proyek', function (Blueprint $table) {
            $table->unsignedBigInteger('dosen_pengampu_id')->nullable()->after('manager_id');
            $table->foreign('dosen_pengampu_id')->references('id')->on('dosen')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_proyek', function (Blueprint $table) {
            $table->dropForeign(['dosen_pengampu_id']);
            $table->dropColumn('dosen_pengampu_id');
        });
    }
};