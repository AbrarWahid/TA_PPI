@extends('layouts.app')
@section('title', 'Logbook Harian')

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
            Logbook <span class="text-[#2dce89]">Harian</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Riwayat aktivitas harian proyek PBL</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('logbook_harian.rekap') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-[#004d4d] rounded-2xl font-black text-sm hover:bg-gray-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-lg">summarize</span>
            Lihat Rekap
        </a>
        <a href="{{ route('logbook_mingguan.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-[#004d4d] rounded-2xl font-black text-sm hover:bg-gray-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
            Logbook Mingguan
        </a>
        <a href="{{ route('logbook_harian.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-[#004d4d] text-white rounded-2xl font-black text-sm hover:bg-[#003d3d] transition-all shadow-lg">
            <span class="material-symbols-outlined text-lg">add</span>
            Input Hari Ini
        </a>
    </div>
</div>

{{-- TABEL DATA --}}
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#004d4d] text-white text-xs">
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Minggu</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Proyek</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Manager</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Jam</th>
                    <th class="px-5 py-4 text-left font-black italic uppercase tracking-wide">Aktivitas</th>
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Status</th>
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Dokumen</th>
                    <th class="px-5 py-4 text-center font-black italic uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logbook->flatten() as $entry)
                @php
                    $namaProyek  = $entry->proyek->judul_proyek ?? '-';
                    $namaManager = $entry->proyek->manager->name ?? '-';
                    $statusColor = match($entry->status_verifikasi) {
                        'disetujui' => 'bg-emerald-50 text-emerald-700',
                        'ditolak'   => 'bg-red-50 text-red-600',
                        default     => 'bg-amber-50 text-amber-700',
                    };
                    $statusLabel = match($entry->status_verifikasi) {
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        default     => 'Pending',
                    };
                    $jamTampil = '';
                    if ($entry->jam_masuk && $entry->jam_keluar) {
                        $masuk  = \Carbon\Carbon::parse($entry->jam_masuk);
                        $keluar = \Carbon\Carbon::parse($entry->jam_keluar);
                        $menit  = $masuk->diffInMinutes($keluar);
                        $jam    = intdiv($menit, 60);
                        $sisa   = $menit % 60;
                        $durasi = ($jam > 0 ? $jam . ' jam ' : '') . ($sisa > 0 ? $sisa . ' mnt' : '');
                        $jamTampil = $masuk->format('H:i') . ' - ' . $keluar->format('H:i');
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- Tanggal --}}
                    <td class="px-5 py-4">
                        <p class="font-black text-[#004d4d]">{{ $entry->hari }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($entry->tanggal)->format('d M Y') }}
                        </p>
                    </td>

                    {{-- Minggu --}}
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 bg-[#004d4d]/10 text-[#004d4d] rounded-xl text-xs font-black">
                            Minggu {{ $entry->minggu_ke }}
                        </span>
                    </td>

                    {{-- Proyek --}}
                    <td class="px-5 py-4">
                        <p class="font-bold text-gray-700 text-xs max-w-[150px] truncate" title="{{ $namaProyek }}">
                            {{ $namaProyek }}
                        </p>
                    </td>

                    {{-- Manager --}}
                    <td class="px-5 py-4">
                        <p class="text-gray-600 text-xs max-w-[120px] truncate" title="{{ $namaManager }}">
                            {{ $namaManager }}
                        </p>
                    </td>

                    {{-- Jam --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        @if($entry->jam_masuk && $entry->jam_keluar)
                            <p class="font-bold text-gray-700 text-xs">{{ $jamTampil }}</p>
                            <p class="text-xs text-gray-400">({{ $durasi }})</p>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Aktivitas + Kendala --}}
                    <td class="px-5 py-4">
                        <p class="text-gray-700 text-xs max-w-[200px] line-clamp-2">{{ $entry->aktivitas }}</p>
                        @if($entry->kendala)
                        <p class="text-xs text-red-400 mt-1 line-clamp-1">
                            <span class="material-symbols-outlined text-xs align-middle">warning</span>
                            {{ $entry->kendala }}
                        </p>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-1 rounded-xl text-xs font-black {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>

                    {{-- Dokumentasi --}}
                    <td class="px-5 py-4 text-center">
                        @if($entry->dokumentasi)
                        <a href="{{ asset('storage/' . $entry->dokumentasi) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-100 transition">
                            <span class="material-symbols-outlined text-xs">attach_file</span> Lihat
                        </a>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('logbook_harian.show', $entry) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-xl bg-teal-50 text-[#004d4d] hover:bg-teal-100 transition"
                               title="Detail">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                            @if($entry->status_verifikasi === 'pending')
                            <a href="{{ route('logbook_harian.edit', $entry) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                               title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('logbook_harian.destroy', $entry) }}" method="POST"
                                  onsubmit="return confirm('Hapus logbook {{ $entry->hari }}, {{ \Carbon\Carbon::parse($entry->tanggal)->format('d M Y') }} ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition"
                                        title="Hapus">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2 block">edit_note</span>
                        <p class="font-bold">Belum ada logbook harian</p>
                        <p class="text-xs mt-1">Klik "Input Hari Ini" untuk mulai mencatat</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection