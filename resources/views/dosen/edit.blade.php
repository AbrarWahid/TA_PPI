@extends('layouts.app')
@section('title', 'Edit Dosen')
@section('content')

<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('dosen.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-on-surface">Edit Dosen</h1>
            <p class="text-sm text-on-surface-variant mt-0.5">Perbarui data dosen</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-6">

        @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('dosen.update', $dosen->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">NIDN</label>
                <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface-container-low text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama_dosen" value="{{ old('nama_dosen', $dosen->nama_dosen) }}"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface-container-low text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" value="{{ $dosen->user->email ?? '-' }}"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 bg-surface-container text-sm text-on-surface-variant cursor-not-allowed"
                    disabled>
                <p class="text-xs text-on-surface-variant/60 mt-1">Email tidak dapat diubah</p>
            </div>

            {{-- Akses Role --}}
            @php $currentAkses = old('akses_role', $dosen->user->akses_role ?? 'dosen_pengampu'); @endphp
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-3">Akses Role</label>
                <div class="grid grid-cols-1 gap-3">

                    <label class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all
                        {{ $currentAkses === 'dosen_pengampu' ? 'border-[#2dce89] bg-teal-50/50' : 'border-gray-100 hover:border-[#2dce89]' }}">
                        <input type="radio" name="akses_role" value="dosen_pengampu" class="accent-[#004d4d]"
                            {{ $currentAkses === 'dosen_pengampu' ? 'checked' : '' }}>
                        <div class="w-10 h-10 rounded-xl bg-[#2dce89] flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#004d4d] text-xl">school</span>
                        </div>
                        <div>
                            <p class="font-black text-[#004d4d] text-sm uppercase">Dosen Pengampu</p>
                            <p class="text-[10px] text-gray-400">Verifikasi laporan & beri nilai (45%)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all
                        {{ $currentAkses === 'manager_proyek' ? 'border-[#2dce89] bg-teal-50/50' : 'border-gray-100 hover:border-[#2dce89]' }}">
                        <input type="radio" name="akses_role" value="manager_proyek" class="accent-[#004d4d]"
                            {{ $currentAkses === 'manager_proyek' ? 'checked' : '' }}>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#004d4d;">
                            <span class="material-symbols-outlined text-[#7fffd4] text-xl">rocket_launch</span>
                        </div>
                        <div>
                            <p class="font-black text-[#004d4d] text-sm uppercase">Manager Proyek</p>
                            <p class="text-[10px] text-gray-400">Kelola & ajukan proyek PBL (55%)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all
                        {{ $currentAkses === 'keduanya' ? 'border-[#2dce89] bg-teal-50/50' : 'border-gray-100 hover:border-[#2dce89]' }}">
                        <input type="radio" name="akses_role" value="keduanya" class="accent-[#004d4d]"
                            {{ $currentAkses === 'keduanya' ? 'checked' : '' }}>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-[#004d4d] to-[#2dce89]">
                            <span class="material-symbols-outlined text-white text-xl">swap_horiz</span>
                        </div>
                        <div>
                            <p class="font-black text-[#004d4d] text-sm uppercase">Keduanya</p>
                            <p class="text-[10px] text-gray-400">Dosen bisa switch role saat login</p>
                        </div>
                    </label>

                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary text-sm font-medium hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-base">check</span>
                    Update
                </button>
                <a href="{{ route('dosen.index') }}"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border border-outline-variant text-on-surface-variant hover:bg-surface-container transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection