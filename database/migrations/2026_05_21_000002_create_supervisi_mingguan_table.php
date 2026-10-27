<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisi_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('supervisi_matkul_id')->nullable()->constrained('supervisi_matkul')->onDelete('set null');
            $table->unsignedBigInteger('pengajuan_proyek_id')->nullable();
            $table->unsignedTinyInteger('minggu_ke');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('restrict');
            $table->foreignId('matkul_id')->constrained('mata_kuliah')->onDelete('restrict');
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_dosen')->nullable();
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisi_mingguan');
    }
};