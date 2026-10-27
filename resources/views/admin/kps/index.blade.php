@extends('layouts.app')
@section('title', 'Manajemen KPS')
@section('content')

@php
$prodiColor = [
    'informatika' => 'bg-blue-100 text-blue-700',
    'otomasi'     => 'bg-emerald-100 text-emerald-700',
    'mekatronika' => 'bg-purple-100 text-purple-700',
];
$prodiKode = [
    'informatika' => 'TRIN',
    'otomasi'     => 'TRO',
    'mekatronika' => 'TRMO',
];
@endphp

<div class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Manajemen <span class="text-[#2dce89]">KPS</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                Koordinator Program Studi — {{ $kpsList->count() }} dari 3 Prodi Terdaftar
            </p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        @php
            $prodiList = ['informatika', 'otomasi', 'mekatronika'];
        @endphp
        @foreach($prodiList as $p)
        @php
            $kpsProdi = $kpsList->firstWhere('prodi', $p);
        @endphp
        <div class="bg-white border border-gray-100 p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ strtoupper($prodiKode[$p]) }}</p>
                @if($kpsProdi)
                <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                    Terisi
                </span>
                @else
                <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-xl bg-gray-50 text-gray-400 border border-gray-100">
                    Kosong
                </span>
                @endif
            </div>
            @if($kpsProdi)
            <p class="text-sm font-black text-[#004d4d] leading-tight">
                {{ $kpsProdi->user->dosen->nama_dosen ?? $kpsProdi->user->name }}
            </p>
            @else
            <p class="text-sm font-black text-gray-300 italic">Belum ada KPS</p>
            @endif
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-5 gap-6">

        {{-- TABEL KPS AKTIF --}}
        <div class="col-span-3 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-7 py-5 border-b border-gray-100">
                <h4 class="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">account_balance</span>
                    KPS Aktif
                </h4>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Dosen</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Prodi</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kpsList as $kps)
                    <tr class="hover:bg-teal-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                    {{ strtoupper(substr($kps->user->dosen->nama_dosen ?? $kps->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-sm text-[#004d4d]">
                                        {{ $kps->user->dosen->nama_dosen ?? $kps->user->name }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-bold">
                                        {{ $kps->user->dosen->nidn ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl {{ $prodiColor[$kps->prodi] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $prodiKode[$kps->prodi] ?? $kps->prodi }}
                            </span>
                            <p class="text-[10px] text-gray-400 font-bold mt-1">
                                {{ $prodiMap[$kps->prodi] ?? $kps->prodi }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.kps.destroy', $kps->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus KPS ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-200">account_balance</span>
                            <p class="font-black italic text-gray-300 mt-2 text-sm">Belum ada KPS terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FORM ASSIGN KPS --}}
        <div class="col-span-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-7">
                <h4 class="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Tetapkan KPS
                </h4>

                @if(session('error'))
                <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
                    {{ session('error') }}
                </div>
                @endif

                @if(session('success'))
                <div class="mb-4 p-3 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 text-xs font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('admin.kps.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Pilih Dosen
                        </label>
                        <select name="user_id"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->user_id }}" {{ old('user_id') == $d->user_id ? 'selected' : '' }}>
                                {{ $d->nama_dosen }} ({{ $d->nidn }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Program Studi
                        </label>
                        <select name="prodi"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition">
                            <option value="">-- Pilih Prodi --</option>
                            <option value="informatika" {{ old('prodi') == 'informatika' ? 'selected' : '' }}>
                                D4 TRIN – Teknologi Rekayasa Informatika Industri
                            </option>
                            <option value="otomasi" {{ old('prodi') == 'otomasi' ? 'selected' : '' }}>
                                D4 TRO – Teknologi Rekayasa Otomasi
                            </option>
                            <option value="mekatronika" {{ old('prodi') == 'mekatronika' ? 'selected' : '' }}>
                                D4 TRMO – Teknologi Rekayasa Mekatronika
                            </option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg shadow-teal-900/20">
                        <span class="material-symbols-outlined text-base">check</span>
                        TETAPKAN KPS
                    </button>
                </form>
            </div>

            {{-- INFO --}}
            <div class="mt-4 p-5 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500 text-xl mt-0.5">info</span>
                <p class="text-xs font-bold text-amber-700 leading-relaxed">
                    Setiap prodi hanya boleh memiliki <span class="font-black">1 KPS</span>. 
                    Dosen yang ditetapkan sebagai KPS akan mendapatkan akses tambahan saat memilih role login.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection