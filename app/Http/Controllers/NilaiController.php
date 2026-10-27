<?php

namespace App\Http\Controllers;

use App\Models\PenilaianManager;
use App\Models\PenilaianDosen;
use App\Models\LogbookMingguan;
use App\Models\LaporanMkPpi;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return view('mahasiswa.nilai', [
                'nilaiManager' => null,
                'nilaiDosen'   => collect(),
                'mahasiswa'    => null,
            ]);
        }

        $nilaiManager = PenilaianManager::where('mahasiswa_id', $mahasiswa->id)
            ->with('pengajuanProyek')
            ->first();

        $nilaiDosen = PenilaianDosen::where('mahasiswa_id', $mahasiswa->id)
            ->with('supervisiMatkul.mataKuliah')
            ->get();

        return view('mahasiswa.nilai', compact('nilaiManager', 'nilaiDosen', 'mahasiswa'));
    }

    public function feedbackHub()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return view('mahasiswa.feedback', [
                'mahasiswa'     => null,
                'feedbackList'  => collect(),
                'totalFeedback' => 0,
                'feedbackPerTipe' => [],
            ]);
        }

        $feedbackList = collect();

        // 1. Feedback dari catatan PenilaianManager
        $nilaiManager = PenilaianManager::where('mahasiswa_id', $mahasiswa->id)
            ->with('pengajuanProyek')
            ->get();

        foreach ($nilaiManager as $p) {
            $proyekJudul = $p->pengajuanProyek?->judul_proyek ?? 'Proyek';

            if (!empty($p->catatan_manager)) {
                $feedbackList->push([
                    'tipe'    => 'penilaian',
                    'sumber'  => 'Manager Proyek',
                    'icon'    => 'manage_accounts',
                    'color'   => 'primary',
                    'konteks' => $proyekJudul,
                    'pesan'   => $p->catatan_manager,
                    'tanggal' => $p->updated_at,
                    'badge'   => 'Penilaian',
                ]);
            }
        }

        // 2. Feedback dari catatan PenilaianDosen
        $nilaiDosen = PenilaianDosen::where('mahasiswa_id', $mahasiswa->id)
            ->with('supervisiMatkul.mataKuliah')
            ->get();

        foreach ($nilaiDosen as $p) {
            $matkulNama = $p->supervisiMatkul?->mataKuliah?->nama_matkul ?? 'Mata Kuliah';

            if (!empty($p->catatan_dosen)) {
                $feedbackList->push([
                    'tipe'    => 'penilaian',
                    'sumber'  => 'Dosen Pengampu',
                    'icon'    => 'school',
                    'color'   => 'secondary',
                    'konteks' => $matkulNama,
                    'pesan'   => $p->catatan_dosen,
                    'tanggal' => $p->updated_at,
                    'badge'   => 'Penilaian',
                ]);
            }
        }

        // 3. Feedback dari catatan logbook mingguan
        $logbookList = LogbookMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('catatan_dosen')
            ->where('catatan_dosen', '!=', '')
            ->orderBy('minggu_ke')
            ->get();

        foreach ($logbookList as $lb) {
            $feedbackList->push([
                'tipe'    => 'logbook',
                'sumber'  => 'Manager Proyek',
                'icon'    => 'book',
                'color'   => 'amber',
                'konteks' => 'Minggu ' . $lb->minggu_ke,
                'pesan'   => $lb->catatan_dosen,
                'tanggal' => $lb->updated_at,
                'badge'   => 'Logbook',
            ]);
        }

        // 4. Feedback dari catatan laporan
        $laporanList = LaporanMkPpi::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('catatan_verifikasi')
            ->where('catatan_verifikasi', '!=', '')
            ->get();

        foreach ($laporanList as $lp) {
            $feedbackList->push([
                'tipe'    => 'laporan',
                'sumber'  => 'Dosen Pengampu',
                'icon'    => 'description',
                'color'   => 'rose',
                'konteks' => $lp->jenis_laporan,
                'pesan'   => $lp->catatan_verifikasi,
                'tanggal' => $lp->updated_at,
                'badge'   => 'Laporan',
            ]);
        }

        $feedbackList = $feedbackList->sortByDesc('tanggal')->values();

        $totalFeedback = $feedbackList->count();
        $feedbackPerTipe = [
            'penilaian' => $feedbackList->where('tipe', 'penilaian')->count(),
            'logbook'   => $feedbackList->where('tipe', 'logbook')->count(),
            'laporan'   => $feedbackList->where('tipe', 'laporan')->count(),
        ];

        return view('mahasiswa.feedback', compact(
            'mahasiswa',
            'feedbackList',
            'totalFeedback',
            'feedbackPerTipe'
        ));
    }
}