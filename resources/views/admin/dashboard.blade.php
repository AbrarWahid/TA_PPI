@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Command <span class="text-[#2dce89]">Center</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Selamat datang, {{ auth()->user()->name }}
        </p>
    </div>
    <div class="flex items-center gap-3 flex-wrap justify-end">
        @if(($stats['pending_akun'] ?? 0) > 0)
        <a href="{{ route('approval.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-xs font-bold hover:bg-amber-100 transition-all">
            <span class="material-symbols-outlined text-base">how_to_reg</span>
            {{ $stats['pending_akun'] }} Pending Akun
        </a>
        @endif
        @if(($stats['pending_proyek'] ?? 0) > 0)
        <a href="{{ route('pengajuan_proyek.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-100 transition-all">
            <span class="material-symbols-outlined text-base">pending_actions</span>
            {{ $stats['pending_proyek'] }} Proyek Pending
        </a>
        @endif
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">

    {{-- Total Mahasiswa --}}
    <div class="relative overflow-hidden rounded-[2rem] h-36 p-6 flex flex-col justify-between shadow-xl"
         style="background:#004d4d;">
        <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
        <h2 class="text-5xl font-black italic tracking-tighter text-white">{{ $stats['total_mahasiswa'] }}</h2>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">group</span>
    </div>

    {{-- Proyek Aktif --}}
    <div class="relative overflow-hidden rounded-[2rem] h-36 p-6 flex flex-col justify-between shadow-xl"
         style="background:#2dce89;">
        <p class="text-white/70 text-[10px] font-black uppercase tracking-widest">Proyek Aktif</p>
        <h2 class="text-5xl font-black italic tracking-tighter text-white">{{ $stats['total_proyek_aktif'] }}</h2>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/20" style="font-size:100px;">rocket_launch</span>
    </div>

    {{-- Pending Approval Akun --}}
    <div class="relative overflow-hidden rounded-[2rem] h-36 p-6 flex flex-col justify-between bg-white border-2
        {{ ($stats['pending_akun'] ?? 0) > 0 ? 'border-amber-200' : 'border-gray-100' }}">
        <p class="text-[10px] font-black uppercase tracking-widest
            {{ ($stats['pending_akun'] ?? 0) > 0 ? 'text-amber-500' : 'text-gray-400' }}">
            Approval Akun
        </p>
        <div>
            <h2 class="text-5xl font-black italic tracking-tighter
                {{ ($stats['pending_akun'] ?? 0) > 0 ? 'text-amber-500' : 'text-gray-300' }}">
                {{ $stats['pending_akun'] ?? 0 }}
            </h2>
            <p class="text-[10px] font-bold text-gray-400 mt-1">menunggu persetujuan</p>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-gray-100" style="font-size:100px;">how_to_reg</span>
    </div>

    {{-- Pending Approval Proyek --}}
    <div class="relative overflow-hidden rounded-[2rem] h-36 p-6 flex flex-col justify-between bg-white border-2
        {{ ($stats['pending_proyek'] ?? 0) > 0 ? 'border-blue-200' : 'border-gray-100' }}">
        <p class="text-[10px] font-black uppercase tracking-widest
            {{ ($stats['pending_proyek'] ?? 0) > 0 ? 'text-blue-500' : 'text-gray-400' }}">
            Approval Proyek
        </p>
        <div>
            <h2 class="text-5xl font-black italic tracking-tighter
                {{ ($stats['pending_proyek'] ?? 0) > 0 ? 'text-blue-500' : 'text-gray-300' }}">
                {{ $stats['pending_proyek'] ?? 0 }}
            </h2>
            <p class="text-[10px] font-bold text-gray-400 mt-1">menunggu review</p>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-gray-100" style="font-size:100px;">approval</span>
    </div>
</div>

{{-- PROGRESS BAR MONITORING --}}
<div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-6 flex items-center gap-6 shadow-sm">
    <div class="shrink-0">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Progress Penilaian</p>
        <p class="text-4xl font-black italic text-[#004d4d]">{{ $stats['progress_pct'] ?? 0 }}%</p>
    </div>
    <div class="flex-1">
        <div class="w-full h-3 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width:{{ $stats['progress_pct'] ?? 0 }}%; background:linear-gradient(90deg,#7fffd4,#004d4d);"></div>
        </div>
        <p class="text-[10px] text-gray-400 font-bold mt-1.5">
            Mahasiswa yang sudah memiliki nilai lengkap (Manager + Dosen)
        </p>
    </div>
    <a href="{{ route('penilaian.index') }}"
       class="shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-black text-[#7fffd4] shadow-lg hover:opacity-80 transition-all"
       style="background:#004d4d;">
        Lihat Rekap
        <span class="material-symbols-outlined text-sm">chevron_right</span>
    </a>
</div>

{{-- AKTIVITAS TERBARU: 2 kolom --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Logbook Menunggu Verifikasi --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <p class="text-sm font-black text-[#004d4d] uppercase tracking-tight">Logbook Pending</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Mingguan belum diverifikasi</p>
            </div>
            <a href="{{ route('logbook_mingguan.index') }}"
               class="text-[10px] font-black text-[#2dce89] hover:opacity-70 uppercase tracking-widest">
                Lihat Semua →
            </a>
        </div>

        @if($logbookPending->isEmpty())
        <div class="px-6 py-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-200 block mb-2">task_alt</span>
            <p class="text-xs font-bold text-gray-300 italic">Semua logbook sudah diverifikasi</p>
        </div>
        @else
        <ul class="divide-y divide-gray-50">
            @foreach($logbookPending as $lb)
            <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0"
                         style="background:#004d4d;">
                        {{ strtoupper(substr($lb->mahasiswa->nama ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800">{{ $lb->mahasiswa->nama ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">Minggu ke-{{ $lb->minggu_ke }}</p>
                    </div>
                </div>
                <span class="inline-block px-3 py-1 rounded-lg text-[10px] font-black bg-amber-100 text-amber-600">
                    Pending
                </span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Laporan Menunggu Verifikasi --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <p class="text-sm font-black text-[#004d4d] uppercase tracking-tight">Laporan Pending</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">MK/PPI belum diverifikasi</p>
            </div>
            <a href="{{ route('laporan.index') }}"
               class="text-[10px] font-black text-[#2dce89] hover:opacity-70 uppercase tracking-widest">
                Lihat Semua →
            </a>
        </div>

        @if($laporanPending->isEmpty())
        <div class="px-6 py-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-200 block mb-2">task_alt</span>
            <p class="text-xs font-bold text-gray-300 italic">Semua laporan sudah diverifikasi</p>
        </div>
        @else
        <ul class="divide-y divide-gray-50">
            @foreach($laporanPending as $lap)
            <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0"
                         style="background:#2dce89;">
                        {{ strtoupper(substr($lap->mahasiswa->nama ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800">{{ $lap->mahasiswa->nama ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $lap->jenis_laporan }}</p>
                    </div>
                </div>
                <span class="inline-block px-3 py-1 rounded-lg text-[10px] font-black bg-blue-100 text-blue-600">
                    Pending
                </span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

</div>

{{-- QUICK ACTIONS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <a href="{{ route('approval.index') }}"
       class="flex items-center gap-3 px-5 py-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <span class="material-symbols-outlined text-2xl text-[#2dce89]">how_to_reg</span>
        <div>
            <p class="text-xs font-black text-[#004d4d]">Approval Akun</p>
            <p class="text-[10px] text-gray-400">Kelola pendaftaran</p>
        </div>
    </a>
    <a href="{{ route('pengajuan_proyek.index') }}"
       class="flex items-center gap-3 px-5 py-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <span class="material-symbols-outlined text-2xl text-[#2dce89]">approval</span>
        <div>
            <p class="text-xs font-black text-[#004d4d]">Approval Proyek</p>
            <p class="text-[10px] text-gray-400">Review pengajuan</p>
        </div>
    </a>
    <a href="{{ route('laporan.admin') }}"
       class="flex items-center gap-3 px-5 py-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <span class="material-symbols-outlined text-2xl text-[#2dce89]">monitoring</span>
        <div>
            <p class="text-xs font-black text-[#004d4d]">Monitoring</p>
            <p class="text-[10px] text-gray-400">Progress aktivitas</p>
        </div>
    </a>
    <a href="{{ route('penilaian.index') }}"
       class="flex items-center gap-3 px-5 py-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
        <span class="material-symbols-outlined text-2xl text-[#2dce89]">grade</span>
        <div>
            <p class="text-xs font-black text-[#004d4d]">Rekap Nilai</p>
            <p class="text-[10px] text-gray-400">Asesmen & nilai</p>
        </div>
    </a>
</div>

@endsection