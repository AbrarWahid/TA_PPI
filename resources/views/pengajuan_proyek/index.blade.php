@extends('layouts.app')
@section('title', 'Approval Proyek')
@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Approval <span class="text-[#2dce89]">Proyek</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            {{ $pengajuan->count() }} pengajuan ditemukan
        </p>
    </div>
    @if(auth()->user()->isManager() || (auth()->user()->isDosen() && auth()->user()->role_aktif === 'manager_proyek'))
    <a href="{{ route('pengajuan_proyek.create') }}"
       class="flex items-center gap-2 px-5 py-3 bg-primary text-on-primary rounded-2xl text-xs font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg">
        <span class="material-symbols-outlined text-base">add</span> Ajukan Proyek
    </a>
    @endif
</div>

{{-- STAT CARDS --}}
@php
    $cPending  = $pengajuan->where('status','pending')->count();
    $cApproved = $pengajuan->where('status','approved')->count();
    $cRejected = $pengajuan->where('status','rejected')->count();

    // Data untuk dropdown filter
    $managerList = $pengajuan->map(fn($p) => $p->manager)->filter()->unique('id')->sortBy('name')->values();
@endphp
<div class="grid grid-cols-4 gap-5 mb-8">
    <div class="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36" style="background:#004d4d;">
        <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Pengajuan</p>
        <p class="text-5xl font-black italic tracking-tighter text-white">{{ $pengajuan->count() }}</p>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">rocket_launch</span>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-amber-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
            <p class="text-amber-500 text-[10px] font-black uppercase tracking-widest">Menunggu</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cPending }}</p>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-emerald-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
            <p class="text-emerald-600 text-[10px] font-black uppercase tracking-widest">Disetujui</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cApproved }}</p>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-red-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
            <p class="text-red-500 text-[10px] font-black uppercase tracking-widest">Ditolak</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cRejected }}</p>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm px-7 py-5 mb-5">
    <div class="flex flex-wrap items-center gap-3">

        {{-- Search judul / kode --}}
        <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">search</span>
            <input type="text" id="searchProyek" oninput="applyFilter()" placeholder="Cari judul atau kode proyek..."
                class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-outline-variant/20"></div>

        {{-- Filter Status --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-outline uppercase tracking-widest">Status:</span>
            @foreach(['semua'=>'Semua','pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'] as $val=>$lbl)
            <button onclick="setFilter('status','{{ $val }}')"
                data-filter="status" data-value="{{ $val }}"
                class="filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all
                {{ $val==='semua' ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary' }}">
                {{ $lbl }}
                @if($val === 'pending' && $cPending > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-400 text-white text-[9px] font-black">{{ $cPending }}</span>
                @endif
            </button>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-outline-variant/20"></div>



        {{-- Filter Manager --}}
        @if($managerList->count() > 1)
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-outline uppercase tracking-widest">Manager:</span>
            <select id="filterManager" onchange="applyFilter()"
                class="py-2 pl-3 pr-8 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-on-surface focus:outline-none focus:border-primary appearance-none cursor-pointer">
                <option value="semua">Semua</option>
                @foreach($managerList as $mgr)
                <option value="{{ $mgr->id }}">{{ $mgr->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Reset & Counter --}}
        <div class="ml-auto flex items-center gap-3">
            <span id="filterCounter" class="hidden text-[10px] font-black text-outline">
                <span id="visibleCount">0</span> proyek ditemukan
            </span>
            <button onclick="resetFilter()" id="btnReset"
                class="hidden flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:border-red-200 transition-all">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
            </button>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-outline-variant/10 bg-surface-container-low/30">
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">#</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Proyek</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Manager</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Periode</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Mahasiswa</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Status</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($pengajuan as $idx => $p)
            @php
                $statusColor = match($p->status) {
                    'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'rejected' => 'bg-red-50 text-red-500 border-red-100',
                    default    => 'bg-amber-50 text-amber-500 border-amber-100',
                };
                $statusIcon = match($p->status) {
                    'approved' => 'check_circle',
                    'rejected' => 'cancel',
                    default    => 'pending',
                };
                $statusLabel = match($p->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default    => 'Menunggu',
                };
            @endphp
            <tr class="proyek-row hover:bg-surface-container-lowest transition-colors group"
                data-status="{{ $p->status }}"
                data-judul="{{ strtolower($p->judul_proyek) }}"
                data-kode="{{ strtolower($p->kode_pengajuan) }}"
                data-manager-id="{{ $p->manager_id }}">

                <td class="px-7 py-5 text-[10px] font-black text-outline/40 row-num">{{ $idx + 1 }}</td>
                <td class="px-7 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-primary text-base">rocket_launch</span>
                        </div>
                        <div>
                            <p class="font-black text-on-surface text-sm leading-none">{{ $p->judul_proyek }}</p>
                            <p class="text-[10px] text-outline font-mono mt-0.5 uppercase">{{ $p->kode_pengajuan }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-7 py-5">
                    <p class="text-sm font-bold text-on-surface-variant">{{ $p->manager->name ?? '-' }}</p>
                </td>
                <td class="px-7 py-5">
                    <p class="text-xs font-bold text-on-surface-variant">{{ $p->tanggal_mulai->format('d M Y') }}</p>
                    <p class="text-[10px] text-outline">s/d {{ $p->tanggal_selesai->format('d M Y') }}</p>
                </td>
                <td class="px-7 py-5 text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-container text-primary rounded-xl text-[10px] font-black">
                        <span class="material-symbols-outlined text-sm">group</span>
                        {{ $p->getTotalMahasiswa() }}
                    </span>
                </td>
                <td class="px-7 py-5 text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 border rounded-xl text-[10px] font-black {{ $statusColor }}">
                        <span class="material-symbols-outlined text-sm">{{ $statusIcon }}</span>
                        {{ $statusLabel }}
                    </span>
                </td>
                <td class="px-7 py-5">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('pengajuan_proyek.show', $p->id) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-on-primary shadow-md hover:scale-105 transition-all"
                           style="background:#004d4d;">
                            <span class="material-symbols-outlined text-sm">visibility</span> Detail
                        </a>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('pengajuan_proyek.destroy', $p->id) }}" method="POST"
                              onsubmit="return confirm('Hapus proyek ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-2.5 text-outline hover:text-white hover:bg-red-500 rounded-xl transition-all"
                                title="Hapus">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-7 py-20 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">rocket_launch</span>
                    <p class="font-black italic text-on-surface-variant/40 text-sm">Belum ada pengajuan proyek.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div id="emptyFilter" class="hidden px-7 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">search_off</span>
        <p class="font-black italic text-on-surface-variant/40 text-sm">Tidak ada proyek ditemukan.</p>
        <button onclick="resetFilter()" class="mt-4 text-[10px] font-black text-primary hover:underline uppercase tracking-widest">
            Reset Filter
        </button>
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
        btn.className = 'filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ' +
            (isActive
                ? 'bg-primary text-on-primary border-primary'
                : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary');
    });
    applyFilter();
}

function applyFilter() {
    const q         = document.getElementById('searchProyek').value.toLowerCase().trim();
    const managerId = document.getElementById('filterManager')?.value ?? 'semua';
    const rows      = document.querySelectorAll('.proyek-row');
    let visible     = 0;

    rows.forEach(r => {
        const matchSearch  = !q || r.dataset.judul.includes(q) || r.dataset.kode.includes(q);
        const matchStatus  = activeFilters.status === 'semua' || r.dataset.status    === activeFilters.status;
        const matchManager = managerId === 'semua' || r.dataset.managerId            === managerId;

        const show = matchSearch && matchStatus && matchManager;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Nomor urut ulang
    let num = 1;
    rows.forEach(r => {
        if (r.style.display !== 'none') r.querySelector('.row-num').textContent = num++;
    });

    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0);

    const isFiltered = q || activeFilters.status !== 'semua' || managerId !== 'semua';
    document.getElementById('btnReset').classList.toggle('hidden', !isFiltered);
    document.getElementById('filterCounter').classList.toggle('hidden', !isFiltered);
    document.getElementById('visibleCount').textContent = visible;
}

function resetFilter() {
    document.getElementById('searchProyek').value = '';
    const elMgr = document.getElementById('filterManager');
    if (elMgr) elMgr.value = 'semua';
    setFilter('status', 'semua');
}
</script>
@endpush