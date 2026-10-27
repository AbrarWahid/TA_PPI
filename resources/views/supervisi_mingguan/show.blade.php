@extends('layouts.app')

@section('title', 'Detail Laporan Supervisi')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}"
               class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition text-gray-400">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-black text-[#004d4d]">Detail Laporan Mingguan</h1>
                <p class="text-sm text-gray-400 mt-0.5">Minggu ke-{{ $supervisiMingguan->minggu_ke }}</p>
            </div>
        </div>

        {{-- Badge Status --}}
        @php
            $statusConfig = [
                'draft'     => ['bg-gray-100',    'text-gray-500',    'Draft'],
                'diajukan'  => ['bg-amber-50',     'text-amber-600',   'Menunggu Verifikasi'],
                'disetujui' => ['bg-emerald-50',   'text-emerald-600', 'Disetujui'],
                'ditolak'   => ['bg-red-50',       'text-red-500',     'Ditolak'],
            ];
            $sc = $statusConfig[$supervisiMingguan->status] ?? ['bg-gray-100', 'text-gray-500', $supervisiMingguan->status];
        @endphp
        <span class="px-4 py-2 rounded-2xl text-sm font-black {{ $sc[0] }} {{ $sc[1] }}">
            {{ $sc[2] }}
        </span>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- INFO LAPORAN --}}
        <div class="col-span-2 space-y-5">

            {{-- Info Umum --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-xs font-black uppercase tracking-widest text-[#004d4d] mb-4">Informasi Laporan</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Mahasiswa</p>
                        <p class="font-black text-gray-800">{{ $supervisiMingguan->mahasiswa->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $supervisiMingguan->mahasiswa->nim ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Dosen Pengampu</p>
                        <p class="font-black text-gray-800">{{ $supervisiMingguan->dosen->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Mata Kuliah</p>
                        <p class="font-bold text-gray-700">{{ $supervisiMingguan->mataKuliah->nama_matkul ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Proyek</p>
                        <p class="font-bold text-gray-700">{{ $supervisiMingguan->pengajuanProyek->judul_proyek ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Minggu Ke</p>
                        <p class="font-black text-[#004d4d] text-lg">{{ $supervisiMingguan->minggu_ke }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Diajukan</p>
                        <p class="font-bold text-gray-700">{{ $supervisiMingguan->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Log Harian --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <p class="text-xs font-black uppercase tracking-widest text-[#004d4d]">Log Harian Minggu Ke-{{ $supervisiMingguan->minggu_ke }}</p>
                </div>
                @if($logs->isEmpty())
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-200">event_note</span>
                        <p class="text-gray-400 text-sm font-bold mt-2">Tidak ada log harian</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($logs as $log)
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#004d4d]/10 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-sm text-[#004d4d]">calendar_today</span>
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-800 text-sm">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $log->jam_mulai }} – {{ $log->jam_selesai }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-11 space-y-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Aktivitas</p>
                                    <p class="text-sm text-gray-700">{{ $log->aktivitas }}</p>
                                </div>
                                @if($log->kendala)
                                <div>
                                    <p class="text-xs font-bold text-amber-500 uppercase tracking-wide">Kendala</p>
                                    <p class="text-sm text-gray-700">{{ $log->kendala }}</p>
                                </div>
                                @endif
                                @if($log->dokumentasi)
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Dokumentasi</p>
                                    <img src="{{ Storage::url($log->dokumentasi) }}" alt="Dokumentasi"
                                         class="rounded-xl max-h-40 object-cover border border-gray-100">
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- SIDEBAR KANAN --}}
        <div class="space-y-5">

            {{-- PDF --}}
            @if($supervisiMingguan->pdf_path)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-black uppercase tracking-widest text-[#004d4d] mb-3">Dokumen PDF</p>
                <a href="{{ Storage::url($supervisiMingguan->pdf_path) }}" target="_blank"
                   class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-[#004d4d] text-white font-bold text-sm hover:bg-[#003d3d] transition">
                    <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                    Buka PDF
                </a>
            </div>
            @endif

            {{-- Catatan Dosen --}}
            @if($supervisiMingguan->catatan_dosen || $supervisiMingguan->diverifikasi_at)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-black uppercase tracking-widest text-[#004d4d] mb-3">Catatan Dosen</p>
                @if($supervisiMingguan->diverifikasi_at)
                <p class="text-xs text-gray-400 mb-2">{{ \Carbon\Carbon::parse($supervisiMingguan->diverifikasi_at)->format('d M Y, H:i') }}</p>
                @endif
                @if($supervisiMingguan->catatan_dosen)
                <p class="text-sm text-gray-700 leading-relaxed">{{ $supervisiMingguan->catatan_dosen }}</p>
                @else
                <p class="text-sm text-gray-400 italic">Tidak ada catatan</p>
                @endif
            </div>
            @endif

            {{-- Verifikasi Form (Dosen, status diajukan) --}}
            @auth
            @if(auth()->user()->role === 'dosen' && (auth()->user()->role_aktif ?? 'dosen_pengampu') === 'dosen_pengampu' && $supervisiMingguan->status === 'diajukan')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5" x-data="{}">
                <p class="text-xs font-black uppercase tracking-widest text-[#004d4d] mb-4">Verifikasi</p>
                <form action="{{ route('supervisi_mingguan.verifikasi', $supervisiMingguan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 cursor-pointer hover:border-emerald-400 transition has-[:checked]:border-emerald-500">
                            <input type="radio" name="status" value="disetujui" class="accent-emerald-500">
                            <span class="text-sm font-bold text-emerald-700">Setujui</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-red-200 bg-red-50 cursor-pointer hover:border-red-400 transition has-[:checked]:border-red-500">
                            <input type="radio" name="status" value="ditolak" class="accent-red-500">
                            <span class="text-sm font-bold text-red-600">Tolak</span>
                        </label>
                    </div>
                    <textarea name="catatan_dosen" rows="3" placeholder="Catatan untuk mahasiswa (opsional)..."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-[#7fffd4] resize-none"></textarea>
                    <button type="submit"
                        class="w-full px-4 py-3 rounded-2xl bg-[#004d4d] text-white font-black text-sm hover:bg-[#003d3d] transition">
                        Simpan Verifikasi
                    </button>
                </form>
            </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection