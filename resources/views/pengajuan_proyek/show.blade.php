@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
<div class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        {{-- Tombol back sesuai role --}}
        @if(auth()->user()->isMahasiswa())
            <a href="{{ route('dashboard') }}"
               class="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
        @else
            <a href="{{ route('pengajuan_proyek.index') }}"
               class="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
        @endif

        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Detail <span class="text-[#2dce89]">Pengajuan</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">{{ $pengajuan_proyek->kode_pengajuan }}</p>
        </div>
        <div class="ml-auto flex items-center gap-3">
            @if($pengajuan_proyek->status === 'approved')
                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-50 text-green-600 border border-green-100 rounded-2xl text-xs font-black uppercase">
                    <span class="material-symbols-outlined text-base">check_circle</span> Disetujui
                </span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('pengajuan_proyek.assign', $pengajuan_proyek) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-black uppercase text-white transition"
                   style="background-color: #004d4d;">
                    <span class="material-symbols-outlined text-base">group_add</span> Pilih Mahasiswa
                </a>
                @endif
            @elseif($pengajuan_proyek->status === 'rejected')
                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-500 border border-red-100 rounded-2xl text-xs font-black uppercase">
                    <span class="material-symbols-outlined text-base">cancel</span> Ditolak
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-50 text-amber-500 border border-amber-100 rounded-2xl text-xs font-black uppercase">
                    <span class="material-symbols-outlined text-base">pending</span> Menunggu Review
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- KOLOM KIRI (2/3) --}}
        <div class="col-span-2 space-y-6">

            {{-- Info Proyek --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2dce89]">info</span> Informasi Proyek
                </h2>
                <h3 class="text-2xl font-black text-[#004d4d] italic mb-2">{{ $pengajuan_proyek->judul_proyek }}</h3>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-6">
                    Diajukan oleh: {{ $pengajuan_proyek->manager->name ?? '-' }}
                </p>
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Mulai</p>
                        <p class="font-black text-[#004d4d]">{{ $pengajuan_proyek->tanggal_mulai->format('d M Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Selesai</p>
                        <p class="font-black text-[#004d4d]">{{ $pengajuan_proyek->tanggal_selesai->format('d M Y') }}</p>
                    </div>
                    {{-- Anggaran hanya tampil untuk non-mahasiswa --}}
                    @unless(auth()->user()->isMahasiswa())
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Anggaran</p>
                        <p class="font-black text-[#004d4d]">
                            {{ $pengajuan_proyek->anggaran ? 'Rp ' . number_format($pengajuan_proyek->anggaran, 0, ',', '.') : '-' }}
                        </p>
                    </div>
                    @endunless
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Kebutuhan</p>
                        <p class="font-black text-[#004d4d]">{{ $pengajuan_proyek->getTotalMahasiswa() }} Mahasiswa</p>
                    </div>
                </div>
                <div class="mb-5">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi</p>
                    <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">{{ $pengajuan_proyek->deskripsi }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tujuan</p>
                    <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">{{ $pengajuan_proyek->tujuan }}</p>
                </div>
            </div>

            {{-- Kebutuhan Tim --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2dce89]">groups</span> Kebutuhan Tim
                </h2>
                <div class="space-y-3">
                    @foreach($pengajuan_proyek->kebutuhan as $kebutuhan)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#7fffd4] text-base">school</span>
                            </div>
                            <span class="font-bold text-[#004d4d] text-sm">
                                {{ labelProdi($kebutuhan->prodi) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-700 border border-teal-100 rounded-xl text-[10px] font-black">
                            {{ $kebutuhan->jumlah_mahasiswa }} mahasiswa
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Mahasiswa Ditugaskan --}}
            @if($pengajuan_proyek->status === 'approved')
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2dce89]">manage_accounts</span> Mahasiswa Ditugaskan
                </h2>
                @if($pengajuan_proyek->mahasiswa->isEmpty())
                    <div class="text-center py-8 text-gray-300">
                        <span class="material-symbols-outlined text-4xl">person_search</span>
                        <p class="text-sm mt-2 font-bold">Belum ada mahasiswa yang ditugaskan.</p>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('pengajuan_proyek.assign', $pengajuan_proyek) }}"
                           class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-2xl text-xs font-black uppercase text-white transition"
                           style="background-color: #004d4d;">
                            <span class="material-symbols-outlined text-base">group_add</span> Pilih Sekarang
                        </a>
                        @endif
                    </div>
                @else
                    @php
                        $mahasiswaLoginId = auth()->user()->mahasiswa?->id;
                    @endphp
                    <div class="space-y-3">
                        @foreach($pengajuan_proyek->mahasiswa as $mhs)
                        @php $isMe = $mhs->id === $mahasiswaLoginId; @endphp
                        <div class="flex items-center gap-4 p-4 rounded-2xl transition-all
                            {{ $isMe ? 'bg-teal-50 border-2 border-teal-200' : 'bg-gray-50' }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white flex-shrink-0"
                                 style="background-color: {{ $isMe ? '#2dce89' : '#004d4d' }};">
                                {{ strtoupper(substr($mhs->nama, 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-sm text-[#004d4d] flex items-center gap-2">
                                    {{ $mhs->nama }}
                                    @if($isMe)
                                    <span class="text-[9px] font-black bg-teal-500 text-white px-2 py-0.5 rounded-full uppercase tracking-widest">Anda</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">{{ $mhs->nim }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest"
                                  style="background-color: #e6f7f4; color: #004d4d;">
                                {{ labelProdi($mhs->pivot->prodi) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

        </div>

        {{-- KOLOM KANAN (1/3) --}}
        <div class="space-y-6">

            {{-- Catatan Admin --}}
            @if($pengajuan_proyek->catatan_admin)
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2dce89]">comment</span> Catatan Admin
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">{{ $pengajuan_proyek->catatan_admin }}</p>
                @if($pengajuan_proyek->diprosesOleh)
                <p class="text-[10px] text-gray-400 font-bold mt-3 uppercase tracking-widest">
                    Oleh: {{ $pengajuan_proyek->diprosesOleh->name }}
                    · {{ $pengajuan_proyek->diproses_at?->format('d M Y') }}
                </p>
                @endif
            </div>
            @endif

            {{-- Aksi Admin --}}
            @if(auth()->user()->isAdmin() && $pengajuan_proyek->status === 'pending')
            <div class="bg-[#004d4d] rounded-[2rem] p-6 space-y-3" x-data="{ showForm: false, action: '' }">
                <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-4">Tindakan Admin</p>
                <div x-show="!showForm" class="space-y-3">
                    <button @click="showForm = true; action = 'approve'"
                            class="w-full py-3.5 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all">
                        <span class="material-symbols-outlined text-sm align-middle mr-1">check_circle</span> SETUJUI
                    </button>
                    <button @click="showForm = true; action = 'reject'"
                            class="w-full py-3.5 bg-red-500/20 text-red-300 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-red-500 hover:text-white transition-all">
                        <span class="material-symbols-outlined text-sm align-middle mr-1">cancel</span> TOLAK
                    </button>
                </div>
                <div x-show="showForm" x-cloak>
                    <form :action="action === 'approve'
                        ? '{{ route('pengajuan_proyek.approve', $pengajuan_proyek) }}'
                        : '{{ route('pengajuan_proyek.reject', $pengajuan_proyek) }}'"
                        method="POST">
                        @csrf @method('PATCH')
                        <label class="block text-[10px] font-black text-[#7fffd4] uppercase tracking-widest mb-2">
                            Catatan <span x-text="action === 'reject' ? '(Wajib)' : '(Opsional)'"></span>
                        </label>
                        <textarea name="catatan_admin" rows="3"
                                  class="w-full px-4 py-3 bg-white/10 text-white border border-white/20 rounded-2xl outline-none text-sm resize-none placeholder:text-white/30 focus:ring-2 focus:ring-[#7fffd4]"
                                  placeholder="Tulis catatan..."></textarea>
                        <div class="flex gap-2 mt-3">
                            <button type="button" @click="showForm = false"
                                    class="flex-1 py-3 bg-white/10 text-white/60 rounded-2xl text-xs font-black uppercase hover:bg-white/20 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 py-3 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase hover:scale-[1.02] transition-all">
                                Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Info Card --}}
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">Info Pengajuan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode</span>
                        <span class="text-xs font-black text-[#004d4d]">{{ $pengajuan_proyek->kode_pengajuan }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dibuat</span>
                        <span class="text-xs font-bold text-gray-600">{{ $pengajuan_proyek->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Manager</span>
                        <span class="text-xs font-bold text-gray-600">{{ $pengajuan_proyek->manager->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dosen Pengampu</span>
                        <span class="text-xs font-bold text-gray-600">
                            {{ $pengajuan_proyek->dosenPengampu->user->name ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection