@extends('layouts.app')
@section('title', 'Penilaian Manager')
@section('content')

@php
    $periode = \App\Models\PeriodePbl::getAktif();
    $bobotManager = $periode ? $periode->bobot_manager : 55;
@endphp

<div class="max-w-[1400px] mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase text-[#004d4d] tracking-tighter">
                Penilaian <span class="text-[#2dce89]">Manager</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-bold">{{ $penilaian->count() }} data penilaian telah terekam.</p>
        </div>
        <a href="{{ route('penilaian.manager.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-[#004d4d] hover:bg-[#003d3d] text-white font-black rounded-2xl text-sm shadow-lg hover:-translate-y-0.5 transition-all w-fit">
            <span class="material-symbols-outlined text-base">add</span> Tambah Penilaian
        </a>
    </div>

    {{-- TABEL BAKU --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 w-12">#</th>
                        <th class="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Mahasiswa</th>
                        <th class="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Proyek</th>
                        <th class="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Nilai Skala 100</th>
                        <th class="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($penilaian as $i => $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-7 py-5 text-sm font-bold text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-7 py-5">
                            <p class="font-black text-[#004d4d] text-sm">{{ $p->mahasiswa->nama ?? '-' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $p->mahasiswa->nim ?? '-' }}</p>
                        </td>
                        <td class="px-7 py-5">
                            <span class="inline-flex px-3 py-1.5 bg-teal-50 text-[#004d4d] rounded-lg text-[10px] font-black max-w-[200px] truncate">
                                {{ $p->pengajuanProyek->judul_proyek ?? '-' }}
                            </span>
                        </td>
                        <td class="px-7 py-5 text-center">
                            {{-- Perbaikan BUG Visual Nilai Manager --}}
                            <span class="font-black text-[#004d4d] text-lg">{{ number_format($p->nilai_manager, 1) }}</span>
                            <span class="block text-[10px] text-slate-400 font-bold mt-0.5">
                                / 100 &nbsp;(≈ {{ number_format($p->nilai_manager * ($bobotManager / 100), 1) }} / {{ $bobotManager }})
                            </span>
                        </td>
                        <td class="px-7 py-5 text-center">
                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl">
                                <span class="material-symbols-outlined text-[14px]">done_all</span> Selesai
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-7 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">grade</span>
                            <p class="text-sm font-bold text-slate-400">Belum ada penilaian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection