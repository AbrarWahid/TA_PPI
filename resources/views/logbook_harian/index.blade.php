@extends('layouts.app')
@section('title', 'Logbook Magang Dalam Kampus')

@section('content')
@php
    $user = auth()->user();
    $allEntries = $logbook->flatten();
    $totalHari = $allEntries->count();
    $totalDisetujui = $allEntries->where('status_verifikasi', 'disetujui')->count();
    $totalPending = $allEntries->where('status_verifikasi', 'pending')->count();
    $totalMinggu = $logbook->keys()->count();
@endphp

<div class="max-w-[1400px] mx-auto space-y-6">

    {{-- UNIFIED PAGE HEADER --}}
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#013981]"></span>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">MAGANG DALAM KAMPUS · LOGBOOK</span>
            </div>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                Logbook Harian &amp; Mingguan
            </h1>
            <p class="text-xs text-[#6a6f7c] mt-1">
                Catat aktivitas harian proyek kampus, jam kerja, dan ajukan rekap mingguan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('logbook_harian.rekap') }}" class="ps-btn-secondary">
                <span class="material-symbols-outlined text-[16px]">summarize</span>
                <span>Lihat Rekap</span>
            </a>
            <a href="{{ route('logbook_mingguan.index') }}" class="ps-btn-secondary">
                <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                <span>Logbook Mingguan</span>
            </a>
            <a href="{{ route('logbook_harian.create') }}" class="ps-btn-primary">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Input Hari Ini</span>
            </a>
        </div>
    </div>

    {{-- UNIFIED STAT CARDS (Matching Magang Luar Kampus) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-[#e4e4e7] rounded-xl p-5 shadow-surface">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Total Entri Harian</p>
            <p class="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">{{ $totalHari }} <span class="text-xs font-normal text-[#6a6f7c]">Hari</span></p>
        </div>
        <div class="bg-white border border-[#e4e4e7] rounded-xl p-5 shadow-surface">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Minggu Tercatat</p>
            <p class="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">{{ $totalMinggu }} <span class="text-xs font-normal text-[#6a6f7c]">Minggu</span></p>
        </div>
        <div class="bg-white border border-[#e4e4e7] rounded-xl p-5 shadow-surface">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Disetujui</p>
            <p class="text-3xl font-semibold font-mono text-[#15803d] mt-2">{{ $totalDisetujui }}</p>
        </div>
        <div class="bg-white border border-[#e4e4e7] rounded-xl p-5 shadow-surface">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Menunggu Verifikasi</p>
            <p class="text-3xl font-semibold font-mono text-[#b45309] mt-2">{{ $totalPending }}</p>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-xl shadow-surface border border-[#e4e4e7] overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[#0b0b0c]">Riwayat Aktivitas Harian</h3>
            <span class="ps-badge">{{ $totalHari }} catatan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Minggu</th>
                        <th class="px-5 py-3.5">Proyek</th>
                        <th class="px-5 py-3.5">Manager</th>
                        <th class="px-5 py-3.5">Jam Kerja</th>
                        <th class="px-5 py-3.5">Aktivitas</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Dokumen</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f4f4f5]">
                    @forelse($allEntries as $entry)
                    @php
                        $namaProyek  = $entry->proyek->judul_proyek ?? '-';
                        $namaManager = $entry->proyek->manager->name ?? '-';
                        $statusColor = match($entry->status_verifikasi) {
                            'disetujui' => 'bg-emerald-50 border-emerald-200 text-[#15803d]',
                            'ditolak'   => 'bg-red-50 border-red-200 text-[#b91c1c]',
                            default     => 'bg-amber-50 border-amber-200 text-[#b45309]',
                        };
                        $statusLabel = match($entry->status_verifikasi) {
                            'disetujui' => 'Disetujui',
                            'ditolak'   => 'Ditolak',
                            default     => 'Pending',
                        };
                        $jamTampil = '';
                        $durasi = '';
                        if ($entry->jam_masuk && $entry->jam_keluar) {
                            $masuk  = \Carbon\Carbon::parse($entry->jam_masuk);
                            $keluar = \Carbon\Carbon::parse($entry->jam_keluar);
                            $menit  = $masuk->diffInMinutes($keluar);
                            $jam    = intdiv($menit, 60);
                            $sisa   = $menit % 60;
                            $durasi = ($jam > 0 ? $jam . ' jam ' : '') . ($sisa > 0 ? $sisa . ' mnt' : '');
                            $jamTampil = $masuk->format('H:i') . ' – ' . $keluar->format('H:i');
                        }
                    @endphp
                    <tr>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-[#0b0b0c]">{{ $entry->hari }}</p>
                            <p class="font-mono text-[11px] text-[#6a6f7c]">
                                {{ \Carbon\Carbon::parse($entry->tanggal)->format('d M Y') }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono px-2.5 py-0.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded text-[11px] font-medium">
                                Minggu {{ $entry->minggu_ke }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-[#0b0b0c] max-w-[160px] truncate" title="{{ $namaProyek }}">
                                {{ $namaProyek }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-[#6a6f7c] max-w-[130px] truncate" title="{{ $namaManager }}">
                                {{ $namaManager }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($entry->jam_masuk && $entry->jam_keluar)
                                <p class="font-mono font-medium text-[#0b0b0c]">{{ $jamTampil }}</p>
                                <p class="font-mono text-[10px] text-[#6a6f7c]">{{ $durasi }}</p>
                            @else
                                <span class="text-[#a1a1aa]">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-[#0b0b0c] max-w-[220px] line-clamp-2">{{ $entry->aktivitas }}</p>
                            @if($entry->kendala)
                            <p class="text-[11px] text-[#b91c1c] mt-0.5 line-clamp-1">
                                Kendala: {{ $entry->kendala }}
                            </p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-mono px-2.5 py-0.5 rounded-full border text-[10px] font-medium {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($entry->dokumentasi)
                            <a href="{{ asset('storage/' . $entry->dokumentasi) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded-md text-[11px] font-medium hover:bg-[#e4e4e7] transition-colors">
                                <span class="material-symbols-outlined text-[13px]">attach_file</span> Lihat
                            </a>
                            @else
                            <span class="text-[#a1a1aa]">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('logbook_harian.show', $entry) }}"
                                   class="w-7 h-7 flex items-center justify-center rounded-md bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] hover:bg-[#013981] hover:text-white hover:border-[#013981] transition-colors"
                                   title="Detail">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                </a>
                                @if($entry->status_verifikasi === 'pending')
                                <a href="{{ route('logbook_harian.edit', $entry) }}"
                                   class="w-7 h-7 flex items-center justify-center rounded-md bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] hover:bg-[#f4f4f5] transition-colors"
                                   title="Edit">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                </a>
                                <form action="{{ route('logbook_harian.destroy', $entry) }}" method="POST"
                                      onsubmit="return confirm('Hapus logbook {{ $entry->hari }}, {{ \Carbon\Carbon::parse($entry->tanggal)->format('d M Y') }} ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-md bg-red-50 border border-red-200 text-[#b91c1c] hover:bg-red-100 transition-colors"
                                            title="Hapus">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-14 text-center text-[#6a6f7c]">
                            <span class="material-symbols-outlined text-3xl text-[#d4d4d8] mb-2 block">edit_note</span>
                            <p class="font-medium text-xs text-[#0b0b0c]">Belum ada catatan logbook harian</p>
                            <p class="text-[11px] mt-0.5">Klik tombol "Input Hari Ini" untuk mulai mencatat aktivitas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
