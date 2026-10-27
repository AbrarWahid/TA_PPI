<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Kelas::withCount('mahasiswa')->orderBy('nama_kelas');
        
        // KUNCI PRODI UNTUK KPS
        if ($user->isKps()) {
            $prodiKps = \App\Models\UserRole::where('user_id', $user->id)->where('role', 'kps')->value('prodi');
            $query->whereHas('mahasiswa.user', function($q) use ($prodiKps) {
                $q->where('prodi', $prodiKps);
            });
        }
        
        $kelas = $query->get();
        $periodeAktif = \App\Models\PeriodePbl::where('is_aktif', true)->first();
        
        return view('kelas.index', compact('kelas', 'periodeAktif'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|unique:kelas,kode_kelas|max:20',
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
        ]);

        Kelas::create([
            'kode_kelas' => strtoupper($request->kode_kelas),
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('mahasiswa.user');
        return view('kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'kode_kelas' => 'required|max:20|unique:kelas,kode_kelas,' . $kelas->id,
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $kelas->id,
        ]);

        $kelas->update([
            'kode_kelas' => strtoupper($request->kode_kelas),
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate!');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }

    public function assignMahasiswa(Request $request, Kelas $kelas)
    {
        $request->validate(['mahasiswa_ids' => 'required|array']);
        // Update kolom kelas di tabel mahasiswa
        Mahasiswa::whereIn('id', $request->mahasiswa_ids)
            ->update(['kelas' => $kelas->nama_kelas]);
        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas!');
    }

    public function removeMahasiswa(Kelas $kelas, Mahasiswa $mahasiswa)
    {
        $mahasiswa->update(['kelas' => null]);
        return back()->with('success', 'Mahasiswa berhasil dikeluarkan dari kelas!');
    }
}