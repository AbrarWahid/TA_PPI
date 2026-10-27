@extends('layouts.app')
@section('title', 'Dashboard Dosen Pengampu')
@section('content')

@php
    $user = auth()->user();
    
    // JUDUL DASHBOARD DINAMIS
    if ($user->isKps()) {
        $roleName = 'Koordinator Prodi';
    } else {
        $roleName = 'Dosen Pengampu';
    }
@endphp

<div class="space-y-6 max-w-[1400px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black italic uppercase text-[#004d4d] tracking-tighter">
                Dashboard <span class="text-[#2dce89]">{{ $roleName }}</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1 uppercase tracking-widest font-bold">
                Selamat datang, <span class="text-[#004d4d]">{{ $user->name }}</span>. Verifikasi laporan & nilai mahasiswa.
            </p>
        </div>
        <a href="{{ route('penilaian.dosen.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-[#004d4d] hover:bg-[#003d3d] text-[#7fffd4] font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-lg hover:-translate-y-0.5 transition-all w-fit">
            <span class="material-symbols-outlined text-base">add</span> Input Penilaian
        </a>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <div class="bg-white p-7 rounded-[2rem] border border-outline-variant/20 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">school</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-black mb-1">Total Supervisi</p>
                <h3 class="text-5xl font-black italic tracking-tighter text-[#004d4d]">{{ $supervisiList->count() }}</h3>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[2rem] border border-outline-variant/20 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d]">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">group</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-black mb-1">Total Mahasiswa</p>
                <h3 class="text-5xl font-black italic tracking-tighter text-[#004d4d]">{{ $totalMahasiswa }}</h3>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[2rem] border border-outline-variant/20 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">pending_actions</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-black mb-1">Laporan Menunggu</p>
                <h3 class="text-5xl font-black italic tracking-tighter text-[#004d4d]">{{ $laporanMenunggu }}</h3>
            </div>
        </div>

        <div class="bg-[#004d4d] p-7 rounded-[2rem] shadow-sm flex flex-col justify-between h-36 relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">edit_note</span>
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white backdrop-blur-sm relative z-10">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">edit_note</span>
            </div>
            <div class="relative z-10">
                <p class="text-[#7fffd4] uppercase tracking-widest text-[10px] font-black mb-1">Penilaian Selesai</p>
                <h3 class="text-5xl font-black italic tracking-tighter text-white">{{ $penilaianSelesai }}</h3>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Quick Actions --}}
        <div class="bg-white p-7 rounded-[2rem] border border-outline-variant/20 shadow-sm">
            <h4 class="text-[10px] font-black text-outline mb-5 uppercase tracking-widest">Aksi Cepat</h4>
            <div class="space-y-3">
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-surface-container-low/40 hover:bg-emerald-50 hover:border-[#2dce89] border border-transparent transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center group-hover:bg-[#004d4d] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-lg text-outline group-hover:text-white">description</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-[#004d4d]">Verifikasi Laporan</p>
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-500 mt-0.5">{{ $laporanMenunggu }} menunggu review</p>
                    </div>
                    <span class="material-symbols-outlined text-outline group-hover:text-[#2dce89]">chevron_right</span>
                </a>

                <a href="{{ route('penilaian.index') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-surface-container-low/40 hover:bg-emerald-50 hover:border-[#2dce89] border border-transparent transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center group-hover:bg-[#004d4d] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-lg text-outline group-hover:text-white">edit_note</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-[#004d4d]">Input Nilai</p>
                        <p class="text-[10px] font-black uppercase tracking-widest text-outline/60 mt-0.5">Form penilaian mahasiswa</p>
                    </div>
                    <span class="material-symbols-outlined text-outline group-hover:text-[#2dce89]">chevron_right</span>
                </a>

                <a href="{{ route('laporan.index') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-surface-container-low/40 hover:bg-emerald-50 hover:border-[#2dce89] border border-transparent transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center group-hover:bg-[#004d4d] transition-colors shrink-0">
                        <span class="material-symbols-outlined text-lg text-outline group-hover:text-white">folder_open</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-[#004d4d]">Semua Laporan</p>
                        <p class="text-[10px] font-black uppercase tracking-widest text-outline/60 mt-0.5">Riwayat laporan & tugas</p>
                    </div>
                    <span class="material-symbols-outlined text-outline group-hover:text-[#2dce89]">chevron_right</span>
                </a>
            </div>
        </div>

        {{-- Supervisi Aktif (Kini menggunakan Tabel Baku) --}}
        <div class="lg:col-span-2 bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm overflow-hidden flex flex-col">
            <div class="px-7 py-5 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/40">
                <h4 class="text-[10px] font-black text-outline uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">school</span> Supervisi Aktif
                </h4>
                <span class="text-[10px] font-black text-[#2dce89] bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-widest">{{ $supervisiList->count() }} mahasiswa</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low/20 border-b border-outline-variant/10">
                        <tr>
                            <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-12">#</th>
                            <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Mahasiswa</th>
                            <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Mata Kuliah</th>
                            <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Status Laporan Terbaru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($supervisiList as $idx => $supervisi)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-7 py-4 text-[10px] font-black text-outline/40">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-7 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-[#004d4d] flex items-center justify-center text-xs font-black shrink-0">
                                        {{ strtoupper(substr($supervisi->mahasiswa->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-sm text-on-surface">{{ $supervisi->mahasiswa->nama ?? '-' }}</p>
                                        <p class="text-[10px] font-bold text-outline mt-0.5">{{ $supervisi->mahasiswa->nim ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-7 py-4">
                                <span class="inline-flex px-3 py-1.5 bg-purple-50 text-purple-700 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                    {{ $supervisi->mataKuliah->nama_matkul ?? '-' }}
                                </span>
                            </td>
                            <td class="px-7 py-4 text-center">
                                @php
                                    $laporanMhs = $laporanTerbaru->firstWhere('mahasiswa_id', $supervisi->mahasiswa_id);
                                    $statusColor = 'bg-gray-100 text-gray-400';
                                    $statusLabel = 'Belum Ada';
                                    if ($laporanMhs) {
                                        $statusColor = match($laporanMhs->status_verifikasi) {
                                            'disetujui' => 'bg-emerald-50 text-emerald-600',
                                            'ditolak'   => 'bg-rose-50 text-rose-600',
                                            default     => 'bg-amber-50 text-amber-600',
                                        };
                                        $statusLabel = match($laporanMhs->status_verifikasi) {
                                            'disetujui' => 'Disetujui',
                                            'ditolak'   => 'Ditolak',
                                            default     => 'Menunggu',
                                        };
                                    }
                                @endphp
                                <span class="inline-flex px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-7 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline-variant/40 block mb-3">school</span>
                                <p class="text-sm font-black italic text-outline/50">Belum ada mahasiswa yang disupervisi.</p>
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