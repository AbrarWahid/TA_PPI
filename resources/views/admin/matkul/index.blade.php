@extends('layouts.app')

@section('title', 'Master Data Mata Kuliah')

@section('content')
<div class="flex-1 flex flex-col">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Master Data <span class="text-[#2dce89]">Mata Kuliah</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                {{ $matkuls->count() ?? 0 }} Mata Kuliah Terdaftar
            </p>
        </div>
        <div class="flex items-center gap-3">
    <a href="{{ route('admin.matkul.batch.create') }}"
        class="flex items-center gap-2 px-6 py-3.5 rounded-2xl text-xs font-black hover:scale-105 transition-all"
        style="background:#7fffd4; color:#004d4d;">
        <span class="material-symbols-outlined text-base">upload_file</span> INPUT MASSAL
    </a>
    <button onclick="openModal()"
        class="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20">
        <span class="material-symbols-outlined text-base">add</span> TAMBAH MATKUL
    </button>
</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-[#004d4d] text-white p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
            <p class="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mata Kuliah</p>
            <p class="text-4xl font-black italic tracking-tighter">{{ $matkuls->count() ?? 0 }}</p>
        </div>
        <div class="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
            <p class="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Manajemen</p>
            <p class="text-xl font-black italic tracking-tighter text-[#004d4d]">Semester Berjalan</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">#</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Kode & Mata Kuliah</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Program Studi</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">SKS</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Semester</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($matkuls as $idx => $item)
                <tr class="hover:bg-teal-50/30 transition-colors group">
                    <td class="px-8 py-5 text-[10px] font-black text-gray-300">{{ $idx + 1 }}</td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white font-black text-xs shadow-md flex-shrink-0">
                                <span class="material-symbols-outlined text-xl">menu_book</span>
                            </div>
                            <div>
                                <span class="font-bold text-[#004d4d] block leading-none">{{ $item->nama_matkul }}</span>
                                <span class="text-[10px] text-gray-400 font-medium mt-1 block tracking-wider uppercase">{{ $item->kode_matkul }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-bold text-gray-500 italic">{{ labelProdi($item->program_studi) ?? '-' }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl text-[10px] font-black uppercase tracking-wider">
                            {{ $item->sks }} SKS
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-xs font-bold text-[#004d4d]">Sem {{ $item->semester }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center">
                            <form action="{{ route('admin.matkul.destroy', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus mata kuliah {{ $item->nama_matkul }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center opacity-40">
                        <span class="material-symbols-outlined text-5xl mb-2 text-gray-400 block">library_books</span>
                        <p class="font-black italic text-gray-500">Belum ada data mata kuliah.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="matkulModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg mx-4 border border-gray-100 overflow-hidden">
        <div class="bg-[#004d4d] p-6 text-center">
            <h2 class="text-xl font-black text-white italic uppercase tracking-tighter">
                Tambah <span class="text-[#7fffd4]">Mata Kuliah</span>
            </h2>
        </div>
        <form method="POST" action="{{ route('admin.matkul.store') }}" class="p-8">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kode Matkul</label>
                    <input type="text" name="kode_matkul" value="{{ old('kode_matkul') }}" required
                        placeholder="MK01"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('kode_matkul')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program Studi</label>
                    <select name="program_studi" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                        <option value="">Pilih Prodi</option>
                        <option value="informatika" ...>D4 Teknologi Rekayasa Informatika Industri</option>
                        <option value="otomasi" ...>D4 Teknologi Rekayasa Otomasi</option>
                        <option value="mekatronika" ...>D4 Teknologi Rekayasa Mekatronika</option>
                    </select>
                    @error('program_studi')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Matkul</label>
                <input type="text" name="nama_matkul" value="{{ old('nama_matkul') }}" required
                    placeholder="Pemrograman Web"
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                @error('nama_matkul')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">SKS</label>
                    <input type="number" name="sks" value="{{ old('sks') }}" required min="1" max="10"
                        placeholder="3"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('sks')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Semester</label>
                    <input type="number" name="semester" value="{{ old('semester') }}" required min="1" max="8"
                        placeholder="1"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]">
                    @error('semester')<span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal()"
                    class="flex-1 py-3.5 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg hover:scale-[1.02] transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('matkulModal');
    function openModal() { modal.classList.remove('hidden'); }
    function closeModal() { modal.classList.add('hidden'); }
    @if($errors->any()) openModal(); @endif
</script>
@endpush