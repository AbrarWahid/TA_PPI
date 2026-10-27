<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RubrikSeeder extends Seeder
{
    public function run(): void
    {
        $poin = json_encode([10, 25, 40, 55, 70, 85, 100]);
        $desc = json_encode([]);

        $rubrik = [
            ['RBK001','PBL','Learning Skills','a1','Critical Thinking',   0.0500],
            ['RBK002','PBL','Learning Skills','b1','Kolaborasi',          0.0500],
            ['RBK003','PBL','Learning Skills','c1','Kreativitas & Inovasi',0.0500],
            ['RBK004','PBL','Learning Skills','d1','Komunikasi',          0.0500],
            ['RBK005','PBL','Life Skills',    'a1','Fleksibilitas',       0.0500],
            ['RBK006','PBL','Life Skills',    'b1','Kepemimpinan',        0.0500],
            ['RBK007','PBL','Life Skills',    'c1','Produktivitas',       0.0500],
            ['RBK008','PBL','Life Skills',    'd1','Social Skill',        0.0500],
            ['RBK009','PBL','Laporan Project','a1','RPP',                 0.0500],
            ['RBK010','PBL','Laporan Project','b1','Logbook Mingguan',    0.0500],
            ['RBK011','PBL','Laporan Project','c1','Dokumen Projek',      0.0500],
            ['RBK012','PBL','Literacy Skills','a1','Literasi Informasi',  0.0500],
            ['RBK013','PBL','Literacy Skills','b1','Literasi Media',      0.0500],
            ['RBK014','PBL','Literacy Skills','c1','Literasi Teknologi',  0.0500],
            ['RBK015','PBL','Presentasi',     'a1','Konten',              0.0300],
            ['RBK016','PBL','Presentasi',     'b1','Tampilan Visual',     0.0300],
            ['RBK017','PBL','Presentasi',     'c1','Pemilihan Kosakata',  0.0300],
            ['RBK018','PBL','Presentasi',     'd1','Tanya Jawab',         0.0300],
            ['RBK019','PBL','Presentasi',     'e1','Mata & Gerak Tubuh',  0.0300],
            ['RBK020','PBL','Laporan Akhir',  'a1','Penulisan Laporan',   0.0500],
            ['RBK021','PBL','Laporan Akhir',  'b1','Pilihan Kata',        0.0500],
            ['RBK022','PBL','Laporan Akhir',  'c1','Konten Laporan',      0.0500],
        ];

        foreach ($rubrik as $r) {
            DB::table('rubrik_penilaian')->insert([
                'kode_rubrik'    => $r[0],
                'metode'         => $r[1],
                'kategori'       => $r[2],
                'aspek'          => $r[3],
                'sub_aspek'      => $r[4],
                'bobot_persen'   => $r[5],
                'poin_tersedia'  => $poin,
                'deskripsi_poin' => $desc,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}