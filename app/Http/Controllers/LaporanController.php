<?php

namespace App\Http\Controllers;

use App\Models\LaporanMkPpi;
use App\Models\PengajuanProyek;
use App\Models\SupervisiMatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index()
{
    $user = auth()->user();
    $laporan = collect(); // default kosong

    if ($user->isMahasiswa()) {
        $mhsId = $user->mahasiswa->id;

        $supervisiDosen = SupervisiMatkul::where('mahasiswa_id', $mhsId)
            ->with('dosen')
            ->get()
            ->map(fn($s) => $s->dosen->nama_dosen ?? '-')
            ->join(', ');

        $laporan = LaporanMkPpi::with('proyek')
            ->where('mahasiswa_id', $mhsId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($l) use ($supervisiDosen) {
                $l->dosenSupervisi = $supervisiDosen;
                return $l;
            });

    } elseif ($user->isDosen()) {
        $mahasiswaIds = SupervisiMatkul::where('dosen_id', $user->dosen->id)
            ->pluck('mahasiswa_id');

        $laporan = LaporanMkPpi::with(['mahasiswa', 'proyek'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->orderBy('created_at', 'desc')
            ->get();

    } elseif ($user->isAdmin()) {
        $laporan = LaporanMkPpi::with(['mahasiswa', 'proyek'])
            ->orderBy('created_at', 'desc')
            ->get();

    } elseif ($user->isManager()) {
        // Laporan mahasiswa dari proyek yang dikelola manager ini
        $mahasiswaIds = PengajuanProyek::where('manager_id', $user->id)
            ->with('mahasiswa')
            ->get()
            ->flatMap(fn($p) => $p->mahasiswa->pluck('id'));

        $laporan = LaporanMkPpi::with(['mahasiswa', 'proyek'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    return view('laporan.index', compact('laporan'));
}

    public function create()
    {
        $user = auth()->user();

        $supervisiList = collect();
        if ($user->isMahasiswa()) {
            $supervisiList = SupervisiMatkul::where('mahasiswa_id', $user->mahasiswa->id)
                ->with(['mataKuliah', 'dosen'])
                ->get();
        }

        return view('laporan.create', compact('supervisiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:Supervisi,Laporan Teknik,PAB',
            'file'          => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $user     = auth()->user();
        $file     = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('laporan', $fileName, 'public');
        $kode     = 'LPR-' . $user->mahasiswa->nim . '-' . now()->format('YmdHis');

        $proyek = PengajuanProyek::whereHas('mahasiswa', function($q) use ($user) {
            $q->where('mahasiswa_id', $user->mahasiswa->id);
        })->where('status', 'approved')->first();

        LaporanMkPpi::create([
            'kode_laporan'        => $kode,
            'mahasiswa_id'        => $user->mahasiswa->id,
            'pengajuan_proyek_id' => $proyek?->id,
            'jenis_laporan'       => $request->jenis_laporan,
            'file_path'           => $filePath,
        ]);

        return redirect('/laporan')->with('success', 'Laporan berhasil diupload!');
    }

    public function show(LaporanMkPpi $laporan)
    {
        return view('laporan.show', compact('laporan'));
    }

    public function verifikasi(Request $request, LaporanMkPpi $laporan)
    {
        $request->validate([
            'status_verifikasi'  => 'required|in:disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $laporan->update([
            'status_verifikasi'  => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        return back()->with('success', 'Laporan berhasil diverifikasi!');
    }
}