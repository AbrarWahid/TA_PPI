<?php

namespace App\Http\Controllers;

use App\Models\SupervisiHarian;
use App\Models\SupervisiMingguan;
use App\Models\SupervisiMatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupervisiHarianController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $supervisiList    = SupervisiHarian::with(['mahasiswa.user'])->orderBy('tanggal', 'desc')->get();
            $mingguBelumRekap = collect();
            $statusRekap      = collect();
            return view('supervisi_harian.index', compact('supervisiList', 'mingguBelumRekap', 'statusRekap'));
        }

        if (in_array($user->role_aktif ?? $user->role, ['dosen_pengampu', 'dosen'])) {
            $matkulIds     = SupervisiMatkul::where('dosen_id', $user->dosen->id)->pluck('id');
            $supervisiList = SupervisiHarian::with(['mahasiswa.user'])
                ->whereIn('supervisi_matkul_id', $matkulIds)
                ->orderBy('tanggal', 'desc')->get();
            $mingguBelumRekap = collect();
            $statusRekap      = collect();
            return view('supervisi_harian.index', compact('supervisiList', 'mingguBelumRekap', 'statusRekap'));
        }

        // Mahasiswa
        $mahasiswa     = $user->mahasiswa;
        $supervisiList = SupervisiHarian::with(['supervisiMatkul.mataKuliah', 'supervisiMatkul.dosen.user'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('minggu_ke')
            ->orderBy('tanggal')
            ->get();

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->get()
            ->map(fn($r) => $r->supervisi_matkul_id . '-' . $r->minggu_ke)
            ->toArray();

        $mingguBelumRekap = SupervisiHarian::where('mahasiswa_id', $mahasiswa->id)
            ->select('supervisi_matkul_id', 'minggu_ke')->distinct()->get()
            ->filter(fn($row) => !in_array($row->supervisi_matkul_id . '-' . $row->minggu_ke, $sudahRekap))
            ->map(fn($row) => [
                'supervisiMatkul' => SupervisiMatkul::with(['mataKuliah', 'dosen.user'])->find($row->supervisi_matkul_id),
                'minggu_ke'       => $row->minggu_ke,
            ])
            ->filter(fn($item) => !is_null($item['supervisiMatkul']))
            ->values();

        $statusRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->supervisi_matkul_id . '-' . $r->minggu_ke => $r->status
            ]);

        return view('supervisi_harian.index', compact('supervisiList', 'mingguBelumRekap', 'statusRekap'));
    }

    public function create()
    {
        $mahasiswa           = Auth::user()->mahasiswa;
        $supervisiMatkulList = SupervisiMatkul::with(['mataKuliah', 'dosen.user'])
            ->where('mahasiswa_id', $mahasiswa->id)->get();

        return view('supervisi_harian.create', compact('supervisiMatkulList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supervisi_matkul_id' => 'required|exists:supervisi_matkul,id',
            'minggu_ke'           => 'required|integer|min:1',
            'hari'                => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tanggal'             => 'required|date',
            'jam_mulai'           => 'required|date_format:H:i',
            'jam_selesai'         => 'required|date_format:H:i|after:jam_mulai',
            'aktivitas'           => 'required|string|max:2000',
            'kendala'             => 'nullable|string|max:1000',
            'dokumentasi'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;

        $supervisiMatkul = SupervisiMatkul::where('id', $request->supervisi_matkul_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiMatkul->id)
            ->where('minggu_ke', $request->minggu_ke)
            ->exists();

        if ($sudahRekap) {
            return back()->with('error', 'Minggu ke-' . $request->minggu_ke . ' sudah direkap.');
        }

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('supervisi_harian', 'public');
        }

        SupervisiHarian::create([
            'mahasiswa_id'        => $mahasiswa->id,
            'supervisi_matkul_id' => $supervisiMatkul->id,
            'minggu_ke'           => $request->minggu_ke,
            'hari'                => $request->hari,
            'tanggal'             => $request->tanggal,
            'jam_mulai'           => $request->jam_mulai,
            'jam_selesai'         => $request->jam_selesai,
            'aktivitas'           => $request->aktivitas,
            'kendala'             => $request->kendala,
            'dokumentasi'         => $dokumentasiPath,
        ]);

        return redirect()->route('supervisi_harian.index')
            ->with('success', 'Entri supervisi harian berhasil disimpan.');
    }

    public function show(SupervisiHarian $supervisiHarian)
    {
        $this->authorizeAccess($supervisiHarian);
        $supervisiHarian->load(['supervisiMatkul.mataKuliah', 'supervisiMatkul.dosen.user', 'mahasiswa.user']);
        return view('supervisi_harian.show', compact('supervisiHarian'));
    }

    public function edit(SupervisiHarian $supervisiHarian)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        abort_if($supervisiHarian->mahasiswa_id !== $mahasiswa->id, 403);

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiHarian->supervisi_matkul_id)
            ->where('minggu_ke', $supervisiHarian->minggu_ke)
            ->exists();

        if ($sudahRekap) {
            return back()->with('error', 'Entri ini tidak bisa diedit karena sudah direkap.');
        }

        $supervisiMatkulList = SupervisiMatkul::with(['mataKuliah', 'dosen.user'])
            ->where('mahasiswa_id', $mahasiswa->id)->get();

        return view('supervisi_harian.edit', compact('supervisiHarian', 'supervisiMatkulList'));
    }

    public function update(Request $request, SupervisiHarian $supervisiHarian)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        abort_if($supervisiHarian->mahasiswa_id !== $mahasiswa->id, 403);

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiHarian->supervisi_matkul_id)
            ->where('minggu_ke', $supervisiHarian->minggu_ke)
            ->exists();

        if ($sudahRekap) {
            return back()->with('error', 'Entri ini tidak bisa diedit karena sudah direkap.');
        }

        $request->validate([
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'aktivitas'   => 'required|string|max:2000',
            'kendala'     => 'nullable|string|max:1000',
            'dokumentasi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $dokumentasiPath = $supervisiHarian->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($dokumentasiPath && Storage::disk('public')->exists($dokumentasiPath)) {
                Storage::disk('public')->delete($dokumentasiPath);
            }
            $dokumentasiPath = $request->file('dokumentasi')->store('supervisi_harian', 'public');
        }

        $supervisiHarian->update([
            'hari'        => $request->hari,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'aktivitas'   => $request->aktivitas,
            'kendala'     => $request->kendala,
            'dokumentasi' => $dokumentasiPath,
        ]);

        return redirect()->route('supervisi_harian.index')
            ->with('success', 'Entri supervisi harian berhasil diperbarui.');
    }

    public function destroy(SupervisiHarian $supervisiHarian)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        abort_if($supervisiHarian->mahasiswa_id !== $mahasiswa->id, 403);

        $sudahRekap = SupervisiMingguan::where('mahasiswa_id', $mahasiswa->id)
            ->where('supervisi_matkul_id', $supervisiHarian->supervisi_matkul_id)
            ->where('minggu_ke', $supervisiHarian->minggu_ke)
            ->exists();

        if ($sudahRekap) {
            return back()->with('error', 'Entri ini tidak bisa dihapus karena sudah direkap.');
        }

        if ($supervisiHarian->dokumentasi && Storage::disk('public')->exists($supervisiHarian->dokumentasi)) {
            Storage::disk('public')->delete($supervisiHarian->dokumentasi);
        }

        $supervisiHarian->delete();
        return back()->with('success', 'Entri supervisi harian berhasil dihapus.');
    }

    private function authorizeAccess(SupervisiHarian $supervisiHarian)
    {
        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            abort_if($supervisiHarian->mahasiswa_id !== $user->mahasiswa->id, 403);
        } elseif (!in_array($user->role, ['dosen', 'admin'])) {
            abort(403);
        }
    }
}