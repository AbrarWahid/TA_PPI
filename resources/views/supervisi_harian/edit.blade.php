@extends('layouts.app')
@section('title', 'Edit Supervisi Harian')

@section('content')

<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('supervisi_harian.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition text-[#004d4d]">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Edit <span class="text-[#2dce89]">Supervisi Harian</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Perbarui catatan entri supervisi</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('supervisi_harian.update', $supervisiHarian) }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            {{-- ============================================================
                 MATA KULIAH & DOSEN — dropdown (menggantikan info proyek)
                 ============================================================ --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">
                    Mata Kuliah & Dosen *
                </label>

                @if(isset($supervisiMatkulList) && $supervisiMatkulList->isNotEmpty())
                <select name="supervisi_matkul_id" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    <option value="">— Pilih Mata Kuliah & Dosen —</option>
                    @foreach($supervisiMatkulList as $sm)
                    <option value="{{ $sm->id }}"
                        {{ (old('supervisi_matkul_id', $supervisiHarian->supervisi_matkul_id) == $sm->id) ? 'selected' : '' }}>
                        {{ $sm->mataKuliah->nama_matkul ?? '-' }}
                        —
                        {{ $sm->dosen->nama_dosen ?? ($sm->dosen->user->name ?? '-') }}
                        @if($sm->tahun_ajaran)
                        ({{ $sm->tahun_ajaran }}{{ $sm->semester ? ' / Sem. '.$sm->semester : '' }})
                        @endif
                    </option>
                    @endforeach
                </select>
                @else
                {{-- Fallback: belum di-assign admin --}}
                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-400 text-lg mt-0.5">warning</span>
                    <div>
                        <p class="text-sm font-bold text-red-600">Belum ada supervisi matkul</p>
                        <p class="text-xs text-red-400 mt-0.5">Hubungi admin untuk didaftarkan ke mata kuliah supervisi.</p>
                    </div>
                </div>
                {{-- Hidden input agar tetap bisa submit (pakai nilai lama) --}}
                <input type="hidden" name="supervisi_matkul_id" value="{{ $supervisiHarian->supervisi_matkul_id }}">
                @endif

                @error('supervisi_matkul_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info minggu (read-only) --}}
            <div class="px-4 py-3 bg-[#004d4d]/5 rounded-2xl flex items-center gap-3">
                <span class="material-symbols-outlined text-[#004d4d] text-lg">calendar_today</span>
                <div>
                    <p class="text-xs font-black italic uppercase text-[#004d4d]">Minggu ke-{{ $supervisiHarian->minggu_ke }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Minggu tidak dapat diubah di sini</p>
                </div>
            </div>

            {{-- Hari & Tanggal --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Hari *</label>
                    <select name="hari" required
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari)
                        <option value="{{ $hari }}"
                            {{ (old('hari', $supervisiHarian->hari) === $hari) ? 'selected' : '' }}>
                            {{ $hari }}
                        </option>
                        @endforeach
                    </select>
                    @error('hari')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Tanggal *</label>
                    <input type="date" name="tanggal" required
                           value="{{ old('tanggal', \Carbon\Carbon::parse($supervisiHarian->tanggal)->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Jam --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Jam Mulai *</label>
                    <input type="time" name="jam_mulai" required
                           value="{{ old('jam_mulai', \Carbon\Carbon::parse($supervisiHarian->jam_mulai)->format('H:i')) }}"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    @error('jam_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Jam Selesai *</label>
                    <input type="time" name="jam_selesai" required
                           value="{{ old('jam_selesai', \Carbon\Carbon::parse($supervisiHarian->jam_selesai)->format('H:i')) }}"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold transition">
                    @error('jam_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Aktivitas --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">
                    Aktivitas / Kegiatan *
                </label>
                <textarea name="aktivitas" rows="4" required
                          placeholder="Deskripsikan kegiatan yang dilakukan hari ini..."
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm transition resize-none">{{ old('aktivitas', $supervisiHarian->aktivitas) }}</textarea>
                @error('aktivitas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kendala --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">
                    Kendala
                    <span class="text-gray-400 font-normal normal-case italic">(opsional)</span>
                </label>
                <textarea name="kendala" rows="3"
                          placeholder="Tuliskan kendala yang dihadapi, jika ada..."
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm transition resize-none">{{ old('kendala', $supervisiHarian->kendala) }}</textarea>
                @error('kendala')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Dokumentasi --}}
            <div>
                <label class="block text-xs font-black italic uppercase text-[#004d4d] mb-2">Dokumentasi</label>

                @if($supervisiHarian->dokumentasi)
                <div class="mb-3 flex items-center gap-2 p-3 bg-blue-50 rounded-xl">
                    <span class="material-symbols-outlined text-blue-500 text-sm">attach_file</span>
                    <a href="{{ Storage::url($supervisiHarian->dokumentasi) }}" target="_blank"
                       class="text-xs text-blue-600 font-bold hover:underline flex-1">
                        File saat ini (klik untuk lihat)
                    </a>
                    <span class="text-xs text-gray-400">Upload baru untuk mengganti</span>
                </div>
                @endif

                <input type="file" name="dokumentasi" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-600
                              file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0
                              file:text-xs file:font-black file:bg-[#004d4d] file:text-white
                              hover:file:bg-[#003d3d] transition">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Biarkan kosong jika tidak ingin mengganti.</p>
                @error('dokumentasi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-3.5 bg-[#004d4d] text-white rounded-2xl font-black text-sm hover:bg-[#003d3d] transition-all shadow-lg">
                    Simpan Perubahan
                </button>
                <a href="{{ route('supervisi_harian.index') }}"
                   class="px-6 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection