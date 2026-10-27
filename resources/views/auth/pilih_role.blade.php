<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Mode — PBL Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-[#f0faf8] flex items-center justify-center p-6">

    @php
        $user           = auth()->user();
        $availableRoles = $user->getAvailableRoles();
        $bisaManager    = in_array('manager_proyek', $availableRoles);
        $bisaDosen      = in_array('dosen_pengampu', $availableRoles);
        $bisaKps        = in_array('kps', $availableRoles);
        $totalRole      = count($availableRoles);
    @endphp

    <div class="w-full max-w-2xl">

        {{-- Logo & Title --}}
        <div class="text-center mb-10">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-4 rounded-2xl">
            <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
                Masuk <span class="text-[#2dce89]">Sebagai</span>
            </h1>
            <p class="text-sm text-slate-400 mt-2 font-medium">
                Halo, <span class="font-black text-[#004d4d]">{{ $user->name }}</span>! Pilih mode untuk melanjutkan.
            </p>
        </div>

        {{-- Pilihan Role --}}
        <div class="grid gap-4 mb-6 {{ $totalRole === 1 ? 'grid-cols-1 max-w-xs mx-auto' : ($totalRole === 2 ? 'grid-cols-2' : 'grid-cols-3') }}">

            {{-- Manager Proyek --}}
            @if($bisaManager)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="manager_proyek">
                <button type="submit"
                    class="w-full bg-white border-2 border-gray-100 hover:border-[#2dce89] hover:bg-teal-50/50 rounded-[2rem] p-8 text-center transition-all group shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 rounded-2xl bg-[#004d4d] flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[#7fffd4] text-3xl">rocket_launch</span>
                    </div>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Manager</p>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Proyek</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">Kelola & ajukan proyek PBL</p>
                    <div class="mt-4 inline-flex items-center gap-1 text-[10px] font-black text-[#2dce89] uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">assessment</span>
                        Bobot 55%
                    </div>
                </button>
            </form>
            @endif

            {{-- Dosen Pengampu --}}
            @if($bisaDosen)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="dosen_pengampu">
                <button type="submit"
                    class="w-full bg-white border-2 border-gray-100 hover:border-[#2dce89] hover:bg-teal-50/50 rounded-[2rem] p-8 text-center transition-all group shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 rounded-2xl bg-[#2dce89] flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[#004d4d] text-3xl">school</span>
                    </div>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Dosen</p>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Pengampu</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">Verifikasi laporan & beri nilai</p>
                    <div class="mt-4 inline-flex items-center gap-1 text-[10px] font-black text-[#2dce89] uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">assessment</span>
                        Bobot 45%
                    </div>
                </button>
            </form>
            @endif

            {{-- KPS --}}
            @if($bisaKps)
            <form action="{{ route('pilih.role.simpan') }}" method="POST">
                @csrf
                <input type="hidden" name="role_aktif" value="kps">
                <button type="submit"
                    class="w-full bg-white border-2 border-gray-100 hover:border-[#2dce89] hover:bg-teal-50/50 rounded-[2rem] p-8 text-center transition-all group shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-white text-3xl">account_balance</span>
                    </div>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Koordinator</p>
                    <p class="font-black text-[#004d4d] text-lg uppercase tracking-tight">Program Studi</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">
                        Monitoring & manajemen prodi
                    </p>
                    @php $prodiKps = $user->getProdiKps(); @endphp
                    @if($prodiKps)
                    <div class="mt-4 inline-flex items-center gap-1 text-[10px] font-black text-blue-600 uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">domain</span>
                        {{ strtoupper($prodiKps) }}
                    </div>
                    @endif
                </button>
            </form>
            @endif

        </div>

        {{-- Logout --}}
        <div class="text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-red-400 font-medium transition flex items-center gap-1 mx-auto">
                    <span class="material-symbols-outlined text-sm">logout</span>
                    Keluar dari akun ini
                </button>
            </form>
        </div>

    </div>

</body>
</html>