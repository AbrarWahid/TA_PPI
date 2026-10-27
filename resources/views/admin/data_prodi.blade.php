@extends('layouts.app')

@section('title', 'Master Data Program Studi')

@section('content')

<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Master Data <span class="text-[#2dce89]">Program Studi</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            3 Prodi Terdaftar
        </p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    @php
        $prodiList = [
            ['kode' => 'TRIN', 'nama' => 'D4 Teknologi Rekayasa Informatika Industri', 'short' => 'D4 TRIN', 'color' => 'bg-blue-500'],
            ['kode' => 'TRO',  'nama' => 'D4 Teknologi Rekayasa Otomasi',               'short' => 'D4 TRO',  'color' => 'bg-emerald-500'],
            ['kode' => 'TRMO', 'nama' => 'D4 Teknologi Rekayasa Mekatronika',            'short' => 'D4 TRMO', 'color' => 'bg-purple-500'],
        ];
    @endphp
    @foreach($prodiList as $p)
    <div class="bg-white rounded-[1.5rem] border border-gray-100 p-6 shadow-sm flex items-center gap-5">
        <div class="w-14 h-12 rounded-2xl {{ $p['color'] }} flex items-center justify-center text-white font-black text-xs flex-shrink-0 px-2">
            {{ $p['kode'] }}
        </div>
        <div>
            <p class="font-black text-[#004d4d] text-sm leading-tight">{{ $p['short'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-0.5 leading-tight">{{ $p['nama'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- TABLE --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Kode</th>
                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama Program Studi</th>
                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php
                $defaultProdis = [
                    ['id' => 1, 'kode_prodi' => 'TRIN', 'nama_prodi' => 'D4 Teknologi Rekayasa Informatika Industri'],
                    ['id' => 2, 'kode_prodi' => 'TRO',  'nama_prodi' => 'D4 Teknologi Rekayasa Otomasi'],
                    ['id' => 3, 'kode_prodi' => 'TRMO', 'nama_prodi' => 'D4 Teknologi Rekayasa Mekatronika'],
                ];
                $displayProdis = isset($prodis) && count($prodis) ? $prodis : $defaultProdis;
            @endphp
            @foreach($displayProdis as $idx => $prodi)
            @php $isArray = is_array($prodi); @endphp
            <tr class="hover:bg-teal-50/30 transition-colors group">
                <td class="px-10 py-6 text-[10px] font-black text-gray-300">{{ $idx + 1 }}</td>
                <td class="px-10 py-6">
                    <span class="px-4 py-1.5 rounded-xl bg-[#004d4d] text-[#7fffd4] text-xs font-black tracking-wider">
                        {{ $isArray ? $prodi['kode_prodi'] : $prodi->kode_prodi }}
                    </span>
                </td>
                <td class="px-10 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[#7fffd4] text-base">school</span>
                        </div>
                        <span class="font-bold text-[#004d4d] text-sm">
                            {{ $isArray ? $prodi['nama_prodi'] : $prodi->nama_prodi }}
                        </span>
                    </div>
                </td>
                <td class="px-10 py-6 text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl text-[10px] font-black uppercase tracking-widest">
                        <span class="material-symbols-outlined text-xs">check_circle</span> Aktif
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection