<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\LogbookMingguan;
use App\Models\LaporanMkPpi;
use App\Models\PenilaianManager;
use App\Models\PenilaianDosen;
use App\Models\PengajuanProyek;
use App\Models\User;
use App\Models\SupervisiMatkul;
use App\Models\PeriodePbl;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isMahasiswa()) {
            return $this->mahasiswaDashboard();
        } elseif ($user->role === 'dosen') {
            $roleAktif = $user->role_aktif ?? 'dosen_pengampu';
            if ($roleAktif === 'manager_proyek') {
                return $this->managerDashboard();
            } else {
                return $this->dosenDashboard();
            }
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        }

        abort(403);
    }

    private function adminDashboard()
    {
        $stats = [
            'total_mahasiswa'    => Mahasiswa::count(),
            'total_proyek_aktif' => PengajuanProyek::where('status', 'approved')->count(),
            'pending_akun'       => User::whereIn('role', ['dosen', 'manager_proyek', 'mahasiswa'])
                                        ->where('status', 'pending')->count(),
            'pending_proyek'     => PengajuanProyek::where('status', 'pending')->count(),
        ];

        $total        = $stats['total_mahasiswa'] ?: 1;
        $sudahDinilai = Mahasiswa::whereHas('penilaianManager')->whereHas('penilaianDosen')->count();
        $stats['progress_pct'] = round($sudahDinilai / $total * 100);

        $logbookPending = LogbookMingguan::where('status', 'pending')
            ->with(['mahasiswa'])
            ->latest()
            ->take(5)
            ->get();

        $laporanPending = LaporanMkPpi::where('status_verifikasi', 'pending')
            ->with(['mahasiswa'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'logbookPending', 'laporanPending'));
    }

    private function managerDashboard()
    {
        $user = auth()->user();

        $proyekList = PengajuanProyek::where('manager_id', $user->id)
            ->with(['mahasiswa.user', 'kebutuhan'])
            ->latest()
            ->get();

        $totalProyek     = $proyekList->count();
        $proyekDisetujui = $proyekList->where('status', 'approved')->count();
        $proyekPending   = $proyekList->where('status', 'pending')->count();
        $totalMahasiswa  = $proyekList->sum(fn($p) => $p->mahasiswa->count());

        $stats = [
            'total_proyek'     => $totalProyek,
            'proyek_disetujui' => $proyekDisetujui,
            'proyek_pending'   => $proyekPending,
            'total_mahasiswa'  => $totalMahasiswa,
        ];

        return view('manager.dashboard', compact(
            'proyekList', 'stats',
            'totalProyek', 'proyekDisetujui',
            'proyekPending', 'totalMahasiswa'
        ));
    }

    private function dosenDashboard()
    {
        $user  = auth()->user();
        $dosen = $user->dosen;

        $supervisiList = \App\Models\SupervisiMatkul::where('dosen_id', $dosen->id)
            ->with('mahasiswa')
            ->get();

        $mahasiswaIds = $supervisiList->pluck('mahasiswa_id')->unique();
        $totalMahasiswa = $mahasiswaIds->count();

        $laporanMenunggu = LaporanMkPpi::whereIn('mahasiswa_id', $mahasiswaIds)
            ->where('status_verifikasi', 'pending')
            ->count();

        $penilaianSelesai = PenilaianDosen::whereIn('mahasiswa_id', $mahasiswaIds)
            ->whereNotNull('nilai_dosen')
            ->count();

        $laporanTerbaru = LaporanMkPpi::whereIn('mahasiswa_id', $mahasiswaIds)
            ->with(['mahasiswa'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_proyek'      => 0,
            'total_mahasiswa'   => $totalMahasiswa,
            'laporan_menunggu'  => $laporanMenunggu,
            'penilaian_selesai' => $penilaianSelesai,
        ];

        $proyekList = collect();
        $totalProyek = 0;

        return view('dosen.dashboard', compact(
            'proyekList', 'stats',
            'totalProyek', 'totalMahasiswa',
            'laporanMenunggu', 'penilaianSelesai',
            'laporanTerbaru', 'supervisiList'
        ));
    }

    private function mahasiswaDashboard()
    {
        $user = auth()->user();
        $mhs  = $user->mahasiswa;

        // Proyek yang ditugaskan
        $proyekDitugaskan = $mhs
            ? PengajuanProyek::whereHas('mahasiswa', function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->id);
                })
                ->with(['kebutuhan', 'manager'])
                ->get()
            : collect();

        // Supervisi Matkul
        $supervisiList = $mhs
            ? \App\Models\SupervisiMatkul::where('mahasiswa_id', $mhs->id)
                ->with(['mataKuliah', 'dosen'])
                ->get()
            : collect();

        // Data Penilaian & Perbaikan Bug Kalkulasi 188.8
        $periode = PeriodePbl::getAktif();
        $bobotManager = $periode ? $periode->bobot_manager : 55;
        $bobotDosen   = $periode ? $periode->bobot_dosen   : 45;

        $nilaiManager = PenilaianManager::where('mahasiswa_id', $mhs?->id)->first();
        $nilaiDosen   = PenilaianDosen::where('mahasiswa_id', $mhs?->id)->get();

        $nmSkala100 = $nilaiManager->nilai_manager ?? 0;
        $ndSkala100 = $nilaiDosen->count() > 0 ? $nilaiDosen->avg('nilai_dosen') : 0;

        $nmBobot = $nmSkala100 * ($bobotManager / 100);
        $ndBobot = $ndSkala100 * ($bobotDosen / 100);

        $nilaiAkhir = null;
        if ($nilaiManager || $nilaiDosen->count() > 0) {
            $nilaiAkhir = round($nmBobot + $ndBobot, 1);
        }

        $grade = '-';
        if ($nilaiAkhir !== null) {
            $grade = match(true) {
                $nilaiAkhir >= 85 => 'A',
                $nilaiAkhir >= 75 => 'B',
                $nilaiAkhir >= 65 => 'C',
                $nilaiAkhir >= 55 => 'D',
                default           => 'E',
            };
        }

        // Statistik Logbook Harian (Disetujui, Pending, Ditolak)
        $logbookHarian = \App\Models\LogbookHarian::where('mahasiswa_id', $mhs?->id)->get();
        $lbStats = [
            'pending'   => $logbookHarian->where('status_verifikasi', 'pending')->count(),
            'disetujui' => $logbookHarian->where('status_verifikasi', 'disetujui')->count(),
            'ditolak'   => $logbookHarian->where('status_verifikasi', 'ditolak')->count(),
            'total'     => $logbookHarian->count(),
        ];

        // Statistik Supervisi Harian (Disetujui, Pending, Ditolak)
        $supervisiHarian = \App\Models\SupervisiHarian::whereHas('supervisiMatkul', function($q) use ($mhs) {
            $q->where('mahasiswa_id', $mhs?->id);
        })->get();
        $svStats = [
            'pending'   => $supervisiHarian->where('status_verifikasi', 'pending')->count(),
            'disetujui' => $supervisiHarian->where('status_verifikasi', 'disetujui')->count(),
            'ditolak'   => $supervisiHarian->where('status_verifikasi', 'ditolak')->count(),
            'total'     => $supervisiHarian->count(),
        ];

        // Logbook Terbaru untuk List
        $logbookTerbaru = \App\Models\LogbookHarian::where('mahasiswa_id', $mhs?->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'mhs', 'nilaiAkhir', 'grade', 
            'logbookTerbaru', 'proyekDitugaskan', 'supervisiList',
            'lbStats', 'svStats'
        ));
    }
}