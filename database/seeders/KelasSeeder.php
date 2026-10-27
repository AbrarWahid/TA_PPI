<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua nilai kelas unik dari tabel mahasiswa
        $kelasUnik = Mahasiswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas')
            ->sort()
            ->values();

        foreach ($kelasUnik as $namaKelas) {
            DB::table('kelas')->insertOrIgnore([
                'kode_kelas' => strtoupper(str_replace(' ', '', $namaKelas)),
                'nama_kelas' => $namaKelas,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}