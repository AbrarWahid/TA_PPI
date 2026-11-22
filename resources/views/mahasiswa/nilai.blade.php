@extends('layouts.app')
@section('title', 'Nilai & Transkrip — Magang Dalam Kampus')
@section('content')

<div class="max-w-[1400px] mx-auto space-y-6">

    {{-- UNIFIED PAGE HEADER (Matching Magang Luar Kampus) --}}
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#013981]"></span>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">MAGANG DALAM KAMPUS · TRANSKRIP NILAI</span>
            </div>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                Transkrip &amp; Rincian Penilaian
            </h1>
            <p class="text-xs text-[#6a6f7c] mt-1">
                Rincian evaluasi berbasis rubrik dari Manager Proyek dan Dosen Pengampu.
            </p>
        </div>
    </div>

@if(!$nilaiManager && $nilaiDosen->isEmpty())
    <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface py-16 text-center">
        <span class="material-symbols-outlined text-4xl text-[#d4d4d8] block mb-2">grade</span>
        <p class="text-xs font-medium text-[#0b0b0c]">Belum ada nilai yang dipublikasikan</p>
        <p class="text-[11px] text-[#6a6f7c] mt-0.5">Nilai akan muncul setelah Manager Proyek atau Dosen Pengampu menyelesaikan evaluasi.</p>
    </div>
@else

@php
    $periode = \App\Models\PeriodePbl::getAktif();
    $bobotManager = $periode ? $periode->bobot_manager : 55;
    $bobotDosen   = $periode ? $periode->bobot_dosen   : 45;

    $nmSkala100 = $nilaiManager->nilai_manager ?? 0;
    $ndSkala100 = $nilaiDosen->count() > 0 ? $nilaiDosen->avg('nilai_dosen') : 0;

    $nmBobot = $nmSkala100 * ($bobotManager / 100);
    $ndBobot = $ndSkala100 * ($bobotDosen / 100);

    $nilaiAkhir = $nmBobot + $ndBobot;
    $grade = $nilaiAkhir >= 85 ? 'A' : ($nilaiAkhir >= 75 ? 'B' : ($nilaiAkhir >= 65 ? 'C' : ($nilaiAkhir >= 55 ? 'D' : 'E')));
@endphp

    {{-- SUMMARY HERO CARD (Unified with External Nilai) --}}
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-6 sm:p-7 shadow-surface flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Rekapitulasi Akhir</p>
            <h2 class="text-xl font-semibold text-[#0b0b0c]">{{ $mahasiswa->nama }}</h2>
            <p class="font-mono text-xs text-[#6a6f7c]">NIM · {{ $mahasiswa->nim }}</p>
            <div class="pt-3 flex flex-wrap items-center gap-4 text-xs text-[#6a6f7c]">
                <span>Manager ({{ $bobotManager }}%): <strong class="font-mono text-[#0b0b0c]">{{ number_format($nmBobot, 1) }}</strong> ({{ number_format($nmSkala100, 1) }}/100)</span>
                <span class="text-[#d4d4d8]">·</span>
                <span>Dosen ({{ $bobotDosen }}%): <strong class="font-mono text-[#0b0b0c]">{{ number_format($ndBobot, 1) }}</strong> ({{ number_format($ndSkala100, 1) }}/100)</span>
            </div>
        </div>
        <div class="flex items-center gap-4 bg-[#fafafa] border border-[#e4e4e7] rounded-xl px-6 py-4 shrink-0">
            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">Nilai Akhir</p>
                <p class="font-mono text-3xl font-semibold text-[#0b0b0c] mt-0.5">{{ number_format($nilaiAkhir, 1) }}<span class="text-xs text-[#6a6f7c] font-normal">/100</span></p>
            </div>
            <div class="w-px h-10 bg-[#e4e4e7]"></div>
            <div class="text-center">
                <p class="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">Predikat</p>
                <span class="inline-flex items-center justify-center font-mono text-xl font-semibold bg-[#013981] text-white w-10 h-10 rounded-lg mt-0.5">{{ $grade }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- I. MANAGER PROYEK --}}
        <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
            <div class="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between bg-[#fafafa]">
                <div>
                    <h3 class="font-semibold text-[#0b0b0c] text-sm">I. Evaluasi Manager Proyek</h3>
                    <p class="text-[11px] text-[#6a6f7c]">Bobot {{ $bobotManager }}% dari nilai akhir</p>
                </div>
                <div class="text-right">
                    <span class="ps-badge">{{ number_format($nmBobot, 1) }} / {{ $bobotManager }}</span>
                    <span class="font-mono text-[10px] text-[#6a6f7c] mt-0.5 block">Skala 100: {{ number_format($nmSkala100, 1) }}</span>
                </div>
            </div>
            <div class="p-5 space-y-5">
                @if($nilaiManager)
                @php
                $grupManager = [
                    ['label'=>'Learning Skills','bobot'=>'20%','items'=>[
                        ['Critical Thinking',    $nilaiManager->ls_critical_thinking],
                        ['Kolaborasi',           $nilaiManager->ls_kolaborasi],
                        ['Kreativitas & Inovasi',$nilaiManager->ls_kreativitas],
                        ['Komunikasi',           $nilaiManager->ls_komunikasi],
                    ]],
                    ['label'=>'Life Skills','bobot'=>'20%','items'=>[
                        ['Fleksibilitas', $nilaiManager->lf_fleksibilitas],
                        ['Kepemimpinan',  $nilaiManager->lf_kepemimpinan],
                        ['Produktivitas', $nilaiManager->lf_produktivitas],
                        ['Social Skill',  $nilaiManager->lf_social_skill],
                    ]],
                    ['label'=>'Laporan Project','bobot'=>'15%','items'=>[
                        ['RPP',            $nilaiManager->lp_rpp],
                        ['Logbook',        $nilaiManager->lp_logbook],
                        ['Dokumen Projek', $nilaiManager->lp_dokumen_projek],
                    ]],
                ];
                @endphp
                @foreach($grupManager as $grup)
                <div>
                    <div class="flex justify-between items-center mb-2.5">
                        <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">{{ $grup['label'] }}</p>
                        <span class="font-mono text-[11px] text-[#6a6f7c]">{{ $grup['bobot'] }}</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($grup['items'] as [$label, $value])
                        <div class="flex items-center gap-3">
                            <span class="w-36 text-xs text-[#52525b] shrink-0">{{ $label }}</span>
                            <div class="flex-1 bg-[#f4f4f5] h-1.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-[#013981]" style="width: {{ min($value ?? 0, 100) }}%"></div>
                            </div>
                            <span class="w-8 text-right font-mono text-xs font-medium text-[#0b0b0c]">{{ $value ?? '-' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @if($nilaiManager->catatan_manager)
                <div class="p-3.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-xs text-[#52525b] leading-relaxed">
                    <span class="font-semibold text-[#0b0b0c] block mb-1">Catatan Manager:</span>
                    {{ $nilaiManager->catatan_manager }}
                </div>
                @endif
                @else
                <p class="text-xs text-[#6a6f7c] text-center py-6">Belum ada penilaian dari Manager Proyek.</p>
                @endif
            </div>
        </div>

        {{-- II. DOSEN PENGAMPU --}}
        <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
            <div class="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between bg-[#fafafa]">
                <div>
                    <h3 class="font-semibold text-[#0b0b0c] text-sm">II. Evaluasi Dosen Pengampu</h3>
                    <p class="text-[11px] text-[#6a6f7c]">Bobot {{ $bobotDosen }}% dari nilai akhir</p>
                </div>
                <div class="text-right">
                    <span class="ps-badge">{{ number_format($ndBobot, 1) }} / {{ $bobotDosen }}</span>
                    <span class="font-mono text-[10px] text-[#6a6f7c] mt-0.5 block">Skala 100: {{ number_format($ndSkala100, 1) }}</span>
                </div>
            </div>
            <div class="p-5 space-y-5">
                @forelse($nilaiDosen as $pd)
                @php
                    $ndSingleSkala100 = $pd->nilai_dosen ?? 0;
                    $jumlahMatkul     = $nilaiDosen->count();
                    $ndSingleBobot    = $jumlahMatkul > 0 ? ($ndSingleSkala100 * ($bobotDosen / 100)) / $jumlahMatkul : 0;
                    $grupDosen = [
                        ['label'=>'Literacy Skills','bobot'=>'15%','items'=>[
                            ['Literasi Informasi', $pd->lit_informasi],
                            ['Literasi Media',     $pd->lit_media],
                            ['Literasi Teknologi', $pd->lit_teknologi],
                        ]],
                        ['label'=>'Presentasi','bobot'=>'15%','items'=>[
                            ['Konten',          $pd->pr_konten],
                            ['Tampilan Visual', $pd->pr_visual],
                            ['Kosakata',        $pd->pr_kosakata],
                            ['Tanya Jawab',     $pd->pr_tanya_jawab],
                            ['Mata & Gerak',    $pd->pr_mata_gerak],
                        ]],
                        ['label'=>'Laporan Akhir','bobot'=>'15%','items'=>[
                            ['Penulisan Laporan', $pd->la_penulisan],
                            ['Pilihan Kata',      $pd->la_pilihan_kata],
                            ['Konten',            $pd->la_konten],
                        ]],
                    ];
                @endphp
                <div class="pb-4 border-b border-[#f4f4f5] last:border-b-0 last:pb-0">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-[#0b0b0c]">
                            {{ $pd->supervisiMatkul->mataKuliah->nama_matkul ?? '-' }}
                            <span class="font-mono text-[11px] text-[#6a6f7c] font-normal ml-1">· Sem {{ $pd->supervisiMatkul->semester ?? '' }}</span>
                        </p>
                        <span class="font-mono text-[11px] text-[#0b0b0c] bg-[#f4f4f5] border border-[#e4e4e7] px-2 py-0.5 rounded">
                            {{ number_format($ndSingleSkala100, 1) }} / 100
                        </span>
                    </div>
                    @foreach($grupDosen as $grup)
                    <div class="mb-3.5 last:mb-0">
                        <div class="flex justify-between items-center mb-2">
                            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">{{ $grup['label'] }}</p>
                            <span class="font-mono text-[11px] text-[#6a6f7c]">{{ $grup['bobot'] }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($grup['items'] as [$label, $value])
                            <div class="flex items-center gap-3">
                                <span class="w-36 text-xs text-[#52525b] shrink-0">{{ $label }}</span>
                                <div class="flex-1 bg-[#f4f4f5] h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-[#013981]" style="width: {{ min($value ?? 0, 100) }}%"></div>
                                </div>
                                <span class="w-8 text-right font-mono text-xs font-medium text-[#0b0b0c]">{{ $value ?? '-' }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @if($pd->catatan_dosen)
                    <div class="mt-3 p-3.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-xs text-[#52525b] leading-relaxed">
                        <span class="font-semibold text-[#0b0b0c] block mb-1">Catatan Dosen:</span>
                        {{ $pd->catatan_dosen }}
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-xs text-[#6a6f7c] text-center py-6">Belum ada penilaian dari Dosen Pengampu.</p>
                @endforelse
            </div>
        </div>

    </div>
@endif

</div>
@endsection
