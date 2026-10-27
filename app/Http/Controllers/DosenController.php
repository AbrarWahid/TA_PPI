<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use App\Imports\DosenImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::with('user')->get();
        return view('dosen.index', compact('dosens'));
    }

    public function create()
    {
        return view('dosen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn'       => 'required|unique:dosen,nidn',
            'nama_dosen' => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'akses_role' => 'required|in:dosen_pengampu,manager_proyek,keduanya',
        ]);

        $aksesRole = $request->akses_role;
        $roleAktif = match($aksesRole) {
            'manager_proyek' => 'manager_proyek',
            'keduanya'       => 'dosen_pengampu',
            default          => 'dosen_pengampu',
        };

        $user = User::create([
            'name'       => $request->nama_dosen,
            'email'      => $request->email,
            'password'   => Hash::make('password123'),
            'role'       => 'dosen',
            'role_aktif' => $roleAktif,
            'akses_role' => $aksesRole,
            'status'     => 'active',
        ]);

        Dosen::create([
            'user_id'    => $user->id,
            'nidn'       => $request->nidn,
            'nama_dosen' => $request->nama_dosen,
        ]);

        return redirect('/dosen')->with('success', 'Dosen ' . $request->nama_dosen . ' berhasil ditambahkan!');
    }

    public function batchCreate()
    {
        return view('dosen.batch_create');
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

        $import = new DosenImport();
        Excel::import($import, $request->file('file'));

        $message = "{$import->inserted} dosen berhasil ditambahkan.";

        if (!empty($import->errors)) {
            return redirect()->route('dosen.index')
                ->with('success', $message)
                ->with('warnings', $import->errors);
        }

        return redirect()->route('dosen.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $filename = 'template_dosen.csv';

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nidn', 'nama_dosen', 'email', 'akses_role']);
            fputcsv($file, ['0012345678', 'Contoh Nama Dosen', 'dosen1@kampus.ac.id', 'dosen_pengampu']);
            fputcsv($file, ['0087654321', 'Contoh Nama Lainnya', 'dosen2@kampus.ac.id', 'keduanya']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function edit(Dosen $dosen)
    {
        return view('dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nidn'       => 'required|unique:dosen,nidn,' . $dosen->id,
            'nama_dosen' => 'required|string|max:100',
            'akses_role' => 'required|in:dosen_pengampu,manager_proyek,keduanya',
        ]);

        $aksesRole = $request->akses_role;
        $roleAktifSekarang = $dosen->user->role_aktif ?? 'dosen_pengampu';
        $roleAktifBaru = match($aksesRole) {
            'dosen_pengampu' => 'dosen_pengampu',
            'manager_proyek' => 'manager_proyek',
            default          => $roleAktifSekarang,
        };

        $dosen->update([
            'nidn'       => $request->nidn,
            'nama_dosen' => $request->nama_dosen,
        ]);

        $dosen->user->update([
            'name'       => $request->nama_dosen,
            'akses_role' => $aksesRole,
            'role_aktif' => $roleAktifBaru,
        ]);

        return redirect('/dosen')->with('success', 'Data dosen ' . $request->nama_dosen . ' berhasil diupdate!');
    }

    public function destroy(Dosen $dosen)
{
    // Cek relasi yang menghalangi penghapusan
    $supervisiMingguan = \DB::table('supervisi_mingguan')->where('dosen_id', $dosen->id)->count();
    $supervisiMatkul   = \DB::table('supervisi_matkul')->where('dosen_id', $dosen->id)->count();
    $penilaianDosen    = \DB::table('penilaian_dosen')->where('dosen_id', $dosen->id)->count();

    $kendala = [];

    if ($supervisiMingguan > 0) {
        $kendala[] = "{$supervisiMingguan} data supervisi mingguan";
    }
    if ($supervisiMatkul > 0) {
        $kendala[] = "{$supervisiMatkul} data supervisi mata kuliah";
    }
    if ($penilaianDosen > 0) {
        $kendala[] = "{$penilaianDosen} data penilaian";
    }

    if (!empty($kendala)) {
        return redirect('/dosen')->with('error',
            'Dosen <strong>' . $dosen->nama_dosen . '</strong> tidak dapat dihapus karena masih memiliki: ' .
            implode(', ', $kendala) . '. Hapus atau pindahkan data tersebut terlebih dahulu.'
        );
    }

    $dosen->user->delete();

    return redirect('/dosen')->with('success', 'Dosen ' . $dosen->nama_dosen . ' berhasil dihapus!');
}
}