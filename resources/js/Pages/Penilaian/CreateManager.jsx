import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const SEMUA_ASPEK = {
    ls_critical_thinking: {
        label: 'Critical Thinking',
        kategori: 'Learning Skills',
        bobot: 5,
        opsi: {
            10: 'Menganalisis permasalahan secara dangkal.',
            16: 'Tidak melakukan evaluasi terhadap informasi yang diterima.',
            23: 'Menggunakan ide yang sudah ada tanpa mengevaluasi.',
            29: 'Menerima masukan tanpa ada pertimbangan.',
            36: 'Tidak mampu memberikan alasan yang valid untuk mempertahankan pilihan.',
            42: 'Mengidentifikasi aspek permasalahan utama tetapi tidak mempertimbangkan kerumitan.',
            49: 'Melakukan evaluasi terhadap informasi yang diterima.',
            55: 'Menggunakan ide yang sudah ada dengan mengevaluasi terlebih dahulu (tidak rinci).',
            61: 'Menerima masukan dengan sedikit pertimbangan.',
            68: 'Mulai mampu memberikan alasan untuk mempertahankan pilihan.',
            74: 'Mengidentifikasi aspek permasalahan utama dan mempertimbangkan kerumitan yang ada.',
            81: 'Melakukan evaluasi terhadap informasi yang diterima secara detail.',
            87: 'Menggunakan ide dengan mengevaluasi dan menyesuaikan apakah mungkin diterapkan.',
            94: 'Menerima masukan dengan melakukan pertimbangan terlebih dahulu.',
            100: 'Dapat memberikan alasan yang valid untuk mempertahankan pilihan yang dibuat.',
        },
    },
    ls_kolaborasi: {
        label: 'Kolaborasi',
        kategori: 'Learning Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak bertanggung jawab terhadap tugas masing-masing.',
            18: 'Tidak menyelesaikan tugas tepat waktu.',
            26: 'Tidak mempertimbangkan pendapat orang lain.',
            35: 'Tidak melimpahkan tugas kepada orang lain.',
            43: 'Bertanggung jawab terhadap tugas masing-masing.',
            51: 'Berusaha menyelesaikan tugas tepat waktu walaupun akhirnya tidak tepat waktu.',
            59: 'Mempertimbangkan masukan orang lain.',
            67: 'Melimpahkan tugas kepada orang lain.',
            75: 'Bertanggung jawab penuh terhadap tugas masing-masing.',
            84: 'Menyelesaikan tugas tepat waktu.',
            92: 'Mempertimbangkan masukan orang lain secara aktif.',
            100: 'Melimpahkan tugas kepada orang lain secara efektif.',
        },
    },
    ls_kreativitas: {
        label: 'Kreativitas & Inovasi',
        kategori: 'Learning Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak mengetahui tujuan dari proyek.',
            15: 'Tidak mempertimbangkan kebutuhan user.',
            21: 'Tidak mengetahui tantangan dalam proyek.',
            26: 'Hanya mengikuti arahan saja.',
            31: 'Tidak memberikan ide baru untuk penyelesaian masalah.',
            36: 'Tidak mampu mengidentifikasi kebutuhan proyek.',
            42: 'Mengetahui secara umum tujuan dari proyek.',
            47: 'Mempertimbangkan kebutuhan user.',
            52: 'Mengetahui sebagian dari tantangan proyek.',
            58: 'Hanya mengikuti arahan yang sudah ada.',
            63: 'Tidak memberikan ide baru untuk penyelesaian masalah (cukup).',
            68: 'Tidak mampu mengidentifikasi kebutuhan proyek (cukup).',
            74: 'Mengetahui tujuan dari proyek dengan baik.',
            79: 'Mempertimbangkan kebutuhan user secara menyeluruh.',
            84: 'Mengetahui semua tantangan proyek.',
            89: 'Mampu memberikan alternatif solusi dalam pemecahan masalah.',
            95: 'Memberikan ide baru untuk penyelesaian masalah.',
            100: 'Mampu mengidentifikasi kebutuhan proyek secara lengkap.',
        },
    },
    ls_komunikasi: {
        label: 'Komunikasi',
        kategori: 'Learning Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak bisa berkomunikasi dengan anggota tim (lebih banyak diam).',
            21: 'Tidak bisa menyampaikan ide atau pendapat kepada tim.',
            32: 'Menggunakan kata-kata yang tidak sopan dalam berkomunikasi.',
            44: 'Mampu berkomunikasi dengan anggota tim.',
            55: 'Mampu menyampaikan ide kepada tim.',
            66: 'Seringkali menggunakan kata-kata yang tidak sopan.',
            78: 'Mampu berkomunikasi dengan baik dengan anggota tim.',
            89: 'Mampu menyampaikan ide kepada tim dengan jelas.',
            100: 'Dalam berkomunikasi tidak pernah menggunakan kata-kata yang tidak sopan.',
        },
    },
    lf_fleksibilitas: {
        label: 'Fleksibilitas',
        kategori: 'Life Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak mampu mencari jalan keluar ketika ada masalah.',
            28: 'Tidak mampu beradaptasi jika strategi yang dirancang tidak sesuai dengan implementasi.',
            46: 'Berusaha mencari jalan keluar walaupun kadang belum sesuai.',
            64: 'Mampu beradaptasi dengan strategi baru walaupun dengan arahan dan bimbingan.',
            82: 'Mampu mencari jalan keluar ketika ditemukan masalah.',
            100: 'Mampu beradaptasi dengan strategi baru tanpa harus dibimbing secara keseluruhan.',
        },
    },
    lf_kepemimpinan: {
        label: 'Kepemimpinan',
        kategori: 'Life Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak menjadi ketua kelompok.',
            23: 'Tidak bisa menerima pendapat orang lain.',
            36: 'Tidak menjadi ketua kelompok dan kurang inisiatif.',
            49: 'Mampu menghargai pendapat orang lain.',
            61: 'Dapat menentukan strategi dalam penyelesaian proyek.',
            74: 'Menjadi ketua kelompok.',
            87: 'Bisa menampung dan menghargai pendapat semua anggota dalam tim.',
            100: 'Dapat menentukan strategi dalam penyelesaian proyek secara efektif.',
        },
    },
    lf_produktivitas: {
        label: 'Produktivitas',
        kategori: 'Life Skills',
        bobot: 5,
        opsi: {
            10: 'Waktu lebih banyak digunakan untuk hal-hal yang tidak penting.',
            28: 'Hasil dari setiap tahapan tidak sesuai dengan yang direncanakan.',
            46: 'Berusaha memanfaatkan waktu dengan sebaik-baiknya.',
            64: 'Terdapat beberapa output pada tahapan yang selesai melebihi waktu.',
            82: 'Waktu dimanfaatkan sebaik-baiknya sehingga output sesuai perencanaan.',
            100: 'Terdapat beberapa output yang selesai sebelum waktunya.',
        },
    },
    lf_social_skill: {
        label: 'Social Skill',
        kategori: 'Life Skills',
        bobot: 5,
        opsi: {
            10: 'Tidak mampu berkomunikasi dengan teman dalam tim.',
            55: 'Cukup mampu berkomunikasi dengan teman dalam tim.',
            100: 'Mampu berkomunikasi dengan baik dengan teman dalam tim maupun tim lain.',
        },
    },
    lp_rpp: {
        label: 'RPP',
        kategori: 'Laporan Project',
        bobot: 5,
        opsi: {
            10: 'RPP tidak lengkap, tidak mencakup komponen dasar, dan tidak sesuai dengan proyek.',
            40: 'RPP mencakup komponen dasar namun kurang rinci; beberapa bagian belum sesuai konteks proyek.',
            70: 'RPP cukup lengkap dan sesuai proyek, namun masih ada bagian yang perlu disempurnakan.',
            100: 'RPP lengkap, rinci, dan sepenuhnya sesuai proyek dengan semua komponen secara komprehensif.',
        },
    },
    lp_logbook: {
        label: 'Logbook Mingguan',
        kategori: 'Laporan Project',
        bobot: 5,
        opsi: {
            10: 'Tidak diisi atau <25% pertemuan; catatan tidak informatif.',
            40: 'Diisi 26-50% pertemuan; catatan singkat, kurang menggambarkan aktivitas.',
            70: 'Diisi 51-75% pertemuan; cukup informatif meski belum konsisten.',
            100: 'Diisi konsisten setiap minggu (>75%); lengkap, mencatat aktivitas, kendala, dan solusi.',
        },
    },
    lp_dokumen_projek: {
        label: 'Dokumen Projek',
        kategori: 'Laporan Project',
        bobot: 5,
        opsi: {
            10: 'Tidak lengkap; tidak ada latar belakang, tujuan, metodologi, atau hasil.',
            40: 'Mencakup beberapa bagian utama namun penjelasan dangkal.',
            70: 'Mencakup sebagian besar komponen dengan penjelasan yang cukup jelas.',
            100: 'Lengkap, terstruktur dengan baik, semua komponen rinci dan mudah dipahami.',
        },
    },
};

const IKON_MAP = {
    'Learning Skills': 'lightbulb',
    'Life Skills': 'self_improvement',
    'Laporan Project': 'folder_open',
};

const PER_KATEGORI = {};
Object.entries(SEMUA_ASPEK).forEach(([fieldName, aspek]) => {
    if (!PER_KATEGORI[aspek.kategori]) PER_KATEGORI[aspek.kategori] = {};
    PER_KATEGORI[aspek.kategori][fieldName] = aspek;
});

const SEMUA_FIELD = Object.keys(SEMUA_ASPEK);

function strLimit(str, limit = 85) {
    if (!str) return '';
    return str.length > limit ? str.slice(0, limit) + '...' : str;
}

const CSS_STYLES = `
/* ── Level Columns ── */
.level-col {
    flex: 1;
    border-radius: 14px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    min-width: 0;
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
    padding: 10px 12px;
    border-radius: 11px 11px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.level-col .lc-body { padding: 10px 12px 12px; }
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
.kat-progress-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#004d4d,#10b981); transition:width 0.3s ease; }

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
`;

export default function PenilaianCreateManager({ proyekList = [] }) {
    const { errors = {} } = usePage().props;
    const allErrors = Object.values(errors);

    const [proyekId, setProyekId] = useState('');
    const [mahasiswaId, setMahasiswaId] = useState('');
    const [catatanManager, setCatatanManager] = useState('');
    const [activeLevel, setActiveLevel] = useState({});
    const [nilaiTerpilih, setNilaiTerpilih] = useState({});
    const [levelTerpilih, setLevelTerpilih] = useState({});

    const selectedProyek = proyekList.find((p) => String(p.id) === String(proyekId));
    const mahasiswaOptions = selectedProyek?.mahasiswa || [];

    const selectLevel = (fieldName, level) => {
        setActiveLevel((prev) => ({
            ...prev,
            [fieldName]: prev[fieldName] === level ? null : level,
        }));
    };

    const pilihSubPoin = (e, fieldName, poin, level) => {
        e.stopPropagation();
        setNilaiTerpilih((prev) => ({ ...prev, [fieldName]: Number(poin) }));
        setLevelTerpilih((prev) => ({ ...prev, [fieldName]: level }));
    };

    const getKategoriPercentage = (aspekList) => {
        let total = 0;
        let bobotTotal = 0;
        Object.entries(aspekList).forEach(([field, aspek]) => {
            bobotTotal += aspek.bobot;
            if (nilaiTerpilih[field] !== undefined) {
                total += nilaiTerpilih[field] * (aspek.bobot / 100);
            }
        });
        return bobotTotal > 0 ? Math.round((total / bobotTotal) * 100) : 0;
    };

    let grandTotalNum = 0;
    SEMUA_FIELD.forEach((field) => {
        if (nilaiTerpilih[field] !== undefined) {
            grandTotalNum += nilaiTerpilih[field] * (SEMUA_ASPEK[field].bobot / 100);
        }
    });
    const grandTotal = grandTotalNum.toFixed(2);
    const grandTotalPer100 = grandTotalNum > 0 ? ((grandTotalNum / 55) * 100).toFixed(2) : '0.00';
    const belumCount = SEMUA_FIELD.filter((f) => nilaiTerpilih[f] === undefined).length;
    const anySelected = Object.keys(nilaiTerpilih).length > 0;

    const handleSubmit = (e) => {
        e.preventDefault();
        if (belumCount > 0) {
            window.alert(`${belumCount} aspek belum dipilih. Harap pilih semua aspek terlebih dahulu.`);
            return;
        }
        router.post('/penilaian/manager', {
            pengajuan_proyek_id: proyekId,
            mahasiswa_id: mahasiswaId,
            ...nilaiTerpilih,
            catatan_manager: catatanManager,
        });
    };

    const badgeColors = {
        kurang: ['#fee2e2', '#dc2626'],
        cukup: ['#fef3c7', '#d97706'],
        baik: ['#dcfce7', '#16a34a'],
    };
    const badgeLabels = { kurang: 'Kurang', cukup: 'Cukup', baik: 'Baik' };

    return (
        <AppLayout title="Input Penilaian Manager">
            <style>{CSS_STYLES}</style>

            <div className="max-w-4xl mx-auto">
                <div className="mb-6">
                    <h1 className="text-2xl font-black text-on-surface tracking-tight">Input Penilaian</h1>
                    <p className="text-sm text-on-surface-variant mt-0.5">Manager Proyek — bobot 55%</p>
                </div>

                {allErrors.length > 0 && (
                    <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
                        <ul className="list-disc list-inside space-y-1">
                            {allErrors.map((e, i) => (
                                <li key={i}>{e}</li>
                            ))}
                        </ul>
                    </div>
                )}

                {/* LEGENDA */}
                <div className="flex items-center gap-2 mb-6 flex-wrap">
                    <span className="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mr-1">
                        Level:
                    </span>
                    <span
                        className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                        style={{ background: '#fee2e2', color: '#dc2626' }}
                    >
                        <span className="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Kurang &middot;
                        10–42
                    </span>
                    <span
                        className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                        style={{ background: '#fef3c7', color: '#d97706' }}
                    >
                        <span className="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Cukup &middot;
                        43–74
                    </span>
                    <span
                        className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                        style={{ background: '#dcfce7', color: '#16a34a' }}
                    >
                        <span className="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Baik &middot;
                        75–100
                    </span>
                    <span className="text-[11px] text-on-surface-variant ml-2">
                        ← Klik level, lalu pilih poin spesifik
                    </span>
                </div>

                <form onSubmit={handleSubmit} id="formPenilaian">
                    {/* PILIH PROYEK & MAHASISWA */}
                    <div className="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
                        <h2 className="text-sm font-black text-on-surface uppercase tracking-widest mb-5">
                            Pilih Mahasiswa
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-bold text-on-surface-variant mb-2">
                                    Proyek
                                </label>
                                <select
                                    name="pengajuan_proyek_id"
                                    id="proyekSelect"
                                    required
                                    value={proyekId}
                                    onChange={(e) => {
                                        setProyekId(e.target.value);
                                        setMahasiswaId('');
                                    }}
                                    className="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm font-medium text-on-surface bg-surface-container-low focus:outline-none focus:border-primary"
                                >
                                    <option value="">-- Pilih Proyek --</option>
                                    {proyekList.map((proyek) => (
                                        <option key={proyek.id} value={proyek.id}>
                                            {proyek.judul_proyek}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-on-surface-variant mb-2">
                                    Mahasiswa
                                </label>
                                <select
                                    name="mahasiswa_id"
                                    id="mahasiswaSelect"
                                    required
                                    value={mahasiswaId}
                                    onChange={(e) => setMahasiswaId(e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm font-medium text-on-surface bg-surface-container-low focus:outline-none focus:border-primary"
                                >
                                    <option value="">
                                        {proyekId ? '-- Pilih Mahasiswa --' : '-- Pilih Proyek Dulu --'}
                                    </option>
                                    {mahasiswaOptions.map((m) => (
                                        <option key={m.id} value={m.id}>
                                            {m.nama} ({m.nim})
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* ASPEK PER KATEGORI */}
                    {Object.entries(PER_KATEGORI).map(([namaKategori, aspekList]) => {
                        const ikon = IKON_MAP[namaKategori] ?? 'star';
                        const slugKat = namaKategori.toLowerCase().replace(/ /g, '-');
                        const pct = getKategoriPercentage(aspekList);

                        return (
                            <div
                                key={namaKategori}
                                className="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm mb-6 overflow-hidden"
                            >
                                {/* Header Kategori */}
                                <div
                                    className="flex items-center justify-between px-7 py-5 border-b border-outline-variant/10"
                                    style={{ background: 'linear-gradient(135deg,#f0fdf4,#ffffff)' }}
                                >
                                    <div className="flex items-center gap-3">
                                        <div
                                            className="w-10 h-10 rounded-xl flex items-center justify-center"
                                            style={{ background: '#004d4d' }}
                                        >
                                            <span
                                                className="material-symbols-outlined text-lg"
                                                style={{ color: '#7fffd4' }}
                                            >
                                                {ikon}
                                            </span>
                                        </div>
                                        <div>
                                            <p className="font-black text-on-surface text-sm">
                                                {namaKategori}
                                            </p>
                                            <p className="text-[10px] text-on-surface-variant">
                                                {Object.keys(aspekList).length} aspek
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <div className="kat-progress-bar">
                                            <div
                                                className="kat-progress-fill"
                                                id={`bar-${slugKat}`}
                                                style={{ width: `${pct}%` }}
                                            />
                                        </div>
                                        <span
                                            className="text-xl font-black text-primary min-w-[48px] text-right"
                                            id={`total-${slugKat}`}
                                        >
                                            {pct}%
                                        </span>
                                    </div>
                                </div>

                                {/* Aspek-aspek */}
                                {Object.entries(aspekList).map(([fieldName, aspek]) => {
                                    const entries = Object.entries(aspek.opsi).map(([k, v]) => [
                                        Number(k),
                                        v,
                                    ]);
                                    const opsiKurang = entries.filter(([p]) => p <= 42);
                                    const opsiCukup = entries.filter(([p]) => p >= 43 && p <= 74);
                                    const opsiBaik = entries.filter(([p]) => p >= 75);

                                    const descKurang = opsiKurang.length
                                        ? opsiKurang
                                              .slice(0, 2)
                                              .map(([, d]) => d)
                                              .join(' ')
                                        : '-';
                                    const descCukup = opsiCukup.length
                                        ? opsiCukup
                                              .slice(0, 2)
                                              .map(([, d]) => d)
                                              .join(' ')
                                        : '-';
                                    const descBaik = opsiBaik.length
                                        ? opsiBaik
                                              .slice(0, 2)
                                              .map(([, d]) => d)
                                              .join(' ')
                                        : '-';

                                    const chosenVal = nilaiTerpilih[fieldName];
                                    const chosenLv = levelTerpilih[fieldName];
                                    const openLv = activeLevel[fieldName];

                                    return (
                                        <div key={fieldName} className="aspek-row">
                                            {/* Judul aspek + hasil pilih */}
                                            <div className="flex items-center justify-between mb-4">
                                                <div>
                                                    <p className="font-black text-on-surface text-sm">
                                                        {aspek.label}
                                                    </p>
                                                    <p className="text-[10px] text-on-surface-variant">
                                                        Bobot {aspek.bobot}%
                                                    </p>
                                                </div>
                                                <span
                                                    className={`nilai-result ${
                                                        chosenVal !== undefined ? 'show' : ''
                                                    }`}
                                                    id={`result-badge-${fieldName}`}
                                                    style={
                                                        chosenLv
                                                            ? {
                                                                  background: badgeColors[chosenLv][0],
                                                                  color: badgeColors[chosenLv][1],
                                                              }
                                                            : undefined
                                                    }
                                                >
                                                    {chosenVal !== undefined && chosenLv && (
                                                        <>
                                                            <span
                                                                style={{
                                                                    fontSize: '15px',
                                                                    fontWeight: 900,
                                                                }}
                                                            >
                                                                {chosenVal}
                                                            </span>
                                                            <span
                                                                style={{
                                                                    fontSize: '10px',
                                                                    fontWeight: 700,
                                                                    opacity: 0.75,
                                                                }}
                                                            >
                                                                {badgeLabels[chosenLv]}
                                                            </span>
                                                        </>
                                                    )}
                                                </span>
                                            </div>

                                            <input
                                                type="hidden"
                                                name={fieldName}
                                                id={`val-${fieldName}`}
                                                value={chosenVal ?? ''}
                                            />

                                            {/* 3 KOLOM LEVEL */}
                                            <div className="flex gap-3">
                                                {/* KURANG */}
                                                {opsiKurang.length > 0 && (
                                                    <div
                                                        className={`level-col kurang ${
                                                            openLv === 'kurang' ? 'active' : ''
                                                        }`}
                                                        id={`col-${fieldName}-kurang`}
                                                        onClick={() => selectLevel(fieldName, 'kurang')}
                                                    >
                                                        <div className="lc-header">
                                                            <div>
                                                                <div className="lc-title">Kurang</div>
                                                                <div className="lc-range">Poin 10–42</div>
                                                            </div>
                                                            <div className="lc-check">
                                                                <svg
                                                                    width="10"
                                                                    height="10"
                                                                    viewBox="0 0 12 12"
                                                                    fill="none"
                                                                >
                                                                    <path
                                                                        d="M2 6l3 3 5-5"
                                                                        stroke="white"
                                                                        strokeWidth="2.2"
                                                                        strokeLinecap="round"
                                                                        strokeLinejoin="round"
                                                                    />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div className="lc-body">
                                                            <p className="lc-desc">
                                                                {strLimit(descKurang, 85)}
                                                            </p>
                                                            <div
                                                                className="subpoin-wrap"
                                                                id={`sub-${fieldName}-kurang`}
                                                            >
                                                                {opsiKurang.map(([poin, desc]) => (
                                                                    <button
                                                                        key={poin}
                                                                        type="button"
                                                                        className={`subpoin-btn ${
                                                                            chosenVal === poin
                                                                                ? 'selected'
                                                                                : ''
                                                                        }`}
                                                                        id={`sp-${fieldName}-${poin}`}
                                                                        onClick={(e) =>
                                                                            pilihSubPoin(
                                                                                e,
                                                                                fieldName,
                                                                                poin,
                                                                                'kurang',
                                                                            )
                                                                        }
                                                                    >
                                                                        <span className="sp-poin">
                                                                            {poin}
                                                                        </span>
                                                                        <span>{desc}</span>
                                                                    </button>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* CUKUP */}
                                                {opsiCukup.length > 0 && (
                                                    <div
                                                        className={`level-col cukup ${
                                                            openLv === 'cukup' ? 'active' : ''
                                                        }`}
                                                        id={`col-${fieldName}-cukup`}
                                                        onClick={() => selectLevel(fieldName, 'cukup')}
                                                    >
                                                        <div className="lc-header">
                                                            <div>
                                                                <div className="lc-title">Cukup</div>
                                                                <div className="lc-range">Poin 43–74</div>
                                                            </div>
                                                            <div className="lc-check">
                                                                <svg
                                                                    width="10"
                                                                    height="10"
                                                                    viewBox="0 0 12 12"
                                                                    fill="none"
                                                                >
                                                                    <path
                                                                        d="M2 6l3 3 5-5"
                                                                        stroke="white"
                                                                        strokeWidth="2.2"
                                                                        strokeLinecap="round"
                                                                        strokeLinejoin="round"
                                                                    />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div className="lc-body">
                                                            <p className="lc-desc">
                                                                {strLimit(descCukup, 85)}
                                                            </p>
                                                            <div
                                                                className="subpoin-wrap"
                                                                id={`sub-${fieldName}-cukup`}
                                                            >
                                                                {opsiCukup.map(([poin, desc]) => (
                                                                    <button
                                                                        key={poin}
                                                                        type="button"
                                                                        className={`subpoin-btn ${
                                                                            chosenVal === poin
                                                                                ? 'selected'
                                                                                : ''
                                                                        }`}
                                                                        id={`sp-${fieldName}-${poin}`}
                                                                        onClick={(e) =>
                                                                            pilihSubPoin(
                                                                                e,
                                                                                fieldName,
                                                                                poin,
                                                                                'cukup',
                                                                            )
                                                                        }
                                                                    >
                                                                        <span className="sp-poin">
                                                                            {poin}
                                                                        </span>
                                                                        <span>{desc}</span>
                                                                    </button>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* BAIK */}
                                                {opsiBaik.length > 0 && (
                                                    <div
                                                        className={`level-col baik ${
                                                            openLv === 'baik' ? 'active' : ''
                                                        }`}
                                                        id={`col-${fieldName}-baik`}
                                                        onClick={() => selectLevel(fieldName, 'baik')}
                                                    >
                                                        <div className="lc-header">
                                                            <div>
                                                                <div className="lc-title">Baik</div>
                                                                <div className="lc-range">Poin 75–100</div>
                                                            </div>
                                                            <div className="lc-check">
                                                                <svg
                                                                    width="10"
                                                                    height="10"
                                                                    viewBox="0 0 12 12"
                                                                    fill="none"
                                                                >
                                                                    <path
                                                                        d="M2 6l3 3 5-5"
                                                                        stroke="white"
                                                                        strokeWidth="2.2"
                                                                        strokeLinecap="round"
                                                                        strokeLinejoin="round"
                                                                    />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div className="lc-body">
                                                            <p className="lc-desc">
                                                                {strLimit(descBaik, 85)}
                                                            </p>
                                                            <div
                                                                className="subpoin-wrap"
                                                                id={`sub-${fieldName}-baik`}
                                                            >
                                                                {opsiBaik.map(([poin, desc]) => (
                                                                    <button
                                                                        key={poin}
                                                                        type="button"
                                                                        className={`subpoin-btn ${
                                                                            chosenVal === poin
                                                                                ? 'selected'
                                                                                : ''
                                                                        }`}
                                                                        id={`sp-${fieldName}-${poin}`}
                                                                        onClick={(e) =>
                                                                            pilihSubPoin(
                                                                                e,
                                                                                fieldName,
                                                                                poin,
                                                                                'baik',
                                                                            )
                                                                        }
                                                                    >
                                                                        <span className="sp-poin">
                                                                            {poin}
                                                                        </span>
                                                                        <span>{desc}</span>
                                                                    </button>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        );
                    })}

                    {/* CATATAN */}
                    <div className="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-6">
                        <label className="block text-xs font-black text-on-surface uppercase tracking-widest mb-3">
                            Catatan Manager
                        </label>
                        <textarea
                            name="catatan_manager"
                            rows={3}
                            value={catatanManager}
                            onChange={(e) => setCatatanManager(e.target.value)}
                            placeholder="Catatan tambahan (opsional)..."
                            className="w-full px-4 py-3 rounded-xl border border-outline-variant/30 text-sm text-on-surface bg-surface-container-low focus:outline-none focus:border-primary resize-none"
                        />
                    </div>

                    {/* SUMMARY & SUBMIT */}
                    <div className="bg-white rounded-[1.5rem] border border-outline-variant/20 shadow-sm p-7 mb-8">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs font-black text-on-surface-variant uppercase tracking-widest mb-1">
                                    Estimasi Nilai Manager (55%)
                                </p>
                                <div className="flex items-end gap-3">
                                    <p className="text-4xl font-black text-primary" id="grandTotal">
                                        {grandTotal}
                                    </p>
                                    <div className="mb-1">
                                        <span className="text-[10px] text-gray-400 font-bold block">
                                            dari maks. 55
                                        </span>
                                        <span className="text-sm font-black text-gray-500">
                                            ≈ <span id="grandTotalPer100">{grandTotalPer100}</span> / 100
                                        </span>
                                    </div>
                                </div>
                                <div className="mt-2 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                                    <span className="material-symbols-outlined text-amber-500 text-sm shrink-0 mt-0.5">
                                        info
                                    </span>
                                    <p className="text-[10px] text-amber-700 leading-relaxed">
                                        Nilai ini adalah <strong>kontribusi 55%</strong> dari nilai akhir
                                        mahasiswa. Nilai Akhir = Nilai Manager + Nilai Dosen Pengampu (total
                                        maks. 100).
                                    </p>
                                </div>
                                <p
                                    className="text-[10px] mt-2"
                                    id="infoAspekBelum"
                                    style={{ color: belumCount === 0 ? '#004d4d' : '#9ca3af' }}
                                >
                                    {!anySelected
                                        ? 'Semua aspek harus dipilih sebelum menyimpan.'
                                        : belumCount === 0
                                          ? '✓ Semua aspek sudah dipilih.'
                                          : `${belumCount} aspek belum dipilih.`}
                                </p>
                            </div>
                            <button
                                type="submit"
                                className="flex items-center gap-2 px-8 py-4 rounded-2xl text-sm font-black text-white shadow-xl transition-all hover:scale-105"
                                style={{ background: 'linear-gradient(135deg,#004d4d,#008080)' }}
                            >
                                <span className="material-symbols-outlined text-lg">save</span>
                                Simpan Penilaian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
