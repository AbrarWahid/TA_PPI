@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Master Data <span class="text-[#2dce89]">Kelas</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                {{ $kelas->count() }} Kelas Terdaftar
            </p>
        </div>
        <a href="{{ route('kelas.create') }}"
           class="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20">
            <span class="material-symbols-outlined text-base">add</span> TAMBAH KELAS
        </a>
    </div>

    {{-- STAT STRIP --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-[#004d4d] text-white p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
            <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Kelas</p>
            <p class="text-5xl font-black italic tracking-tighter">{{ $kelas->count() }}</p>
        </div>
        <div class="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
            <p class="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Periode Aktif</p>
            <p class="text-lg font-black italic tracking-tighter text-[#004d4d] leading-tight">
                {{ $periodeAktif->nama_periode ?? '-' }}
            </p>
        </div>
        <div class="bg-white border-2 border-teal-100 p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
            <p class="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
            <p class="text-4xl font-black italic tracking-tighter text-[#004d4d]">
                {{ $kelas->sum('mahasiswa_count') }}
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">#</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Kelas</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Jumlah Mahasiswa</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kelas as $idx => $k)
                <tr class="hover:bg-teal-50/30 transition-colors group">
                    <td class="px-8 py-5 text-[10px] font-black text-gray-300">{{ $idx + 1 }}</td>

                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white shadow-md flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">class</span>
                            </div>
                            <div>
                                <span class="font-black text-[#004d4d] block leading-none">{{ $k->nama_kelas }}</span>
                                <span class="text-[10px] text-gray-400 font-medium mt-0.5 block uppercase tracking-wider">
                                    {{ $k->kode_kelas }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-8 py-5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-700 border border-teal-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">group</span>
                            {{ $k->mahasiswa_count }} Mahasiswa
                        </span>
                    </td>

                    <td class="px-8 py-5 text-center">
    <a href="{{ route('kelas.show', $k->id) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#004d4d] text-[#7fffd4] rounded-xl text-[10px] font-black hover:scale-105 transition-all"
       title="Detail">
        <span class="material-symbols-outlined text-sm">visibility</span> DETAIL
    </a>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center opacity-40">
                        <span class="material-symbols-outlined text-5xl text-gray-300">class</span>
                        <p class="font-black italic text-gray-400 mt-2">Belum ada data kelas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection