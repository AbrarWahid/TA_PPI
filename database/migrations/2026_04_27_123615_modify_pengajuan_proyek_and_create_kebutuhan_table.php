<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom kelas_id dari pengajuan_proyek
        Schema::table('pengajuan_proyek', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });

        // Buat tabel kebutuhan
        Schema::create('pengajuan_proyek_kebutuhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_proyek_id')->constrained('pengajuan_proyek')->onDelete('cascade');
            $table->enum('prodi', ['mekatronika', 'otomasi', 'informatika_industri']);
            $table->unsignedTinyInteger('jumlah_mahasiswa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_proyek_kebutuhan');

        Schema::table('pengajuan_proyek', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
        });
    }
};