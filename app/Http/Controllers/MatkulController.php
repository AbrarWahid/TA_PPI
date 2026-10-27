<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Imports\MatkulImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MatkulController extends Controller
{
    public function index()
    {
        $matkuls = MataKuliah::orderBy('semester')->orderBy('nama_matkul')->get();
        return view('admin.matkul.index', compact('matkuls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_matkul'   => 'required|string|max:50|unique:App\Models\MataKuliah,kode_matkul',
            'program_studi' => 'required|string|max:100',
            'nama_matkul'   => 'required|string|max:255',
            'sks'           => 'required|integer|min:1|max:10',
            'semester'      => 'required|integer|min:1|max:8',
        ], [
            'kode_matkul.unique' => 'Kode Mata Kuliah ini sudah terdaftar di sistem.',
        ]);

        MataKuliah::create($validated);

        return redirect()->route('admin.matkul.index')
                         ->with('success', 'Data Mata Kuliah berhasil ditambahkan!');
    }

    public function destroy($id)
{
    $matkul = MataKuliah::findOrFail($id);

    // Cek apakah mata kuliah masih digunakan di tabel kelas
    $kelasCount = \DB::table('kelas')->where('matkul_id', $id)->count();

    if ($kelasCount > 0) {
        return redirect()->route('admin.matkul.index')
                         ->with('error', "Mata kuliah \"{$matkul->nama_matkul}\" tidak dapat dihapus karena masih terhubung dengan {$kelasCount} kelas aktif. Hapus data kelas terkait terlebih dahulu.");
    }

    $matkul->delete();

    return redirect()->route('admin.matkul.index')
                     ->with('success', 'Data Mata Kuliah berhasil dihapus!');
}

    // ── BATCH INSERT ──────────────────────────────────────────

    public function batchCreate()
    {
        return view('admin.matkul.batch_create');
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel/CSV terlebih dahulu.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new MatkulImport();
        Excel::import($import, $request->file('file'));

        $msg = "Berhasil mengimpor {$import->inserted} mata kuliah.";

        return redirect()->route('admin.matkul.index')
                         ->with('success', $msg)
                         ->with('warnings', $import->warnings);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_matkul.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header kolom
            fputcsv($file, ['kode_matkul', 'nama_matkul', 'program_studi', 'sks', 'semester']);

            // Contoh data
            fputcsv($file, ['MK001', 'Pemrograman Web', 'informatika', '3', '3']);
            fputcsv($file, ['MK002', 'Sistem Otomasi Industri', 'otomasi', '3', '4']);
            fputcsv($file, ['MK003', 'Robotika Dasar', 'mekatronika', '2', '5']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}