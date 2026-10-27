@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
@php $user = auth()->user(); @endphp

{{-- HEADING --}}
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('penilaian.index') }}"
        class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-teal-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Detail <span class="text-[#2dce89]">Penilaian</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Rincian nilai mahasiswa</p>
    </div>
</div>

{{-- INFO + NILAI AKHIR --}}
<div class="grid grid-cols-3 gap-4 mb-6">

    {{-- Info Mahasiswa --}}
    <div class="col-span-2 bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-5">Informasi Mahasiswa</p>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Mahasiswa</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-black bg-[#004d4d]">
                        {{ strtoupper(substr($penilaian->mahasiswa->nama ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-black text-[#004d4d]">{{ $penilaian->mahasiswa->nama ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $penilaian->mahasiswa->nim ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kelas</p>
                <p class="font-black text-[#004d4d]">{{ $penilaian->kelas->nama_kelas ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Dosen Pengampu</p>
                <p class="font-black text-[#004d4d]">{{ $penilaian->dosen->nama_dosen ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Input</p>
                <p class="font-black text-[#004d4d]">{{ $penilaian->updated_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Nilai Akhir Card --}}
    <div class="bg-[#004d4d] rounded-[1.5rem] shadow-sm p-6 flex flex-col justify-between">
        <p class="text-[10px] font-black uppercase tracking-widest text-teal-100/60">Nilai Akhir</p>
        <div>
            <div class="text-6xl font-black italic text-white mt-2">
                {{ $penilaian->nilai_akhir !== null ? number_format($penilaian->nilai_akhir, 1) : '-' }}
            </div>
            @if($penilaian->nilai_akhir)
            <div class="mt-3 flex items-center gap-2">
                <span class="px-4 py-1.5 rounded-full text-[#004d4d] font-black text-lg bg-[#2dce89]">
                    {{ $penilaian->getGrade() }}
                </span>
                <span class="text-teal-100/60 text-xs font-medium">/ 100</span>
            </div>
            @endif
        </div>
        <div class="mt-4 pt-4 border-t border-white/10 space-y-1">
            <div class="flex justify-between text-xs">
                <span class="text-teal-100/60">Manager (55%)</span>
                <span class="font-black text-white">{{ $penilaian->nilai_manager !== null ? number_format($penilaian->nilai_manager, 1) : '-' }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-teal-100/60">Dosen (45%)</span>
                <span class="font-black text-white">{{ $penilaian->nilai_dosen !== null ? number_format($penilaian->nilai_dosen, 1) : '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL GRID --}}
<div class="grid grid-cols-2 gap-4">

{{-- MANAGER PROYEK --}}
<div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d]">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">manage_accounts</span>
            </div>
            <h3 class="font-black text-[#004d4d] text-sm uppercase tracking-widest">Manager Proyek</h3>
        </div>
        <span class="text-[10px] font-black px-3 py-1.5 rounded-full bg-teal-50 text-[#004d4d] uppercase tracking-widest">55%</span>
    </div>

    <div class="p-6 space-y-5">
        @php
        $grupManager = [
            ['label' => 'Learning Skills', 'bobot' => '20%', 'color' => 'bg-teal-500', 'items' => [
                ['ls_critical_thinking', 'Critical Thinking'],
                ['ls_kolaborasi',        'Kolaborasi'],
                ['ls_kreativitas',       'Kreativitas & Inovasi'],
                ['ls_komunikasi',        'Komunikasi'],
            ]],
            ['label' => 'Life Skills', 'bobot' => '20%', 'color' => 'bg-purple-500', 'items' => [
                ['lf_fleksibilitas', 'Fleksibilitas'],
                ['lf_kepemimpinan',  'Kepemimpinan'],
                ['lf_produktivitas', 'Produktivitas'],
                ['lf_social_skill',  'Social Skill'],
            ]],
            ['label' => 'Laporan Project', 'bobot' => '15%', 'color' => 'bg-blue-500', 'items' => [
                ['lp_rpp',            'RPP'],
                ['lp_logbook',        'Logbook'],
                ['lp_dokumen_projek', 'Dokumen Projek'],
            ]],
        ];
        @endphp

        @foreach($grupManager as $grup)
        <div>
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $grup['label'] }}</p>
                <span class="text-[10px] font-black text-gray-400">{{ $grup['bobot'] }}</span>
            </div>
            <div class="space-y-2">
                @foreach($grup['items'] as [$field, $label])
                <div class="flex items-center gap-3">
                    <span class="w-32 text-xs text-slate-500 font-medium shrink-0">{{ $label }}</span>
                    <div class="flex-1 bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $grup['color'] }}" style="width: {{ $penilaian->$field ?? 0 }}%"></div>
                    </div>
                    <span class="w-8 text-right text-xs font-black text-[#004d4d]">{{ $penilaian->$field ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Nilai Manager</span>
            <span class="text-2xl font-black italic text-[#004d4d]">
                {{ $penilaian->nilai_manager !== null ? number_format($penilaian->nilai_manager, 1) : '-' }}
            </span>
        </div>

        @if($penilaian->catatan_manager)
        <div class="p-4 rounded-2xl bg-teal-50 text-xs text-slate-600 leading-relaxed">
            <span class="font-black text-[#004d4d] block mb-1">Catatan Manager:</span>
            {{ $penilaian->catatan_manager }}
        </div>
        @endif
    </div>
</div>

{{-- DOSEN PENGAMPU --}}
<div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">school</span>
            </div>
            <h3 class="font-black text-[#004d4d] text-sm uppercase tracking-widest">Dosen Pengampu</h3>
        </div>
        <span class="text-[10px] font-black px-3 py-1.5 rounded-full bg-purple-50 text-purple-600 uppercase tracking-widest">45%</span>
    </div>

    <div class="p-6 space-y-5">
        @php
        $grupDosen = [
            ['label' => 'Literacy Skills', 'bobot' => '15%', 'color' => 'bg-amber-500', 'items' => [
                ['lit_informasi',  'Literasi Informasi'],
                ['lit_media',      'Literasi Media'],
                ['lit_teknologi',  'Literasi Teknologi'],
            ]],
            ['label' => 'Presentasi', 'bobot' => '15%', 'color' => 'bg-pink-500', 'items' => [
                ['pr_konten',      'Konten'],
                ['pr_visual',      'Tampilan Visual'],
                ['pr_kosakata',    'Kosakata'],
                ['pr_tanya_jawab', 'Tanya Jawab'],
                ['pr_mata_gerak',  'Mata & Gerak'],
            ]],
            ['label' => 'Laporan Akhir', 'bobot' => '15%', 'color' => 'bg-emerald-500', 'items' => [
                ['la_penulisan',    'Penulisan Laporan'],
                ['la_pilihan_kata', 'Pilihan Kata'],
                ['la_konten',       'Konten'],
            ]],
        ];
        @endphp

        @foreach($grupDosen as $grup)
        <div>
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $grup['label'] }}</p>
                <span class="text-[10px] font-black text-gray-400">{{ $grup['bobot'] }}</span>
            </div>
            <div class="space-y-2">
                @foreach($grup['items'] as [$field, $label])
                <div class="flex items-center gap-3">
                    <span class="w-32 text-xs text-slate-500 font-medium shrink-0">{{ $label }}</span>
                    <div class="flex-1 bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $grup['color'] }}" style="width: {{ $penilaian->$field ?? 0 }}%"></div>
                    </div>
                    <span class="w-8 text-right text-xs font-black text-[#004d4d]">{{ $penilaian->$field ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Nilai Dosen</span>
            <span class="text-2xl font-black italic text-purple-600">
                {{ $penilaian->nilai_dosen !== null ? number_format($penilaian->nilai_dosen, 1) : '-' }}
            </span>
        </div>

        @if($penilaian->catatan_dosen)
        <div class="p-4 rounded-2xl bg-purple-50 text-xs text-slate-600 leading-relaxed">
            <span class="font-black text-purple-700 block mb-1">Catatan Dosen:</span>
            {{ $penilaian->catatan_dosen }}
        </div>
        @endif
    </div>
</div>

</div>{{-- end grid --}}

@endsection