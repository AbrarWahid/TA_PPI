@extends('layouts.app')

@section('title', 'Approval Akun')

@section('content')

<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Daftar <span class="text-[#2dce89]">Tunggu</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Persetujuan Akun Dosen Baru
        </p>
    </div>
    <div class="px-4 py-2 bg-[#7fffd4] text-[#004d4d] rounded-full text-xs font-black italic">
        {{ $pendingDosen->count() }} Permintaan
    </div>
</div>

{{-- PENDING --}}
<div class="mb-10">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">NIDN</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Role</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pendingDosen as $u)
                <tr class="hover:bg-teal-50/30 transition-all group">
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="material-symbols-outlined text-[#008080] text-base">mail</span>
                            <span class="text-sm font-bold">{{ $u->email }}</span>
                        </div>
                    </td>
                    <td class="px-10 py-8 text-center">
                        <span class="px-4 py-1.5 rounded-lg bg-gray-100 text-xs font-black text-gray-500 tracking-wider">
                            {{ $u->dosen?->nidn ?? '-' }}
                        </span>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#004d4d] text-[#7fffd4] flex items-center justify-center font-black text-xs shadow-md flex-shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <span class="font-black italic text-[#004d4d] uppercase tracking-tight">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-10 py-8 text-center">
                        @php
                            $roleColor = $u->role === 'manager_proyek' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700';
                            $roleLabel = $u->role === 'manager_proyek' ? 'Manager Proyek' : 'Dosen';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $roleColor }}">{{ $roleLabel }}</span>
                    </td>
                    <td class="px-10 py-8 text-right">
                        <div class="flex justify-end gap-3">
                            <form method="POST" action="{{ route('approval.reject', $u) }}"
                                  onsubmit="return confirm('Tolak dan hapus akun {{ $u->name }}?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-2 px-6 py-3 bg-red-50 text-red-500 rounded-xl text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-sm">close</span> Tolak
                                </button>
                            </form>
                            <form method="POST" action="{{ route('approval.approve', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-2 px-6 py-3 bg-[#7fffd4] text-[#004d4d] rounded-xl text-[10px] font-black uppercase shadow-lg shadow-[#7fffd4]/20 hover:bg-[#004d4d] hover:text-[#7fffd4] transition-all">
                                    <span class="material-symbols-outlined text-sm">check</span> Setujui
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-32 text-center">
                        <div class="flex flex-col items-center opacity-20">
                            <span class="material-symbols-outlined text-[#004d4d] mb-4" style="font-size:60px;">how_to_reg</span>
                            <p class="font-black italic uppercase text-2xl tracking-tighter text-[#004d4d]">Antrean Kosong</p>
                            <p class="text-xs font-bold uppercase mt-2 text-gray-500">Tidak ada permintaan baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SUDAH DISETUJUI --}}
<div>
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-black italic text-[#004d4d] uppercase tracking-tighter">Sudah <span class="text-[#2dce89]">Disetujui</span></h2>
            <span id="approvedCount" class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700">{{ $approvedDosen->count() }}</span>
        </div>

        {{-- FILTER BAR --}}
        <div class="flex items-center gap-3">
            {{-- Search Nama / NIDN --}}
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">search</span>
                <input type="text" id="searchApproved" placeholder="Cari nama / NIDN..."
                    class="pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 w-52 transition-all">
            </div>

            {{-- Filter Akses Role --}}
            <select id="filterAksesRole"
                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 transition-all">
                <option value="">Semua Role</option>
                <option value="dosen_pengampu">Dosen Pengampu</option>
                <option value="manager_proyek">Manager Proyek</option>
                <option value="keduanya">Keduanya</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">NIDN</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Akses Role</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Terdaftar</th>
                </tr>
            </thead>
            <tbody id="approvedTableBody" class="divide-y divide-gray-50">
                @forelse($approvedDosen as $u)
                @php
                    $akses = $u->akses_role ?? '-';
                    $aksesLabel = match($akses) {
                        'dosen_pengampu' => 'Dosen Pengampu',
                        'manager_proyek' => 'Manager Proyek',
                        'keduanya'       => 'Keduanya',
                        default          => $akses,
                    };
                    $aksesColor = match($akses) {
                        'dosen_pengampu' => 'bg-violet-100 text-violet-700',
                        'manager_proyek' => 'bg-blue-100 text-blue-700',
                        'keduanya'       => 'bg-teal-100 text-teal-700',
                        default          => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <tr class="hover:bg-teal-50/30 transition-colors approved-row"
                    data-nama="{{ strtolower($u->name) }}"
                    data-nidn="{{ $u->dosen?->nidn ?? '' }}"
                    data-akses="{{ $akses }}">
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center font-black text-[#7fffd4] text-xs shadow-md flex-shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <span class="font-black italic text-[#004d4d] uppercase tracking-tight text-sm">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-10 py-6 text-sm text-gray-500">{{ $u->email }}</td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-4 py-1.5 rounded-lg bg-gray-100 text-xs font-black text-gray-500 tracking-wider">
                            {{ $u->dosen?->nidn ?? '-' }}
                        </span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $aksesColor }}">{{ $aksesLabel }}</span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <span class="flex items-center justify-center gap-1 text-[10px] font-black text-emerald-600">
                            <span class="material-symbols-outlined text-sm">check_circle</span> Aktif
                        </span>
                    </td>
                    <td class="px-10 py-6 text-sm text-gray-400 font-bold">{{ $u->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr id="emptyApproved">
                    <td colspan="6" class="px-10 py-10 text-center text-gray-400 font-black italic text-sm opacity-40">
                        Belum ada dosen yang disetujui.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Empty state saat filter tidak cocok --}}
        <div id="noResultApproved" class="hidden px-10 py-16 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-300 block mb-3">search_off</span>
            <p class="text-gray-400 font-black italic text-sm uppercase">Tidak ada hasil yang cocok.</p>
        </div>
    </div>
</div>

<script>
(function () {
    const searchInput   = document.getElementById('searchApproved');
    const roleSelect    = document.getElementById('filterAksesRole');
    const rows          = document.querySelectorAll('.approved-row');
    const noResult      = document.getElementById('noResultApproved');
    const countBadge    = document.getElementById('approvedCount');

    function applyFilter() {
        const keyword = searchInput.value.toLowerCase().trim();
        const role    = roleSelect.value;
        let visible   = 0;

        rows.forEach(row => {
            const nama  = row.dataset.nama  || '';
            const nidn  = row.dataset.nidn  || '';
            const akses = row.dataset.akses || '';

            const matchSearch = !keyword || nama.includes(keyword) || nidn.includes(keyword);
            const matchRole   = !role   || akses === role;

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

@endsection