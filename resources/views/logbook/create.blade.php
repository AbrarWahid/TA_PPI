@extends('layouts.app')
@section('title', 'Input Logbook')
@section('content')

{{-- HEADING --}}
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('logbook.index') }}"
        class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-teal-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Input <span class="text-[#2dce89]">Logbook</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Catat aktivitas mingguan PBL Anda</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('logbook.store') }}" class="space-y-5">
            @csrf

            {{-- Kelas --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kelas</label>
                <select name="kelas_id" required
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }} ({{ $k->kode_kelas }})
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Minggu & Tanggal --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Minggu Ke</label>
                    <input type="number" name="minggu_ke" min="1" max="20" value="{{ old('minggu_ke') }}" required
                        placeholder="Contoh: 1"
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition"/>
                    @error('minggu_ke')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition"/>
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Aktivitas --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Aktivitas</label>
                <textarea name="aktivitas" rows="4" required
                    placeholder="Deskripsikan aktivitas yang dilakukan minggu ini..."
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition resize-none">{{ old('aktivitas') }}</textarea>
                @error('aktivitas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kendala --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                    Kendala <span class="text-gray-300 font-medium normal-case">(opsional)</span>
                </label>
                <textarea name="kendala" rows="3"
                    placeholder="Kendala yang ditemui..."
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition resize-none">{{ old('kendala') }}</textarea>
            </div>

            {{-- Solusi --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                    Solusi <span class="text-gray-300 font-medium normal-case">(opsional)</span>
                </label>
                <textarea name="solusi" rows="3"
                    placeholder="Solusi yang diterapkan..."
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition resize-none">{{ old('solusi') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition">
                    <span class="material-symbols-outlined text-base">save</span>
                    Simpan Logbook
                </button>
                <a href="{{ route('logbook.index') }}"
                    class="px-6 py-3 rounded-2xl border border-gray-100 text-slate-500 text-sm font-black hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection