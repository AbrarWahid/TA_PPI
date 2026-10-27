@extends('layouts.app')
@section('title', 'Upload Laporan')
@section('content')

{{-- HEADING --}}
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('laporan.index') }}"
        class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-teal-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Upload <span class="text-[#2dce89]">Laporan</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Submit laporan untuk diverifikasi dosen</p>
    </div>
</div>

<div class="max-w-xl">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">

        @if($errors->any())
        <div class="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm">
            <div class="flex items-center gap-2 mb-2 font-black text-red-700">
                <span class="material-symbols-outlined text-base">error</span> Terdapat kesalahan
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Info Supervisi (otomatis) --}}
<div class="px-4 py-3 rounded-2xl bg-teal-50 border border-teal-100 text-sm text-teal-800">
    <p class="text-[10px] font-black uppercase tracking-widest text-teal-500 mb-1">Laporan diajukan ke</p>
    @foreach($supervisiList as $s)
    <p class="font-semibold">{{ $s->dosen->nama_dosen ?? '-' }}
        <span class="font-normal text-teal-600">· {{ $s->mataKuliah->nama_matkul ?? '-' }}</span>
    </p>
    @endforeach
</div>

            {{-- Jenis Laporan --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Laporan</label>
                <select name="jenis_laporan"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Supervisi"      {{ old('jenis_laporan') == 'Supervisi'      ? 'selected' : '' }}>Supervisi</option>
                    <option value="Laporan Teknik" {{ old('jenis_laporan') == 'Laporan Teknik' ? 'selected' : '' }}>Laporan Teknik</option>
                    <option value="PAB"            {{ old('jenis_laporan') == 'PAB'            ? 'selected' : '' }}>PAB</option>
                </select>
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">File Laporan</label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-teal-300 hover:bg-teal-50/30 transition-all">
                    <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">upload_file</span>
                    <p class="text-sm text-gray-400 font-medium mb-4">Klik untuk pilih file</p>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                        class="w-full text-xs text-slate-500
                               file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                               file:text-xs file:font-black file:bg-[#004d4d] file:text-[#7fffd4]
                               cursor-pointer"/>
                    <p class="text-[10px] text-gray-300 font-medium mt-3 uppercase tracking-widest">Format: PDF, DOC, DOCX · Maks 5MB</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition">
                    <span class="material-symbols-outlined text-base">upload</span>
                    Upload
                </button>
                <a href="{{ route('laporan.index') }}"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-100 text-slate-500 text-sm font-black hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection