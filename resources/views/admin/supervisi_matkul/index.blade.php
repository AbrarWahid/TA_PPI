@extends('layouts.app')
@section('title', 'Supervisi Mata Kuliah')
@section('content')

<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Supervisi <span class="text-[#2dce89]">Mata Kuliah</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            {{ $totalSupervisi }} data supervisi terdaftar
        </p>
    </div>
    <a href="{{ route('admin.supervisi.create') }}"
       class="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20">
        <span class="material-symbols-outlined text-base">add</span> TAMBAH SUPERVISI
    </a>
</div>

@if($grouped->isEmpty())
<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">school</span>
    <p class="text-sm text-gray-400 font-medium">Belum ada data supervisi.</p>
</div>
@else

{{-- Stats bar --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-[#004d4d] rounded-2xl p-5">
        <p class="text-[10px] font-black uppercase tracking-widest text-teal-100/50 mb-1">Total Supervisi</p>
        <p class="text-3xl font-black text-white">{{ $totalSupervisi }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Mata Kuliah</p>
        <p class="text-3xl font-black text-[#004d4d]">{{ $grouped->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Mahasiswa</p>
        <p class="text-3xl font-black text-[#004d4d]">{{ $totalSupervisi }}</p>
    </div>
</div>

{{-- Cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach($grouped as $key => $items)
    @php
        $first = $items->first();
        $matkul = $first->mataKuliah;
        $dosen  = $first->dosen;
    @endphp

    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">

        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#004d4d] text-xl" style="font-variation-settings:'FILL' 1">menu_book</span>
                </div>
                <div>
                    <p class="font-black text-[#004d4d] text-sm">{{ $matkul->nama_matkul }}</p>
                    <p class="text-[10px] text-gray-400 font-bold mt-0.5">
                        {{ $matkul->kode_matkul }} &nbsp;·&nbsp; {{ $matkul->sks }} SKS
                    </p>
                </div>
            </div>
            <span class="px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 text-[10px] font-black">
                {{ $first->tahun_ajaran }} / Sem {{ $first->semester }}
            </span>
        </div>

        {{-- Dosen --}}
        <div class="px-6 py-3 bg-gray-50/50 flex items-center gap-3 border-b border-gray-50">
            <span class="material-symbols-outlined text-gray-400 text-base">school</span>
            <p class="text-xs font-bold text-gray-500">
                Dosen Pengampu: <span class="text-[#004d4d]">{{ $dosen->user->name }}</span>
            </p>
        </div>

        {{-- Mahasiswa List --}}
        <div class="px-6 py-4 space-y-3">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">
                {{ $items->count() }} Mahasiswa
            </p>
            @foreach($items as $s)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#004d4d] text-[#7fffd4] flex items-center justify-center font-black text-[10px] flex-shrink-0">
                        {{ strtoupper(substr($s->mahasiswa->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-[#004d4d]">{{ $s->mahasiswa->nama }}</p>
                        <p class="text-[10px] text-gray-400">{{ $s->mahasiswa->nim }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.supervisi.destroy', $s->id) }}" method="POST"
                      onsubmit="return confirm('Hapus supervisi {{ $s->mahasiswa->nama }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-2 text-gray-300 hover:text-white hover:bg-red-500 rounded-xl transition-all">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>

    </div>
    @endforeach
</div>
@endif

@endsection