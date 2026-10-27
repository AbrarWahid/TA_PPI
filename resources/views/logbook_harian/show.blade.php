@extends('layouts.app')
@section('title', 'Detail Logbook Harian')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('logbook_harian.index') }}"
           class="p-2.5 text-gray-400 hover:text-primary hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-on-surface">Detail Logbook</h1>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ $logbook_harian->hari }}, {{ $logbook_harian->tanggal->format('d M Y') }}
            </p>
        </div>
        <div class="ml-auto">
            <span class="text-[10px] font-bold px-3 py-1.5 rounded-full {{ $logbook_harian->getStatusBadgeColor() }}">
                {{ $logbook_harian->getStatusLabel() }}
            </span>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Info Ringkas --}}
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm p-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-xl p-4 col-span-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Proyek</p>
                    <p class="text-sm font-semibold text-on-surface">
                        {{ $logbook_harian->proyek->judul_proyek ?? '-' }}
                    </p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Minggu ke</p>
                    <p class="text-sm font-semibold text-on-surface">{{ $logbook_harian->minggu_ke }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hari</p>
                    <p class="text-sm font-semibold text-on-surface">{{ $logbook_harian->hari }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 col-span-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal</p>
                    <p class="text-sm font-semibold text-on-surface">
                        {{ $logbook_harian->tanggal->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Aktivitas --}}
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm p-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Aktivitas Harian</p>
            <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line">{{ $logbook_harian->aktivitas }}</p>
        </div>

        {{-- Dokumentasi --}}
        @if($logbook_harian->dokumentasi)
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm p-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Dokumentasi</p>
            @php $ext = strtolower(pathinfo($logbook_harian->dokumentasi, PATHINFO_EXTENSION)); @endphp
            @if(in_array($ext, ['jpg','jpeg','png']))
                <img src="{{ asset('storage/' . $logbook_harian->dokumentasi) }}"
                     class="rounded-xl max-h-64 object-cover border border-slate-100 w-full" alt="Dokumentasi">
            @else
                <a href="{{ asset('storage/' . $logbook_harian->dokumentasi) }}" target="_blank"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span> Lihat Dokumen
                </a>
            @endif
        </div>
        @endif

        {{-- Catatan Dosen --}}
        @if($logbook_harian->catatan_dosen)
        <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6">
            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">comment</span> Catatan Dosen
            </p>
            <p class="text-sm text-amber-800 leading-relaxed">{{ $logbook_harian->catatan_dosen }}</p>
        </div>
        @endif

        {{-- Tombol Edit — hanya mahasiswa & status masih pending --}}
        @if(auth()->user()->role === 'mahasiswa' && $logbook_harian->status_verifikasi === 'pending')
        <div class="flex gap-3 pt-2">
            <a href="{{ route('logbook_harian.edit', $logbook_harian) }}"
               class="flex-1 py-3 text-center text-sm font-bold text-primary bg-teal-50 rounded-xl hover:bg-teal-100 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">edit</span> Edit Logbook
            </a>
        </div>
        @endif

        {{-- Verifikasi — hanya dosen/manager & status masih pending --}}
        @if(in_array(auth()->user()->role, ['dosen','manager']) && $logbook_harian->status_verifikasi === 'pending')
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm p-6"
             x-data="{ showForm: false, action: '' }">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Tindakan Verifikasi</p>
            <div x-show="!showForm" class="flex gap-3">
                <button @click="showForm = true; action = 'disetujui'"
                        class="flex-1 py-3 bg-teal-50 text-teal-700 font-bold text-sm rounded-xl hover:bg-teal-100 transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">check_circle</span> Setujui
                </button>
                <button @click="showForm = true; action = 'ditolak'"
                        class="flex-1 py-3 bg-red-50 text-red-600 font-bold text-sm rounded-xl hover:bg-red-100 transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">cancel</span> Tolak
                </button>
            </div>
            <div x-show="showForm" x-cloak>
                <form action="{{ route('logbook_harian.verifikasi', $logbook_harian) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status_verifikasi" :value="action">
                    <textarea name="catatan_dosen" rows="3" placeholder="Catatan (opsional)..."
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/30 mb-3"></textarea>
                    <div class="flex gap-3">
                        <button type="button" @click="showForm = false"
                                class="flex-1 py-3 bg-slate-100 text-slate-500 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:opacity-90 transition-all">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection