<?php

namespace App\Http\Controllers;

use App\Models\PenilaianManager;
use App\Models\PenilaianDosen;
use App\Models\PengajuanProyek;
use App\Models\SupervisiMatkul;
use App\Models\Mahasiswa;
use App\Models\PeriodePbl;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    // =====================================================================
    // INDEX — daftar penilaian sesuai role
    // =====================================================================
    public function index()
    {
        $user = auth()->user();

        if ($user->isManager()) {
            $proyekIds = PengajuanProyek::where('manager_id', $user->id)->pluck('id');
            $penilaian = PenilaianManager::with(['mahasiswa', 'pengajuanProyek'])
                ->whereIn('pengajuan_proyek_id', $proyekIds)
                ->get();
            return view('penilaian.index_manager', compact('penilaian'));

        } elseif ($user->roleAktifDosen()) {
            $penilaian = PenilaianDosen::with(['mahasiswa', 'supervisiMatkul.mataKuliah'])
                ->where('dosen_id', $user->dosen->id)
                ->get();
            return view('penilaian.index_dosen', compact('penilaian'));

        } elseif ($user->isMahasiswa()) {
            $mahasiswa    = $user->mahasiswa;
            $nilaiManager = PenilaianManager::where('mahasiswa_id', $mahasiswa->id)->first();
            $nilaiDosen   = PenilaianDosen::with('supervisiMatkul.mataKuliah')
                ->where('mahasiswa_id', $mahasiswa->id)->get();
            return view('mahasiswa.nilai', compact('nilaiManager', 'nilaiDosen', 'mahasiswa'));

        } else {
            // Admin & KPS — gabungkan per mahasiswa
            $query = Mahasiswa::with([
                'user',
                'penilaianManager.pengajuanProyek',
                'penilaianDosen.supervisiMatkul.mataKuliah',
            ]);

            $isKps = $user->isKps();

            // KUNCI PRODI UNTUK KPS
            if ($isKps) {
                $prodiKps = \App\Models\UserRole::where('user_id', $user->id)->where('role', 'kps')->value('prodi');
                $query->whereHas('user', function($q) use ($prodiKps) {
                    $q->where('prodi', $prodiKps);
                });
            }

            $mahasiswaList = $query->get();

            // Ambil daftar kelas unik dari mahasiswa yang didapat untuk dropdown filter
            $kelasList = $mahasiswaList->pluck('kelas')->filter()->unique()->sort()->values();

            return view('penilaian.index_admin', compact('mahasiswaList', 'isKps', 'kelasList'));
        }
    }

    // =====================================================================
    // CREATE MANAGER
    // =====================================================================
    public function createManager()
    {
        $user = auth()->user();

        if (!$user->isManager()) {
            abort(403);
        }

        $proyekList = PengajuanProyek::where('manager_id', $user->id)
            ->where('status', 'approved')
            ->with(['mahasiswa'])
            ->get();

        return view('penilaian.create_manager', compact('proyekList'));
    }

    // =====================================================================
    // STORE MANAGER
    // =====================================================================
    public function storeManager(Request $request)
    {
        // Validasi input
        $request->validate([
            'pengajuan_proyek_id'  => 'required|exists:pengajuan_proyek,id',
            'mahasiswa_id'         => 'required|exists:mahasiswa,id',
            'ls_critical_thinking' => 'required|numeric|min:0|max:100',
            'ls_kolaborasi'        => 'required|numeric|min:0|max:100',
            'ls_kreativitas'       => 'required|numeric|min:0|max:100',
            'ls_komunikasi'        => 'required|numeric|min:0|max:100',
            'lf_fleksibilitas'     => 'required|numeric|min:0|max:100',
            'lf_kepemimpinan'      => 'required|numeric|min:0|max:100',
            'lf_produktivitas'     => 'required|numeric|min:0|max:100',
            'lf_social_skill'      => 'required|numeric|min:0|max:100',
            'lp_rpp'               => 'required|numeric|min:0|max:100',
            'lp_logbook'           => 'required|numeric|min:0|max:100',
            'lp_dokumen_projek'    => 'required|numeric|min:0|max:100',
            'catatan_manager'      => 'nullable|string',
        ]);

        // Hitung total dari 11 indikator
        $totalSkor = (
            $request->ls_critical_thinking +
            $request->ls_kolaborasi +
            $request->ls_kreativitas +
            $request->ls_komunikasi +
            $request->lf_fleksibilitas +
            $request->lf_kepemimpinan +
            $request->lf_produktivitas +
            $request->lf_social_skill +
            $request->lp_rpp +
            $request->lp_logbook +
            $request->lp_dokumen_projek
        );

        // Dapatkan rata-rata skala 100 (Total dibagi 11)
        $nilaiManager = $totalSkor / 11;

        // Gunakan updateOrCreate untuk menghindari Duplicate Entry
        \App\Models\PenilaianManager::updateOrCreate(
            [
                'pengajuan_proyek_id' => $request->pengajuan_proyek_id,
                'mahasiswa_id'        => $request->mahasiswa_id,
            ],
            [
                'manager_id'           => auth()->id(),
                'ls_critical_thinking' => $request->ls_critical_thinking,
                'ls_kolaborasi'        => $request->ls_kolaborasi,
                'ls_kreativitas'       => $request->ls_kreativitas,
                'ls_komunikasi'        => $request->ls_komunikasi,
                'lf_fleksibilitas'     => $request->lf_fleksibilitas,
                'lf_kepemimpinan'      => $request->lf_kepemimpinan,
                'lf_produktivitas'     => $request->lf_produktivitas,
                'lf_social_skill'      => $request->lf_social_skill,
                'lp_rpp'               => $request->lp_rpp,
                'lp_logbook'           => $request->lp_logbook,
                'lp_dokumen_projek'    => $request->lp_dokumen_projek,
                'catatan_manager'      => $request->catatan_manager,
                'nilai_manager'        => $nilaiManager, 
            ]
        );

        return redirect()->back()->with('success', 'Penilaian Manager berhasil disimpan/diperbarui!');
    }

    // =====================================================================
    // CREATE DOSEN
    // =====================================================================
    public function createDosen()
    {
        $user = auth()->user();

        if (!$user->roleAktifDosen()) {
            abort(403);
        }

        $supervisiList = SupervisiMatkul::where('dosen_id', $user->dosen->id)
            ->with(['mahasiswa', 'mataKuliah'])
            ->get();

        return view('penilaian.create_dosen', compact('supervisiList'));
    }

    // =====================================================================
    // STORE DOSEN
    // =====================================================================
    public function storeDosen(Request $request)
    {
        // Validasi input
        $request->validate([
            'supervisi_matkul_id' => 'required|exists:supervisi_matkul,id',
            'mahasiswa_id'        => 'required|exists:mahasiswa,id',
            'lit_informasi'       => 'required|numeric|min:0|max:100',
            'lit_media'           => 'required|numeric|min:0|max:100',
            'lit_teknologi'       => 'required|numeric|min:0|max:100',
            'pr_konten'           => 'required|numeric|min:0|max:100',
            'pr_visual'           => 'required|numeric|min:0|max:100',
            'pr_kosakata'         => 'required|numeric|min:0|max:100',
            'pr_tanya_jawab'      => 'required|numeric|min:0|max:100',
            'pr_mata_gerak'       => 'required|numeric|min:0|max:100',
            'la_penulisan'        => 'required|numeric|min:0|max:100',
            'la_pilihan_kata'     => 'required|numeric|min:0|max:100',
            'la_konten'           => 'required|numeric|min:0|max:100',
            'catatan_dosen'       => 'nullable|string',
        ]);

        // Hitung total dari 11 indikator penilaian dosen
        $totalSkor = (
            $request->lit_informasi +
            $request->lit_media +
            $request->lit_teknologi +
            $request->pr_konten +
            $request->pr_visual +
            $request->pr_kosakata +
            $request->pr_tanya_jawab +
            $request->pr_mata_gerak +
            $request->la_penulisan +
            $request->la_pilihan_kata +
            $request->la_konten
        );

        // Dapatkan rata-rata skala 100 (Total dibagi 11)
        $nilaiDosen = $totalSkor / 11;

        // Ambil ID Dosen dari user yang sedang login
        $dosen = \App\Models\Dosen::where('user_id', auth()->id())->first();

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data profil dosen tidak ditemukan.');
        }

        // Gunakan updateOrCreate untuk menghindari Duplicate Entry
        \App\Models\PenilaianDosen::updateOrCreate(
            [
                'supervisi_matkul_id' => $request->supervisi_matkul_id,
                'mahasiswa_id'        => $request->mahasiswa_id,
            ],
            [
                'dosen_id'            => $dosen->id,
                'lit_informasi'       => $request->lit_informasi,
                'lit_media'           => $request->lit_media,
                'lit_teknologi'       => $request->lit_teknologi,
                'pr_konten'           => $request->pr_konten,
                'pr_visual'           => $request->pr_visual,
                'pr_kosakata'         => $request->pr_kosakata,
                'pr_tanya_jawab'      => $request->pr_tanya_jawab,
                'pr_mata_gerak'       => $request->pr_mata_gerak,
                'la_penulisan'        => $request->la_penulisan,
                'la_pilihan_kata'     => $request->la_pilihan_kata,
                'la_konten'           => $request->la_konten,
                'catatan_dosen'       => $request->catatan_dosen,
                'nilai_dosen'         => $nilaiDosen,
            ]
        );

        return redirect()->back()->with('success', 'Penilaian Dosen berhasil disimpan/diperbarui!');
    }

    // =====================================================================
    // HELPER — hitung nilai (DINAMIS DARI PERIODE PBL)
    // =====================================================================
    private function hitungNilaiManager(PenilaianManager $p): float
    {
        $ls = (($p->ls_critical_thinking + $p->ls_kolaborasi + $p->ls_kreativitas + $p->ls_komunikasi) / 4) * 0.20;
        $lf = (($p->lf_fleksibilitas + $p->lf_kepemimpinan + $p->lf_produktivitas + $p->lf_social_skill) / 4) * 0.20;
        $lp = (($p->lp_rpp + $p->lp_logbook + $p->lp_dokumen_projek) / 3) * 0.15;
        
        $totalBase = $ls + $lf + $lp; // Total maksimal dasar adalah 0.55 (55)

        // Ambil bobot dari periode aktif
        $periode = PeriodePbl::getAktif();
        $bobotAktif = $periode ? $periode->bobot_manager : 55;

        // Skalakan base 55 ke 100, lalu kalikan dengan persentase bobot
        return round(($totalBase / 0.55) * $bobotAktif, 2);
    }

    private function hitungNilaiDosen(PenilaianDosen $p): float
    {
        $lit = (($p->lit_informasi + $p->lit_media + $p->lit_teknologi) / 3) * 0.15;
        $pr  = (($p->pr_konten + $p->pr_visual + $p->pr_kosakata + $p->pr_tanya_jawab + $p->pr_mata_gerak) / 5) * 0.15;
        $la  = (($p->la_penulisan + $p->la_pilihan_kata + $p->la_konten) / 3) * 0.15;
        
        $totalBase = $lit + $pr + $la; // Total maksimal dasar adalah 0.45 (45)

        // Ambil bobot dari periode aktif
        $periode = PeriodePbl::getAktif();
        $bobotAktif = $periode ? $periode->bobot_dosen : 45;

        // Skalakan base 45 ke 100, lalu kalikan dengan persentase bobot
        return round(($totalBase / 0.45) * $bobotAktif, 2);
    }

    // =====================================================================
    // MISC
    // =====================================================================
    public function rubrik() { return view('penilaian.rubrik'); }
    public function bobot()  { return view('penilaian.bobot'); }
}