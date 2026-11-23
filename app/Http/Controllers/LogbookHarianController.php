<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\LogbookHarianFoto;
use App\Models\PengajuanProyek;
use App\Models\PeriodePbl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LogbookHarianController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $mhs     = $user->mahasiswa;
        $periode = PeriodePbl::getAktif();

        $proyek = $mhs
            ? PengajuanProyek::with('manager')
                ->whereHas('mahasiswa', function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->id);
                })
                ->where('status', 'approved')
                ->get()
            : collect();

        $entries = $mhs
            ? LogbookHarian::where('mahasiswa_id', $mhs->id)
                ->with(['proyek.manager'])
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get()
            : collect();

        $fotosByDate = [];
        if ($mhs) {
            $fotos = LogbookHarianFoto::where('mahasiswa_id', $mhs->id)->get();
            foreach ($fotos as $foto) {
                $tgl = $foto->tanggal instanceof \Carbon\Carbon
                    ? $foto->tanggal->format('Y-m-d')
                    : substr((string) $foto->tanggal, 0, 10);
                $key = $foto->pengajuan_proyek_id . '_' . $tgl;
                if (!isset($fotosByDate[$key])) {
                    $fotosByDate[$key] = [];
                }
                $fotosByDate[$key][] = [
                    'id'       => $foto->id,
                    'url'      => '/storage/' . $foto->foto_path,
                    'path'     => $foto->foto_path,
                    'filename' => basename($foto->foto_path),
                ];
            }

            // Fallback untuk foto lama di kolom dokumentasi yang belum ada di logbook_harian_fotos
            foreach ($entries as $entry) {
                if ($entry->dokumentasi) {
                    $tgl = $entry->tanggal instanceof \Carbon\Carbon
                        ? $entry->tanggal->format('Y-m-d')
                        : substr((string) $entry->tanggal, 0, 10);
                    $key = $entry->pengajuan_proyek_id . '_' . $tgl;
                    $exists = collect($fotosByDate[$key] ?? [])->contains('path', $entry->dokumentasi);
                    if (!$exists) {
                        $fotosByDate[$key][] = [
                            'id'       => null,
                            'url'      => '/storage/' . $entry->dokumentasi,
                            'path'     => $entry->dokumentasi,
                            'filename' => basename($entry->dokumentasi),
                        ];
                    }
                }
            }
        }

        $totalMinggu = $entries->pluck('minggu_ke')->unique()->count();

        return Inertia::render('LogbookHarian/Index', [
            'entries'     => $entries,
            'proyek'      => $proyek,
            'periode'     => $periode,
            'fotosByDate' => $fotosByDate,
            'mahasiswa'   => $mhs,
            'totalMinggu' => $totalMinggu,
        ]);
    }

    public function storeDaily(Request $request)
    {
        $periode = PeriodePbl::getAktif();

        $dateRules = ['required', 'date'];
        $dateMessages = [];
        $maxMinggu = 20;
        if ($periode) {
            $dateRules[] = 'after_or_equal:' . $periode->tanggal_mulai->toDateString();
            $dateRules[] = 'before_or_equal:' . $periode->tanggal_selesai->toDateString();
            $dateMessages['tanggal.after_or_equal'] = 'Tanggal tidak boleh sebelum periode aktif (' . $periode->tanggal_mulai->format('d M Y') . ').';
            $dateMessages['tanggal.before_or_equal'] = 'Tanggal tidak boleh setelah periode aktif (' . $periode->tanggal_selesai->format('d M Y') . ').';
            $totalHariPeriode = $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1;
            $maxMinggu = max(1, (int) ceil($totalHariPeriode / 7));
        }

        $request->validate([
            'pengajuan_proyek_id' => 'required|exists:pengajuan_proyek,id',
            'tanggal'             => $dateRules,
            'minggu_ke'           => 'required|integer|min:1|max:' . $maxMinggu,
            'hari'                => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'status'              => 'required|in:Kerja,Libur,Izin,Sakit',
            'kegiatan'            => 'nullable|string',
            'kendala'             => 'nullable|string',
            'activities_json'     => 'nullable|string',
            'fotos'               => 'nullable|array',
            'fotos.*'             => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], $dateMessages);

        $mhs = auth()->user()->mahasiswa;
        if (!$mhs) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        // Cek apakah logbook tanggal ini sudah disetujui
        $existingApproved = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->where('pengajuan_proyek_id', $request->pengajuan_proyek_id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('status_verifikasi', 'disetujui')
            ->exists();

        if ($existingApproved) {
            return response()->json([
                'message' => 'Logbook pada tanggal ini sudah disetujui dan tidak dapat diubah.',
            ], 422);
        }

        // Simpan foto baru terlebih dahulu
        $newFotos = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $file) {
                $path = $file->store('dokumentasi_logbook', 'public');
                $record = LogbookHarianFoto::create([
                    'mahasiswa_id'        => $mhs->id,
                    'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
                    'tanggal'             => $request->tanggal,
                    'foto_path'           => $path,
                ]);
                $newFotos[] = [
                    'id'       => $record->id,
                    'url'      => '/storage/' . $path,
                    'path'     => $path,
                    'filename' => basename($path),
                ];
            }
        }

        // Ambil foto pertama yang tersedia di tanggal ini untuk kolom dokumentasi utama
        $firstFoto = LogbookHarianFoto::where('mahasiswa_id', $mhs->id)
            ->where('pengajuan_proyek_id', $request->pengajuan_proyek_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();
        $primaryDocPath = $firstFoto?->foto_path;

        // Hapus entri lama (pending / ditolak) di tanggal ini lalu simpan yang baru
        LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->where('pengajuan_proyek_id', $request->pengajuan_proyek_id)
            ->whereDate('tanggal', $request->tanggal)
            ->delete();

        $activities = [];
        if ($request->filled('activities_json')) {
            $activities = json_decode($request->input('activities_json'), true) ?? [];
        }

        if ($request->status === 'Kerja' && count($activities) > 0) {
            $savedAny = false;
            foreach ($activities as $act) {
                $waktu = (int) ($act['waktu'] ?? 0);
                $ket = trim((string) ($act['kegiatan'] ?? ''));
                if ($waktu > 0 || $ket !== '') {
                    LogbookHarian::create([
                        'mahasiswa_id'        => $mhs->id,
                        'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
                        'minggu_ke'           => $request->minggu_ke,
                        'hari'                => $request->hari,
                        'tanggal'             => $request->tanggal,
                        'status'              => 'Kerja',
                        'jam_masuk'           => !empty($act['jamMulai']) ? $act['jamMulai'] : null,
                        'jam_keluar'          => !empty($act['jamSelesai']) ? $act['jamSelesai'] : null,
                        'durasi_mnt'          => $waktu,
                        'aktivitas'           => $ket !== '' ? $ket : '-',
                        'kendala'             => $request->kendala,
                        'dokumentasi'         => $primaryDocPath,
                        'status_verifikasi'   => 'pending',
                    ]);
                    $savedAny = true;
                }
            }
            if (!$savedAny) {
                return response()->json([
                    'message' => 'Harap isi minimal satu aktivitas atau jam kerja.',
                ], 422);
            }
        } else {
            LogbookHarian::create([
                'mahasiswa_id'        => $mhs->id,
                'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
                'minggu_ke'           => $request->minggu_ke,
                'hari'                => $request->hari,
                'tanggal'             => $request->tanggal,
                'status'              => $request->status,
                'jam_masuk'           => null,
                'jam_keluar'          => null,
                'durasi_mnt'          => 0,
                'aktivitas'           => trim((string) ($request->kegiatan ?? '')) ?: $request->status,
                'kendala'             => $request->kendala,
                'dokumentasi'         => $primaryDocPath,
                'status_verifikasi'   => 'pending',
            ]);
        }

        return response()->json([
            'message'   => 'Berhasil menyimpan logbook harian.',
            'new_fotos' => $newFotos,
        ]);
    }

    public function deleteFoto($id)
    {
        $mhs = auth()->user()->mahasiswa;
        if (!$mhs) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $foto = LogbookHarianFoto::where('id', $id)
            ->where('mahasiswa_id', $mhs->id)
            ->firstOrFail();

        if (Storage::disk('public')->exists($foto->foto_path)) {
            Storage::disk('public')->delete($foto->foto_path);
        }

        $foto->delete();

        return response()->json(['message' => 'Foto berhasil dihapus.']);
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

        return Inertia::render('LogbookHarian/Create', compact('proyek', 'periode'));
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
        $logbook_harian->load('proyek');
        return Inertia::render('LogbookHarian/Show', compact('logbook_harian'));
    }

    public function edit(LogbookHarian $logbook_harian)
    {
        $mhs = auth()->user()->mahasiswa;

        if ($logbook_harian->mahasiswa_id !== $mhs->id || $logbook_harian->status_verifikasi !== 'pending') {
            abort(403);
        }

        $logbook_harian->load('proyek');

        $proyek = PengajuanProyek::whereHas('mahasiswa', function ($q) use ($mhs) {
            $q->where('mahasiswa_id', $mhs->id);
        })->where('status', 'approved')->get();

        return Inertia::render('LogbookHarian/Edit', compact('logbook_harian', 'proyek'));
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
        $mingguKe = (int) $request->get('minggu_ke', 1);

        $logbook = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->where('minggu_ke', $mingguKe)
            ->with('proyek')
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
            ->get();

        $totalMinggu = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->distinct('minggu_ke')
            ->count('minggu_ke');

        return Inertia::render('LogbookHarian/Rekap', compact('logbook', 'mingguKe', 'totalMinggu'));
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