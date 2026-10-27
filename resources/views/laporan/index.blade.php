@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
@php $user = auth()->user(); @endphp

{{-- HEADING --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            @if($user->isMahasiswa())
                Pengumpulan <span class="text-[#2dce89]">Laporan</span>
            @else
                Verifikasi <span class="text-[#2dce89]">Laporan</span>
            @endif
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
            @if($user->isMahasiswa()) Upload dan pantau status laporan kamu
            @else Verifikasi laporan yang disubmit mahasiswa
            @endif
        </p>
    </div>
    @if($user->isMahasiswa())
    <a href="{{ route('laporan.create') }}"
        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition">
        <span class="material-symbols-outlined text-base">upload_file</span>
        Upload Laporan
    </a>
    @endif
</div>

{{-- TABLE --}}
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Daftar Laporan</p>
        <span class="text-[10px] font-black text-gray-400">{{ $laporan->count() }} entri</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">No</th>
                    @if(!$user->isMahasiswa())
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Mahasiswa</th>
                    @endif
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Jenis Laporan</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Dosen Supervisi</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Tanggal Upload</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($laporan as $l)
                <tr class="hover:bg-teal-50/30 transition-colors">
                    <td class="px-6 py-4 text-[10px] font-black text-gray-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>

                    @if(!$user->isMahasiswa())
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                {{ strtoupper(substr($l->mahasiswa->nama ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-black text-[#004d4d] text-sm">{{ $l->mahasiswa->nama ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $l->mahasiswa->nim ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    @endif

                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black bg-teal-50 text-[#004d4d] uppercase tracking-wide">
                            <span class="material-symbols-outlined text-xs">description</span>
                            {{ $l->jenis_laporan }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-sm text-slate-500 font-medium">
    @if($user->isMahasiswa())
        {{ $l->dosenSupervisi ?? '-' }}
    @else
        {{ $l->proyek->judul_proyek ?? '-' }}
    @endif
</td>
                    <td class="px-6 py-4 text-xs text-slate-500 font-medium">{{ $l->created_at->format('d M Y') }}</td>

                    <td class="px-6 py-4">
                        @php
                            $status = $l->status_verifikasi ?? 'menunggu';
                            $cfg = match($status) {
                                'disetujui' => ['cls' => 'bg-emerald-50 text-emerald-700', 'icon' => 'check_circle',   'label' => 'Disetujui'],
                                'ditolak'   => ['cls' => 'bg-red-50 text-red-600',         'icon' => 'cancel',          'label' => 'Ditolak'],
                                default     => ['cls' => 'bg-amber-50 text-amber-600',     'icon' => 'hourglass_empty', 'label' => 'Menunggu'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black {{ $cfg['cls'] }} uppercase tracking-wide">
                            <span class="material-symbols-outlined text-xs">{{ $cfg['icon'] }}</span>
                            {{ $cfg['label'] }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ route('laporan.show', $l->id) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black bg-teal-50 text-[#004d4d] hover:bg-teal-100 transition uppercase tracking-wide">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">folder_open</span>
                        <p class="text-sm text-gray-400 font-medium">Belum ada laporan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection