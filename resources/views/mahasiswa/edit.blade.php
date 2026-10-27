@extends('layouts.app')
@section('title', 'Edit Mahasiswa')
@section('content')

<div class="max-w-lg">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('mahasiswa.index') }}"
           class="p-2.5 text-outline hover:text-primary hover:bg-primary-container rounded-xl transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-black text-on-surface tracking-tight">Edit <span class="text-[#2dce89]">Mahasiswa</span></h1>
            <p class="text-sm text-on-surface-variant mt-0.5">Perbarui data mahasiswa</p>
        </div>
        {{-- Avatar --}}
        <div class="ml-auto w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-on-primary font-black text-sm">
            {{ strtoupper(substr($mahasiswa->nama, 0, 2)) }}
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
        <span class="material-symbols-outlined text-red-500 text-base mt-0.5">error</span>
        <ul class="text-xs text-red-600 font-bold space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm p-8 space-y-5">

        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->id) }}">
            @csrf @method('PUT')

            <div class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                        class="w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all
                        {{ $errors->has('nim') ? 'border-red-400 bg-red-50' : '' }}">
                    @error('nim')<p class="text-xs text-red-500 font-bold mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                        class="w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all
                        {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : '' }}">
                    @error('nama')<p class="text-xs text-red-500 font-bold mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">Program Studi</label>
                    <select name="prodi" required
                        class="w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">Pilih Program Studi</option>
                        <option value="informatika" {{ old('prodi', $mahasiswa->user->prodi ?? '') === 'informatika' ? 'selected' : '' }}>D4 TRIN – Teknologi Rekayasa Informatika Industri</option>
                        <option value="otomasi" {{ old('prodi', $mahasiswa->user->prodi ?? '') === 'otomasi' ? 'selected' : '' }}>D4 TRO – Teknologi Rekayasa Otomasi</option>
                        <option value="mekatronika" {{ old('prodi', $mahasiswa->user->prodi ?? '') === 'mekatronika' ? 'selected' : '' }}>D4 TRMO – Teknologi Rekayasa Mekatronika</option>
                    </select>
                    @error('prodi')<p class="text-xs text-red-500 font-bold mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Field Kelas --}}
                <div>
                    <label class="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">Kelas <span class="text-outline/50 normal-case font-medium">(Opsional)</span></label>
                    <input type="text" name="kelas" value="{{ old('kelas', $mahasiswa->kelas) }}"
                        placeholder="contoh: 3 AEA 3"
                        class="w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface placeholder:text-outline/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all
                        {{ $errors->has('kelas') ? 'border-red-400 bg-red-50' : '' }}">
                    @error('kelas')<p class="text-xs text-red-500 font-bold mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Email (read-only) --}}
                <div>
                    <label class="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">Email</label>
                    <input type="text" value="{{ $mahasiswa->user->email ?? '-' }}" disabled
                        class="w-full px-5 py-3.5 bg-surface-container border border-outline-variant/20 rounded-2xl text-sm text-on-surface-variant cursor-not-allowed">
                    <p class="text-[11px] text-outline mt-1.5">Email tidak dapat diubah</p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-2xl text-sm font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg">
                        <span class="material-symbols-outlined text-base">check</span> Update
                    </button>
                    <a href="{{ route('mahasiswa.index') }}"
                        class="flex items-center gap-2 px-6 py-3 bg-surface-container text-on-surface-variant rounded-2xl text-sm font-black hover:bg-surface-container-high transition-all">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection