@extends('layouts.app')
@section('title', 'Rekap Penilaian')
@section('content')

@php
    $totalMhs     = $mahasiswaList->count();
    $sudahLengkap = $mahasiswaList->filter(fn($m) =>
        $m->penilaianManager->isNotEmpty() && $m->penilaianDosen->isNotEmpty()
    )->count();
    $belumLengkap = $totalMhs - $sudahLengkap;
@endphp

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Rekap <span class="text-[#2dce89]">Semua Penilaian</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Rekapitulasi nilai akhir per mahasiswa
        </p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-3 gap-5 mb-8">
    <div class="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36" style="background:#004d4d;">
        <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
        <div class="flex items-end justify-between">
            <p class="text-5xl font-black italic text-white tracking-tighter">{{ $totalMhs }}</p>
            <span class="text-xs font-black text-[#7fffd4]/60">Terdaftar</span>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">group</span>
    </div>

    <div class="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36" style="background:#2dce89;">
        <p class="text-white/70 text-[10px] font-black uppercase tracking-widest">Nilai Lengkap</p>
        <div class="flex items-end justify-between">
            <p class="text-5xl font-black italic text-white tracking-tighter">{{ $sudahLengkap }}</p>
            <span class="text-xs font-black text-white/60">Mahasiswa</span>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/20" style="font-size:100px;">task_alt</span>
    </div>

    <div class="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between bg-white border-2 border-outline-variant/20 h-36">
        <p class="text-secondary text-[10px] font-black uppercase tracking-widest">Belum Lengkap</p>
        <div class="flex items-end justify-between">
            <p class="text-5xl font-black italic text-primary tracking-tighter">{{ $belumLengkap }}</p>
            <span class="text-xs font-black text-outline">Mahasiswa</span>
        </div>
        <div class="w-full bg-surface-container-highest h-1.5 rounded-full overflow-hidden mt-2">
            @php $pct = $totalMhs > 0 ? round($sudahLengkap / $totalMhs * 100) : 0; @endphp
            <div class="h-full rounded-full" style="width:{{ $pct }}%; background:linear-gradient(90deg,#7fffd4,#008080);"></div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm px-7 py-5 mb-5">
    <div class="flex flex-wrap items-center gap-4">

        {{-- Search nama / NIM --}}
        <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">search</span>
            <input type="text" id="searchNilai" oninput="applyFilter()" placeholder="Cari nama atau NIM..."
                class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary transition-colors">
        </div>

        {{-- Filter Prodi (Hanya Admin) --}}
        @if(!$isKps)
        <select id="filterProdi" onchange="setDropdownFilter('prodi', this.value)" class="px-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-[#004d4d] focus:outline-none focus:border-[#2dce89] cursor-pointer">
            <option value="semua">Semua Prodi</option>
            <option value="informatika">TRIN - Informatika</option>
            <option value="otomasi">TRO - Otomasi</option>
            <option value="mekatronika">TRMO - Mekatronika</option>
        </select>
        @endif

        {{-- Filter Kelas --}}
        <select id="filterKelas" onchange="setDropdownFilter('kelas', this.value)" class="px-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-[#004d4d] focus:outline-none focus:border-[#2dce89] cursor-pointer">
            <option value="semua">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}">{{ $k }}</option>
            @endforeach
        </select>

        {{-- Divider --}}
        <div class="h-6 w-px bg-outline-variant/20 ml-2"></div>

        {{-- Filter Status Nilai --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-outline uppercase tracking-widest mr-1">Status:</span>
            @foreach(['semua'=>'Semua','lengkap'=>'Nilai Lengkap','sebagian'=>'Sebagian','belum'=>'Belum Dinilai'] as $val=>$lbl)
            <button onclick="setFilter('status','{{ $val }}')"
                data-filter="status" data-value="{{ $val }}"
                class="filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all
                {{ $val==='semua' ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary' }}">
                {{ $lbl }}
                @if($val === 'belum' && $belumLengkap > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-red-400 text-white text-[9px] font-black">{{ $belumLengkap }}</span>
                @endif
            </button>
            @endforeach
        </div>

        {{-- Reset & Counter --}}
        <div class="ml-auto flex items-center gap-3">
            <span id="filterCounter" class="hidden text-[10px] font-black text-outline bg-gray-50 px-3 py-1.5 rounded-lg">
                <span id="visibleCount" class="text-[#004d4d]">0</span> ditemukan
            </span>
            <button onclick="resetFilter()" id="btnReset"
                class="hidden flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-all">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
            </button>
        </div>
    </div>
</div>

{{-- TABEL REKAP --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-outline-variant/10 bg-surface-container-low/40">
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">#</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Mahasiswa</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Proyek / Matkul</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Manager<br><span class="normal-case font-medium text-[9px]">(55%)</span></th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Dosen<br><span class="normal-case font-medium text-[9px]">(45%)</span></th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Nilai Akhir</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Grade</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10" id="nilaiTbody">
            @forelse($mahasiswaList as $i => $mhs)
            @php
                $pm         = $mhs->penilaianManager->first();
                $pds        = $mhs->penilaianDosen; // semua record dosen
                
                $nilaiM     = $pm->nilai_manager ?? null;
                $nilaiD     = $pds->count() > 0 ? round($pds->avg('nilai_dosen'), 1) : null;
                
                $matkulList = $pds->map(fn($p) =>
                    $p->supervisiMatkul->mataKuliah->nama_matkul ?? null
                )->filter()->unique()->values();
                
                $nilaiAkhir = ($nilaiM !== null && $nilaiD !== null)
                    ? round(($nilaiM * 0.55) + ($nilaiD * 0.45), 1)
                    : null;
                    
                $grade = null;
                if ($nilaiAkhir !== null) {
                    $grade = $nilaiAkhir >= 85 ? 'A'
                           : ($nilaiAkhir >= 75 ? 'B'
                           : ($nilaiAkhir >= 65 ? 'C'
                           : ($nilaiAkhir >= 55 ? 'D' : 'E')));
                }
                
                $proyek = $pm->pengajuanProyek->judul_proyek ?? null;
                $statusNilai = ($nilaiM !== null && $nilaiD !== null) ? 'lengkap'
                             : (($nilaiM !== null || $nilaiD !== null) ? 'sebagian' : 'belum');
            @endphp
            {{-- DATA ATRIBUTES UNTUK FILTER JS --}}
            <tr class="nilai-row hover:bg-surface-container-lowest transition-colors"
                data-name="{{ strtolower($mhs->nama) }}"
                data-nim="{{ $mhs->nim }}"
                data-prodi="{{ strtolower($mhs->user->prodi ?? '') }}"
                data-kelas="{{ $mhs->kelas ?? '' }}"
                data-status="{{ $statusNilai }}">

                <td class="px-7 py-5 text-[10px] font-black text-outline/40 row-num">{{ $i + 1 }}</td>

                {{-- Mahasiswa --}}
                <td class="px-7 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center font-black text-xs text-primary shrink-0">
                            {{ strtoupper(substr($mhs->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-black text-on-surface text-sm">{{ $mhs->nama }}</p>
                            <p class="text-[10px] text-outline font-bold mt-0.5">
                                {{ $mhs->nim }} • <span class="text-[#2dce89]">{{ $mhs->kelas ?? '-' }}</span>
                            </p>
                        </div>
                    </div>
                </td>

                {{-- Proyek / Matkul --}}
                <td class="px-7 py-5">
                    @if($proyek)
                        <p class="text-sm font-medium text-on-surface-variant">{{ $proyek }}</p>
                    @endif
                    
                    @foreach($matkulList as $mk)
                        <p class="text-[10px] text-outline mt-0.5">{{ $mk }}</p>
                    @endforeach
                    @if($matkulList->isEmpty() && !$proyek)
                        <span class="text-outline/40 text-sm">-</span>
                    @endif
                </td>

                {{-- Manager (55%) --}}
                <td class="px-7 py-5 text-center">
                    @if($nilaiM !== null)
                        <span class="font-black text-sm" style="color:#004d4d;">{{ number_format($nilaiM, 1) }}</span>
                    @else
                        <span class="text-outline/40 font-black text-sm">-</span>
                    @endif
                </td>

                {{-- Dosen (45%) --}}
                <td class="px-7 py-5 text-center">
                    @if($nilaiD !== null)
                        <span class="font-black text-sm" style="color:#2dce89;">{{ number_format($nilaiD, 1) }}</span>
                    @else
                        <span class="text-outline/40 font-black text-sm">-</span>
                    @endif
                </td>

                {{-- Nilai Akhir --}}
                <td class="px-7 py-5 text-center">
                    @if($nilaiAkhir !== null)
                        @php
                            $colorAkhir = $nilaiAkhir >= 85 ? 'bg-emerald-100 text-emerald-700'
                                        : ($nilaiAkhir >= 75 ? 'bg-blue-100 text-blue-700'
                                        : ($nilaiAkhir >= 65 ? 'bg-yellow-100 text-yellow-700'
                                        : ($nilaiAkhir >= 55 ? 'bg-orange-100 text-orange-700'
                                        : 'bg-red-100 text-red-700')));
                        @endphp
                        <span class="inline-block px-4 py-1.5 rounded-xl text-sm font-black {{ $colorAkhir }}">
                            {{ number_format($nilaiAkhir, 1) }}
                        </span>
                    @else
                        <span class="inline-block px-4 py-1.5 rounded-xl text-sm font-black bg-gray-100 text-gray-400">-</span>
                    @endif
                </td>

                {{-- Grade --}}
                <td class="px-7 py-5 text-center">
                    @if($grade)
                        @php
                            $colorGrade = $grade === 'A' ? 'bg-emerald-500'
                                        : ($grade === 'B' ? 'bg-blue-500'
                                        : ($grade === 'C' ? 'bg-yellow-500'
                                        : ($grade === 'D' ? 'bg-orange-500' : 'bg-red-500')));
                        @endphp
                        <span class="inline-flex w-9 h-9 rounded-xl items-center justify-center text-white font-black text-sm {{ $colorGrade }}">
                            {{ $grade }}
                        </span>
                    @else
                        <span class="text-outline/40 font-black text-sm">-</span>
                    @endif
                </td>

                {{-- Tombol Detail --}}
                <td class="px-7 py-5 text-center">
                    <button onclick="bukaDetail({{ $mhs->id }})"
                        class="px-4 py-2 rounded-xl text-xs font-black text-[#7fffd4] transition-all hover:scale-105 shadow-lg shadow-teal-900/20"
                        style="background:#004d4d;">
                        Detail
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-7 py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">group</span>
                    <p class="text-on-surface-variant/40 font-black italic text-sm">Belum ada data mahasiswa.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Empty filter state --}}
    <div id="emptyFilter" class="hidden px-7 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">search_off</span>
        <p class="font-black italic text-on-surface-variant/40 text-sm">Tidak ada mahasiswa ditemukan.</p>
        <button onclick="resetFilter()" class="mt-4 px-4 py-2 bg-gray-100 rounded-xl text-[10px] font-black text-[#004d4d] hover:bg-gray-200 transition-colors uppercase tracking-widest">
            Reset Filter
        </button>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modalDetail" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-[2rem] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl relative">
        <div class="p-7">
            <div class="flex items-start justify-between mb-6 sticky top-0 bg-white z-10 pb-2">
                <div>
                    <h2 class="text-xl font-black text-[#004d4d] italic uppercase">Detail <span class="text-[#2dce89]">Penilaian</span></h2>
                    <p id="modalNama" class="text-xs text-outline font-bold mt-0.5 uppercase tracking-widest"></p>
                </div>
                <button onclick="tutupDetail()" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-colors text-gray-500">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>
</div>

{{-- DATA JSON + SCRIPT --}}
<script>
const dataPenilaian = {
    @foreach($mahasiswaList as $mhs)
    @php
        $pm  = $mhs->penilaianManager->first();
        $pds = $mhs->penilaianDosen; // collection semua dosen
        $avgNilaiDosen = $pds->count() > 0 ? round($pds->avg('nilai_dosen'), 2) : null;
    @endphp
    {{ $mhs->id }}: {
        nama:    @json($mhs->nama),
        nim:     @json($mhs->nim),
        kelas:   @json($mhs->kelas ?? '-'),
        manager: @if($pm) {
            proyek:          @json($pm->pengajuanProyek->judul_proyek ?? '-'),
            learning_skills: {{ $pm->learning_skills ?? 'null' }},
            life_skills:     {{ $pm->life_skills ?? 'null' }},
            laporan_project: {{ $pm->laporan_project ?? 'null' }},
            nilai_manager:   {{ $pm->nilai_manager ?? 'null' }},
            catatan:         @json($pm->catatan_manager ?? '')
        } @else null @endif,
        dosen_avg: {{ $avgNilaiDosen ?? 'null' }},
        dosen_list: [
            @foreach($pds as $pd)
            {
                matkul:          @json($pd->supervisiMatkul->mataKuliah->nama_matkul ?? '-'),
                literacy_skills: {{ $pd->literacy_skills ?? 'null' }},
                presentasi:      {{ $pd->presentasi ?? 'null' }},
                laporan_akhir:   {{ $pd->laporan_akhir ?? 'null' }},
                nilai_dosen:     {{ $pd->nilai_dosen ?? 'null' }},
                catatan:         @json($pd->catatan_dosen ?? '')
            },
            @endforeach
        ]
    },
    @endforeach
};

// ===================== FILTER =====================
const activeFilters = { status: 'semua', prodi: 'semua', kelas: 'semua' };

function setFilter(type, value) { // Khusus tombol status
    activeFilters[type] = value;
    document.querySelectorAll(`[data-filter="${type}"]`).forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.className = 'filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ' +
            (isActive
                ? 'bg-[#004d4d] text-[#7fffd4] border-[#004d4d]'
                : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-[#2dce89] hover:text-[#004d4d]');
    });
    applyFilter();
}

function setDropdownFilter(type, value) { // Khusus select prodi & kelas
    activeFilters[type] = value;
    applyFilter();
}

function applyFilter() {
    const q      = document.getElementById('searchNilai').value.toLowerCase().trim();
    const rows   = document.querySelectorAll('.nilai-row');
    let visible  = 0;

    rows.forEach(r => {
        const matchSearch = !q || r.dataset.name.includes(q) || r.dataset.nim.includes(q);
        const matchStatus = activeFilters.status === 'semua' || r.dataset.status === activeFilters.status;
        const matchProdi  = activeFilters.prodi === 'semua' || r.dataset.prodi === activeFilters.prodi;
        const matchKelas  = activeFilters.kelas === 'semua' || r.dataset.kelas === activeFilters.kelas;

        const show = matchSearch && matchStatus && matchProdi && matchKelas;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Nomor urut ulang
    let num = 1;
    rows.forEach(r => {
        if (r.style.display !== 'none') r.querySelector('.row-num').textContent = num++;
    });

    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0);

    const isFiltered = q || activeFilters.status !== 'semua' || activeFilters.prodi !== 'semua' || activeFilters.kelas !== 'semua';
    document.getElementById('btnReset').classList.toggle('hidden', !isFiltered);
    document.getElementById('filterCounter').classList.toggle('hidden', !isFiltered);
    document.getElementById('visibleCount').textContent = visible;
}

function resetFilter() {
    document.getElementById('searchNilai').value = '';
    
    if(document.getElementById('filterProdi')) {
        document.getElementById('filterProdi').value = 'semua';
        activeFilters.prodi = 'semua';
    }
    
    if(document.getElementById('filterKelas')) {
        document.getElementById('filterKelas').value = 'semua';
        activeFilters.kelas = 'semua';
    }
    
    setFilter('status', 'semua'); // akan memanggil applyFilter()
}

// ===================== MODAL =====================
function bukaDetail(id) {
    const d = dataPenilaian[id];
    if (!d) return;

    document.getElementById('modalNama').innerHTML = `${d.nama} <span class="text-gray-400 font-medium">(${d.nim} • ${d.kelas})</span>`;

    let html = '';

    // ── MANAGER ──
    html += `<div class="mb-5 p-5 rounded-2xl border border-outline-variant/20 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-base" style="color:#004d4d;">manage_accounts</span>
            <p class="font-black text-sm text-on-surface uppercase tracking-widest">Manager Proyek</p>
            <span class="ml-auto text-xs font-black px-3 py-1 rounded-xl" style="background:#e6f7f2;color:#004d4d;">Bobot 55%</span>
        </div>`;

    if (d.manager) {
        const nilaiM    = d.manager.nilai_manager;
        const nilaiMFmt = nilaiM !== null ? Number(nilaiM).toFixed(1) : '-';
        html += `
        <p class="text-xs text-outline mb-3">Proyek: <span class="font-black text-[#004d4d]">${d.manager.proyek}</span></p>
        <div class="grid grid-cols-3 gap-3 mb-4">
            ${nilaiBox('Learning Skills', d.manager.learning_skills, '#004d4d')}
            ${nilaiBox('Life Skills',     d.manager.life_skills,     '#004d4d')}
            ${nilaiBox('Lap. Project',    d.manager.laporan_project,  '#004d4d')}
        </div>
        <div class="flex items-center justify-between p-4 rounded-2xl" style="background:#004d4d;">
            <span class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Nilai Manager (skala 100)</span>
            <span class="text-white font-black text-xl">${nilaiMFmt}</span>
        </div>
        ${d.manager.catatan ? `<div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100"><p class="text-xs text-gray-500 italic">"${d.manager.catatan}"</p></div>` : ''}`;
    } else {
        html += `<div class="py-6 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border border-dashed border-gray-200"><span class="material-symbols-outlined text-3xl text-gray-300 mb-2">pending_actions</span><p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Belum Dinilai</p></div>`;
    }
    html += `</div>`;

    // ── DOSEN ──
    html += `<div class="mb-5 p-5 rounded-2xl border border-outline-variant/20 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-base" style="color:#2dce89;">school</span>
            <p class="font-black text-sm text-on-surface uppercase tracking-widest">Dosen Pengampu</p>
            <span class="ml-auto text-xs font-black px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700">Bobot 45%</span>
        </div>`;

    if (d.dosen_list && d.dosen_list.length > 0) {
        if (d.dosen_list.length > 1) {
            html += `<p class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl mb-4 uppercase tracking-widest border border-emerald-100 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">functions</span> Rata-rata dari ${d.dosen_list.length} mata kuliah</p>`;
        }
        d.dosen_list.forEach((pd, idx) => {
            const nilaiDFmt = pd.nilai_dosen !== null ? Number(pd.nilai_dosen).toFixed(1) : '-';
            html += `
            <div class="${idx < d.dosen_list.length - 1 ? 'mb-5 pb-5 border-b border-gray-100' : ''}">
                <p class="text-xs text-outline mb-3">Mata Kuliah: <span class="font-black text-[#004d4d]">${pd.matkul}</span></p>
                <div class="grid grid-cols-3 gap-3 mb-3">
                    ${nilaiBox('Literacy Skills', pd.literacy_skills, '#2dce89')}
                    ${nilaiBox('Presentasi',      pd.presentasi,      '#2dce89')}
                    ${nilaiBox('Lap. Akhir',      pd.laporan_akhir,   '#2dce89')}
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Sub-Nilai Matkul</span>
                    <span class="text-[#004d4d] font-black text-lg">${nilaiDFmt}</span>
                </div>
                ${pd.catatan ? `<div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100"><p class="text-xs text-gray-500 italic">"${pd.catatan}"</p></div>` : ''}
            </div>`;
        });
        
        if (d.dosen_list.length > 1 && d.dosen_avg !== null) {
            html += `
            <div class="flex items-center justify-between p-4 rounded-2xl mt-4" style="background:#2dce89;">
                <span class="text-white text-[10px] font-black uppercase tracking-widest">Rata-rata Nilai Dosen</span>
                <span class="text-white font-black text-xl">${Number(d.dosen_avg).toFixed(1)}</span>
            </div>`;
        } else if (d.dosen_list.length === 1 && d.dosen_list[0].nilai_dosen !== null) {
             html += `
            <div class="flex items-center justify-between p-4 rounded-2xl mt-4" style="background:#2dce89;">
                <span class="text-white text-[10px] font-black uppercase tracking-widest">Total Nilai Dosen</span>
                <span class="text-white font-black text-xl">${Number(d.dosen_list[0].nilai_dosen).toFixed(1)}</span>
            </div>`;
        }

    } else {
        html += `<div class="py-6 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border border-dashed border-gray-200"><span class="material-symbols-outlined text-3xl text-gray-300 mb-2">pending_actions</span><p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Belum Dinilai</p></div>`;
    }
    html += `</div>`;

    // ── NILAI AKHIR SUMMARY ──
    if (d.manager && d.dosen_avg !== null && d.manager.nilai_manager !== null) {
        const nilaiAkhir = ((d.manager.nilai_manager * 0.55) + (d.dosen_avg * 0.45)).toFixed(1);
        const grade = nilaiAkhir >= 85 ? 'A' : nilaiAkhir >= 75 ? 'B' : nilaiAkhir >= 65 ? 'C' : nilaiAkhir >= 55 ? 'D' : 'E';
        const gradeColor = grade === 'A' ? '#10b981' : grade === 'B' ? '#3b82f6' : grade === 'C' ? '#f59e0b' : grade === 'D' ? '#f97316' : '#ef4444';
        
        html += `
        <div class="p-6 rounded-[2rem] flex items-center justify-between shadow-xl mt-6 relative overflow-hidden" style="background:linear-gradient(135deg,#004d4d,#008080);">
            <span class="material-symbols-outlined absolute -right-4 -top-4 text-white/5" style="font-size:120px;">workspace_premium</span>
            <div class="relative z-10">
                <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-1.5">Kalkulasi Nilai Akhir</p>
                <p class="text-white text-xs font-medium opacity-80 bg-black/20 px-3 py-1 rounded-lg inline-block">
                    (${Number(d.manager.nilai_manager).toFixed(1)} × 55%) + (${Number(d.dosen_avg).toFixed(1)} × 45%)
                </p>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <p class="text-white font-black text-5xl tracking-tighter">${nilaiAkhir}</p>
                <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 border-white/20" style="background:${gradeColor};">${grade}</span>
            </div>
        </div>`;
    }

    document.getElementById('modalContent').innerHTML = html;
    const modal = document.getElementById('modalDetail');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupDetail() {
    const modal = document.getElementById('modalDetail');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function nilaiBox(label, nilai, color) {
    const fmt = nilai !== null && nilai !== undefined ? Number(nilai).toFixed(1) : '-';
    return `<div class="p-3 rounded-xl text-center bg-white border border-gray-100 shadow-sm transition-transform hover:-translate-y-0.5">
        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">${label}</p>
        <p class="text-xl font-black" style="color:${color}">${fmt}</p>
    </div>`;
}

document.getElementById('modalDetail').addEventListener('click', function(e) {
    if (e.target === this) tutupDetail();
});
</script>

@endsection