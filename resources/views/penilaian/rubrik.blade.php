@extends('layouts.app')

@section('title', 'Rubrik Penilaian')

@section('content')
@php
$activeTab = request('tab', 'learning');

$kategori = [
    'learning' => [
        'label' => 'Learning Skills',
        'bobot' => '20%',
        'penilai' => 'Manager Proyek',
        'color' => 'primary',
        'icon' => 'psychology',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'Critical Thinking', 'bobot' => '5%', 'icon' => 'lightbulb',
                'poin' => [
                    ['range' => 'Poin 10–42', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Menganalisis permasalahan secara dangkal, tidak mengevaluasi informasi, menggunakan ide yang sudah ada tanpa evaluasi, menerima masukan tanpa pertimbangan.'],
                    ['range' => 'Poin 49–74', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mengidentifikasi aspek permasalahan utama, mulai melakukan evaluasi terhadap informasi, menggunakan ide dengan evaluasi awal, menerima masukan dengan sedikit pertimbangan.'],
                    ['range' => 'Poin 81–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mengidentifikasi dan mempertimbangkan kerumitan masalah, evaluasi informasi secara detail, menggunakan ide dengan evaluasi mendalam, memberikan alasan valid untuk pilihan yang dibuat.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Kolaborasi', 'bobot' => '5%', 'icon' => 'groups',
                'poin' => [
                    ['range' => 'Poin 10–35', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak bertanggung jawab terhadap tugas, tidak menyelesaikan tugas tepat waktu, tidak mempertimbangkan pendapat orang lain.'],
                    ['range' => 'Poin 43–75', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Bertanggung jawab terhadap tugas, berusaha menyelesaikan tepat waktu, mempertimbangkan masukan orang lain.'],
                    ['range' => 'Poin 84–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Bertanggung jawab penuh, menyelesaikan tugas tepat waktu, mempertimbangkan masukan secara aktif, melimpahkan tugas secara efektif.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Kreativitas & Inovasi', 'bobot' => '5%', 'icon' => 'auto_awesome',
                'poin' => [
                    ['range' => 'Poin 10–36', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak mengetahui tujuan proyek, tidak mempertimbangkan kebutuhan user, tidak memberikan ide baru, hanya mengikuti arahan.'],
                    ['range' => 'Poin 42–68', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mengetahui secara umum tujuan proyek, mempertimbangkan kebutuhan user, mengetahui sebagian tantangan proyek.'],
                    ['range' => 'Poin 74–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mengetahui tujuan proyek dengan baik, mempertimbangkan kebutuhan user secara menyeluruh, memberikan ide baru, mengidentifikasi kebutuhan proyek secara lengkap.'],
                ],
            ],
            [
                'kode' => 'd1', 'nama' => 'Komunikasi', 'bobot' => '5%', 'icon' => 'campaign',
                'poin' => [
                    ['range' => 'Poin 10–32', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak bisa berkomunikasi dengan anggota tim, tidak bisa menyampaikan ide, menggunakan kata-kata tidak sopan.'],
                    ['range' => 'Poin 44–66', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mampu berkomunikasi dengan anggota tim, mampu menyampaikan ide, namun seringkali menggunakan kata-kata tidak sopan.'],
                    ['range' => 'Poin 78–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mampu berkomunikasi dengan baik, menyampaikan ide dengan jelas, tidak pernah menggunakan kata-kata tidak sopan.'],
                ],
            ],
        ],
    ],
    'life' => [
        'label' => 'Life Skills',
        'bobot' => '20%',
        'penilai' => 'Manager Proyek',
        'color' => 'primary',
        'icon' => 'self_improvement',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'Fleksibilitas', 'bobot' => '5%', 'icon' => 'swap_horiz',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak mampu mencari jalan keluar saat ada masalah, tidak mampu beradaptasi jika strategi tidak sesuai implementasi.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Berusaha mencari jalan keluar meski belum sesuai, mampu beradaptasi dengan strategi baru dengan arahan dan bimbingan.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mampu mencari jalan keluar saat menemukan masalah, mampu beradaptasi dengan strategi baru tanpa harus dibimbing.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Kepemimpinan', 'bobot' => '5%', 'icon' => 'military_tech',
                'poin' => [
                    ['range' => 'Poin 10–36', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak menjadi ketua kelompok, tidak bisa menerima pendapat orang lain, kurang inisiatif.'],
                    ['range' => 'Poin 49–74', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mampu menghargai pendapat orang lain, dapat menentukan strategi dalam penyelesaian proyek.'],
                    ['range' => 'Poin 87–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Menjadi ketua kelompok, menampung dan menghargai pendapat semua anggota, menentukan strategi secara efektif.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Produktivitas', 'bobot' => '5%', 'icon' => 'trending_up',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Waktu banyak digunakan untuk hal tidak penting, hasil setiap tahapan tidak sesuai rencana.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Berusaha memanfaatkan waktu, namun beberapa output selesai melebihi waktu.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Waktu dimanfaatkan sebaik-baiknya, output sesuai perencanaan, beberapa output selesai sebelum waktunya.'],
                ],
            ],
            [
                'kode' => 'd1', 'nama' => 'Social Skill', 'bobot' => '5%', 'icon' => 'handshake',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak mampu berkomunikasi dengan teman dalam tim.'],
                    ['range' => 'Poin 55', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Cukup mampu berkomunikasi dengan teman dalam tim.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mampu berkomunikasi dengan baik dengan teman dalam tim maupun tim lain.'],
                ],
            ],
        ],
    ],
    'laporan_project' => [
        'label' => 'Laporan Project',
        'bobot' => '15%',
        'penilai' => 'Manager Proyek',
        'color' => 'primary',
        'icon' => 'folder_open',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'RPP', 'bobot' => '5%', 'icon' => 'description',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'RPP tidak lengkap, tidak mencakup komponen dasar, dan tidak sesuai dengan proyek.'],
                    ['range' => 'Poin 40–70', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'RPP mencakup komponen dasar namun kurang rinci; beberapa bagian belum sesuai konteks proyek, cukup lengkap namun masih ada yang perlu disempurnakan.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'RPP lengkap, rinci, dan sepenuhnya sesuai proyek dengan semua komponen secara komprehensif.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Logbook Mingguan', 'bobot' => '5%', 'icon' => 'menu_book',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak diisi atau <25% pertemuan; catatan tidak informatif.'],
                    ['range' => 'Poin 40–70', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Diisi 26–75% pertemuan; catatan singkat hingga cukup informatif meski belum konsisten.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Diisi konsisten setiap minggu (>75%); lengkap, mencatat aktivitas, kendala, dan solusi.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Dokumen Projek', 'bobot' => '5%', 'icon' => 'article',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak lengkap; tidak ada latar belakang, tujuan, metodologi, atau hasil.'],
                    ['range' => 'Poin 40–70', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mencakup beberapa bagian utama namun penjelasan dangkal hingga sebagian besar komponen dengan penjelasan cukup jelas.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Lengkap, terstruktur dengan baik, semua komponen rinci dan mudah dipahami.'],
                ],
            ],
        ],
    ],
    'literacy' => [
        'label' => 'Literacy Skills',
        'bobot' => '15%',
        'penilai' => 'Dosen Pengampu',
        'color' => 'secondary',
        'icon' => 'import_contacts',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'Literasi Informasi', 'bobot' => '5%', 'icon' => 'info',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Menggunakan informasi tanpa etika yang benar, tidak mengerti dengan apa yang dicari.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Menggunakan informasi dengan etika yang benar, tidak terlalu mengerti dengan apa yang dicari.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Menggunakan informasi dengan etika yang benar secara konsisten, mengerti dengan apa yang dicari secara mendalam.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Literasi Media', 'bobot' => '5%', 'icon' => 'perm_media',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak tepat dalam menggunakan sumber, tidak mempertimbangkan kualitas informasi.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Melakukan identifikasi sebagian sumber dengan tepat, memahami bahwa kualitas informasi perlu dipertimbangkan.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Melakukan identifikasi sumber dengan tepat dan sesuai, menilai kualitas informasi secara menyeluruh.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Literasi Teknologi', 'bobot' => '5%', 'icon' => 'devices',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.'],
                    ['range' => 'Poin 55', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Berusaha menggunakan dan memahami teknologi meskipun masih ada kendala.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mampu menggunakan, mengelola, memahami, dan menggunakan teknologi yang sesuai.'],
                ],
            ],
        ],
    ],
    'presentasi' => [
        'label' => 'Presentasi',
        'bobot' => '15%',
        'penilai' => 'Dosen Pengampu',
        'color' => 'secondary',
        'icon' => 'co_present',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'Konten', 'bobot' => '3%', 'icon' => 'format_list_bulleted',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Informasi penting tidak disampaikan; penyampaian tidak rinci sehingga audiens bingung.'],
                    ['range' => 'Poin 55', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Informasi penting disampaikan secara lengkap namun masih ada pertanyaan dari audiens.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Menyajikan informasi dengan lengkap dan jelas; penyampaian rinci sehingga audiens mengerti.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Tampilan Visual', 'bobot' => '3%', 'icon' => 'slideshow',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tampilan penuh teks, tidak ada gambar atau grafik, judul tidak sesuai dengan yang ditampilkan.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Tampilan diselingi beberapa gambar/grafik/tabel, terdapat beberapa judul yang tidak sesuai.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Gambar/tabel/grafik dan teks ditampilkan seimbang, judul sesuai dengan yang ditampilkan.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Pemilihan Kosakata', 'bobot' => '3%', 'icon' => 'spellcheck',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Sering menggunakan kata berulang-ulang, menggunakan kata tidak formal dalam penyampaian.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Sedikit sekali menggunakan kata berulang, sebagian penyampaian menggunakan kata tidak formal.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Lancar menyampaikan presentasi, tidak gugup, menggunakan kata-kata formal dan mudah dipahami.'],
                ],
            ],
            [
                'kode' => 'd1', 'nama' => 'Tanya Jawab', 'bobot' => '3%', 'icon' => 'quiz',
                'poin' => [
                    ['range' => 'Poin 10', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak bisa menjawab satupun pertanyaan dari audiens.'],
                    ['range' => 'Poin 55', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Mampu menjawab pertanyaan audiens walaupun tidak semuanya dan masih ada kesalahan.'],
                    ['range' => 'Poin 100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Mampu menjawab semua pertanyaan audiens dengan jelas.'],
                ],
            ],
            [
                'kode' => 'e1', 'nama' => 'Mata & Gerak Tubuh', 'bobot' => '3%', 'icon' => 'visibility',
                'poin' => [
                    ['range' => 'Poin 10–35', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Tidak melihat audiens, hanya membaca slide, tidak ada gerakan tubuh (monoton), gelisah dan tidak tenang.'],
                    ['range' => 'Poin 43–67', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Sesekali melihat kepada audiens, mencoba mengembangkan isi slide, menggunakan gerakan tubuh tapi tidak natural.'],
                    ['range' => 'Poin 75–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Menjaga kontak mata, tidak terpaku pada teks slide, menggunakan gerakan tubuh natural, tenang dan percaya diri.'],
                ],
            ],
        ],
    ],
    'laporan_akhir' => [
        'label' => 'Laporan Akhir',
        'bobot' => '15%',
        'penilai' => 'Dosen Pengampu',
        'color' => 'secondary',
        'icon' => 'task',
        'aspek' => [
            [
                'kode' => 'a1', 'nama' => 'Penulisan Laporan', 'bobot' => '5%', 'icon' => 'edit_note',
                'poin' => [
                    ['range' => 'Poin 10–30', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Banyak kesalahan pengetikan, banyak kalimat sulit dipahami, dokumen tidak selesai, penomoran tidak sesuai.'],
                    ['range' => 'Poin 40–70', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Tidak ditemukan kesalahan pengetikan, kalimat mudah dipahami, namun masih ada kesalahan penomoran tabel/grafik/gambar.'],
                    ['range' => 'Poin 80–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Tidak ada kesalahan pengetikan (konsisten), kalimat mudah dipahami dan runtut, penomoran tabel/grafik/gambar sudah sesuai.'],
                ],
            ],
            [
                'kode' => 'b1', 'nama' => 'Pilihan Kata', 'bobot' => '5%', 'icon' => 'text_fields',
                'poin' => [
                    ['range' => 'Poin 10–28', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => '50% penulisan menggunakan kata tidak formal, banyak ditemukan penulisan kata dalam bentuk singkatan.'],
                    ['range' => 'Poin 46–64', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => '20% penulisan menggunakan kata tidak formal, tidak ditemukan penulisan kata dalam bentuk singkatan.'],
                    ['range' => 'Poin 82–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Penulisan seluruhnya menggunakan kata formal, tidak ditemukan penulisan kata dalam bentuk singkatan.'],
                ],
            ],
            [
                'kode' => 'c1', 'nama' => 'Konten Laporan', 'bobot' => '5%', 'icon' => 'content_paste',
                'poin' => [
                    ['range' => 'Poin 10–32', 'level' => 'Kurang', 'color' => 'red',
                     'desc' => 'Informasi tidak jelas/akurat/relevan, banyak copy paste tanpa elaborasi, isi tidak sesuai proyek.'],
                    ['range' => 'Poin 44–66', 'level' => 'Cukup', 'color' => 'amber',
                     'desc' => 'Informasi akurat dan relevan, masih ada copy paste, 30% isi tidak sesuai dengan proyek yang dibuat.'],
                    ['range' => 'Poin 78–100', 'level' => 'Baik', 'color' => 'emerald',
                     'desc' => 'Informasi akurat, jelas dan relevan secara konsisten, sedikit copy paste, isi laporan seluruhnya sesuai proyek.'],
                ],
            ],
        ],
    ],
];

$tab = $kategori[$activeTab] ?? $kategori['learning'];
@endphp

{{-- Header --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black italic uppercase text-[#004d4d]">
            Rubrik <span class="text-[#2dce89]">Penilaian</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">Standar evaluasi capaian pembelajaran berbasis proyek PBL</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 bg-teal-50 rounded-2xl">
        <span class="material-symbols-outlined text-[#004d4d] text-lg">info</span>
        <span class="text-sm font-black text-[#004d4d]">Total Bobot: 100%</span>
    </div>
</div>
{{-- Ringkasan Bobot --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white border border-outline-variant/30 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white">manage_accounts</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-medium">Manager Proyek</p>
            <p class="text-xl font-bold text-primary">55%</p>
            <p class="text-xs text-on-surface-variant">Learning Skills + Life Skills + Laporan Project</p>
        </div>
    </div>
    <div class="bg-white border border-outline-variant/30 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white">school</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-medium">Dosen Pengampu</p>
            <p class="text-xl font-bold text-secondary">45%</p>
            <p class="text-xs text-on-surface-variant">Literacy Skills + Presentasi + Laporan Akhir</p>
        </div>
    </div>
</div>

{{-- Layout 2 kolom --}}
<div class="flex gap-6">

    {{-- Sidebar Kategori --}}
    <div class="w-56 shrink-0 flex flex-col gap-3">
        <div class="bg-white border border-outline-variant/30 rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest mb-3">Kategori Rubrik</p>
            <nav class="space-y-1">
                @foreach($kategori as $key => $kat)
                    @php
                        $isActive = $activeTab === $key;
                        $penilaiColor = $kat['penilai'] === 'Manager Proyek' ? 'text-primary' : 'text-secondary';
                    @endphp
                    <a href="?tab={{ $key }}"
                       class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between text-sm transition-all
                              {{ $isActive
                                  ? 'bg-primary text-white font-semibold'
                                  : 'text-on-surface-variant hover:bg-surface-container font-medium' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">{{ $kat['icon'] }}</span>
                            <span class="leading-tight">{{ $kat['label'] }}</span>
                        </div>
                        <span class="text-[10px] font-bold {{ $isActive ? 'text-white/70' : 'text-outline' }}">
                            {{ $kat['bobot'] }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Info card --}}
        <div class="bg-[#002b24] rounded-2xl p-4 text-white">
            <span class="material-symbols-outlined text-3xl text-emerald-400/50 mb-2 block">auto_awesome</span>
            <h4 class="font-bold text-sm leading-tight mb-1">Pedoman Penilaian PBL</h4>
            <p class="text-[11px] text-emerald-100/60 leading-relaxed">Nilai akhir dihitung otomatis berdasarkan bobot tiap aspek. Skala poin 1–100.</p>
            <p class="text-[10px] text-emerald-300/50 mt-2 italic">* Jika tidak mencapai Poin 1, dianggap 0.</p>
        </div>
    </div>

    {{-- Konten Aspek --}}
    <div class="flex-1 bg-white border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden">

        {{-- Header Tab --}}
        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between bg-surface-container-low">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-lg">{{ $tab['icon'] }}</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-sm">{{ strtoupper($tab['label']) }}</h3>
                    <p class="text-[11px] text-on-surface-variant">Bobot {{ $tab['bobot'] }} · Dinilai oleh {{ $tab['penilai'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-24 h-1.5 bg-surface-container-highest rounded-full overflow-hidden">
                    @php $w = (int) $tab['bobot']; @endphp
                    <div class="h-full bg-primary rounded-full" style="width: {{ $w }}%"></div>
                </div>
                <span class="text-xs font-bold text-primary">{{ $tab['bobot'] }}</span>
            </div>
        </div>

        {{-- Daftar Aspek --}}
        <div class="p-6 space-y-5 overflow-y-auto max-h-[calc(100vh-380px)]">
            @foreach($tab['aspek'] as $aspek)
            <div class="border border-outline-variant/20 rounded-2xl overflow-hidden">
                {{-- Aspek Header --}}
                <div class="px-5 py-4 bg-surface-container-low flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-container flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-primary text-lg">{{ $aspek['icon'] }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-on-surface text-sm">{{ $aspek['kode'] }}. {{ $aspek['nama'] }}</h4>
                        <p class="text-xs text-on-surface-variant">Bobot: <span class="font-bold text-primary">{{ $aspek['bobot'] }}</span></p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-primary-container text-primary font-semibold">
                        {{ count($aspek['poin']) }} Level
                    </span>
                </div>

                {{-- Poin Grid --}}
                <div class="grid grid-cols-3 gap-0 divide-x divide-outline-variant/20">
                    @foreach($aspek['poin'] as $p)
                        @php
                            $colors = [
                                'red'     => ['bg' => 'bg-red-50',     'border' => 'border-red-100',     'badge_bg' => 'bg-red-100',     'badge_text' => 'text-red-700',     'label_text' => 'text-red-600'],
                                'amber'   => ['bg' => 'bg-amber-50',   'border' => 'border-amber-100',   'badge_bg' => 'bg-amber-100',   'badge_text' => 'text-amber-700',   'label_text' => 'text-amber-600'],
                                'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'badge_bg' => 'bg-emerald-100', 'badge_text' => 'text-emerald-700', 'label_text' => 'text-emerald-600'],
                            ];
                            $c = $colors[$p['color']];
                        @endphp
                        <div class="p-4 {{ $c['bg'] }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold {{ $c['label_text'] }} uppercase tracking-wider">{{ $p['range'] }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $c['badge_bg'] }} {{ $c['badge_text'] }} font-bold">
                                    {{ $p['level'] }}
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">{{ $p['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 bg-surface-container-low border-t border-outline-variant/20 flex items-center justify-between">
            <p class="text-[11px] text-outline font-medium">
                {{ count($tab['aspek']) }} aspek penilaian · Total bobot kategori: <span class="font-bold text-primary">{{ $tab['bobot'] }}</span>
            </p>
            <p class="text-[10px] text-outline italic">Dinilai oleh: {{ $tab['penilai'] }}</p>
        </div>
    </div>
</div>

@endsection