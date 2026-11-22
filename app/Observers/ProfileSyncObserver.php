<?php

namespace App\Observers;

use App\External\Models\Dosen as ExtDosen;
use App\External\Models\Mahasiswa as ExtMahasiswa;
use App\External\Models\User as ExtUser;
use App\Models\Dosen as IntDosen;
use App\Models\Mahasiswa as IntMahasiswa;
use App\Models\User;
use App\Services\ProfileSync;
use Illuminate\Database\Eloquent\Model;

class ProfileSyncObserver
{
    public function __construct(private ProfileSync $sync)
    {
    }

    public function saved(Model $model): void
    {
        try {
            match (true) {
                $model instanceof IntMahasiswa => $this->sync->mahasiswaToExternal($model),
                $model instanceof ExtMahasiswa => $this->sync->mahasiswaToInternal($model),
                $model instanceof IntDosen     => $this->sync->dosenToExternal($model),
                $model instanceof ExtDosen     => $this->sync->dosenToInternal($model),
                $model instanceof User         => $this->userSaved($model),
                default                        => null,
            };
        } catch (\Throwable $e) {
            // Sinkronisasi tidak boleh menggagalkan penyimpanan utama.
            report($e);
        }
    }

    public function deleted(Model $model): void
    {
        try {
            match (true) {
                $model instanceof IntMahasiswa => $this->sync->deleteMahasiswaExternal($model),
                $model instanceof ExtMahasiswa => $this->sync->deleteMahasiswaInternal($model),
                $model instanceof IntDosen     => $this->sync->deleteDosenExternal($model),
                $model instanceof ExtDosen     => $this->sync->deleteDosenInternal($model),
                $model instanceof User,
                $model instanceof ExtUser      => $this->sync->deleteUserProfiles((int) $model->id),
                default                        => null,
            };
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Prodi user berubah -> prodi di profil External mahasiswa ikut berubah. */
    private function userSaved(User $user): void
    {
        if (! $user->wasChanged('prodi')) {
            return;
        }

        $mahasiswa = IntMahasiswa::where('user_id', $user->id)->first();
        if ($mahasiswa) {
            $this->sync->mahasiswaToExternal($mahasiswa);
        }
    }
}
