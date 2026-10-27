@extends('layouts.app')
@section('content')
@php
    $user = auth()->user();
    
    // 1. FIX JUDUL DASHBOARD DINAMIS BERDASARKAN ROLE
    if ($user->isAdmin()) {
        $roleName = 'Dashboard Administrator';
    } elseif ($user->isKps()) {
        $roleName = 'Dashboard Koordinator Prodi';
    } elseif ($user->roleAktifDosen()) {
        $roleName = 'Dashboard Dosen Pengampu';
    } elseif ($user->isManager()) {
        $roleName = 'Dashboard Manager Proyek';
    } else {
        $roleName = 'Dashboard Mahasiswa';
    }

    // 2. KUNCI PRODI UNTUK KPS PADA STATISTIK DASHBOARD
    $mhsQuery       = \App\Models\Mahasiswa::query();
    $kelasQuery     = \App\Models\Kelas::query();
    $penilaianQuery = \App\Models\Penilaian::query();
    
    // Jika KPS, ambil prodi dari tabel user_roles
    if ($user->isKps()) {
        $prodiKps = \App\Models\UserRole::where('user_id', $user->id)->where('role', 'kps')->value('prodi');
        
        $mhsQuery->whereHas('user', fn($q) => $q->where('prodi', $prodiKps));
        $kelasQuery->whereHas('mahasiswa.user', fn($q) => $q->where('prodi', $prodiKps));
        $penilaianQuery->whereHas('mahasiswa.user', fn($q) => $q->where('prodi', $prodiKps));
    }

    $totalMahasiswa = $mhsQuery->count();
    $totalDosen     = \App\Models\Dosen::count(); // Dosen biarkan global
    $totalKelas     = $kelasQuery->count();
    $totalPenilaian = $penilaianQuery->count();
    $rataRata       = $penilaianQuery->avg('nilai_akhir');
@endphp

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            {{-- Menampilkan Role Dinamis --}}
            <h2 class="text-2xl font-extrabold tracking-tight text-on-surface">{{ $roleName }}</h2>
            <p class="text-sm text-slate-500 mt-1">Selamat datang, <span class="font-semibold text-primary">{{ $user->name }}</span>. Berikut ringkasan sistem semester ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-4 py-2 bg-surface-container-high text-primary font-semibold rounded-lg text-sm hover:bg-surface-container-highest transition-all">
                <span class="material-symbols-outlined text-sm">filter_list</span> Filter
            </button>
            <button class="flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-primary to-secondary text-white font-semibold rounded-lg text-sm shadow-lg shadow-primary/10 hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-sm">download</span> Unduh Rekap
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">group</span>
                </div>
                <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">Mahasiswa</span>
            </div>
            <p class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">Total Mahasiswa</p>
            <h3 class="text-3xl font-extrabold text-on-surface mt-1">{{ $totalMahasiswa }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">analytics</span>
                </div>
            </div>
            <p class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">Rata-rata Nilai</p>
            <h3 class="text-3xl font-extrabold text-on-surface mt-1">{{ $rataRata ? number_format($rataRata, 1) : '-' }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10 shadow-sm hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">edit_note</span>
                </div>
            </div>
            <p class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">Jumlah Penilaian</p>
            <h3 class="text-3xl font-extrabold text-on-surface mt-1">{{ $totalPenilaian }}</h3>
        </div>

        <div class="bg-gradient-to-br from-primary to-secondary p-6 rounded-xl shadow-md hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">verified</span>
                </div>
            </div>
            <p class="text-teal-100/70 uppercase tracking-widest text-[10px] font-bold">Total Kelas</p>
            <h3 class="text-2xl font-extrabold text-white mt-1">{{ $totalKelas }} Kelas</h3>
        </div>
    </section>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

        {{-- TABLE: 2 cols --}}
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant/10 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-surface-container-low flex justify-between items-center">
                <h4 class="text-sm font-bold text-on-surface">Daftar Mahasiswa</h4>
                <span class="text-xs text-slate-400">Total: {{ $totalMahasiswa }}</span>
            </div>
            <div class="overflow-y-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low text-[13px]">
                        {{-- Memastikan query tabel ini juga terpengaruh filter KPS --}}
                        @forelse($mhsQuery->with(['penilaian'])->take(5)->get() as $i => $m)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 text-slate-400 font-medium">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-3 font-bold text-on-surface">{{ $m->nim }}</td>
                            <td class="px-6 py-3 font-semibold text-primary">{{ $m->nama }}</td>
                            <td class="px-6 py-3 text-right font-bold text-primary">
                                {{ $m->penilaian->first()?->nilai_akhir ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada data mahasiswa</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-surface-container-low">
                <a href="/mahasiswa" class="text-xs font-semibold text-primary hover:underline">Lihat semua →</a>
            </div>
        </div>

        {{-- DISTRIBUSI: 1 col --}}
        <div class="flex flex-col gap-5">
            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10 shadow-sm flex-1">
                <h4 class="text-[10px] font-bold text-on-surface mb-5 uppercase tracking-wider">Distribusi Rentang Nilai</h4>
                @php
                    // Pastikan range nilai juga mengikuti filter
                    $ranges = [
                        '0-50'   => clone $penilaianQuery->whereBetween('nilai_akhir',[0,50])->count(),
                        '51-70'  => clone $penilaianQuery->whereBetween('nilai_akhir',[51,70])->count(),
                        '71-85'  => clone $penilaianQuery->whereBetween('nilai_akhir',[71,85])->count(),
                        '86-100' => clone $penilaianQuery->whereBetween('nilai_akhir',[86,100])->count(),
                    ];
                    $maxVal = max(array_values($ranges)) ?: 1;
                    $colors = ['bg-slate-300','bg-teal-200','bg-primary','bg-teal-400'];
                @endphp
                <div class="flex items-end gap-3 h-36 justify-between">
                    @php $ri = 0; @endphp
                    @foreach($ranges as $label => $count)
                        @php $height = max(5, round(($count/$maxVal)*100)); $c = $colors[$ri++]; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-[9px] font-bold text-slate-500">{{ $count }}</span>
                            <div class="{{ $c }} rounded-t-md w-full" style="height:{{ $height }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-2">
                    @foreach(array_keys($ranges) as $label)
                        <span class="text-[8px] font-bold text-slate-400 flex-1 text-center">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ANNOUNCEMENTS: 1 col --}}
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10 shadow-sm flex flex-col">
            <h4 class="text-[10px] font-bold text-on-surface mb-4 uppercase tracking-wider">Pengumuman</h4>
            <div class="space-y-3 flex-1">
                <div class="p-3 bg-amber-50 rounded-lg border-l-4 border-amber-400">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-amber-600 text-sm">warning</span>
                        <p class="text-[11px] font-bold text-amber-900">Deadline Input Nilai</p>
                    </div>
                    <p class="text-[10px] text-amber-800 leading-relaxed">Penginputan nilai akan ditutup dalam 48 jam. Harap segera verifikasi data.</p>
                </div>
                <div class="p-3 bg-teal-50 rounded-lg border-l-4 border-teal-400">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-teal-600 text-sm">info</span>
                        <p class="text-[11px] font-bold text-teal-900">Pembaruan Sistem</p>
                    </div>
                    <p class="text-[10px] text-teal-800 leading-relaxed">Fitur penilaian rubrik baru telah aktif. Cek menu Penilaian.</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border-l-4 border-slate-300">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-slate-500 text-sm">mail</span>
                        <p class="text-[11px] font-bold text-slate-800">Pesan Masuk</p>
                    </div>
                    <p class="text-[10px] text-slate-600 leading-relaxed">Terdapat permintaan verifikasi rubrik baru dari Admin.</p>
                </div>
            </div>

            {{-- System Health --}}
            <div class="mt-4 bg-slate-900 text-white p-4 rounded-xl">
                <h4 class="text-[10px] font-bold mb-3 uppercase tracking-wider text-slate-400">System Health</h4>
                <div class="flex justify-between text-[10px] mb-1.5">
                    <span>Server Load</span>
                    <span class="text-emerald-400 font-bold">Normal</span>
                </div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden mb-3">
                    <div class="bg-emerald-500 h-full w-[24%]"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[8px] text-slate-500 uppercase font-bold">Latency</p>
                        <p class="text-sm font-bold">14ms</p>
                    </div>
                    <div>
                        <p class="text-[8px] text-slate-500 uppercase font-bold">Uptime</p>
                        <p class="text-sm font-bold">99.9%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection