@extends('layouts.app')
@section('title', 'Tambah Supervisi')
@section('content')

<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.supervisi.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-[#004d4d] mb-6 transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>

    <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase mb-1">
        Tambah <span class="text-[#2dce89]">Supervisi</span>
    </h1>
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-8">
        Assign Mahasiswa ke Mata Kuliah & Dosen Pengampu
    </p>

    @if($errors->any())
    <div class="bg-red-50 border border-red-100 rounded-2xl px-5 py-4 mb-6">
        <ul class="text-xs text-red-600 font-bold space-y-1">
            @foreach($errors->all() as $e)
            <li>• {{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!$periode)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined text-yellow-500">warning</span>
        <p class="text-xs font-bold text-yellow-700">Belum ada Periode PBL yang aktif. Tahun ajaran dan semester tidak dapat diisi otomatis.</p>
    </div>
    @endif

    {{-- Data matkul -> prodi untuk JS --}}
    <script>
        const matkulProdiMap = @json($matkulList->mapWithKeys(fn($mk) => [$mk->id => $mk->program_studi]));
    </script>

    <form action="{{ route('admin.supervisi.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-7 space-y-6">

            {{-- Mata Kuliah --}}
            <div>
                <label class="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="matkulSelect" required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    @foreach($matkulList as $mk)
                    <option value="{{ $mk->id }}"
                            data-prodi="{{ $mk->program_studi }}"
                            {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->nama_matkul }} ({{ $mk->kode_matkul }}) — {{ labelProdi($mk->program_studi) }}
                    </option>
                    @endforeach
                </select>
                <p id="matkulProdiInfo" class="text-[10px] text-teal-600 font-bold mt-1.5 hidden">
                    <span class="material-symbols-outlined text-xs align-middle">info</span>
                    Menampilkan mahasiswa prodi: <span id="matkulProdiLabel"></span>
                </p>
            </div>

            {{-- Dosen Pengampu --}}
            <div>
                <label class="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">Dosen Pengampu</label>
                <select name="dosen_id" required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white">
                    <option value="">-- Pilih Dosen --</option>
                    @foreach($dosenList as $d)
                    <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->user->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun Ajaran & Semester — dari Periode PBL aktif --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran"
            value="{{ old('tahun_ajaran', $periode?->tahun_ajaran ?? '') }}"
            required placeholder="2026/2027"
            {{ $periode ? 'readonly' : '' }}
            class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 {{ $periode ? 'bg-gray-50 cursor-not-allowed' : '' }}">
        @if($periode)
        <p class="text-[10px] text-gray-400 font-medium mt-1">Dari Periode PBL aktif</p>
        @endif
    </div>
    <div>
        <label class="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">Semester</label>
        <input type="text"
            value="{{ $periode ? ucfirst($periode->semester) : '' }}"
            readonly
            class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold bg-gray-50 cursor-not-allowed">
        <input type="hidden" name="semester" value="{{ $periode?->semester ?? '' }}">
        @if($periode)
        <p class="text-[10px] text-gray-400 font-medium mt-1">Dari Periode PBL aktif</p>
        @endif
    </div>
</div>

            {{-- semester hidden field jika disabled (agar ikut POST) --}}
            @if($periode)
            <input type="hidden" name="semester" value="{{ $periode->semester }}">
            @endif

            {{-- Multi-select Mahasiswa --}}
            <div>
                <label class="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-1">Mahasiswa</label>
                <p class="text-[10px] text-gray-400 font-medium mb-3">Pilih satu atau lebih mahasiswa yang akan di-assign.</p>

                {{-- Filter Prodi --}}
                <div class="flex flex-wrap gap-2 mb-4" id="prodiFilterBar">
                    <button type="button" data-prodi="all"
                        class="prodi-filter px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-[#004d4d] text-[#7fffd4] transition-all">
                        Semua
                    </button>
                    @foreach($prodiList as $prodi)
                    <button type="button" data-prodi="{{ $prodi }}"
                        class="prodi-filter px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-500 hover:bg-teal-50 hover:text-teal-700 transition-all">
                        {{ $prodi }}
                    </button>
                    @endforeach
                </div>

                {{-- Select All --}}
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                    <input type="checkbox" id="selectAll"
                        class="w-4 h-4 rounded accent-teal-600 cursor-pointer">
                    <label for="selectAll" class="text-xs font-black text-gray-500 cursor-pointer uppercase tracking-widest">
                        Pilih Semua
                    </label>
                </div>

                {{-- Checklist --}}
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1" id="mahasiswaList">
                    @foreach($mahasiswaList as $m)
                    <label data-prodi="{{ $m->user->prodi }}"
                        class="mahasiswa-item flex items-center gap-3 p-3 rounded-2xl border border-gray-100 hover:border-teal-300 hover:bg-teal-50/30 cursor-pointer transition-all group">
                        <input type="checkbox" name="mahasiswa_ids[]" value="{{ $m->id }}"
                            class="mahasiswa-check w-4 h-4 rounded accent-teal-600"
                            {{ is_array(old('mahasiswa_ids')) && in_array($m->id, old('mahasiswa_ids')) ? 'checked' : '' }}>
                        <div class="w-8 h-8 rounded-xl bg-[#004d4d] text-[#7fffd4] flex items-center justify-center font-black text-[10px] flex-shrink-0">
                            {{ strtoupper(substr($m->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-[#004d4d] group-hover:text-teal-700">{{ $m->nama }}</p>
                            <p class="text-[10px] text-gray-400">{{ $m->nim }} &nbsp;·&nbsp; {{ $m->user->prodi }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('mahasiswa_ids')
                <p class="text-xs text-red-500 font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.supervisi.index') }}"
               class="px-6 py-3 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all">
                Batal
            </a>
            <button type="submit"
                class="px-8 py-3 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg shadow-teal-900/20">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
const filterBtns = document.querySelectorAll('.prodi-filter');
const items      = document.querySelectorAll('.mahasiswa-item');
const selectAll  = document.getElementById('selectAll');

// ── Filter prodi mahasiswa ──────────────────────────────
function applyProdiFilter(prodi) {
    filterBtns.forEach(b => {
        const active = b.dataset.prodi === prodi;
        b.classList.toggle('bg-[#004d4d]', active);
        b.classList.toggle('text-[#7fffd4]', active);
        b.classList.toggle('bg-gray-100', !active);
        b.classList.toggle('text-gray-500', !active);
    });

    items.forEach(item => {
        const show = prodi === 'all' || item.dataset.prodi === prodi;
        item.style.display = show ? '' : 'none';
        if (!show) item.querySelector('input[type=checkbox]').checked = false;
    });
    updateSelectAll();
}

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => applyProdiFilter(btn.dataset.prodi));
});

// ── Auto-filter mahasiswa saat pilih matkul ─────────────
document.getElementById('matkulSelect').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const prodi = selectedOption.dataset.prodi ?? 'all';

    if (prodi && prodi !== '') {
        applyProdiFilter(prodi);

        // Tampilkan info prodi
        document.getElementById('matkulProdiInfo').classList.remove('hidden');
        document.getElementById('matkulProdiLabel').textContent = prodi;
    } else {
        applyProdiFilter('all');
        document.getElementById('matkulProdiInfo').classList.add('hidden');
    }
});

// ── Select All ──────────────────────────────────────────
selectAll.addEventListener('change', () => {
    items.forEach(item => {
        if (item.style.display !== 'none') {
            item.querySelector('input[type=checkbox]').checked = selectAll.checked;
        }
    });
});

document.querySelectorAll('.mahasiswa-check').forEach(c => {
    c.addEventListener('change', updateSelectAll);
});

function updateSelectAll() {
    const visible  = [...items].filter(i => i.style.display !== 'none');
    const allChked = visible.every(i => i.querySelector('input[type=checkbox]').checked);
    selectAll.checked = allChked && visible.length > 0;
}

// ── Init: jika ada old value matkul, trigger filter ─────
const oldMatkul = document.getElementById('matkulSelect').value;
if (oldMatkul) {
    document.getElementById('matkulSelect').dispatchEvent(new Event('change'));
}
</script>

@endsection