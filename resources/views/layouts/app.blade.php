<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PBL Portal') — AE Polman Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.design-system')
    @stack('styles')
</head>
<body x-data="{ mobileOpen: false }" class="bg-[#fafafa] font-sans text-[#0b0b0c] antialiased h-screen flex overflow-hidden">

@php
    $authUser    = auth()->user();
    $roleEfektif = $authUser->getRoleEfektif();
    $roleLabel   = match($roleEfektif) {
        'admin'          => 'Administrator',
        'manager_proyek' => 'Manager Proyek',
        'dosen_pengampu' => 'Dosen Pengampu',
        'kps'            => 'Koordinator Prodi',
        'mahasiswa'      => 'Mahasiswa',
        'mentor'         => 'Mentor Industri',
        default          => $roleEfektif,
    };
    $isDosen          = $authUser->role === 'dosen';
    $roleAktif        = $authUser->role_aktif ?? 'dosen_pengampu';
    $isDosenAsManager = $isDosen && $roleAktif === 'manager_proyek';
    $isDosenAsDosen   = $isDosen && $roleAktif === 'dosen_pengampu';
    $isDosenAsKps     = $isDosen && $roleAktif === 'kps';
    $aksesRoleDosen   = $authUser->akses_role ?? 'keduanya';
    $path             = request()->path();

    $navActive = 'bg-[#013981] text-white font-medium shadow-surface';
    $navIdle   = 'text-[#52525b] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] font-normal';
    $subActive = 'text-[#0b0b0c] font-semibold bg-[#f4f4f5]/80';
    $subIdle   = 'text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5]/50 font-normal';
@endphp

    {{-- MOBILE OVERLAY --}}
    <div x-show="mobileOpen" x-cloak
         x-transition.opacity
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/25 backdrop-blur-[1px] z-30 lg:hidden"></div>

    {{-- ================= SIDEBAR ================= --}}
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 left-0 w-[264px] bg-white border-r border-[#e4e4e7] flex flex-col z-40 flex-shrink-0 h-screen transition-transform duration-200 ease-out">

        {{-- Brand Header --}}
        <div class="h-14 px-5 flex items-center justify-between border-b border-[#e4e4e7] flex-shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-[#013981] border border-[#013981] flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="AE Polman" class="w-5 h-5 object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="text-white font-mono font-semibold text-xs hidden items-center justify-center">AE</span>
                </div>
                <div class="leading-none">
                    <span class="text-[14px] font-semibold tracking-tight text-[#0b0b0c] block">PBL Portal</span>
                    <span class="font-mono text-[10px] text-[#6a6f7c] tracking-wider uppercase block mt-0.5">AE Polman</span>
                </div>
            </a>
            <span class="font-mono text-[10px] text-[#6a6f7c] bg-[#f4f4f5] border border-[#e4e4e7] px-1.5 py-0.5 rounded">v2.4</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- ADMIN --}}
            @if($authUser->isAdmin())
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Main Menu</p>
                <a href="{{ route('dashboard') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path === 'dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>

                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Manajemen</p>

                @php $pendingCount = \App\Models\User::whereIn('role',['dosen','manager_proyek'])->where('status','pending')->count(); @endphp
                <a href="{{ route('approval.index') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'approval-dosen') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <div class="flex items-center gap-2 flex-1">
                        <span>Approval Akun</span>
                        @if($pendingCount > 0)
                            <span class="ml-auto font-mono text-[10px] px-1.5 py-0.5 rounded bg-amber-50 border border-amber-200 text-[#b45309] font-medium">{{ $pendingCount }}</span>
                        @endif
                    </div>
                </a>

                @php $pendingProyek = \App\Models\PengajuanProyek::where('status','pending')->count(); @endphp
                <a href="{{ route('pengajuan_proyek.index') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'pengajuan-proyek') || str_starts_with($path,'proyek-pbl') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <div class="flex items-center gap-2 flex-1">
                        <span>Approval Proyek</span>
                        @if($pendingProyek > 0)
                            <span class="ml-auto font-mono text-[10px] px-1.5 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] font-medium">{{ $pendingProyek }}</span>
                        @endif
                    </div>
                </a>

                <a href="{{ route('admin.periode_pbl.index') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'admin/periode-pbl') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    <span class="flex-1">Periode PBL</span>
                </a>

                @php $masterActive = str_starts_with($path,'mahasiswa') || str_starts_with($path,'dosen') || str_starts_with($path,'admin/prodi') || str_starts_with($path,'admin/matkul') || str_starts_with($path,'kelas') || str_starts_with($path,'admin/kps'); @endphp
                <div x-data="{ open: {{ $masterActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-colors {{ $masterActive ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]' : $navIdle }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px]">database</span>
                            <span>Master Data</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150" :class="open ? 'rotate-90' : ''">chevron_right</span>
                    </button>
                    <div x-show="open" x-cloak class="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                        <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'mahasiswa') ? $subActive : $subIdle }}">
                            <span>Data Mahasiswa</span>
                        </a>
                        <a href="{{ route('dosen.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'dosen') ? $subActive : $subIdle }}">
                            <span>Data Dosen</span>
                        </a>
                        <a href="{{ route('admin.prodi.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'admin/prodi') ? $subActive : $subIdle }}">
                            <span>Data Program Studi</span>
                        </a>
                        <a href="{{ route('admin.matkul.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'admin/matkul') ? $subActive : $subIdle }}">
                            <span>Mata Kuliah</span>
                        </a>
                        <a href="{{ route('kelas.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'kelas') ? $subActive : $subIdle }}">
                            <span>Data Kelas</span>
                        </a>
                        <a href="{{ route('admin.kps.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'admin/kps') ? $subActive : $subIdle }}">
                            <span>Manajemen KPS</span>
                        </a>
                    </div>
                </div>

                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>

                {{-- ACADEMIC & PBL --}}
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PBL (Internal)</p>

                @php $kurikulumActive = str_starts_with($path,'rubrik') || str_starts_with($path,'bobot'); @endphp
                <div x-data="{ open: {{ $kurikulumActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-colors {{ $kurikulumActive ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]' : $navIdle }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px]">menu_book</span>
                            <span>Kurikulum PBL</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150" :class="open ? 'rotate-90' : ''">chevron_right</span>
                    </button>
                    <div x-show="open" x-cloak class="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                        <a href="{{ route('rubrik.index') }}" class="block px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'rubrik') ? $subActive : $subIdle }}">Rubrik Penilaian</a>
                        <a href="{{ route('bobot.index') }}" class="block px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'bobot') ? $subActive : $subIdle }}">Bobot Penilaian</a>
                    </div>
                </div>

                <a href="{{ route('admin.supervisi.index') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'admin/supervisi') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">supervisor_account</span>
                    <span>Supervisi Matkul</span>
                </a>

                <a href="{{ route('laporan.admin') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'laporan-admin') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Monitoring & Laporan</span>
                </a>

                <a href="{{ route('penilaian.index') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'penilaian') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">grade</span>
                    <span>Asesmen & Nilai</span>
                </a>

            {{-- DOSEN sebagai MANAGER PROYEK --}}
            @elseif($isDosenAsManager)
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PBL (Internal)</p>
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path==='dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span><span>Dashboard</span>
                </a>
                <a href="{{ route('penilaian.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'penilaian') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">grade</span><span>Penilaian</span>
                </a>
                <a href="{{ route('pengajuan_proyek.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'pengajuan') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">rocket_launch</span><span>Pengajuan Proyek</span>
                </a>
                <a href="{{ route('logbook_mingguan.daftar_verifikasi') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'logbook-mingguan') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">rate_review</span><span>Verifikasi Logbook</span>
                </a>
                @if($aksesRoleDosen === 'keduanya')
                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>
                <form action="{{ route('pilih.role.simpan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role_aktif" value="dosen_pengampu">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[12px] text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors">
                        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                        <span>Ganti ke Dosen Pengampu</span>
                    </button>
                </form>
                @endif

            {{-- DOSEN sebagai KPS --}}
            @elseif($isDosenAsKps)
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Main Menu</p>
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path==='dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span><span>Dashboard</span>
                </a>

                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>

                @php $masterKpsActive = str_starts_with($path,'mahasiswa') || str_starts_with($path,'admin/matkul') || str_starts_with($path,'kelas'); @endphp
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Master Data</p>
                <div x-data="{ open: {{ $masterKpsActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-colors {{ $masterKpsActive ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]' : $navIdle }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px]">database</span>
                            <span>Master Data</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150" :class="open ? 'rotate-90' : ''">chevron_right</span>
                    </button>
                    <div x-show="open" x-cloak class="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                        <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'mahasiswa') ? $subActive : $subIdle }}">
                            <span>Data Mahasiswa</span>
                        </a>
                        <a href="{{ route('admin.matkul.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'admin/matkul') ? $subActive : $subIdle }}">
                            <span>Mata Kuliah</span>
                        </a>
                        <a href="{{ route('kelas.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ str_starts_with($path,'kelas') ? $subActive : $subIdle }}">
                            <span>Data Kelas</span>
                        </a>
                    </div>
                </div>

                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>

                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PBL (Internal)</p>
                <a href="{{ route('admin.supervisi.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'admin/supervisi') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">supervisor_account</span><span>Supervisi Matkul</span>
                </a>
                <a href="{{ route('laporan.admin') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'laporan-admin') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">monitoring</span><span>Monitoring & Laporan</span>
                </a>
                <a href="{{ route('penilaian.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'penilaian') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">grade</span><span>Asesmen & Nilai</span>
                </a>

                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>

                @php $availableRoles = $authUser->getAvailableRoles(); @endphp
                @foreach($availableRoles as $r)
                    @if($r !== 'kps')
                    <form action="{{ route('pilih.role.simpan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_aktif" value="{{ $r }}">
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[12px] text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors">
                            <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                            <span>Ganti ke {{ $r === 'dosen_pengampu' ? 'Dosen Pengampu' : 'Manager Proyek' }}</span>
                        </button>
                    </form>
                    @endif
                @endforeach

            {{-- DOSEN sebagai DOSEN PENGAMPU --}}
            @elseif($isDosenAsDosen)
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PBL (Internal)</p>
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path==='dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span><span>Dashboard</span>
                </a>
                <a href="{{ route('penilaian.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'penilaian') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">grade</span><span>Penilaian</span>
                </a>
                <a href="{{ route('supervisi_mingguan.daftar_verifikasi') }}"
                   class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'supervisi-mingguan') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                    <span>Verifikasi Supervisi</span>
                </a>
                @if($aksesRoleDosen === 'keduanya')
                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>
                <form action="{{ route('pilih.role.simpan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role_aktif" value="manager_proyek">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[12px] text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors">
                        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                        <span>Ganti ke Manager Proyek</span>
                    </button>
                </form>
                @endif

            {{-- MANAGER PROYEK permanen --}}
            @elseif($authUser->isManager())
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PBL (Internal)</p>
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path==='dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span><span>Dashboard</span>
                </a>
                <a href="{{ route('penilaian.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'penilaian') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">grade</span><span>Penilaian</span>
                </a>
                <a href="{{ route('pengajuan_proyek.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'pengajuan') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">rocket_launch</span><span>Pengajuan Proyek</span>
                </a>
                <a href="{{ route('logbook_mingguan.daftar_verifikasi') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path,'logbook-mingguan') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">rate_review</span><span>Verifikasi Logbook</span>
                </a>

            {{-- MENTOR INDUSTRI (modul External) --}}
            @elseif($authUser->role === 'mentor')
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Menu Mentor</p>
                @include('layouts.partials.external-menu')

            {{-- MAHASISWA --}}
            @else
                @php
                    $lbInternalAktif  = str_starts_with($path, 'logbook');
                    $lbEksternalAktif = request()->routeIs('external.mahasiswa.dashboard');
                    $nInternalAktif   = str_starts_with($path, 'nilai-saya');
                    $nEksternalAktif  = request()->routeIs('external.mahasiswa.nilai*');

                    $mhsGrup = [
                        ['Logbook Magang', 'menu_book', $lbInternalAktif || $lbEksternalAktif, [
                            [route('logbook_harian.index'),          'Magang Dalam Kampus',           'school',    $lbInternalAktif],
                            [route('external.mahasiswa.dashboard'),  'Magang Luar Kampus (Industri)', 'apartment', $lbEksternalAktif],
                        ]],
                        ['Nilai & Transkrip', 'workspace_premium', $nInternalAktif || $nEksternalAktif, [
                            [route('nilai.index'),                   'Magang Dalam Kampus',           'school',    $nInternalAktif],
                            [route('external.mahasiswa.nilai'),      'Magang Luar Kampus (Industri)', 'apartment', $nEksternalAktif],
                        ]],
                    ];
                @endphp
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">Menu PPI</p>

                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ $path === 'dashboard' ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span><span>Dashboard</span>
                </a>

                @foreach ($mhsGrup as [$grpLabel, $grpIcon, $grpAktif, $grpItems])
                    <div x-data="{ open: {{ $grpAktif ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-[13px] transition-colors {{ $grpAktif ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]' : $navIdle }}">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px]">{{ $grpIcon }}</span>
                                <span>{{ $grpLabel }}</span>
                            </div>
                            <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150" :class="open ? 'rotate-90' : ''">chevron_right</span>
                        </button>
                        <div x-show="open" x-cloak class="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                            @foreach ($grpItems as [$itUrl, $itLabel, $itIcon, $itAktif])
                                <a href="{{ $itUrl }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-md text-[12px] transition-colors {{ $itAktif ? $subActive : $subIdle }}">
                                    <span class="material-symbols-outlined text-[15px]">{{ $itIcon }}</span>
                                    <span>{{ $itLabel }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <a href="{{ route('supervisi_harian.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path, 'supervisi') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span><span>Supervisi</span>
                </a>
                <a href="{{ route('feedback.index') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path, 'feedback') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">forum</span><span>Feedback Hub</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-colors {{ str_starts_with($path, 'profile') ? $navActive : $navIdle }}">
                    <span class="material-symbols-outlined text-[18px]">settings</span><span>Pengaturan Akun</span>
                </a>
            @endif

            {{-- ================= MODUL EXTERNAL (Admin & Dosen) ================= --}}
            @if(in_array($authUser->role, ['admin', 'dosen']))
                <div class="h-px bg-[#e4e4e7] my-3.5 mx-2"></div>
                <p class="px-3 font-mono text-[10px] uppercase tracking-[0.08em] text-[#6a6f7c] font-medium mb-1.5">PPI Industri (Eksternal)</p>
                @include('layouts.partials.external-menu')
            @endif

        </nav>

        {{-- User Footer --}}
        <div class="p-3 border-t border-[#e4e4e7] bg-[#fafafa]">
            <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg bg-white border border-[#e4e4e7]">
                <div class="w-8 h-8 rounded-md bg-[#013981] flex items-center justify-center font-mono font-medium text-white text-xs flex-shrink-0">
                    {{ strtoupper(substr($authUser->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-[#0b0b0c] truncate leading-tight">{{ $authUser->name }}</p>
                    <p class="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider truncate mt-0.5">{{ $roleLabel }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-md text-[#6a6f7c] hover:text-[#b91c1c] hover:bg-red-50 transition-colors" title="Logout">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ================= MAIN AREA ================= --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

        {{-- TOPBAR --}}
        <header class="bg-white/90 backdrop-blur-md border-b border-[#e4e4e7] px-5 lg:px-8 h-14 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" @click="mobileOpen = true"
                        class="lg:hidden p-1.5 -ml-1.5 rounded-lg text-[#52525b] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">menu</span>
                </button>
                <div class="flex items-center gap-2 text-[13px] min-w-0">
                    <span class="font-mono text-[11px] text-[#6a6f7c] hidden sm:inline">AE Polman Bandung</span>
                    <span class="text-[#d4d4d8] hidden sm:inline">/</span>
                    <span class="font-medium text-[#0b0b0c] truncate">@yield('title', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                @if(session('success'))
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-[#15803d] rounded-lg text-xs font-medium">
                    <span class="material-symbols-outlined text-[15px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                @if(session('error'))
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 text-[#b91c1c] rounded-lg text-xs font-medium">
                    <span class="material-symbols-outlined text-[15px]">error</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <div class="relative hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#a1a1aa] text-[16px]">search</span>
                    <input type="text" placeholder="Cari mahasiswa, proyek..."
                           class="pl-8 pr-3.5 py-1.5 bg-[#fafafa] border border-[#e4e4e7] rounded-lg focus:border-[#013981] focus:bg-white outline-none text-xs w-56 text-[#0b0b0c] placeholder:text-[#a1a1aa] transition-colors">
                </div>

                @if($authUser->role === 'dosen')
                    @include('external.partials.notifikasi_dosen')
                @elseif($authUser->role === 'mentor')
                    @include('external.partials.notifikasi_mentor')
                @else
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c] relative">
                        <span class="material-symbols-outlined text-[18px]">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-[#15803d] rounded-full"></span>
                    </button>
                @endif

                <a href="{{ route('profile.edit') }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c]"
                   title="Pengaturan Akun">
                    <span class="material-symbols-outlined text-[18px]">settings</span>
                </a>

                <div class="w-px h-5 bg-[#e4e4e7] mx-0.5 hidden sm:block"></div>

                <div class="hidden sm:flex items-center gap-2.5">
                    <div class="text-right">
                        <p class="text-xs font-medium text-[#0b0b0c] leading-tight">{{ $authUser->name }}</p>
                        <p class="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider">{{ $roleLabel }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-[#013981] flex items-center justify-center text-white font-mono font-medium text-xs flex-shrink-0">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-[#fafafa]">
            @if(session('success'))
            <div class="md:hidden mb-4 flex items-center gap-2 px-3.5 py-2.5 bg-emerald-50 border border-emerald-200 text-[#15803d] rounded-lg text-xs font-medium">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="md:hidden mb-4 flex items-center gap-2 px-3.5 py-2.5 bg-red-50 border border-red-200 text-[#b91c1c] rounded-lg text-xs font-medium">
                <span class="material-symbols-outlined text-[16px]">error</span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const sidebar = document.querySelector('aside nav');
        if (sidebar) {
            const savedScroll = sessionStorage.getItem('sidebarScroll');
            if (savedScroll) sidebar.scrollTop = parseInt(savedScroll);
            sidebar.addEventListener('scroll', () => {
                sessionStorage.setItem('sidebarScroll', sidebar.scrollTop);
            });
        }
    </script>
</body>
</html>
