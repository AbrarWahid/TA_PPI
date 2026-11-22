@extends('layouts.app')
@section('title', 'Ekspor Transkrip')
@section('content')

<div class="max-w-[1400px] mx-auto space-y-6">
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
        <div>
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">KOORDINATOR PROGRAM STUDI</p>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">Ekspor Transkrip PPI</h1>
            <p class="text-xs text-[#6a6f7c] mt-1">Pilih mahasiswa dari program studi Anda untuk mencetak transkrip resmi.</p>
        </div>
        <span class="ps-badge">{{ count($mahasiswas) }} mahasiswa</span>
    </div>

    <div class="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3.5">NIM</th>
                        <th class="px-5 py-3.5">Nama Mahasiswa</th>
                        <th class="px-5 py-3.5 text-center">Kelas</th>
                        <th class="px-5 py-3.5 text-center">Status Logbook</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f4f4f5]">
                    @forelse($mahasiswas as $m)
                    <tr>
                        <td class="px-5 py-3.5 font-mono text-[#0b0b0c]">{{ $m->nim }}</td>
                        <td class="px-5 py-3.5 font-medium text-[#0b0b0c]">{{ $m->nama_mhs }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-mono px-2 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] text-[11px]">{{ $m->kelas ?: '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($m->nilai_logbook_lengkap)
                                <span class="font-mono px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[#15803d] text-[10px] font-medium">Lengkap (20 Minggu)</span>
                            @else
                                <span class="font-mono px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-[#b45309] text-[10px] font-medium">Belum Lengkap (20 Minggu)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($m->nilai_logbook_lengkap)
                                <a target="_blank" href="{{ route('external.kaprodi.transkrip', $m->nim) }}" class="ps-btn-primary text-xs py-1.5 px-3">
                                    <span class="material-symbols-outlined text-[15px]">picture_as_pdf</span>
                                    <span>Buka / Cetak PDF</span>
                                </a>
                            @else
                                <button disabled class="inline-flex items-center gap-1.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#a1a1aa] px-3 py-1.5 rounded-lg text-xs cursor-not-allowed">
                                    <span class="material-symbols-outlined text-[14px]">lock</span>
                                    <span>Terkunci</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-[#6a6f7c]">Belum ada data mahasiswa pada program studi ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
