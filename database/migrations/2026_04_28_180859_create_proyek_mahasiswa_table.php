<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyek_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_proyek_id')->constrained('pengajuan_proyek')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->enum('prodi', ['mekatronika', 'otomasi', 'informatika']);
            $table->unique(['pengajuan_proyek_id', 'mahasiswa_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyek_mahasiswa');
    }
};