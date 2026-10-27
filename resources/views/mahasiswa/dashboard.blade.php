@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HERO CARD --}}
    <div class="bg-gradient-to-br from-primary via-emerald-800 to-secondary rounded-3xl p-8 flex flex-col lg:flex-row justify-between gap-8 shadow-xl">
        {{-- Bagian Kiri: Profil & Nilai --}}
        <div class="flex-1 flex flex-col justify-between">
            <div>
                <p class="text-teal-200/70 text-xs font-bold uppercase tracking-widest mb-1">Selamat Datang</p>
                <h2 class="text-3xl font-extrabold text-white">{{ auth()->user()->name }}</h2>
                <p class="text-teal-100/60 text-sm mt-1">NIM: {{ $mhs?->nim ?? '-' }}</p>
            </div>
            <div class="mt-8">
                <p class="text-teal-200/60 text-[10px] uppercase font-bold mb-2">Nilai Akhir PBL</p>
                @if($nilaiAkhir !== null)
                    <div class="flex items-end gap-3">
                        <span class="text-5xl font-extrabold text-white leading-none">{{ number_format($nilaiAkhir, 1) }}</span>
                        <span class="px-3 py-1 bg-white text-emerald-700 font-black rounded-lg text-sm mb-1 shadow-sm">Grade {{ $grade }}</span>
                    </div>
                @else
                    <p class="text-3xl font-extrabold text-white/50 leading-none mt-1">—</p>
                    <p class="text-teal-300/60 text-[10px] mt-1">Belum dinilai</p>
                @endif
            </div>
        </div>

        {{-- Garis Pemisah (Hanya di layar besar) --}}
        <div class="hidden lg:block w-px bg-white/20"></div>

        {{-- Bagian Kanan: Statistik Detail Logbook & Supervisi --}}
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- Stat Logbook --}}
            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-teal-200 text-lg">menu_book</span>
                    <h3 class="font-bold text-white text-sm tracking-wide">Logbook Harian</h3>
                </div>
                <p class="text-4xl font-black text-white mb-4">{{ $lbStats['total'] }}</p>
                <div class="flex justify-between text-xs font-semibold pt-3 border-t border-white/10">
                    <div class="text-center">
                        <span class="text-emerald-300 block mb-0.5">{{ $lbStats['disetujui'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Setuju</span>
                    </div>
                    <div class="text-center">
                        <span class="text-amber-300 block mb-0.5">{{ $lbStats['pending'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Wait</span>
                    </div>
                    <div class="text-center">
                        <span class="text-rose-300 block mb-0.5">{{ $lbStats['ditolak'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Tolak</span>
                    </div>
                </div>
            </div>

            {{-- Stat Supervisi --}}
            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-teal-200 text-lg">fact_check</span>
                    <h3 class="font-bold text-white text-sm tracking-wide">Supervisi Harian</h3>
                </div>
                <p class="text-4xl font-black text-white mb-4">{{ $svStats['total'] }}</p>
                <div class="flex justify-between text-xs font-semibold pt-3 border-t border-white/10">
                    <div class="text-center">
                        <span class="text-emerald-300 block mb-0.5">{{ $svStats['disetujui'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Setuju</span>
                    </div>
                    <div class="text-center">
                        <span class="text-amber-300 block mb-0.5">{{ $svStats['pending'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Wait</span>
                    </div>
                    <div class="text-center">
                        <span class="text-rose-300 block mb-0.5">{{ $svStats['ditolak'] }}</span>
                        <span class="text-white/60 text-[9px] uppercase tracking-wider">Tolak</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('logbook_harian.create') }}" class="bg-white p-5 rounded-2xl border border-outline-variant/20 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all flex flex-col items-center gap-3 text-center group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">edit_note</span>
            </div>
            <p class="text-sm font-bold text-on-surface">Input Logbook</p>
            <p class="text-[10px] text-slate-400">Catat aktivitas harian</p>
        </a>

        <a href="{{ route('supervisi_harian.index') }}" class="bg-white p-5 rounded-2xl border border-outline-variant/20 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all flex flex-col items-center gap-3 text-center group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">fact_check</span>
            </div>
            <p class="text-sm font-bold text-on-surface">Supervisi</p>
            <p class="text-[10px] text-slate-400">Cek persetujuan</p>
        </a>

        <a href="{{ route('logbook_harian.index') }}" class="bg-white p-5 rounded-2xl border border-outline-variant/20 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all flex flex-col items-center gap-3 text-center group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">history</span>
            </div>
            <p class="text-sm font-bold text-on-surface">Riwayat Logbook</p>
            <p class="text-[10px] text-slate-400">Lihat semua logbook</p>
        </a>

        {{-- PERBAIKAN ROUTE DI SINI --}}
        <a href="{{ route('penilaian.index') }}" class="bg-white p-5 rounded-2xl border border-outline-variant/20 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all flex flex-col items-center gap-3 text-center group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">analytics</span>
            </div>
            <p class="text-sm font-bold text-on-surface">Nilai Saya</p>
            <p class="text-[10px] text-slate-400">Lihat rekap penilaian</p>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- PROYEK YANG DITUGASKAN --}}
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h4 class="text-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings:'FILL' 1">work</span>
                    Proyek Saya
                </h4>
                <span class="text-xs text-slate-400">{{ $proyekDitugaskan->count() }} proyek</span>
            </div>
            <div class="flex-1">
                @forelse($proyekDitugaskan as $proyek)
                <div class="px-6 py-4 border-b border-slate-50 hover:bg-slate-50/80 transition-colors last:border-0">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">engineering</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-on-surface text-sm truncate">{{ $proyek->judul_proyek }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Manager: {{ $proyek->manager->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 mt-1">
                                {{ $proyek->tanggal_mulai?->format('d M Y') }} — {{ $proyek->tanggal_selesai?->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-slate-300 text-sm h-full flex items-center justify-center">
                    Belum ada proyek yang ditugaskan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- SUPERVISI SAYA --}}
        <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h4 class="text-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings:'FILL' 1">school</span>
                    Supervisi Saya
                </h4>
                <span class="text-xs text-slate-400">{{ $supervisiList->count() }} matkul</span>
            </div>
            <div class="flex-1">
                @forelse($supervisiList as $s)
                <div class="px-6 py-4 border-b border-slate-50 last:border-0 hover:bg-slate-50/80 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">menu_book</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-on-surface text-sm">{{ $s->mataKuliah->nama_matkul ?? '-' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $s->mataKuliah->kode_matkul ?? '' }} · {{ $s->mataKuliah->sks ?? '' }} SKS</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-semibold text-on-surface">{{ $s->dosen->nama_dosen ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Dosen Pengampu</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-slate-300 text-sm h-full flex items-center justify-center">
                    Belum ada supervisi yang ditugaskan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- LOGBOOK HARIAN TERBARU --}}
    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h4 class="text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings:'FILL' 1">history</span>
                Logbook Terbaru
            </h4>
            <a href="{{ route('logbook_harian.index') }}" class="text-xs font-semibold text-primary hover:underline">Lihat semua →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($logbookTerbaru as $logbook)
            <div class="px-6 py-4 hover:bg-slate-50/80 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex flex-col items-center justify-center text-primary shrink-0">
                        <span class="text-[9px] font-black uppercase tracking-wider">{{ isset($logbook->hari) ? substr($logbook->hari, 0, 3) : 'M'.$logbook->minggu_ke }}</span>
                        <span class="text-sm font-black leading-none mt-0.5">{{ \Carbon\Carbon::parse($logbook->tanggal)->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-on-surface text-sm line-clamp-1">
                            {{ $logbook->aktivitas ?? 'Aktivitas tidak ada' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Minggu ke-{{ $logbook->minggu_ke }} · {{ \Carbon\Carbon::parse($logbook->tanggal)->format('M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @php
                            $statusColor = match($logbook->status_verifikasi) {
                                'disetujui' => 'bg-emerald-50 text-emerald-600',
                                'ditolak'   => 'bg-rose-50 text-rose-600',
                                default     => 'bg-amber-50 text-amber-600',
                            };
                            $statusLabel = match($logbook->status_verifikasi) {
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                default     => 'Menunggu',
                            };
                        @endphp
                        <span class="text-[10px] font-black px-3 py-1 rounded-full {{ $statusColor }} uppercase tracking-wider">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-slate-300 text-sm">
                Belum ada logbook.
                <a href="{{ route('logbook_harian.create') }}" class="text-primary font-semibold hover:underline ml-1">Buat sekarang →</a>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection