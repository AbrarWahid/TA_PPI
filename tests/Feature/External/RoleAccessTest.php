<?php

namespace Tests\Feature\External;

use App\External\Models\PembimbingIndustri;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\AbstractPaginator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /** role => [role, [dashboard, halaman lain]] */
    public static function halamanPerRole(): array
    {
        return [
            'admin'     => ['admin',     ['external.admin.dashboard',     'external.admin.users']],
            'mahasiswa' => ['mahasiswa', ['external.mahasiswa.dashboard', 'external.mahasiswa.nilai']],
            'dosen'     => ['dosen',     ['external.dosen.dashboard',     'external.dosen.riwayat_ttd']],
            'mentor'    => ['mentor',    ['external.mentor.dashboard',    'external.mentor.riwayat_ttd']],
        ];
    }

    public static function roleDenganMenuExternalDiSidebarUtama(): array
    {
        // Mahasiswa kini hanya melihat 1 set menu sesuai jalur magangnya (lihat JalurMagangTest)
        return ['admin' => ['admin'], 'dosen' => ['dosen']];
    }

    public static function halamanKps(): array
    {
        return [
            'dashboard' => ['external.kaprodi.dashboard'],
            'parameter' => ['external.kaprodi.parameter'],
            'export'    => ['external.kaprodi.export'],
        ];
    }

    private function buatUser(string $role, array $extra = []): User
    {
        $user = User::create(array_merge([
            'name'       => "Uji {$role}",
            'email'      => "{$role}@uji.test",
            'password'   => 'password',
            'role'       => $role,
            'role_aktif' => $role === 'dosen' ? 'dosen_pengampu' : null,
            'status'     => 'active',
        ], $extra));

        match ($role) {
            'mahasiswa' => Mahasiswa::create(['user_id' => $user->id, 'nim' => 'T0001', 'nama' => 'Mahasiswa Uji', 'kelas' => '3A']),
            'dosen'     => Dosen::create(['user_id' => $user->id, 'nidn' => 'D0001', 'nama_dosen' => 'Dosen Uji']),
            'mentor'    => PembimbingIndustri::create(['user_id' => $user->id, 'nama_pem' => 'Mentor Uji', 'perusahaan' => 'PT Uji']),
            default     => null,
        };

        return $user;
    }

    /** Dosen yang sedang aktif sebagai KPS untuk prodi tertentu (lewat user_roles, seperti di Internal). */
    private function buatDosenKps(string $prodi = 'otomasi'): User
    {
        $user = $this->buatUser('dosen', ['email' => 'kps@uji.test', 'role_aktif' => 'kps']);
        UserRole::create(['user_id' => $user->id, 'role' => 'kps', 'prodi' => $prodi]);

        return $user;
    }

    #[DataProvider('halamanPerRole')]
    public function test_halaman_external_terbuka_untuk_rolenya(string $role, array $routes): void
    {
        $user = $this->buatUser($role);

        foreach ($routes as $nama) {
            $this->actingAs($user)->get(route($nama))->assertOk();
        }
    }

    #[DataProvider('halamanPerRole')]
    public function test_pintu_masuk_external_mengarahkan_sesuai_role(string $role, array $routes): void
    {
        $this->actingAs($this->buatUser($role))
            ->get(route('external.dashboard'))
            ->assertRedirect(route($routes[0]));
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('external.dashboard'))->assertRedirect(route('login'));
        $this->get(route('external.mahasiswa.dashboard'))->assertRedirect('/login');
    }

    public function test_role_lain_tidak_bisa_membuka_halaman_external_role_lain(): void
    {
        $mentor = $this->buatUser('mentor');
        $this->actingAs($mentor)->get(route('external.admin.dashboard'))->assertForbidden();
        $this->actingAs($mentor)->get(route('external.mahasiswa.dashboard'))->assertForbidden();

        $mahasiswa = $this->buatUser('mahasiswa');
        $this->actingAs($mahasiswa)->get(route('external.admin.users'))->assertForbidden();
    }

    public function test_dosen_ke_halaman_admin_external_dialihkan_ke_dashboard(): void
    {
        $this->actingAs($this->buatUser('dosen'))
            ->get(route('external.admin.dashboard'))
            ->assertRedirect(route('dashboard'));
    }

    #[DataProvider('roleDenganMenuExternalDiSidebarUtama')]
    public function test_sidebar_utama_menampilkan_menu_external(string $role): void
    {
        $this->actingAs($this->buatUser($role))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('PBL (Internal)')
            ->assertSee('PPI Industri (Eksternal)');
    }

    public function test_dashboard_internal_mengarahkan_mentor_ke_external(): void
    {
        $this->actingAs($this->buatUser('mentor'))
            ->get(route('dashboard'))
            ->assertRedirect(route('external.dashboard'));
    }

    public function test_sidebar_mentor_hanya_berisi_menu_external(): void
    {
        $this->actingAs($this->buatUser('mentor'))
            ->get(route('external.mentor.dashboard'))
            ->assertOk()
            ->assertSee('Menu Mentor')
            ->assertDontSee('Approval Akun');
    }

    // ------------------------------------------------------------ KPS (kaprodi)
    #[DataProvider('halamanKps')]
    public function test_dosen_kps_bisa_membuka_halaman_kaprodi(string $route): void
    {
        $this->actingAs($this->buatDosenKps())->get(route($route))->assertOk();
    }

    public function test_dosen_pengampu_dialihkan_dari_halaman_kaprodi(): void
    {
        $this->actingAs($this->buatUser('dosen'))
            ->get(route('external.kaprodi.dashboard'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_mahasiswa_dan_mentor_ditolak_di_halaman_kaprodi(): void
    {
        $this->actingAs($this->buatUser('mahasiswa'))->get(route('external.kaprodi.dashboard'))->assertForbidden();
        $this->actingAs($this->buatUser('mentor'))->get(route('external.kaprodi.export'))->assertForbidden();
    }

    public function test_sidebar_dosen_kps_menampilkan_menu_kaprodi(): void
    {
        $this->actingAs($this->buatDosenKps())
            ->get(route('external.dosen.dashboard'))
            ->assertOk()
            ->assertSee('Manajemen Formulir');
    }

    public function test_sidebar_dosen_pengampu_tidak_menampilkan_menu_kaprodi(): void
    {
        $this->actingAs($this->buatUser('dosen'))
            ->get(route('external.dosen.dashboard'))
            ->assertOk()
            ->assertDontSee('Manajemen Formulir');
    }

    public function test_kps_hanya_melihat_mahasiswa_prodinya(): void
    {
        foreach ([['otomasi', 'T-OTO'], ['mekatronika', 'T-MEK']] as $i => [$prodi, $nim]) {
            $u = User::create([
                'name' => "Mhs {$prodi}", 'email' => "m{$i}@uji.test", 'password' => 'password',
                'role' => 'mahasiswa', 'prodi' => $prodi, 'status' => 'active',
            ]);
            Mahasiswa::create(['user_id' => $u->id, 'nim' => $nim, 'nama' => "Mhs {$prodi}", 'kelas' => '3A']);
        }

        $this->actingAs($this->buatDosenKps('otomasi'))
            ->get(route('external.kaprodi.export'))
            ->assertOk()
            ->assertViewHas('mahasiswas', function ($data) {
                $koleksi = $data instanceof AbstractPaginator ? $data->getCollection() : collect($data);

                return $koleksi->pluck('nim')->all() === ['T-OTO'];
            });
    }

    public function test_admin_assign_pembimbing_dan_jadwal_monitoring_berhasil(): void
    {
        $admin = $this->buatUser('admin');
        $this->buatUser('mahasiswa');
        $this->buatUser('dosen');
        $mentorUser = $this->buatUser('mentor');
        $mentor = PembimbingIndustri::where('user_id', $mentorUser->id)->first();
        \App\External\Models\Perusahaan::create(['nama_perusahaan' => 'PT Uji']);

        $this->actingAs($admin)
            ->postJson(route('external.admin.mahasiswa.assign'), [
                'nim'    => 'T0001',
                'nidn'   => 'D0001',
                'id_pem' => $mentor->id_pem,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($admin)
            ->postJson(route('external.admin.jadwal.store'), [
                'nidn'       => 'D0001',
                'perusahaan' => 'PT Uji',
                'tanggal'    => '2026-11-15',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_admin_dashboard_otomatis_menginisialisasi_tiga_prodi(): void
    {
        $admin = $this->buatUser('admin');

        $this->actingAs($admin)
            ->get(route('external.admin.dashboard'))
            ->assertOk()
            ->assertViewHas('prodis', function ($prodis) {
                $kodes = $prodis->pluck('nama_prodi')->all();

                return in_array('TRIN', $kodes, true)
                    && in_array('TRO', $kodes, true)
                    && in_array('TRMO', $kodes, true);
            });
    }

    public function test_mentor_simpan_disiplin_kuisioner_dan_evaluasi_logbook_berhasil(): void
    {
        $this->buatUser('mahasiswa');
        $mentorUser = $this->buatUser('mentor');

        $matkul = \App\External\Models\MataKuliah::create([
            'id_prodi'  => \App\External\Models\Mahasiswa::find('T0001')->id_prodi,
            'kd_mat'    => 'MK01',
            'nama_komp' => 'Kompetensi Uji',
            'jam_min'   => 10,
        ]);

        $logbook = \App\External\Models\Logbook::create([
            'nim'        => 'T0001',
            'kd_mat'     => $matkul->kd_mat,
            'tanggal'    => '2026-11-12',
            'durasi_mnt' => 120,
            'kegiatan'   => 'Uji kegiatan industri',
            'status'     => 'Kerja',
        ]);

        $this->actingAs($mentorUser)
            ->postJson(route('external.mentor.disiplin.store'), [
                'nim'       => 'T0001',
                'penilaian' => '{"hadir":100}',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($mentorUser)
            ->postJson(route('external.mentor.kuisioner.store'), [
                'nim'       => 'T0001',
                'penilaian' => '{"skor":5}',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($mentorUser)
            ->postJson(route('external.mentor.logbook.store'), [
                'nim'      => 'T0001',
                'logbooks' => [
                    [
                        'id'             => $logbook->id_log,
                        'nilai'          => 88,
                        'catatan_mentor' => 'Sangat baik',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }
}
