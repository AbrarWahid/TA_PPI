@extends('layouts.app')

@section('title', 'Import Massal Mata Kuliah')

@section('content')
<div class="flex-1 flex flex-col">

    {{-- Header --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                Import Massal <span class="text-[#2dce89]">Mata Kuliah</span>
            </h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                Upload file Excel/CSV untuk menambahkan banyak mata kuliah sekaligus
            </p>
        </div>
        <a href="{{ route('admin.matkul.index') }}"
            class="flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black hover:bg-gray-200 transition-all">
            <span class="material-symbols-outlined text-base">arrow_back</span> KEMBALI
        </a>
    </div>

    {{-- Flash Warnings --}}
    @if(session('warnings') && count(session('warnings')) > 0)
    <div class="mb-6 bg-amber-50 border-2 border-amber-200 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-amber-500">warning</span>
            <p class="text-xs font-black text-amber-700 uppercase tracking-widest">
                {{ count(session('warnings')) }} Baris Dilewati
            </p>
        </div>
        <ul class="space-y-1">
            @foreach(session('warnings') as $w)
            <li class="text-xs text-amber-700 font-medium flex items-start gap-2">
                <span class="mt-0.5 text-amber-400">•</span> {{ $w }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Upload --}}
        <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
            <h2 class="text-sm font-black text-[#004d4d] uppercase tracking-widest mb-6 italic">
                Upload File
            </h2>

            <form action="{{ route('admin.matkul.batch.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Drag & Drop Zone --}}
                <div id="dropZone"
                    class="border-2 border-dashed border-[#7fffd4] rounded-2xl p-12 text-center cursor-pointer hover:bg-teal-50/30 transition-all mb-6"
                    onclick="document.getElementById('fileInput').click()"
                    ondragover="event.preventDefault(); this.classList.add('bg-teal-50')"
                    ondragleave="this.classList.remove('bg-teal-50')"
                    ondrop="handleDrop(event)">
                    <span class="material-symbols-outlined text-5xl text-[#7fffd4] mb-3 block">upload_file</span>
                    <p class="text-sm font-black text-[#004d4d] italic mb-1">Drag & drop file di sini</p>
                    <p class="text-xs text-gray-400 font-medium">atau klik untuk memilih file</p>
                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-3">
                        .xlsx / .xls / .csv — Maks. 5MB
                    </p>
                </div>

                <input type="file" id="fileInput" name="file"
                    accept=".xlsx,.xls,.csv" class="hidden"
                    onchange="showFileName(this)">

                {{-- Nama File Terpilih --}}
                <div id="fileNameBox" class="hidden mb-6 flex items-center gap-3 bg-teal-50 border border-[#7fffd4] rounded-2xl px-5 py-4">
                    <span class="material-symbols-outlined text-[#2dce89]">description</span>
                    <span id="fileNameText" class="text-sm font-bold text-[#004d4d]"></span>
                </div>

                @error('file')
                <p class="text-red-500 text-xs font-bold mb-4">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="w-full py-4 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg hover:scale-[1.01] transition-all">
                    <span class="material-symbols-outlined text-base align-middle mr-1">cloud_upload</span>
                    PROSES IMPORT
                </button>
            </form>
        </div>

        {{-- Panel Kanan --}}
        <div class="flex flex-col gap-4">

            {{-- Download Template --}}
            <div class="bg-[#004d4d] rounded-[2rem] p-6 text-white">
                <span class="material-symbols-outlined text-[#7fffd4] text-3xl mb-3 block">table_view</span>
                <h3 class="font-black italic uppercase tracking-tight text-sm mb-1">Template CSV</h3>
                <p class="text-[10px] text-teal-300 font-medium mb-4 leading-relaxed">
                    Download template lalu isi sesuai format yang ditentukan.
                </p>
                <a href="{{ route('admin.matkul.template') }}"
                    class="flex items-center justify-center gap-2 w-full py-3 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all">
                    <span class="material-symbols-outlined text-sm">download</span> DOWNLOAD
                </a>
            </div>

            {{-- Panduan Format --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="font-black italic uppercase tracking-widest text-[10px] text-gray-400 mb-4">
                    Format Kolom
                </h3>
                <div class="space-y-3">
                    @foreach([
                        ['kode_matkul',   'Kode unik matkul',              true],
                        ['nama_matkul',   'Nama lengkap mata kuliah',      true],
                        ['program_studi', 'mekatronika / otomasi / informatika', true],
                        ['sks',           'Angka 1–10',                    true],
                        ['semester',      'Angka 1–8',                     true],
                    ] as [$col, $desc, $req])
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-lg text-[10px] font-black font-mono whitespace-nowrap">
                            {{ $col }}
                        </span>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500">{{ $desc }}</p>
                            @if($req)
                            <span class="text-[9px] text-red-400 font-black uppercase">Wajib</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameText').textContent = input.files[0].name;
            document.getElementById('fileNameBox').classList.remove('hidden');
        }
    }
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('dropZone').classList.remove('bg-teal-50');
        const files = e.dataTransfer.files;
        if (files.length) {
            const input = document.getElementById('fileInput');
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            showFileName(input);
        }
    }
</script>
@endpush