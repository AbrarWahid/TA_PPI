<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBL &amp; PPI Portal — Politeknik Manufaktur Bandung</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.design-system')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50%      { transform: translateY(-10px); }
        }
        .animate-float-slow {
            animation: floatSlow 6s ease-in-out infinite;
        }
        @keyframes heroReveal {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .h-anim-1 { animation: heroReveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.05s both; }
        .h-anim-2 { animation: heroReveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both; }
        .h-anim-3 { animation: heroReveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.25s both; }
        .h-anim-4 { animation: heroReveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.35s both; }
        .h-anim-5 { animation: heroReveal 0.75s cubic-bezier(0.22, 1, 0.36, 1) 0.25s both; }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-white font-sans text-[#0b0b0c] antialiased selection:bg-[#013981] selection:text-white">

{{-- ============================================================
     STICKY NAVBAR (Polman Logos + Center Links + Sliding Pill Button)
============================================================ --}}
<header class="sticky top-0 z-50 w-full bg-slate-50/85 backdrop-blur-md border-b border-slate-200/70">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 max-w-7xl mx-auto">

        {{-- Left: Polman & BLU Speed Logos --}}
        <a href="{{ url('/') }}" class="shrink-0 flex items-center gap-3.5">
            <img src="{{ asset('images/logo-polman-teks.webp') }}" alt="Logo Polman" class="h-8 md:h-10 w-auto object-contain">
            <img src="{{ asset('images/logo-blu-speed.png') }}" alt="Logo BLU Speed" class="h-9 md:h-11 w-auto object-contain">
        </a>

        {{-- Center: Navigation Links --}}
        <nav class="hidden md:flex items-center gap-7">
            <a href="#prodi"      class="text-sm font-semibold text-slate-600 hover:text-[#013981] transition-colors">Program Studi</a>
            <a href="#fitur"      class="text-sm font-semibold text-slate-600 hover:text-[#013981] transition-colors">Kapabilitas Sistem</a>
            <a href="#keunggulan" class="text-sm font-semibold text-slate-600 hover:text-[#013981] transition-colors">Standar Mutu</a>
            <a href="#faq"        class="text-sm font-semibold text-slate-600 hover:text-[#013981] transition-colors">Informasi &amp; FAQ</a>
        </nav>

        {{-- Right: Animated Sliding Pill CTA --}}
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="group relative flex items-center h-10 rounded-full bg-[#013981] hover:bg-[#012b63] text-white text-sm font-medium overflow-hidden cursor-pointer shadow-md hover:shadow-lg transition-all">
                    <div class="absolute right-1 group-hover:right-[calc(100%-36px)] flex items-center justify-center w-8 h-8 rounded-full bg-white text-[#013981] transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] z-10">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </div>
                    <span class="whitespace-nowrap font-bold pl-4 pr-11 group-hover:translate-x-8 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] text-white">
                        Dashboard
                    </span>
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="hidden sm:inline-flex items-center h-10 px-4 rounded-full text-sm font-bold text-slate-700 hover:text-[#013981] hover:bg-slate-200/60 transition-colors">
                    Daftar
                </a>
                <a href="{{ route('login') }}"
                   class="group relative flex items-center h-10 rounded-full bg-[#013981] hover:bg-[#012b63] text-white text-sm font-medium overflow-hidden cursor-pointer shadow-md hover:shadow-lg transition-all">
                    <div class="absolute right-1 group-hover:right-[calc(100%-36px)] flex items-center justify-center w-8 h-8 rounded-full bg-white text-[#013981] transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] z-10">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </div>
                    <span class="whitespace-nowrap font-bold pl-4 pr-11 group-hover:translate-x-8 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] text-white">
                        Login
                    </span>
                </a>
            @endauth
        </div>
    </div>
</header>

<main class="flex-1 flex flex-col">

    {{-- ============================================================
         SECTION 1: ASYMMETRIC CAMPUS HERO
    ============================================================ --}}
    <section class="relative border-b border-slate-200 py-20 lg:py-28 bg-slate-900 overflow-hidden">
        {{-- Campus Image with Smooth Fade-in & Directional Gradient --}}
        <div id="hero-bg"
             class="absolute inset-0 bg-cover bg-center opacity-0 transition-opacity duration-1000 ease-out"
             style="background-image: url('{{ asset('images/PolmanBandung.png') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/85 to-[#013981]/75 backdrop-blur-[2px] z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="max-w-3xl space-y-7 text-left">
                <div class="h-anim-1 inline-flex items-center gap-2.5 text-xs font-bold text-emerald-400 bg-emerald-400/10 border border-emerald-400/25 px-4 py-1.5 rounded-full shadow-sm tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Jurusan Automation Engineering · Polman Bandung</span>
                </div>

                <h1 class="h-anim-2 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.12] tracking-tight drop-shadow-md">
                    Portal Terpadu <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 to-emerald-300">Project-Based Learning</span> &amp; Praktik Industri
                </h1>

                <p class="h-anim-3 text-lg sm:text-xl text-slate-300 max-w-2xl leading-relaxed font-medium">
                    Kelola logbook aktivitas harian, supervisi mata kuliah, evaluasi mitra perusahaan, hingga pengesahan transkrip ber-QR dalam satu ekosistem akademik.
                </p>

                <div class="h-anim-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center h-14 px-8 text-base font-bold rounded-full bg-[#013981] hover:bg-[#012b63] text-white shadow-lg shadow-[#013981]/40 transition-all hover:scale-105 active:scale-95">
                            <span>Buka Dashboard</span>
                            <span class="material-symbols-outlined ml-2 text-[20px]">arrow_forward</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center h-14 px-8 text-base font-bold rounded-full bg-[#013981] hover:bg-[#012b63] text-white shadow-lg shadow-[#013981]/40 transition-all hover:scale-105 active:scale-95">
                            <span>Masuk ke Portal</span>
                            <span class="material-symbols-outlined ml-2 text-[20px]">arrow_forward</span>
                        </a>
                    @endauth
                    <a href="#fitur"
                       class="inline-flex items-center justify-center h-14 px-8 text-base font-bold rounded-full bg-white/10 text-white border border-white/25 hover:bg-white/20 transition-all backdrop-blur-sm">
                        <span>Jelajahi Fitur</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 2: PROGRAM STUDI JURUSAN AUTOMATION ENGINEERING (#prodi)
         (3 High-Impact Cards for D4 TRIN, D4 TRO, and D4 TRMO)
    ============================================================ --}}
    <section id="prodi" class="relative bg-white py-20 lg:py-28 border-b border-slate-100 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-blue-100/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-6">
            <div class="reveal text-center max-w-2xl mx-auto mb-16 space-y-3">
                <span class="inline-block text-xs font-bold text-[#013981] bg-[#eef4fc] border border-[#d8e6f8] px-4 py-1.5 rounded-full uppercase tracking-wider">
                    Jurusan Automation Engineering
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Tiga Program Studi Sarjana Terapan (D4)
                </h2>
                <p class="text-base sm:text-lg text-slate-600 font-medium">
                    Kurikulum berbasis praktik industri dan proyek rekayasa terapan yang terintegrasi untuk seluruh program studi di Jurusan AE.
                </p>
            </div>

            @php
            $prodiCards = [
                [
                    'code'        => 'D4 — TRIN',
                    'kelas'       => 'Kelas AEC',
                    'title'       => 'Teknologi Rekayasa Informatika Industri',
                    'desc'        => 'Berfokus pada rekayasa perangkat lunak manufaktur, Industrial IoT, sistem eksekusi produksi (MES), akuisisi data pabrik, dan kecerdasan buatan terapan.',
                    'icon'        => 'terminal',
                    'bg'          => 'bg-blue-50',
                    'iconColor'   => 'text-[#013981]',
                    'borderColor' => 'border-blue-200 hover:border-[#013981]',
                    'shadowColor' => 'hover:shadow-blue-500/15',
                    'topics'      => ['Industrial IoT & Cloud', 'Sistem Informasi Manufaktur', 'Cyber-Physical Systems'],
                ],
                [
                    'code'        => 'D4 — TRO',
                    'kelas'       => 'Kelas AEB',
                    'title'       => 'Teknologi Rekayasa Otomasi',
                    'desc'        => 'Berfokus pada perancangan sistem kendali industri, pemrograman PLC & SCADA, instrumentasi proses, jaringan komunikasi industri, dan robotika.',
                    'icon'        => 'settings_suggest',
                    'bg'          => 'bg-emerald-50',
                    'iconColor'   => 'text-emerald-600',
                    'borderColor' => 'border-emerald-200 hover:border-emerald-500',
                    'shadowColor' => 'hover:shadow-emerald-500/15',
                    'topics'      => ['PLC, HMI & SCADA', 'Instrumentasi & Kendali', 'Robotika Industri'],
                ],
                [
                    'code'        => 'D4 — TRMO',
                    'kelas'       => 'Kelas AEA',
                    'title'       => 'Teknologi Rekayasa Mekatronika',
                    'desc'        => 'Berfokus pada sinergi perancangan mekanik presisi, elektronika daya, sistem pneumatik-hidrolik, kendali tertanam (embedded), dan mesin otomatis.',
                    'icon'        => 'precision_manufacturing',
                    'bg'          => 'bg-purple-50',
                    'iconColor'   => 'text-purple-600',
                    'borderColor' => 'border-purple-200 hover:border-purple-500',
                    'shadowColor' => 'hover:shadow-purple-500/15',
                    'topics'      => ['Desain Sistem Mekatronik', 'Embedded & Pneumatik', 'Otomasi Mesin Produksi'],
                ],
            ];
            @endphp

            <div class="reveal grid grid-cols-1 lg:grid-cols-3 gap-8">
                @foreach($prodiCards as $p)
                <div class="group relative overflow-hidden rounded-[2.25rem] border-2 {{ $p['borderColor'] }} bg-white p-8 transition-all duration-300 hover:shadow-2xl {{ $p['shadowColor'] }} hover:-translate-y-2 flex flex-col justify-between">
                    <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full blur-3xl transition-all duration-500 group-hover:scale-150 {{ $p['bg'] }} opacity-70 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-7">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl {{ $p['bg'] }} {{ $p['iconColor'] }} shadow-inner transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                <span class="material-symbols-outlined text-[32px]">{{ $p['icon'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono text-xs font-extrabold {{ $p['iconColor'] }} {{ $p['bg'] }} px-3 py-1 rounded-full block">
                                    {{ $p['code'] }}
                                </span>
                                <span class="font-mono text-[11px] text-slate-400 mt-1 block">{{ $p['kelas'] }}</span>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-3 leading-snug">
                            {{ $p['title'] }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed font-medium mb-6">
                            {{ $p['desc'] }}
                        </p>
                    </div>

                    <div class="relative z-10 pt-5 border-t border-slate-100 space-y-2">
                        @foreach($p['topics'] as $tp)
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                            <span class="material-symbols-outlined text-[15px] {{ $p['iconColor'] }}">check_circle</span>
                            <span>{{ $tp }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 3: 6 KAPABILITAS SISTEM (#fitur)
    ============================================================ --}}
    <section id="fitur" class="relative bg-slate-50 py-20 lg:py-28 border-b border-slate-100 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#013981]/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6">
            <div class="reveal text-center max-w-2xl mx-auto mb-16 space-y-3">
                <span class="inline-block text-xs font-bold text-[#013981] bg-[#eef4fc] border border-[#d8e6f8] px-4 py-1.5 rounded-full uppercase tracking-wider">
                    Kapabilitas Sistem
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Fitur Lengkap untuk Seluruh Kebutuhan Akademik
                </h2>
                <p class="text-base sm:text-lg text-slate-600 font-medium">
                    Dirancang agar proses pencatatan, supervisi, dan penilaian berjalan lebih cepat, akurat, dan terdokumentasi.
                </p>
            </div>

            @php
            $features = [
                [
                    'icon'        => 'menu_book',
                    'title'       => 'Logbook & Jam Kompetensi',
                    'desc'        => 'Catat aktivitas harian lengkap dengan kalkulasi durasi menit/jam otomatis serta unggah multi-foto dokumentasi kegiatan.',
                    'tag'         => 'Mahasiswa',
                    'bg'          => 'bg-blue-50',
                    'iconColor'   => 'text-[#013981]',
                    'borderColor' => 'border-blue-200 hover:border-[#013981]',
                    'shadowColor' => 'hover:shadow-blue-500/15',
                ],
                [
                    'icon'        => 'grade',
                    'title'       => 'Penilaian Berbasis Rubrik',
                    'desc'        => 'Sistem evaluasi objektif dengan indikator keterampilan terukur. Nilai Manager (55%) dan Dosen (45%) dihitung otomatis.',
                    'tag'         => 'Evaluasi',
                    'bg'          => 'bg-emerald-50',
                    'iconColor'   => 'text-emerald-600',
                    'borderColor' => 'border-emerald-200 hover:border-emerald-500',
                    'shadowColor' => 'hover:shadow-emerald-500/15',
                ],
                [
                    'icon'        => 'qr_code_scanner',
                    'title'       => 'Validasi Tanda Tangan QR',
                    'desc'        => 'Pengajuan dan pengesahan tanda tangan digital terenkripsi untuk transkrip nilai dan lembar evaluasi pembimbing.',
                    'tag'         => 'Otentikasi',
                    'bg'          => 'bg-purple-50',
                    'iconColor'   => 'text-purple-600',
                    'borderColor' => 'border-purple-200 hover:border-purple-500',
                    'shadowColor' => 'hover:shadow-purple-500/15',
                ],
                [
                    'icon'        => 'swap_horiz',
                    'title'       => 'Multi-Role Dosen Terpadu',
                    'desc'        => 'Satu akun dosen dapat beralih mode antara Dosen Pengampu, Manager Proyek, dan Koordinator Prodi (KPS) secara instan.',
                    'tag'         => 'Single Sign-On',
                    'bg'          => 'bg-orange-50',
                    'iconColor'   => 'text-orange-600',
                    'borderColor' => 'border-orange-200 hover:border-orange-500',
                    'shadowColor' => 'hover:shadow-orange-500/15',
                ],
                [
                    'icon'        => 'fact_check',
                    'title'       => 'Supervisi & Verifikasi Laporan',
                    'desc'        => 'Mahasiswa mengajukan rekap supervisi mingguan dan laporan mata kuliah untuk diverifikasi langsung oleh dosen pengampu.',
                    'tag'         => 'Akademik',
                    'bg'          => 'bg-cyan-50',
                    'iconColor'   => 'text-cyan-600',
                    'borderColor' => 'border-cyan-200 hover:border-cyan-500',
                    'shadowColor' => 'hover:shadow-cyan-500/15',
                ],
                [
                    'icon'        => 'analytics',
                    'title'       => 'Pusat Kendali KPS & Ekspor',
                    'desc'        => 'Pantau pemenuhan standar jam minimum prodi, kelola parameter kuisioner industri, dan ekspor transkrip ke PDF & CSV.',
                    'tag'         => 'KPS & Admin',
                    'bg'          => 'bg-rose-50',
                    'iconColor'   => 'text-rose-600',
                    'borderColor' => 'border-rose-200 hover:border-rose-500',
                    'shadowColor' => 'hover:shadow-rose-500/15',
                ],
            ];
            @endphp

            <div class="reveal grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($features as $f)
                <div class="group relative overflow-hidden rounded-[2rem] border-2 {{ $f['borderColor'] }} bg-white p-7 transition-all duration-300 hover:shadow-2xl {{ $f['shadowColor'] }} hover:-translate-y-1.5 flex flex-col justify-between">
                    <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full blur-3xl transition-all duration-500 group-hover:scale-150 {{ $f['bg'] }} opacity-60 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl {{ $f['bg'] }} {{ $f['iconColor'] }} shadow-inner transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                <span class="material-symbols-outlined text-[30px]">{{ $f['icon'] }}</span>
                            </div>
                            <span class="text-[11px] font-bold {{ $f['iconColor'] }} {{ $f['bg'] }} px-3 py-1 rounded-full">
                                {{ $f['tag'] }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-2.5">{{ $f['title'] }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">
                            {{ $f['desc'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 4: STANDAR MUTU & TRANSPARANSI EVALUASI (#keunggulan)
         (Split Highlight Section: Quality Assurance & Rubric Transparency)
    ============================================================ --}}
    <section id="keunggulan" class="py-20 lg:py-28 bg-white border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="reveal grid lg:grid-cols-12 gap-12 items-center">

                {{-- Left Column (6 cols): Editorial Explanation & Pillars --}}
                <div class="lg:col-span-6 space-y-6">
                    <span class="inline-block text-xs font-bold text-[#013981] bg-[#eef4fc] border border-[#d8e6f8] px-4 py-1.5 rounded-full uppercase tracking-wider">
                        Standar Mutu Akademik
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Transparansi Penilaian &amp; Keaslian Dokumen Digital
                    </h2>
                    <p class="text-slate-600 text-base leading-relaxed font-medium">
                        Setiap catatan kegiatan harian mahasiswa diverifikasi secara berlapis dan dikonversi otomatis ke dalam komponen nilai akhir tanpa rekapitulasi manual yang rawan kesalahan.
                    </p>

                    <div class="space-y-4 pt-2">
                        @foreach([
                            ['verified_user', 'Verifikasi Berlapis & Real-Time', 'Catatan aktivitas dan dokumentasi foto diperiksa keasliannya serta divalidasi langsung oleh pembimbing terkait.'],
                            ['calculate',     'Kalkulasi Nilai Akhir Otomatis', 'Sistem menggabungkan nilai keterampilan lapangan, disiplin kerja, supervisi, dan sidang presentasi secara presisi.'],
                            ['qr_code_2',     'Dokumen Transkrip Ber-QR Resmi', 'Setiap lembar pengesahan memiliki token verifikasi QR unik yang dapat dipindai untuk memastikan keabsahan nilai.'],
                        ] as [$ic, $ttl, $dsc])
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="w-11 h-11 rounded-xl bg-[#eef4fc] text-[#013981] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">{{ $ic }}</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">{{ $ttl }}</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-relaxed">{{ $dsc }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column (6 cols): Visual Evaluation & Competency Dashboard Card --}}
                <div class="lg:col-span-6">
                    <div class="rounded-[2.25rem] bg-slate-50 border-2 border-slate-200 p-7 sm:p-9 space-y-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                            <div>
                                <p class="font-mono text-[11px] font-bold text-[#013981] uppercase tracking-wider">STANDAR EVALUASI KURIKULUM</p>
                                <h3 class="text-xl font-bold text-slate-900 mt-0.5">Komponen &amp; Indikator Capaian</h3>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Skala 0–100
                            </span>
                        </div>

                        {{-- 4 Competency Modules --}}
                        <div class="grid grid-cols-2 gap-3.5">
                            @foreach([
                                ['PII', 'Praktik Industri', 'Implementasi teknis di lini produksi / proyek rekayasa', 'bg-blue-50 text-[#013981] border-blue-200'],
                                ['SUP', 'Supervisi Akademik', 'Konsultasi berkala & pelaporan capaian mata kuliah', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                ['K3',  'Keselamatan Kerja', 'Penerapan standar K3, ergonomi, dan disiplin lingkungan', 'bg-amber-50 text-amber-700 border-amber-200'],
                                ['LTD', 'Laporan Teknik', 'Penyusunan dokumentasi teknis & sidang presentasi akhir', 'bg-purple-50 text-purple-700 border-purple-200'],
                            ] as [$kd, $nm, $ket, $cls])
                            <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-2">
                                <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded-lg border {{ $cls }} inline-block">{{ $kd }}</span>
                                <h4 class="text-sm font-bold text-slate-900">{{ $nm }}</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ $ket }}</p>
                            </div>
                            @endforeach
                        </div>

                        {{-- Grade Conversion Bar --}}
                        <div class="bg-white rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-3">
                                <span>Konversi Indeks Prestasi Akhir</span>
                                <span class="font-mono text-[#013981]">Standar Polman Bandung</span>
                            </div>
                            <div class="grid grid-cols-5 gap-2 text-center">
                                @foreach([['A','85–100','bg-[#013981] text-white border-[#013981]'],['B','75–84','bg-slate-50 text-slate-800 border-slate-200'],['C','65–74','bg-slate-50 text-slate-800 border-slate-200'],['D','55–64','bg-slate-50 text-slate-800 border-slate-200'],['E','< 55','bg-slate-50 text-slate-800 border-slate-200']] as [$g, $rng, $gCls])
                                <div class="py-2 rounded-xl border {{ $gCls }}">
                                    <p class="font-mono text-sm font-extrabold">{{ $g }}</p>
                                    <p class="font-mono text-[10px] opacity-80 mt-0.5">{{ $rng }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         SECTION 5: PUSAT INFORMASI AKADEMIK / FAQ & CTA (#faq)
    ============================================================ --}}
    <section id="faq" class="py-20 lg:py-28 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-4xl mx-auto px-6">
            <div class="reveal text-center max-w-2xl mx-auto mb-14 space-y-3">
                <span class="inline-block text-xs font-bold text-[#013981] bg-[#eef4fc] border border-[#d8e6f8] px-4 py-1.5 rounded-full uppercase tracking-wider">
                    Pusat Informasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-base text-slate-600 font-medium">
                    Panduan singkat mengenai pengisian logbook, supervisi, dan pengesahan nilai di portal PBL &amp; PPI.
                </p>
            </div>

            @php
            $faqs = [
                [
                    'q' => 'Bagaimana cara mahasiswa memulai pengisian logbook harian?',
                    'a' => 'Setelah akun mahasiswa aktif dan telah di-plotting ke proyek kampus atau perusahaan mitra oleh Admin/KPS, mahasiswa dapat masuk ke menu Logbook Magang, memilih minggu dan hari pelaksanaan, mengisi deskripsi kegiatan, durasi jam, serta mengunggah foto dokumentasi.',
                ],
                [
                    'q' => 'Apakah dosen perlu membuat dua akun untuk menjadi Dosen Pengampu sekaligus KPS atau Manager Proyek?',
                    'a' => 'Tidak perlu. Sistem mendukung fitur Multi-Role (Single Sign-On) sehingga satu akun dosen yang telah diberi hak akses dapat langsung berganti mode antara Dosen Pengampu, Manager Proyek, maupun Koordinator Program Studi (KPS) dari menu sidebar.',
                ],
                [
                    'q' => 'Bagaimana mekanisme pengesahan tanda tangan digital ber-QR pada transkrip nilai?',
                    'a' => 'Mahasiswa dapat menekan tombol Ajukan TTD QR pada halaman Nilai & Transkrip. Notifikasi akan terkirim secara real-time ke akun Dosen Pembimbing atau Mentor Industri untuk direview dan disetujui, lalu kode QR resmi otomatis tercetak pada dokumen PDF.',
                ],
                [
                    'q' => 'Bagaimana jika akun dosen yang baru didaftarkan belum bisa login?',
                    'a' => 'Pendaftaran akun baru untuk peran Dosen memerlukan verifikasi dan persetujuan dari Administrator melalui menu Approval Akun untuk menjaga keamanan akses penilaian akademik.',
                ],
            ];
            @endphp

            <div x-data="{ openFaq: 0 }" class="reveal space-y-3.5">
                @foreach($faqs as $idx => $item)
                <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs transition-all">
                    <button type="button"
                            @click="openFaq = (openFaq === {{ $idx }} ? null : {{ $idx }})"
                            class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                        <span class="text-sm sm:text-base font-bold text-slate-900">{{ $item['q'] }}</span>
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 transition-transform duration-200"
                              :class="openFaq === {{ $idx }} ? 'rotate-180 bg-[#eef4fc] text-[#013981]' : ''">
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </span>
                    </button>
                    <div x-show="openFaq === {{ $idx }}" x-collapse>
                        <div class="px-6 pb-5 pt-1 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            {{ $item['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</main>

{{-- ============================================================
     OFFICIAL POLMAN BANDUNG FOOTER
============================================================ --}}
<footer class="mt-auto bg-[#013981] text-white">
    <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between gap-12">
        {{-- Logo & Copyright --}}
        <div class="flex flex-col gap-5 md:w-1/2">
            <img src="{{ asset('images/logo-banner.png') }}" alt="Polman Logo Banner" class="h-16 w-auto object-contain object-left">
            <div class="text-sm font-medium text-white/80">
                &copy; {{ date('Y') }} Politeknik Manufaktur Bandung. All rights reserved.
            </div>
        </div>

        {{-- Address Info --}}
        <div class="flex flex-col gap-2 text-sm text-white/90 md:w-1/2 md:text-right">
            <h4 class="font-bold text-lg text-white mb-1">Alamat Kampus</h4>
            <p>Jl. Kanayakan No. 21, Dago, Kecamatan Coblong</p>
            <p>Kota Bandung, Jawa Barat 40135</p>
            <p>Telepon: (022) 2500241</p>
            <p>Surel: sekretariat@polman-bandung.ac.id</p>
        </div>
    </div>
</footer>

<script>
(function () {
    const heroBg = document.getElementById('hero-bg');
    if (heroBg) {
        const img = new Image();
        img.src = "{{ asset('images/PolmanBandung.png') }}";
        img.onload = () => {
            heroBg.classList.remove('opacity-0');
            heroBg.classList.add('opacity-100');
        };
    }

    const reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && reveals.length) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        reveals.forEach(el => obs.observe(el));
    } else {
        reveals.forEach(el => el.classList.add('is-visible'));
    }
})();
</script>

</body>
</html>
