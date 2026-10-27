<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    public array $errors   = [];
    public int   $inserted = 0;
    public int   $updated  = 0; // Tambahan counter untuk data yang diupdate

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $no    = $index + 2; // baris 1 = header
            $nim   = trim($row['nim']   ?? '');
            $nama  = trim($row['nama']  ?? '');
            $prodi = strtolower(trim($row['prodi'] ?? ''));
            $kelas = trim($row['kelas'] ?? ''); // Menangkap data kelas dari CSV

            // Skip baris kosong
            if (empty($nim) && empty($nama)) continue;

            // Validasi dasar
            if (empty($nim)) {
                $this->errors[] = "Baris {$no}: NIM kosong, dilewati.";
                continue;
            }
            if (empty($nama)) {
                $this->errors[] = "Baris {$no}: Nama kosong, dilewati.";
                continue;
            }
            if (!in_array($prodi, ['mekatronika', 'otomasi', 'informatika'])) {
                $this->errors[] = "Baris {$no}: Prodi '{$row['prodi']}' tidak valid (NIM: {$nim}), dilewati.";
                continue;
            }

            // CEK APAKAH MAHASISWA SUDAH ADA
            $mahasiswaExist = Mahasiswa::where('nim', $nim)->first();

            if ($mahasiswaExist) {
                // JIKA SUDAH ADA: Update data kelasnya
                $mahasiswaExist->update([
                    'nama'  => $nama, // sekalian update nama jika ada perubahan ejaan
                    'kelas' => empty($kelas) ? $mahasiswaExist->kelas : $kelas,
                ]);
                
                // Update prodi di tabel user sekalian jika ada perubahan
                if ($mahasiswaExist->user) {
                    $mahasiswaExist->user->update(['prodi' => $prodi]);
                }

                $this->updated++;
                continue; // Lanjut ke baris excel berikutnya (skip insert)
            }

            // CEK APAKAH EMAIL USER BENTROK (NIM beda tapi email sama - jarang terjadi tapi untuk jaga-jaga)
            if (User::where('email', $nim . '@pbl.com')->exists()) {
                $this->errors[] = "Baris {$no}: Email sistem untuk NIM {$nim} bentrok, dilewati.";
                continue;
            }

            // JIKA BELUM ADA: Insert data baru
            $user = User::create([
                'name'     => $nama,
                'email'    => $nim . '@pbl.com',
                'password' => Hash::make($nim),
                'role'     => 'mahasiswa',
                'prodi'    => $prodi,
                'status'   => 'active',
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim'     => $nim,
                'nama'    => $nama,
                'kelas'   => empty($kelas) ? null : $kelas,
            ]);

            $this->inserted++;
        }
    }
}