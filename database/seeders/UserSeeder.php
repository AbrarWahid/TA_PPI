<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun manager proyek
        $managerId = DB::table('users')->insertGetId([
            'name'       => 'Admin Manager',
            'email'      => 'admin@pbl.com',
            'password'   => Hash::make('password123'),
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat akun dosen
        $dosenUserId = DB::table('users')->insertGetId([
            'name'       => 'Dr. Budi Santoso',
            'email'      => 'dosen@pbl.com',
            'password'   => Hash::make('password123'),
            'role'       => 'dosen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat akun mahasiswa
        $mhsUserId = DB::table('users')->insertGetId([
            'name'       => 'Ahmad Fauzi',
            'email'      => 'mahasiswa@pbl.com',
            'password'   => Hash::make('password123'),
            'role'       => 'mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data dosen
        DB::table('dosen')->insert([
            'user_id'    => $dosenUserId,
            'nidn'       => '0012345678',
            'nama_dosen' => 'Dr. Budi Santoso, M.T.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data mahasiswa
        DB::table('mahasiswa')->insert([
            'user_id'    => $mhsUserId,
            'nim'        => '21TI001',
            'nama'       => 'Ahmad Fauzi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data mata kuliah
        DB::table('mata_kuliah')->insert([
            'kode_matkul'   => 'TI601',
            'nama_matkul'   => 'Project Based Learning',
            'sks'           => 3,
            'semester'      => 6,
            'program_studi' => 'Teknik Informatika',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}