<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('pengajuan_proyek_id')->constrained('pengajuan_proyek')->onDelete('cascade');
            $table->unsignedTinyInteger('minggu_ke');
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_dosen')->nullable();
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamps();

            // Satu mahasiswa hanya boleh punya 1 rekap per minggu per proyek
            $table->unique(['mahasiswa_id', 'pengajuan_proyek_id', 'minggu_ke'], 'lm_mhs_proyek_minggu_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_mingguan');
    }
};