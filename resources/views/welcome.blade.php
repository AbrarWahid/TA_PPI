<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBL Portal — AE Polman Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        aqua: {
                            50:  '#f0fffe',
                            100: '#ccfff7',
                            200: '#7fffd4',
                            300: '#40e0d0',
                            400: '#00c4b4',
                            500: '#00a896',
                            600: '#008080',
                            700: '#006666',
                            800: '#004d4d',
                            900: '#003333',
                        }
                    },
                    keyframes: {
                        float: {
                            '0%,100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-14px)' },
                        },
                    },
                    animation: {
                        float: 'float 7s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        ::selection { background: #7fffd4; color: #004d4d; }

        .dot-grid {
            background-image: radial-gradient(#008080 1px, transparent 0);
            background-size: 28px 28px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #008080 0%, #40e0d0 50%, #7fffd4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        html { scroll-behavior: smooth; }

        #navbar {
            transition: background 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        #navbar.scrolled {
            background: rgba(255,255,255,0.97) !important;
            box-shadow: 0 1px 24px 0 rgba(0,128,128,0.10);
            border-bottom-color: rgba(127,255,212,0.4) !important;
        }

        .feature-card:hover {
            box-shadow: 0 8px 40px -8px rgba(0,128,128,0.18);
        }

        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 0; bottom: -2px;
            width: 0; height: 2px;
            background: #008080;
            border-radius: 9999px;
            transition: width 0.25s ease;
        }
        .nav-link:hover::after { width: 100%; }

        .blob {
            filter: blur(90px);
            border-radius: 9999px;
            position: absolute;
            pointer-events: none;
        }

        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.8; }
            80%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .pulse-dot { position: relative; }
        .pulse-dot::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #008080;
            animation: pulse-ring 2.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-white font-sans text-aqua-900 antialiased">

{{-- ============================================================
     NAVBAR
============================================================ --}}
<nav id="navbar" class="fixed top-0 w-full z-50 bg-white/85 backdrop-blur-xl border-b border-aqua-200/30">
    <div class="max-w-7xl mx-auto px-6 h-[68px] flex items-center justify-between">

        <a href="#" class="flex items-center gap-3 group">
            <div class="h-12 w-12 rounded-xl bg-aqua-800 flex items-center justify-center shadow-sm group-hover:bg-aqua-700 transition-colors overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="AE Logo" class="h-9 w-9 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span class="text-aqua-200 font-black text-sm hidden items-center justify-center w-full h-full">AE</span>
            </div>
            <div class="leading-tight">
                <span class="font-black text-aqua-800 text-base tracking-tight">PBL Portal</span>
                <span class="block text-[10px] text-aqua-600 font-semibold tracking-widest uppercase">AE Polman Bandung</span>
            </div>
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#fitur" class="nav-link text-sm font-semibold text-aqua-700/70 hover:text-aqua-800 transition-colors">Fitur</a>
            <a href="#alur"  class="nav-link text-sm font-semibold text-aqua-700/70 hover:text-aqua-800 transition-colors">Alur</a>
            <a href="#role"  class="nav-link text-sm font-semibold text-aqua-700/70 hover:text-aqua-800 transition-colors">Pengguna</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('register') }}"
               class="hidden sm:inline-flex items-center gap-1.5 text-aqua-700 font-bold text-sm px-4 py-2 rounded-xl hover:bg-aqua-100/60 transition-colors">
                Daftar
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-aqua-200 text-aqua-800 px-5 py-2.5 rounded-xl font-black text-sm hover:bg-aqua-300 transition-colors shadow-sm shadow-aqua-300/40 active:scale-95 border border-aqua-300/50">
                <span class="material-symbols-outlined" style="font-size:16px;">login</span>
                Masuk
            </a>
        </div>
    </div>
</nav>


{{-- ============================================================
     HERO
============================================================ --}}
<section id="hero" class="relative pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden min-h-[92vh] flex items-center">

    <div class="blob w-[60%] h-[70%] bg-aqua-200/35 -top-[10%] -right-[5%]"></div>
    <div class="blob w-[45%] h-[55%] bg-aqua-300/15 top-[30%] -left-[10%]"></div>
    <div class="blob w-[35%] h-[40%] bg-aqua-200/15 bottom-0 right-[20%]"></div>
    <div class="absolute inset-0 dot-grid opacity-[0.04] pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center w-full">

        {{-- LEFT --}}
        <div class="space-y-9">

            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-aqua-200/20 border border-aqua-200/60">
                <span class="pulse-dot w-2 h-2 rounded-full bg-aqua-600 shrink-0"></span>
                <span class="text-aqua-700 font-bold text-[11px] tracking-[0.25em] uppercase font-mono">
                    Sistem Aktif — AE Polman Bandung
                </span>
            </div>

            <div class="space-y-1">
                <h1 class="text-6xl lg:text-7xl font-black text-aqua-900 leading-[0.88] tracking-tighter">
                    Kelola PBL
                </h1>
                <h1 class="text-6xl lg:text-7xl font-black leading-[0.88] tracking-tighter gradient-text">
                    Lebih Cerdas.
                </h1>
            </div>

            <p class="text-aqua-800/60 text-xl max-w-md leading-relaxed font-medium italic border-l-4 border-aqua-300 pl-5">
                Platform manajemen Project-Based Learning terintegrasi untuk seluruh sivitas akademika Jurusan Automation Engineering.
            </p>

            <div class="flex items-center gap-7 pt-1">
                <div class="text-center">
                    <p class="text-3xl font-black text-aqua-600 counter" data-target="4">0</p>
                    <p class="text-[10px] text-aqua-700/50 font-bold uppercase tracking-widest mt-0.5">Role</p>
                </div>
                <div class="w-px h-8 bg-aqua-200/60"></div>
                <div class="text-center">
                    <p class="text-3xl font-black text-aqua-600 counter" data-target="3">0</p>
                    <p class="text-[10px] text-aqua-700/50 font-bold uppercase tracking-widest mt-0.5">Prodi</p>
                </div>
                <div class="w-px h-8 bg-aqua-200/60"></div>
                <div class="text-center">
                    <p class="text-3xl font-black text-aqua-600 counter" data-target="6">0</p>
                    <p class="text-[10px] text-aqua-700/50 font-bold uppercase tracking-widest mt-0.5">Fitur</p>
                </div>
                <div class="w-px h-8 bg-aqua-200/60"></div>
                <div class="text-center">
                    <p class="text-3xl font-black text-aqua-600">100%</p>
                    <p class="text-[10px] text-aqua-700/50 font-bold uppercase tracking-widest mt-0.5">Terintegrasi</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-1">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-aqua-200 text-aqua-900 px-9 py-4 rounded-2xl font-black text-sm shadow-2xl shadow-aqua-300/50 hover:bg-aqua-300 hover:scale-[1.02] transition-all active:scale-95 border border-aqua-300/60">
                    <span class="material-symbols-outlined" style="font-size:16px;">rocket_launch</span>
                    Masuk Sekarang
                </a>
                <a href="#fitur"
                   class="inline-flex items-center gap-2 text-aqua-700 border border-aqua-200/70 px-8 py-4 rounded-2xl font-bold text-sm hover:bg-aqua-100/50 transition-colors">
                    <span class="material-symbols-outlined" style="font-size:16px;">info</span>
                    Pelajari Fitur
                </a>
            </div>
        </div>

        {{-- RIGHT: CARD --}}
        <div class="relative hidden lg:block">
            <div class="absolute inset-4 bg-aqua-200/25 rounded-full blur-3xl"></div>

            <div class="relative animate-float">
                <div class="bg-white/30 backdrop-blur-2xl rounded-[3rem] border border-white/50 p-2 shadow-2xl shadow-aqua-300/30">
                    <div class="bg-aqua-800 rounded-[2.6rem] p-9 overflow-hidden relative">
                        <div class="absolute inset-0 dot-grid opacity-[0.07]"></div>

                        <div class="relative z-10 space-y-7">

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 rounded-xl bg-aqua-200/15 border border-aqua-200/25 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <span class="font-black text-aqua-200 text-base hidden">AE</span>
                                    </div>
                                    <div>
                                        <p class="font-black text-white text-sm">Integrated Assessment</p>
                                        <p class="text-aqua-200/50 text-xs">PBL System v2.0</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-aqua-300/60 tracking-widest bg-aqua-200/10 px-2 py-1 rounded-lg border border-aqua-200/15 uppercase">LIVE</span>
                            </div>

                            <div>
                                <p class="text-aqua-200/50 text-xs font-bold uppercase tracking-widest mb-3">Bobot Penilaian</p>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-white/70 text-xs font-semibold">Manager Proyek</span>
                                            <span class="text-aqua-200 text-xs font-black">55%</span>
                                        </div>
                                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-aqua-200 rounded-full" style="width:55%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-white/70 text-xs font-semibold">Dosen Pengampu</span>
                                            <span class="text-aqua-200 text-xs font-black">45%</span>
                                        </div>
                                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-aqua-300/70 rounded-full" style="width:45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-5 gap-2">
                                @foreach([['A','≥85',true],['B','≥75',false],['C','≥65',false],['D','≥55',false],['E','<55',false]] as $g)
                                <div class="rounded-xl {{ $g[2] ? 'bg-aqua-200/20 text-aqua-200 border border-aqua-200/30' : 'bg-white/8 text-white/40 border border-white/10' }} p-2.5 text-center">
                                    <p class="font-black text-sm">{{ $g[0] }}</p>
                                    <p class="text-[9px] opacity-70 font-bold">{{ $g[1] }}</p>
                                </div>
                                @endforeach
                            </div>

                            <div class="space-y-2.5 pt-1">
                                @foreach(['Logbook Harian & Mingguan','Upload & Verifikasi Laporan','Export PDF & CSV','Monitoring Real-time'] as $f)
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded-full bg-aqua-200/20 flex items-center justify-center shrink-0 border border-aqua-200/30">
                                        <span class="material-symbols-outlined text-aqua-200" style="font-size:10px;">check</span>
                                    </div>
                                    <span class="text-white/60 text-xs font-medium">{{ $f }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute -bottom-4 -left-8 bg-white rounded-2xl border border-aqua-200/40 p-3.5 shadow-xl shadow-aqua-900/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-aqua-200/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-aqua-600" style="font-size:16px;">check_circle</span>
                    </div>
                    <div>
                        <p class="text-aqua-900 font-black text-xs">Logbook Diverifikasi</p>
                        <p class="text-aqua-600/50 text-[10px]">Minggu ke-8 · 2 menit lalu</p>
                    </div>
                </div>
            </div>

            <div class="absolute -top-4 -right-4 bg-white rounded-2xl border border-aqua-200/40 p-3.5 shadow-xl shadow-aqua-900/10">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-aqua-600" style="font-size:18px;">trending_up</span>
                    <div>
                        <p class="text-aqua-900 font-black text-xs">Nilai A</p>
                        <p class="text-aqua-600/50 text-[10px]">Rekap terbaru</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-35">
        <span class="text-[10px] text-aqua-700 font-bold uppercase tracking-widest">Scroll</span>
        <div class="w-px h-8 bg-gradient-to-b from-aqua-500 to-transparent"></div>
    </div>
</section>


{{-- ============================================================
     FEATURES
============================================================ --}}
<section id="fitur" class="py-24 bg-gradient-to-b from-white via-aqua-50/40 to-white">
    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <span class="inline-block text-[11px] font-bold tracking-[0.35em] uppercase text-aqua-600 bg-aqua-200/20 border border-aqua-200/50 px-4 py-1.5 rounded-full">
                Fitur Sistem
            </span>
            <h2 class="text-4xl font-black text-aqua-900 mt-4 tracking-tight">Semua yang kamu butuhkan</h2>
            <p class="text-aqua-700/50 mt-2 text-lg">dalam satu platform terintegrasi.</p>
        </div>

        @php
        $features = [
            ['icon' => 'menu_book',       'title' => 'Logbook Harian & Mingguan',   'desc' => 'Catat aktivitas proyek setiap hari. Generate rekap mingguan sebagai PDF dan kirim untuk diverifikasi Manager.', 'tag' => 'Mahasiswa'],
            ['icon' => 'grade',           'title' => 'Penilaian Berbasis Rubrik',   'desc' => 'Sistem penilaian terstruktur dengan radio button per kriteria. Bobot Manager 55% dan Dosen 45% dihitung otomatis.', 'tag' => 'Dosen & Manager'],
            ['icon' => 'upload_file',     'title' => 'Upload & Verifikasi Laporan', 'desc' => 'Mahasiswa upload laporan MK. Sistem otomatis mendeteksi dosen supervisi yang bersangkutan untuk verifikasi.', 'tag' => 'Mahasiswa & Dosen'],
            ['icon' => 'analytics',       'title' => 'Monitoring & Rekap Nilai',    'desc' => 'Admin dapat memantau progres seluruh mahasiswa dan mengekspor rekap nilai ke CSV maupun PDF.', 'tag' => 'Admin'],
            ['icon' => 'manage_accounts', 'title' => 'Dual Role Dosen',             'desc' => 'Dosen dapat beralih mode antara Dosen Pengampu dan Manager Proyek dalam satu akun tanpa perlu login ulang.', 'tag' => 'Dosen'],
            ['icon' => 'approval',        'title' => 'Manajemen Proyek & Akun',     'desc' => 'Admin mengelola pengajuan proyek, approval akun mahasiswa, serta penugasan supervisi mata kuliah.', 'tag' => 'Admin'],
        ];
        @endphp

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($features as $f)
            <div class="feature-card group bg-white rounded-3xl p-7 border border-aqua-200/40 hover:-translate-y-1.5 transition-all duration-300">
                <div class="flex items-start justify-between mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-aqua-200/25 border border-aqua-200/50 flex items-center justify-center group-hover:bg-aqua-200/50 transition-colors">
                        <span class="material-symbols-outlined text-aqua-600" style="font-size:20px;">{{ $f['icon'] }}</span>
                    </div>
                    <span class="text-[10px] font-bold text-aqua-600 bg-aqua-200/20 border border-aqua-200/50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                        {{ $f['tag'] }}
                    </span>
                </div>
                <h3 class="font-black text-aqua-900 text-base mb-2 leading-snug">{{ $f['title'] }}</h3>
                <p class="text-aqua-700/55 text-sm leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     ALUR
============================================================ --}}
<section id="alur" class="py-24 bg-aqua-800 relative overflow-hidden">

    <div class="absolute inset-0 dot-grid opacity-[0.07] pointer-events-none"></div>
    <div class="blob w-[40%] h-[80%] bg-aqua-300/10 top-0 -right-[10%]"></div>

    <div class="relative max-w-7xl mx-auto px-6 z-10">

        <div class="text-center mb-16">
            <span class="inline-block text-[11px] font-bold tracking-[0.35em] uppercase text-aqua-200/60 bg-aqua-200/8 border border-aqua-200/15 px-4 py-1.5 rounded-full">
                Alur Kerja
            </span>
            <h2 class="text-4xl font-black text-white mt-4 tracking-tight">Bagaimana Cara Kerjanya?</h2>
            <p class="text-aqua-200/50 mt-2 text-lg">4 langkah mudah dari pengajuan hingga penilaian.</p>
        </div>

        @php
        $steps = [
            ['num' => '01', 'icon' => 'assignment_add',    'title' => 'Pengajuan Proyek',     'desc' => 'Manager Proyek atau Dosen mengajukan proyek PBL. Admin mereview dan memberikan approval serta menugaskan mahasiswa.', 'actor' => 'Manager / Admin'],
            ['num' => '02', 'icon' => 'edit_note',         'title' => 'Input Logbook',         'desc' => 'Mahasiswa mencatat aktivitas harian dan merekap menjadi logbook mingguan yang siap diverifikasi.', 'actor' => 'Mahasiswa'],
            ['num' => '03', 'icon' => 'fact_check',        'title' => 'Verifikasi & Supervisi','desc' => 'Manager memverifikasi logbook mingguan. Dosen Pengampu mereview laporan MK yang di-upload mahasiswa.', 'actor' => 'Manager & Dosen'],
            ['num' => '04', 'icon' => 'workspace_premium', 'title' => 'Penilaian & Rekap',    'desc' => 'Nilai akhir dihitung otomatis dari rubrik Manager (55%) dan Dosen (45%). Admin export rekap ke CSV atau PDF.', 'actor' => 'Semua Role'],
        ];
        @endphp

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 relative">

            @foreach($steps as $s)
            <div class="group">
                <div class="bg-white/5 hover:bg-white/10 border border-white/10 hover:border-aqua-200/30 rounded-3xl p-6 transition-all duration-300 h-full">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="relative w-14 h-14 rounded-2xl bg-aqua-200/12 border border-aqua-200/20 flex items-center justify-center shrink-0 group-hover:bg-aqua-200/20 transition-colors">
                            <span class="material-symbols-outlined text-aqua-200" style="font-size:22px;">{{ $s['icon'] }}</span>
                            <span class="absolute -top-1.5 -right-1.5 text-[10px] font-black text-aqua-300 bg-aqua-800 border border-aqua-600 rounded-lg px-1.5 py-0.5">{{ $s['num'] }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-aqua-300/60 uppercase tracking-widest leading-tight">{{ $s['actor'] }}</span>
                    </div>
                    <h3 class="font-black text-white text-base mb-2">{{ $s['title'] }}</h3>
                    <p class="text-aqua-200/50 text-sm leading-relaxed">{{ $s['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     ROLE
============================================================ --}}
<section id="role" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <span class="inline-block text-[11px] font-bold tracking-[0.35em] uppercase text-aqua-600 bg-aqua-200/20 border border-aqua-200/50 px-4 py-1.5 rounded-full">
                Pengguna Sistem
            </span>
            <h2 class="text-4xl font-black text-aqua-900 mt-4 tracking-tight">4 Role Terintegrasi</h2>
            <p class="text-aqua-700/50 mt-2 text-lg">Setiap aktor punya peran dan akses yang terdefinisi jelas.</p>
        </div>

        @php
        $roles = [
            ['icon' => 'admin_panel_settings', 'role' => 'Admin',          'sub' => 'KPS / Kaprodi',                    'desc' => 'Kelola seluruh data master sistem, approve pengajuan proyek dan akun baru, assign mahasiswa ke proyek, serta pantau rekap nilai seluruh mahasiswa.',     'perms' => ['Master Data','Approve Proyek','Assign Mahasiswa','Export Rekap'], 'dark' => true],
            ['icon' => 'manage_accounts',       'role' => 'Manager Proyek', 'sub' => 'Dosen Pembimbing / Pihak Industri', 'desc' => 'Mengajukan proyek PBL, memverifikasi logbook mingguan mahasiswa (55% bobot penilaian), dan memberikan penilaian berbasis rubrik.',                       'perms' => ['Ajukan Proyek','Verifikasi Logbook','Penilaian 55%','Feedback'],  'dark' => false],
            ['icon' => 'school',                'role' => 'Dosen Pengampu', 'sub' => 'Dosen Mata Kuliah',                'desc' => 'Mensupervisi mahasiswa per mata kuliah, memverifikasi laporan yang di-upload, dan memberikan penilaian (45% bobot). Bisa beralih ke mode Manager Proyek.', 'perms' => ['Supervisi MK','Verifikasi Laporan','Penilaian 45%','Dual Role'],  'dark' => false],
            ['icon' => 'person',                'role' => 'Mahasiswa',      'sub' => 'Peserta PBL',                      'desc' => 'Input logbook harian, generate rekap logbook mingguan (PDF), upload laporan MK, serta melihat nilai akhir dan feedback dari pembimbing.',               'perms' => ['Logbook Harian','Rekap PDF','Upload Laporan','Lihat Nilai'],       'dark' => false],
        ];
        @endphp

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($roles as $r)
            <div class="rounded-3xl {{ $r['dark'] ? 'bg-aqua-800 border-aqua-600/40' : 'bg-aqua-50/60 border-aqua-200/40' }} border p-6 flex flex-col gap-5 hover:-translate-y-1.5 transition-transform duration-300 feature-card">
                <div class="w-12 h-12 rounded-2xl {{ $r['dark'] ? 'bg-aqua-200/15 border border-aqua-200/25' : 'bg-white border border-aqua-200/60' }} flex items-center justify-center">
                    <span class="material-symbols-outlined {{ $r['dark'] ? 'text-aqua-200' : 'text-aqua-600' }}" style="font-size:22px;">{{ $r['icon'] }}</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold {{ $r['dark'] ? 'text-aqua-300/60' : 'text-aqua-500' }} uppercase tracking-widest mb-1">{{ $r['sub'] }}</p>
                    <h3 class="font-black {{ $r['dark'] ? 'text-white' : 'text-aqua-900' }} text-lg">{{ $r['role'] }}</h3>
                </div>
                <p class="{{ $r['dark'] ? 'text-aqua-200/55' : 'text-aqua-700/60' }} text-sm leading-relaxed flex-1">{{ $r['desc'] }}</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($r['perms'] as $p)
                    <span class="{{ $r['dark'] ? 'bg-aqua-200/12 text-aqua-200 border-aqua-200/20' : 'bg-white text-aqua-600 border-aqua-200/60' }} text-[10px] font-bold border px-2.5 py-1 rounded-full uppercase tracking-wider">
                        {{ $p }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     QUOTE STRIP
============================================================ --}}
<section class="py-16 bg-aqua-200/15 border-y border-aqua-200/40">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="material-symbols-outlined text-aqua-300 mb-4 block" style="font-size:32px;">format_quote</span>
        <blockquote class="text-2xl font-black text-aqua-900 tracking-tight leading-snug mb-4">
            "Streamlining industrial excellence through smart project-based evaluation."
        </blockquote>
        <p class="text-aqua-600/60 text-sm font-bold uppercase tracking-widest">
            Jurusan Automation Engineering · Politeknik Manufaktur Bandung
        </p>
    </div>
</section>


{{-- ============================================================
     CTA
============================================================ --}}
<section class="py-24 bg-white">
    <div class="max-w-2xl mx-auto px-6">
        <div class="bg-aqua-800 rounded-[2.5rem] p-12 text-center relative overflow-hidden">
            <div class="absolute inset-0 dot-grid opacity-[0.07] pointer-events-none"></div>
            <div class="blob w-[60%] h-[80%] bg-aqua-300/10 -top-[20%] -right-[20%]"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-3xl bg-aqua-200/15 border border-aqua-200/25 flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-aqua-200" style="font-size:28px;">rocket_launch</span>
                </div>
                <h2 class="text-4xl font-black text-white tracking-tight mb-3">Siap memulai?</h2>
                <p class="text-aqua-200/55 mb-8 text-base max-w-sm mx-auto leading-relaxed">
                    Masuk ke sistem dan mulai kelola proyek PBL kamu sekarang juga.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 bg-aqua-200 text-aqua-900 px-8 py-4 rounded-2xl font-black text-sm hover:bg-aqua-300 transition-colors active:scale-95 shadow-xl shadow-aqua-900/30">
                        <span class="material-symbols-outlined" style="font-size:16px;">login</span>
                        Masuk ke Sistem
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white/10 text-aqua-100 border border-aqua-200/20 px-8 py-4 rounded-2xl font-bold text-sm hover:bg-white/15 transition-colors active:scale-95">
                        <span class="material-symbols-outlined" style="font-size:16px;">person_add</span>
                        Daftar Akun Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ============================================================
     FOOTER
============================================================ --}}
<footer class="border-t border-aqua-200/30 bg-white">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-aqua-800 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-aqua-200 font-black text-xs hidden">AE</span>
                </div>
                <div>
                    <p class="font-black text-aqua-900 text-sm">AE Polman Bandung</p>
                    <p class="text-aqua-600/50 text-[10px] font-semibold">PBL Management System</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="#fitur" class="text-xs text-aqua-600/60 hover:text-aqua-700 font-semibold transition-colors">Fitur</a>
                <a href="#alur"  class="text-xs text-aqua-600/60 hover:text-aqua-700 font-semibold transition-colors">Alur</a>
                <a href="#role"  class="text-xs text-aqua-600/60 hover:text-aqua-700 font-semibold transition-colors">Pengguna</a>
                <a href="{{ route('login') }}" class="text-xs text-aqua-600/60 hover:text-aqua-700 font-semibold transition-colors">Masuk</a>
            </div>
            <p class="text-[11px] text-aqua-500/40 font-semibold uppercase tracking-widest">
                © {{ date('Y') }} · AE Polman Bandung
            </p>
        </div>
    </div>
</footer>


{{-- ============================================================
     JAVASCRIPT
============================================================ --}}
<script>
(function () {
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });

    const counters = document.querySelectorAll('.counter[data-target]');
    const countObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.dataset.target, 10);
            let current = 0;
            const step = Math.ceil(target / 30);
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current;
                if (current >= target) clearInterval(timer);
            }, 40);
            countObserver.unobserve(el);
        });
    }, { threshold: 0.6 });
    counters.forEach(c => countObserver.observe(c));

    const fadeEls = document.querySelectorAll('[data-animate="fadeUp"]');
    if ('IntersectionObserver' in window && fadeEls.length) {
        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });
        fadeEls.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(24px)';
            fadeObserver.observe(el);
        });
    }

    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(sec => {
            if (window.scrollY >= sec.offsetTop - 100) current = sec.id;
        });
        navLinks.forEach(link => {
            link.style.color = (link.getAttribute('href') === '#' + current) ? '#004d4d' : '';
        });
    }, { passive: true });
})();
</script>

</body>
</html>