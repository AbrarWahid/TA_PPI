<?php

namespace App\Services;

use App\External\Models\Dosen as ExtDosen;
use App\External\Models\Mahasiswa as ExtMahasiswa;
use App\External\Models\ProgramStudi;
use App\Models\Dosen as IntDosen;
use App\Models\Mahasiswa as IntMahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Menjaga profil mahasiswa & dosen tetap konsisten antara tabel Internal
 * (mahasiswa, dosen, users.prodi) dan External (external_mahasiswas, external_dosens).
 *
 * Aturan:
 *  - Dicocokkan lewat user_id; kalau belum ada, dibuat.
 *  - NIM/NIDN tidak pernah diubah otomatis (jadi primary key & dirujuk FK di External).
 *    Kalau berbeda, dicatat sebagai KONFLIK dan dibiarkan untuk diperiksa manual.
 *  - Nama, kelas, dan prodi disamakan, hanya bila nilai sumber tidak kosong.
 *  - Prodi Internal (users.prodi: informatika/otomasi/mekatronika) dipetakan ke kode
 *    External (TRIN/TRO/TRMO) lewat config('ppi.prodi_map').
 *  - Penugasan External (nidn pembimbing, id_pem) tidak disentuh.
 */
class ProfileSync
{
    public const DIBUAT = 'dibuat';
    public const DIPERBARUI = 'diperbarui';
    public const SAMA = 'sama';
    public const KONFLIK = 'konflik';

    /** Catatan konflik dari pemanggilan terakhir (dipakai command untuk laporan). */
    public array $notes = [];

    // ------------------------------------------------------------ Mahasiswa
    public function mahasiswaToExternal(IntMahasiswa $src, bool $dry = false): string
    {
        $target = ExtMahasiswa::where('user_id', $src->user_id)->first();
        $slugProdi = $src->user?->prodi;

        if (! $target) {
            if (blank($src->nim)) {
                return $this->konflik("Mahasiswa Internal user_id={$src->user_id}: NIM kosong");
            }
            if (ExtMahasiswa::find($src->nim)) {
                return $this->konflik("NIM {$src->nim} sudah dipakai profil External milik user lain");
            }
            if (! $dry) {
                $this->write(fn () => ExtMahasiswa::create([
                    'nim'      => $src->nim,
                    'user_id'  => $src->user_id,
                    'nama_mhs' => $src->nama,
                    'kelas'    => filled($src->kelas) ? $src->kelas : '-',
                    'id_prodi' => $this->prodiIdFor($slugProdi),
                ]));
            }
            return self::DIBUAT;
        }

        if ((string) $target->nim !== (string) $src->nim) {
            return $this->konflik("user_id={$src->user_id}: NIM Internal {$src->nim} ≠ NIM External {$target->nim}");
        }

        return $this->update($target, [
            'nama_mhs' => $src->nama,
            'kelas'    => $src->kelas,
            'id_prodi' => filled($slugProdi) ? $this->prodiIdFor($slugProdi, ! $dry) : null,
        ], $dry);
    }

    public function mahasiswaToInternal(ExtMahasiswa $src, bool $dry = false): string
    {
        $target = IntMahasiswa::where('user_id', $src->user_id)->first();

        if (! $target) {
            if (IntMahasiswa::where('nim', $src->nim)->exists()) {
                return $this->konflik("NIM {$src->nim} sudah dipakai profil Internal milik user lain");
            }
            if (! $dry) {
                $this->write(fn () => IntMahasiswa::create([
                    'user_id' => $src->user_id,
                    'nim'     => $src->nim,
                    'nama'    => $src->nama_mhs,
                    'kelas'   => $src->kelas,
                ]));
                $this->isiProdiUser($src);
            }
            return self::DIBUAT;
        }

        if ((string) $target->nim !== (string) $src->nim) {
            return $this->konflik("user_id={$src->user_id}: NIM External {$src->nim} ≠ NIM Internal {$target->nim}");
        }

        $hasil = $this->update($target, ['nama' => $src->nama_mhs, 'kelas' => $src->kelas], $dry);
        if (! $dry) {
            $this->isiProdiUser($src);
        }
        return $hasil;
    }

    // ------------------------------------------------------------ Dosen
    public function dosenToExternal(IntDosen $src, bool $dry = false): string
    {
        $target = ExtDosen::where('user_id', $src->user_id)->first();

        if (! $target) {
            if (blank($src->nidn)) {
                return $this->konflik("Dosen Internal user_id={$src->user_id}: NIDN kosong");
            }
            if (ExtDosen::find($src->nidn)) {
                return $this->konflik("NIDN {$src->nidn} sudah dipakai profil External milik user lain");
            }
            if (! $dry) {
                $this->write(fn () => ExtDosen::create([
                    'nidn'       => $src->nidn,
                    'user_id'    => $src->user_id,
                    'nama_dosen' => $src->nama_dosen,
                ]));
            }
            return self::DIBUAT;
        }

        if ((string) $target->nidn !== (string) $src->nidn) {
            return $this->konflik("user_id={$src->user_id}: NIDN Internal {$src->nidn} ≠ NIDN External {$target->nidn}");
        }

        return $this->update($target, ['nama_dosen' => $src->nama_dosen], $dry);
    }

    public function dosenToInternal(ExtDosen $src, bool $dry = false): string
    {
        $target = IntDosen::where('user_id', $src->user_id)->first();

        if (! $target) {
            if (IntDosen::where('nidn', $src->nidn)->exists()) {
                return $this->konflik("NIDN {$src->nidn} sudah dipakai profil Internal milik user lain");
            }
            if (! $dry) {
                $this->write(fn () => IntDosen::create([
                    'user_id'    => $src->user_id,
                    'nidn'       => $src->nidn,
                    'nama_dosen' => $src->nama_dosen,
                ]));
            }
            return self::DIBUAT;
        }

        if ((string) $target->nidn !== (string) $src->nidn) {
            return $this->konflik("user_id={$src->user_id}: NIDN External {$src->nidn} ≠ NIDN Internal {$target->nidn}");
        }

        return $this->update($target, ['nama_dosen' => $src->nama_dosen], $dry);
    }

    // ------------------------------------------------------------ Prodi
    /** Slug Internal (otomasi) -> id program studi External (kode TRO). */
    private function prodiIdFor(?string $slug, bool $create = true): int
    {
        $kode = config('ppi.prodi_map', [])[strtolower((string) $slug)] ?? config('ppi.default_prodi', 'TRO');

        if ($create) {
            return ProgramStudi::firstOrCreate(['nama_prodi' => $kode])->id_prodi;
        }

        return ProgramStudi::where('nama_prodi', $kode)->value('id_prodi') ?? -1;
    }

    /** id program studi External -> slug Internal, atau null bila tidak dikenal. */
    private function slugFor(int $idProdi): ?string
    {
        $kode = ProgramStudi::find($idProdi)?->nama_prodi;
        $slug = array_search($kode, config('ppi.prodi_map', []), true);

        return $slug === false ? null : $slug;
    }

    /** Isi atau perbarui users.prodi dari prodi External. */
    private function isiProdiUser(ExtMahasiswa $src): void
    {
        $user = User::find($src->user_id);
        $slug = $this->slugFor((int) $src->id_prodi);

        if ($user && $slug && (blank($user->prodi) || $src->wasChanged('id_prodi')) && $user->prodi !== $slug) {
            $this->write(fn () => $user->forceFill(['prodi' => $slug])->save());
        }
    }

    // ------------------------------------------------------------ Hapus Profil (Dua Arah)
    public function deleteMahasiswaExternal(IntMahasiswa $src): void
    {
        if ($src->user_id) {
            $this->write(fn () => ExtMahasiswa::where('user_id', $src->user_id)->delete());
        }
    }

    public function deleteMahasiswaInternal(ExtMahasiswa $src): void
    {
        if ($src->user_id) {
            $this->write(function () use ($src) {
                $mhsIds = IntMahasiswa::where('user_id', $src->user_id)->pluck('id');
                if ($mhsIds->isNotEmpty()) {
                    DB::table('nilai_mahasiswa')->whereIn('mahasiswa_id', $mhsIds)->delete();
                    DB::table('laporan_mk_ppi')->whereIn('mahasiswa_id', $mhsIds)->delete();
                }
                IntMahasiswa::where('user_id', $src->user_id)->delete();
            });
        }
    }

    public function deleteDosenExternal(IntDosen $src): void
    {
        if ($src->user_id) {
            $this->write(fn () => ExtDosen::where('user_id', $src->user_id)->delete());
        }
    }

    public function deleteDosenInternal(ExtDosen $src): void
    {
        if ($src->user_id) {
            $this->write(fn () => IntDosen::where('user_id', $src->user_id)->delete());
        }
    }

    public function deleteUserProfiles(int $userId): void
    {
        $this->write(function () use ($userId) {
            $mhsIds = IntMahasiswa::where('user_id', $userId)->pluck('id');
            if ($mhsIds->isNotEmpty()) {
                DB::table('nilai_mahasiswa')->whereIn('mahasiswa_id', $mhsIds)->delete();
                DB::table('laporan_mk_ppi')->whereIn('mahasiswa_id', $mhsIds)->delete();
            }
            IntMahasiswa::where('user_id', $userId)->delete();
            ExtMahasiswa::where('user_id', $userId)->delete();
            IntDosen::where('user_id', $userId)->delete();
            ExtDosen::where('user_id', $userId)->delete();
        });
    }

    // ------------------------------------------------------------ Helper
    private function update(Model $target, array $values, bool $dry): string
    {
        $changes = [];
        foreach ($values as $column => $value) {
            if (filled($value) && (string) $target->{$column} !== (string) $value) {
                $changes[$column] = $value;
            }
        }

        if (! $changes) {
            return self::SAMA;
        }
        if (! $dry) {
            $this->write(fn () => $target->forceFill($changes)->save());
        }
        return self::DIPERBARUI;
    }

    /**
     * Tulis tanpa memicu observer lagi (mencegah saling memanggil), di dalam savepoint
     * supaya error di sini tidak menggagalkan transaksi pemanggil (PostgreSQL).
     */
    private function write(callable $fn): void
    {
        DB::transaction(fn () => Model::withoutEvents($fn));
    }

    private function konflik(string $pesan): string
    {
        $this->notes[] = $pesan;
        Log::warning('ProfileSync konflik: ' . $pesan);
        return self::KONFLIK;
    }
}
