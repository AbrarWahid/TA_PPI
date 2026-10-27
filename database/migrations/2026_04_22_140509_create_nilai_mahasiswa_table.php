<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa');
            $table->foreignId('rubrik_id')->constrained('rubrik_penilaian');
            $table->tinyInteger('skala_diberikan');
            $table->decimal('nilai_akhir', 6, 2)->nullable();
            $table->enum('penilai', ['manager_proyek','dosen']);
            $table->unique(['penilaian_id','mahasiswa_id','rubrik_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};