<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\LogbookMingguan;
use App\Models\PengajuanProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogbookMingguanController extends Controller
{
    // Daftar rekap mingguan milik mahasiswa
    public function index()
{
    $user = Auth::user();

    // ── Admin: lihat semua logbook mingguan ──
    if ($user->isAdmin()) {
        $rekapList = LogbookMingguan::with(['mahasiswa.user', 'proyek'])
            ->orderBy('created_at', 'desc')
            ->get();
        $mingguBelumRekap = [];
        return view('logbook_mingguan.index', compact('rekapList', 'mingguBelumRekap'));
    }

    // ── Manager / Dosen: lihat logbook dari proyek yang dikelola ──
    if (in_array($user->role_aktif ?? $user->role, ['manager_proyek', 'dosen'])) {
        $proyekIds = PengajuanProyek::where('manager_id', $user->id)->pluck('id');
        $rekapList = LogbookMingguan::with(['mahasiswa.user', 'proyek'])
            ->whereIn('pengajuan_proyek_id', $proyekIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $mingguBelumRekap = [];
        return view('logbook_mingguan.index', compact('rekapList', 'mingguBelumRekap'));
    }

    // ── Mahasiswa: logbook milik sendiri ──
    $mahasiswa = $user->mahasiswa;

    $rekapList = LogbookMingguan::with('proyek')
        ->where('mahasiswa_id', $mahasiswa->id)
        ->orderBy('pengajuan_proyek_id')
        ->orderBy('minggu_ke')
        ->get();

    $proyekAktif = PengajuanProyek::whereHas('mahasiswa', function ($q) use ($mahasiswa) {
        $q->where('mahasiswa_id', $mahasiswa->id);
    })->get();

    $mingguBelumRekap = [];
    foreach ($proyekAktif as $proyek) {
        $mingguAda = LogbookHarian::where('mahasiswa_id', $mahasiswa->id)
            ->where('pengajuan_proyek_id', $proyek->id)
            ->distinct()
            ->pluck('minggu_ke');

        foreach ($mingguAda as $minggu) {
            $sudahAda = LogbookMingguan::where('mahasiswa_id', $mahasiswa->id)
                ->where('pengajuan_proyek_id', $proyek->id)
                ->where('minggu_ke', $minggu)
                ->exists();

            if (!$sudahAda) {
                $mingguBelumRekap[] = [
                    'proyek'    => $proyek,
                    'minggu_ke' => $minggu,
                ];
            }
        }
    }

    return view('logbook_mingguan.index', compact('rekapList', 'mingguBelumRekap'));
}

    // Generate PDF rekap mingguan
    public function generate(Request $request)
    {
        $request->validate([
            'pengajuan_proyek_id' => 'required|exists:pengajuan_proyek,id',
            'minggu_ke'           => 'required|integer|min:1',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;

        $existing = LogbookMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('pengajuan_proyek_id', $request->pengajuan_proyek_id)
            ->where('minggu_ke', $request->minggu_ke)
            ->first();

        if ($existing) {
            return back()->with('error', 'Rekap minggu ke-' . $request->minggu_ke . ' sudah pernah dibuat.');
        }

        $harian = LogbookHarian::where('mahasiswa_id', $mahasiswa->id)
            ->where('pengajuan_proyek_id', $request->pengajuan_proyek_id)
            ->where('minggu_ke', $request->minggu_ke)
            ->orderBy('tanggal')
            ->get();

        if ($harian->isEmpty()) {
            return back()->with('error', 'Belum ada logbook harian untuk minggu ini.');
        }

        $proyek = PengajuanProyek::findOrFail($request->pengajuan_proyek_id);

        $pdf = app('dompdf.wrapper');
        $mingguKe = $request->minggu_ke;
$pdf->loadView('logbook_mingguan.pdf_template', compact('harian', 'mahasiswa', 'proyek', 'mingguKe'));

        $filename = 'logbook_mingguan_' . $mahasiswa->id . '_proyek' . $proyek->id . '_minggu' . $request->minggu_ke . '.pdf';
        $pdfPath  = 'logbook_mingguan/' . $filename;
        Storage::disk('public')->put($pdfPath, $pdf->output());

        LogbookMingguan::create([
            'mahasiswa_id'        => $mahasiswa->id,
            'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
            'minggu_ke'           => $request->minggu_ke,
            'pdf_path'            => $pdfPath,
            'status'              => 'diajukan',
            'diajukan_at'         => now(),
        ]);

        return back()->with('success', 'Rekap minggu ke-' . $request->minggu_ke . ' berhasil digenerate & diajukan.');
    }

    // Batalkan rekap mingguan (hanya status diajukan, oleh pemilik)
public function batal(LogbookMingguan $logbookMingguan)
{
    $mahasiswa = Auth::user()->mahasiswa;

    // Pastikan hanya pemilik yang bisa batalkan
    if ($logbookMingguan->mahasiswa_id !== $mahasiswa->id) {
        abort(403);
    }

    // Hanya boleh batalkan jika masih diajukan/menunggu
    if (!in_array($logbookMingguan->status, ['diajukan', 'draft'])) {
        return back()->with('error', 'Rekap yang sudah diverifikasi tidak dapat dibatalkan.');
    }

    // Hapus file PDF dari storage
    if ($logbookMingguan->pdf_path && Storage::disk('public')->exists($logbookMingguan->pdf_path)) {
        Storage::disk('public')->delete($logbookMingguan->pdf_path);
    }

    $minggu = $logbookMingguan->minggu_ke;
    $logbookMingguan->delete();

    return back()->with('success', 'Rekap minggu ke-' . $minggu . ' berhasil dibatalkan. Kamu bisa generate ulang.');
}

    // Lihat PDF
    public function show(LogbookMingguan $logbookMingguan)
    {
        $this->authorizeAccess($logbookMingguan);

        if (!$logbookMingguan->pdf_path || !Storage::disk('public')->exists($logbookMingguan->pdf_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($logbookMingguan->pdf_path));
    }

    // Daftar verifikasi logbook mingguan (untuk Dosen)
    public function daftarVerifikasi()
    {
        $user = Auth::user();

        $proyekIds = PengajuanProyek::where('manager_id', $user->id)
            ->pluck('id');

        $rekapList = LogbookMingguan::with(['mahasiswa.user', 'proyek'])
            ->whereIn('pengajuan_proyek_id', $proyekIds)
            ->where('status', '!=', 'draft')
            ->orderByRaw("FIELD(status, 'diajukan', 'ditolak', 'disetujui')")
            ->orderBy('diajukan_at', 'desc')
            ->get();

        return view('logbook_mingguan.verifikasi', compact('rekapList'));
    }

    // Verifikasi oleh Dosen (ACC / Tolak)
    public function verifikasi(Request $request, LogbookMingguan $logbookMingguan)
    {
        $request->validate([
            'aksi'          => 'required|in:disetujui,ditolak',
            'catatan_dosen' => 'nullable|string|max:500',
        ]);

        $logbookMingguan->update([
            'status'          => $request->aksi,
            'catatan_dosen'   => $request->catatan_dosen,
            'diverifikasi_at' => now(),
        ]);

        $label = $request->aksi === 'disetujui' ? 'disetujui' : 'ditolak';

        return back()->with('success', 'Logbook mingguan berhasil ' . $label . '.');
    }

    // Helper: pastikan hanya pemilik atau dosen yang bisa akses
    private function authorizeAccess(LogbookMingguan $logbookMingguan)
    {
        $user = Auth::user();

        if ($user->role === 'mahasiswa') {
            abort_if($logbookMingguan->mahasiswa_id !== $user->mahasiswa->id, 403);
        } elseif (!in_array($user->role, ['dosen', 'admin'])) {
            abort(403);
        }
    }
}