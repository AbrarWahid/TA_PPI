<?php

namespace App\Http\Middleware;

use App\Models\PengajuanProyek;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $authData = null;
        $sidebarCounts = [
            'pending_akun'   => 0,
            'pending_proyek' => 0,
        ];
        $sidebarSections = [];

        if ($user) {
            $roleEfektif = $user->getRoleEfektif();
            $roleLabel = match ($roleEfektif) {
                'admin'          => 'Administrator',
                'manager_proyek' => 'Manager Proyek',
                'dosen_pengampu' => 'Dosen Pengampu',
                'kps'            => 'Koordinator Prodi',
                'mahasiswa'      => 'Mahasiswa',
                'mentor'         => 'Mentor Industri',
                default          => $roleEfektif,
            };

            if (in_array($user->role, ['admin', 'dosen'], true)) {
                $sidebarSections = ['PBL (Internal)', 'PPI Industri (Eksternal)'];
                if ($user->roleAktifKps()) {
                    $sidebarSections[] = 'Manajemen Formulir';
                }
            } elseif ($user->role === 'mentor') {
                $sidebarSections = ['Menu Mentor'];
            } else {
                $sidebarSections = ['Menu PPI'];
            }

            if ($user->isAdmin()) {
                $sidebarCounts = [
                    'pending_akun'   => User::whereIn('role', ['dosen', 'manager_proyek'])->where('status', 'pending')->count(),
                    'pending_proyek' => PengajuanProyek::where('status', 'pending')->count(),
                ];
            }

            $notifications = [];
            $unreadCount = 0;
            if (in_array($user->role, ['dosen', 'mentor'], true)) {
                $unreadCount = $user->unreadNotifications()->count();
                $notifications = $user->notifications()->take(15)->get()->map(fn ($n) => [
                    'id'         => $n->id,
                    'read_at'    => $n->read_at,
                    'title'      => $n->data['title'] ?? 'Pemberitahuan',
                    'message'    => $n->data['message'] ?? 'Ada pembaruan data.',
                    'url'        => $n->data['url'] ?? '#',
                    'status'     => $n->data['status'] ?? 'pending',
                    'time_human' => $n->created_at?->diffForHumans(),
                ])->values()->all();
            }

            $authData = [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'role'            => $user->role,
                'role_aktif'      => $user->role_aktif ?? ($user->role === 'dosen' ? 'dosen_pengampu' : null),
                'akses_role'      => $user->akses_role ?? 'keduanya',
                'role_efektif'    => $roleEfektif,
                'role_label'      => $roleLabel,
                'prodi'           => $user->prodi,
                'available_roles' => $user->role === 'dosen' ? $user->getAvailableRoles() : [],
                'prodi_kps'       => $user->role === 'dosen' ? $user->getProdiKps() : null,
                'is_kps'          => $user->isKps(),
                'role_aktif_kps'  => $user->roleAktifKps(),
                'unread_count'    => $unreadCount,
                'notifications'   => $notifications,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $authData,
            ],
            'sidebarCounts'   => $sidebarCounts,
            'sidebarSections' => $sidebarSections,
            'navigation'      => [
                'path'       => $request->path(),
                'route_name' => $request->route()?->getName(),
            ],
            'flash' => [
                'status'  => $request->session()->get('status'),
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ];
    }
}
