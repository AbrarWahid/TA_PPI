<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePbl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodePblController extends Controller
{
    public function index()
    {
        $periodeList  = PeriodePbl::with('koordinator')
            ->orderByDesc('is_aktif')
            ->orderByDesc('created_at')
            ->get();
        $periodeAktif = PeriodePbl::getAktif();

        return view('admin.periode_pbl.index', compact('periodeList', 'periodeAktif'));
    }

    public function create()
    {
        $dosenList       = User::where('role', 'dosen')->where('status', 'active')->get();
        $tahunAjaranList = $this->generateTahunAjaran();

        return view('admin.periode_pbl.create', compact('dosenList', 'tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'     => 'required|string|max:100',
            'tahun_ajaran'     => 'required|string',
            'semester'         => 'required|in:ganjil,genap',
            'koordinator_id'   => 'required|exists:users,id',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after:tanggal_mulai',
            'frekuensi_logbook' => 'required|in:harian,mingguan',
            'bobot_manager'    => 'required|integer|min:1|max:99',
            'bobot_dosen'      => 'required|integer|min:1|max:99',
        ]);

        if ((int)$request->bobot_manager + (int)$request->bobot_dosen !== 100) {
            return back()->withInput()->with(
                'error',
                'Total bobot harus 100%. Saat ini: '
                    . ((int)$request->bobot_manager + (int)$request->bobot_dosen) . '%'
            );
        }

        DB::transaction(function () use ($request) {
            if ($request->boolean('is_aktif')) {
                PeriodePbl::where('is_aktif', true)->update(['is_aktif' => false]);
            }

            PeriodePbl::create([
                'nama_periode'      => $request->nama_periode,
                'tahun_ajaran'      => $request->tahun_ajaran,
                'semester'          => $request->semester,
                'koordinator_id'    => $request->koordinator_id,
                'mekanisme_proyek'  => 'direct', // Selalu direct, tidak perlu dari form
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'frekuensi_logbook' => $request->frekuensi_logbook,
                'bobot_manager'     => $request->bobot_manager,
                'bobot_dosen'       => $request->bobot_dosen,
                'is_aktif'          => $request->boolean('is_aktif'),
            ]);
        });

        return redirect()->route('admin.periode_pbl.index')
            ->with('success', 'Periode PBL berhasil dibuat.');
    }

    public function edit(PeriodePbl $periode_pbl)
    {
        $dosenList       = User::where('role', 'dosen')->where('status', 'active')->get();
        $tahunAjaranList = $this->generateTahunAjaran();

        return view('admin.periode_pbl.edit', compact('periode_pbl', 'dosenList', 'tahunAjaranList'));
    }

    public function update(Request $request, PeriodePbl $periode_pbl)
    {
        $request->validate([
            'nama_periode'      => 'required|string|max:100',
            'tahun_ajaran'      => 'required|string',
            'semester'          => 'required|in:ganjil,genap',
            'koordinator_id'    => 'required|exists:users,id',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after:tanggal_mulai',
            'frekuensi_logbook' => 'required|in:harian,mingguan',
            'bobot_manager'     => 'required|integer|min:1|max:99',
            'bobot_dosen'       => 'required|integer|min:1|max:99',
        ]);

        if ((int)$request->bobot_manager + (int)$request->bobot_dosen !== 100) {
            return back()->withInput()->with(
                'error',
                'Total bobot harus 100%. Saat ini: '
                    . ((int)$request->bobot_manager + (int)$request->bobot_dosen) . '%'
            );
        }

        DB::transaction(function () use ($request, $periode_pbl) {
            if ($request->boolean('is_aktif') && !$periode_pbl->is_aktif) {
                PeriodePbl::where('is_aktif', true)->update(['is_aktif' => false]);
            }

            $periode_pbl->update([
                'nama_periode'      => $request->nama_periode,
                'tahun_ajaran'      => $request->tahun_ajaran,
                'semester'          => $request->semester,
                'koordinator_id'    => $request->koordinator_id,
                'mekanisme_proyek'  => 'direct', // Selalu direct
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'frekuensi_logbook' => $request->frekuensi_logbook,
                'bobot_manager'     => $request->bobot_manager,
                'bobot_dosen'       => $request->bobot_dosen,
                'is_aktif'          => $request->boolean('is_aktif'),
            ]);
        });

        return redirect()->route('admin.periode_pbl.index')
            ->with('success', 'Periode PBL berhasil diperbarui.');
    }

    public function destroy(PeriodePbl $periode_pbl)
    {
        if ($periode_pbl->is_aktif) {
            return back()->with('error', 'Periode aktif tidak dapat dihapus. Nonaktifkan dulu sebelum menghapus.');
        }

        $jumlahProyek = $periode_pbl->pengajuanProyek()->count();
        if ($jumlahProyek > 0) {
            return back()->with('error', "Periode tidak dapat dihapus karena masih terhubung dengan {$jumlahProyek} proyek.");
        }

        $periode_pbl->delete();

        return redirect()->route('admin.periode_pbl.index')
            ->with('success', 'Periode PBL berhasil dihapus.');
    }

    public function setAktif(PeriodePbl $periode_pbl)
    {
        DB::transaction(function () use ($periode_pbl) {
            PeriodePbl::where('is_aktif', true)->update(['is_aktif' => false]);
            $periode_pbl->update(['is_aktif' => true]);
        });

        return redirect()->route('admin.periode_pbl.index')
            ->with('success', "Periode \"{$periode_pbl->nama_periode}\" sekarang aktif.");
    }

    private function generateTahunAjaran(): array
    {
        $tahunSekarang = (int) date('Y');
        $list = [];
        for ($i = -1; $i <= 2; $i++) {
            $awal   = $tahunSekarang + $i;
            $list[] = $awal . '/' . ($awal + 1);
        }
        return $list;
    }
}