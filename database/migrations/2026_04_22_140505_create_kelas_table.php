<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas', 20)->unique();
            $table->string('nama_kelas', 50);
            $table->foreignId('matkul_id')->constrained('mata_kuliah');
            $table->foreignId('dosen_id')->constrained('dosen');
            $table->foreignId('manager_id')->constrained('users');
            $table->string('tahun_akademik', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};