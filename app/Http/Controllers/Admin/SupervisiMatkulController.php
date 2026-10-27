<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\SupervisiMatkul;
use Illuminate\Http\Request;

class SupervisiMatkulController extends Controller
{
    public function index()
    {
        $supervisi = SupervisiMatkul::with(['mahasiswa.user', 'mataKuliah', 'dosen.user'])
            ->latest()
            ->get();

        $grouped = $supervisi->groupBy(function($s) {
            return $s->mata_kuliah_id . '_' . $s->dosen_id . '_' . $s->tahun_ajaran . '_' . $s->semester;
        });

        $totalSupervisi = $supervisi->count();

        return view('admin.supervisi_matkul.index', compact('grouped', 'totalSupervisi'));
    }

    public function create()
{
    $periode = \App\Models\PeriodePbl::where('is_aktif', true)->first();

    $mahasiswaList = Mahasiswa::with('user')->get();
    $matkulList    = MataKuliah::orderBy('nama_matkul')->get();
    $dosenList     = Dosen::whereHas('user', fn($q) => $q->where('status', 'active'))
                        ->with('user')->get();

    $prodiList = $mahasiswaList->map(fn($m) => $m->user->prodi)
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();

    return view('admin.supervisi_matkul.create', compact(
        'mahasiswaList', 'matkulList', 'dosenList', 'prodiList', 'periode'
    ));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_ids'   => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
            'mata_kuliah_id'  => 'required|exists:mata_kuliah,id',
            'dosen_id'        => 'required|exists:dosen,id',
            'tahun_ajaran'    => 'required|string|max:10',
            'semester'        => 'required|string|in:ganjil,genap',
        ]);

        $added   = 0;
        $skipped = 0;

        foreach ($validated['mahasiswa_ids'] as $mahasiswaId) {
            $exists = SupervisiMatkul::where('mahasiswa_id', $mahasiswaId)
                ->where('mata_kuliah_id', $validated['mata_kuliah_id'])
                ->where('tahun_ajaran', $validated['tahun_ajaran'])
                ->where('semester', $validated['semester'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            SupervisiMatkul::create([
                'mahasiswa_id'   => $mahasiswaId,
                'mata_kuliah_id' => $validated['mata_kuliah_id'],
                'dosen_id'       => $validated['dosen_id'],
                'tahun_ajaran'   => $validated['tahun_ajaran'],
                'semester'       => $validated['semester'],
            ]);
            $added++;
        }

        $msg = "$added mahasiswa berhasil ditambahkan ke supervisi.";
        if ($skipped > 0) $msg .= " $skipped data dilewati (sudah terdaftar).";

        return redirect()->route('admin.supervisi.index')->with('success', $msg);
    }

    public function destroy(SupervisiMatkul $supervisi)
    {
        $supervisi->delete();
        return back()->with('success', 'Data supervisi berhasil dihapus.');
    }
}