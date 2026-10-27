@extends('layouts.app')
@section('title', 'Rekap Logbook Mingguan')
@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('logbook_harian.index') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3 transition">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Logbook Harian
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Rekap Logbook Mingguan</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola dan ajukan rekap logbook harian per minggu ke dosen pengampu.</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Minggu Belum Direkap --}}
    @if(count($mingguBelumRekap) > 0)
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-blue-700 mb-3">
            <span class="material-symbols-outlined align-middle text-base">pending_actions</span>
            Minggu Siap Direkap
        </h2>
        <div class="space-y-2">
            @foreach($mingguBelumRekap as $item)
            <div class="flex items-center justify-between bg-white border border-blue-100 rounded-lg px-4 py-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $item['proyek']->judul_proyek }}</p>
                    <p class="text-xs text-gray-500">Minggu ke-{{ $item['minggu_ke'] }}</p>
                </div>
                <form method="POST" action="{{ route('logbook_mingguan.generate') }}">
                    @csrf
                    <input type="hidden" name="pengajuan_proyek_id" value="{{ $item['proyek']->id }}">
                    <input type="hidden" name="minggu_ke" value="{{ $item['minggu_ke'] }}">
                    <button type="submit"
                        class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                        Generate & Ajukan
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Daftar Rekap Mingguan --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        {{-- FILTER BAR --}}
        @php
            $proyekUnik  = $rekapList->map(fn($r) => $r->proyek)->filter()->unique('id')->sortBy('judul_proyek')->values();
            $mingguUnik  = $rekapList->pluck('minggu_ke')->unique()->sort()->values();
        @endphp
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">

            {{-- Filter Status Verifikasi --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status:</span>
                @foreach(['semua'=>'Semua','diajukan'=>'Menunggu','disetujui'=>'Disetujui','ditolak'=>'Ditolak'] as $val=>$lbl)
                <button onclick="setFilter('status','{{ $val }}')"
                    data-filter="status" data-value="{{ $val }}"
                    class="filter-btn px-3 py-1.5 rounded-lg text-[10px] font-semibold border transition-all
                    {{ $val==='semua' ? 'bg-[#004d4d] text-white border-[#004d4d]' : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#004d4d] hover:text-[#004d4d]' }}">
                    {{ $lbl }}
                </button>
                @endforeach
            </div>

            @if($proyekUnik->count() > 1)
            {{-- Divider --}}
            <div class="h-5 w-px bg-gray-200"></div>

            {{-- Filter Proyek --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Proyek:</span>
                <select id="filterProyek" onchange="applyFilter()"
                    class="py-1.5 pl-3 pr-7 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 focus:outline-none focus:border-[#004d4d] appearance-none cursor-pointer">
                    <option value="semua">Semua</option>
                    @foreach($proyekUnik as $proyek)
                    <option value="{{ $proyek->id }}">{{ Str::limit($proyek->judul_proyek, 35) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($mingguUnik->count() > 1)
            {{-- Divider --}}
            <div class="h-5 w-px bg-gray-200"></div>

            {{-- Filter Minggu ke- --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Minggu:</span>
                <select id="filterMinggu" onchange="applyFilter()"
                    class="py-1.5 pl-3 pr-7 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 focus:outline-none focus:border-[#004d4d] appearance-none cursor-pointer">
                    <option value="semua">Semua</option>
                    @foreach($mingguUnik as $minggu)
                    <option value="{{ $minggu }}">Minggu ke-{{ $minggu }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Reset & Counter --}}
            <div class="ml-auto flex items-center gap-3">
                <span id="filterCounter" class="hidden text-[10px] font-semibold text-gray-400">
                    <span id="visibleCount">0</span> rekap ditemukan
                </span>
                <button onclick="resetFilter()" id="btnReset"
                    class="hidden flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-semibold border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-all">
                    <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
                </button>
            </div>
        </div>

        {{-- Daftar item --}}
        @if($rekapList->isEmpty())
        <div class="px-5 py-10 text-center text-gray-400 text-sm">
            <span class="material-symbols-outlined text-4xl block mb-2">folder_open</span>
            Belum ada rekap mingguan.
        </div>
        @else
        <div class="divide-y divide-gray-100" id="rekapContainer">
            @foreach($rekapList as $rekap)
            <div class="rekap-item px-5 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 transition-colors"
                data-status="{{ $rekap->status }}"
                data-proyek-id="{{ $rekap->pengajuan_proyek_id }}"
                data-minggu="{{ $rekap->minggu_ke }}">

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">
                        {{ $rekap->proyek->judul_proyek ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Minggu ke-{{ $rekap->minggu_ke }} &nbsp;·&nbsp;
                        Diajukan: {{ $rekap->diajukan_at ? $rekap->diajukan_at->format('d/m/Y') : '-' }}
                    </p>
                    @if($rekap->catatan_dosen)
                    <p class="text-xs text-amber-600 mt-1">
                        <span class="material-symbols-outlined text-xs align-middle">comment</span>
                        {{ $rekap->catatan_dosen }}
                    </p>
                    @endif
                </div>

                {{-- Badge Status --}}
                <div class="shrink-0">
                    @php
                        $badgeClass = match($rekap->status) {
                            'diajukan'  => 'bg-blue-100 text-blue-700',
                            'disetujui' => 'bg-green-100 text-green-700',
                            'ditolak'   => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                        $badgeLabel = match($rekap->status) {
                            'diajukan'  => 'Menunggu',
                            'disetujui' => 'Disetujui',
                            'ditolak'   => 'Ditolak',
                            default     => 'Draft',
                        };
                    @endphp
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                </div>

                {{-- Tombol Lihat PDF --}}
@if($rekap->pdf_path)
<a href="{{ route('logbook_mingguan.show', $rekap->id) }}"
    target="_blank"
    class="shrink-0 flex items-center gap-1 text-xs font-medium text-gray-600 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded-lg transition">
    <span class="material-symbols-outlined text-sm">open_in_new</span>
    Lihat PDF
</a>
@endif

{{-- Tombol Batalkan — hanya untuk status diajukan/draft --}}
@if(in_array($rekap->status, ['diajukan', 'draft']) && auth()->user()->isMahasiswa())
<form method="POST" action="{{ route('logbook_mingguan.batal', $rekap->id) }}"
    onsubmit="return confirm('Batalkan rekap minggu ke-{{ $rekap->minggu_ke }}? File PDF akan dihapus dan kamu bisa generate ulang.')">
    @csrf
    @method('DELETE')
    <button type="submit"
        class="shrink-0 flex items-center gap-1 text-xs font-medium text-red-500 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 px-3 py-1.5 rounded-lg transition">
        <span class="material-symbols-outlined text-sm">undo</span>
        Batalkan
    </button>
</form>
@endif
            </div>
            @endforeach
        </div>

        {{-- Empty filter state --}}
        <div id="emptyFilter" class="hidden px-5 py-10 text-center text-gray-400 text-sm">
            <span class="material-symbols-outlined text-4xl block mb-2">search_off</span>
            Tidak ada rekap yang cocok dengan filter.
            <button onclick="resetFilter()" class="block mx-auto mt-3 text-[10px] font-bold text-[#004d4d] hover:underline uppercase tracking-widest">
                Reset Filter
            </button>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
const activeFilters = { status: 'semua' };

function setFilter(type, value) {
    activeFilters[type] = value;
    document.querySelectorAll(`[data-filter="${type}"]`).forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.className = 'filter-btn px-3 py-1.5 rounded-lg text-[10px] font-semibold border transition-all ' +
            (isActive
                ? 'bg-[#004d4d] text-white border-[#004d4d]'
                : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#004d4d] hover:text-[#004d4d]');
    });
    applyFilter();
}

function applyFilter() {
    const proyekId = document.getElementById('filterProyek')?.value ?? 'semua';
    const minggu   = document.getElementById('filterMinggu')?.value  ?? 'semua';
    const items    = document.querySelectorAll('.rekap-item');
    let visible    = 0;

    items.forEach(item => {
        const matchStatus = activeFilters.status === 'semua' || item.dataset.status   === activeFilters.status;
        const matchProyek = proyekId === 'semua'             || item.dataset.proyekId === proyekId;
        const matchMinggu = minggu   === 'semua'             || item.dataset.minggu   === minggu;

        const show = matchStatus && matchProyek && matchMinggu;
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0);

    const isFiltered = activeFilters.status !== 'semua' || proyekId !== 'semua' || minggu !== 'semua';
    document.getElementById('btnReset').classList.toggle('hidden', !isFiltered);
    document.getElementById('filterCounter').classList.toggle('hidden', !isFiltered);
    document.getElementById('visibleCount').textContent = visible;
}

function resetFilter() {
    const elProyek = document.getElementById('filterProyek');
    const elMinggu = document.getElementById('filterMinggu');
    if (elProyek) elProyek.value = 'semua';
    if (elMinggu) elMinggu.value = 'semua';
    setFilter('status', 'semua');
}
</script>
@endpush