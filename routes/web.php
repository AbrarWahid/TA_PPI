<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PengajuanProyekController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\LaporanAdminController;
use App\Http\Controllers\ApprovalDosenController;
use App\Http\Controllers\MatkulController;
use App\Http\Controllers\LogbookHarianController;
use App\Http\Controllers\LogbookMingguanController;
use App\Http\Controllers\Admin\SupervisiMatkulController;
use App\Http\Controllers\Admin\PeriodePblController;
use App\Http\Controllers\SupervisiHarianController;
use App\Http\Controllers\SupervisiMingguanController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth'])->group(function () {

    // ── Pilih Role (khusus dosen) ───────────────────────────────────────────
    Route::get('/pilih-role', function () {
        $user = auth()->user();
        if ($user->role !== 'dosen') {
            return redirect('/dashboard');
        }

        $availableRoles = $user->getAvailableRoles();

        if (count($availableRoles) === 1) {
            $user->update(['role_aktif' => $availableRoles[0]]);
            return redirect('/dashboard');
        }

        return view('auth.pilih_role');
    })->name('pilih.role');

    Route::post('/pilih-role', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'role_aktif' => 'required|in:manager_proyek,dosen_pengampu,kps',
        ]);
        auth()->user()->update(['role_aktif' => $request->role_aktif]);
        return redirect('/dashboard');
    })->name('pilih.role.simpan');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Admin only ──────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        Route::get('admin/prodi', function () {
            return view('admin.data_prodi');
        })->name('admin.prodi.index');

        Route::get('rubrik-penilaian', [PenilaianController::class, 'rubrik'])->name('rubrik.index');
        Route::get('bobot-penilaian',  [PenilaianController::class, 'bobot'])->name('bobot.index');

        Route::get('approval-dosen',                         [ApprovalDosenController::class, 'index'])->name('approval.index');
        Route::patch('approval-dosen/{user}/approve',        [ApprovalDosenController::class, 'approve'])->name('approval.approve');
        Route::patch('approval-dosen/{user}/approve-role',   [ApprovalDosenController::class, 'approveWithRole'])->name('approval.approve.role');
        Route::patch('approval-dosen/{user}/reject',         [ApprovalDosenController::class, 'reject'])->name('approval.reject');

        Route::patch('pengajuan-proyek/{pengajuan_proyek}/approve', [PengajuanProyekController::class, 'approve'])->name('pengajuan_proyek.approve');
        Route::delete('pengajuan-proyek/{pengajuan_proyek}',        [PengajuanProyekController::class, 'destroy'])->name('pengajuan_proyek.destroy');
        Route::patch('pengajuan-proyek/{pengajuan_proyek}/reject',  [PengajuanProyekController::class, 'reject'])->name('pengajuan_proyek.reject');

        Route::get('pengajuan-proyek/{pengajuan_proyek}/assign',  [PengajuanProyekController::class, 'assignMahasiswa'])->name('pengajuan_proyek.assign');
        Route::post('pengajuan-proyek/{pengajuan_proyek}/assign', [PengajuanProyekController::class, 'simpanMahasiswa'])->name('pengajuan_proyek.simpan_mahasiswa');

        // ── Periode PBL ──
        Route::get('admin/periode-pbl',                       [PeriodePblController::class, 'index'])->name('admin.periode_pbl.index');
        Route::get('admin/periode-pbl/create',                [PeriodePblController::class, 'create'])->name('admin.periode_pbl.create');
        Route::post('admin/periode-pbl',                      [PeriodePblController::class, 'store'])->name('admin.periode_pbl.store');
        Route::get('admin/periode-pbl/{periode_pbl}/edit',    [PeriodePblController::class, 'edit'])->name('admin.periode_pbl.edit');
        Route::put('admin/periode-pbl/{periode_pbl}',         [PeriodePblController::class, 'update'])->name('admin.periode_pbl.update');
        Route::delete('admin/periode-pbl/{periode_pbl}',      [PeriodePblController::class, 'destroy'])->name('admin.periode_pbl.destroy');
        Route::patch('admin/periode-pbl/{periode_pbl}/aktif', [PeriodePblController::class, 'setAktif'])->name('admin.periode_pbl.aktif');

        // ── Manajemen KPS ──
        Route::get('admin/kps',               [\App\Http\Controllers\Admin\KpsController::class, 'index'])->name('admin.kps.index');
        Route::post('admin/kps',              [\App\Http\Controllers\Admin\KpsController::class, 'store'])->name('admin.kps.store');
        Route::delete('admin/kps/{userRole}', [\App\Http\Controllers\Admin\KpsController::class, 'destroy'])->name('admin.kps.destroy');
    });

    // ── Admin & KPS ─────────────────────────────────────────────────────────
    Route::middleware('role:admin,kps')->group(function () {

        // ── Laporan Admin ──
        Route::get('laporan-admin', [LaporanAdminController::class, 'index'])->name('laporan.admin');

        // ── Supervisi Matkul (Full CRUD) ──
        Route::get('admin/supervisi',                [SupervisiMatkulController::class, 'index'])->name('admin.supervisi.index');
        Route::get('admin/supervisi/create',         [SupervisiMatkulController::class, 'create'])->name('admin.supervisi.create');
        Route::post('admin/supervisi',               [SupervisiMatkulController::class, 'store'])->name('admin.supervisi.store');
        Route::delete('admin/supervisi/{supervisi}', [SupervisiMatkulController::class, 'destroy'])->name('admin.supervisi.destroy');

        // ── Mata Kuliah (Full CRUD) ──
        Route::get('admin/matkul/batch',    [MatkulController::class, 'batchCreate'])->name('admin.matkul.batch.create');
        Route::post('admin/matkul/batch',   [MatkulController::class, 'batchStore'])->name('admin.matkul.batch.store');
        Route::get('admin/matkul/template', [MatkulController::class, 'downloadTemplate'])->name('admin.matkul.template');
        Route::get('admin/matkul',          [MatkulController::class, 'index'])->name('admin.matkul.index');
        Route::post('admin/matkul',         [MatkulController::class, 'store'])->name('admin.matkul.store');
        Route::delete('admin/matkul/{id}',  [MatkulController::class, 'destroy'])->name('admin.matkul.destroy');
    });

    // ── Manager Proyek only ─────────────────────────────────────────────────
    Route::middleware('role:manager_proyek,dosen')->group(function () {
        Route::patch('logbook/{logbook}/verifikasi',  [LogbookController::class, 'verifikasi'])->name('logbook.verifikasi');
        Route::get('pengajuan-proyek/create',         [PengajuanProyekController::class, 'create'])->name('pengajuan_proyek.create');
        Route::post('pengajuan-proyek',               [PengajuanProyekController::class, 'store'])->name('pengajuan_proyek.store');
        Route::get('logbook-mingguan/verifikasi',     [LogbookMingguanController::class, 'daftarVerifikasi'])->name('logbook_mingguan.daftar_verifikasi');
    });

    // ── Admin & Manager & Dosen & KPS ──────────────────────────────────────
    Route::middleware('role:admin,manager_proyek,dosen,kps')->group(function () {

        Route::get('mahasiswa/batch',    [MahasiswaController::class, 'batchCreate'])->name('mahasiswa.batch.create');
        Route::post('mahasiswa/batch',   [MahasiswaController::class, 'batchStore'])->name('mahasiswa.batch.store');
        Route::get('mahasiswa/template', [MahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template');
        Route::resource('mahasiswa', MahasiswaController::class);

        Route::get('dosen/batch',    [DosenController::class, 'batchCreate'])->name('dosen.batch.create');
        Route::post('dosen/batch',   [DosenController::class, 'batchStore'])->name('dosen.batch.store');
        Route::get('dosen/template', [DosenController::class, 'downloadTemplate'])->name('dosen.template');
        Route::resource('dosen', DosenController::class);

        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::post('kelas/{kelas}/assign',               [KelasController::class, 'assignMahasiswa'])->name('kelas.assign');
        Route::delete('kelas/{kelas}/remove/{mahasiswa}', [KelasController::class, 'removeMahasiswa'])->name('kelas.remove');

        Route::get('pengajuan-proyek', [PengajuanProyekController::class, 'index'])->name('pengajuan_proyek.index');
        Route::get('proyek-pbl',       [PengajuanProyekController::class, 'proyekPbl'])->name('proyek_pbl.index');
    });

    // ── Detail proyek (semua role auth) ────────────────────────────────────
    Route::get('pengajuan-proyek/{pengajuan_proyek}', [PengajuanProyekController::class, 'show'])->name('pengajuan_proyek.show');

    // ── Penilaian ───────────────────────────────────────────────────────────
    Route::middleware('role:manager_proyek,dosen')->group(function () {
        Route::get('penilaian/manager/create', [PenilaianController::class, 'createManager'])->name('penilaian.manager.create');
        Route::post('penilaian/manager',       [PenilaianController::class, 'storeManager'])->name('penilaian.manager.store');
    });

    Route::middleware('role:dosen')->group(function () {
        Route::get('penilaian/dosen/create', [PenilaianController::class, 'createDosen'])->name('penilaian.dosen.create');
        Route::post('penilaian/dosen',       [PenilaianController::class, 'storeDosen'])->name('penilaian.dosen.store');
    });

    Route::middleware('role:admin,manager_proyek,dosen,mahasiswa,kps')->group(function () {
        Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    });

    // ── Verifikasi Laporan (dosen) ──────────────────────────────────────────
    Route::middleware('role:dosen')->group(function () {
        Route::patch('laporan/{laporan}/verifikasi', [LaporanController::class, 'verifikasi'])->name('laporan.verifikasi');
    });

    // ── Mahasiswa only ──────────────────────────────────────────────────────
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('nilai-saya',   [NilaiController::class, 'index'])->name('nilai.index');
        Route::get('feedback-hub', [NilaiController::class, 'feedbackHub'])->name('feedback.index');
    });

    // ── Semua role ──────────────────────────────────────────────────────────
    Route::resource('logbook', LogbookController::class);
    Route::resource('laporan', LaporanController::class);

    // ── Logbook Harian — Mahasiswa ──────────────────────────────────────────
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('logbook-harian',                       [LogbookHarianController::class, 'index'])->name('logbook_harian.index');
        Route::get('logbook-harian/create',                [LogbookHarianController::class, 'create'])->name('logbook_harian.create');
        Route::post('logbook-harian',                      [LogbookHarianController::class, 'store'])->name('logbook_harian.store');
        Route::get('logbook-harian/{logbook_harian}',      [LogbookHarianController::class, 'show'])->name('logbook_harian.show');
        Route::get('logbook-harian/{logbook_harian}/edit', [LogbookHarianController::class, 'edit'])->name('logbook_harian.edit');
        Route::put('logbook-harian/{logbook_harian}',      [LogbookHarianController::class, 'update'])->name('logbook_harian.update');
        Route::delete('logbook-harian/{logbook_harian}',   [LogbookHarianController::class, 'destroy'])->name('logbook_harian.destroy');
        Route::get('logbook-harian-rekap',                 [LogbookHarianController::class, 'rekapMingguan'])->name('logbook_harian.rekap');
    });

    Route::patch('logbook-harian/{logbook_harian}/verifikasi', [LogbookHarianController::class, 'verifikasi'])->name('logbook_harian.verifikasi');

    // ── Logbook Mingguan ────────────────────────────────────────────────────
    Route::get('logbook-mingguan/{logbookMingguan}', [LogbookMingguanController::class, 'show'])
        ->name('logbook_mingguan.show')
        ->middleware('role:mahasiswa,manager_proyek,admin,dosen,kps');

    Route::middleware('role:mahasiswa,admin,manager_proyek,dosen,kps')->group(function () {
        Route::get('logbook-mingguan', [LogbookMingguanController::class, 'index'])->name('logbook_mingguan.index');
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::post('logbook-mingguan/generate',                  [LogbookMingguanController::class, 'generate'])->name('logbook_mingguan.generate');
        Route::delete('logbook-mingguan/{logbookMingguan}/batal', [LogbookMingguanController::class, 'batal'])->name('logbook_mingguan.batal');
    });

    Route::middleware('role:manager_proyek,admin,dosen,kps')->group(function () {
        Route::patch('logbook-mingguan/{logbookMingguan}/verifikasi', [LogbookMingguanController::class, 'verifikasi'])->name('logbook_mingguan.verifikasi');
    });

    // ── SUPERVISI HARIAN ─────────────────────────────────────────────────────
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('supervisi-harian',                         [SupervisiHarianController::class, 'index'])->name('supervisi_harian.index');
        Route::get('supervisi-harian/create',                  [SupervisiHarianController::class, 'create'])->name('supervisi_harian.create');
        Route::post('supervisi-harian',                        [SupervisiHarianController::class, 'store'])->name('supervisi_harian.store');
        Route::get('supervisi-harian/{supervisiHarian}/edit',  [SupervisiHarianController::class, 'edit'])->name('supervisi_harian.edit');
        Route::put('supervisi-harian/{supervisiHarian}',       [SupervisiHarianController::class, 'update'])->name('supervisi_harian.update');
        Route::delete('supervisi-harian/{supervisiHarian}',    [SupervisiHarianController::class, 'destroy'])->name('supervisi_harian.destroy');
    });

    Route::get('supervisi-harian/{supervisiHarian}', [SupervisiHarianController::class, 'show'])
        ->name('supervisi_harian.show')
        ->middleware('role:mahasiswa,dosen,admin,kps');

    // ── SUPERVISI MINGGUAN ───────────────────────────────────────────────────
    Route::middleware('role:mahasiswa')->group(function () {
        Route::post('supervisi-mingguan/generate',                    [SupervisiMingguanController::class, 'generate'])->name('supervisi_mingguan.generate');
        Route::delete('supervisi-mingguan/{supervisiMingguan}/batal', [SupervisiMingguanController::class, 'batal'])->name('supervisi_mingguan.batal');
    });

    Route::middleware('role:mahasiswa,dosen,admin,kps')->group(function () {
        Route::get('supervisi-mingguan', [SupervisiMingguanController::class, 'index'])->name('supervisi_mingguan.index');
    });

    Route::middleware('role:dosen,kps')->group(function () {
        Route::get('supervisi-mingguan/verifikasi/daftar',                [SupervisiMingguanController::class, 'daftarVerifikasi'])->name('supervisi_mingguan.daftar_verifikasi');
        Route::patch('supervisi-mingguan/{supervisiMingguan}/verifikasi', [SupervisiMingguanController::class, 'verifikasi'])->name('supervisi_mingguan.verifikasi');
    });

    Route::get('supervisi-mingguan/{supervisiMingguan}', [SupervisiMingguanController::class, 'show'])
        ->name('supervisi_mingguan.show')
        ->middleware('role:mahasiswa,dosen,admin,kps');

}); // ← akhir middleware(['auth'])

require __DIR__.'/auth.php';