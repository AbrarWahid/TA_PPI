<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_mk_ppi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 30)->unique();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->enum('jenis_laporan', ['Supervisi','Laporan Teknik','PAB']);
            $table->string('file_path', 255);
            $table->enum('status_verifikasi', ['pending','disetujui','ditolak'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_mk_ppi');
    }
};