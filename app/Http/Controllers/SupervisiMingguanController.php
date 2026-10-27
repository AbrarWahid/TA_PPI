<?php

namespace App\Http\Controllers;

use App\Models\SupervisiHarian;
use App\Models\SupervisiMingguan;
use App\Models\SupervisiMatkul;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SupervisiMingguanController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $rekapList = SupervisiMingguan::with(['supervisiMatkul.mataKuliah', 'supervisiMatkul.dosen.user', 'dosen', 'matkul'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('minggu_ke')
            ->get();

        $supervisiMatkulList = SupervisiMatkul::with(['dosen.user', 'mataKuliah'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get();

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->get()
            ->map(fn($r) => $r->supervisi_matkul_id . '-' . $r->minggu_ke)
            ->toArray();

        $mingguBelumRekap = SupervisiHarian::where('mahasiswa_id', $mahasiswa->id)
            ->select('supervisi_matkul_id', 'minggu_ke')
            ->distinct()
            ->get()
            ->filter(fn($row) => !in_array($row->supervisi_matkul_id . '-' . $row->minggu_ke, $sudahRekap))
            ->map(fn($row) => [
                'supervisiMatkul' => SupervisiMatkul::with(['mataKuliah', 'dosen.user'])->find($row->supervisi_matkul_id),
                'minggu_ke'       => $row->minggu_ke,
            ])
            ->filter(fn($item) => !is_null($item['supervisiMatkul']))
            ->values();

        return view('supervisi_mingguan.index', compact(
            'rekapList', 'mingguBelumRekap', 'mahasiswa', 'supervisiMatkulList'
        ));
    }

    public function generate(Request $request)
    {
        $user      = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $request->validate([
            'supervisi_matkul_id' => 'required|exists:supervisi_matkul,id',
            'minggu_ke'           => 'required|integer|min:1',
        ]);

        $supervisiMatkul = SupervisiMatkul::with(['dosen.user', 'mataKuliah'])
            ->where('id', $request->supervisi_matkul_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $dosen  = $supervisiMatkul->dosen;
        $matkul = $supervisiMatkul->mataKuliah;

        $logs = SupervisiHarian::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiMatkul->id)
            ->where('minggu_ke', $request->minggu_ke)
            ->orderBy('tanggal')
            ->get();

        if ($logs->isEmpty()) {
            return back()->with('error', 'Tidak ada log harian untuk minggu ke-' . $request->minggu_ke . '.');
        }

        $existing = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiMatkul->id)
            ->where('minggu_ke', $request->minggu_ke)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Laporan minggu ke-' . $request->minggu_ke . ' sudah diajukan/disetujui.');
        }
        
        $html = view('supervisi_mingguan.pdf_template', [
    'mahasiswa' => $mahasiswa,
    'matkul'    => $matkul,
    'dosen'     => $dosen,
    'minggu_ke' => $request->minggu_ke,
    'logs'      => $logs,
])->render();

$pdf = Pdf::loadHtml($html)->setPaper('a4', 'portrait');

        $filename = 'supervisi_' . $mahasiswa->id . '_matkul' . $matkul->id . '_minggu' . $request->minggu_ke . '_' . time() . '.pdf';
        $pdfPath  = 'supervisi_mingguan/' . $filename;
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Hapus draft lama jika ada
        $draft = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiMatkul->id)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('status', 'draft')
            ->first();

        if ($draft) {
            if ($draft->pdf_path) Storage::disk('public')->delete($draft->pdf_path);
            $draft->delete();
        }

        SupervisiMingguan::create([
            'mahasiswa_id'        => $mahasiswa->id,
            'supervisi_matkul_id' => $supervisiMatkul->id,
            'minggu_ke'           => $request->minggu_ke,
            'dosen_id'            => $dosen->id,
            'matkul_id'           => $matkul->id,
            'pdf_path'            => $pdfPath,
            'status'              => 'diajukan',
        ]);

        return redirect()->route('supervisi_mingguan.index')
            ->with('success', 'Laporan minggu ke-' . $request->minggu_ke . ' berhasil digenerate dan diajukan.');
    }

    public function show(SupervisiMingguan $supervisiMingguan)
    {
        $user = Auth::user();

        if ($user->role === 'mahasiswa') {
            $mahasiswa = $user->mahasiswa;
            if (!$mahasiswa || $supervisiMingguan->mahasiswa_id !== $mahasiswa->id) abort(403);
        }

        $supervisiMingguan->load(['mahasiswa.user', 'supervisiMatkul.mataKuliah', 'supervisiMatkul.dosen.user', 'dosen', 'matkul']);

        $logs = SupervisiHarian::where('mahasiswa_id', $supervisiMingguan->mahasiswa_id)
            ->where('supervisi_matkul_id', $supervisiMingguan->supervisi_matkul_id)
            ->where('minggu_ke', $supervisiMingguan->minggu_ke)
            ->orderBy('tanggal')
            ->get();

        return view('supervisi_mingguan.show', compact('supervisiMingguan', 'logs'));
    }

    public function batal(SupervisiMingguan $supervisiMingguan)
    {
        $user      = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa || $supervisiMingguan->mahasiswa_id !== $mahasiswa->id) abort(403);

        if ($supervisiMingguan->status !== 'diajukan') {
            return back()->with('error', 'Hanya laporan berstatus "diajukan" yang dapat dibatalkan.');
        }

        if ($supervisiMingguan->pdf_path) {
            Storage::disk('public')->delete($supervisiMingguan->pdf_path);
        }

        $supervisiMingguan->delete();

        return redirect()->route('supervisi_mingguan.index')
            ->with('success', 'Pengajuan laporan berhasil dibatalkan.');
    }

    public function daftarVerifikasi()
    {
        $user  = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan.');
        }

        $laporans = SupervisiMingguan::with(['mahasiswa.user', 'matkul', 'supervisiMatkul.mataKuliah'])
            ->where('dosen_id', $dosen->id)
            ->orderByRaw("FIELD(status, 'diajukan', 'ditolak', 'disetujui')")
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('supervisi_mingguan.daftar_verifikasi', compact('laporans', 'dosen'));
    }

    public function verifikasi(Request $request, SupervisiMingguan $supervisiMingguan)
    {
        $user  = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen || $supervisiMingguan->dosen_id !== $dosen->id) abort(403);

        $request->validate([
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_dosen' => 'nullable|string|max:1000',
        ]);

        $supervisiMingguan->update([
            'status'          => $request->status,
            'catatan_dosen'   => $request->catatan_dosen,
            'diverifikasi_at' => now(),
        ]);

        $pesan = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';

        return back()->with('success', 'Laporan berhasil ' . $pesan . '.');
    }
}