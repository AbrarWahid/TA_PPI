@extends('layouts.app')

@section('title', 'Approval Akun')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Daftar <span class="text-[#2dce89]">Tunggu</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Persetujuan Akun Dosen Baru
        </p>
    </div>
    <span class="px-5 py-2.5 rounded-2xl text-xs font-black text-white shadow-lg"
          style="background:{{ $pendingDosen->count() > 0 ? '#f59e0b' : '#004d4d' }};">
        {{ $pendingDosen->count() }} Permintaan
    </span>
</div>

@if(session('success'))
<div class="mb-6 px-5 py-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold flex items-center gap-2">
    <span class="material-symbols-outlined text-base">check_circle</span>
    {{ session('success') }}
</div>
@endif

{{-- PENDING --}}
<div class="mb-10">
    @if($pendingDosen->isEmpty())
    <div class="bg-white border border-gray-100 rounded-[2rem] p-16 text-center shadow-sm">
        <span class="material-symbols-outlined text-6xl text-gray-200 block mb-3">how_to_reg</span>
        <p class="font-black italic text-gray-300 uppercase tracking-widest text-sm">Antrean Kosong</p>
        <p class="text-xs text-gray-300 mt-2">Tidak ada permintaan akun baru.</p>
    </div>
    @else
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">NIDN</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Terdaftar</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pendingDosen as $u)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-7 py-5">
                        <div class="flex items-center gap-2 text-gray-500">
                            <span class="material-symbols-outlined text-[#2dce89] text-base">mail</span>
                            <span class="text-sm font-bold">{{ $u->email }}</span>
                        </div>
                    </td>
                    <td class="px-7 py-5">
                        <span class="px-3 py-1 rounded-lg bg-gray-100 font-mono text-xs font-black text-gray-600">
                            {{ $u->dosen?->nidn ?? '-' }}
                        </span>
                    </td>
                    <td class="px-7 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs text-[#7fffd4] shrink-0"
                                 style="background:#004d4d;">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <span class="font-black text-gray-800 text-sm">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-7 py-5 text-center text-xs text-gray-400 font-bold">
                        {{ $u->created_at->format('d M Y') }}
                    </td>
                    <td class="px-7 py-5">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('approval.reject', $u) }}"
                                  onsubmit="return confirm('Tolak dan hapus akun {{ $u->name }}?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 rounded-xl text-xs font-black border border-red-200 hover:bg-red-500 hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                    Tolak
                                </button>
                            </form>
                            <button type="button"
                                onclick="bukaPopup({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black text-[#004d4d] shadow-lg hover:scale-105 transition-all"
                                style="background:#7fffd4;">
                                <span class="material-symbols-outlined text-sm">check</span>
                                Setujui
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- SUDAH DISETUJUI --}}
<div>
    {{-- Heading + Filter Bar --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <h2 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Sudah <span class="text-[#2dce89]">Disetujui</span>
            </h2>
            <span id="approvedCount" class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700">
                {{ $approvedDosen->count() }}
            </span>
        </div>

        {{-- FILTER BAR --}}
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">search</span>
                <input type="text" id="searchApproved" placeholder="Cari nama / NIDN..."
                    class="pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 w-52 transition-all">
            </div>
            <select id="filterAksesRole"
                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 transition-all">
                <option value="">Semua Role</option>
                <option value="dosen_pengampu">Dosen Pengampu</option>
                <option value="manager_proyek">Manager Proyek</option>
                <option value="keduanya">Keduanya</option>
            </select>
        </div>
    </div>

    @if($approvedDosen->isEmpty())
    <div class="bg-white border border-gray-100 rounded-[2rem] p-8 text-center shadow-sm">
        <p class="text-sm text-gray-300 font-black italic">Belum ada dosen yang disetujui.</p>
    </div>
    @else
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">NIDN</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Akses Role</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                    <th class="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Terdaftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($approvedDosen as $u)
                @php
                    $akses = $u->akses_role ?? 'keduanya';
                    $aksesLabel = match($akses) {
                        'manager_proyek' => 'Manager Proyek',
                        'dosen_pengampu' => 'Dosen Pengampu',
                        default          => 'Keduanya',
                    };
                    $aksesColor = match($akses) {
                        'manager_proyek' => 'bg-blue-100 text-blue-700',
                        'dosen_pengampu' => 'bg-purple-100 text-purple-700',
                        default          => 'bg-teal-100 text-teal-700',
                    };
                @endphp
                {{-- data-nama & data-nidn lowercase untuk matching JS --}}
                <tr class="hover:bg-gray-50/50 transition-colors approved-row"
                    data-nama="{{ strtolower($u->name) }}"
                    data-nidn="{{ strtolower($u->dosen?->nidn ?? '') }}"
                    data-akses="{{ $akses }}">
                    <td class="px-7 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center font-black text-xs text-[#004d4d] shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-7 py-4 text-sm text-gray-500">{{ $u->email }}</td>
                    <td class="px-7 py-4 font-mono text-sm text-gray-500">{{ $u->dosen?->nidn ?? '-' }}</td>
                    <td class="px-7 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $aksesColor }}">
                            {{ $aksesLabel }}
                        </span>
                    </td>
                    <td class="px-7 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            Aktif
                        </span>
                    </td>
                    <td class="px-7 py-4 text-sm text-gray-400">{{ $u->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Empty state saat filter tidak cocok --}}
        <div id="noResultApproved" class="hidden px-10 py-16 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-300 block mb-3">search_off</span>
            <p class="text-gray-400 font-black italic text-sm uppercase">Tidak ada hasil yang cocok.</p>
        </div>
    </div>
    @endif
</div>

{{-- POPUP PILIH ROLE --}}
<div id="popupApprove" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6"
     style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                 style="background:#004d4d;">
                <span class="material-symbols-outlined text-[#7fffd4] text-2xl">manage_accounts</span>
            </div>
            <h3 class="text-xl font-black text-[#004d4d] uppercase tracking-tight italic">
                Atur Akses <span class="text-[#2dce89]">Role</span>
            </h3>
            <p class="text-xs text-gray-400 font-bold mt-1" id="popupNama">—</p>
        </div>

        <form id="formApprove" method="POST" action="">
            @csrf @method('PATCH')
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                Pilih akses yang diberikan:
            </p>
            <div class="grid grid-cols-1 gap-3 mb-6">
                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-[#2dce89] cursor-pointer transition-all">
                    <input type="radio" name="akses_role" value="dosen_pengampu" class="accent-[#004d4d]">
                    <div class="w-10 h-10 rounded-xl bg-[#2dce89] flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[#004d4d] text-xl">school</span>
                    </div>
                    <div>
                        <p class="font-black text-[#004d4d] text-sm uppercase">Dosen Pengampu</p>
                        <p class="text-[10px] text-gray-400">Verifikasi laporan & beri nilai (45%)</p>
                    </div>
                </label>
                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-[#2dce89] cursor-pointer transition-all">
                    <input type="radio" name="akses_role" value="manager_proyek" class="accent-[#004d4d]">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#004d4d;">
                        <span class="material-symbols-outlined text-[#7fffd4] text-xl">rocket_launch</span>
                    </div>
                    <div>
                        <p class="font-black text-[#004d4d] text-sm uppercase">Manager Proyek</p>
                        <p class="text-[10px] text-gray-400">Kelola & ajukan proyek PBL (55%)</p>
                    </div>
                </label>
                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 border-[#2dce89] bg-teal-50/50 cursor-pointer transition-all">
                    <input type="radio" name="akses_role" value="keduanya" class="accent-[#004d4d]" checked>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-[#004d4d] to-[#2dce89]">
                        <span class="material-symbols-outlined text-white text-xl">swap_horiz</span>
                    </div>
                    <div>
                        <p class="font-black text-[#004d4d] text-sm uppercase">Keduanya</p>
                        <p class="text-[10px] text-gray-400">Dosen bisa switch role saat login</p>
                    </div>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="tutupPopup()"
                    class="flex-1 px-5 py-3 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-5 py-3 rounded-2xl text-xs font-black text-[#004d4d] shadow-lg hover:scale-105 transition-all"
                    style="background:#7fffd4;">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">check</span>
                    Setujui & Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Popup ──────────────────────────────────────────────
function bukaPopup(userId, nama) {
    document.getElementById('popupNama').textContent = nama;
    document.getElementById('formApprove').action = '/approval-dosen/' + userId + '/approve-role';
    document.getElementById('popupApprove').classList.remove('hidden');
}
function tutupPopup() {
    document.getElementById('popupApprove').classList.add('hidden');
}
document.getElementById('popupApprove').addEventListener('click', function(e) {
    if (e.target === this) tutupPopup();
});

// ── Multi-Filter "Sudah Disetujui" ────────────────────
(function () {
    const searchInput = document.getElementById('searchApproved');
    const roleSelect  = document.getElementById('filterAksesRole');
    const rows        = document.querySelectorAll('.approved-row');
    const noResult    = document.getElementById('noResultApproved');
    const countBadge  = document.getElementById('approvedCount');

    if (!searchInput || !roleSelect) return; // guard: tabel kosong

    function applyFilter() {
        const keyword = searchInput.value.toLowerCase().trim();
        const role    = roleSelect.value;
        let visible   = 0;

        rows.forEach(row => {
            const nama  = row.dataset.nama  || '';
            const nidn  = row.dataset.nidn  || '';
            const akses = row.dataset.akses || '';

            const matchSearch = !keyword || nama.includes(keyword) || nidn.includes(keyword);
            const matchRole   = !role    || akses === role;

            if (matchSearch && matchRole) {
                row.classList.remove('hidden');
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });

        noResult.classList.toggle('hidden', visible > 0);
        countBadge.textContent = visible;
    }

    searchInput.addEventListener('input', applyFilter);
    roleSelect.addEventListener('change', applyFilter);
})();
</script>
@endpush