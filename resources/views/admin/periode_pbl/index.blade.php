@extends('layouts.app')

@section('title', 'Manajemen Periode PBL')

@section('content')
<div class="flex-1 flex flex-col">

    {{-- Header --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Manajemen <span class="text-[#2dce89]">Periode PBL</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                Konfigurasi Tahun Ajaran & Sistem Penilaian
            </p>
        </div>
        <a href="{{ route('admin.periode_pbl.create') }}"
            class="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20">
            <span class="material-symbols-outlined text-base">add</span> TAMBAH PERIODE
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-base">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Banner Periode Aktif --}}
    @if($periodeAktif)
        <div class="mb-8 bg-gradient-to-r from-[#004d4d] to-[#007070] rounded-[1.5rem] p-6 shadow-lg shadow-teal-900/20 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#7fffd4]/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#7fffd4] text-2xl">event_available</span>
                </div>
                <div>
                    <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-1">Periode Aktif Saat Ini</p>
                    <p class="text-white text-xl font-black italic tracking-tight">{{ $periodeAktif->nama_periode }}</p>
                    <p class="text-white/60 text-[11px] font-medium mt-0.5 uppercase tracking-wider">
                        T.A {{ $periodeAktif->tahun_ajaran }} • {{ $periodeAktif->label_semester }}
                        @if($periodeAktif->koordinator)
                            • Koordinator: {{ $periodeAktif->koordinator->name }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-6 flex-shrink-0">
                <div class="text-center">
                    <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Bobot Nilai</p>
                    <p class="text-white text-lg font-black tracking-tighter mt-0.5">
                        P {{ $periodeAktif->bobot_manager }}%
                        <span class="text-white/40 text-sm font-medium">:</span>
                        S {{ $periodeAktif->bobot_dosen }}%
                    </p>
                    <p class="text-white/50 text-[10px] font-medium">Manager : Dosen</p>
                </div>
                <div class="text-center">
                    <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Logbook</p>
                    <p class="text-white text-lg font-black tracking-tighter mt-0.5 capitalize">{{ $periodeAktif->frekuensi_logbook }}</p>
                </div>
                <a href="{{ route('admin.periode_pbl.edit', $periodeAktif->id) }}"
                    class="flex items-center gap-1.5 px-4 py-2.5 bg-[#7fffd4]/10 hover:bg-[#7fffd4]/20 text-[#7fffd4] border border-[#7fffd4]/30 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                    <span class="material-symbols-outlined text-sm">edit</span> Edit
                </a>
            </div>
        </div>
    @else
        <div class="mb-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-[1.5rem] p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-gray-400 text-2xl">event_busy</span>
            </div>
            <div>
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-0.5">Periode Aktif Saat Ini</p>
                <p class="text-gray-500 font-black italic">Belum Ada Periode Aktif</p>
                <p class="text-gray-400 text-[11px] mt-0.5">Tambahkan periode baru atau aktifkan salah satu periode yang ada.</p>
            </div>
        </div>
    @endif

    {{-- Tabel Semua Periode --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">#</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama & Waktu</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Logbook</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Bobot Nilai</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($periodeList as $idx => $item)
                <tr class="hover:bg-teal-50/30 transition-colors {{ $item->is_aktif ? 'bg-teal-50/20' : '' }}">
                    <td class="px-8 py-5 text-[10px] font-black text-gray-300">{{ $idx + 1 }}</td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white font-black text-xs shadow-md flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">event_note</span>
                            </div>
                            <div>
                                <span class="font-bold text-[#004d4d] block leading-none">{{ $item->nama_periode }}</span>
                                <span class="text-[10px] text-gray-400 font-medium mt-1 block tracking-wider uppercase">
                                    T.A {{ $item->tahun_ajaran }} • {{ $item->label_semester }}
                                    @if($item->koordinator)
                                        • {{ $item->koordinator->name }}
                                    @endif
                                </span>
                                @if($item->tanggal_mulai && $item->tanggal_selesai)
                                    <span class="text-[10px] text-gray-300 font-medium block">
                                        {{ $item->tanggal_mulai->format('d M Y') }} – {{ $item->tanggal_selesai->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs text-gray-600 font-bold uppercase tracking-wider capitalize">
                            {{ $item->frekuensi_logbook }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-[#004d4d] border border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-wider">
                            P {{ $item->bobot_manager }}% : S {{ $item->bobot_dosen }}%
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @if($item->is_aktif)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                                <span class="material-symbols-outlined text-xs">check_circle</span> Aktif
                            </span>
                        @else
                            <form action="{{ route('admin.periode_pbl.aktif', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1 bg-gray-100 hover:bg-[#7fffd4] text-gray-500 hover:text-[#004d4d] rounded-full text-[10px] font-black uppercase tracking-wider transition-all"
                                    onclick="return confirm('Aktifkan periode {{ $item->nama_periode }}? Periode aktif sebelumnya akan dinonaktifkan.')">
                                    Set Aktif
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.periode_pbl.edit', $item->id) }}"
                                class="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-[#7fffd4] rounded-xl transition-all border border-transparent"
                                title="Edit">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            @if(!$item->is_aktif)
                                <form action="{{ route('admin.periode_pbl.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus periode \'{{ $item->nama_periode }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent"
                                        title="Hapus">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center opacity-40">
                        <span class="material-symbols-outlined text-5xl mb-2 text-gray-400 block">calendar_today</span>
                        <p class="font-black italic text-gray-500">Belum ada data periode.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection