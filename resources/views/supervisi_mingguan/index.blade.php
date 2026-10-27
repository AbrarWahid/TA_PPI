@extends('layouts.app')
@section('title', 'Supervisi Mingguan')

@section('content')
@php $user = auth()->user(); @endphp

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold">
    <span class="material-symbols-outlined text-lg">check_circle</span>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3 rounded-2xl text-sm font-bold">
    <span class="material-symbols-outlined text-lg">error</span>
    {{ session('error') }}
</div>
@endif

{{-- HEADER --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-[#004d4d]">Rekap Supervisi Mingguan</h1>
        <p class="text-sm text-gray-400 mt-1">Rekap dan pengajuan laporan supervisi per minggu</p>
    </div>
    @if($user->isMahasiswa())
    <a href="{{ route('supervisi_harian.index') }}"
       class="flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 text-[#004d4d] rounded-2xl font-black text-sm hover:bg-gray-50 transition-all shadow-sm">
        <span class="material-symbols-outlined text-lg">edit_note</span>
        Lihat Harian
    </a>
    @endif
</div>

{{-- ============================================================
     FORM GENERATE — hanya mahasiswa, hanya jika ada minggu belum direkap
     ============================================================ --}}
@if($user->isMahasiswa() && count($mingguBelumRekap) > 0)
<div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-6">
    <p class="text-sm font-black text-amber-700 mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">summarize</span>
        Minggu Belum Direkap ({{ count($mingguBelumRekap) }} item)
    </p>

    @foreach($mingguBelumRekap as $item)
    @php
        /** @var \App\Models\SupervisiMatkul $sm */
        $sm = $item['supervisiMatkul'];
    @endphp
    <div class="bg-white rounded-2xl p-5 mb-4 border border-amber-100" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-black text-[#004d4d] text-sm">Minggu ke-{{ $item['minggu_ke'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $sm->mataKuliah->nama_matkul ?? '-' }}
                    &mdash;
                    {{ $sm->dosen->nama_dosen ?? ($sm->dosen->user->name ?? '-') }}
                </p>
            </div>
            <button @click="open = !open"
                    class="flex items-center gap-2 px-4 py-2 bg-[#004d4d] text-white rounded-xl text-xs font-black hover:bg-[#003d3d] transition">
                <span class="material-symbols-outlined text-sm" x-text="open ? 'close' : 'add'">add</span>
                <span x-text="open ? 'Tutup' : 'Generate'">Generate</span>
            </button>
        </div>

        <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-amber-100">
            <form action="{{ route('supervisi_mingguan.generate') }}" method="POST" class="space-y-4">
                @csrf
                {{-- Kirim supervisi_matkul_id & minggu_ke — BUKAN pengajuan_proyek_id --}}
                <input type="hidden" name="supervisi_matkul_id" value="{{ $sm->id }}">
                <input type="hidden" name="minggu_ke" value="{{ $item['minggu_ke'] }}">

                {{-- Info ringkas —  read-only, tidak perlu user pilih lagi --}}
                <div class="bg-gray-50 rounded-xl px-4 py-3 space-y-1">
                    <p class="text-xs font-black uppercase text-[#004d4d] tracking-wide">Detail Laporan</p>
                    <p class="text-sm font-bold text-gray-700">
                        {{ $sm->mataKuliah->nama_matkul ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Dosen: {{ $sm->dosen->nama_dosen ?? ($sm->dosen->user->name ?? '-') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Minggu ke-{{ $item['minggu_ke'] }}
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-[#004d4d] text-white rounded-xl font-black text-sm hover:bg-[#003d3d] transition flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    Generate PDF & Ajukan ke Dosen
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Info jika mahasiswa tapi tidak ada data minggu belum rekap & tidak ada rekap sama sekali --}}
@if($user->isMahasiswa() && count($mingguBelumRekap) === 0 && $rekapList->isEmpty())
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-start gap-3">
    <span class="material-symbols-outlined text-blue-400 text-xl mt-0.5">info</span>
    <div>
        <p class="text-sm font-bold text-blue-700">Belum ada log harian</p>
        <p class="text-xs text-blue-500 mt-0.5">Isi supervisi harian terlebih dahulu, lalu kembali ke sini untuk generate rekap mingguan.</p>
    </div>
</div>
@endif

{{-- ============================================================
     TABEL REKAP
     ============================================================ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <p class="font-black text-[#004d4d] text-sm uppercase tracking-widest">Daftar Rekap</p>
        <span class="text-xs text-gray-400">{{ $rekapList->count() }} laporan</span>
    </div>

    @if($rekapList->isEmpty())
        <div class="py-16 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-200">summarize</span>
            <p class="text-gray-400 font-bold mt-3">Belum ada rekap supervisi mingguan</p>
            @if($user->isMahasiswa())
            <p class="text-xs text-gray-400 mt-1">Isi supervisi harian terlebih dahulu, lalu generate rekap di sini</p>
            @endif
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#004d4d] text-[10px] uppercase tracking-widest text-white font-black italic">
                    <th class="px-6 py-3 text-center">Minggu</th>
                    @if(!$user->isMahasiswa())
                    <th class="px-6 py-3 text-left">Mahasiswa</th>
                    @endif
                    <th class="px-6 py-3 text-left">Mata Kuliah</th>
                    <th class="px-6 py-3 text-left">Dosen</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Catatan Dosen</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($rekapList as $rekap)
                @php
                    $statusCfg = match($rekap->status) {
                        'diajukan'  => ['cls' => 'bg-amber-50 text-amber-600',     'dot' => 'bg-amber-400',   'label' => 'Diajukan'],
                        'disetujui' => ['cls' => 'bg-emerald-50 text-emerald-600', 'dot' => 'bg-emerald-400', 'label' => 'Disetujui'],
                        'ditolak'   => ['cls' => 'bg-red-50 text-red-500',         'dot' => 'bg-red-400',     'label' => 'Ditolak'],
                        default     => ['cls' => 'bg-gray-100 text-gray-400',      'dot' => 'bg-gray-300',    'label' => 'Draft'],
                    };

                    // Ambil nama matkul & dosen via supervisiMatkul (relasi baru)
                    $namaMatkul = $rekap->supervisiMatkul?->mataKuliah?->nama_matkul
                                  ?? $rekap->matkul?->nama_matkul
                                  ?? '-';

                    $namaDosen  = $rekap->supervisiMatkul?->dosen?->nama_dosen
                                  ?? $rekap->dosen?->nama_dosen
                                  ?? '-';
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Minggu --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#004d4d]/10 text-[#004d4d] font-black text-sm">
                            {{ $rekap->minggu_ke }}
                        </span>
                    </td>

                    {{-- Mahasiswa (hanya jika bukan role mahasiswa) --}}
                    @if(!$user->isMahasiswa())
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#004d4d] flex items-center justify-center text-white font-black text-xs flex-shrink-0">
                                {{ strtoupper(substr($rekap->mahasiswa?->nama_mahasiswa ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $rekap->mahasiswa?->nama_mahasiswa ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $rekap->mahasiswa?->nim ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    @endif

                    {{-- Mata Kuliah — via supervisiMatkul --}}
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-700 text-xs">{{ $namaMatkul }}</p>
                        @if($rekap->supervisiMatkul?->tahun_ajaran)
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            {{ $rekap->supervisiMatkul->tahun_ajaran }}
                            {{ $rekap->supervisiMatkul->semester ? '/ Sem. '.$rekap->supervisiMatkul->semester : '' }}
                        </p>
                        @endif
                    </td>

                    {{-- Dosen — via supervisiMatkul --}}
                    <td class="px-6 py-4 text-xs text-gray-600">
                        {{ $namaDosen }}
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black {{ $statusCfg['cls'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }} inline-block"></span>
                            {{ $statusCfg['label'] }}
                        </span>
                    </td>

                    {{-- Catatan Dosen --}}
                    <td class="px-6 py-4 text-center">
                        @if($rekap->catatan_dosen)
                        <span class="text-xs text-gray-500 italic max-w-[150px] inline-block truncate" title="{{ $rekap->catatan_dosen }}">
                            {{ $rekap->catatan_dosen }}
                        </span>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">

                            {{-- Tombol PDF / Show --}}
                            @if($rekap->pdf_path)
                            <a href="{{ route('supervisi_mingguan.show', $rekap) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-xs font-bold hover:bg-gray-200 transition">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                PDF
                            </a>
                            @endif

                            {{-- Tombol Batal — mahasiswa, status diajukan atau draft --}}
                            @if($user->isMahasiswa() && in_array($rekap->status, ['diajukan', 'draft']))
                            <form action="{{ route('supervisi_mingguan.batal', $rekap) }}" method="POST"
                                  onsubmit="return confirm('Batalkan rekap minggu ke-{{ $rekap->minggu_ke }} ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-red-50 text-red-500 text-xs font-bold hover:bg-red-100 transition">
                                    <span class="material-symbols-outlined text-sm">undo</span>
                                    Batal
                                </button>
                            </form>
                            @endif

                            {{-- Tombol Verifikasi — dosen, status diajukan --}}
                            @if(in_array($user->role_aktif ?? $user->role, ['dosen_pengampu', 'dosen']) && $rekap->status === 'diajukan')
                            <button onclick="document.getElementById('modal-verif-{{ $rekap->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#004d4d] text-white text-xs font-bold hover:bg-[#003d3d] transition">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                Verifikasi
                            </button>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ============================================================
     MODALS VERIFIKASI — di luar tabel, valid HTML
     ============================================================ --}}
@foreach($rekapList as $rekap)
@if(in_array($user->role_aktif ?? $user->role, ['dosen_pengampu', 'dosen']) && $rekap->status === 'diajukan')
@php
    $modalMatkul = $rekap->supervisiMatkul?->mataKuliah?->nama_matkul
                   ?? $rekap->matkul?->nama_matkul
                   ?? '-';
@endphp
<div id="modal-verif-{{ $rekap->id }}"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#004d4d]">Verifikasi Supervisi</h3>
            <button onclick="document.getElementById('modal-verif-{{ $rekap->id }}').classList.add('hidden')"
                    class="text-gray-300 hover:text-gray-500 transition">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Info rekap --}}
        <div class="bg-gray-50 rounded-2xl p-4 mb-5 space-y-1">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Mahasiswa</p>
            <p class="font-black text-gray-800">{{ $rekap->mahasiswa?->nama_mahasiswa ?? '-' }}</p>
            <p class="text-xs text-gray-500">
                Minggu ke-{{ $rekap->minggu_ke }}
                &mdash;
                {{ $modalMatkul }}
            </p>
            @if($rekap->pdf_path)
            <a href="{{ route('supervisi_mingguan.show', $rekap) }}"
               class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-[#004d4d] hover:underline">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                Buka PDF laporan
            </a>
            @endif
        </div>

        <form action="{{ route('supervisi_mingguan.verifikasi', $rekap) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')

            {{-- Pilihan keputusan --}}
            <div>
                <label class="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">Keputusan *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 cursor-pointer hover:border-emerald-400 transition">
                        <input type="radio" name="status" value="disetujui" required class="accent-emerald-500">
                        <span class="text-sm font-bold text-emerald-700">✓ Setujui</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-red-200 bg-red-50 cursor-pointer hover:border-red-400 transition">
                        <input type="radio" name="status" value="ditolak" class="accent-red-500">
                        <span class="text-sm font-bold text-red-600">✕ Tolak</span>
                    </label>
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">Catatan untuk Mahasiswa</label>
                <textarea name="catatan_dosen" rows="3"
                    placeholder="Opsional — tuliskan revisi atau komentar..."
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-[#7fffd4] resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-verif-{{ $rekap->id }}').classList.add('hidden')"
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