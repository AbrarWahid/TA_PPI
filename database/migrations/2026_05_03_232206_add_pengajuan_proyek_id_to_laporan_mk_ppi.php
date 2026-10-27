<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_mk_ppi', function (Blueprint $table) {
            $table->unsignedBigInteger('pengajuan_proyek_id')->nullable()->after('mahasiswa_id');
            $table->foreign('pengajuan_proyek_id')->references('id')->on('pengajuan_proyek')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_mk_ppi', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_proyek_id']);
            $table->dropColumn('pengajuan_proyek_id');
        });
    }
};