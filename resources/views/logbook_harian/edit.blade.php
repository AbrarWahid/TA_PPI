@extends('layouts.app')
@section('title', 'Edit Logbook Harian')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('logbook_harian.show', $logbook_harian) }}"
           class="p-2.5 text-gray-400 hover:text-primary hover:bg-teal-50 rounded-xl transition-all border border-gray-100">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-on-surface">Edit Logbook Harian</h1>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui catatan aktivitas harian</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-sm p-8 space-y-6">

        @if($errors->any())
        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-sm text-red-600">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('logbook_harian.update', $logbook_harian) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Proyek (read-only) --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Proyek</label>
                <div class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-on-surface font-semibold">
                    {{ $logbook_harian->proyek->judul_proyek ?? '-' }}
                </div>
            </div>

            {{-- Minggu & Hari --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Minggu Ke <span class="text-red-400">*</span></label>
                    <select name="minggu_ke" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @for($i = 1; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('minggu_ke', $logbook_harian->minggu_ke) == $i ? 'selected' : '' }}>Minggu {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Hari <span class="text-red-400">*</span></label>
                    <select name="hari" required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari', $logbook_harian->hari) == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal <span class="text-red-400">*</span></label>
                <input type="date" name="tanggal"
                       value="{{ old('tanggal', $logbook_harian->tanggal) }}"
                       required
                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>

            {{-- Jam Masuk & Jam Keluar --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Masuk <span class="text-red-400">*</span></label>
                    <input type="time" name="jam_masuk"
                           value="{{ old('jam_masuk', $logbook_harian->jam_masuk ? \Carbon\Carbon::parse($logbook_harian->jam_masuk)->format('H:i') : '') }}"
                           required
                           class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Keluar <span class="text-red-400">*</span></label>
                    <input type="time" name="jam_keluar"
                           value="{{ old('jam_keluar', $logbook_harian->jam_keluar ? \Carbon\Carbon::parse($logbook_harian->jam_keluar)->format('H:i') : '') }}"
                           required
                           class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
            </div>

            {{-- Aktivitas --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Aktivitas Harian <span class="text-red-400">*</span></label>
                <textarea name="aktivitas" rows="4" required
                          class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ old('aktivitas', $logbook_harian->aktivitas) }}</textarea>
            </div>

            {{-- Kendala --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                    Kendala <span class="text-slate-300 font-normal normal-case">(opsional)</span>
                </label>
                <textarea name="kendala" rows="3"
                          placeholder="Tuliskan kendala atau hambatan yang dihadapi (jika ada)..."
                          class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ old('kendala', $logbook_harian->kendala) }}</textarea>
            </div>

            {{-- Dokumentasi --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                    Dokumentasi <span class="text-slate-300 font-normal normal-case">(opsional · jpg, png, pdf · maks 2MB)</span>
                </label>
                @if($logbook_harian->dokumentasi)
                <div class="mb-2 text-xs text-teal-600 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">attach_file</span>
                    File saat ini: <a href="{{ Storage::url($logbook_harian->dokumentasi) }}" target="_blank" class="underline font-semibold">Lihat Dokumen</a>
                </div>
                @endif
                <input type="file" name="dokumentasi" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-primary hover:file:bg-teal-100">
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('logbook_harian.show', $logbook_harian) }}"
                   class="flex-1 py-3 text-center text-sm font-semibold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-br from-primary to-emerald-800 rounded-xl hover:opacity-90 transition-all shadow-sm">
                    Perbarui Logbook
                </button>
            </div>
        </form>
    </div>
</div>
@endsection