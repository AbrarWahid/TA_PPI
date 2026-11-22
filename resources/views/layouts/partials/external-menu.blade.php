@php
    $extMenu = match ($authUser->role) {
        'admin' => [
            ['external.admin.dashboard',     'Pusat Kendali',      'apartment'],
            ['external.admin.users',         'Kelola Akun',        'manage_accounts'],
        ],
        'dosen' => array_merge(
            [
                ['external.dosen.dashboard',   'Dashboard TTD', 'apartment'],
                ['external.dosen.riwayat_ttd', 'Riwayat TTD',   'history'],
            ],
            // Dosen yang sedang aktif sebagai KPS juga mendapat menu Kaprodi
            $authUser->roleAktifKps() ? [
                ['external.kaprodi.dashboard', 'Pusat Kendali KPS',  'tune'],
                ['external.kaprodi.parameter', 'Manajemen Formulir', 'dynamic_form'],
                ['external.kaprodi.export',    'Ekspor Data',        'download'],
            ] : []
        ),
        'mentor' => [
            ['external.mentor.dashboard',    'Dashboard Mentor',   'apartment'],
            ['external.mentor.riwayat_ttd',  'Riwayat TTD',        'history'],
        ],
        default => [],
    };
@endphp
@foreach ($extMenu as [$extRoute, $extLabel, $extIcon])
    <a href="{{ route($extRoute) }}"
       class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors
       {{ request()->routeIs($extRoute . '*') ? 'bg-[#013981] text-white font-medium shadow-surface' : 'text-[#52525b] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] font-normal' }}">
        <span class="material-symbols-outlined text-[18px]">{{ $extIcon }}</span>
        <span>{{ $extLabel }}</span>
    </a>
@endforeach
