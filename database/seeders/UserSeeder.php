<?php

namespace Database\Seeders;

use App\External\Models\PembimbingIndustri;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@pbl.com'],
            [
                'name'     => 'Admin PPI',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // 2. Akun Dosen (akses: Dosen Pengampu + Manager Proyek + KPS Informatika)
        $dosenUser = User::updateOrCreate(
            ['email' => 'dosen@pbl.com'],
            [
                'name'       => 'Dr. Budi Santoso',
                'password'   => Hash::make('password123'),
                'role'       => 'dosen',
                'prodi'      => 'informatika',
                'akses_role' => 'keduanya',
                'role_aktif' => 'dosen_pengampu',
                'status'     => 'active',
            ]
        );

        Dosen::updateOrCreate(
            ['user_id' => $dosenUser->id],
            [
                'nidn'       => '0012345678',
                'nama_dosen' => 'Dr. Budi Santoso, M.T.',
            ]
        );

        UserRole::updateOrCreate(
            ['user_id' => $dosenUser->id, 'role' => 'kps'],
            ['prodi' => 'informatika']
        );

        // 3. Akun Manager Proyek (khusus Manager Proyek)
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@pbl.com'],
            [
                'name'       => 'Ir. Hendra Wijaya (Manager Proyek)',
                'password'   => Hash::make('password123'),
                'role'       => 'dosen',
                'prodi'      => 'informatika',
                'akses_role' => 'manager_proyek',
                'role_aktif' => 'manager_proyek',
                'status'     => 'active',
            ]
        );

        Dosen::updateOrCreate(
            ['user_id' => $managerUser->id],
            [
                'nidn'       => '0087654321',
                'nama_dosen' => 'Ir. Hendra Wijaya, M.T.',
            ]
        );

        // 4. Akun Mentor Industri (Pembimbing PT)
        $mentorUser = User::updateOrCreate(
            ['email' => 'mentor@pbl.com'],
            [
                'name'     => 'Rizky Pratama (Mentor PT)',
                'password' => Hash::make('password123'),
                'role'     => 'mentor',
                'status'   => 'active',
            ]
        );

        PembimbingIndustri::updateOrCreate(
            ['user_id' => $mentorUser->id],
            [
                'nama_pem'   => 'Rizky Pratama',
                'perusahaan' => 'PT. Industri Nusantara',
            ]
        );

        // 5. Akun Mahasiswa
        $mhsUser = User::updateOrCreate(
            ['email' => 'mahasiswa@pbl.com'],
            [
                'name'     => 'Ahmad Fauzi',
                'password' => Hash::make('password123'),
                'role'     => 'mahasiswa',
                'prodi'    => 'informatika',
                'status'   => 'active',
            ]
        );

        Mahasiswa::updateOrCreate(
            ['user_id' => $mhsUser->id],
            [
                'nim'   => '21TI001',
                'nama'  => 'Ahmad Fauzi',
                'kelas' => '3 AEA 1',
            ]
        );

        // Data mata kuliah contoh
        DB::table('mata_kuliah')->updateOrInsert(
            ['kode_matkul' => 'TI601'],
            [
                'nama_matkul'   => 'Project Based Learning',
                'sks'           => 3,
                'semester'      => 6,
                'program_studi' => 'Teknik Informatika',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );

        // 6. Periode PBL Aktif (agar Mahasiswa langsung bisa mengisi Logbook Dalam Kampus)
        \App\Models\PeriodePbl::updateOrCreate(
            ['nama_periode' => 'Periode PPI Semester Ganjil 2026/2027'],
            [
                'tahun_ajaran'      => '2026/2027',
                'semester'          => 'ganjil',
                'koordinator_id'    => $dosenUser->id,
                'mekanisme_proyek'  => 'direct',
                'tanggal_mulai'     => now()->subMonth()->toDateString(),
                'tanggal_selesai'   => now()->addMonths(5)->toDateString(),
                'frekuensi_logbook' => 'harian',
                'bobot_manager'     => 55,
                'bobot_dosen'       => 45,
                'is_aktif'          => true,
            ]
        );

        // 7. Plotting Supervisi Mata Kuliah untuk Mahasiswa
        $matkul = \App\Models\MataKuliah::where('kode_matkul', 'TI601')->first();
        $dosen  = Dosen::where('user_id', $dosenUser->id)->first();
        if ($matkul && $dosen) {
            foreach (Mahasiswa::all() as $mhs) {
                \App\Models\SupervisiMatkul::updateOrCreate(
                    [
                        'mahasiswa_id'   => $mhs->id,
                        'mata_kuliah_id' => $matkul->id,
                        'tahun_ajaran'   => '2026/2027',
                        'semester'       => 'ganjil',
                    ],
                    [
                        'dosen_id' => $dosen->id,
                    ]
                );
            }
        }
    }
}
