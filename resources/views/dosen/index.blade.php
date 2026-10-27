@extends('layouts.app')
@section('title', 'Data Dosen')
@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Master Data <span class="text-[#2dce89]">Dosen</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            {{ $dosens->count() }} dosen terdaftar
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('dosen.batch.create') }}"
           class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg text-[#004d4d]"
           style="background:#7fffd4;">
            <span class="material-symbols-outlined text-base">table_rows</span> Input Massal
        </a>
        <a href="{{ route('dosen.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-primary text-on-primary rounded-2xl text-xs font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg">
            <span class="material-symbols-outlined text-base">person_add</span> Tambah Dosen
        </a>
    </div>
</div>

{{-- STAT CARDS --}}
@php
    $cDosen    = $dosens->filter(fn($d) => $d->user && $d->user->akses_role === 'dosen_pengampu')->count();
    $cManager  = $dosens->filter(fn($d) => $d->user && $d->user->akses_role === 'manager_proyek')->count();
    $cKeduanya = $dosens->filter(fn($d) => $d->user && $d->user->akses_role === 'keduanya')->count();
@endphp
<div class="grid grid-cols-4 gap-5 mb-8">
    <div class="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36" style="background:#004d4d;">
        <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Dosen</p>
        <p class="text-5xl font-black italic tracking-tighter text-white">{{ $dosens->count() }}</p>
        <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5" style="font-size:100px;">school</span>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-amber-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-base">school</span>
            <p class="text-amber-500 text-[10px] font-black uppercase tracking-widest">Dosen Pengampu</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cDosen }}</p>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-blue-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-base">manage_accounts</span>
            <p class="text-blue-500 text-[10px] font-black uppercase tracking-widest">Manager Proyek</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cManager }}</p>
    </div>
    <div class="bg-white rounded-[2rem] border-2 border-teal-200 p-7 flex flex-col justify-between h-36 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-500 text-base">swap_horiz</span>
            <p class="text-teal-500 text-[10px] font-black uppercase tracking-widest">Keduanya</p>
        </div>
        <p class="text-5xl font-black italic tracking-tighter text-on-surface">{{ $cKeduanya }}</p>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">

    {{-- FILTER BAR --}}
    <div class="px-7 py-5 border-b border-outline-variant/10 flex flex-wrap items-center gap-3">

        {{-- Search: Nama / NIDN / Email --}}
        <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">search</span>
            <input type="text" id="searchDosen" oninput="applyFilter()" placeholder="Cari nama, NIDN, atau email..."
                class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-outline-variant/20"></div>

        {{-- Filter Akses Role --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-outline uppercase tracking-widest">Role:</span>
            @foreach(['semua'=>'Semua','dosen_pengampu'=>'Dosen Pengampu','manager_proyek'=>'Manager','keduanya'=>'Keduanya'] as $val=>$lbl)
            <button onclick="setFilter('role','{{ $val }}')"
                data-filter="role" data-value="{{ $val }}"
                class="filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all
                {{ $val==='semua' ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary' }}">
                {{ $lbl }}
            </button>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-outline-variant/20"></div>

        {{-- Filter Status Akun --}}
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-outline uppercase tracking-widest">Status:</span>
            @foreach(['semua'=>'Semua','active'=>'Aktif','pending'=>'Pending'] as $val=>$lbl)
            <button onclick="setFilter('status','{{ $val }}')"
                data-filter="status" data-value="{{ $val }}"
                class="filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all
                {{ $val==='semua' ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary' }}">
                {{ $lbl }}
            </button>
            @endforeach
        </div>

        {{-- Reset Filter --}}
        <button onclick="resetFilter()"
            id="btnReset"
            class="hidden ml-auto flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:border-red-200 transition-all">
            <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
        </button>

        {{-- Counter hasil --}}
        <span id="filterCounter" class="ml-auto text-[10px] font-black text-outline hidden">
            — <span id="visibleCount">0</span> dosen ditemukan
        </span>
    </div>

    {{-- TABLE --}}
    <table class="w-full text-left" id="dosenTable">
        <thead>
            <tr class="border-b border-outline-variant/10 bg-surface-container-low/30">
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">#</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Identitas</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">NIDN</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Akses Role</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Status</th>
                <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10" id="dosenTbody">
            @forelse($dosens as $idx => $dosen)
            @php
                $aksesRole = $dosen->user->akses_role ?? 'dosen_pengampu';
                $status    = $dosen->user->status ?? 'active';
            @endphp
            <tr class="dosen-row hover:bg-surface-container-lowest transition-colors group"
                data-name="{{ strtolower($dosen->nama_dosen) }}"
                data-nidn="{{ $dosen->nidn }}"
                data-email="{{ strtolower($dosen->user->email ?? '') }}"
                data-role="{{ $aksesRole }}"
                data-status="{{ $status }}">

                <td class="px-7 py-5 text-[10px] font-black text-outline/40 row-num">{{ $idx + 1 }}</td>

                <td class="px-7 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0
                            {{ $aksesRole === 'manager_proyek' ? 'bg-blue-100 text-blue-700' : ($aksesRole === 'keduanya' ? 'bg-teal-100 text-teal-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ strtoupper(substr($dosen->nama_dosen, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-black text-on-surface text-sm leading-none">{{ $dosen->nama_dosen }}</p>
                            <p class="text-[10px] text-outline font-medium mt-0.5">{{ $dosen->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </td>

                <td class="px-7 py-5">
                    <span class="inline-flex px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-xl text-[10px] font-black font-mono tracking-wider">
                        {{ $dosen->nidn }}
                    </span>
                </td>

                <td class="px-7 py-5">
                    @if($aksesRole === 'keduanya')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">swap_horiz</span> Keduanya
                        </span>
                    @elseif($aksesRole === 'manager_proyek')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">manage_accounts</span> Manager Proyek
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">school</span> Dosen Pengampu
                        </span>
                    @endif
                </td>

                <td class="px-7 py-5 text-center">
                    @if($status === 'active')
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">check_circle</span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-500 border border-amber-100 rounded-xl text-[10px] font-black">
                            <span class="material-symbols-outlined text-sm">schedule</span> Pending
                        </span>
                    @endif
                </td>

                <td class="px-7 py-5">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('dosen.edit', $dosen->id) }}"
                           class="p-2.5 text-outline hover:text-primary hover:bg-primary-container rounded-xl transition-all"
                           title="Edit">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </a>
                        <form action="{{ route('dosen.destroy', $dosen->id) }}" method="POST"
                              onsubmit="return confirm('Hapus dosen ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-2.5 text-outline hover:text-white hover:bg-red-500 rounded-xl transition-all"
                                    title="Hapus">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="emptyData">
                <td colspan="6" class="px-7 py-20 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">person_search</span>
                    <p class="font-black italic text-on-surface-variant/40 text-sm">Belum ada data dosen.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Empty filter state --}}
    <div id="emptyFilter" class="hidden px-7 py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">search_off</span>
        <p class="font-black italic text-on-surface-variant/40 text-sm">Tidak ada dosen ditemukan.</p>
        <button onclick="resetFilter()" class="mt-4 text-[10px] font-black text-primary hover:underline uppercase tracking-widest">
            Reset Filter
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
// State filter aktif
const activeFilters = {
    role:   'semua',
    status: 'semua',
};

// Set filter & update tombol
function setFilter(type, value) {
    activeFilters[type] = value;

    // Update tampilan tombol per grup
    document.querySelectorAll(`[data-filter="${type}"]`).forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.className = 'filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ' +
            (isActive
                ? 'bg-primary text-on-primary border-primary'
                : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary');
    });

    applyFilter();
}

// Terapkan semua filter sekaligus
function applyFilter() {
    const q      = document.getElementById('searchDosen').value.toLowerCase().trim();
    const rows   = document.querySelectorAll('.dosen-row');
    let visible  = 0;

    rows.forEach(r => {
        const matchSearch = !q ||
            r.dataset.name.includes(q)  ||
            r.dataset.nidn.includes(q)  ||
            r.dataset.email.includes(q);

        const matchRole   = activeFilters.role   === 'semua' || r.dataset.role   === activeFilters.role;
        const matchStatus = activeFilters.status === 'semua' || r.dataset.status === activeFilters.status;

        const show = matchSearch && matchRole && matchStatus;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Nomor urut ulang
    let num = 1;
    rows.forEach(r => {
        if (r.style.display !== 'none') {
            r.querySelector('.row-num').textContent = num++;
        }
    });

    // Empty state
    document.getElementById('emptyFilter').classList.toggle('hidden', visible > 0);

    // Counter & reset btn
    const isFiltered = q || activeFilters.role !== 'semua' || activeFilters.status !== 'semua';
    document.getElementById('btnReset').classList.toggle('hidden', !isFiltered);
    document.getElementById('filterCounter').classList.toggle('hidden', !isFiltered);
    document.getElementById('visibleCount').textContent = visible;
}

// Reset semua filter
function resetFilter() {
    document.getElementById('searchDosen').value = '';
    setFilter('role', 'semua');
    setFilter('status', 'semua');
}
</script>
@endpush