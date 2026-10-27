@extends('layouts.app')

@section('title', 'Pilih Mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tight text-[#004d4d]">
            Pilih <span class="text-[#2dce89]">Mahasiswa</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">{{ $pengajuan_proyek->judul_proyek }} &mdash; {{ $pengajuan_proyek->kode_pengajuan }}</p>
        <p class="text-xs text-gray-400 mt-0.5">
            Dosen Pengampu: <span class="font-black text-[#004d4d]">{{ $pengajuan_proyek->manager->name }}</span>
            <span class="text-gray-300 mx-1">·</span> otomatis dari pengaju proyek
        </p>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3"
         x-data>
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-lg select-none">search</span>
            <input
                type="text"
                x-model="$store.assignFilter.search"
                placeholder="Cari nama atau NIM mahasiswa..."
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#2dce89] focus:ring-1 focus:ring-[#2dce89] transition"
            >
        </div>
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-lg select-none pointer-events-none">filter_list</span>
            <select
                x-model="$store.assignFilter.filterProdi"
                class="pl-9 pr-8 py-2.5 rounded-xl border border-gray-200 text-sm font-black text-gray-700 focus:outline-none focus:border-[#2dce89] focus:ring-1 focus:ring-[#2dce89] transition appearance-none bg-white cursor-pointer"
            >
                <option value="all">Semua Prodi</option>
                @foreach($pengajuan_proyek->kebutuhan as $k)
                    <option value="{{ $k->prodi }}">{{ labelProdi($k->prodi) }}</option>
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none">expand_more</span>
        </div>
        <button
            type="button"
            x-show="$store.assignFilter.search !== '' || $store.assignFilter.filterProdi !== 'all'"
            x-on:click="$store.assignFilter.search = ''; $store.assignFilter.filterProdi = 'all'"
            class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-black text-gray-400 hover:border-gray-300 hover:text-gray-600 transition"
        >
            Reset
        </button>
    </div>

    <form action="{{ route('pengajuan_proyek.simpan_mahasiswa', $pengajuan_proyek) }}" method="POST"
          x-data="assignForm()"
          x-init="init(
              {{ json_encode($pengajuan_proyek->mahasiswa->pluck('id')->values()) }},
              @json($pengajuan_proyek->mahasiswa->mapWithKeys(fn($m) => [$m->id => $m->pivot->prodi]))
          )">
        @csrf

        @foreach($pengajuan_proyek->kebutuhan as $kebutuhan)
        @php
            $prodi     = $kebutuhan->prodi;
            $jumlah    = $kebutuhan->jumlah_mahasiswa;
            $daftarMhs = $mahasiswaPerProdi[$prodi] ?? collect();
        @endphp

        <div
            x-show="$store.assignFilter.filterProdi === 'all' || $store.assignFilter.filterProdi === '{{ $prodi }}'"
            class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header Prodi --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="font-black text-[#004d4d] text-base">{{ labelProdi($prodi) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Dibutuhkan <span class="font-black text-[#2dce89]">{{ $jumlah }} mahasiswa</span></p>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-black bg-[#004d4d] text-white uppercase tracking-widest">{{ singkatProdi($prodi) }}</span>
            </div>

            {{-- Daftar Mahasiswa --}}
            <div class="p-6">
                @if($daftarMhs->isEmpty())
                    <div class="text-center py-8 text-gray-300">
                        <span class="material-symbols-outlined text-4xl">person_off</span>
                        <p class="text-sm mt-2">Belum ada mahasiswa terdaftar untuk prodi ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-3">
                        <p class="text-xs text-gray-400 font-bold mb-1">Pilih maksimal {{ $jumlah }} mahasiswa:</p>

                        @foreach($daftarMhs as $mhs)
                        @php $mhsId = $mhs->id; @endphp
                        <label
                            x-show="
                                $store.assignFilter.search === '' ||
                                '{{ addslashes(strtolower($mhs->nama)) }}'.includes($store.assignFilter.search.toLowerCase()) ||
                                '{{ strtolower($mhs->nim) }}'.includes($store.assignFilter.search.toLowerCase())
                            "
                            :class="selected[{{ $mhsId }}] ? 'border-[#2dce89] bg-[#2dce89]/5' : 'border-gray-100 hover:border-gray-200'"
                            class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all">

                            <input type="checkbox"
                                name="mahasiswa[]"
                                value="{{ $mhsId }}"
                                class="hidden"
                                :checked="selected[{{ $mhsId }}]"
                                x-on:change="toggle({{ $mhsId }}, '{{ $prodi }}', {{ $jumlah }}, $event)">

                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0 transition-colors"
                                :class="selected[{{ $mhsId }}] ? 'bg-[#2dce89] text-white' : 'bg-gray-100 text-gray-400'">
                                {{ strtoupper(substr($mhs->nama, 0, 2)) }}
                            </div>

                            <div class="flex-1">
                                <p class="font-black text-sm text-[#004d4d]">{{ $mhs->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $mhs->nim }}</p>
                            </div>

                            <span class="material-symbols-outlined text-xl transition-all"
                                :class="selected[{{ $mhsId }}] ? 'text-[#2dce89]' : 'text-gray-200'">
                                check_circle
                            </span>
                        </label>
                        @endforeach

                        <p class="text-xs text-gray-400 mt-1">
                            Terpilih: <span class="font-black text-[#004d4d]" x-text="countSelected('{{ $prodi }}')"></span> / {{ $jumlah }}
                        </p>

                        <p x-show="countSelected('{{ $prodi }}') > {{ $jumlah }}"
                           class="text-xs font-black text-red-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">warning</span>
                            Melebihi kuota! Maksimal {{ $jumlah }} mahasiswa untuk prodi ini.
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('pengajuan_proyek.show', $pengajuan_proyek) }}"
                class="px-6 py-3 rounded-2xl border-2 border-gray-200 text-sm font-black text-gray-400 hover:border-gray-300 transition">
                Kembali
            </a>
            <button type="submit"
                class="px-8 py-3 rounded-2xl text-sm font-black text-[#7fffd4] transition"
                style="background-color: #004d4d;">
                Simpan Penugasan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('assignFilter', {
        search: '',
        filterProdi: 'all',
    });
});

function assignForm() {
    return {
        selected: {},
        prodiMap: {},

        init(sudahDipilih, prodiSeed) {
            sudahDipilih.forEach(id => {
                this.selected[id] = true;
            });
            Object.entries(prodiSeed).forEach(([id, prodi]) => {
                this.prodiMap[id] = prodi;
            });
        },

        toggle(id, prodi, maxKuota, event) {
            if (event.target.checked) {
                if (this.countSelected(prodi) >= maxKuota) {
                    event.target.checked = false;
                    return;
                }
                this.selected[id] = true;
                this.prodiMap[id] = prodi;
            } else {
                this.selected[id] = false;
            }
        },

        countSelected(prodi) {
            return Object.entries(this.selected).filter(
                ([id, val]) => val === true && this.prodiMap[id] === prodi
            ).length;
        }
    };
}
</script>
@endsection