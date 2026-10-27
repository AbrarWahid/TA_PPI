<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_pbl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->string('tahun_ajaran'); // "2026/2027"
            $table->enum('semester', ['ganjil', 'genap']);
            $table->foreignId('koordinator_id')->constrained('users')->onDelete('restrict');
            $table->enum('mekanisme_proyek', ['apply', 'direct'])->default('apply');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('deadline_pengajuan')->nullable();
            $table->date('deadline_lamaran')->nullable();
            $table->enum('frekuensi_logbook', ['harian', 'mingguan', 'custom'])->default('mingguan');
            $table->tinyInteger('hari_deadline')->nullable(); // 0=Minggu, 5=Jumat
            $table->unsignedTinyInteger('bobot_manager')->default(55);
            $table->unsignedTinyInteger('bobot_dosen')->default(45);
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_pbl');
    }
};