@extends('layouts.app')

@section('title', 'Verifikasi Supervisi')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
                Verifikasi <span class="text-[#2dce89]">Supervisi Mingguan</span>
            </h1>
            <p class="text-sm text-gray-400 mt-1">Daftar laporan supervisi mahasiswa yang diajukan kepada Anda</p>
        </div>
    </div>

    {{-- STATS --}}
    @php
        $menunggu  = $laporans->where('status', 'diajukan')->count();
        $disetujui = $laporans->where('status', 'disetujui')->count();
        $ditolak   = $laporans->where('status', 'ditolak')->count();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-500">pending</span>
            </div>
            <div>
                <p class="text-2xl font-black text-[#004d4d]">{{ $menunggu }}</p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Menunggu</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            </div>
            <div>
                <p class="text-2xl font-black text-[#004d4d]">{{ $disetujui }}</p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Disetujui</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-400">cancel</span>
            </div>
            <div>
                <p class="text-2xl font-black text-[#004d4d]">{{ $ditolak }}</p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Ditolak</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#004d4d] text-white text-xs">
                        <th class="px-6 py-4 text-left font-black italic uppercase tracking-wide">Mahasiswa</th>
                        <th class="px-6 py-4 text-left font-black italic uppercase tracking-wide">Mata Kuliah</th>
                        <th class="px-6 py-4 text-center font-black italic uppercase tracking-wide">Minggu</th>
                        <th class="px-6 py-4 text-center font-black italic uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-center font-black italic uppercase tracking-wide">Diajukan</th>
                        <th class="px-6 py-4 text-center font-black italic uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($laporans as $laporan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#004d4d] flex items-center justify-center text-white font-black text-xs flex-shrink-0">
                                    {{ strtoupper(substr($laporan->mahasiswa->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $laporan->mahasiswa->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $laporan->mahasiswa->nim ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-700">{{ $laporan->matkul->nama_matkul ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $laporan->proyek->judul_proyek ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#004d4d]/10 text-[#004d4d] font-black text-sm">
                                {{ $laporan->minggu_ke }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($laporan->status === 'diajukan')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-black">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                                    Menunggu
                                </span>
                            @elseif($laporan->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-black">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                    Disetujui
                                </span>
                            @elseif($laporan->status === 'ditolak')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-500 text-xs font-black">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-400 text-xs font-black">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-gray-400">
                            {{ $laporan->updated_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($laporan->pdf_path)
                                <a href="{{ Storage::url($laporan->pdf_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-xs font-bold hover:bg-gray-200 transition">
                                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                    PDF
                                </a>
                                @endif
                                @if($laporan->status === 'diajukan')
                                <button onclick="document.getElementById('modal-verif-{{ $laporan->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#004d4d] text-white text-xs font-bold hover:bg-[#003d3d] transition">
                                    <span class="material-symbols-outlined text-sm">rate_review</span>
                                    Verifikasi
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <span class="material-symbols-outlined text-5xl text-gray-200 block mb-3">inbox</span>
                            <p class="text-gray-400 font-bold">Belum ada laporan yang masuk</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODALS — di luar tabel ===== --}}
@foreach($laporans as $laporan)
@if($laporan->status === 'diajukan')
<div id="modal-verif-{{ $laporan->id }}"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#004d4d]">Verifikasi Laporan</h3>
            <button onclick="document.getElementById('modal-verif-{{ $laporan->id }}').classList.add('hidden')"
                    class="text-gray-300 hover:text-gray-500 transition">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 mb-5 space-y-1">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Mahasiswa</p>
            <p class="font-black text-gray-800">{{ $laporan->mahasiswa->user->name ?? '-' }}</p>
            <p class="text-xs text-gray-500">Minggu ke-{{ $laporan->minggu_ke }} — {{ $laporan->matkul->nama_matkul ?? '-' }}</p>
        </div>
        <form action="{{ route('supervisi_mingguan.verifikasi', $laporan->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">Keputusan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 cursor-pointer hover:border-emerald-400 transition">
                        <input type="radio" name="status" value="disetujui" class="accent-emerald-500">
                        <span class="text-sm font-bold text-emerald-700">Setujui</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-red-200 bg-red-50 cursor-pointer hover:border-red-400 transition">
                        <input type="radio" name="status" value="ditolak" class="accent-red-500">
                        <span class="text-sm font-bold text-red-600">Tolak</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">Catatan (opsional)</label>
                <textarea name="catatan_dosen" rows="3"
                    placeholder="Tambahkan catatan atau revisi untuk mahasiswa..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-[#7fffd4] resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-verif-{{ $laporan->id }}').classList.add('hidden')"
                        class="flex-1 px-4 py-3 rounded-2xl border border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-2xl bg-[#004d4d] text-white font-black text-sm hover:bg-[#003d3d] transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

@endsection