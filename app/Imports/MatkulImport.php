<?php

namespace App\Imports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class MatkulImport implements ToCollection, WithHeadingRow
{
    public array $warnings = [];
    public int   $inserted = 0;

    private array $validProdi = ['mekatronika', 'otomasi', 'informatika'];

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            $line = $i + 2; // baris di Excel (heading = baris 1)

            // Skip baris kosong
            if (empty(trim($row['kode_matkul'] ?? '')) && empty(trim($row['nama_matkul'] ?? ''))) {
                continue;
            }

            $kode        = strtoupper(trim($row['kode_matkul']  ?? ''));
            $nama        = trim($row['nama_matkul']             ?? '');
            $prodi       = strtolower(trim($row['program_studi'] ?? ''));
            $sks         = intval($row['sks']                   ?? 0);
            $semester    = intval($row['semester']              ?? 0);

            // Validasi kode_matkul
            if (empty($kode)) {
                $this->warnings[] = "Baris {$line}: Kode Matkul kosong — dilewati.";
                continue;
            }

            // Validasi nama_matkul
            if (empty($nama)) {
                $this->warnings[] = "Baris {$line}: Nama Matkul kosong — dilewati.";
                continue;
            }

            // Validasi program_studi
            if (!in_array($prodi, $this->validProdi)) {
                $this->warnings[] = "Baris {$line}: Program studi '{$prodi}' tidak valid (harus: mekatronika/otomasi/informatika) — dilewati.";
                continue;
            }

            // Validasi SKS
            if ($sks < 1 || $sks > 10) {
                $this->warnings[] = "Baris {$line}: SKS '{$sks}' tidak valid (1–10) — dilewati.";
                continue;
            }

            // Validasi Semester
            if ($semester < 1 || $semester > 8) {
                $this->warnings[] = "Baris {$line}: Semester '{$semester}' tidak valid (1–8) — dilewati.";
                continue;
            }

            // Cek duplikat kode_matkul
            if (MataKuliah::where('kode_matkul', $kode)->exists()) {
                $this->warnings[] = "Baris {$line}: Kode '{$kode}' sudah terdaftar — dilewati.";
                continue;
            }

            // Insert
            MataKuliah::create([
                'kode_matkul'   => $kode,
                'nama_matkul'   => $nama,
                'program_studi' => $prodi,
                'sks'           => $sks,
                'semester'      => $semester,
            ]);

            $this->inserted++;
        }
    }
}