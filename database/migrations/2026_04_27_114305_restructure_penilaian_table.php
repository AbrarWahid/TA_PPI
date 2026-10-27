<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom lama, tambah kolom sub-aspek baru
        Schema::table('penilaian', function (Blueprint $table) {
            // Hapus kolom lama jika ada
            $table->dropColumn([
                'nilai_logbook',
                'nilai_laporan',
                'nilai_presentasi',
                'nilai_akhir',
                'kode_penilaian',
                'tanggal_penilaian',
                'status',
                'catatan',
            ]);
        });

        Schema::table('penilaian', function (Blueprint $table) {
            // =============================================
            // MANAGER PROYEK (55%)
            // =============================================

            // Learning Skills (20%)
            $table->decimal('ls_critical_thinking', 5, 2)->nullable()->comment('5%');
            $table->decimal('ls_kolaborasi', 5, 2)->nullable()->comment('5%');
            $table->decimal('ls_kreativitas', 5, 2)->nullable()->comment('5%');
            $table->decimal('ls_komunikasi', 5, 2)->nullable()->comment('5%');

            // Life Skills (20%)
            $table->decimal('lf_fleksibilitas', 5, 2)->nullable()->comment('5%');
            $table->decimal('lf_kepemimpinan', 5, 2)->nullable()->comment('5%');
            $table->decimal('lf_produktivitas', 5, 2)->nullable()->comment('5%');
            $table->decimal('lf_social_skill', 5, 2)->nullable()->comment('5%');

            // Laporan Project (15%)
            $table->decimal('lp_rpp', 5, 2)->nullable()->comment('5%');
            $table->decimal('lp_logbook', 5, 2)->nullable()->comment('5%');
            $table->decimal('lp_dokumen_projek', 5, 2)->nullable()->comment('5%');

            // Nilai Manager (55%)
            $table->decimal('nilai_manager', 5, 2)->nullable();

            // =============================================
            // DOSEN PENGAMPU (45%)
            // =============================================

            // Literacy Skills (15%)
            $table->decimal('lit_informasi', 5, 2)->nullable()->comment('5%');
            $table->decimal('lit_media', 5, 2)->nullable()->comment('5%');
            $table->decimal('lit_teknologi', 5, 2)->nullable()->comment('5%');

            // Presentasi (15%)
            $table->decimal('pr_konten', 5, 2)->nullable()->comment('3%');
            $table->decimal('pr_visual', 5, 2)->nullable()->comment('3%');
            $table->decimal('pr_kosakata', 5, 2)->nullable()->comment('3%');
            $table->decimal('pr_tanya_jawab', 5, 2)->nullable()->comment('3%');
            $table->decimal('pr_mata_gerak', 5, 2)->nullable()->comment('3%');

            // Laporan Akhir (15%)
            $table->decimal('la_penulisan', 5, 2)->nullable()->comment('5%');
            $table->decimal('la_pilihan_kata', 5, 2)->nullable()->comment('5%');
            $table->decimal('la_konten', 5, 2)->nullable()->comment('5%');

            // Nilai Dosen (45%)
            $table->decimal('nilai_dosen', 5, 2)->nullable();

            // =============================================
            // NILAI AKHIR GABUNGAN
            // =============================================
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan_manager')->nullable();
            $table->text('catatan_dosen')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn([
                'ls_critical_thinking','ls_kolaborasi','ls_kreativitas','ls_komunikasi',
                'lf_fleksibilitas','lf_kepemimpinan','lf_produktivitas','lf_social_skill',
                'lp_rpp','lp_logbook','lp_dokumen_projek','nilai_manager',
                'lit_informasi','lit_media','lit_teknologi',
                'pr_konten','pr_visual','pr_kosakata','pr_tanya_jawab','pr_mata_gerak',
                'la_penulisan','la_pilihan_kata','la_konten','nilai_dosen',
                'nilai_akhir','catatan_manager','catatan_dosen',
            ]);

            // Restore kolom lama
            $table->decimal('nilai_logbook', 5, 2)->nullable();
            $table->decimal('nilai_laporan', 5, 2)->nullable();
            $table->decimal('nilai_presentasi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('kode_penilaian')->nullable();
            $table->date('tanggal_penilaian')->nullable();
            $table->string('status')->nullable();
            $table->text('catatan')->nullable();
        });
    }
};