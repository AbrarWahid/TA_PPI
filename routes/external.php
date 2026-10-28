<?php

use App\Http\Controllers\ProfileController;
use App\External\Http\Controllers\AdminController;
use App\External\Http\Controllers\MahasiswaController;
use App\External\Http\Controllers\DosenController;
use App\External\Http\Controllers\MentorController;
use App\External\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// Group Route untuk Admin
Route::prefix('external')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('external.admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('external.admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('external.admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('external.admin.users.update');
    Route::put('/admin/users/{id}/password', [AdminController::class, 'changePassword'])->name('external.admin.users.password');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('external.admin.users.destroy');
    Route::post('/admin/perusahaan', [AdminController::class, 'storePerusahaan'])->name('external.admin.perusahaan.store');
    Route::delete('/admin/perusahaan/{id}', [AdminController::class, 'destroyPerusahaan'])->name('external.admin.perusahaan.destroy');
    Route::post('/admin/mahasiswa/assign-pembimbing', [AdminController::class, 'assignPembimbing'])->name('external.admin.mahasiswa.assign');
    Route::post('/admin/matkul', [AdminController::class, 'storeMatkul'])->name('external.admin.matkul.store');
    Route::put('/admin/matkul/{id}', [AdminController::class, 'updateMatkul'])->name('external.admin.matkul.update');
    Route::delete('/admin/matkul/{id}', [AdminController::class, 'deleteMatkul'])->name('external.admin.matkul.destroy');
    Route::post('/admin/jadwal', [AdminController::class, 'storeJadwal'])->name('external.admin.jadwal.store');
    Route::delete('/admin/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('external.admin.jadwal.destroy');
    Route::get('/admin/backup/list', [AdminController::class, 'listBackups']);
    Route::post('/admin/backup/create', [AdminController::class, 'createBackup']);
    Route::post('/admin/backup/delete', [AdminController::class, 'deleteBackupFile']);
    Route::post('/admin/backup/rename', [AdminController::class, 'renameBackup']);
    Route::post('/admin/category/delete-all', [AdminController::class, 'deleteCategoryData']);
});

// Group Route untuk Mahasiswa
Route::prefix('external')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'index'])->name('external.mahasiswa.dashboard');
    Route::post('/mahasiswa/logbook', [MahasiswaController::class, 'storeLogbook'])->name('external.mahasiswa.logbook.store');
    Route::delete('/mahasiswa/logbook/foto/{id}', [MahasiswaController::class, 'deleteLogbookFoto'])->name('external.mahasiswa.logbook.foto.delete');
    Route::post('/mahasiswa/saran', [MahasiswaController::class, 'storeSaran'])->name('external.mahasiswa.saran.store');
    Route::get('/mahasiswa/nilai', [MahasiswaController::class, 'nilai'])->name('external.mahasiswa.nilai');
    Route::get('/mahasiswa/nilai/logbook', [MahasiswaController::class, 'apiNilaiLogbook'])->name('external.mahasiswa.nilai.logbook');
    Route::post('/mahasiswa/nilai/ajukan-qr', [MahasiswaController::class, 'ajukanQR'])->name('external.mahasiswa.nilai.ajukan_qr');
    Route::post('/mahasiswa/pengajuan-ttd', [MahasiswaController::class, 'requestTtd'])->name('external.mahasiswa.request-ttd');
    Route::post('/mahasiswa/nilai/batal-qr', [MahasiswaController::class, 'batalQR'])->name('external.mahasiswa.nilai.batal_qr');
});

// Group Route untuk Dosen
Route::prefix('external')->middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'index'])->name('external.dosen.dashboard');
    Route::post('/dosen/penilaian', [DosenController::class, 'storePenilaian'])->name('external.dosen.penilaian.store');
    Route::get('/dosen/riwayat-ttd', [DosenController::class, 'riwayatTtd'])->name('external.dosen.riwayat_ttd');
});

// Group Route untuk Mentor
Route::prefix('external')->middleware(['auth', 'role:mentor'])->group(function () {
    Route::get('/mentor/dashboard', [MentorController::class, 'index'])->name('external.mentor.dashboard');
    Route::post('/mentor/saran', [MentorController::class, 'storeSaran'])->name('external.mentor.saran.store');
    Route::post('/mentor/disiplin', [MentorController::class, 'storeDisiplin'])->name('external.mentor.disiplin.store');
    Route::post('/mentor/kuisioner', [MentorController::class, 'storeKuisioner'])->name('external.mentor.kuisioner.store');
    Route::post('/mentor/logbook', [MentorController::class, 'storeLogbook'])->name('external.mentor.logbook.store');
    Route::get('/mentor/riwayat-ttd', [MentorController::class, 'riwayatTtd'])->name('external.mentor.riwayat_ttd');
});

// Group Route untuk Kaprodi
Route::prefix('external')->middleware(['auth', 'role:kaprodi'])->group(function () {
    Route::get('/kaprodi/dashboard', [\App\External\Http\Controllers\KaprodiController::class, 'index'])->name('external.kaprodi.dashboard');
    Route::get('/kaprodi/export', [\App\External\Http\Controllers\KaprodiController::class, 'exportIndex'])->name('external.kaprodi.export');
    Route::get('/kaprodi/export/{nim}', [\App\External\Http\Controllers\KaprodiController::class, 'transkrip'])->name('external.kaprodi.transkrip');
    
    // Route untuk manajemen parameter penilaian presentasi
    Route::get('/kaprodi/parameter', [\App\External\Http\Controllers\KaprodiController::class, 'parameter'])->name('external.kaprodi.parameter');
    Route::post('/kaprodi/parameter', [\App\External\Http\Controllers\KaprodiController::class, 'storeParameter'])->name('external.kaprodi.parameter.store');
    Route::put('/kaprodi/parameter/{id}', [\App\External\Http\Controllers\KaprodiController::class, 'updateParameter'])->name('external.kaprodi.parameter.update');
    Route::delete('/kaprodi/parameter/{id}', [\App\External\Http\Controllers\KaprodiController::class, 'destroyParameter'])->name('external.kaprodi.parameter.destroy');
    Route::get('/kaprodi/rekap-all', [\App\External\Http\Controllers\KaprodiController::class, 'cetakRekap'])->name('external.kaprodi.rekap_all');
});

// Group Route untuk Approval Tanda Tangan Digital (QR Code)
// (Tidak dimasukkan ke dalam middleware auth agar Dosen/Mentor bisa langsung scan dan acc dari HP tanpa harus login dulu. Autentikasi menggunakan token unik).
Route::get('/external/approval/{token}', [\App\External\Http\Controllers\ApprovalController::class, 'show'])->name('external.approval.show');
Route::post('/external/approval/{token}/process', [\App\External\Http\Controllers\ApprovalController::class, 'process'])->name('external.approval.process');


// Route bawaan Breeze untuk Profile (Bisa diakses semua role yang login)
Route::middleware('auth')->group(function () {
    
    // ROUTE NOTIFIKASI DIPINDAHKAN KE SINI
    Route::post('/external/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'markAllRead'])->name('external.notifikasi.read_all');
    Route::delete('/external/notifikasi/hapus/{id}', [NotifikasiController::class, 'hapusNotifikasi'])->name('external.notifikasi.delete');
    Route::delete('/external/notifikasi/bersihkan-semua', [NotifikasiController::class, 'bersihkanSemua'])->name('external.notifikasi.clear_all');
    Route::post('/external/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('external.notifikasi.read');

    Route::get('/external/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'dosen') return redirect()->route('dosen.dashboard');
        if ($role === 'mentor') return redirect()->route('mentor.dashboard');
        if ($role === 'kaprodi') return redirect()->route('kaprodi.dashboard');
        return redirect()->route('mahasiswa.dashboard');
    })->name('external.dashboard');

    Route::get('/external/transkrip/{nim}', [MahasiswaController::class, 'transkrip'])->name('external.transkrip.show');
    Route::get('/external/api/rekapan-jam/{nim}', [MahasiswaController::class, 'apiRekapanJam'])->name('external.api.rekapan-jam');

    Route::get('/external/profile', [ProfileController::class, 'edit'])->name('external.profile.edit');
    Route::patch('/external/profile', [ProfileController::class, 'update'])->name('external.profile.update');
    Route::delete('/external/profile', [ProfileController::class, 'destroy'])->name('external.profile.destroy');
});
