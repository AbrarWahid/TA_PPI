<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class DosenImport implements ToCollection, WithHeadingRow
{
    public array $errors   = [];
    public int   $inserted = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $no         = $index + 2;
            $nidn       = trim($row['nidn']       ?? '');
            $nama_dosen = trim($row['nama_dosen']  ?? '');
            $email      = strtolower(trim($row['email'] ?? ''));
            $akses_role = strtolower(trim($row['akses_role'] ?? ''));

            // Skip baris kosong
            if (empty($nidn) && empty($nama_dosen)) continue;

            // Validasi kolom wajib
            if (empty($nidn)) {
                $this->errors[] = "Baris {$no}: NIDN kosong, dilewati.";
                continue;
            }
            if (empty($nama_dosen)) {
                $this->errors[] = "Baris {$no}: Nama kosong, dilewati.";
                continue;
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Baris {$no}: Email tidak valid (NIDN: {$nidn}), dilewati.";
                continue;
            }
            if (!in_array($akses_role, ['dosen_pengampu', 'manager_proyek', 'keduanya'])) {
                $this->errors[] = "Baris {$no}: akses_role '{$row['akses_role']}' tidak valid (NIDN: {$nidn}), dilewati.";
                continue;
            }

            // Cek duplikat
            if (Dosen::where('nidn', $nidn)->exists()) {
                $this->errors[] = "Baris {$no}: NIDN {$nidn} sudah terdaftar, dilewati.";
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $this->errors[] = "Baris {$no}: Email {$email} sudah terdaftar, dilewati.";
                continue;
            }

            // Tentukan role_aktif
            $role_aktif = match($akses_role) {
                'manager_proyek' => 'manager_proyek',
                default          => 'dosen_pengampu',
            };

            // Insert
            $user = User::create([
                'name'       => $nama_dosen,
                'email'      => $email,
                'password'   => Hash::make('password123'),
                'role'       => 'dosen',
                'role_aktif' => $role_aktif,
                'akses_role' => $akses_role,
                'status'     => 'active',
            ]);

            Dosen::create([
                'user_id'    => $user->id,
                'nidn'       => $nidn,
                'nama_dosen' => $nama_dosen,
            ]);

            $this->inserted++;
        }
    }
}