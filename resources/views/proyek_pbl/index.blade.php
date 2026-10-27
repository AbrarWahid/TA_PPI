@extends('layouts.app')

@section('title', 'Proyek PBL')

@section('content')

@php
    $totalMahasiswa = $proyek->sum(fn($p) => $p->getTotalMahasiswa());
    $totalProdi = $proyek->flatMap(fn($p) => $p->kebutuhan->pluck('prodi'))->unique()->count();
@endphp

{{-- HEADING --}}
<div class="mb-8">
    <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
        Proyek <span class="text-[#2dce89]">PBL</span>
    </h1>
    <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Daftar proyek yang telah disetujui</p>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d] mb-4">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">assignment</span>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Proyek</p>
        <h3 class="text-5xl font-black italic text-[#004d4d] mt-1">{{ $proyek->count() }}</h3>
    </div>

    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d] mb-4">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">group</span>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Mahasiswa</p>
        <h3 class="text-5xl font-black italic text-[#004d4d] mt-1">{{ $totalMahasiswa }}</h3>
    </div>

    <div class="bg-[#004d4d] rounded-[1.5rem] shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-white mb-4">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">school</span>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-teal-100/70">Prodi Terlibat</p>
        <h3 class="text-5xl font-black italic text-white mt-1">{{ $totalProdi }}</h3>
    </div>
</div>

{{-- LIST PROYEK --}}
@if($proyek->isEmpty())
<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-200">assignment</span>
    <p class="mt-4 text-gray-400 text-sm font-medium">Belum ada proyek yang disetujui.</p>
    <a href="{{ route('pengajuan_proyek.index') }}"
        class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black bg-[#004d4d] text-[#7fffd4] rounded-2xl hover:opacity-90 transition">
        <span class="material-symbols-outlined text-base">arrow_forward</span>
        Lihat Pengajuan
    </a>
</div>

@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($proyek as $item)
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 font-mono">{{ $item->kode_pengajuan }}</p>
                <h3 class="font-black text-[#004d4d] text-base leading-tight">{{ $item->judul_proyek }}</h3>
            </div>
            <span class="flex-shrink-0 inline-flex items-center gap-1 text-[10px] font-black px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 uppercase tracking-wide">
                <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1">check_circle</span> Aktif
            </span>
        </div>

        {{-- Deskripsi --}}
        <p class="text-sm text-slate-400 leading-relaxed mb-4 line-clamp-2">{{ $item->deskripsi }}</p>

        {{-- Kebutuhan Prodi --}}
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach($item->kebutuhan as $k)
            <span class="text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wide {{ $k->getProdiColor() }}">
                {{ $k->getProdiShort() }} · {{ $k->jumlah_mahasiswa }} mhs
            </span>
            @endforeach
        </div>

        {{-- Meta --}}
        <div class="border-t border-gray-100 pt-4 flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-slate-400 font-medium">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-[#004d4d]">person</span>
                {{ $item->manager->name }}
            </span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-[#004d4d]">calendar_today</span>
                {{ $item->tanggal_mulai->format('d M Y') }} – {{ $item->tanggal_selesai->format('d M Y') }}
            </span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-[#004d4d]">group</span>
                {{ $item->getTotalMahasiswa() }} mahasiswa
            </span>
            @if($item->anggaran)
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-[#004d4d]">payments</span>
                Rp {{ number_format($item->anggaran, 0, ',', '.') }}
            </span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection