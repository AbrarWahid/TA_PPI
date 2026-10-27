@extends('layouts.app')
@section('title', 'Supervisi Harian')

@section('content')
@php $user = auth()->user(); @endphp

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold">
    <span class="material-symbols-outlined text-lg">check_circle</span>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3 rounded-2xl text-sm font-bold">
    <span class="material-symbols-outlined text-lg">error</span>
    {{ session('error') }}
</div>
@endif

{{-- HEADING --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Supervisi <span class="text-[#2dce89]">Harian</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Catatan aktivitas supervisi per hari</p>
    </div>
    @if($user->isMahasiswa())
    <a href="{{ route('supervisi_harian.create') }}"
       class="flex items-center gap-2 px-5 py-3 bg-[#004d4d] text-white rounded-2xl font-black text-sm hover:bg-[#003d3d] transition-all shadow-lg">
        <span class="material-symbols-outlined text-lg">add</span>
        Tambah Entri
    </a>
    @endif
</div>

{{-- ALERT: Minggu yang siap direkap --}}
@if($user->isMahasiswa() && isset($mingguBelumRekap) && count($mingguBelumRekap) > 0)
<div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
    <span class="material-symbols-outlined text-amber-500 mt-0.5">info</span>
    <div>
        <p class="text-sm font-black text-amber-700">Ada minggu yang siap direkap!</p>
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach($mingguBelumRekap as $item)
            @php $sm = $item['supervisiMatkul']; @endphp
            <a href="{{ route('supervisi_mingguan.index') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-xl text-xs font-black hover:bg-amber-200 transition">
                <span class="material-symbols-outlined text-xs">summarize</span>
                Minggu {{ $item['minggu_ke'] }}
                @if($sm)
                — {{ $sm->mataKuliah->nama_matkul ?? 'Matkul' }}
                @endif
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- TABEL DATA --}}
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#004d4d] text-white text-xs">
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Minggu</th>
                    @if($user->isMahasiswa())
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Status Rekap</th>
                    @endif
                    @if(!$user->isMahasiswa())
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Mahasiswa</th>
                    @endif
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Mata Kuliah</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Dosen</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Jam</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Aktivitas</th>
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Dokumen</th>
                    @if($user->isMahasiswa())
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($supervisiList as $item)
                @php
                    $namaMatkul = $item->supervisiMatkul?->mataKuliah?->nama_matkul ?? '-';
                    $namaDosen  = $item->supervisiMatkul?->dosen?->nama_dosen
                                  ?? $item->supervisiMatkul?->dosen?->user?->name
                                  ?? '-';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- Tanggal --}}
                    <td class="px-5 py-4">
                        <p class="font-black text-[#004d4d]">{{ $item->hari }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </p>
                    </td>

                    {{-- Minggu --}}
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 bg-[#004d4d]/10 text-[#004d4d] rounded-xl text-xs font-black">
                            Minggu {{ $item->minggu_ke }}
                        </span>
                    </td>

                    {{-- Status Rekap Mingguan — hanya mahasiswa --}}
                    @if($user->isMahasiswa())
                    @php
                        $keyRekap = $item->supervisi_matkul_id . '-' . $item->minggu_ke;
                        $stRekap  = $statusRekap[$keyRekap] ?? null;
                        $rekapCfg = match($stRekap) {
                            'disetujui' => ['cls' => 'bg-emerald-50 text-emerald-600 border border-emerald-200', 'icon' => 'check_circle',          'label' => 'Disetujui'],
                            'diajukan'  => ['cls' => 'bg-amber-50 text-amber-600 border border-amber-200',       'icon' => 'pending',                'label' => 'Diajukan'],
                            'ditolak'   => ['cls' => 'bg-red-50 text-red-500 border border-red-200',             'icon' => 'cancel',                 'label' => 'Ditolak'],
                            default     => ['cls' => 'bg-gray-100 text-gray-400 border border-gray-200',         'icon' => 'radio_button_unchecked', 'label' => 'Belum Rekap'],
                        };
                    @endphp
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black {{ $rekapCfg['cls'] }}">
                            <span class="material-symbols-outlined text-xs">{{ $rekapCfg['icon'] }}</span>
                            {{ $rekapCfg['label'] }}
                        </span>
                    </td>
                    @endif

                    {{-- Mahasiswa (bukan role mahasiswa) --}}
                    @if(!$user->isMahasiswa())
                    <td class="px-5 py-4">
                        <p class="font-bold text-gray-800">{{ $item->mahasiswa?->nama_mahasiswa ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $item->mahasiswa?->nim ?? '-' }}</p>
                    </td>
                    @endif

                    {{-- Mata Kuliah --}}
                    <td class="px-5 py-4">
                        <p class="font-bold text-gray-700 text-xs max-w-[150px] truncate" title="{{ $namaMatkul }}">
                            {{ $namaMatkul }}
                        </p>
                    </td>

                    {{-- Dosen --}}
                    <td class="px-5 py-4">
                        <p class="text-gray-600 text-xs max-w-[120px] truncate" title="{{ $namaDosen }}">
                            {{ $namaDosen }}
                        </p>
                    </td>

                    {{-- Jam --}}
                    <td class="px-5 py-4 text-gray-600 text-xs whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} –
                        {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                    </td>

                    {{-- Aktivitas --}}
                    <td class="px-5 py-4">
                        <p class="text-gray-700 text-xs max-w-[200px] line-clamp-2">{{ $item->aktivitas }}</p>
                        @if($item->kendala)
                        <p class="text-xs text-red-400 mt-0.5 line-clamp-1 italic">⚠ {{ $item->kendala }}</p>
                        @endif
                    </td>

                    {{-- Dokumentasi --}}
                    <td class="px-5 py-4 text-center">
                        @if($item->dokumentasi)
                        <a href="{{ Storage::url($item->dokumentasi) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-100 transition">
                            <span class="material-symbols-outlined text-xs">attach_file</span> Lihat
                        </a>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Aksi — hanya mahasiswa --}}
                    @if($user->isMahasiswa())
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('supervisi_harian.edit', $item) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                               title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('supervisi_harian.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Hapus entri supervisi {{ $item->hari }}, {{ \Carbon\Carbon::parse($item->tanggal)->format('d M') }} ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition"
                                        title="Hapus">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-16 text-center text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2 block">event_note</span>
                        <p class="font-bold">Belum ada entri supervisi harian</p>
                        @if($user->isMahasiswa())
                        <p class="text-xs mt-1">Klik "Tambah Entri" untuk mulai mencatat</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection