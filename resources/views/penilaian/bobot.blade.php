@extends('layouts.app')

@section('title', 'Bobot Penilaian')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Bobot <span class="text-[#2dce89]">Penilaian</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Distribusi bobot penilaian PBL AE Polman Bandung</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-50 text-emerald-600 text-xs font-black">
        <span class="material-symbols-outlined text-base">check_circle</span>
        Total 100%
    </div>
</div>

<div class="flex gap-6">
    {{-- PANEL KIRI: Tabel Bobot --}}
    <div class="flex-1 space-y-4">

        @php
        $aspeks = [
            [
                'nama' => 'Learning Skills', 'total' => '20%', 'warna' => '#0d9488', 'penilai' => 'Manager Proyek',
                'items' => ['Critical Thinking' => '5%', 'Kolaborasi' => '5%', 'Kreativitas & Inovasi' => '5%', 'Komunikasi' => '5%'],
            ],
            [
                'nama' => 'Life Skills', 'total' => '20%', 'warna' => '#7c3aed', 'penilai' => 'Manager Proyek',
                'items' => ['Fleksibilitas' => '5%', 'Kepemimpinan' => '5%', 'Produktivitas' => '5%', 'Social Skill' => '5%'],
            ],
            [
                'nama' => 'Laporan Project', 'total' => '15%', 'warna' => '#0284c7', 'penilai' => 'Manager Proyek',
                'items' => ['RPP' => '5%', 'Logbook Mingguan' => '5%', 'Dokumen Projek' => '5%'],
            ],
            [
                'nama' => 'Literacy Skills', 'total' => '15%', 'warna' => '#d97706', 'penilai' => 'Dosen Pengampu',
                'items' => ['Literasi Informasi' => '5%', 'Literasi Media' => '5%', 'Literasi Teknologi' => '5%'],
            ],
            [
                'nama' => 'Presentasi', 'total' => '15%', 'warna' => '#be185d', 'penilai' => 'Dosen Pengampu',
                'items' => ['Konten' => '3%', 'Tampilan Visual' => '3%', 'Kosakata' => '3%', 'Tanya Jawab' => '3%', 'Mata & Gerak Tubuh' => '3%'],
            ],
            [
                'nama' => 'Laporan Akhir', 'total' => '15%', 'warna' => '#16a34a', 'penilai' => 'Dosen Pengampu',
                'items' => ['Penulisan Laporan' => '5%', 'Pilihan Kata' => '5%', 'Konten' => '5%'],
            ],
        ];
        @endphp

        @foreach($aspeks as $aspek)
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header aspek --}}
            <div class="px-7 py-4 flex items-center justify-between border-b border-gray-100"
                 style="border-left: 4px solid {{ $aspek['warna'] }};">
                <div>
                    <h3 class="font-black text-[#004d4d]">{{ $aspek['nama'] }}</h3>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $aspek['penilai'] }}</span>
                </div>
                <span class="text-2xl font-black italic" style="color:{{ $aspek['warna'] }};">{{ $aspek['total'] }}</span>
            </div>
            {{-- Sub items --}}
            <div class="divide-y divide-gray-50">
                @foreach($aspek['items'] as $nama => $bobot)
                <div class="px-7 py-3.5 flex items-center justify-between hover:bg-teal-50/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full" style="background:{{ $aspek['warna'] }};"></div>
                        <span class="text-sm text-slate-600">{{ $nama }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-24 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ $bobot }}; background:{{ $aspek['warna'] }};"></div>
                        </div>
                        <span class="text-sm font-black w-8 text-right" style="color:{{ $aspek['warna'] }};">{{ $bobot }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Catatan --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
            <span class="material-symbols-outlined text-blue-400 text-lg mt-0.5">info</span>
            <div>
                <p class="text-xs font-black text-blue-700 uppercase tracking-widest mb-1">Catatan</p>
                <p class="text-xs text-blue-600 leading-relaxed">Bobot mengacu pada standar kurikulum PBL AE Polman Bandung. Manager Proyek menilai 55%, Dosen Pengampu menilai 45%.</p>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN --}}
    <div class="w-64 flex-shrink-0 space-y-4">

        {{-- Distribusi --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Distribusi Bobot</p>
            <div class="space-y-3">
                @foreach($aspeks as $a)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-slate-500 truncate max-w-[130px]">{{ $a['nama'] }}</span>
                        <span class="text-xs font-black text-[#004d4d]">{{ $a['total'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $a['total'] }}; background:{{ $a['warna'] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</span>
                <span class="text-lg font-black text-emerald-500">100%</span>
            </div>
        </div>

        {{-- Komposisi penilai --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Komposisi Penilai</p>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-bold text-slate-500">Manager Proyek</span>
                        <span class="text-xs font-black text-[#004d4d]">55%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full w-[55%]" style="background:#0d9488;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-bold text-slate-500">Dosen Pengampu</span>
                        <span class="text-xs font-black text-[#004d4d]">45%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full w-[45%]" style="background:#7c3aed;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Link rubrik --}}
        <div class="bg-[#004d4d] rounded-[2rem] shadow-sm p-6">
            <span class="material-symbols-outlined text-[#2dce89] text-3xl mb-3 block">help_outline</span>
            <h4 class="font-black text-white text-sm mb-2">Butuh Bantuan?</h4>
            <p class="text-xs text-teal-100/60 leading-relaxed mb-4">Lihat rubrik penilaian lengkap untuk detail kriteria per level.</p>
            <a href="{{ route('rubrik.index') }}"
               class="inline-flex items-center gap-1 text-xs font-black text-[#2dce89] hover:underline">
                Buka Rubrik Penilaian
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
        </div>
    </div>
</div>

@endsection