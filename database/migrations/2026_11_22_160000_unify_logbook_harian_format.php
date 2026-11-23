<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('logbook_harian', 'status')) {
                $table->string('status', 20)->default('Kerja')->after('tanggal');
            }
            if (!Schema::hasColumn('logbook_harian', 'durasi_mnt')) {
                $table->integer('durasi_mnt')->default(0)->after('jam_keluar');
            }
        });

        // Izinkan hari Sabtu & Minggu pada PostgreSQL agar rentang 5-7 hari per minggu seragam dengan External
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE logbook_harian DROP CONSTRAINT IF EXISTS logbook_harian_hari_check');
            DB::statement("ALTER TABLE logbook_harian ADD CONSTRAINT logbook_harian_hari_check CHECK (hari::text = ANY (ARRAY['Senin'::text, 'Selasa'::text, 'Rabu'::text, 'Kamis'::text, 'Jumat'::text, 'Sabtu'::text, 'Minggu'::text]))");
        }

        if (!Schema::hasTable('logbook_harian_fotos')) {
            Schema::create('logbook_harian_fotos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
                $table->foreignId('pengajuan_proyek_id')->constrained('pengajuan_proyek')->onDelete('cascade');
                $table->date('tanggal');
                $table->string('foto_path');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_harian_fotos');

        Schema::table('logbook_harian', function (Blueprint $table) {
            if (Schema::hasColumn('logbook_harian', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('logbook_harian', 'durasi_mnt')) {
                $table->dropColumn('durasi_mnt');
            }
        });
    }
};
