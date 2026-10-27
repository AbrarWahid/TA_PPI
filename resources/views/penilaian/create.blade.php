@extends('layouts.app')

@section('title', 'Input Penilaian')

@section('content')
@php
    $isManager = auth()->user()->isManager();
    $isDosen   = auth()->user()->isDosen();

    $semuaAspek = [
        'ls_critical_thinking' => ['label'=>'Critical Thinking','kategori'=>'Learning Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Menganalisis permasalahan secara dangkal.',16=>'Tidak melakukan evaluasi terhadap informasi yang diterima.',23=>'Menggunakan ide yang sudah ada tanpa mengevaluasi.',29=>'Menerima masukan tanpa ada pertimbangan.',36=>'Tidak mampu memberikan alasan yang valid untuk mempertahankan pilihan.',42=>'Mengidentifikasi aspek permasalahan utama tetapi tidak mempertimbangkan kerumitan.',49=>'Melakukan evaluasi terhadap informasi yang diterima.',55=>'Menggunakan ide yang sudah ada dengan mengevaluasi terlebih dahulu (tidak rinci).',61=>'Menerima masukan dengan sedikit pertimbangan.',68=>'Mulai mampu memberikan alasan untuk mempertahankan pilihan.',74=>'Mengidentifikasi aspek permasalahan utama dan mempertimbangkan kerumitan yang ada.',81=>'Melakukan evaluasi terhadap informasi yang diterima secara detail.',87=>'Menggunakan ide dengan mengevaluasi dan menyesuaikan apakah mungkin diterapkan.',94=>'Menerima masukan dengan melakukan pertimbangan terlebih dahulu.',100=>'Dapat memberikan alasan yang valid untuk mempertahankan pilihan yang dibuat.']],
        'ls_kolaborasi' => ['label'=>'Kolaborasi','kategori'=>'Learning Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak bertanggung jawab terhadap tugas masing-masing.',18=>'Tidak menyelesaikan tugas tepat waktu.',26=>'Tidak mempertimbangkan pendapat orang lain.',35=>'Tidak melimpahkan tugas kepada orang lain.',43=>'Bertanggung jawab terhadap tugas masing-masing.',51=>'Berusaha menyelesaikan tugas tepat waktu walaupun akhirnya tidak tepat waktu.',59=>'Mempertimbangkan masukan orang lain.',67=>'Melimpahkan tugas kepada orang lain.',75=>'Bertanggung jawab penuh terhadap tugas masing-masing.',84=>'Menyelesaikan tugas tepat waktu.',92=>'Mempertimbangkan masukan orang lain secara aktif.',100=>'Melimpahkan tugas kepada orang lain secara efektif.']],
        'ls_kreativitas' => ['label'=>'Kreativitas & Inovasi','kategori'=>'Learning Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak mengetahui tujuan dari proyek.',15=>'Tidak mempertimbangkan kebutuhan user.',21=>'Tidak mengetahui tantangan dalam proyek.',26=>'Hanya mengikuti arahan saja.',31=>'Tidak memberikan ide baru untuk penyelesaian masalah.',36=>'Tidak mampu mengidentifikasi kebutuhan proyek.',42=>'Mengetahui secara umum tujuan dari proyek.',47=>'Mempertimbangkan kebutuhan user.',52=>'Mengetahui sebagian dari tantangan proyek.',58=>'Hanya mengikuti arahan yang sudah ada.',63=>'Tidak memberikan ide baru untuk penyelesaian masalah (cukup).',68=>'Tidak mampu mengidentifikasi kebutuhan proyek (cukup).',74=>'Mengetahui tujuan dari proyek dengan baik.',79=>'Mempertimbangkan kebutuhan user secara menyeluruh.',84=>'Mengetahui semua tantangan proyek.',89=>'Mampu memberikan alternatif solusi dalam pemecahan masalah.',95=>'Memberikan ide baru untuk penyelesaian masalah.',100=>'Mampu mengidentifikasi kebutuhan proyek secara lengkap.']],
        'ls_komunikasi' => ['label'=>'Komunikasi','kategori'=>'Learning Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak bisa berkomunikasi dengan anggota tim (lebih banyak diam).',21=>'Tidak bisa menyampaikan ide atau pendapat kepada tim.',32=>'Menggunakan kata-kata yang tidak sopan dalam berkomunikasi.',44=>'Mampu berkomunikasi dengan anggota tim.',55=>'Mampu menyampaikan ide kepada tim.',66=>'Seringkali menggunakan kata-kata yang tidak sopan.',78=>'Mampu berkomunikasi dengan baik dengan anggota tim.',89=>'Mampu menyampaikan ide kepada tim dengan jelas.',100=>'Dalam berkomunikasi tidak pernah menggunakan kata-kata yang tidak sopan.']],
        'lf_fleksibilitas' => ['label'=>'Fleksibilitas','kategori'=>'Life Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak mampu mencari jalan keluar ketika ada masalah.',28=>'Tidak mampu beradaptasi jika strategi yang dirancang tidak sesuai dengan implementasi.',46=>'Berusaha mencari jalan keluar walaupun kadang belum sesuai.',64=>'Mampu beradaptasi dengan strategi baru walaupun dengan arahan dan bimbingan.',82=>'Mampu mencari jalan keluar ketika ditemukan masalah.',100=>'Mampu beradaptasi dengan strategi baru tanpa harus dibimbing secara keseluruhan.']],
        'lf_kepemimpinan' => ['label'=>'Kepemimpinan','kategori'=>'Life Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak menjadi ketua kelompok.',23=>'Tidak bisa menerima pendapat orang lain.',36=>'Tidak menjadi ketua kelompok dan kurang inisiatif.',49=>'Mampu menghargai pendapat orang lain.',61=>'Dapat menentukan strategi dalam penyelesaian proyek.',74=>'Menjadi ketua kelompok.',87=>'Bisa menampung dan menghargai pendapat semua anggota dalam tim.',100=>'Dapat menentukan strategi dalam penyelesaian proyek secara efektif.']],
        'lf_produktivitas' => ['label'=>'Produktivitas','kategori'=>'Life Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Waktu lebih banyak digunakan untuk hal-hal yang tidak penting.',28=>'Hasil dari setiap tahapan tidak sesuai dengan yang direncanakan.',46=>'Berusaha memanfaatkan waktu dengan sebaik-baiknya.',64=>'Terdapat beberapa output pada tahapan yang selesai melebihi waktu.',82=>'Waktu dimanfaatkan sebaik-baiknya sehingga output sesuai perencanaan.',100=>'Terdapat beberapa output yang selesai sebelum waktunya.']],
        'lf_social_skill' => ['label'=>'Social Skill','kategori'=>'Life Skills','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak mampu berkomunikasi dengan teman dalam tim.',55=>'Cukup mampu berkomunikasi dengan teman dalam tim.',100=>'Mampu berkomunikasi dengan baik dengan teman dalam tim maupun tim lain.']],
        'lp_rpp' => ['label'=>'RPP','kategori'=>'Laporan Project','bobot'=>5,'role'=>'manager','opsi'=>[10=>'RPP tidak lengkap, tidak mencakup komponen dasar, dan tidak sesuai dengan proyek.',40=>'RPP mencakup komponen dasar namun kurang rinci; beberapa bagian belum sesuai konteks proyek.',70=>'RPP cukup lengkap dan sesuai proyek, namun masih ada bagian yang perlu disempurnakan.',100=>'RPP lengkap, rinci, dan sepenuhnya sesuai proyek dengan semua komponen secara komprehensif.']],
        'lp_logbook' => ['label'=>'Logbook Mingguan','kategori'=>'Laporan Project','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak diisi atau <25% pertemuan; catatan tidak informatif.',40=>'Diisi 26-50% pertemuan; catatan singkat, kurang menggambarkan aktivitas.',70=>'Diisi 51-75% pertemuan; cukup informatif meski belum konsisten.',100=>'Diisi konsisten setiap minggu (>75%); lengkap, mencatat aktivitas, kendala, dan solusi.']],
        'lp_dokumen_projek' => ['label'=>'Dokumen Projek','kategori'=>'Laporan Project','bobot'=>5,'role'=>'manager','opsi'=>[10=>'Tidak lengkap; tidak ada latar belakang, tujuan, metodologi, atau hasil.',40=>'Mencakup beberapa bagian utama namun penjelasan dangkal.',70=>'Mencakup sebagian besar komponen dengan penjelasan yang cukup jelas.',100=>'Lengkap, terstruktur dengan baik, semua komponen rinci dan mudah dipahami.']],
        'lit_informasi' => ['label'=>'Literasi Informasi','kategori'=>'Literacy Skills','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'Menggunakan informasi tanpa menggunakan etika yang benar.',28=>'Tidak mengerti dengan apa yang dicari.',46=>'Menggunakan informasi dengan menggunakan etika yang benar.',64=>'Tidak terlalu mengerti dengan apa yang dicari.',82=>'Menggunakan informasi dengan etika yang benar secara konsisten.',100=>'Mengerti dengan apa yang dicari secara mendalam.']],
        'lit_media' => ['label'=>'Literasi Media','kategori'=>'Literacy Skills','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'Tidak tepat dalam menggunakan sumber.',28=>'Tidak mempertimbangkan kualitas informasi.',46=>'Melakukan identifikasi sebagian sumber dengan tepat walaupun masih ada yang belum tepat.',64=>'Memahami bahwa kualitas informasi perlu dipertimbangkan walaupun belum menyeluruh.',82=>'Melakukan identifikasi sumber dengan tepat dan sesuai.',100=>'Menilai kualitas informasi secara menyeluruh dengan mempertimbangkan keakuratan dan kredibilitas.']],
        'lit_teknologi' => ['label'=>'Literasi Teknologi','kategori'=>'Literacy Skills','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'Tidak mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.',55=>'Berusaha menggunakan dan memahami teknologi meskipun masih ada kendala.',100=>'Mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.']],
        'pr_konten' => ['label'=>'Konten Presentasi','kategori'=>'Presentasi','bobot'=>3,'role'=>'dosen','opsi'=>[10=>'Informasi penting tidak disampaikan; penyampaian tidak rinci sehingga audiens bingung.',55=>'Informasi penting disampaikan secara lengkap dan berupaya menjelaskan rinci walaupun masih ada pertanyaan.',100=>'Menyajikan informasi dengan lengkap dan jelas. Penyampaian rinci sehingga audiens mengerti.']],
        'pr_visual' => ['label'=>'Tampilan Visual Presentasi','kategori'=>'Presentasi','bobot'=>3,'role'=>'dosen','opsi'=>[10=>'Tampilannya penuh dengan teks, tidak ada gambar atau grafik.',28=>'Judul tidak sesuai dengan apa yang ditampilkan.',46=>'Tampilan diselingi dengan beberapa gambar/grafik/tabel.',64=>'Terdapat beberapa judul yang tidak sesuai.',82=>'Dalam tampilan gambar/tabel/grafik dan teks ditampilkan seimbang sehingga audiens tertarik.',100=>'Judul sesuai dengan yang ditampilkan.']],
        'pr_kosakata' => ['label'=>'Pemilihan Kosakata','kategori'=>'Presentasi','bobot'=>3,'role'=>'dosen','opsi'=>[10=>'Sering menggunakan kata berulang-ulang.',28=>'Menggunakan kata yang tidak formal dalam penyampaian.',46=>'Sedikit sekali menggunakan kata berulang.',64=>'Sebagian dari penyampaian menggunakan kata-kata yang tidak formal.',82=>'Lancar menyampaikan presentasi, tidak gugup, tidak menggunakan kata berulang.',100=>'Dalam penyampaian menggunakan kata-kata formal dan mudah dipahami.']],
        'pr_tanya_jawab' => ['label'=>'Tanya Jawab','kategori'=>'Presentasi','bobot'=>3,'role'=>'dosen','opsi'=>[10=>'Tidak bisa menjawab satupun pertanyaan dari audiens.',55=>'Mampu menjawab pertanyaan audiens walaupun tidak semuanya dan masih ada kesalahan.',100=>'Mampu menjawab semua pertanyaan audiens dengan jelas.']],
        'pr_mata_gerak' => ['label'=>'Mata & Gerak Tubuh','kategori'=>'Presentasi','bobot'=>3,'role'=>'dosen','opsi'=>[10=>'Tidak melihat audiens.',18=>'Hanya membaca slide, tidak ada pengembangan.',26=>'Tidak ada gerakan tubuh (monoton).',35=>'Gelisah, tidak tenang.',43=>'Sesekali melihat kepada audiens.',51=>'Mencoba mengembangkan isi dari beberapa slide.',59=>'Menggunakan gerakan tubuh tetapi tidak natural.',67=>'Tidak gelisah dan cukup tenang.',75=>'Menjaga kontak mata dengan audiens.',84=>'Tidak terpaku pada teks di slide dan mengembangkan isi slide presentasi.',92=>'Menggunakan gerakan tubuh yang tidak dibuat-buat.',100=>'Tenang dan percaya diri.']],
        'la_penulisan' => ['label'=>'Penulisan Laporan','kategori'=>'Laporan Akhir','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'Banyak ditemukan kesalahan dalam pengetikan.',20=>'Banyak kalimat yang sulit dipahami.',30=>'Dokumen tidak selesai.',40=>'Penomoran untuk tabel, gambar dan grafik tidak sesuai.',50=>'Tidak ditemukan kesalahan pengetikan.',60=>'Kalimat-kalimat mudah dipahami.',70=>'Sebagian masih ditemukan kesalahan dalam penomoran tabel, grafik, dan gambar.',80=>'Tidak ditemukan kesalahan pengetikan (konsisten).',90=>'Kalimat-kalimat mudah dipahami dan runtut.',100=>'Penomoran tabel, grafik dan gambar sudah sesuai.']],
        'la_pilihan_kata' => ['label'=>'Pilihan Kata','kategori'=>'Laporan Akhir','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'50% dari penulisan laporan menggunakan kata-kata yang tidak formal.',28=>'Banyak ditemukan penulisan kata dalam bentuk singkatan.',46=>'20% dari penulisan laporan menggunakan kata-kata yang tidak formal.',64=>'Tidak ditemukan penulisan kata dalam bentuk singkatan.',82=>'Penulisan laporan semuanya menggunakan kata-kata formal.',100=>'Tidak ditemukan penulisan kata-kata dalam bentuk singkatan.']],
        'la_konten' => ['label'=>'Konten Laporan Akhir','kategori'=>'Laporan Akhir','bobot'=>5,'role'=>'dosen','opsi'=>[10=>'Informasi yang disampaikan tidak jelas, tidak akurat, tidak relevan.',21=>'Berdasarkan hasil investigasi banyak ditemukan hasil copy paste tanpa elaborasi.',32=>'Isi dari laporan tidak sesuai dengan proyek yang dibuat.',44=>'Informasi yang disampaikan akurat, jelas dan relevan.',55=>'Dari hasil pencarian masih ada ditemukan hasil copy paste tanpa elaborasi.',66=>'30% dari isi laporan tidak sesuai dengan proyek yang dibuat.',78=>'Informasi yang disampaikan akurat, jelas dan relevan (konsisten).',89=>'Dari hasil pencarian sedikit ditemukan copy paste.',100=>'Isi laporan semuanya sesuai dengan proyek yang dibuat.']],
    ];

    $aspekAktif = array_filter($semuaAspek, fn($a) =>
        ($isManager && $a['role'] === 'manager') ||
        ($isDosen   && $a['role'] === 'dosen')
    );

    $ikonMap = ['Learning Skills'=>'lightbulb','Life Skills'=>'self_improvement','Laporan Project'=>'folder_open','Literacy Skills'=>'menu_book','Presentasi'=>'co_present','Laporan Akhir'=>'description'];

    // Kelompokkan per kategori (manual, tidak pakai groupBy)
    $perKategori = [];
    foreach ($aspekAktif as $fieldName => $aspek) {
        $perKategori[$aspek['kategori']][$fieldName] = $aspek;
    }
@endphp

<div class="max-w-4xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl font-black text-on-surface tracking-tight">Input Penilaian</h1>
        <p class="text-sm text-on-surface-variant mt-0.5">
            {{ $isManager ? 'Manager Proyek — bobot 55%' : 'Dosen Pengampu — bobot 45%' }}
        </p>
    </div>

    <form action="{{ route('penilaian.store') }}" method="POST" id="formPenilaian">
        @csrf

        {{-- PILIH PROYEK & MAHASISWA --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
            <h2 class="text-sm font-black text-on-surface uppercase tracking-widest mb-5">Pilih Mahasiswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-2">Proyek</label>
                    <select name="pengajuan_proyek_id" id="proyekSelect" required
                        class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm font-medium text-on-surface bg-surface-container-low focus:outline-none focus:border-primary">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($proyekList as $proyek)
                        <option value="{{ $proyek->id }}"
                            data-mahasiswa='@json($proyek->mahasiswa->map(fn($m) => ["id"=>$m->id,"nama"=>$m->nama,"nim"=>$m->nim]))'>
                            {{ $proyek->judul_proyek }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-2">Mahasiswa</label>
                    <select name="mahasiswa_id" id="mahasiswaSelect" required
                        class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm font-medium text-on-surface bg-surface-container-low focus:outline-none focus:border-primary">
                        <option value="">-- Pilih Proyek Dulu --</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ASPEK PER KATEGORI --}}
        @foreach($perKategori as $namaKategori => $aspekList)
        @php $ikon = $ikonMap[$namaKategori] ?? 'star'; @endphp
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm mb-6 overflow-hidden">

            {{-- Header Kategori --}}
            <div class="flex items-center justify-between px-7 py-5 border-b border-outline-variant/10" style="background:linear-gradient(135deg,#f0fdf4,#ffffff);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#004d4d;">
                        <span class="material-symbols-outlined text-lg" style="color:#7fffd4;">{{ $ikon }}</span>
                    </div>
                    <div>
                        <p class="font-black text-on-surface text-sm">{{ $namaKategori }}</p>
                        <p class="text-[10px] text-on-surface-variant">{{ count($aspekList) }} aspek</p>
                    </div>
                </div>
                @php $slugKat = strtolower(str_replace(' ','-',$namaKategori)); @endphp
                <span class="text-2xl font-black text-primary" id="total-{{ $slugKat }}">0%</span>
            </div>

            {{-- Aspek --}}
            @foreach($aspekList as $fieldName => $aspek)
            @php $slugKat = strtolower(str_replace(' ','-',$aspek['kategori'])); @endphp
            <div class="px-7 py-6 border-b border-outline-variant/10 last:border-b-0">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="font-black text-on-surface text-sm">{{ $aspek['label'] }}</p>
                        <p class="text-[10px] text-on-surface-variant">Bobot {{ $aspek['bobot'] }}%</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-on-surface-variant">Poin terpilih</span>
                        <p class="font-black text-primary text-lg" id="preview-{{ $fieldName }}">—</p>
                    </div>
                </div>

                <input type="hidden" name="{{ $fieldName }}" id="val-{{ $fieldName }}" value="">

                <div class="space-y-2" id="opsi-{{ $fieldName }}">
                    @foreach($aspek['opsi'] as $poin => $deskripsi)
                    <div class="rubrik-opsi-item flex items-start gap-3 p-3.5 rounded-xl cursor-pointer transition-all"
                         style="border: 2px solid transparent;"
                         data-field="{{ $fieldName }}"
                         data-poin="{{ $poin }}"
                         data-slugkat="{{ $slugKat }}"
                         onclick="pilihOpsi('{{ $fieldName }}', {{ $poin }}, '{{ $slugKat }}', this)">
                        <div class="w-5 h-5 rounded-full border-2 flex-shrink-0 mt-0.5 flex items-center justify-center transition-all"
                             style="border-color: #d1d5db;"
                             id="dot-{{ $fieldName }}-{{ $poin }}">
                        </div>
                        <span class="text-sm text-on-surface-variant leading-relaxed select-none">{{ $deskripsi }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
        @endforeach

        {{-- CATATAN --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
            <label class="block text-xs font-black text-on-surface uppercase tracking-widest mb-3">
                Catatan {{ $isManager ? 'Manager' : 'Dosen' }}
            </label>
            <textarea name="{{ $isManager ? 'catatan_manager' : 'catatan_dosen' }}"
                rows="3"
                placeholder="Catatan tambahan (opsional)..."
                class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm text-on-surface bg-surface-container-low focus:outline-none focus:border-primary resize-none"></textarea>
        </div>

        {{-- SUMMARY & SUBMIT --}}
        <div class="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black text-on-surface-variant uppercase tracking-widest mb-1">
                        Estimasi Nilai {{ $isManager ? 'Manager (55%)' : 'Dosen (45%)' }}
                    </p>
                    <p class="text-4xl font-black text-primary" id="grandTotal">0.00</p>
                </div>
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-4 rounded-2xl text-sm font-black text-white shadow-xl transition-all hover:scale-105"
                    style="background:linear-gradient(135deg,#004d4d,#008080);">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Penilaian
                </button>
            </div>
            <p class="text-[10px] text-on-surface-variant mt-3" id="infoAspekBelum">
                Semua aspek harus dipilih sebelum menyimpan.
            </p>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
const bobotMap = {
    @foreach($aspekAktif as $fieldName => $aspek)
    '{{ $fieldName }}': {{ $aspek['bobot'] }},
    @endforeach
};

const kategoriMap = {
    @foreach($aspekAktif as $fieldName => $aspek)
    '{{ $fieldName }}': '{{ strtolower(str_replace(' ', '-', $aspek['kategori'])) }}',
    @endforeach
};

const semuaField = {!! json_encode(array_keys($aspekAktif)) !!};
const nilaiTerpilih = {};

function pilihOpsi(fieldName, poin, slugKat, elKlik) {
    // Reset semua opsi dalam field ini
    document.querySelectorAll('#opsi-' + fieldName + ' .rubrik-opsi-item').forEach(function(el) {
        el.style.borderColor = 'transparent';
        el.style.backgroundColor = '';
        var dotId = 'dot-' + fieldName + '-' + el.dataset.poin;
        var dot = document.getElementById(dotId);
        if (dot) {
            dot.style.borderColor = '#d1d5db';
            dot.innerHTML = '';
        }
    });

    // Style opsi yang dipilih
    elKlik.style.borderColor = '#004d4d';
    elKlik.style.backgroundColor = 'rgba(0,77,77,0.05)';
    var dotPilih = document.getElementById('dot-' + fieldName + '-' + poin);
    if (dotPilih) {
        dotPilih.style.borderColor = '#004d4d';
        dotPilih.innerHTML = '<div style="width:10px;height:10px;background:#004d4d;border-radius:50%;"></div>';
    }

    // Set nilai
    document.getElementById('val-' + fieldName).value = poin;
    document.getElementById('preview-' + fieldName).textContent = poin;
    nilaiTerpilih[fieldName] = poin;

    updateTotalKategori(slugKat);
    updateGrandTotal();
}

function updateTotalKategori(slugKat) {
    var total = 0, max = 0;
    Object.keys(kategoriMap).forEach(function(field) {
        if (kategoriMap[field] !== slugKat) return;
        max += bobotMap[field];
        if (nilaiTerpilih[field] !== undefined) {
            total += nilaiTerpilih[field] * (bobotMap[field] / 100);
        }
    });
    var el = document.getElementById('total-' + slugKat);
    if (el) el.textContent = max > 0 ? Math.round((total / max) * 100) + '%' : '0%';
}

function updateGrandTotal() {
    var total = 0;
    semuaField.forEach(function(field) {
        if (nilaiTerpilih[field] !== undefined) {
            total += nilaiTerpilih[field] * (bobotMap[field] / 100);
        }
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);

    var belum = semuaField.filter(function(f) { return nilaiTerpilih[f] === undefined; });
    var info = document.getElementById('infoAspekBelum');
    if (belum.length === 0) {
        info.textContent = '✓ Semua aspek sudah dipilih.';
        info.style.color = '#004d4d';
    } else {
        info.textContent = belum.length + ' aspek belum dipilih.';
        info.style.color = '#9ca3af';
    }
}

document.getElementById('proyekSelect').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var list = opt.dataset.mahasiswa ? JSON.parse(opt.dataset.mahasiswa) : [];
    var sel = document.getElementById('mahasiswaSelect');
    sel.innerHTML = '<option value="">-- Pilih Mahasiswa --</option>';
    list.forEach(function(m) {
        sel.innerHTML += '<option value="' + m.id + '">' + m.nama + ' (' + m.nim + ')</option>';
    });
});

document.getElementById('formPenilaian').addEventListener('submit', function(e) {
    var belum = semuaField.filter(function(f) { return nilaiTerpilih[f] === undefined; });
    if (belum.length > 0) {
        e.preventDefault();
        alert(belum.length + ' aspek belum dipilih. Harap pilih semua aspek terlebih dahulu.');
    }
});
</script>
@endpush