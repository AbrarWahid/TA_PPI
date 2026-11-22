@extends('layouts.app')
@section('title', 'Dashboard Dosen Pengampu')
@section('content')

@php
    $user = auth()->user();
    $roleName = $user->isKps() ? 'Koordinator Prodi' : 'Dosen Pengampu';
@endphp

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1">PORTAL AKADEMIK</p>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                Dashboard {{ $roleName }}
            </h1>
            <p class="text-xs text-[#6a6f7c] mt-1">
                Selamat datang, <span class="text-[#0b0b0c] font-medium">{{ $user->name }}</span>. Kelola verifikasi laporan &amp; penilaian mahasiswa.
            </p>
        </div>
        <a href="{{ route('penilaian.dosen.create') }}" class="ps-btn-primary w-fit">
            <span class="material-symbols-outlined text-[16px]">add</span>
            <span>Input Penilaian</span>
        </a>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Total Supervisi</p>
                <span class="material-symbols-outlined text-[18px] text-[#6a6f7c]">school</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $supervisiList->count() }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Total Mahasiswa</p>
                <span class="material-symbols-outlined text-[18px] text-[#6a6f7c]">group</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $totalMahasiswa }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Laporan Menunggu</p>
                <span class="material-symbols-outlined text-[18px] text-[#b45309]">pending_actions</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $laporanMenunggu }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Penilaian Selesai</p>
                <span class="material-symbols-outlined text-[18px] text-[#15803d]">edit_note</span>
            </div>
            <h3 class="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">{{ $penilaianSelesai }}</h3>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Quick Actions --}}
        <div class="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
            <h4 class="font-mono text-[11px] font-medium text-[#6a6f7c] mb-4 uppercase tracking-wider">Aksi Cepat</h4>
            <div class="space-y-2.5">
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group">
                    <div class="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[16px]">description</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-[#0b0b0c]">Verifikasi Laporan</p>
                        <p class="font-mono text-[10px] text-[#b45309] mt-0.5">{{ $laporanMenunggu }} menunggu review</p>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-[#6a6f7c]">chevron_right</span>
                </a>

                <a href="{{ route('penilaian.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group">
                    <div class="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[16px]">edit_note</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-[#0b0b0c]">Input Nilai</p>
                        <p class="text-[11px] text-[#6a6f7c] mt-0.5">Formulir penilaian mahasiswa</p>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-[#6a6f7c]">chevron_right</span>
                </a>

                <a href="{{ route('supervisi_mingguan.daftar_verifikasi') }}" class="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group">
                    <div class="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-[#0b0b0c]">Verifikasi Supervisi</p>
                        <p class="text-[11px] text-[#6a6f7c] mt-0.5">Review supervisi mingguan</p>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-[#6a6f7c]">chevron_right</span>
                </a>
            </div>
        </div>

        {{-- Supervisi Aktif --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                <h4 class="text-sm font-semibold text-[#0b0b0c]">Supervisi Aktif</h4>
                <span class="ps-badge">{{ $supervisiList->count() }} mahasiswa</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 w-12">#</th>
                            <th class="px-5 py-3">Mahasiswa</th>
                            <th class="px-5 py-3">Mata Kuliah</th>
                            <th class="px-5 py-3 text-center">Status Laporan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f4f4f5]">
                        @forelse($supervisiList as $idx => $supervisi)
                        <tr>
                            <td class="px-5 py-3.5 font-mono text-xs text-[#6a6f7c]">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-md bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] flex items-center justify-center font-mono text-xs font-medium shrink-0">
                                        {{ strtoupper(substr($supervisi->mahasiswa->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-xs text-[#0b0b0c]">{{ $supervisi->mahasiswa->nama ?? '-' }}</p>
                                        <p class="font-mono text-[11px] text-[#6a6f7c]">{{ $supervisi->mahasiswa->nim ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono inline-flex px-2.5 py-0.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded text-[11px]">
                                    {{ $supervisi->mataKuliah->nama_matkul ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $laporanMhs = $laporanTerbaru->firstWhere('mahasiswa_id', $supervisi->mahasiswa_id);
                                    $statusColor = 'bg-[#f4f4f5] border-[#e4e4e7] text-[#6a6f7c]';
                                    $statusLabel = 'Belum Ada';
                                    if ($laporanMhs) {
                                        $statusColor = match($laporanMhs->status_verifikasi) {
                                            'disetujui' => 'bg-emerald-50 border-emerald-200 text-[#15803d]',
                                            'ditolak'   => 'bg-red-50 border-red-200 text-[#b91c1c]',
                                            default     => 'bg-amber-50 border-amber-200 text-[#b45309]',
                                        };
                                        $statusLabel = match($laporanMhs->status_verifikasi) {
                                            'disetujui' => 'Disetujui',
                                            'ditolak'   => 'Ditolak',
                                            default     => 'Menunggu',
                                        };
                                    }
                                @endphp
                                <span class="font-mono inline-flex px-2.5 py-0.5 rounded-full border text-[10px] font-medium {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">school</span>
                                <p class="text-xs text-[#6a6f7c]">Belum ada mahasiswa yang disupervisi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
