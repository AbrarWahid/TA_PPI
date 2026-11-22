<?php

namespace App\Console\Commands;

use App\External\Models\Dosen as ExtDosen;
use App\External\Models\Mahasiswa as ExtMahasiswa;
use App\Models\Dosen as IntDosen;
use App\Models\Mahasiswa as IntMahasiswa;
use App\Services\ProfileSync;
use Illuminate\Console\Command;

class SyncProfiles extends Command
{
    protected $signature = 'profiles:sync {--dry-run : Hanya tampilkan hasil, tanpa menulis ke database}';

    protected $description = 'Sinkronkan profil mahasiswa & dosen antara modul Internal dan External';

    public function handle(ProfileSync $sync): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info($dry ? 'DRY RUN: tidak ada data yang ditulis.' : 'Menyinkronkan profil...');

        $jobs = [
            'Mahasiswa  Internal → External' => [IntMahasiswa::all(), fn ($m) => $sync->mahasiswaToExternal($m, $dry)],
            'Mahasiswa  External → Internal' => [ExtMahasiswa::all(), fn ($m) => $sync->mahasiswaToInternal($m, $dry)],
            'Dosen      Internal → External' => [IntDosen::all(),     fn ($d) => $sync->dosenToExternal($d, $dry)],
            'Dosen      External → Internal' => [ExtDosen::all(),     fn ($d) => $sync->dosenToInternal($d, $dry)],
        ];

        $rows = [];
        $notes = [];

        foreach ($jobs as $label => [$items, $fn]) {
            $count = [
                ProfileSync::DIBUAT => 0, ProfileSync::DIPERBARUI => 0,
                ProfileSync::SAMA => 0, ProfileSync::KONFLIK => 0, 'error' => 0,
            ];

            foreach ($items as $item) {
                $sync->notes = [];
                try {
                    $count[$fn($item)]++;
                } catch (\Throwable $e) {
                    $count['error']++;
                    $notes[] = "[$label] " . $e->getMessage();
                }
                foreach ($sync->notes as $n) {
                    $notes[] = "[$label] $n";
                }
            }

            $rows[] = [$label, ...array_values($count)];
        }

        $this->table(['Arah', 'Dibuat', 'Diperbarui', 'Sama', 'Konflik', 'Error'], $rows);

        if ($notes) {
            $this->warn('Perlu diperiksa manual:');
            foreach (array_slice($notes, 0, 30) as $n) {
                $this->line(' - ' . $n);
            }
            if (count($notes) > 30) {
                $this->line(' ... dan ' . (count($notes) - 30) . ' catatan lain (lihat storage/logs/laravel.log)');
            }
        }

        return self::SUCCESS;
    }
}
