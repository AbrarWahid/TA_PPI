<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "parameter_penilaians" DROP CONSTRAINT IF EXISTS "parameter_penilaians_jenis_check"');
            DB::statement("ALTER TABLE \"parameter_penilaians\" ADD CONSTRAINT \"parameter_penilaians_jenis_check\" CHECK (jenis IN ('presentasi', 'makalah', 'saran_mahasiswa'))");
        } else {
            DB::statement("ALTER TABLE parameter_penilaians MODIFY COLUMN jenis ENUM('presentasi', 'makalah', 'saran_mahasiswa') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "parameter_penilaians" DROP CONSTRAINT IF EXISTS "parameter_penilaians_jenis_check"');
            DB::statement("ALTER TABLE \"parameter_penilaians\" ADD CONSTRAINT \"parameter_penilaians_jenis_check\" CHECK (jenis IN ('presentasi', 'makalah'))");
        } else {
            DB::statement("ALTER TABLE parameter_penilaians MODIFY COLUMN jenis ENUM('presentasi', 'makalah') NOT NULL");
        }
    }
};
