@extends('layouts.app')
@section('title', 'Dashboard Manager Proyek')
@section('content')

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black italic uppercase text-[#004d4d] tracking-tighter">
                Dashboard <span class="text-[#2dce89]">Manager Proyek</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Selamat datang, <span class="font-bold text-[#004d4d]">{{ auth()->user()->name }}</span>. Kelola proyek PBL Anda.</p>
        </div>
        <a href="{{ route('pengajuan_proyek.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-[#004d4d] hover:bg-[#003d3d] text-white font-black rounded-2xl text-sm shadow-lg hover:-translate-y-0.5 transition-all w-fit">
            <span class="material-symbols-outlined text-base">add</span> Ajukan Proyek
        </a>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d]">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">folder_open</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold">Total Proyek</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-[#004d4d]">{{ $totalProyek }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">check_circle</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold">Disetujui</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-[#004d4d]">{{ $proyekDisetujui }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">pending</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold">Menunggu</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-[#004d4d]">{{ $proyekPending }}</h3>
            </div>
        </div>

        <div class="bg-[#004d4d] p-6 rounded-[2rem] shadow-sm flex flex-col justify-between h-36 relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">group</span>
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white backdrop-blur-sm relative z-10">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">group</span>
            </div>
            <div class="relative z-10">
                <p class="text-[#7fffd4] uppercase tracking-widest text-[10px] font-bold">Total Mahasiswa</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-white">{{ $totalMahasiswa }}</h3>
            </div>
        </div>
    </section>

    {{-- SHORTCUT VERIFIKASI LOGBOOK --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <a href="{{ route('logbook_mingguan.daftar_verifikasi') }}"
           class="flex items-center gap-4 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:border-[#2dce89] hover:bg-slate-50/50 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shrink-0">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">rate_review</span>
            </div>
            <div>
                <p class="font-black text-sm text-[#004d4d]">Verifikasi Logbook Mingguan</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Review & ACC rekap mingguan mahasiswa</p>
            </div>
            <span class="material-symbols-outlined text-slate-300 group-hover:text-[#2dce89] ml-auto transition-colors">chevron_right</span>
        </a>

        <a href="{{ route('logbook_harian.rekap') }}"
           class="flex items-center gap-4 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:border-[#2dce89] hover:bg-slate-50/50 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-[#004d4d] group-hover:bg-[#004d4d] group-hover:text-white transition-colors shrink-0">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">calendar_month</span>
            </div>
            <div>
                <p class="font-black text-sm text-[#004d4d]">Rekap Logbook Harian</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Lihat aktivitas harian mahasiswa per minggu</p>
            </div>
            <span class="material-symbols-outlined text-slate-300 group-hover:text-[#2dce89] ml-auto transition-colors">chevron_right</span>
        </a>
    </section>

    {{-- PROYEK LIST (Kini menggunakan style Tabel Baku) --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-7 py-5 border-b border-gray-100 flex justify-between items-center">
            <h4 class="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">work</span> Proyek Saya
            </h4>
            <a href="{{ route('pengajuan_proyek.index') }}" class="text-xs font-bold text-[#2dce89] hover:underline">Lihat semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 w-12">#</th>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Proyek</th>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Periode</th>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Tim</th>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                @forelse($proyekList as $idx => $proyek)
                <tbody x-data="{ open: false }" class="divide-y divide-gray-50 border-b border-gray-50">
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-7 py-5 text-sm font-bold text-slate-400">{{ $idx + 1 }}</td>
                        <td class="px-7 py-5">
                            <p class="font-black text-[#004d4d] text-sm">{{ $proyek->judul_proyek }}</p>
                        </td>
                        <td class="px-7 py-5">
                            <p class="text-xs font-bold text-slate-600">{{ $proyek->tanggal_mulai?->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400">s.d. {{ $proyek->tanggal_selesai?->format('d M Y') }}</p>
                        </td>
                        <td class="px-7 py-5 text-center">
                            @if($proyek->mahasiswa->count() > 0)
                            <button @click="open = !open" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold transition-colors">
                                {{ $proyek->mahasiswa->count() }} Org
                                <span class="material-symbols-outlined text-[14px]" x-text="open ? 'expand_less' : 'expand_more'">expand_more</span>
                            </button>
                            @else
                            <span class="text-xs font-bold text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-7 py-5 text-center">
                            <span class="inline-flex px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $proyek->getStatusBadgeColor() }}">
                                {{ $proyek->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-7 py-5 text-center">
                            <a href="{{ route('pengajuan_proyek.show', $proyek) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-teal-50 text-[#004d4d] hover:bg-[#004d4d] hover:text-white transition-colors" title="Detail">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    {{-- Row Expand Tim Mahasiswa --}}
                    @if($proyek->mahasiswa->count() > 0)
                    <tr x-show="open" x-transition.opacity class="bg-slate-50/50">
                        <td colspan="6" class="px-7 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($proyek->mahasiswa as $mhs)
                                <div class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                                    <div class="w-8 h-8 rounded-lg bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                        {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-black text-[#004d4d] truncate">{{ $mhs->nama }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $mhs->nim }} · {{ $mhs->pivot->prodi ?? '-' }}</p>
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
                        <td colspan="6" class="px-7 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">work_off</span>
                            <p class="text-sm font-bold text-slate-400">Belum ada proyek yang diajukan.</p>
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>
    </div>

</div>
@endsection