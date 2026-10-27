@extends('layouts.app')

@section('title', 'Ajukan Proyek')

@section('content')
<div class="flex-1 flex flex-col">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('pengajuan_proyek.index') }}"
           class="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Ajukan <span class="text-[#2dce89]">Proyek Baru</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">Isi form pengajuan proyek PBL</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pengajuan_proyek.store') }}">
        @csrf

        <div class="grid grid-cols-3 gap-6">

            {{-- KOLOM KIRI (2/3) --}}
            <div class="col-span-2 space-y-6">

                {{-- Info Proyek --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                    <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#2dce89]">info</span> Informasi Proyek
                    </h2>

                    <div class="mb-5">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Judul Proyek</label>
                        <input type="text" name="judul_proyek" value="{{ old('judul_proyek') }}" required
                               class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                               placeholder="Nama proyek PBL">
                        @error('judul_proyek') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" required
                                  class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm text-gray-700 resize-none"
                                  placeholder="Deskripsi singkat proyek">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tujuan</label>
                        <textarea name="tujuan" rows="3" required
                                  class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm text-gray-700 resize-none"
                                  placeholder="Tujuan proyek">{{ old('tujuan') }}</textarea>
                        @error('tujuan') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                            @error('tanggal_mulai') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                            @error('tanggal_selesai') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Kebutuhan Tim --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8" x-data="kebutuhanForm()">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#2dce89]">groups</span> Kebutuhan Tim
                        </h2>
                        <button type="button" @click="tambah()"
                                class="flex items-center gap-1.5 px-4 py-2 bg-teal-50 text-[#004d4d] border border-teal-100 rounded-xl text-[10px] font-black hover:bg-[#004d4d] hover:text-[#7fffd4] transition-all">
                            <span class="material-symbols-outlined text-sm">add</span> TAMBAH PRODI
                        </button>
                    </div>

                    @error('kebutuhan') <p class="text-red-500 text-xs font-bold mb-4">{{ $message }}</p> @enderror

                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Program Studi</label>
                                <select :name="`kebutuhan[${idx}][prodi]`" x-model="item.prodi"
                                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                                    <option value="">-- Pilih Prodi --</option>
                                    <option value="mekatronika">D4 TRMO - Teknologi Rekayasa Mekatronika</option>
<option value="otomasi">D4 TRO - Teknologi Rekayasa Otomasi</option>
<option value="informatika">D4 TRIN - Teknologi Rekayasa Informatika Industri</option>
                                </select>
                            </div>
                            <div class="w-32">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Jumlah</label>
                                <input type="number" :name="`kebutuhan[${idx}][jumlah]`" x-model="item.jumlah"
                                       min="1" max="50"
                                       class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                       placeholder="0">
                            </div>
                            <div class="pt-6">
                                <button type="button" @click="hapus(idx)"
                                        x-show="items.length > 1"
                                        class="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- KOLOM KANAN (1/3) --}}
            <div class="space-y-6">

                {{-- Anggaran --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                    <h2 class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#2dce89]">payments</span> Anggaran
                    </h2>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Estimasi Anggaran (Rp)</label>
                    <input type="number" name="anggaran" value="{{ old('anggaran') }}" min="0"
                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                           placeholder="0">
                    @error('anggaran') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Submit --}}
                <div class="bg-[#004d4d] rounded-[2rem] p-8">
                    <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-2">Siap Diajukan?</p>
                    <p class="text-white/60 text-xs mb-6">Pengajuan akan dikirim ke Admin untuk disetujui.</p>
                    <button type="submit"
                            class="w-full py-4 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all shadow-lg">
                        <span class="material-symbols-outlined text-sm align-middle mr-1">send</span>
                        KIRIM PENGAJUAN
                    </button>
                    <a href="{{ route('pengajuan_proyek.index') }}"
                       class="w-full mt-3 py-3.5 bg-white/10 text-white/60 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-white/20 transition-all text-center block">
                        BATAL
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function kebutuhanForm() {
    return {
        items: [{ prodi: '', jumlah: 1 }],
        tambah() {
            if (this.items.length < 3) {
                this.items.push({ prodi: '', jumlah: 1 });
            }
        },
        hapus(idx) {
            this.items.splice(idx, 1);
        }
    }
}
</script>
@endpush