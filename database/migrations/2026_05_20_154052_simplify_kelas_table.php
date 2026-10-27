<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['matkul_id']);
            $table->dropForeign(['dosen_id']);
            $table->dropForeign(['manager_id']);

            // Hapus kolom yang tidak diperlukan
            $table->dropColumn(['matkul_id', 'dosen_id', 'manager_id', 'tahun_akademik']);

            // Jadikan nama_kelas unique
            $table->unique('nama_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique(['nama_kelas']);
            $table->foreignId('matkul_id')->constrained('mata_kuliah');
            $table->foreignId('dosen_id')->constrained('dosen');
            $table->foreignId('manager_id')->constrained('users');
            $table->string('tahun_akademik', 10);
        });
    }
};