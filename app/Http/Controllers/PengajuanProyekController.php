<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PengajuanProyek;
use App\Models\PengajuanProyekKebutuhan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengajuanProyekController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $pengajuan = PengajuanProyek::with(['manager', 'kebutuhan', 'mahasiswa', 'dosenPengampu.user'])
                ->latest()
                ->get();
        } else {
            $pengajuan = PengajuanProyek::with(['manager', 'kebutuhan', 'mahasiswa', 'dosenPengampu.user'])
                ->where('manager_id', $user->id)
                ->latest()
                ->get();
        }

        return view('pengajuan_proyek.index', compact('pengajuan'));
    }

    public function create()
    {
        return view('pengajuan_proyek.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_proyek'              => 'required|string|max:255',
            'deskripsi'                 => 'required|string',
            'tujuan'                    => 'required|string',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'anggaran'                  => 'nullable|numeric|min:0',
            'kebutuhan'                 => 'required|array|min:1',
            'kebutuhan.*.prodi'         => 'required|in:mekatronika,otomasi,informatika',
            'kebutuhan.*.jumlah'        => 'required|integer|min:1|max:50',
        ]);

        $prodis = array_column($request->kebutuhan, 'prodi');
        if (count($prodis) !== count(array_unique($prodis))) {
            return back()->withInput()->withErrors(['kebutuhan' => 'Prodi tidak boleh duplikat.']);
        }

        $pengajuan = PengajuanProyek::create([
            'kode_pengajuan'  => 'PP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'manager_id'      => auth()->id(),
            'judul_proyek'    => $request->judul_proyek,
            'deskripsi'       => $request->deskripsi,
            'tujuan'          => $request->tujuan,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'anggaran'        => $request->anggaran,
            'status'          => 'pending',
        ]);

        foreach ($request->kebutuhan as $item) {
            PengajuanProyekKebutuhan::create([
                'pengajuan_proyek_id' => $pengajuan->id,
                'prodi'               => $item['prodi'],
                'jumlah_mahasiswa'    => $item['jumlah'],
            ]);
        }

        return redirect()->route('pengajuan_proyek.index')
            ->with('success', 'Pengajuan proyek berhasil dikirim!');
    }

    public function show(PengajuanProyek $pengajuan_proyek)
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $mhs = $user->mahasiswa;
            abort_unless(
                $mhs && $pengajuan_proyek->mahasiswa()->where('mahasiswa_id', $mhs->id)->exists(),
                403
            );
        }

        $pengajuan_proyek->load(['manager', 'kebutuhan', 'mahasiswa', 'diprosesOleh', 'dosenPengampu.user']);
        return view('pengajuan_proyek.show', compact('pengajuan_proyek'));
    }

    public function approve(Request $request, PengajuanProyek $pengajuan_proyek)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pengajuan_proyek->update([
            'status'        => 'approved',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
            'diproses_at'   => now(),
        ]);

        return redirect()->route('pengajuan_proyek.assign', $pengajuan_proyek)
            ->with('success', 'Proyek disetujui! Silakan pilih mahasiswa.');
    }

    public function reject(Request $request, PengajuanProyek $pengajuan_proyek)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        $pengajuan_proyek->update([
            'status'        => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
            'diproses_at'   => now(),
        ]);

        return redirect()->route('pengajuan_proyek.show', $pengajuan_proyek)
            ->with('success', 'Pengajuan proyek berhasil ditolak.');
    }

    // Form assign mahasiswa
    public function assignMahasiswa(PengajuanProyek $pengajuan_proyek)
{
    abort_if(!auth()->user()->isAdmin(), 403);
    abort_if($pengajuan_proyek->status !== 'approved', 403);

    $pengajuan_proyek->load(['kebutuhan', 'mahasiswa', 'manager']);

    $mahasiswaPerProdi = [];
    foreach ($pengajuan_proyek->kebutuhan as $kebutuhan) {
        $mahasiswaPerProdi[$kebutuhan->prodi] = Mahasiswa::whereHas('user', function ($q) use ($kebutuhan) {
            $q->where('prodi', $kebutuhan->prodi);
        })->get();
    }

    return view('pengajuan_proyek.assign', compact('pengajuan_proyek', 'mahasiswaPerProdi'));
}

    // Simpan pilihan mahasiswa
    public function simpanMahasiswa(Request $request, PengajuanProyek $pengajuan_proyek)
{
    abort_if(!auth()->user()->isAdmin(), 403);

    $request->validate([
        'mahasiswa'   => 'required|array|min:1',
        'mahasiswa.*' => 'exists:mahasiswa,id',
    ]);

    $pengajuan_proyek->mahasiswa()->detach();

    foreach ($request->mahasiswa as $mahasiswaId) {
        $mhs = Mahasiswa::with('user')->find($mahasiswaId);
        $pengajuan_proyek->mahasiswa()->attach($mahasiswaId, [
            'prodi' => $mhs->user->prodi,
        ]);
    }

    // Auto-set dosen_pengampu dari manager jika manager adalah dosen
    $manager = $pengajuan_proyek->manager;
    if ($manager && $manager->isDosen()) {
        $dosen = \App\Models\Dosen::where('user_id', $manager->id)->first();
        if ($dosen) {
            $pengajuan_proyek->update(['dosen_pengampu_id' => $dosen->id]);
        }
    }

    return redirect()->route('pengajuan_proyek.show', $pengajuan_proyek)
        ->with('success', 'Mahasiswa berhasil ditugaskan ke proyek!');
}

    public function proyekPbl()
    {
        $proyek = PengajuanProyek::with(['manager', 'kebutuhan', 'mahasiswa', 'dosenPengampu.user'])
            ->where('status', 'approved')
            ->latest('diproses_at')
            ->get();

        return view('proyek_pbl.index', compact('proyek'));
    }
    public function destroy(PengajuanProyek $pengajuan_proyek)
{
    abort_if(!auth()->user()->isAdmin(), 403);

    // Hapus relasi pivot mahasiswa dulu
    $pengajuan_proyek->mahasiswa()->detach();

    // Hapus kebutuhan
    $pengajuan_proyek->kebutuhan()->delete();

    // Hapus proyek
    $pengajuan_proyek->delete();

    return back()->with('success', 'Proyek "' . $pengajuan_proyek->judul_proyek . '" berhasil dihapus.');
}
}