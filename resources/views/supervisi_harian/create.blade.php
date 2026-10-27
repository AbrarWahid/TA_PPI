@extends('layouts.app')
@section('title', 'Tambah Supervisi Harian')

@section('content')

<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('supervisi_harian.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Tambah <span class="text-[#2dce89]">Supervisi Harian</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Catat aktivitas supervisi hari ini</p>
    </div>
</div>

@if($supervisiMatkulList->isEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
    <span class="material-symbols-outlined text-amber-500 mt-0.5">warning</span>
    <div>
        <p class="font-black text-amber-700 text-sm">Belum ada supervisi matkul</p>
        <p class="text-xs text-amber-600 mt-1">Admin belum mengassign Anda ke mata kuliah & dosen manapun. Hubungi admin untuk mengatur supervisi matkul terlebih dahulu.</p>
    </div>
</div>
@else

<div class="max-w-2xl">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('supervisi_harian.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Mata Kuliah & Dosen --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Mata Kuliah & Dosen *</label>
                <select name="supervisi_matkul_id" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] focus:border-[#004d4d] outline-none text-sm font-bold transition">
                    <option value="">— Pilih Mata Kuliah & Dosen —</option>
                    @foreach($supervisiMatkulList as $sm)
                    <option value="{{ $sm->id }}" {{ old('supervisi_matkul_id') == $sm->id ? 'selected' : '' }}>
                        {{ $sm->mataKuliah->nama_matkul ?? '-' }} — {{ $sm->dosen->nama_dosen ?? ($sm->dosen->user->name ?? '-') }}
                    </option>
                    @endforeach
                </select>
                @error('supervisi_matkul_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Minggu ke & Hari --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Minggu Ke *</label>
                    <input type="number" name="minggu_ke" min="1" value="{{ old('minggu_ke') }}" required
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition"
                           placeholder="Contoh: 9">
                    @error('minggu_ke')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Hari *</label>
                    <select name="hari" required
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                        <option value="">— Pilih Hari —</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                       class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Jam --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Jam Mulai *</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" required
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    @error('jam_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Jam Selesai *</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" required
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    @error('jam_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Aktivitas --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Aktivitas / Kegiatan *</label>
                <textarea name="aktivitas" rows="4" required
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm transition resize-none"
                          placeholder="Deskripsikan kegiatan supervisi yang dilakukan...">{{ old('aktivitas') }}</textarea>
                @error('aktivitas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kendala --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">
                    Kendala <span class="text-gray-400 font-normal normal-case">(opsional)</span>
                </label>
                <textarea name="kendala" rows="3"
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm transition resize-none"
                          placeholder="Kendala yang dihadapi (jika ada)...">{{ old('kendala') }}</textarea>
                @error('kendala')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Dokumentasi --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">
                    Dokumentasi <span class="text-gray-400 font-normal normal-case">(foto/pdf, maks 5MB)</span>
                </label>
                <input type="file" name="dokumentasi" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-600
                              file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0
                              file:text-xs file:font-black file:bg-[#004d4d] file:text-white
                              hover:file:bg-[#003d3d] transition">
                @error('dokumentasi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-3.5 bg-[#004d4d] text-white rounded-2xl font-black text-sm hover:bg-[#003d3d] transition-all shadow-lg">
                    Simpan Entri
                </button>
                <a href="{{ route('supervisi_harian.index') }}"
                   class="px-6 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endif
@endsection