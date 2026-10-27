<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel penilaian lama (disable FK check dulu)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TABLE IF EXISTS "penilaian" CASCADE');
        } else {
            Schema::dropIfExists('penilaian');
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Tabel penilaian Manager (55%)
        Schema::create('penilaian_manager', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('pengajuan_proyek_id')->constrained('pengajuan_proyek')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');

            // Learning Skills (20%)
            $table->integer('ls_critical_thinking')->default(0);
            $table->integer('ls_kolaborasi')->default(0);
            $table->integer('ls_kreativitas')->default(0);
            $table->integer('ls_komunikasi')->default(0);

            // Life Skills (20%)
            $table->integer('lf_fleksibilitas')->default(0);
            $table->integer('lf_kepemimpinan')->default(0);
            $table->integer('lf_produktivitas')->default(0);
            $table->integer('lf_social_skill')->default(0);

            // Laporan Project (15%)
            $table->integer('lp_rpp')->default(0);
            $table->integer('lp_logbook')->default(0);
            $table->integer('lp_dokumen_projek')->default(0);

            $table->decimal('nilai_manager', 5, 2)->default(0);
            $table->text('catatan_manager')->nullable();

            $table->timestamps();

            $table->unique(['mahasiswa_id', 'pengajuan_proyek_id'], 'unique_penilaian_manager');
        });

        // Tabel penilaian Dosen (45%)
        Schema::create('penilaian_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('supervisi_matkul_id')->constrained('supervisi_matkul')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');

            // Literacy Skills (15%)
            $table->integer('lit_informasi')->default(0);
            $table->integer('lit_media')->default(0);
            $table->integer('lit_teknologi')->default(0);

            // Presentasi (15%)
            $table->integer('pr_konten')->default(0);
            $table->integer('pr_visual')->default(0);
            $table->integer('pr_kosakata')->default(0);
            $table->integer('pr_tanya_jawab')->default(0);
            $table->integer('pr_mata_gerak')->default(0);

            // Laporan Akhir (15%)
            $table->integer('la_penulisan')->default(0);
            $table->integer('la_pilihan_kata')->default(0);
            $table->integer('la_konten')->default(0);

            $table->decimal('nilai_dosen', 5, 2)->default(0);
            $table->text('catatan_dosen')->nullable();

            $table->timestamps();

            $table->unique(['mahasiswa_id', 'supervisi_matkul_id'], 'unique_penilaian_dosen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_dosen');
        Schema::dropIfExists('penilaian_manager');
    }
};