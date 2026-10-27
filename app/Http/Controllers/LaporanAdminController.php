<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PengajuanProyek;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanAdminController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $roleAktif = $authUser->getRoleEfektif();

        $proyekId    = $request->get('proyek_id');
        $filterProdi = $request->get('prodi');
        $filterKelas = $request->get('kelas');

        // Jika KPS → scope otomatis ke prodi KPS, tidak bisa ganti prodi
        $isKps = $roleAktif === 'kps';
        $kpsProdi = null;
        if ($isKps) {
            $kpsRole  = $authUser->userRoles()->where('role', 'kps')->first();
            $kpsProdi = $kpsRole?->prodi;
            $filterProdi = $kpsProdi; // paksa scope
        }

        $proyekList = PengajuanProyek::where('status', 'approved')
                        ->orderBy('judul_proyek')->get();

        // Daftar prodi & kelas untuk dropdown filter (Admin only)
        $prodiList = ['informatika' => 'Informatika', 'mekatronika' => 'Mekatronika', 'otomasi' => 'Otomasi'];
        
        // Mapping prodi → prefix kelas
        $prodiKelasPrefix = [
            'informatika' => 'AEC',
            'mekatronika' => 'AEA',
            'otomasi'     => 'AEB',
        ];

        $kelasQuery = Kelas::orderBy('nama_kelas');

        // Jika KPS → filter kelas sesuai prodinya
        if ($isKps && $kpsProdi && isset($prodiKelasPrefix[$kpsProdi])) {
            $prefix = $prodiKelasPrefix[$kpsProdi];
            $kelasQuery->where('nama_kelas', 'like', '%' . $prefix . '%');
        }

        // Jika Admin pilih prodi tertentu → filter kelas juga
        if (!$isKps && $filterProdi && isset($prodiKelasPrefix[$filterProdi])) {
            $prefix = $prodiKelasPrefix[$filterProdi];
            $kelasQuery->where('nama_kelas', 'like', '%' . $prefix . '%');
        }

        $kelasList = $kelasQuery->pluck('nama_kelas');

        // Query mahasiswa
        $query = Mahasiswa::with([
            'user',
            'proyek',
            'logbook',
            'logbookHarian',
            'laporan',
        ]);

        // Filter prodi (via relasi user)
        if ($filterProdi) {
            $query->whereHas('user', fn($q) => $q->where('prodi', $filterProdi));
        }

        // Filter kelas
        if ($filterKelas) {
            $query->where('kelas', $filterKelas);
        }

        $mahasiswaList = $query->get();

        // Filter per proyek
        if ($proyekId) {
            $mahasiswaList = $mahasiswaList->filter(function ($m) use ($proyekId) {
                return $m->proyek->contains('id', (int)$proyekId);
            })->values();
        }

        $mahasiswaProgress = $mahasiswaList->map(function ($m) {
            $proyekAktif     = $m->proyek->where('status', 'approved')->first();
            $totalLogbook    = $m->logbook->count();
            $terverifikasi   = $m->logbook->where('status', 'disetujui')->count();
            $totalHarian     = $m->logbookHarian->count();
            
            // "Laporan" diubah perannya menjadi "Supervisi Mingguan" yang jumlah batasnya adalah 2.
            $totalLaporan    = $m->laporan->count();
            $laporanVerified = $m->laporan->where('status_verifikasi', 'disetujui')->count();

            // Progress dihitung dari Logbook terverifikasi dibagi total maksimum (8)
            $progressLogbook = round(($terverifikasi / 8) * 100);
            if ($progressLogbook > 100) $progressLogbook = 100;

            $status = 'Belum Mulai';
            if ($totalLogbook > 0 || $totalLaporan > 0) {
                // Selesai jika logbook terverifikasi 8 dan supervisi mingguan terverifikasi 2
                $allSupervisiDone = $laporanVerified >= 2;
                $status = ($terverifikasi >= 8 && $allSupervisiDone) ? 'Selesai' : 'Berjalan';
            }

            return (object)[
                'id'               => $m->id,
                'nama'             => $m->nama,
                'nim'              => $m->nim,
                'prodi'            => $m->user?->prodi ?? '-',
                'kelas'            => $m->kelas ?? '-',
                'proyek'           => $proyekAktif?->judul_proyek ?? '-',
                'total_logbook'    => $totalLogbook,
                'logbook_verified' => $terverifikasi,
                'progress_logbook' => $progressLogbook,
                'total_harian'     => $totalHarian,
                'total_laporan'    => $totalLaporan, // Akan kita sebut "supervisi mingguan" di view
                'laporan_verified' => $laporanVerified,
                'status'           => $status,
            ];
        })->values();

        $totalMahasiswa = $mahasiswaList->count();
        $sudahSelesai   = $mahasiswaProgress->where('status', 'Selesai')->count();
        $sedangBerjalan = $mahasiswaProgress->where('status', 'Berjalan')->count();
        $belumMulai     = $mahasiswaProgress->where('status', 'Belum Mulai')->count();

        if ($request->get('export')) {
            return $this->exportCsv($mahasiswaProgress);
        }

        return view('laporan.admin', compact(
            'mahasiswaProgress',
            'proyekList',
            'proyekId',
            'filterProdi',
            'filterKelas',
            'prodiList',
            'kelasList',
            'isKps',
            'kpsProdi',
            'totalMahasiswa',
            'sudahSelesai',
            'sedangBerjalan',
            'belumMulai'
        ));
    }

    private function exportCsv($mahasiswaProgress)
    {
        $filename = 'monitoring-aktivitas-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($mahasiswaProgress) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No', 'NIM', 'Nama Mahasiswa', 'Program Studi', 'Kelas', 'Proyek',
                'Logbook Mingguan (/8)', 'Logbook Mingguan Verified', 'Progress (%)',
                'Total Harian', 'Supervisi Mingguan (/2)', 'Supervisi Mingguan Verified', 'Status'
            ]);

            foreach ($mahasiswaProgress as $i => $r) {
                fputcsv($file, [
                    $i + 1,
                    $r->nim,
                    $r->nama,
                    $r->prodi,
                    $r->kelas,
                    $r->proyek,
                    $r->total_logbook,
                    $r->logbook_verified,
                    $r->progress_logbook . '%',
                    $r->total_harian,
                    $r->total_laporan,
                    $r->laporan_verified,
                    $r->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}