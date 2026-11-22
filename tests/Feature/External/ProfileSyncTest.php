<?php

namespace Tests\Feature\External;

use App\External\Models\Dosen as ExtDosen;
use App\External\Models\Mahasiswa as ExtMahasiswa;
use App\External\Models\ProgramStudi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSyncTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, ?string $prodi = null): User
    {
        return User::create([
            'name' => "Uji {$email}", 'email' => $email, 'password' => 'password',
            'role' => $role, 'status' => 'active', 'prodi' => $prodi,
        ]);
    }

    private function prodiId(string $kode = 'TRO'): int
    {
        return ProgramStudi::firstOrCreate(['nama_prodi' => $kode])->id_prodi;
    }

    public function test_mahasiswa_internal_otomatis_dibuatkan_profil_external(): void
    {
        $u = $this->user('mahasiswa', 'a@uji.test');
        Mahasiswa::create(['user_id' => $u->id, 'nim' => 'T001', 'nama' => 'Budi', 'kelas' => '3A']);

        $this->assertDatabaseHas('external_mahasiswas', [
            'nim' => 'T001', 'user_id' => $u->id, 'nama_mhs' => 'Budi', 'kelas' => '3A',
        ]);
        $this->assertDatabaseHas('program_studis', ['nama_prodi' => config('ppi.default_prodi')]);
    }

    public function test_mahasiswa_tanpa_kelas_tetap_bisa_disinkronkan(): void
    {
        $u = $this->user('mahasiswa', 'b@uji.test');
        Mahasiswa::create(['user_id' => $u->id, 'nim' => 'T002', 'nama' => 'Sari']);

        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'T002', 'kelas' => '-']);
    }

    public function test_profil_external_otomatis_dibuatkan_profil_internal(): void
    {
        $u = $this->user('mahasiswa', 'c@uji.test');
        ExtMahasiswa::create([
            'nim' => 'T003', 'user_id' => $u->id, 'nama_mhs' => 'Dewi', 'kelas' => '3B', 'id_prodi' => $this->prodiId(),
        ]);

        $this->assertDatabaseHas('mahasiswa', ['nim' => 'T003', 'user_id' => $u->id, 'nama' => 'Dewi', 'kelas' => '3B']);
    }

    public function test_perubahan_nama_internal_ikut_ke_external(): void
    {
        $u = $this->user('mahasiswa', 'd@uji.test');
        $m = Mahasiswa::create(['user_id' => $u->id, 'nim' => 'T004', 'nama' => 'Lama', 'kelas' => '3A']);

        $m->update(['nama' => 'Baru']);

        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'T004', 'nama_mhs' => 'Baru']);
    }

    public function test_nim_berbeda_tidak_ditimpa_dan_tidak_menduplikasi(): void
    {
        $u = $this->user('mahasiswa', 'e@uji.test');
        Model::withoutEvents(fn () => ExtMahasiswa::create([
            'nim' => 'X001', 'user_id' => $u->id, 'nama_mhs' => 'Asli', 'kelas' => '3A', 'id_prodi' => $this->prodiId(),
        ]));

        Mahasiswa::create(['user_id' => $u->id, 'nim' => 'Y001', 'nama' => 'Asli', 'kelas' => '3A']);

        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'X001', 'user_id' => $u->id]);
        $this->assertDatabaseMissing('external_mahasiswas', ['nim' => 'Y001']);
        $this->assertSame(1, ExtMahasiswa::where('user_id', $u->id)->count());
    }

    public function test_dosen_tersinkron_dua_arah(): void
    {
        $a = $this->user('dosen', 'f@uji.test');
        Dosen::create(['user_id' => $a->id, 'nidn' => 'D001', 'nama_dosen' => 'Pak A']);
        $this->assertDatabaseHas('external_dosens', ['nidn' => 'D001', 'user_id' => $a->id, 'nama_dosen' => 'Pak A']);

        $b = $this->user('dosen', 'g@uji.test');
        ExtDosen::create(['nidn' => 'D002', 'user_id' => $b->id, 'nama_dosen' => 'Bu B']);
        $this->assertDatabaseHas('dosen', ['nidn' => 'D002', 'user_id' => $b->id, 'nama_dosen' => 'Bu B']);
    }

    public function test_perintah_profiles_sync_menyamakan_data_lama(): void
    {
        $u = $this->user('mahasiswa', 'h@uji.test');
        Model::withoutEvents(fn () => Mahasiswa::create([
            'user_id' => $u->id, 'nim' => 'T005', 'nama' => 'Lama', 'kelas' => '3A',
        ]));

        // dry-run: tidak menulis apa pun
        $this->artisan('profiles:sync', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseMissing('external_mahasiswas', ['nim' => 'T005']);

        // sungguhan: profil External dibuat
        $this->artisan('profiles:sync')->assertSuccessful();
        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'T005', 'user_id' => $u->id]);
    }

    // ------------------------------------------------------------ Prodi
    public function test_prodi_mahasiswa_dipetakan_ke_kode_prodi_external(): void
    {
        foreach (['informatika' => 'TRIN', 'otomasi' => 'TRO', 'mekatronika' => 'TRMO'] as $slug => $kode) {
            $u = $this->user('mahasiswa', "p-{$slug}@uji.test", $slug);
            Mahasiswa::create(['user_id' => $u->id, 'nim' => "P-{$slug}", 'nama' => 'X', 'kelas' => '3A']);

            $this->assertSame($kode, ExtMahasiswa::find("P-{$slug}")->programStudi->nama_prodi);
        }
    }

    public function test_perubahan_prodi_user_ikut_ke_external(): void
    {
        $u = $this->user('mahasiswa', 'q@uji.test', 'otomasi');
        Mahasiswa::create(['user_id' => $u->id, 'nim' => 'T010', 'nama' => 'Fajar', 'kelas' => '3A']);
        $this->assertSame('TRO', ExtMahasiswa::find('T010')->programStudi->nama_prodi);

        $u->update(['prodi' => 'mekatronika']);

        $this->assertSame('TRMO', ExtMahasiswa::find('T010')->programStudi->nama_prodi);
    }

    public function test_prodi_external_mengisi_prodi_user_internal_yang_kosong(): void
    {
        $u = $this->user('mahasiswa', 'r@uji.test');
        ExtMahasiswa::create([
            'nim' => 'T011', 'user_id' => $u->id, 'nama_mhs' => 'Eka', 'kelas' => '3A', 'id_prodi' => $this->prodiId('TRIN'),
        ]);

        $this->assertSame('informatika', $u->fresh()->prodi);
    }

    public function test_buat_dan_hapus_akun_lewat_admin_external_tersinkron_ke_internal(): void
    {
        $admin = $this->user('admin', 'admin-ext@uji.test');

        $this->actingAs($admin)
            ->postJson(route('external.admin.users.store'), [
                'role'       => 'dosen',
                'name'       => 'Dosen Baru Ext',
                'email'      => 'dosen-ext@uji.test',
                'identifier' => 'D999',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $dosenUser = User::where('email', 'dosen-ext@uji.test')->firstOrFail();
        $this->assertSame('active', $dosenUser->status);
        $this->assertSame('dosen_pengampu', $dosenUser->role_aktif);
        $this->assertSame('dosen_pengampu', $dosenUser->akses_role);
        $this->assertDatabaseHas('dosen', ['user_id' => $dosenUser->id, 'nidn' => 'D999']);
        $this->assertDatabaseHas('external_dosens', ['user_id' => $dosenUser->id, 'nidn' => 'D999']);

        // Hapus lewat Admin External -> kedua tabel bersih
        $this->actingAs($admin)
            ->deleteJson(route('external.admin.users.destroy', $dosenUser->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('dosen', ['user_id' => $dosenUser->id]);
        $this->assertDatabaseMissing('external_dosens', ['user_id' => $dosenUser->id]);
    }

    public function test_hapus_profil_internal_ikut_menghapus_profil_external(): void
    {
        $uMhs = $this->user('mahasiswa', 'del-mhs@uji.test');
        $mhs = Mahasiswa::create(['user_id' => $uMhs->id, 'nim' => 'DEL01', 'nama' => 'Hapus Mhs', 'kelas' => '3A']);
        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'DEL01']);

        $mhs->delete();
        $this->assertDatabaseMissing('external_mahasiswas', ['nim' => 'DEL01']);

        $uDosen = $this->user('dosen', 'del-dsn@uji.test');
        $dsn = Dosen::create(['user_id' => $uDosen->id, 'nidn' => 'DELD1', 'nama_dosen' => 'Hapus Dosen']);
        $this->assertDatabaseHas('external_dosens', ['nidn' => 'DELD1']);

        $dsn->delete();
        $this->assertDatabaseMissing('external_dosens', ['nidn' => 'DELD1']);
    }

    public function test_hapus_mahasiswa_dan_dosen_lewat_controller_internal_menghapus_user_dan_profil_external(): void
    {
        $admin = $this->user('admin', 'admin-int@uji.test');

        // Buat mahasiswa lewat endpoint Master Data Internal
        $this->actingAs($admin)->post('/mahasiswa', [
            'nim'   => 'MHS900',
            'nama'  => 'Mhs Internal',
            'prodi' => 'informatika',
            'kelas' => '3A',
        ])->assertRedirect('/mahasiswa');

        $mhs = Mahasiswa::where('nim', 'MHS900')->firstOrFail();
        $userId = $mhs->user_id;
        $this->assertDatabaseHas('external_mahasiswas', ['nim' => 'MHS900']);
        $this->assertDatabaseHas('users', ['id' => $userId]);

        // Hapus mahasiswa lewat endpoint Internal -> mahasiswa, external_mahasiswas, dan users bersih
        $this->actingAs($admin)->delete("/mahasiswa/{$mhs->id}")->assertRedirect(route('mahasiswa.index'));

        $this->assertDatabaseMissing('mahasiswa', ['nim' => 'MHS900']);
        $this->assertDatabaseMissing('external_mahasiswas', ['nim' => 'MHS900']);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
