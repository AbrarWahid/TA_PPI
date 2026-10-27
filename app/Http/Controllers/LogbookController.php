<?php

namespace App\Http\Controllers;

use App\Models\LogbookMingguan;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $logbook = LogbookMingguan::with('kelas')
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->orderBy('minggu_ke')
                ->get();
        } else {
            // Dosen lihat semua logbook di kelasnya
            $logbook = LogbookMingguan::with(['mahasiswa','kelas'])
                ->whereHas('kelas', fn($q) => $q->where('dosen_id', $user->dosen?->id))
                ->orderBy('created_at','desc')
                ->get();
        }

        return view('logbook.index', compact('logbook'));
    }

    public function create()
    {
        $user   = auth()->user();
        $kelas  = $user->mahasiswa->kelas;
        return view('logbook.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'  => 'required|exists:kelas,id',
            'minggu_ke' => 'required|integer|min:1|max:20',
            'tanggal'   => 'required|date',
            'aktivitas' => 'required|string',
            'kendala'   => 'nullable|string',
            'solusi'    => 'nullable|string',
        ]);

        $user = auth()->user();
        $kode = 'LBK-' . $user->mahasiswa->nim . '-W' . $request->minggu_ke;

        LogbookMingguan::create([
            'kode_logbook' => $kode,
            'mahasiswa_id' => $user->mahasiswa->id,
            'kelas_id'     => $request->kelas_id,
            'minggu_ke'    => $request->minggu_ke,
            'tanggal'      => $request->tanggal,
            'aktivitas'    => $request->aktivitas,
            'kendala'      => $request->kendala,
            'solusi'       => $request->solusi,
        ]);

        return redirect('/logbook')->with('success', 'Logbook berhasil disimpan!');
    }

    public function show(LogbookMingguan $logbook)
    {
        return view('logbook.show', compact('logbook'));
    }

    public function verifikasi(Request $request, LogbookMingguan $logbook)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan_dosen'     => 'nullable|string',
        ]);

        $logbook->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_dosen'     => $request->catatan_dosen,
        ]);

        return back()->with('success', 'Logbook berhasil diverifikasi!');
    }
}