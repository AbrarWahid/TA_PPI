@extends('layouts.app')

@section('title', 'Import Mahasiswa')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
            Import <span class="text-[#2dce89]">Mahasiswa</span>
        </h1>
        <p class="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
            Upload file Excel atau CSV sekaligus
        </p>
    </div>
    <a href="{{ route('mahasiswa.index') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
</div>

@if($errors->any())
<div class="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-xs font-bold">
    <p class="font-black mb-1 flex items-center gap-2">
        <span class="material-symbols-outlined text-base">error</span> Ada kesalahan:
    </p>
    <ul class="list-disc list-inside space-y-1 mt-1">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-3 gap-6">

    {{-- UPLOAD CARD --}}
    <div class="col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
        <p class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6">Upload File</p>

        <form method="POST" action="{{ route('mahasiswa.batch.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf

            {{-- Drop zone --}}
            <div id="dropZone"
                 class="relative border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center cursor-pointer hover:border-[#2dce89] hover:bg-teal-50/30 transition-all"
                 onclick="document.getElementById('fileInput').click()">
                <span class="material-symbols-outlined text-5xl text-gray-300 block mb-3">upload_file</span>
                <p class="text-sm font-black text-gray-400">Klik atau drag & drop file di sini</p>
                <p class="text-[10px] font-bold text-gray-300 mt-1 uppercase tracking-widest">
                    Format: .xlsx, .xls, .csv — Maks. 2MB
                </p>
                <input type="file" id="fileInput" name="file"
                       accept=".xlsx,.xls,.csv" class="hidden"
                       onchange="previewFile(this)">
            </div>

            {{-- Preview nama file --}}
            <div id="filePreview" class="hidden mt-4 px-5 py-3 rounded-xl bg-teal-50 border border-[#2dce89]/30 flex items-center gap-3">
                <span class="material-symbols-outlined text-[#004d4d] text-base">description</span>
                <p class="text-xs font-black text-[#004d4d] flex-1" id="fileName">—</p>
                <button type="button" onclick="clearFile()"
                    class="text-gray-300 hover:text-red-400 transition-colors">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>

            <button type="submit"
                class="mt-6 w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl text-sm font-black text-[#004d4d] shadow-lg hover:scale-[1.02] transition-all"
                style="background:#7fffd4;">
                <span class="material-symbols-outlined text-base">cloud_upload</span>
                Import Sekarang
            </button>
        </form>
    </div>

    {{-- PANDUAN CARD --}}
    <div class="flex flex-col gap-5">

        {{-- Download template --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">Template File</p>
            <p class="text-[11px] text-gray-400 font-medium mb-4 leading-relaxed">
                Download template, isi data, lalu upload kembali.
            </p>
            <a href="{{ route('mahasiswa.template') }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border-2 border-dashed border-[#2dce89] text-[#004d4d] text-xs font-black hover:bg-teal-50 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>
                Download Template
            </a>
        </div>

        {{-- Ketentuan --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <p class="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">Ketentuan Kolom</p>
            <div class="space-y-3">
                @foreach([
                    ['nim',   'NIM mahasiswa (unik)', 'tag'],
                    ['nama',  'Nama lengkap',         'person'],
                    ['prodi', 'mekatronika / otomasi / informatika', 'school'],
                ] as [$col, $desc, $icon])
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#2dce89] text-sm mt-0.5">{{ $icon }}</span>
                    <div>
                        <p class="text-[10px] font-black text-[#004d4d] font-mono uppercase">{{ $col }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1">
                    <p class="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[10px]">info</span>
                        Password otomatis = NIM
                    </p>
                    <p class="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[10px]">info</span>
                        Email otomatis = NIM@pbl.com
                    </p>
                    <p class="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[10px]">info</span>
                        NIM duplikat otomatis dilewati
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function previewFile(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileName').textContent = input.files[0].name;
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('dropZone').classList.add('border-[#2dce89]', 'bg-teal-50/30');
    }
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('border-[#2dce89]', 'bg-teal-50/30');
}

// Drag & drop
const zone = document.getElementById('dropZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-[#2dce89]','bg-teal-50/30'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-[#2dce89]','bg-teal-50/30'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('fileInput').files = e.dataTransfer.files;
        previewFile(document.getElementById('fileInput'));
    }
});
</script>
@endpush