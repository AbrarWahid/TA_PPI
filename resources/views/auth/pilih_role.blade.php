<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Mode Peran — PBL Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.design-system')
</head>
<body class="min-h-screen bg-[#fafafa] font-sans text-[#0b0b0c] antialiased flex items-center justify-center p-6">

    @php
        $user           = auth()->user();
        $availableRoles = $user->getAvailableRoles();
        $bisaManager    = in_array('manager_proyek', $availableRoles);
        $bisaDosen      = in_array('dosen_pengampu', $availableRoles);
        $bisaKps        = in_array('kps', $availableRoles);
        $totalRole      = count($availableRoles);
    @endphp

    <div class="w-full max-w-2xl">

        <div class="text-center mb-8">
            <div class="w-10 h-10 rounded-lg bg-[#013981] flex items-center justify-center mx-auto mb-4 overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 object-contain"
                     onerror="this.style.display='none';">
            </div>
            <p class="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1.5">SESI MULTI-PERAN</p>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                Pilih mode kerja aktif
            </h1>
            <p class="text-xs text-[#6a6f7c] mt-1.5">
                Halo, <span class="font-medium text-[#0b0b0c]">{{ $user->name }}</span>. Pilih peran untuk masuk ke dashboard.
            </p>
        </div>

        <div class="grid gap-3.5 mb-6 {{ $totalRole === 1 ? 'grid-cols-1 max-w-xs mx-auto' : ($totalRole === 2 ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1 sm:grid-cols-3') }}">

            @if($bisaManager)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="manager_proyek">
                <button type="submit"
                    class="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">rocket_launch</span>
                        </div>
                        <p class="font-semibold text-[#0b0b0c] text-base">Manager Proyek</p>
                        <p class="text-xs text-[#6a6f7c] mt-1 leading-relaxed">Kelola pengajuan proyek PBL &amp; verifikasi logbook mingguan.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                        <span class="font-mono text-[10px] text-[#6a6f7c] uppercase">Bobot 55%</span>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">arrow_forward</span>
                    </div>
                </button>
            </form>
            @endif

            @if($bisaDosen)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="dosen_pengampu">
                <button type="submit"
                    class="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">school</span>
                        </div>
                        <p class="font-semibold text-[#0b0b0c] text-base">Dosen Pengampu</p>
                        <p class="text-xs text-[#6a6f7c] mt-1 leading-relaxed">Supervisi mata kuliah, verifikasi laporan &amp; penilaian akademik.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                        <span class="font-mono text-[10px] text-[#6a6f7c] uppercase">Bobot 45%</span>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">arrow_forward</span>
                    </div>
                </button>
            </form>
            @endif

            @if($bisaKps)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="kps">
                <button type="submit"
                    class="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">account_balance</span>
                        </div>
                        <p class="font-semibold text-[#0b0b0c] text-base">Koordinator Prodi</p>
                        <p class="text-xs text-[#6a6f7c] mt-1 leading-relaxed">Monitoring mahasiswa, parameter penilaian &amp; ekspor transkrip.</p>
                    </div>
                    @php $prodiKps = $user->getProdiKps(); @endphp
                    <div class="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                        <span class="font-mono text-[10px] text-[#0b0b0c] bg-[#f4f4f5] px-2 py-0.5 rounded uppercase">{{ $prodiKps ? strtoupper($prodiKps) : 'KPS' }}</span>
                        <span class="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">arrow_forward</span>
                    </div>
                </button>
            </form>
            @endif

        </div>

        <div class="text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-[#6a6f7c] hover:text-[#b91c1c] font-medium transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">logout</span>
                    <span>Keluar dari akun ini</span>
                </button>
            </form>
        </div>

    </div>

</body>
</html>
