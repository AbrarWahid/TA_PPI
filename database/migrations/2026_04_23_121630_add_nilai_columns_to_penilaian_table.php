<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('kelas_id');
            $table->unsignedBigInteger('dosen_id')->nullable()->after('mahasiswa_id');
            $table->decimal('nilai_logbook', 5, 2)->nullable()->after('dosen_id');
            $table->decimal('nilai_laporan', 5, 2)->nullable()->after('nilai_logbook');
            $table->decimal('nilai_presentasi', 5, 2)->nullable()->after('nilai_laporan');
            $table->decimal('nilai_akhir', 5, 2)->nullable()->after('nilai_presentasi');
            $table->text('catatan')->nullable()->after('nilai_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn([
                'mahasiswa_id','dosen_id',
                'nilai_logbook','nilai_laporan','nilai_presentasi','nilai_akhir','catatan'
            ]);
        });
    }
};