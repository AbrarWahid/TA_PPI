@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('content')

@php
$prodiMap = [
    'informatika' => 'D4 TRIN – Informatika Industri',
    'otomasi'     => 'D4 TRO – Teknik Rekayasa Otomasi',
    'mekatronika' => 'D4 TRMO – Teknik Rekayasa Mekatronika',
];
$prodiColor = [
    'informatika' => 'bg-blue-100 text-blue-700',
    'otomasi'     => 'bg-emerald-100 text-emerald-700',
    'mekatronika' => 'bg-purple-100 text-purple-700',
];
@endphp

<div class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('kelas.index') }}"
            class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 hover:bg-teal-50 hover:border-teal-100 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[#004d4d]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Kelas <span class="text-[#2dce89]">{{ $kelas->nama_kelas }}</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                {{ $kelas->kode_kelas }} &middot; {{ $kelas->mahasiswa->count() }} Mahasiswa
            </p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-[#004d4d] text-white p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
            <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
            <p class="text-5xl font-black italic tracking-tighter">{{ $kelas->mahasiswa->count() }}</p>
        </div>
        <div class="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
            <p class="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Kode Kelas</p>
            <p class="text-2xl font-black italic tracking-tighter text-[#004d4d]">{{ $kelas->kode_kelas }}</p>
        </div>
        <div class="bg-white border border-gray-100 p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
            <p class="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Nama Kelas</p>
            <p class="text-2xl font-black italic tracking-tighter text-[#004d4d]">{{ $kelas->nama_kelas }}</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-7 py-5 border-b border-gray-100 flex justify-between items-center">
            <h4 class="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">group</span> Daftar Mahasiswa
            </h4>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-xl">
                {{ $kelas->mahasiswa->count() }} Mahasiswa
            </span>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 w-12">No</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">NIM</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama Mahasiswa</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Program Studi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kelas->mahasiswa as $mhs)
                @php
                    $prodiKey  = $mhs->user->prodi ?? null;
                    $prodiNama = $prodiMap[$prodiKey] ?? '-';
                    $prodiCls  = $prodiColor[$prodiKey] ?? 'bg-gray-100 text-gray-500';
                @endphp
                <tr class="hover:bg-teal-50/30 transition-colors">
                    <td class="px-8 py-5 text-[10px] font-black text-gray-300">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                            {{ $mhs->nim }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                            </div>
                            <span class="font-black text-sm text-[#004d4d]">{{ $mhs->nama }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @if($prodiKey)
                        <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl {{ $prodiCls }}">
                            {{ $prodiNama }}
                        </span>
                        @else
                        <span class="text-xs text-gray-300 font-bold">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <span class="material-symbols-outlined text-5xl text-gray-300">group_off</span>
                        <p class="font-black italic text-gray-400 mt-2">Belum ada mahasiswa di kelas ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection