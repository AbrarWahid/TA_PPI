@extends('layouts.app')
@section('title', 'Monitoring & Laporan')
@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Monitoring <span class="text-[#2dce89]">& Laporan</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Progress aktivitas logbook & laporan mahasiswa
        </p>
    </div>
    <a href="{{ route('laporan.admin') }}?export=1{{ $proyekId ? '&proyek_id='.$proyekId : '' }}"
        class="flex items-center gap-2 px-5 py-3 rounded-2xl text-[#7fffd4] text-xs font-black hover:opacity-80 transition-all shadow-xl shadow-teal-900/20"
        style="background:#004d4d;">
        <span class="material-symbols-outlined text-base">download</span>
        EXPORT CSV
    </a>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-4 gap-5 mb-8">
    <div class="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between shadow-xl h-32" style="background:#004d4d;">
        <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
        <div class="flex items-end justify-between">
            <p class="text-4xl font-black italic text-white tracking-tighter">{{ $totalMahasiswa }}</p>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:90px;">group</span>
    </div>

    <div class="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between shadow-xl h-32" style="background:#2dce89;">
        <p class="text-white/70 text-[10px] font-black uppercase tracking-widest">Selesai</p>
        <div class="flex items-end justify-between">
            <p class="text-4xl font-black italic text-white tracking-tighter">{{ $sudahSelesai }}</p>
            <span class="text-xs font-black text-white/60">Mahasiswa</span>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/20" style="font-size:90px;">task_alt</span>
    </div>

    <div class="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between h-32 bg-white border-2 border-outline-variant/20">
        <p class="text-[10px] font-black uppercase tracking-widest" style="color:#004d4d;">Berjalan</p>
        <div class="flex items-end justify-between">
            <p class="text-4xl font-black italic tracking-tighter" style="color:#004d4d;">{{ $sedangBerjalan }}</p>
            <span class="text-xs font-black text-gray-400">Mahasiswa</span>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-gray-100" style="font-size:90px;">pending_actions</span>
    </div>

    <div class="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between h-32 bg-white border-2 border-orange-100">
        <p class="text-orange-400 text-[10px] font-black uppercase tracking-widest">Belum Mulai</p>
        <div class="flex items-end justify-between">
            <p class="text-4xl font-black italic text-orange-400 tracking-tighter">{{ $belumMulai }}</p>
            <span class="text-xs font-black text-gray-400">Mahasiswa</span>
        </div>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-orange-50" style="font-size:90px;">hourglass_empty</span>
    </div>
</div>

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('laporan.admin') }}" id="formFilter">
    <div class="flex items-center gap-3 mb-6 flex-wrap">

        {{-- Filter Proyek --}}
        <select name="proyek_id" onchange="this.form.submit()"
            class="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all">
            <option value="">Semua Proyek</option>
            @foreach($proyekList as $p)
                <option value="{{ $p->id }}" {{ $proyekId == $p->id ? 'selected' : '' }}>
                    {{ $p->judul_proyek }}
                </option>
            @endforeach
        </select>

        {{-- Filter Prodi (Admin only; KPS dikunci otomatis) --}}
        @if($isKps)
            <div class="px-5 py-3 rounded-2xl border border-teal-200 bg-teal-50 text-sm font-black text-[#004d4d]">
                {{ $prodiList[$kpsProdi] ?? $kpsProdi }}
                <input type="hidden" name="prodi" value="{{ $kpsProdi }}">
            </div>
        @else
            <select name="prodi" onchange="this.form.submit()"
                class="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all">
                <option value="">Semua Prodi</option>
                @foreach($prodiList as $key => $label)
                    <option value="{{ $key }}" {{ $filterProdi === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        @endif

        {{-- Filter Kelas --}}
        <select name="kelas" onchange="this.form.submit()"
            class="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ $filterKelas === $k ? 'selected' : '' }}>
                    {{ $k }}
                </option>
            @endforeach
        </select>

        {{-- Search Nama / NIM (client-side) --}}
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">search</span>
            <input type="text" id="searchMahasiswa" placeholder="Cari nama / NIM..."
                class="pl-9 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 w-52 transition-all">
        </div>

        {{-- Filter Status (client-side) --}}
        <select id="filterStatus"
            class="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all">
            <option value="">Semua Status</option>
            <option value="selesai">Selesai</option>
            <option value="berjalan">Berjalan</option>
            <option value="belum mulai">Belum Mulai</option>
        </select>

        {{-- Reset filter --}}
        @if($filterProdi || $filterKelas || $proyekId)
            <a href="{{ route('laporan.admin') }}"
                class="px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs font-black text-gray-400 hover:text-red-400 hover:border-red-200 transition-all">
                Reset
            </a>
        @endif

        {{-- Badge hasil filter --}}
        <span id="resultCount" class="text-xs font-black text-gray-400 italic hidden"></span>

    </div>
</form>

{{-- TABEL MONITORING --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/40">
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">#</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Mahasiswa</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Proyek</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                    Logbook<br><span class="normal-case font-medium text-[9px]">Mingguan</span>
                </th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Harian</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                    Supervisi<br><span class="normal-case font-medium text-[9px]">Mingguan</span>
                </th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Progress Logbook</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
            </tr>
        </thead>
        <tbody id="monitoringTableBody" class="divide-y divide-gray-50">
            @forelse($mahasiswaProgress as $i => $m)
            @php
                $statusColor = match($m->status) {
                    'Selesai'     => 'bg-emerald-100 text-emerald-700',
                    'Berjalan'    => 'bg-blue-100 text-blue-700',
                    'Belum Mulai' => 'bg-orange-100 text-orange-600',
                    default       => 'bg-gray-100 text-gray-500',
                };
                $barColor = match($m->status) {
                    'Selesai'  => '#2dce89',
                    'Berjalan' => '#004d4d',
                    default    => '#e0e0e0',
                };
            @endphp
            <tr class="hover:bg-teal-50/20 transition-colors monitoring-row"
                data-nama="{{ strtolower($m->nama) }}"
                data-nim="{{ $m->nim }}"
                data-status="{{ strtolower($m->status) }}">
                <td class="px-6 py-5 text-[10px] font-black text-gray-300">{{ $i + 1 }}</td>

                {{-- Mahasiswa --}}
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center font-black text-xs text-[#004d4d] shrink-0">
                            {{ strtoupper(substr($m->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-800 text-sm">{{ $m->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">{{ $m->nim }} · {{ $m->prodi }}</p>
                        </div>
                    </div>
                </td>

                {{-- Proyek --}}
                <td class="px-6 py-5">
                    <p class="text-sm font-medium text-gray-600">{{ $m->proyek }}</p>
                </td>

                {{-- Logbook Mingguan (/8) --}}
                <td class="px-6 py-5 text-center">
                    <span class="font-black text-sm" style="color:#004d4d;">{{ $m->logbook_verified }}</span>
                    <span class="text-gray-300 text-xs font-bold"> / 8</span>
                    <p class="text-[9px] text-gray-400 mt-0.5">verified</p>
                </td>

                {{-- Harian --}}
                <td class="px-6 py-5 text-center">
                    <span class="font-black text-sm text-gray-600">{{ $m->total_harian }}</span>
                    <p class="text-[9px] text-gray-400 mt-0.5">entri</p>
                </td>

                {{-- Supervisi Mingguan (/2) --}}
                <td class="px-6 py-5 text-center">
                    <span class="font-black text-sm" style="color:#2dce89;">{{ $m->laporan_verified }}</span>
                    <span class="text-gray-300 text-xs font-bold"> / 2</span>
                    <p class="text-[9px] text-gray-400 mt-0.5">verified</p>
                </td>

                {{-- Progress Bar --}}
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all"
                                style="width:{{ $m->progress_logbook }}%; background:{{ $barColor }};"></div>
                        </div>
                        <span class="text-[10px] font-black text-gray-400 w-8 text-right">{{ $m->progress_logbook }}%</span>
                    </div>
                </td>

                {{-- Status --}}
                <td class="px-6 py-5 text-center">
                    <span class="inline-block px-3 py-1.5 rounded-xl text-[10px] font-black {{ $statusColor }}">
                        {{ $m->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-gray-200 block mb-3">monitoring</span>
                    <p class="text-gray-400 font-black italic text-sm">Belum ada data aktivitas.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Empty state filter --}}
    <div id="noResultMonitoring" class="hidden px-6 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-200 block mb-3">search_off</span>
        <p class="text-gray-400 font-black italic text-sm uppercase">Tidak ada hasil yang cocok.</p>
    </div>
</div>

<script>
(function () {
    const searchInput = document.getElementById('searchMahasiswa');
    const statusSelect = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('.monitoring-row');
    const noResult = document.getElementById('noResultMonitoring');
    const resultCount = document.getElementById('resultCount');

    function applyFilter() {
        const keyword = searchInput.value.toLowerCase().trim();
        const status  = statusSelect.value.toLowerCase();
        let visible   = 0;

        rows.forEach(row => {
            const nama      = row.dataset.nama   || '';
            const nim       = row.dataset.nim    || '';
            const rowStatus = row.dataset.status || '';

            const matchSearch = !keyword || nama.includes(keyword) || nim.includes(keyword);
            const matchStatus = !status  || rowStatus === status;

            if (matchSearch && matchStatus) {
                row.classList.remove('hidden');
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });

        noResult.classList.toggle('hidden', visible > 0);

        if (keyword || status) {
            resultCount.textContent = visible + ' hasil';
            resultCount.classList.remove('hidden');
        } else {
            resultCount.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', applyFilter);
    statusSelect.addEventListener('change', applyFilter);
})();
</script>

<script>
(function () {
    const prodiKelasMap = {
        'informatika' : 'AEC',
        'mekatronika' : 'AEA',
        'otomasi'     : 'AEB',
    };

    const prodiSelect = document.querySelector('select[name="prodi"]');
    const kelasSelect = document.querySelector('select[name="kelas"]');

    if (!prodiSelect || !kelasSelect) return;

    const allKelasOptions = Array.from(kelasSelect.options);

    function filterKelas() {
        const prodi  = prodiSelect.value;
        const prefix = prodiKelasMap[prodi] || null;

        kelasSelect.innerHTML = '';

        allKelasOptions.forEach(opt => {
            if (!prefix || opt.value === '' || opt.text.includes(prefix)) {
                kelasSelect.appendChild(opt.cloneNode(true));
            }
        });

        const currentKelas = "{{ $filterKelas }}";
        if (currentKelas) {
            kelasSelect.value = currentKelas;
        }
    }

    filterKelas();

    prodiSelect.addEventListener('change', function () {
        kelasSelect.value = '';
        this.form.submit();
    });
})();
</script>

@endsection