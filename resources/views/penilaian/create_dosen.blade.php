@extends('layouts.app')
@section('title', 'Input Penilaian Dosen')
@section('content')
@php
$semuaAspek = [
    'lit_informasi' => ['label'=>'Literasi Informasi','kategori'=>'Literacy Skills','bobot'=>5,'opsi'=>[10=>'Menggunakan informasi tanpa menggunakan etika yang benar.',28=>'Tidak mengerti dengan apa yang dicari.',46=>'Menggunakan informasi dengan menggunakan etika yang benar.',64=>'Tidak terlalu mengerti dengan apa yang dicari.',82=>'Menggunakan informasi dengan etika yang benar secara konsisten.',100=>'Mengerti dengan apa yang dicari secara mendalam.']],
    'lit_media' => ['label'=>'Literasi Media','kategori'=>'Literacy Skills','bobot'=>5,'opsi'=>[10=>'Tidak tepat dalam menggunakan sumber.',28=>'Tidak mempertimbangkan kualitas informasi.',46=>'Melakukan identifikasi sebagian sumber dengan tepat walaupun masih ada yang belum tepat.',64=>'Memahami bahwa kualitas informasi perlu dipertimbangkan walaupun belum menyeluruh.',82=>'Melakukan identifikasi sumber dengan tepat dan sesuai.',100=>'Menilai kualitas informasi secara menyeluruh dengan mempertimbangkan keakuratan dan kredibilitas.']],
    'lit_teknologi' => ['label'=>'Literasi Teknologi','kategori'=>'Literacy Skills','bobot'=>5,'opsi'=>[10=>'Tidak mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.',55=>'Berusaha menggunakan dan memahami teknologi meskipun masih ada kendala.',100=>'Mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.']],
    'pr_konten' => ['label'=>'Konten Presentasi','kategori'=>'Presentasi','bobot'=>3,'opsi'=>[10=>'Informasi penting tidak disampaikan; penyampaian tidak rinci sehingga audiens bingung.',55=>'Informasi penting disampaikan secara lengkap dan berupaya menjelaskan rinci walaupun masih ada pertanyaan.',100=>'Menyajikan informasi dengan lengkap dan jelas. Penyampaian rinci sehingga audiens mengerti.']],
    'pr_visual' => ['label'=>'Tampilan Visual Presentasi','kategori'=>'Presentasi','bobot'=>3,'opsi'=>[10=>'Tampilannya penuh dengan teks, tidak ada gambar atau grafik.',28=>'Judul tidak sesuai dengan apa yang ditampilkan.',46=>'Tampilan diselingi dengan beberapa gambar/grafik/tabel.',64=>'Terdapat beberapa judul yang tidak sesuai.',82=>'Dalam tampilan gambar/tabel/grafik dan teks ditampilkan seimbang sehingga audiens tertarik.',100=>'Judul sesuai dengan yang ditampilkan.']],
    'pr_kosakata' => ['label'=>'Pemilihan Kosakata','kategori'=>'Presentasi','bobot'=>3,'opsi'=>[10=>'Sering menggunakan kata berulang-ulang.',28=>'Menggunakan kata yang tidak formal dalam penyampaian.',46=>'Sedikit sekali menggunakan kata berulang.',64=>'Sebagian dari penyampaian menggunakan kata-kata yang tidak formal.',82=>'Lancar menyampaikan presentasi, tidak gugup, tidak menggunakan kata berulang.',100=>'Dalam penyampaian menggunakan kata-kata formal dan mudah dipahami.']],
    'pr_tanya_jawab' => ['label'=>'Tanya Jawab','kategori'=>'Presentasi','bobot'=>3,'opsi'=>[10=>'Tidak bisa menjawab satupun pertanyaan dari audiens.',55=>'Mampu menjawab pertanyaan audiens walaupun tidak semuanya dan masih ada kesalahan.',100=>'Mampu menjawab semua pertanyaan audiens dengan jelas.']],
    'pr_mata_gerak' => ['label'=>'Mata & Gerak Tubuh','kategori'=>'Presentasi','bobot'=>3,'opsi'=>[10=>'Tidak melihat audiens.',18=>'Hanya membaca slide, tidak ada pengembangan.',26=>'Tidak ada gerakan tubuh (monoton).',35=>'Gelisah, tidak tenang.',43=>'Sesekali melihat kepada audiens.',51=>'Mencoba mengembangkan isi dari beberapa slide.',59=>'Menggunakan gerakan tubuh tetapi tidak natural.',67=>'Tidak gelisah dan cukup tenang.',75=>'Menjaga kontak mata dengan audiens.',84=>'Tidak terpaku pada teks di slide dan mengembangkan isi slide presentasi.',92=>'Menggunakan gerakan tubuh yang tidak dibuat-buat.',100=>'Tenang dan percaya diri.']],
    'la_penulisan' => ['label'=>'Penulisan Laporan','kategori'=>'Laporan Akhir','bobot'=>5,'opsi'=>[10=>'Banyak ditemukan kesalahan dalam pengetikan.',20=>'Banyak kalimat yang sulit dipahami.',30=>'Dokumen tidak selesai.',40=>'Penomoran untuk tabel, gambar dan grafik tidak sesuai.',50=>'Tidak ditemukan kesalahan pengetikan.',60=>'Kalimat-kalimat mudah dipahami.',70=>'Sebagian masih ditemukan kesalahan dalam penomoran tabel, grafik, dan gambar.',80=>'Tidak ditemukan kesalahan pengetikan (konsisten).',90=>'Kalimat-kalimat mudah dipahami dan runtut.',100=>'Penomoran tabel, grafik dan gambar sudah sesuai.']],
    'la_pilihan_kata' => ['label'=>'Pilihan Kata','kategori'=>'Laporan Akhir','bobot'=>5,'opsi'=>[10=>'50% dari penulisan laporan menggunakan kata-kata yang tidak formal.',28=>'Banyak ditemukan penulisan kata dalam bentuk singkatan.',46=>'20% dari penulisan laporan menggunakan kata-kata yang tidak formal.',64=>'Tidak ditemukan penulisan kata dalam bentuk singkatan.',82=>'Penulisan laporan semuanya menggunakan kata-kata formal.',100=>'Tidak ditemukan penulisan kata-kata dalam bentuk singkatan.']],
    'la_konten' => ['label'=>'Konten Laporan Akhir','kategori'=>'Laporan Akhir','bobot'=>5,'opsi'=>[10=>'Informasi yang disampaikan tidak jelas, tidak akurat, tidak relevan.',21=>'Berdasarkan hasil investigasi banyak ditemukan hasil copy paste tanpa elaborasi.',32=>'Isi dari laporan tidak sesuai dengan proyek yang dibuat.',44=>'Informasi yang disampaikan akurat, jelas dan relevan.',55=>'Dari hasil pencarian masih ada ditemukan hasil copy paste tanpa elaborasi.',66=>'30% dari isi laporan tidak sesuai dengan proyek yang dibuat.',78=>'Informasi yang disampaikan akurat, jelas dan relevan (konsisten).',89=>'Dari hasil pencarian sedikit ditemukan copy paste.',100=>'Isi laporan semuanya sesuai dengan proyek yang dibuat.']],
];

$ikonMap = ['Literacy Skills'=>'menu_book','Presentasi'=>'co_present','Laporan Akhir'=>'description'];
$perKategori = [];
foreach ($semuaAspek as $fieldName => $aspek) {
    $perKategori[$aspek['kategori']][$fieldName] = $aspek;
}
@endphp

<style>
/* ── Level Columns ── */
.level-col {
    flex: 1; border-radius: 14px; border: 2px solid transparent;
    cursor: pointer; transition: all 0.2s ease;
    position: relative; overflow: hidden; min-width: 0;
}
.level-col:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.10); }

/* Kurang */
.level-col.kurang { background:#fff5f5; border-color:#fecaca; }
.level-col.kurang:hover { border-color:#f87171; }
.level-col.kurang .lc-header { background:#fee2e2; }
.level-col.kurang .lc-title { color:#dc2626; }
.level-col.kurang .lc-range { color:#ef4444; }
.level-col.kurang.active { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.18); background:#fff0f0; }
.level-col.kurang.active .lc-header { background:#ef4444; }
.level-col.kurang.active .lc-title { color:#fff; }
.level-col.kurang.active .lc-range { color:#fecaca; }
.level-col.kurang .subpoin-btn .sp-poin { background:#fee2e2; color:#dc2626; }
.level-col.kurang .subpoin-btn.selected { border-color:#ef4444; color:#dc2626; background:#fff; }
.level-col.kurang .subpoin-btn.selected .sp-poin { background:#ef4444; color:#fff; }

/* Cukup */
.level-col.cukup { background:#fffbeb; border-color:#fde68a; }
.level-col.cukup:hover { border-color:#f59e0b; }
.level-col.cukup .lc-header { background:#fef3c7; }
.level-col.cukup .lc-title { color:#d97706; }
.level-col.cukup .lc-range { color:#f59e0b; }
.level-col.cukup.active { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.18); background:#fffbeb; }
.level-col.cukup.active .lc-header { background:#f59e0b; }
.level-col.cukup.active .lc-title { color:#fff; }
.level-col.cukup.active .lc-range { color:#fef3c7; }
.level-col.cukup .subpoin-btn .sp-poin { background:#fef3c7; color:#d97706; }
.level-col.cukup .subpoin-btn.selected { border-color:#f59e0b; color:#d97706; background:#fff; }
.level-col.cukup .subpoin-btn.selected .sp-poin { background:#f59e0b; color:#fff; }

/* Baik */
.level-col.baik { background:#f0fdf4; border-color:#bbf7d0; }
.level-col.baik:hover { border-color:#22c55e; }
.level-col.baik .lc-header { background:#dcfce7; }
.level-col.baik .lc-title { color:#16a34a; }
.level-col.baik .lc-range { color:#22c55e; }
.level-col.baik.active { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,0.18); background:#ecfdf5; }
.level-col.baik.active .lc-header { background:#22c55e; }
.level-col.baik.active .lc-title { color:#fff; }
.level-col.baik.active .lc-range { color:#dcfce7; }
.level-col.baik .subpoin-btn .sp-poin { background:#dcfce7; color:#16a34a; }
.level-col.baik .subpoin-btn.selected { border-color:#22c55e; color:#16a34a; background:#fff; }
.level-col.baik .subpoin-btn.selected .sp-poin { background:#22c55e; color:#fff; }

.level-col .lc-header {
    padding: 10px 12px; border-radius: 11px 11px 0 0;
    display: flex; align-items: center; justify-content: space-between;
}
.level-col .lc-body  { padding: 10px 12px 12px; }
.level-col .lc-title { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.07em; }
.level-col .lc-range { font-size:10px; font-weight:700; margin-top:1px; }
.level-col .lc-desc  { font-size:11.5px; color:#6b7280; line-height:1.55; min-height:48px; }

/* Sub-poin */
.subpoin-wrap { display:none; margin-top:8px; border-top:1px solid rgba(0,0,0,0.07); padding-top:8px; }
.level-col.active .subpoin-wrap { display:block; }

.subpoin-btn {
    width:100%; text-align:left; padding:7px 9px;
    border-radius:8px; font-size:11.5px; color:#374151;
    background:rgba(255,255,255,0.6); border:1.5px solid transparent;
    cursor:pointer; transition:all 0.15s;
    display:flex; align-items:flex-start; gap:7px; margin-bottom:4px;
}
.subpoin-btn:last-child { margin-bottom:0; }
.subpoin-btn:hover { background:rgba(255,255,255,0.95); border-color:rgba(0,0,0,0.10); }
.subpoin-btn .sp-poin {
    font-size:10.5px; font-weight:900; min-width:26px; height:20px;
    border-radius:5px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}

/* check icon di header */
.lc-check {
    width:18px; height:18px; border-radius:50%;
    background:rgba(255,255,255,0.3);
    display:flex; align-items:center; justify-content:center;
    opacity:0; transition:opacity 0.2s;
}
.level-col.active .lc-check { opacity:1; }

/* progress bar kategori */
.kat-progress-bar { height:6px; border-radius:99px; background:#e5e7eb; overflow:hidden; width:120px; }
.kat-progress-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#0d4d4d,#0d9488); transition:width 0.3s ease; }

/* hasil pilih badge */
.nilai-result {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 12px; border-radius:99px;
    font-size:12px; font-weight:800;
    opacity:0; transform:scale(0.85);
    transition:all 0.2s ease;
}
.nilai-result.show { opacity:1; transform:scale(1); }

.aspek-row { padding:20px 28px; border-bottom:1px solid #f3f4f6; }
.aspek-row:last-child { border-bottom:none; }
</style>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-on-surface tracking-tight">Input Penilaian</h1>
        <p class="text-sm text-on-surface-variant mt-0.5">Dosen Pengampu — bobot 45%</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- LEGENDA --}}
    <div class="flex items-center gap-2 mb-6 flex-wrap">
        <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mr-1">Level:</span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold" style="background:#fee2e2;color:#dc2626;">
            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Kurang &middot; 10–42
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold" style="background:#fef3c7;color:#d97706;">
            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Cukup &middot; 43–74
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold" style="background:#dcfce7;color:#16a34a;">
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Baik &middot; 75–100
        </span>
        <span class="text-[11px] text-on-surface-variant ml-2">← Klik level, lalu pilih poin spesifik</span>
    </div>

    <form action="{{ route('penilaian.dosen.store') }}" method="POST" id="formPenilaian">
        @csrf

        {{-- PILIH SUPERVISI & MAHASISWA --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
            <h2 class="text-sm font-black text-on-surface uppercase tracking-widest mb-5">Pilih Mahasiswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-2">Mata Kuliah Supervisi</label>
                    <select name="supervisi_matkul_id" id="supervisiSelect" required
                        class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm font-medium text-on-surface bg-surface-container-low focus:outline-none focus:border-primary"
                        onchange="loadMahasiswaDosen(this)">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($supervisiList as $supervisi)
                        <option value="{{ $supervisi->id }}"
                            data-mahasiswa='@json(["id"=>$supervisi->mahasiswa->id,"nama"=>$supervisi->mahasiswa->nama,"nim"=>$supervisi->mahasiswa->nim])'>
                            {{ $supervisi->mataKuliah->nama_matkul }} — {{ $supervisi->mahasiswa->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-2">Mahasiswa</label>
                    <input type="hidden" name="mahasiswa_id" id="inputMahasiswaId">
                    <input type="text" id="inputMahasiswaNama" readonly
                        placeholder="Otomatis terisi..."
                        class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm text-on-surface bg-surface-container-low cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- ASPEK PER KATEGORI --}}
        @foreach($perKategori as $namaKategori => $aspekList)
        @php
            $ikon    = $ikonMap[$namaKategori] ?? 'star';
            $slugKat = strtolower(str_replace(' ', '-', $namaKategori));
        @endphp

        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm mb-6 overflow-hidden">

            {{-- Header Kategori --}}
            <div class="flex items-center justify-between px-7 py-5 border-b border-outline-variant/10"
                 style="background:linear-gradient(135deg,#f0fdfa,#ffffff);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#0d4d4d;">
                        <span class="material-symbols-outlined text-lg" style="color:#5eead4;">{{ $ikon }}</span>
                    </div>
                    <div>
                        <p class="font-black text-on-surface text-sm">{{ $namaKategori }}</p>
                        <p class="text-[10px] text-on-surface-variant">{{ count($aspekList) }} aspek</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="kat-progress-bar">
                        <div class="kat-progress-fill" id="bar-{{ $slugKat }}" style="width:0%"></div>
                    </div>
                    <span class="text-xl font-black min-w-[48px] text-right"
                          style="color:#0d9488;" id="total-{{ $slugKat }}">0%</span>
                </div>
            </div>

            {{-- Aspek-aspek --}}
            @foreach($aspekList as $fieldName => $aspek)
            @php
                $slugKat2   = strtolower(str_replace(' ', '-', $aspek['kategori']));
                $opsiKurang = array_filter($aspek['opsi'], fn($p) => $p <= 42,             ARRAY_FILTER_USE_KEY);
                $opsiCukup  = array_filter($aspek['opsi'], fn($p) => $p >= 43 && $p <= 74, ARRAY_FILTER_USE_KEY);
                $opsiBaik   = array_filter($aspek['opsi'], fn($p) => $p >= 75,             ARRAY_FILTER_USE_KEY);

                $descKurang = count($opsiKurang) ? implode(' ', array_slice(array_values($opsiKurang), 0, 2)) : '-';
                $descCukup  = count($opsiCukup)  ? implode(' ', array_slice(array_values($opsiCukup),  0, 2)) : '-';
                $descBaik   = count($opsiBaik)   ? implode(' ', array_slice(array_values($opsiBaik),   0, 2)) : '-';
            @endphp

            <div class="aspek-row">
                {{-- Judul aspek + hasil pilih --}}
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="font-black text-on-surface text-sm">{{ $aspek['label'] }}</p>
                        <p class="text-[10px] text-on-surface-variant">Bobot {{ $aspek['bobot'] }}%</p>
                    </div>
                    <span class="nilai-result" id="result-badge-{{ $fieldName }}"></span>
                </div>

                <input type="hidden" name="{{ $fieldName }}" id="val-{{ $fieldName }}" value="">

                {{-- 3 KOLOM LEVEL --}}
                <div class="flex gap-3">

                    {{-- KURANG --}}
                    @if(count($opsiKurang) > 0)
                    <div class="level-col kurang" id="col-{{ $fieldName }}-kurang"
                         onclick="selectLevel('{{ $fieldName }}', 'kurang', '{{ $slugKat2 }}')">
                        <div class="lc-header">
                            <div>
                                <div class="lc-title">Kurang</div>
                                <div class="lc-range">Poin 10–42</div>
                            </div>
                            <div class="lc-check">
                                <svg width="10" height="10" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="lc-body">
                            <p class="lc-desc">{{ Str::limit($descKurang, 85) }}</p>
                            <div class="subpoin-wrap" id="sub-{{ $fieldName }}-kurang">
                                @foreach($opsiKurang as $poin => $desc)
                                <button type="button" class="subpoin-btn" id="sp-{{ $fieldName }}-{{ $poin }}"
                                    onclick="event.stopPropagation(); pilihSubPoin('{{ $fieldName }}', {{ $poin }}, 'kurang', '{{ $slugKat2 }}', this)">
                                    <span class="sp-poin">{{ $poin }}</span>
                                    <span>{{ $desc }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- CUKUP --}}
                    @if(count($opsiCukup) > 0)
                    <div class="level-col cukup" id="col-{{ $fieldName }}-cukup"
                         onclick="selectLevel('{{ $fieldName }}', 'cukup', '{{ $slugKat2 }}')">
                        <div class="lc-header">
                            <div>
                                <div class="lc-title">Cukup</div>
                                <div class="lc-range">Poin 43–74</div>
                            </div>
                            <div class="lc-check">
                                <svg width="10" height="10" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="lc-body">
                            <p class="lc-desc">{{ Str::limit($descCukup, 85) }}</p>
                            <div class="subpoin-wrap" id="sub-{{ $fieldName }}-cukup">
                                @foreach($opsiCukup as $poin => $desc)
                                <button type="button" class="subpoin-btn" id="sp-{{ $fieldName }}-{{ $poin }}"
                                    onclick="event.stopPropagation(); pilihSubPoin('{{ $fieldName }}', {{ $poin }}, 'cukup', '{{ $slugKat2 }}', this)">
                                    <span class="sp-poin">{{ $poin }}</span>
                                    <span>{{ $desc }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- BAIK --}}
                    @if(count($opsiBaik) > 0)
                    <div class="level-col baik" id="col-{{ $fieldName }}-baik"
                         onclick="selectLevel('{{ $fieldName }}', 'baik', '{{ $slugKat2 }}')">
                        <div class="lc-header">
                            <div>
                                <div class="lc-title">Baik</div>
                                <div class="lc-range">Poin 75–100</div>
                            </div>
                            <div class="lc-check">
                                <svg width="10" height="10" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="lc-body">
                            <p class="lc-desc">{{ Str::limit($descBaik, 85) }}</p>
                            <div class="subpoin-wrap" id="sub-{{ $fieldName }}-baik">
                                @foreach($opsiBaik as $poin => $desc)
                                <button type="button" class="subpoin-btn" id="sp-{{ $fieldName }}-{{ $poin }}"
                                    onclick="event.stopPropagation(); pilihSubPoin('{{ $fieldName }}', {{ $poin }}, 'baik', '{{ $slugKat2 }}', this)">
                                    <span class="sp-poin">{{ $poin }}</span>
                                    <span>{{ $desc }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>{{-- end 3 kolom --}}
            </div>
            @endforeach

        </div>
        @endforeach

        {{-- CATATAN --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
            <label class="block text-xs font-black text-on-surface uppercase tracking-widest mb-3">Catatan Dosen</label>
            <textarea name="catatan_dosen" rows="3"
                placeholder="Catatan tambahan (opsional)..."
                class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm text-on-surface bg-surface-container-low focus:outline-none focus:border-primary resize-none"></textarea>
        </div>

        {{-- SUMMARY & SUBMIT --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black text-on-surface-variant uppercase tracking-widest mb-1">Estimasi Nilai Dosen (45%)</p>
<div class="flex items-end gap-3">
    <p class="text-4xl font-black" style="color:#0d9488;" id="grandTotal">0.00</p>
    <div class="mb-1">
        <span class="text-[10px] text-gray-400 font-bold block">dari maks. 45</span>
        <span class="text-sm font-black text-gray-500">≈ <span id="grandTotalPer100">0.00</span> / 100</span>
    </div>
</div>
<div class="mt-2 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
    <span class="material-symbols-outlined text-amber-500 text-sm shrink-0 mt-0.5">info</span>
    <p class="text-[10px] text-amber-700 leading-relaxed">
        Nilai ini adalah <strong>kontribusi 45%</strong> dari nilai akhir mahasiswa.
        Nilai Akhir = Nilai Manager Proyek + Nilai Dosen (total maks. 100).
    </p>
</div>
<p class="text-[10px] mt-2" id="infoAspekBelum" style="color:#9ca3af;">
    Semua aspek harus dipilih sebelum menyimpan.
</p>
                </div>
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-4 rounded-2xl text-sm font-black text-white shadow-xl transition-all hover:scale-105"
                    style="background:linear-gradient(135deg,#0d4d4d,#0d9488);">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Penilaian
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var bobotMap = {
    @foreach($semuaAspek as $fieldName => $aspek)
    '{{ $fieldName }}': {{ $aspek['bobot'] }},
    @endforeach
};
var kategoriMap = {
    @foreach($semuaAspek as $fieldName => $aspek)
    '{{ $fieldName }}': '{{ strtolower(str_replace(' ', '-', $aspek['kategori'])) }}',
    @endforeach
};
var semuaField    = {!! json_encode(array_keys($semuaAspek)) !!};
var nilaiTerpilih = {};

// Populate mahasiswa otomatis dari supervisi
function loadMahasiswaDosen(select) {
    var opt  = select.options[select.selectedIndex];
    var data = opt.dataset.mahasiswa ? JSON.parse(opt.dataset.mahasiswa) : null;
    if (data) {
        document.getElementById('inputMahasiswaId').value   = data.id;
        document.getElementById('inputMahasiswaNama').value = data.nama + ' (' + data.nim + ')';
    } else {
        document.getElementById('inputMahasiswaId').value   = '';
        document.getElementById('inputMahasiswaNama').value = '';
    }
}

// Step 1 — Klik kolom level → expand sub-poin (toggle)
function selectLevel(fieldName, level, slugKat) {
    var levels = ['kurang', 'cukup', 'baik'];
    var colEl  = document.getElementById('col-' + fieldName + '-' + level);
    if (!colEl) return;
    var sudahAktif = colEl.classList.contains('active');

    levels.forEach(function(lv) {
        var el = document.getElementById('col-' + fieldName + '-' + lv);
        if (el) el.classList.remove('active');
    });

    if (!sudahAktif) colEl.classList.add('active');
}

// Step 2 — Klik sub-poin → simpan nilai & update UI
function pilihSubPoin(fieldName, poin, level, slugKat, elBtn) {
    ['kurang', 'cukup', 'baik'].forEach(function(lv) {
        var wrap = document.getElementById('sub-' + fieldName + '-' + lv);
        if (!wrap) return;
        wrap.querySelectorAll('.subpoin-btn').forEach(function(b) { b.classList.remove('selected'); });
    });

    elBtn.classList.add('selected');

    document.getElementById('val-' + fieldName).value = poin;
    nilaiTerpilih[fieldName] = poin;

    var badge  = document.getElementById('result-badge-' + fieldName);
    var colors = { kurang:['#fee2e2','#dc2626'], cukup:['#fef3c7','#d97706'], baik:['#dcfce7','#16a34a'] };
    var labels = { kurang:'Kurang', cukup:'Cukup', baik:'Baik' };
    badge.style.background = colors[level][0];
    badge.style.color      = colors[level][1];
    badge.innerHTML = '<span style="font-size:15px;font-weight:900;">' + poin + '</span>'
                    + '<span style="font-size:10px;font-weight:700;opacity:0.75;">' + labels[level] + '</span>';
    badge.classList.add('show');

    updateTotalKategori(slugKat);
    updateGrandTotal();
}

function updateTotalKategori(slugKat) {
    var total = 0, bobotTotal = 0;
    Object.keys(kategoriMap).forEach(function(field) {
        if (kategoriMap[field] !== slugKat) return;
        bobotTotal += bobotMap[field];
        if (nilaiTerpilih[field] !== undefined)
            total += nilaiTerpilih[field] * (bobotMap[field] / 100);
    });
    var pct = bobotTotal > 0 ? Math.round((total / bobotTotal) * 100) : 0;
    var el  = document.getElementById('total-' + slugKat);
    var bar = document.getElementById('bar-'   + slugKat);
    if (el)  el.textContent  = pct + '%';
    if (bar) bar.style.width = pct + '%';
}

function updateGrandTotal() {
    var total = 0;
    semuaField.forEach(function(field) {
        if (nilaiTerpilih[field] !== undefined)
            total += nilaiTerpilih[field] * (bobotMap[field] / 100);
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
    var per100 = total > 0 ? (total / 45 * 100).toFixed(2) : '0.00';
    var el100  = document.getElementById('grandTotalPer100');
    if (el100) el100.textContent = per100;

    var belum = semuaField.filter(function(f) { return nilaiTerpilih[f] === undefined; });
    var info  = document.getElementById('infoAspekBelum');
    if (belum.length === 0) {
        info.textContent = '✓ Semua aspek sudah dipilih.';
        info.style.color = '#0d9488';
    } else {
        info.textContent = belum.length + ' aspek belum dipilih.';
        info.style.color = '#9ca3af';
    }
}

// Submit guard
document.getElementById('formPenilaian').addEventListener('submit', function(e) {
    var mhsId = document.getElementById('inputMahasiswaId').value;
    if (!mhsId) {
        e.preventDefault();
        alert('Pilih mata kuliah supervisi terlebih dahulu.');
        return;
    }
    var belum = semuaField.filter(function(f) { return nilaiTerpilih[f] === undefined; });
    if (belum.length > 0) {
        e.preventDefault();
        alert(belum.length + ' aspek belum dipilih. Harap pilih semua aspek terlebih dahulu.');
    }
});
</script>
@endpush