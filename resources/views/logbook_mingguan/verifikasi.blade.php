@extends('layouts.app')

@section('title', 'Verifikasi Logbook Mingguan')

@section('content')
<div class="flex-1 flex flex-col space-y-6" x-data="{ tab: 'semua' }">

    @php
        $jumlahMenunggu  = $rekapList->where('status', 'diajukan')->count();
        $jumlahDisetujui = $rekapList->where('status', 'disetujui')->count();
        $jumlahDitolak   = $rekapList->where('status', 'ditolak')->count();
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black italic uppercase text-[#004d4d] tracking-tighter">
                Verifikasi <span class="text-[#2dce89]">Logbook Mingguan</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar rekap logbook mingguan mahasiswa yang perlu diverifikasi.</p>
        </div>
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-white border border-gray-100 px-4 py-2 rounded-2xl shadow-sm self-start md:self-auto">
            Total: {{ $rekapList->count() }} Pengajuan
        </span>
    </div>

    {{-- STAT CARDS --}}
    <section class="grid grid-cols-3 gap-5">

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">pending</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold">Menunggu</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-[#004d4d]">{{ $jumlahMenunggu }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between h-36">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">check_circle</span>
            </div>
            <div>
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold">Disetujui</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-[#004d4d]">{{ $jumlahDisetujui }}</h3>
            </div>
        </div>

        <div class="bg-[#004d4d] p-6 rounded-[2rem] shadow-sm flex flex-col justify-between h-36 relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">cancel</span>
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white relative z-10">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">cancel</span>
            </div>
            <div class="relative z-10">
                <p class="text-[#7fffd4] uppercase tracking-widest text-[10px] font-bold">Ditolak</p>
                <h3 class="text-3xl font-black italic tracking-tighter text-white">{{ $jumlahDitolak }}</h3>
            </div>
        </div>

    </section>

    {{-- FILTER TAB --}}
    <div class="flex gap-1 bg-white border border-gray-100 rounded-2xl p-1.5 shadow-sm w-fit">
        <button @click="tab = 'semua'"
            :class="tab === 'semua' ? 'bg-[#004d4d] text-white shadow-sm' : 'text-slate-400 hover:text-[#004d4d]'"
            class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all">
            Semua ({{ $rekapList->count() }})
        </button>
        <button @click="tab = 'menunggu'"
            :class="tab === 'menunggu' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-400 hover:text-amber-600'"
            class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all">
            Menunggu ({{ $jumlahMenunggu }})
        </button>
        <button @click="tab = 'disetujui'"
            :class="tab === 'disetujui' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-400 hover:text-emerald-600'"
            class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all">
            Disetujui ({{ $jumlahDisetujui }})
        </button>
        <button @click="tab = 'ditolak'"
            :class="tab === 'ditolak' ? 'bg-red-500 text-white shadow-sm' : 'text-slate-400 hover:text-red-500'"
            class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all">
            Ditolak ({{ $jumlahDitolak }})
        </button>
    </div>

    {{-- LIST --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">

        {{-- TABLE HEADER --}}
        <div class="px-7 py-5 border-b border-gray-100 flex justify-between items-center">
            <h4 class="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">rate_review</span> Daftar Pengajuan
            </h4>
        </div>

        @if($rekapList->isEmpty())
        <div class="px-7 py-16 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-300 block mb-2">task_alt</span>
            <p class="text-sm font-bold text-slate-400">Tidak ada rekap yang perlu diverifikasi.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($rekapList as $rekap)
            @php
                $tabStatus = match($rekap->status) {
                    'diajukan'  => 'menunggu',
                    'disetujui' => 'disetujui',
                    'ditolak'   => 'ditolak',
                    default     => 'semua',
                };
                $badgeClass = match($rekap->status) {
                    'diajukan'  => 'bg-amber-50 text-amber-600 border border-amber-100',
                    'disetujui' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                    'ditolak'   => 'bg-red-50 text-red-500 border border-red-100',
                    default     => 'bg-gray-100 text-gray-500',
                };
                $badgeLabel = match($rekap->status) {
                    'diajukan'  => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak'   => 'Ditolak',
                    default     => 'Draft',
                };
            @endphp

            <div class="px-7 py-5" x-data="{ open: false }"
                 x-show="tab === 'semua' || tab === '{{ $tabStatus }}'">

                <div class="flex items-center justify-between gap-4">

                    {{-- Avatar + Info --}}
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-11 h-11 rounded-2xl bg-[#004d4d] flex items-center justify-center text-white text-sm font-black shrink-0">
                            {{ strtoupper(substr($rekap->mahasiswa->user->name ?? 'M', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-sm text-[#004d4d]">
                                {{ $rekap->mahasiswa->user->name ?? '-' }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wide">
                                {{ $rekap->proyek->judul_proyek ?? '-' }}
                                &nbsp;&middot;&nbsp; Minggu ke-{{ $rekap->minggu_ke }}
                                &nbsp;&middot;&nbsp; {{ $rekap->diajukan_at?->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Badge + Aksi --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>

                        @if($rekap->pdf_path)
                        <a href="{{ route('logbook_mingguan.show', $rekap->id) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-[#004d4d] border border-gray-100 hover:border-[#004d4d] bg-white px-3 py-2 rounded-xl transition-all">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                            PDF
                        </a>
                        @endif

                        @if($rekap->status === 'diajukan')
                        <button @click="open = !open"
                            class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest bg-[#004d4d] hover:bg-[#003d3d] text-[#7fffd4] px-4 py-2 rounded-xl transition-all shadow-sm hover:-translate-y-0.5">
                            <span class="material-symbols-outlined text-sm">rate_review</span>
                            Verifikasi
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Catatan jika sudah diverifikasi --}}
                @if($rekap->status !== 'diajukan' && $rekap->catatan_dosen)
                <div class="mt-4 ml-15 pl-4 border-l-2 border-[#2dce89]">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Catatan Manager</p>
                    <p class="text-xs font-medium text-slate-600">{{ $rekap->catatan_dosen }}</p>
                </div>
                @endif

                {{-- Form Verifikasi --}}
                @if($rekap->status === 'diajukan')
                <div x-show="open" x-transition class="mt-4 ml-15 bg-gray-50 border border-gray-100 rounded-2xl p-5">
                    <form method="POST" action="{{ route('logbook_mingguan.verifikasi', $rekap->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Catatan (opsional)</label>
                            <textarea name="catatan_dosen" rows="2"
                                class="w-full text-sm font-medium border border-gray-100 bg-white rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-[#7fffd4] text-[#004d4d] placeholder:text-slate-300"
                                placeholder="Tulis catatan untuk mahasiswa..."></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="aksi" value="disetujui"
                                class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                ACC / Setujui
                            </button>
                            <button type="submit" name="aksi" value="ditolak"
                                class="inline-flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                Tolak
                            </button>
                            <button type="button" @click="open = false"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 px-4 py-2.5 rounded-xl border border-gray-100 hover:border-gray-200 transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection