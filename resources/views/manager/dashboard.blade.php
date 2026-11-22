@extends('layouts.app')
@section('title', 'Dashboard Manager Proyek')
@section('content')

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1">MANAJEMEN PROYEK PBL</p>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                Dashboard Manager Proyek
            </h1>
            <p class="text-xs text-[#6a6f7c] mt-1">
                Selamat datang, <span class="font-medium text-[#0b0b0c]">{{ auth()->user()->name }}</span>. Kelola proyek dan verifikasi logbook tim.
            </p>
        </div>
        <a href="{{ route('pengajuan_proyek.create') }}" class="ps-btn-primary w-fit">
            <span class="material-symbols-outlined text-[16px]">add</span>
            <span>Ajukan Proyek</span>
        </a>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Total Proyek</p>
                <span class="material-symbols-outlined text-[18px] text-[#6a6f7c]">folder_open</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $totalProyek }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Disetujui</p>
                <span class="material-symbols-outlined text-[18px] text-[#15803d]">check_circle</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $proyekDisetujui }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Menunggu</p>
                <span class="material-symbols-outlined text-[18px] text-[#b45309]">pending</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $proyekPending }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Total Mahasiswa</p>
                <span class="material-symbols-outlined text-[18px] text-[#6a6f7c]">group</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $totalMahasiswa }}</h3>
        </div>
    </section>

    {{-- SHORTCUT VERIFIKASI LOGBOOK --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('logbook_mingguan.daftar_verifikasi') }}"
           class="flex items-center gap-4 bg-white p-5 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors group">
            <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                <span class="material-symbols-outlined text-[20px]">rate_review</span>
            </div>
            <div>
                <p class="font-semibold text-sm text-[#0b0b0c]">Verifikasi Logbook Mingguan</p>
                <p class="text-xs text-[#6a6f7c] mt-0.5">Review &amp; ACC rekap mingguan mahasiswa</p>
            </div>
            <span class="material-symbols-outlined text-[#6a6f7c] group-hover:text-[#0b0b0c] ml-auto transition-colors">chevron_right</span>
        </a>

        <a href="{{ route('logbook_harian.rekap') }}"
           class="flex items-center gap-4 bg-white p-5 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors group">
            <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
            </div>
            <div>
                <p class="font-semibold text-sm text-[#0b0b0c]">Rekap Logbook Harian</p>
                <p class="text-xs text-[#6a6f7c] mt-0.5">Pantau aktivitas harian mahasiswa per minggu</p>
            </div>
            <span class="material-symbols-outlined text-[#6a6f7c] group-hover:text-[#0b0b0c] ml-auto transition-colors">chevron_right</span>
        </a>
    </section>

    {{-- PROYEK LIST --}}
    <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
            <h4 class="text-sm font-semibold text-[#0b0b0c]">Daftar Proyek Saya</h4>
            <a href="{{ route('pengajuan_proyek.index') }}" class="font-mono text-xs text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors">Lihat semua →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3 w-12">#</th>
                        <th class="px-5 py-3">Judul Proyek</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3 text-center">Tim</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                @forelse($proyekList as $idx => $proyek)
                <tbody x-data="{ open: false }" class="divide-y divide-[#f4f4f5] border-b border-[#f4f4f5]">
                    <tr>
                        <td class="px-5 py-4 font-mono text-xs text-[#6a6f7c]">{{ $idx + 1 }}</td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#0b0b0c] text-xs">{{ $proyek->judul_proyek }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-mono text-xs text-[#0b0b0c]">{{ $proyek->tanggal_mulai?->format('d M Y') }}</p>
                            <p class="font-mono text-[10px] text-[#6a6f7c]">s.d. {{ $proyek->tanggal_selesai?->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($proyek->mahasiswa->count() > 0)
                            <button @click="open = !open" class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#f4f4f5] hover:bg-[#e4e4e7] border border-[#e4e4e7] text-[#0b0b0c] rounded-md font-mono text-xs transition-colors">
                                {{ $proyek->mahasiswa->count() }} Org
                                <span class="material-symbols-outlined text-[14px]" x-text="open ? 'expand_less' : 'expand_more'">expand_more</span>
                            </button>
                            @else
                            <span class="text-xs text-[#a1a1aa]">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="font-mono inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-medium {{ $proyek->getStatusBadgeColor() }}">
                                {{ $proyek->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('pengajuan_proyek.show', $proyek) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] hover:bg-[#013981] hover:text-white hover:border-[#013981] transition-colors" title="Detail">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @if($proyek->mahasiswa->count() > 0)
                    <tr x-show="open" x-transition.opacity class="bg-[#fafafa]">
                        <td colspan="6" class="px-5 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                @foreach($proyek->mahasiswa as $mhs)
                                <div class="flex items-center gap-2.5 bg-white border border-[#e4e4e7] rounded-lg p-2.5">
                                    <div class="w-7 h-7 rounded-md bg-[#013981] flex items-center justify-center text-white font-mono text-xs font-medium shrink-0">
                                        {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-[#0b0b0c] truncate">{{ $mhs->nama }}</p>
                                        <p class="font-mono text-[10px] text-[#6a6f7c]">{{ $mhs->nim }} · {{ $mhs->pivot->prodi ?? '-' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">work_off</span>
                            <p class="text-xs text-[#6a6f7c]">Belum ada proyek yang diajukan.</p>
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>
    </div>

</div>
@endsection
