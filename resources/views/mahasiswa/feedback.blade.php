@extends('layouts.app')

@section('title', 'Feedback Hub')

@section('content')
@php $tipeActive = request('tipe', 'semua'); @endphp

{{-- HEADING --}}
<div class="mb-8">
    <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
        Feedback <span class="text-[#2dce89]">Hub</span>
    </h1>
    <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Feedback dari dosen dan manager untuk perkembanganmu</p>
</div>

@if(!$mahasiswa)
<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">person_off</span>
    <p class="text-sm text-gray-400 font-medium">Data mahasiswa tidak ditemukan.</p>
</div>

@elseif($totalFeedback === 0)
<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">mark_chat_read</span>
    <h3 class="text-base font-black text-[#004d4d] mb-2">Belum ada feedback</h3>
    <p class="text-sm text-slate-400">Feedback akan muncul setelah penilaian, verifikasi logbook, atau laporan selesai.</p>
</div>

@else

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#004d4d] rounded-[1.5rem] shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-teal-100/60">Total</p>
        <h3 class="text-5xl font-black italic text-white mt-1">{{ $totalFeedback }}</h3>
    </div>
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Penilaian</p>
        <h3 class="text-5xl font-black italic text-[#004d4d] mt-1">{{ $feedbackPerTipe['penilaian'] }}</h3>
    </div>
    <div class="bg-amber-50 rounded-[1.5rem] border border-amber-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-amber-400">Logbook</p>
        <h3 class="text-5xl font-black italic text-amber-600 mt-1">{{ $feedbackPerTipe['logbook'] }}</h3>
    </div>
    <div class="bg-rose-50 rounded-[1.5rem] border border-rose-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-rose-400">Laporan</p>
        <h3 class="text-5xl font-black italic text-rose-500 mt-1">{{ $feedbackPerTipe['laporan'] }}</h3>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="flex gap-2 mb-6 flex-wrap">
    @foreach([
        ['key' => 'semua',     'label' => 'Semua',     'count' => $totalFeedback],
        ['key' => 'penilaian', 'label' => 'Penilaian', 'count' => $feedbackPerTipe['penilaian']],
        ['key' => 'logbook',   'label' => 'Logbook',   'count' => $feedbackPerTipe['logbook']],
        ['key' => 'laporan',   'label' => 'Laporan',   'count' => $feedbackPerTipe['laporan']],
    ] as $tab)
    <a href="?tipe={{ $tab['key'] }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm font-black border transition-all
               {{ $tipeActive === $tab['key']
                   ? 'bg-[#004d4d] text-[#7fffd4] border-[#004d4d]'
                   : 'bg-white text-slate-500 border-gray-100 hover:border-teal-200 hover:text-[#004d4d]' }}">
        {{ $tab['label'] }}
        <span class="text-[10px] px-2 py-0.5 rounded-full font-black
                     {{ $tipeActive === $tab['key'] ? 'bg-white/20 text-[#7fffd4]' : 'bg-gray-100 text-gray-400' }}">
            {{ $tab['count'] }}
        </span>
    </a>
    @endforeach
</div>

{{-- FEEDBACK LIST --}}
<div class="space-y-3">
    @foreach($feedbackList as $fb)
        @if($tipeActive !== 'semua' && $fb['tipe'] !== $tipeActive)
            @continue
        @endif

        @php
        $cfg = match($fb['color']) {
            'secondary' => ['border' => 'border-l-emerald-400', 'icon_bg' => 'bg-emerald-50',  'icon_color' => 'text-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-700'],
            'amber'     => ['border' => 'border-l-amber-400',   'icon_bg' => 'bg-amber-50',     'icon_color' => 'text-amber-600',   'badge' => 'bg-amber-50 text-amber-700'],
            'rose'      => ['border' => 'border-l-rose-400',    'icon_bg' => 'bg-rose-50',      'icon_color' => 'text-rose-500',    'badge' => 'bg-rose-50 text-rose-600'],
            default     => ['border' => 'border-l-[#004d4d]',   'icon_bg' => 'bg-teal-50',      'icon_color' => 'text-[#004d4d]',   'badge' => 'bg-teal-50 text-[#004d4d]'],
        };
        @endphp

        <div class="bg-white border border-gray-100 border-l-4 {{ $cfg['border'] }} rounded-[1.5rem] p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl {{ $cfg['icon_bg'] }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl {{ $cfg['icon_color'] }}" style="font-variation-settings:'FILL' 1">{{ $fb['icon'] }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-sm font-black text-[#004d4d]">{{ $fb['sumber'] }}</span>
                        <span class="text-[10px] px-2.5 py-1 rounded-full font-black {{ $cfg['badge'] }} uppercase tracking-wide">
                            {{ $fb['badge'] }}
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium mb-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">folder</span>
                        {{ $fb['konteks'] }}
                    </p>
                    <div class="bg-gray-50 rounded-2xl px-4 py-3 text-sm text-slate-600 leading-relaxed italic border-l-2 border-gray-200">
                        "{{ $fb['pesan'] }}"
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-xs text-slate-400 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($fb['tanggal'])->locale('id')->diffForHumans() }}
                    </p>
                    <p class="text-[10px] text-gray-300 font-medium mt-0.5">
                        {{ \Carbon\Carbon::parse($fb['tanggal'])->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    @if($tipeActive !== 'semua' && $feedbackPerTipe[$tipeActive] === 0)
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-16 text-center">
        <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">filter_list_off</span>
        <p class="text-sm text-gray-400 font-medium">Tidak ada feedback dari kategori <strong class="text-[#004d4d]">{{ ucfirst($tipeActive) }}</strong>.</p>
    </div>
    @endif
</div>

@endif

@endsection