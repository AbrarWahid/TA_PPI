@extends('layouts.app')

@section('title', 'Edit Periode PBL')

@section('content')
<div class="max-w-4xl mx-auto flex-1 flex flex-col">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.periode_pbl.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-[#004d4d] transition-all shadow-sm">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">Edit <span class="text-[#2dce89]">Periode</span></h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">{{ $periode_pbl->nama_periode }}</p>
        </div>
    </div>

    {{-- Flash error (misal bobot tidak 100%) --}}
    @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Warning jika sedang edit periode aktif --}}
    @if($periode_pbl->is_aktif)
        <div class="mb-6 px-5 py-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-700 text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-base">warning</span>
            Anda sedang mengedit <strong>Periode Aktif</strong>. Perubahan bobot nilai akan langsung berpengaruh pada kalkulasi penilaian.
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <form method="POST" action="{{ route('admin.periode_pbl.update', $periode_pbl->id) }}" class="p-8">
            @csrf @method('PUT')

            {{-- Mekanisme proyek: Direct (hidden) --}}
            <input type="hidden" name="mekanisme_proyek" value="direct">

            {{-- Baris 1: Nama Periode & Tahun Ajaran --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Periode</label>
                    <input type="text" name="nama_periode"
                        value="{{ old('nama_periode', $periode_pbl->nama_periode) }}" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('nama_periode')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tahun Ajaran</label>
                    <select name="tahun_ajaran" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta }}" {{ old('tahun_ajaran', $periode_pbl->tahun_ajaran) == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Baris 2: Semester & Koordinator PBL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-b border-gray-100 pb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Semester</label>
                    <select name="semester" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                        <option value="ganjil" {{ old('semester', $periode_pbl->semester) == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap"  {{ old('semester', $periode_pbl->semester) == 'genap'  ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Koordinator PBL</label>
                    <select name="koordinator_id" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                        @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('koordinator_id', $periode_pbl->koordinator_id) == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                        @endforeach
                    </select>
                    @error('koordinator_id')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Baris 3: Frekuensi Logbook (hanya Harian & Mingguan) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Frekuensi Logbook</label>
                    <select name="frekuensi_logbook" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                        <option value="mingguan" {{ old('frekuensi_logbook', $periode_pbl->frekuensi_logbook) == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="harian"   {{ old('frekuensi_logbook', $periode_pbl->frekuensi_logbook) == 'harian'   ? 'selected' : '' }}>Harian</option>
                    </select>
                </div>
                <div></div>
            </div>

            {{-- Baris 4: Tanggal Mulai & Selesai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tgl Mulai Periode</label>
                    <input type="date" name="tanggal_mulai"
                        value="{{ old('tanggal_mulai', $periode_pbl->tanggal_mulai?->format('Y-m-d')) }}" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('tanggal_mulai')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tgl Selesai Periode</label>
                    <input type="date" name="tanggal_selesai"
                        value="{{ old('tanggal_selesai', $periode_pbl->tanggal_selesai?->format('Y-m-d')) }}" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('tanggal_selesai')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Baris 5: Bobot Nilai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-b border-gray-100 pb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        Bobot Manager (%) <span class="text-gray-300 normal-case font-medium">— Nilai dari Manager Proyek</span>
                    </label>
                    <input type="number" name="bobot_manager" id="bobot_manager"
                        value="{{ old('bobot_manager', $periode_pbl->bobot_manager) }}"
                        min="1" max="99" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                        oninput="syncBobot(this.value)">
                    @error('bobot_manager')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        Bobot Dosen (%) <span class="text-gray-300 normal-case font-medium">— Nilai dari Dosen Pengampu</span>
                    </label>
                    <input type="number" name="bobot_dosen" id="bobot_dosen"
                        value="{{ old('bobot_dosen', $periode_pbl->bobot_dosen) }}"
                        min="1" max="99" required
                        readonly
                        class="w-full px-5 py-3.5 bg-gray-100 border border-gray-100 rounded-2xl outline-none text-sm font-bold text-[#004d4d] cursor-not-allowed">
                    <p class="text-[10px] text-gray-400 mt-1">Otomatis dihitung: 100% − Bobot Manager</p>
                    @error('bobot_dosen')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Checkbox Aktif --}}
            <div class="mb-8 flex items-center gap-3 bg-teal-50/50 p-4 rounded-2xl border border-teal-100">
                <input type="checkbox" name="is_aktif" value="1" id="is_aktif"
                    {{ old('is_aktif', $periode_pbl->is_aktif) ? 'checked' : '' }}
                    class="w-5 h-5 text-[#004d4d] bg-white border-gray-300 rounded focus:ring-[#7fffd4] cursor-pointer">
                <label for="is_aktif" class="text-sm font-bold text-[#004d4d] cursor-pointer">
                    Jadikan Sebagai Periode Aktif Saat Ini
                </label>
            </div>

            <button type="submit" class="w-full py-4 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg hover:scale-[1.01] transition-all">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
function syncBobot(managerVal) {
    const manager = parseInt(managerVal) || 0;
    const dosen   = 100 - manager;
    document.getElementById('bobot_dosen').value = dosen >= 0 ? dosen : '';
}
</script>
@endsection