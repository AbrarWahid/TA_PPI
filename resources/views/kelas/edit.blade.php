@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')

<div class="flex-1 flex flex-col max-w-xl">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('kelas.index') }}"
            class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 hover:bg-teal-50 hover:border-teal-100 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[#004d4d]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Edit <span class="text-[#2dce89]">{{ $kelas->nama_kelas }}</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                Perbarui data kelas
            </p>
        </div>
    </div>

    {{-- WARNING --}}
    <div class="mb-6 flex items-start gap-3 p-4 bg-amber-50 border border-amber-100 rounded-2xl">
        <span class="material-symbols-outlined text-amber-500 text-xl mt-0.5">warning</span>
        <p class="text-xs font-bold text-amber-700 leading-relaxed">
            Mengubah <span class="font-black">Nama Kelas</span> akan memutus relasi dengan mahasiswa yang memiliki kelas tersebut.
            Pastikan nama kelas sesuai dengan data di kolom kelas mahasiswa.
        </p>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span> {{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                    Kode Kelas
                </label>
                <input type="text" name="kode_kelas" value="{{ old('kode_kelas', $kelas->kode_kelas) }}"
                    placeholder="contoh: 3AEC4"
                    class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] placeholder:text-gray-300 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                    Nama Kelas
                </label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                    placeholder="contoh: 3 AEC 4"
                    class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] placeholder:text-gray-300 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition">
                <p class="text-[10px] text-gray-400 font-bold mt-1.5 tracking-wide">
                    Nama kelas harus sama persis dengan data di kolom kelas mahasiswa (contoh: "3 AEA 3")
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 bg-[#004d4d]