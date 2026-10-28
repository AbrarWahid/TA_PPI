<?php

namespace App\External\Http\Controllers;

use Illuminate\Http\Request;
use App\External\Models\SaranPerusahaan;
use App\External\Models\Mahasiswa;
use App\External\Models\MataKuliah;
use App\External\Models\Logbook;
use App\External\Models\Penilaian;
use Barryvdh\DomPDF\Facade\Pdf;

class KaprodiController extends \App\Http\Controllers\Controller
{
    private function prodiId()
    {
        return \App\External\Models\User::find(\Illuminate\Support\Facades\Auth::id())?->kaprodi?->id_prodi;
    }

    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $idProdi = $this->prodiId();

        $kelasAktif = Mahasiswa::when($idProdi, fn($q) => $q->where('id_prodi', $idProdi))
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $mahasiswas = Mahasiswa::when($idProdi, fn($q) => $q->where('id_prodi', $idProdi))
            ->orderBy('kelas')
            ->orderBy('nama_mhs')
            ->get();

        $sarans = SaranPerusahaan::with(['mahasiswa.programStudi'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $disiplins = \App\External\Models\DisiplinMahasiswa::with(['mahasiswa.programStudi', 'mentor'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $kuisioners = \App\External\Models\KuisionerMentor::with(['mahasiswa.programStudi', 'mentor'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $saranMentors = \App\External\Models\SaranMahasiswa::with(['mahasiswa.programStudi', 'pembimbingIndustri'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        return view('external.kaprodi.dashboard', compact('sarans', 'disiplins', 'kuisioners', 'saranMentors', 'user', 'kelasAktif', 'mahasiswas'));
    }

    public function parameter()
    {
        $parameters = \App\External\Models\ParameterPenilaian::all();
        return view('external.kaprodi.parameter', compact('parameters'));
    }

    public function exportIndex()
    {
        $mahasiswas = Mahasiswa::with('programStudi')->when($this->prodiId(), fn ($q, $id) => $q->where('id_prodi', $id))->orderBy('kelas')->orderBy('nama_mhs')->get();
        $mahasiswas->each(fn ($m) => $m->nilai_logbook_lengkap = $this->transkripLengkap($m->nim));
        return view('external.kaprodi.export', compact('mahasiswas'));
    }

    public function transkrip(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::with('programStudi')->when($this->prodiId(), fn ($q, $id) => $q->where('id_prodi', $id))->where('nim', $nim)->firstOrFail();
        abort_unless($this->transkripLengkap($nim), 422, 'Transkrip belum lengkap: isi nilai logbook minggu 1–20, presentasi, makalah, prestasi, dan supervisi terlebih dahulu.');
        
        $matkuls = MataKuliah::where('id_prodi', $mahasiswa->id_prodi)->get();
        $dosen = Penilaian::where('nim', $nim)->whereNotNull('nidn')->first();
        $mentor = Penilaian::where('nim', $nim)->whereNotNull('id_pem')->first();
        
        $pm = (($dosen->n_presentasi ?? 0) * 0.4) + (($dosen->n_makalah ?? 0) * 0.6);
        $ps = (($mentor->n_prestasi ?? 0) * 0.5) + (($mentor->n_supervisi ?? 0) * 0.5);
        
        $courses = $matkuls->map(function ($matkul) use ($nim, $pm, $ps) {
            $entries = Logbook::where('nim', $nim)->where('kd_mat', $matkul->kd_mat)->whereNotNull('nilai')->get();
            
            $raw = $entries->count() > 0 ? (float) $entries->avg('nilai') : 0;
            $jam = $entries->sum('durasi_mnt') / 60;
            
            $akhir = (0.1 * $pm) + (0.3 * $ps) + (0.6 * $raw);
            $grade = match(true) {
                $akhir >= 85 => 'A',
                $akhir >= 78 => 'AB',
                $akhir >= 70 => 'B',
                $akhir >= 63 => 'BC',
                $akhir >= 55 => 'C',
                $akhir >= 40 => 'D',
                default => 'E'
            };
            
            return compact('matkul', 'raw', 'jam', 'akhir', 'grade');
        });

        // Memanggil 'kaprodi.transkrip' sesuai dengan nama file di VS Code Anda
        if ($request->query('export') === 'pdf') {
            $pdf = Pdf::loadView('kaprodi.transkrip', compact('mahasiswa', 'pm', 'ps', 'courses', 'dosen', 'mentor'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("Transkrip_PPI_{$mahasiswa->nim}_{$mahasiswa->nama_mhs}.pdf");
        }

        return view('external.kaprodi.transkrip', compact('mahasiswa', 'pm', 'ps', 'courses'));
    }

    private function transkripLengkap($nim): bool
    {
        $logbookLengkap = Logbook::where('nim', $nim)->whereNotNull('nilai')->distinct('minggu_ke')->count('minggu_ke') === 20;
        $dosen = Penilaian::where('nim', $nim)->whereNotNull('nidn')->first();
        $mentor = Penilaian::where('nim', $nim)->whereNotNull('id_pem')->first();
        
        return $logbookLengkap && 
               $dosen && 
               $mentor && 
               $dosen->n_presentasi > 0 && 
               $dosen->n_makalah > 0 && 
               $mentor->n_prestasi > 0 && 
               $mentor->n_supervisi > 0;
    }

    public function storeParameter(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:presentasi,makalah,saran_mahasiswa,saran_mentor,disiplin_prestasi,kuisioner_mentor',
            'sub_kategori' => 'required|string',
            'indikator' => 'required|array',
        ]);

        \App\External\Models\ParameterPenilaian::create([
            'jenis' => $request->jenis,
            'sub_kategori' => $request->sub_kategori,
            'indikator' => $request->indikator,
        ]);

        return redirect()->back()->with('success', 'Parameter penilaian berhasil ditambahkan.');
    }

    public function updateParameter(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:presentasi,makalah,saran_mahasiswa,saran_mentor,disiplin_prestasi,kuisioner_mentor',
            'sub_kategori' => 'required|string',
            'indikator' => 'required|array',
        ]);

        $parameter = \App\External\Models\ParameterPenilaian::findOrFail($id);
        $parameter->update([
            'jenis' => $request->jenis,
            'sub_kategori' => $request->sub_kategori,
            'indikator' => $request->indikator,
        ]);

        return redirect()->back()->with('success', 'Parameter penilaian berhasil diubah.');
    }

    public function destroyParameter($id)
    {
        $parameter = \App\External\Models\ParameterPenilaian::findOrFail($id);
        $parameter->delete();

        return redirect()->back()->with('success', 'Parameter penilaian berhasil dihapus.');
    }

    public function cetakRekap(Request $request)
    {
        $idProdi = $this->prodiId();
        $kelas = $request->query('kelas'); // Opsional jika ingin cetak per kelas
        
        $query = Mahasiswa::with(['programStudi'])->when($idProdi, fn($q) => $q->where('id_prodi', $idProdi));
            
        if ($kelas) {
            $query->where('kelas', $kelas);
        }
        
        $mahasiswas = $query->orderBy('kelas')->orderBy('nama_mhs')->get();
        
        // Ambil daftar matkul sesuai urutan (PII, SUP, K3, LTD)
        $matkuls = MataKuliah::where('id_prodi', $idProdi)->get();
        
        $rekapData = $mahasiswas->map(function($mhs) use ($matkuls) {
            $penilaian = Penilaian::where('nim', $mhs->nim)->first();
            
            $n_pres = $penilaian->n_presentasi ?? 0;
            $n_mak = $penilaian->n_makalah ?? 0;
            $pm = ($n_pres * 0.4) + ($n_mak * 0.6);
            
            $n_pres_ind = $penilaian->n_prestasi ?? 0;
            $n_sup = $penilaian->n_supervisi ?? 0;
            $ps = ($n_pres_ind * 0.5) + ($n_sup * 0.5);
            
            $courses = [];
            foreach($matkuls as $mk) {
                $entries = Logbook::where('nim', $mhs->nim)->where('kd_mat', $mk->kd_mat)->whereNotNull('nilai')->get();
                $raw = $entries->count() > 0 ? (float) $entries->avg('nilai') : 0;
                $jam = $entries->sum('durasi_mnt') / 60;
                
                $akhir = (0.1 * $pm) + (0.3 * $ps) + (0.6 * $raw);
                $grade = match(true) {
                    $akhir >= 85 => 'A',
                    $akhir >= 78 => 'AB',
                    $akhir >= 70 => 'B',
                    $akhir >= 63 => 'BC',
                    $akhir >= 55 => 'C',
                    $akhir >= 40 => 'D',
                    default => 'E'
                };
                
                $courses[$mk->kd_mat] = [
                    'rata' => $raw,
                    'jam' => $jam,
                    'akhir' => $akhir,
                    'grade' => $grade
                ];
            }
            
            return [
                'nim' => $mhs->nim,
                'nama' => $mhs->nama_mhs,
                'n_pres' => $n_pres,
                'n_mak' => $n_mak,
                'pm' => $pm,
                'n_pres_ind' => $n_pres_ind,
                'n_sup' => $n_sup,
                'ps' => $ps,
                'courses' => $courses
            ];
        });
        
        if ($request->query('export') === 'pdf') {
            $pdf = Pdf::loadView('kaprodi.rekap_all_pdf', compact('rekapData', 'matkuls', 'kelas'));
            // Menggunakan kertas A3 Landscape agar tabel raksasa ala Excel tidak terpotong
            $pdf->setPaper('A3', 'landscape');
            $namaFile = "DAFTAR_NILAI_PPI_" . ($kelas ?? "SEMUA_KELAS") . ".pdf";
            return $pdf->download($namaFile);
        }
        
        return view('external.kaprodi.rekap_all_pdf', compact('rekapData', 'matkuls', 'kelas'));
    }
}