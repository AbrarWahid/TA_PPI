@extends('layouts.app')
@section('title', 'Detail Logbook')
@section('content')
@php
    $user  = auth()->user();
    $color = match($logbook->status_verifikasi) {
        'disetujui' => 'bg-emerald-50 text-emerald-700',
        'ditolak'   => 'bg-red-50 text-red-600',
        default     => 'bg-amber-50 text-amber-600',
    };
    $label = match($logbook->status_verifikasi) {
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
        default     => 'Menunggu',
    };
@endphp

{{-- HEADING --}}
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('logbook.index') }}"
        class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-teal-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div class="flex-1">
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Detail <span class="text-[#2dce89]">Logbook</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
            Minggu ke-{{ $logbook->minggu_ke }} · {{ \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y') }}
        </p>
    </div>
    <span class="text-[10px] font-black px-4 py-2 rounded-full {{ $color }} uppercase tracking-widest">{{ $label }}</span>
</div>

<div class="max-w-2xl space-y-4">

    {{-- INFO CARD --}}
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Informasi Logbook</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach([
                ['Mahasiswa', $logbook->mahasiswa->nama],
                ['NIM',       $logbook->mahasiswa->nim],
                ['Kelas',     $logbook->kelas->nama_kelas ?? '-'],
                ['Minggu Ke', 'Minggu ' . $logbook->minggu_ke],
                ['Tanggal',   \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y')],
            ] as [$key, $val])
            <div class="px-6 py-4 flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-32 shrink-0">{{ $key }}</span>
                <span class="text-sm font-black text-[#004d4d] text-right">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ISI LOGBOOK --}}
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Isi Logbook</p>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="px-6 py-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center text-[#004d4d]">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">task_alt</span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktivitas</p>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $logbook->aktivitas }}</p>
            </div>
            <div class="px-6 py-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">warning</span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kendala</p>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $logbook->kendala ?: '-' }}</p>
            </div>
            <div class="px-6 py-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">lightbulb</span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Solusi</p>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $logbook->solusi ?: '-' }}</p>
            </div>
        </div>
    </div>

    {{-- CATATAN DOSEN --}}
    @if($logbook->catatan_dosen)
    <div class="bg-amber-50 border border-amber-100 rounded-[1.5rem] px-6 py-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-amber-500 text-lg" style="font-variation-settings:'FILL' 1">comment</span>
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-700">Catatan Dosen</p>
        </div>
        <p class="text-sm text-amber-800 leading-relaxed">{{ $logbook->catatan_dosen }}</p>
    </div>
    @endif

    {{-- FORM VERIFIKASI (Manager only) --}}
    @if($user->isManager() && $logbook->status_verifikasi === 'menunggu')
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Verifikasi Logbook</p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('logbook.verifikasi', $logbook) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                        Catatan <span class="text-gray-300 font-medium normal-case">(opsional)</span>
                    </label>
                    <textarea name="catatan_dosen" rows="3"
                        placeholder="Tambahkan catatan untuk mahasiswa..."
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 transition resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="status_verifikasi" value="disetujui"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500 text-white font-black rounded-2xl text-sm hover:opacity-90 transition">
                        <span class="material-symbols-outlined text-sm">check_circle</span> Setujui
                    </button>
                    <button type="submit" name="status_verifikasi" value="ditolak"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 font-black rounded-2xl text-sm hover:bg-red-100 transition border border-red-100">
                        <span class="material-symbols-outlined text-sm">cancel</span> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection