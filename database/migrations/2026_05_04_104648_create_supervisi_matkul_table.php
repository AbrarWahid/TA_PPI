<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisi_matkul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->string('tahun_ajaran', 10); // contoh: 2025/2026
            $table->tinyInteger('semester');     // 1 atau 2
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'mata_kuliah_id', 'tahun_ajaran', 'semester'], 'unique_supervisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisi_matkul');
    }
};