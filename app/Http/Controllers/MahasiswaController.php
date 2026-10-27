<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Imports\MahasiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 20, 50, 100]) ? $request->per_page : 20;
        $search  = $request->search;
        
        $user = auth()->user();
        
        // Ambil prodi khusus dari user_roles jika ia adalah KPS
        $prodiKps = \App\Models\UserRole::where('user_id', $user->id)->where('role', 'kps')->value('prodi');
        
        // Paksakan filter prodi menggunakan prodiKps jika KPS, jika bukan gunakan input request
        $prodi = $user->isKps() ? $prodiKps : $request->prodi;
        $kelas = $request->kelas;

        $query = Mahasiswa::with('user')
            ->when($search, function ($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                });
            })
            ->when($prodi, function ($q) use ($prodi) {
                $q->whereHas('user', fn($u) => $u->where('prodi', $prodi));
            })
            ->when($kelas, function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            })
            ->orderBy('nama');

        $mahasiswas = $perPage === 'semua'
            ? $query->get()
            : $query->paginate($perPage)->withQueryString();

        $kelasListQuery = Mahasiswa::whereNotNull('kelas');
        if ($user->isKps()) {
            $kelasListQuery->whereHas('user', fn($u) => $u->where('prodi', $prodiKps));
        }
        $kelasList = $kelasListQuery->distinct()->orderBy('kelas')->pluck('kelas');

        $allMhsQuery = Mahasiswa::with('user');
        if ($user->isKps()) {
            $allMhsQuery->whereHas('user', fn($u) => $u->where('prodi', $prodiKps));
        }
        $allMhs = $allMhsQuery->get();

        return view('mahasiswa.index', compact(
            'mahasiswas', 'kelasList', 'allMhs',
            'search', 'prodi', 'kelas', 'perPage'
        ));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'   => 'required|unique:mahasiswa,nim',
            'nama'  => 'required|string|max:100',
            'prodi' => 'required|in:mekatronika,otomasi,informatika',
            'kelas' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->nim . '@pbl.com',
            'password' => Hash::make($request->nim),
            'role'     => 'mahasiswa',
            'prodi'    => $request->prodi,
        ]);

        Mahasiswa::create([
            'user_id' => $user->id,
            'nim'     => $request->nim,
            'nama'    => $request->nama,
            'kelas'   => $request->kelas,
        ]);

        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    public function batchCreate()
    {
        return view('mahasiswa.batch_create');
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File wajib diupload.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $import = new MahasiswaImport();
        Excel::import($import, $request->file('file'));

        $message = "{$import->inserted} mahasiswa berhasil ditambahkan.";

        if (!empty($import->errors)) {
            return redirect()->route('mahasiswa.index')
                ->with('success', $message)
                ->with('warnings', $import->errors);
        }

        return redirect()->route('mahasiswa.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $filename = 'template_mahasiswa.csv';

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nim', 'nama', 'prodi', 'kelas']);
            fputcsv($file, ['223443001', 'Contoh Nama Mahasiswa', 'informatika', '3 AEA 3']);
            fputcsv($file, ['223442001', 'Contoh Nama Lainnya',   'mekatronika', '3 AEB 4']);
            fputcsv($file, ['223441001', 'Contoh Nama Lainnya 2', 'otomasi',     '3 AEC 3']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim'   => 'required|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama'  => 'required|string|max:100',
            'prodi' => 'required|in:mekatronika,otomasi,informatika',
            'kelas' => 'nullable|string|max:20',
        ]);

        $mahasiswa->update([
            'nim'   => $request->nim,
            'nama'  => $request->nama,
            'kelas' => $request->kelas,
        ]);

        $mahasiswa->user->update([
            'name'  => $request->nama,
            'prodi' => $request->prodi,
        ]);

        return redirect('/mahasiswa')->with('success', 'Data mahasiswa berhasil diupdate!');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->laporan()->delete();
        $mahasiswa->logbook()->delete();
        $mahasiswa->logbookHarian()->delete();
        $mahasiswa->penilaianManager()->delete();
        $mahasiswa->penilaianDosen()->delete();
        $mahasiswa->proyek()->detach();

        $user = $mahasiswa->user;
        $mahasiswa->delete();
        $user->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}