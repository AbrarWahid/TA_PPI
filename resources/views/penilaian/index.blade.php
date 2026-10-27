@extends('layouts.app')
@section('title', 'Data Penilaian')
@section('content')
@php $user = auth()->user(); @endphp

{{-- HEADING --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Data <span class="text-[#2dce89]">Penilaian</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Rekap nilai mahasiswa PBL</p>
    </div>
    @if($user->isManager() || $user->isDosen())
    <a href="{{ auth()->user()->isManager() ? route('penilaian.manager.create') : route('penilaian.dosen.create') }}"
        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition">
        <span class="material-symbols-outlined text-base">add</span>
        Input Nilai
    </a>
    @endif
</div>

{{-- FILTER BAR --}}
@php
    $proyekList = $penilaian->map(fn($p) => $p->mahasiswa?->proyekAktif())->filter()->unique('id')->sortBy('judul_proyek')->values();
@endphp
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm px-6 py-4 mb-5">
    <div class="flex flex-wrap items-center gap-3">

        {{-- Search nama / NIM --}}
        <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">search</span>
            <input type="text" id="searchNilai" oninput="applyFilter()" placeholder="Cari nama atau NIM..."
                class="w-full pl-9 pr-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-xs font-medium text-gray-700 focus:outline-none focus:border-[#004d4d]">
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-gray-200"></div>

        {{-- Filter Status Nilai --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status:</span>
            @foreach(['semua'=>'Semua','lengkap'=>'Lengkap','sebagian'=>'Sebagian','belum'=>'Belum Dinilai'] as $val=>$lbl)
            <button onclick="setFilter('nilai','{{ $val }}')"
                data-filter="nilai" data-value="{{ $val }}"
                class="filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all
                {{ $val==='semua' ? 'bg-[#004d4d] text-[#7fffd4] border-[#004d4d]' : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#004d4d] hover:text-[#004d4d]' }}">
                {{ $lbl }}
            </button>
            @endforeach
        </div>

        @if($proyekList->count() > 1)
        {{-- Divider --}}
        <div class="h-6 w-px bg-gray-200"></div>

        {{-- Filter Proyek --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Proyek:</span>
            <select id="filterProyek" onchange="applyFilter()"
                class="py-2 pl-3 pr-8 bg-gray-50 rounded-xl border border-gray-200 text-xs font-black text-gray-700 focus:outline-none focus:border-[#004d4d] appearance-none cursor-pointer">
                <option value="semua">Semua Proyek</option>
                @foreach($proyekList as $proyek)
                <option value="{{ $proyek->id }}">{{ Str::limit($proyek->judul_proyek, 40) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Reset & Counter --}}
        <div class="ml-auto flex items-center gap-3">
            <span id="filterCounter" class="hidden text-[10px] font-black text-gray-400">
                <span id="visibleCount">0</span> data ditemukan
            </span>
            <button onclick="resetFilter()" id="btnReset"
                class="hidden flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-all">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
            </button>
        </div>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400" id="totalLabel">
            Total: {{ $penilaian->count() }} Data
        </span>
        @if($user->isManager())
        <span class="text-[10px] font-black px-3 py-1.5 rounded-full bg-teal-50 text-[#004d4d] uppercase tracking-widest">Porsi Anda: 55%</span>
        @elseif($user->isDosen())
        <span class="text-[10px] font-black px-3 py-1.5 rounded-full bg-teal-50 text-[#004d4d] uppercase tracking-widest">Porsi Anda: 45%</span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">No</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Mahasiswa</th>
                    <th class="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Proyek</th>
                    @if(!$user->isMahasiswa())
                    <th class="text-center px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Manager<br><span class="normal-case font-medium text-gray-300">55%</span></th>
                    <th class="text-center px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Dosen<br><span class="normal-case font-medium text-gray-300">45%</span></th>
                    @endif
                    <th class="text-center px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Nilai Akhir</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="nilaiTbody">
                @forelse($penilaian as $p)
                @php
                    $proyekAktif = $p->mahasiswa?->proyekAktif();
                    // Status nilai: lengkap = ada manager+dosen, sebagian = salah satu, belum = keduanya null
                    $hasManager = $p->nilai_manager !== null;
                    $hasDosen   = $p->nilai_dosen   !== null;
                    $statusNilai = ($hasManager && $hasDosen) ? 'lengkap' : (($hasManager || $hasDosen) ? 'sebagian' : 'belum');
                @endphp
                <tr class="nilai-row hover:bg-teal-50/30 transition-colors"
                    data-name="{{ strtolower($p->mahasiswa->nama ?? '') }}"
                    data-nim="{{ $p->mahasiswa->nim ?? '' }}"
                    data-proyek-id="{{ $proyekAktif?->id ?? '' }}"
                    data-status-nilai="{{ $statusNilai }}">

                    <td class="px-6 py-4 text-[10px] font-black text-gray-300 row-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-black bg-[#004d4d]">
                                {{ strtoupper(substr($p->mahasiswa->nama ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-black text-[#004d4d] text-sm">{{ $p->mahasiswa->nama ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400 font-medium">{{ $p->mahasiswa->nim ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 font-medium">{{ $proyekAktif?->judul_proyek ?? '-' }}</td>

                    @if(!$user->isMahasiswa())
                    <td class="px-6 py-4 text-center">
                        @if($p->nilai_manager !== null)
                            <span class="font-black text-[#004d4d]">{{ number_format($p->nilai_manager, 1) }}</span>
                        @else
                            <span class="text-[10px] text-gray-300 italic font-medium">Belum diisi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($p->nilai_dosen !== null)
                            <span class="font-black text-[#004d4d]">{{ number_format($p->nilai_dosen, 1) }}</span>
                        @else
                            <span class="text-[10px] text-gray-300 italic font-medium">Belum diisi</span>
                        @endif
                    </td>
                    @endif

                    <td class="px-6 py-4 text-center">
                        @if($p->nilai_akhir)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black text-white"
                            style="background-color: {{ $p->getGradeColor() }}">
                            {{ number_format($p->nilai_akhir, 1) }} · {{ $p->getGrade() }}
                        </span>
                        @elseif($statusNilai === 'sebagian')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-500">
                            <span class="material-symbols-outlined text-sm">pending</span> Sebagian
                        </span>
                        @else
                        <span class="text-[10px] text-gray-300 italic">Belum dinilai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('penilaian.show', $p->id) }}"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black bg-teal-50 text-[#004d4d] hover:bg-teal-100 transition w-fit uppercase tracking-wide">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">assignment</span>
                        <p class="text-sm text-gray-400 font-medium">Belum ada data penilaian</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Empty filter state --}}
    <div id="emptyFilter" class="hidden px-6 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-200 block mb-3">search_off</span>
        <p class="font-black italic text-gray-300 text-sm">Tidak ada data penilaian ditemukan.</p>
        <button onclick="resetFilter()" class="mt-4 text-[10px] font-black text-[#004d4d] hover:underline uppercase tracking-widest">
            Reset Filter
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
const activeFilters = { nilai: 'semua' };

function setFilter(type, value) {
    activeFilters[type] = value;
    document.querySelectorAll(`[data-filter="${type}"]`).forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.className = 'filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ' +
            (isActive
                ? 'bg-[#004d4d] text-[#7fffd4] border-[#004d4d]'
                : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#004d4d] hover:text-[#004d4d]');
    });
    applyFilter();
}

function applyFilter() {
    const q         = document.getElementById('searchNilai').value.toLowerCase().trim();
    const proyekId  = document.getElementById('filterProyek')?.value ?? 'semua';
    const rows      = document.querySelectorAll('.nilai-row');
    let visible     = 0;

    rows.forEach(r => {
        const matchSearch = !q || r.dataset.name.includes(q) || r.dataset.nim.includes(q);
        const matchNilai  = activeFilters.nilai === 'semua' || r.dataset.statusNilai === activeFilters.nilai;
        const matchProyek = proyekId === 'semua' || r.dataset.proyekId === proyekId;

        const show = matchSearch && matchNilai && matchProyek;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Nomor urut ulang
    let num = 1;
    rows.forEach(r => {
        if (r.style.display !== 'none') {
            r.querySelector('.row-num').textContent = String(num++).padStart(2, '0');
        }
    });

    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0);

    const isFiltered = q || activeFilters.nilai !== 'semua' || proyekId !== 'semua';
    document.getElementById('btnReset').classList.toggle('hidden', !isFiltered);
    document.getElementById('filterCounter').classList.toggle('hidden', !isFiltered);
    document.getElementById('visibleCount').textContent = visible;
}

function resetFilter() {
    document.getElementById('searchNilai').value = '';
    const elProyek = document.getElementById('filterProyek');
    if (elProyek) elProyek.value = 'semua';
    setFilter('nilai', 'semua');
}
</script>
@endpush