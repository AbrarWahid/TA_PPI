@extends('layouts.app')
@section('title', 'Rekap Mingguan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('logbook_harian.index') }}"
           class="p-2.5 text-gray-400 hover:text-primary hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-on-surface">Rekap Minggu ke-{{ $mingguKe }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Gabungan aktivitas harian menjadi laporan mingguan</p>
        </div>
    </div>

    {{-- Navigasi minggu --}}
    <div class="flex items-center gap-2 flex-wrap">
        @for($i = 1; $i <= max($totalMinggu, $mingguKe); $i++)
        <a href="{{ route('logbook_harian.rekap', ['minggu_ke' => $i]) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all
               {{ $i == $mingguKe ? 'bg-primary text-white shadow' : 'bg-white border border-slate-200 text-slate-500 hover:border-primary hover:text-primary' }}">
            Minggu {{ $i }}
        </a>
        @endfor
    </div>

    @if($logbook->isEmpty())
    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm px-6 py-16 text-center">
        <span class="material-symbols-outlined text-4xl text-slate-200">inbox</span>
        <p class="text-slate-300 text-sm mt-3">Belum ada logbook untuk minggu ini.</p>
    </div>
    @else

    {{-- Rekap card per hari --}}
    @foreach($logbook as $entry)
    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-primary font-bold text-xs">
                    {{ substr($entry->hari, 0, 3) }}
                </div>
                <div>
                    <p class="font-bold text-on-surface text-sm">{{ $entry->hari }}</p>
                    <p class="text-xs text-slate-400">{{ $entry->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            <span class="text-[10px] font-bold px-3 py-1 rounded-full {{ $entry->getStatusBadgeColor() }}">
                {{ $entry->getStatusLabel() }}
            </span>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Aktivitas</p>
            <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line">{{ $entry->aktivitas }}</p>

            @if($entry->dokumentasi)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Dokumentasi</p>
                @php $ext = pathinfo($entry->dokumentasi, PATHINFO_EXTENSION); @endphp
                @if(in_array($ext, ['jpg','jpeg','png']))
                <img src="{{ asset('storage/' . $entry->dokumentasi) }}"
                     class="rounded-xl max-h-48 object-cover border border-slate-100" alt="Dokumentasi">
                @else
                <a href="{{ asset('storage/' . $entry->dokumentasi) }}" target="_blank"
                   class="inline-flex items-center gap-2 text-xs font-semibold text-primary hover:underline">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span> Lihat Dokumen
                </a>
                @endif
            </div>
            @endif

            @if($entry->catatan_dosen)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan Dosen</p>
                <p class="text-sm text-slate-600 bg-amber-50 rounded-xl px-4 py-3">{{ $entry->catatan_dosen }}</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    {{-- Summary rekap --}}
    <div class="bg-gradient-to-br from-primary to-emerald-800 rounded-2xl p-6 text-white">
        <p class="text-teal-200/70 text-xs font-bold uppercase tracking-widest mb-3">Ringkasan Minggu ke-{{ $mingguKe }}</p>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-3xl font-extrabold">{{ $logbook->count() }}</p>
                <p class="text-teal-100/60 text-[10px] uppercase font-bold mt-1">Hari Tercatat</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold">{{ $logbook->where('status_verifikasi', 'disetujui')->count() }}</p>
                <p class="text-teal-100/60 text-[10px] uppercase font-bold mt-1">Disetujui</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold">{{ $logbook->where('status_verifikasi', 'pending')->count() }}</p>
                <p class="text-teal-100/60 text-[10px] uppercase font-bold mt-1">Menunggu</p>
            </div>
        </div>
    </div>

    @endif
</div>
@endsection