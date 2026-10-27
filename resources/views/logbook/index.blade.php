@extends('layouts.app')
@section('title', 'Logbook')
@section('content')
@php $user = auth()->user(); @endphp

{{-- HEADING --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            @if($user->isMahasiswa())
                Progress <span class="text-[#2dce89]">PBL</span>
            @else
                Verifikasi <span class="text-[#2dce89]">Logbook</span>
            @endif
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
            @if($user->isMahasiswa()) Riwayat logbook mingguan Anda
            @else Daftar logbook mahasiswa yang perlu diverifikasi
            @endif
        </p>
    </div>
    @if($user->isMahasiswa())
    <a href="{{ route('logbook.create') }}"
        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition">
        <span class="material-symbols-outlined text-base">add</span>
        Input Logbook
    </a>
    @endif
</div>

{{-- STATS (Mahasiswa only) --}}
@if($user->isMahasiswa())
@php
    $total     = $logbook->count();
    $disetujui = $logbook->where('status_verifikasi','disetujui')->count();
    $menunggu  = $logbook->where('status_verifikasi','menunggu')->count();
    $ditolak   = $logbook->where('status_verifikasi','ditolak')->count();
@endphp
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</p>
        <h3 class="text-5xl font-black italic text-[#004d4d] mt-1">{{ $total }}</h3>
    </div>
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Disetujui</p>
        <h3 class="text-5xl font-black italic text-emerald-500 mt-1">{{ $disetujui }}</h3>
    </div>
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Menunggu</p>
        <h3 class="text-5xl font-black italic text-amber-500 mt-1">{{ $menunggu }}</h3>
    </div>
    <div class="bg-[#004d4d] rounded-[1.5rem] shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest text-teal-100/60">Ditolak</p>
        <h3 class="text-5xl font-black italic text-white mt-1">{{ $ditolak }}</h3>
    </div>
</div>
@endif

{{-- TABLE --}}
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">
            {{ $user->isMahasiswa() ? 'Daftar Logbook' : 'Logbook Mahasiswa' }}
        </h4>
        <span class="text-[10px] font-black text-gray-400">{{ $logbook->count() }} entri</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100">
                    @if(!$user->isMahasiswa())
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelas</th>
                    @endif
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Minggu</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aktivitas</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($logbook as $lb)
                <tr class="hover:bg-teal-50/30 transition-colors">
                    @if(!$user->isMahasiswa())
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black">
                                    {{ strtoupper(substr($lb->mahasiswa->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-[#004d4d] text-sm">{{ $lb->mahasiswa->nama }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $lb->mahasiswa->nim }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 font-medium">{{ $lb->kelas->nama_kelas ?? '-' }}</td>
                    @endif
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d] font-black text-sm">
                            W{{ $lb->minggu_ke }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500 font-medium whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($lb->tanggal)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-slate-600 max-w-xs">
                        <p class="truncate text-sm">{{ $lb->aktivitas }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $color = match($lb->status_verifikasi) {
                                'disetujui' => 'bg-emerald-50 text-emerald-700',
                                'ditolak'   => 'bg-red-50 text-red-600',
                                default     => 'bg-amber-50 text-amber-600',
                            };
                            $label = match($lb->status_verifikasi) {
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                default     => 'Menunggu',
                            };
                        @endphp
                        <span class="text-[10px] font-black px-3 py-1.5 rounded-full {{ $color }} uppercase tracking-wide">{{ $label }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('logbook.show', $lb) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-[#004d4d] text-[10px] font-black rounded-xl hover:bg-teal-100 transition uppercase tracking-wide">
                            @if($user->isManager())
                                <span class="material-symbols-outlined text-sm">verified</span> Verifikasi
                            @else
                                <span class="material-symbols-outlined text-sm">visibility</span> Detail
                            @endif
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">menu_book</span>
                        @if($user->isMahasiswa())
                            <p class="text-sm text-gray-400 font-medium">Belum ada logbook.</p>
                            <a href="{{ route('logbook.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-black text-[#004d4d] hover:underline">
                                Buat sekarang →
                            </a>
                        @else
                            <p class="text-sm text-gray-400 font-medium">Belum ada logbook dari mahasiswa.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection