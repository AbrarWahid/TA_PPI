<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\PengajuanProyek;
use App\Models\PeriodePbl;
use Illuminate\Http\Request;

class LogbookHarianController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mhs  = $user->mahasiswa;

        $logbook = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->with(['proyek.manager'])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('minggu_ke');

        return view('logbook_harian.index', compact('logbook'));
    }

    public function create()
    {
        $user    = auth()->user();
        $mhs     = $user->mahasiswa;
        $periode = PeriodePbl::getAktif();

        if (!$periode) {
            return redirect()->route('logbook_harian.index')
                ->with('error', 'Tidak ada periode PBL yang aktif saat ini.');
        }

        $today = now()->toDateString();
        if ($today < $periode->tanggal_mulai->toDateString() || $today > $periode->tanggal_selesai->toDateString()) {
            return redirect()->route('logbook_harian.index')
                ->with('error', 'Input logbook hanya bisa dilakukan dalam periode aktif (' .
                    $periode->tanggal_mulai->format('d M Y') . ' s.d. ' .
                    $periode->tanggal_selesai->format('d M Y') . ').');
        }

        $proyek = PengajuanProyek::whereHas('mahasiswa', function ($q) use ($mhs) {
            $q->where('mahasiswa_id', $mhs->id);
        })->where('status', 'approved')->get();

        return view('logbook_harian.create', compact('proyek', 'periode'));
    }

    public function store(Request $request)
    {
        $periode = PeriodePbl::getAktif();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode PBL yang aktif.');
        }

        $request->validate([
            'pengajuan_proyek_id' => 'required|exists:pengajuan_proyek,id',
            'minggu_ke'           => 'required|integer|min:1|max:20',
            'hari'                => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tanggal'             => [
                'required', 'date',
                'after_or_equal:' . $periode->tanggal_mulai->toDateString(),
                'before_or_equal:' . $periode->tanggal_selesai->toDateString(),
            ],
            'jam_masuk'  => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i|after:jam_masuk',
            'aktivitas'  => 'required|string',
            'kendala'    => 'nullable|string',
            'dokumentasi'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'tanggal.after_or_equal'  => 'Tanggal tidak boleh sebelum periode aktif (' . $periode->tanggal_mulai->format('d M Y') . ').',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh setelah periode aktif (' . $periode->tanggal_selesai->format('d M Y') . ').',
            'jam_masuk.required'     => 'Jam masuk wajib diisi.',
            'jam_masuk.date_format'  => 'Format jam masuk tidak valid (HH:MM).',
            'jam_keluar.required'    => 'Jam keluar wajib diisi.',
            'jam_keluar.date_format' => 'Format jam keluar tidak valid (HH:MM).',
            'jam_keluar.after'       => 'Jam keluar harus setelah jam masuk.',
        ]);

        $user = auth()->user();
        $mhs  = $user->mahasiswa;

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi_logbook', 'public');
        }

        LogbookHarian::create([
            'mahasiswa_id'        => $mhs->id,
            'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
            'minggu_ke'           => $request->minggu_ke,
            'hari'                => $request->hari,
            'tanggal'             => $request->tanggal,
            'jam_masuk'           => $request->jam_masuk,
            'jam_keluar'          => $request->jam_keluar,
            'aktivitas'           => $request->aktivitas,
            'kendala'             => $request->kendala,
            'dokumentasi'         => $dokumentasiPath,
        ]);

        return redirect()->route('logbook_harian.index')
            ->with('success', 'Logbook harian berhasil disimpan!');
    }

    public function show(LogbookHarian $logbook_harian)
    {
        return view('logbook_harian.show', compact('logbook_harian'));
    }

    public function edit(LogbookHarian $logbook_harian)
    {
        $mhs = auth()->user()->mahasiswa;

        if ($logbook_harian->mahasiswa_id !== $mhs->id || $logbook_harian->status_verifikasi !== 'pending') {
            abort(403);
        }

        $proyek = PengajuanProyek::whereHas('mahasiswa', function ($q) use ($mhs) {
            $q->where('mahasiswa_id', $mhs->id);
        })->where('status', 'approved')->get();

        return view('logbook_harian.edit', compact('logbook_harian', 'proyek'));
    }

    public function update(Request $request, LogbookHarian $logbook_harian)
    {
        $mhs = auth()->user()->mahasiswa;

        if ($logbook_harian->mahasiswa_id !== $mhs->id || $logbook_harian->status_verifikasi !== 'pending') {
            abort(403);
        }

        $request->validate([
            'minggu_ke'  => 'required|integer|min:1|max:20',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tanggal'    => 'required|date',
            'jam_masuk'  => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i|after:jam_masuk',
            'aktivitas'  => 'required|string',
            'kendala'    => 'nullable|string',
            'dokumentasi'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'jam_masuk.required'     => 'Jam masuk wajib diisi.',
            'jam_masuk.date_format'  => 'Format jam masuk tidak valid (HH:MM).',
            'jam_keluar.required'    => 'Jam keluar wajib diisi.',
            'jam_keluar.date_format' => 'Format jam keluar tidak valid (HH:MM).',
            'jam_keluar.after'       => 'Jam keluar harus setelah jam masuk.',
        ]);

        $dokumentasiPath = $logbook_harian->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($dokumentasiPath) {
                \Storage::disk('public')->delete($dokumentasiPath);
            }
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi_logbook', 'public');
        }

        $logbook_harian->update([
            'minggu_ke'   => $request->minggu_ke,
            'hari'        => $request->hari,
            'tanggal'     => $request->tanggal,
            'jam_masuk'   => $request->jam_masuk,
            'jam_keluar'  => $request->jam_keluar,
            'aktivitas'   => $request->aktivitas,
            'kendala'     => $request->kendala,
            'dokumentasi' => $dokumentasiPath,
        ]);

        return redirect()->route('logbook_harian.show', $logbook_harian)
            ->with('success', 'Logbook berhasil diperbarui!');
    }

    public function destroy(LogbookHarian $logbook_harian)
    {
        $mhs = auth()->user()->mahasiswa;

        if ($logbook_harian->mahasiswa_id !== $mhs->id || $logbook_harian->status_verifikasi !== 'pending') {
            abort(403);
        }

        if ($logbook_harian->dokumentasi) {
            \Storage::disk('public')->delete($logbook_harian->dokumentasi);
        }

        $logbook_harian->delete();

        return redirect()->route('logbook_harian.index')
            ->with('success', 'Logbook berhasil dihapus!');
    }

    public function rekapMingguan(Request $request)
    {
        $user     = auth()->user();
        $mhs      = $user->mahasiswa;
        $mingguKe = $request->get('minggu_ke', 1);

        $logbook = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->where('minggu_ke', $mingguKe)
            ->with('proyek')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->get();

        $totalMinggu = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->distinct('minggu_ke')
            ->count('minggu_ke');

        return view('logbook_harian.rekap', compact('logbook', 'mingguKe', 'totalMinggu'));
    }

    public function verifikasi(Request $request, LogbookHarian $logbook_harian)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan_dosen'     => 'nullable|string',
        ]);

        $logbook_harian->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_dosen'     => $request->catatan_dosen,
        ]);

        return back()->with('success', 'Logbook berhasil diverifikasi!');
    }
}