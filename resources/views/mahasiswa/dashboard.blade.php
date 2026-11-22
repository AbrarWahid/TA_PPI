    @extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- TOP OVERVIEW CARD --}}
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-6 sm:p-7 flex flex-col lg:flex-row justify-between gap-6 shadow-surface">
        <div class="flex-1 flex flex-col justify-between">
            <div>
                <p class="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-[0.1em] mb-1">PORTAL MAHASISWA</p>
                <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">{{ auth()->user()->name }}</h1>
                <p class="font-mono text-xs text-[#6a6f7c] mt-1">NIM · {{ $mhs?->nim ?? '-' }}</p>
            </div>
            <div class="mt-6 pt-5 border-t border-[#f4f4f5]">
                <p class="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider mb-1.5">Nilai Akhir PBL</p>
                @if($nilaiAkhir !== null)
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl font-semibold font-mono text-[#0b0b0c] leading-none">{{ number_format($nilaiAkhir, 1) }}</span>
                        <span class="font-mono px-2.5 py-0.5 bg-[#013981] text-white font-medium rounded-full text-xs">Grade {{ $grade }}</span>
                    </div>
                @else
                    <p class="text-2xl font-semibold font-mono text-[#a1a1aa] leading-none">—</p>
                    <p class="text-xs text-[#6a6f7c] mt-1">Nilai akhir belum dipublikasikan</p>
                @endif
            </div>
        </div>

        <div class="hidden lg:block w-px bg-[#e4e4e7]"></div>

        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Stat Logbook --}}
            <div class="bg-[#fafafa] rounded-xl p-5 border border-[#e4e4e7]">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Logbook Harian</span>
                    <span class="material-symbols-outlined text-[#6a6f7c] text-[18px]">menu_book</span>
                </div>
                <p class="text-3xl font-semibold font-mono text-[#0b0b0c] mb-4">{{ $lbStats['total'] }}</p>
                <div class="grid grid-cols-3 gap-2 text-xs pt-3 border-t border-[#e4e4e7]">
                    <div>
                        <span class="font-mono font-semibold text-[#15803d] block">{{ $lbStats['disetujui'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Disetujui</span>
                    </div>
                    <div>
                        <span class="font-mono font-semibold text-[#b45309] block">{{ $lbStats['pending'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Menunggu</span>
                    </div>
                    <div>
                        <span class="font-mono font-semibold text-[#b91c1c] block">{{ $lbStats['ditolak'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Ditolak</span>
                    </div>
                </div>
            </div>

            {{-- Stat Supervisi --}}
            <div class="bg-[#fafafa] rounded-xl p-5 border border-[#e4e4e7]">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Supervisi Harian</span>
                    <span class="material-symbols-outlined text-[#6a6f7c] text-[18px]">fact_check</span>
                </div>
                <p class="text-3xl font-semibold font-mono text-[#0b0b0c] mb-4">{{ $svStats['total'] }}</p>
                <div class="grid grid-cols-3 gap-2 text-xs pt-3 border-t border-[#e4e4e7]">
                    <div>
                        <span class="font-mono font-semibold text-[#15803d] block">{{ $svStats['disetujui'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Disetujui</span>
                    </div>
                    <div>
                        <span class="font-mono font-semibold text-[#b45309] block">{{ $svStats['pending'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Menunggu</span>
                    </div>
                    <div>
                        <span class="font-mono font-semibold text-[#b91c1c] block">{{ $svStats['ditolak'] }}</span>
                        <span class="text-[#6a6f7c] text-[10px]">Ditolak</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        @foreach([
            [route('logbook_harian.create'),  'edit_note',  'Input Logbook',   'Catat aktivitas harian'],
            [route('supervisi_harian.index'), 'fact_check', 'Supervisi',       'Cek status supervisi'],
            [route('logbook_harian.index'),   'history',    'Riwayat Logbook', 'Lihat seluruh catatan'],
            [route('penilaian.index'),        'analytics',  'Nilai Saya',      'Lihat rekap penilaian'],
        ] as [$qaUrl, $qaIcon, $qaTitle, $qaSub])
        <a href="{{ $qaUrl }}" class="bg-white p-4 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors flex items-center gap-3.5 group">
            <div class="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                <span class="material-symbols-outlined text-[18px]">{{ $qaIcon }}</span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-[#0b0b0c] truncate">{{ $qaTitle }}</p>
                <p class="text-[11px] text-[#6a6f7c] truncate">{{ $qaSub }}</p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- PROYEK YANG DITUGASKAN --}}
        <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                <h4 class="text-sm font-semibold text-[#0b0b0c]">Proyek Saya</h4>
                <span class="font-mono text-xs text-[#6a6f7c]">{{ $proyekDitugaskan->count() }} proyek</span>
            </div>
            <div class="flex-1 divide-y divide-[#f4f4f5]">
                @forelse($proyekDitugaskan as $proyek)
                <div class="px-5 py-4 hover:bg-[#fafafa] transition-colors">
                    <div class="flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] shrink-0">
                            <span class="material-symbols-outlined text-[18px]">engineering</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-[#0b0b0c] text-xs truncate">{{ $proyek->judul_proyek }}</p>
                            <p class="text-xs text-[#6a6f7c] mt-0.5">Manager: {{ $proyek->manager->name ?? '-' }}</p>
                            <p class="font-mono text-[10px] text-[#6a6f7c] mt-0.5">
                                {{ $proyek->tanggal_mulai?->format('d M Y') }} — {{ $proyek->tanggal_selesai?->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                    Belum ada proyek yang ditugaskan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- SUPERVISI SAYA --}}
        <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                <h4 class="text-sm font-semibold text-[#0b0b0c]">Supervisi Mata Kuliah</h4>
                <span class="font-mono text-xs text-[#6a6f7c]">{{ $supervisiList->count() }} matkul</span>
            </div>
            <div class="flex-1 divide-y divide-[#f4f4f5]">
                @forelse($supervisiList as $s)
                <div class="px-5 py-4 hover:bg-[#fafafa] transition-colors">
                    <div class="flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] shrink-0">
                            <span class="material-symbols-outlined text-[18px]">menu_book</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-[#0b0b0c] text-xs">{{ $s->mataKuliah->nama_matkul ?? '-' }}</p>
                            <p class="font-mono text-[11px] text-[#6a6f7c] mt-0.5">{{ $s->mataKuliah->kode_matkul ?? '' }} · {{ $s->mataKuliah->sks ?? '' }} SKS</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-medium text-[#0b0b0c]">{{ $s->dosen->nama_dosen ?? '-' }}</p>
                            <p class="text-[11px] text-[#6a6f7c]">Dosen Pengampu</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                    Belum ada supervisi yang ditugaskan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- LOGBOOK HARIAN TERBARU --}}
    <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
            <h4 class="text-sm font-semibold text-[#0b0b0c]">Logbook Terbaru</h4>
            <a href="{{ route('logbook_harian.index') }}" class="font-mono text-xs text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors">Lihat semua →</a>
        </div>
        <div class="divide-y divide-[#f4f4f5]">
            @forelse($logbookTerbaru as $logbook)
            <div class="px-5 py-3.5 hover:bg-[#fafafa] transition-colors">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex flex-col items-center justify-center text-[#0b0b0c] shrink-0">
                        <span class="font-mono text-[9px] uppercase text-[#6a6f7c]">{{ isset($logbook->hari) ? substr($logbook->hari, 0, 3) : 'M'.$logbook->minggu_ke }}</span>
                        <span class="font-mono text-xs font-semibold leading-none mt-0.5">{{ \Carbon\Carbon::parse($logbook->tanggal)->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-[#0b0b0c] text-xs line-clamp-1">
                            {{ $logbook->aktivitas ?? 'Aktivitas tidak ada' }}
                        </p>
                        <p class="font-mono text-[11px] text-[#6a6f7c] mt-0.5">
                            Minggu ke-{{ $logbook->minggu_ke }} · {{ \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        @php
                            $statusColor = match($logbook->status_verifikasi) {
                                'disetujui' => 'bg-emerald-50 border-emerald-200 text-[#15803d]',
                                'ditolak'   => 'bg-red-50 border-red-200 text-[#b91c1c]',
                                default     => 'bg-amber-50 border-amber-200 text-[#b45309]',
                            };
                            $statusLabel = match($logbook->status_verifikasi) {
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                default     => 'Menunggu',
                            };
                        @endphp
                        <span class="font-mono text-[10px] font-medium px-2.5 py-0.5 rounded-full border {{ $statusColor }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                Belum ada catatan logbook.
                <a href="{{ route('logbook_harian.create') }}" class="text-[#0b0b0c] font-medium hover:underline ml-1">Buat sekarang →</a>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
