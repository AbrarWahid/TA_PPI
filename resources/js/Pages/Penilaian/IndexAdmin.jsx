import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';

function getLearningSkills(pm) {
    if (!pm) return null;
    if (pm.learning_skills !== undefined && pm.learning_skills !== null) return Number(pm.learning_skills);
    if (pm.ls_critical_thinking === null || pm.ls_critical_thinking === undefined) return null;
    return Number(
        (
            (Number(pm.ls_critical_thinking) +
                Number(pm.ls_kolaborasi) +
                Number(pm.ls_kreativitas) +
                Number(pm.ls_komunikasi)) /
            4
        ).toFixed(1),
    );
}

function getLifeSkills(pm) {
    if (!pm) return null;
    if (pm.life_skills !== undefined && pm.life_skills !== null) return Number(pm.life_skills);
    if (pm.lf_fleksibilitas === null || pm.lf_fleksibilitas === undefined) return null;
    return Number(
        (
            (Number(pm.lf_fleksibilitas) +
                Number(pm.lf_kepemimpinan) +
                Number(pm.lf_produktivitas) +
                Number(pm.lf_social_skill)) /
            4
        ).toFixed(1),
    );
}

function getLaporanProject(pm) {
    if (!pm) return null;
    if (pm.laporan_project !== undefined && pm.laporan_project !== null) return Number(pm.laporan_project);
    if (pm.lp_rpp === null || pm.lp_rpp === undefined) return null;
    return Number(
        ((Number(pm.lp_rpp) + Number(pm.lp_logbook) + Number(pm.lp_dokumen_projek)) / 3).toFixed(1),
    );
}

function getLiteracySkills(pd) {
    if (!pd) return null;
    if (pd.literacy_skills !== undefined && pd.literacy_skills !== null) return Number(pd.literacy_skills);
    if (pd.lit_informasi === null || pd.lit_informasi === undefined) return null;
    return Number(
        ((Number(pd.lit_informasi) + Number(pd.lit_media) + Number(pd.lit_teknologi)) / 3).toFixed(1),
    );
}

function getPresentasi(pd) {
    if (!pd) return null;
    if (pd.presentasi !== undefined && pd.presentasi !== null) return Number(pd.presentasi);
    if (pd.pr_konten === null || pd.pr_konten === undefined) return null;
    return Number(
        (
            (Number(pd.pr_konten) +
                Number(pd.pr_visual) +
                Number(pd.pr_kosakata) +
                Number(pd.pr_tanya_jawab) +
                Number(pd.pr_mata_gerak)) /
            5
        ).toFixed(1),
    );
}

function getLaporanAkhir(pd) {
    if (!pd) return null;
    if (pd.laporan_akhir !== undefined && pd.laporan_akhir !== null) return Number(pd.laporan_akhir);
    if (pd.la_penulisan === null || pd.la_penulisan === undefined) return null;
    return Number(
        ((Number(pd.la_penulisan) + Number(pd.la_pilihan_kata) + Number(pd.la_konten)) / 3).toFixed(1),
    );
}

function NilaiBox({ label, nilai, color }) {
    const fmt = nilai !== null && nilai !== undefined ? Number(nilai).toFixed(1) : '-';
    return (
        <div className="p-3 rounded-xl text-center bg-white border border-gray-100 shadow-sm transition-transform hover:-translate-y-0.5">
            <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">{label}</p>
            <p className="text-xl font-black" style={{ color }}>
                {fmt}
            </p>
        </div>
    );
}

export default function PenilaianIndexAdmin({ mahasiswaList = [], isKps = false, kelasList = [] }) {
    const [search, setSearch] = useState('');
    const [prodiFilter, setProdiFilter] = useState('semua');
    const [kelasFilter, setKelasFilter] = useState('semua');
    const [statusFilter, setStatusFilter] = useState('semua');
    const [modalId, setModalId] = useState(null);

    const processedList = useMemo(() => {
        return mahasiswaList.map((mhs) => {
            const pms = mhs.penilaian_manager || mhs.penilaianManager || [];
            const pm = pms[0] || null;
            const pds = mhs.penilaian_dosen || mhs.penilaianDosen || [];

            const nilaiM =
                pm && pm.nilai_manager !== null && pm.nilai_manager !== undefined
                    ? Number(pm.nilai_manager)
                    : null;

            const nilaiD =
                pds.length > 0
                    ? Number(
                          (
                              pds.reduce((acc, p) => acc + Number(p.nilai_dosen || 0), 0) / pds.length
                          ).toFixed(1),
                      )
                    : null;

            const avgNilaiDosen =
                pds.length > 0
                    ? Number(
                          (
                              pds.reduce((acc, p) => acc + Number(p.nilai_dosen || 0), 0) / pds.length
                          ).toFixed(2),
                      )
                    : null;

            const matkulList = Array.from(
                new Set(
                    pds
                        .map((p) => {
                            const sv = p.supervisi_matkul || p.supervisiMatkul;
                            const mk = sv?.mata_kuliah || sv?.mataKuliah;
                            return mk?.nama_matkul || null;
                        })
                        .filter(Boolean),
                ),
            );

            const nilaiAkhir =
                nilaiM !== null && nilaiD !== null
                    ? Number((nilaiM * 0.55 + nilaiD * 0.45).toFixed(1))
                    : null;

            let grade = null;
            if (nilaiAkhir !== null) {
                grade =
                    nilaiAkhir >= 85
                        ? 'A'
                        : nilaiAkhir >= 75
                          ? 'B'
                          : nilaiAkhir >= 65
                            ? 'C'
                            : nilaiAkhir >= 55
                              ? 'D'
                              : 'E';
            }

            const proyek =
                pm?.pengajuan_proyek?.judul_proyek ?? pm?.pengajuanProyek?.judul_proyek ?? null;

            const statusNilai =
                nilaiM !== null && nilaiD !== null
                    ? 'lengkap'
                    : nilaiM !== null || nilaiD !== null
                      ? 'sebagian'
                      : 'belum';

            const detail = {
                nama: mhs.nama,
                nim: mhs.nim,
                kelas: mhs.kelas ?? '-',
                manager: pm
                    ? {
                          proyek: proyek ?? '-',
                          learning_skills: getLearningSkills(pm),
                          life_skills: getLifeSkills(pm),
                          laporan_project: getLaporanProject(pm),
                          nilai_manager: nilaiM,
                          catatan: pm.catatan_manager ?? '',
                      }
                    : null,
                dosen_avg: avgNilaiDosen,
                dosen_list: pds.map((pd) => {
                    const sv = pd.supervisi_matkul || pd.supervisiMatkul;
                    const mk = sv?.mata_kuliah || sv?.mataKuliah;
                    return {
                        matkul: mk?.nama_matkul ?? '-',
                        literacy_skills: getLiteracySkills(pd),
                        presentasi: getPresentasi(pd),
                        laporan_akhir: getLaporanAkhir(pd),
                        nilai_dosen:
                            pd.nilai_dosen !== null && pd.nilai_dosen !== undefined
                                ? Number(pd.nilai_dosen)
                                : null,
                        catatan: pd.catatan_dosen ?? '',
                    };
                }),
            };

            return {
                mhs,
                pm,
                pds,
                nilaiM,
                nilaiD,
                matkulList,
                nilaiAkhir,
                grade,
                proyek,
                statusNilai,
                detail,
            };
        });
    }, [mahasiswaList]);

    const totalMhs = processedList.length;
    const sudahLengkap = processedList.filter(
        (r) => r.pm !== null && r.pds.length > 0,
    ).length;
    const belumLengkap = totalMhs - sudahLengkap;
    const pct = totalMhs > 0 ? Math.round((sudahLengkap / totalMhs) * 100) : 0;

    const filtered = useMemo(() => {
        const q = search.toLowerCase().trim();
        return processedList.filter((r) => {
            const name = (r.mhs.nama || '').toLowerCase();
            const nim = String(r.mhs.nim || '');
            const prodi = (r.mhs.user?.prodi || '').toLowerCase();
            const kelas = r.mhs.kelas || '';

            const matchSearch = !q || name.includes(q) || nim.includes(q);
            const matchStatus = statusFilter === 'semua' || r.statusNilai === statusFilter;
            const matchProdi = prodiFilter === 'semua' || prodi === prodiFilter;
            const matchKelas = kelasFilter === 'semua' || kelas === kelasFilter;

            return matchSearch && matchStatus && matchProdi && matchKelas;
        });
    }, [processedList, search, statusFilter, prodiFilter, kelasFilter]);

    const isFiltered =
        search.trim() !== '' ||
        statusFilter !== 'semua' ||
        prodiFilter !== 'semua' ||
        kelasFilter !== 'semua';

    const resetFilter = () => {
        setSearch('');
        setProdiFilter('semua');
        setKelasFilter('semua');
        setStatusFilter('semua');
    };

    const modalData = useMemo(() => {
        if (modalId === null) return null;
        return processedList.find((r) => r.mhs.id === modalId)?.detail || null;
    }, [modalId, processedList]);

    return (
        <AppLayout title="Rekap Penilaian">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Rekap <span className="text-[#2dce89]">Semua Penilaian</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        Rekapitulasi nilai akhir per mahasiswa
                    </p>
                </div>
            </div>

            {/* STAT CARDS */}
            <div className="grid grid-cols-3 gap-5 mb-8">
                <div
                    className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36"
                    style={{ background: '#004d4d' }}
                >
                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                        Total Mahasiswa
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-5xl font-black italic text-white tracking-tighter">
                            {totalMhs}
                        </p>
                        <span className="text-xs font-black text-[#7fffd4]/60">Terdaftar</span>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5"
                        style={{ fontSize: '100px' }}
                    >
                        group
                    </span>
                </div>

                <div
                    className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36"
                    style={{ background: '#2dce89' }}
                >
                    <p className="text-white/70 text-[10px] font-black uppercase tracking-widest">
                        Nilai Lengkap
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-5xl font-black italic text-white tracking-tighter">
                            {sudahLengkap}
                        </p>
                        <span className="text-xs font-black text-white/60">Mahasiswa</span>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/20"
                        style={{ fontSize: '100px' }}
                    >
                        task_alt
                    </span>
                </div>

                <div className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between bg-white border-2 border-outline-variant/20 h-36">
                    <p className="text-secondary text-[10px] font-black uppercase tracking-widest">
                        Belum Lengkap
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-5xl font-black italic text-primary tracking-tighter">
                            {belumLengkap}
                        </p>
                        <span className="text-xs font-black text-outline">Mahasiswa</span>
                    </div>
                    <div className="w-full bg-surface-container-highest h-1.5 rounded-full overflow-hidden mt-2">
                        <div
                            className="h-full rounded-full"
                            style={{
                                width: `${pct}%`,
                                background: 'linear-gradient(90deg,#7fffd4,#008080)',
                            }}
                        />
                    </div>
                </div>
            </div>

            {/* FILTER BAR */}
            <div className="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm px-7 py-5 mb-5">
                <div className="flex flex-wrap items-center gap-4">
                    {/* Search nama / NIM */}
                    <div className="relative w-64">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">
                            search
                        </span>
                        <input
                            type="text"
                            id="searchNilai"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nama atau NIM..."
                            className="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary transition-colors"
                        />
                    </div>

                    {/* Filter Prodi (Hanya Admin) */}
                    {!isKps && (
                        <select
                            id="filterProdi"
                            value={prodiFilter}
                            onChange={(e) => setProdiFilter(e.target.value)}
                            className="px-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-[#004d4d] focus:outline-none focus:border-[#2dce89] cursor-pointer"
                        >
                            <option value="semua">Semua Prodi</option>
                            <option value="informatika">TRIN - Informatika</option>
                            <option value="otomasi">TRO - Otomasi</option>
                            <option value="mekatronika">TRMO - Mekatronika</option>
                        </select>
                    )}

                    {/* Filter Kelas */}
                    <select
                        id="filterKelas"
                        value={kelasFilter}
                        onChange={(e) => setKelasFilter(e.target.value)}
                        className="px-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-[#004d4d] focus:outline-none focus:border-[#2dce89] cursor-pointer"
                    >
                        <option value="semua">Semua Kelas</option>
                        {kelasList.map((k) => (
                            <option key={k} value={k}>
                                {k}
                            </option>
                        ))}
                    </select>

                    {/* Divider */}
                    <div className="h-6 w-px bg-outline-variant/20 ml-2"></div>

                    {/* Filter Status Nilai */}
                    <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black text-outline uppercase tracking-widest mr-1">
                            Status:
                        </span>
                        {[
                            ['semua', 'Semua'],
                            ['lengkap', 'Nilai Lengkap'],
                            ['sebagian', 'Sebagian'],
                            ['belum', 'Belum Dinilai'],
                        ].map(([val, lbl]) => {
                            const isActive = statusFilter === val;
                            return (
                                <button
                                    key={val}
                                    type="button"
                                    onClick={() => setStatusFilter(val)}
                                    data-filter="status"
                                    data-value={val}
                                    className={`filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ${
                                        isActive
                                            ? 'bg-[#004d4d] text-[#7fffd4] border-[#004d4d]'
                                            : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-[#2dce89] hover:text-[#004d4d]'
                                    }`}
                                >
                                    {lbl}
                                    {val === 'belum' && belumLengkap > 0 && (
                                        <span className="ml-1 px-1.5 py-0.5 rounded-full bg-red-400 text-white text-[9px] font-black">
                                            {belumLengkap}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>

                    {/* Reset & Counter */}
                    <div className="ml-auto flex items-center gap-3">
                        {isFiltered && (
                            <>
                                <span
                                    id="filterCounter"
                                    className="text-[10px] font-black text-outline bg-gray-50 px-3 py-1.5 rounded-lg"
                                >
                                    <span id="visibleCount" className="text-[#004d4d]">
                                        {filtered.length}
                                    </span>{' '}
                                    ditemukan
                                </span>
                                <button
                                    type="button"
                                    onClick={resetFilter}
                                    id="btnReset"
                                    className="flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-all"
                                >
                                    <span className="material-symbols-outlined text-sm">
                                        filter_alt_off
                                    </span>{' '}
                                    Reset
                                </button>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* TABEL REKAP */}
            <div className="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-outline-variant/10 bg-surface-container-low/40">
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">
                                #
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Mahasiswa
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Proyek / Matkul
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Manager
                                <br />
                                <span className="normal-case font-medium text-[9px]">(55%)</span>
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Dosen
                                <br />
                                <span className="normal-case font-medium text-[9px]">(45%)</span>
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Nilai Akhir
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Grade
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-outline-variant/10" id="nilaiTbody">
                        {processedList.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-7 py-16 text-center">
                                    <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                                        group
                                    </span>
                                    <p className="text-on-surface-variant/40 font-black italic text-sm">
                                        Belum ada data mahasiswa.
                                    </p>
                                </td>
                            </tr>
                        ) : (
                            filtered.map((r, i) => {
                                const { mhs, nilaiM, nilaiD, matkulList, nilaiAkhir, grade, proyek } = r;

                                const colorAkhir =
                                    nilaiAkhir !== null
                                        ? nilaiAkhir >= 85
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : nilaiAkhir >= 75
                                              ? 'bg-blue-100 text-blue-700'
                                              : nilaiAkhir >= 65
                                                ? 'bg-yellow-100 text-yellow-700'
                                                : nilaiAkhir >= 55
                                                  ? 'bg-orange-100 text-orange-700'
                                                  : 'bg-red-100 text-red-700'
                                        : '';

                                const colorGrade =
                                    grade === 'A'
                                        ? 'bg-emerald-500'
                                        : grade === 'B'
                                          ? 'bg-blue-500'
                                          : grade === 'C'
                                            ? 'bg-yellow-500'
                                            : grade === 'D'
                                              ? 'bg-orange-500'
                                              : 'bg-red-500';

                                return (
                                    <tr
                                        key={mhs.id}
                                        className="nilai-row hover:bg-surface-container-lowest transition-colors"
                                    >
                                        <td className="px-7 py-5 text-[10px] font-black text-outline/40 row-num">
                                            {i + 1}
                                        </td>

                                        {/* Mahasiswa */}
                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center font-black text-xs text-primary shrink-0">
                                                    {(mhs.nama || '').substring(0, 2).toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="font-black text-on-surface text-sm">
                                                        {mhs.nama}
                                                    </p>
                                                    <p className="text-[10px] text-outline font-bold mt-0.5">
                                                        {mhs.nim} •{' '}
                                                        <span className="text-[#2dce89]">
                                                            {mhs.kelas ?? '-'}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {/* Proyek / Matkul */}
                                        <td className="px-7 py-5">
                                            {proyek && (
                                                <p className="text-sm font-medium text-on-surface-variant">
                                                    {proyek}
                                                </p>
                                            )}
                                            {matkulList.map((mk, idx) => (
                                                <p key={idx} className="text-[10px] text-outline mt-0.5">
                                                    {mk}
                                                </p>
                                            ))}
                                            {matkulList.length === 0 && !proyek && (
                                                <span className="text-outline/40 text-sm">-</span>
                                            )}
                                        </td>

                                        {/* Manager (55%) */}
                                        <td className="px-7 py-5 text-center">
                                            {nilaiM !== null ? (
                                                <span
                                                    className="font-black text-sm"
                                                    style={{ color: '#004d4d' }}
                                                >
                                                    {nilaiM.toFixed(1)}
                                                </span>
                                            ) : (
                                                <span className="text-outline/40 font-black text-sm">-</span>
                                            )}
                                        </td>

                                        {/* Dosen (45%) */}
                                        <td className="px-7 py-5 text-center">
                                            {nilaiD !== null ? (
                                                <span
                                                    className="font-black text-sm"
                                                    style={{ color: '#2dce89' }}
                                                >
                                                    {nilaiD.toFixed(1)}
                                                </span>
                                            ) : (
                                                <span className="text-outline/40 font-black text-sm">-</span>
                                            )}
                                        </td>

                                        {/* Nilai Akhir */}
                                        <td className="px-7 py-5 text-center">
                                            {nilaiAkhir !== null ? (
                                                <span
                                                    className={`inline-block px-4 py-1.5 rounded-xl text-sm font-black ${colorAkhir}`}
                                                >
                                                    {nilaiAkhir.toFixed(1)}
                                                </span>
                                            ) : (
                                                <span className="inline-block px-4 py-1.5 rounded-xl text-sm font-black bg-gray-100 text-gray-400">
                                                    -
                                                </span>
                                            )}
                                        </td>

                                        {/* Grade */}
                                        <td className="px-7 py-5 text-center">
                                            {grade ? (
                                                <span
                                                    className={`inline-flex w-9 h-9 rounded-xl items-center justify-center text-white font-black text-sm ${colorGrade}`}
                                                >
                                                    {grade}
                                                </span>
                                            ) : (
                                                <span className="text-outline/40 font-black text-sm">-</span>
                                            )}
                                        </td>

                                        {/* Tombol Detail */}
                                        <td className="px-7 py-5 text-center">
                                            <button
                                                type="button"
                                                onClick={() => setModalId(mhs.id)}
                                                className="px-4 py-2 rounded-xl text-xs font-black text-[#7fffd4] transition-all hover:scale-105 shadow-lg shadow-teal-900/20"
                                                style={{ background: '#004d4d' }}
                                            >
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>

                {/* Empty filter state */}
                {processedList.length > 0 && filtered.length === 0 && (
                    <div id="emptyFilter" className="px-7 py-16 text-center">
                        <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                            search_off
                        </span>
                        <p className="font-black italic text-on-surface-variant/40 text-sm">
                            Tidak ada mahasiswa ditemukan.
                        </p>
                        <button
                            type="button"
                            onClick={resetFilter}
                            className="mt-4 px-4 py-2 bg-gray-100 rounded-xl text-[10px] font-black text-[#004d4d] hover:bg-gray-200 transition-colors uppercase tracking-widest"
                        >
                            Reset Filter
                        </button>
                    </div>
                )}
            </div>

            {/* MODAL DETAIL */}
            {modalData && (
                <div
                    id="modalDetail"
                    onClick={(e) => {
                        if (e.target === e.currentTarget) setModalId(null);
                    }}
                    className="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style={{ background: 'rgba(0,0,0,0.5)', backdropFilter: 'blur(4px)' }}
                >
                    <div className="bg-white rounded-[2rem] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl relative">
                        <div className="p-7">
                            <div className="flex items-start justify-between mb-6 sticky top-0 bg-white z-10 pb-2">
                                <div>
                                    <h2 className="text-xl font-black text-[#004d4d] italic uppercase">
                                        Detail <span className="text-[#2dce89]">Penilaian</span>
                                    </h2>
                                    <p
                                        id="modalNama"
                                        className="text-xs text-outline font-bold mt-0.5 uppercase tracking-widest"
                                    >
                                        {modalData.nama}{' '}
                                        <span className="text-gray-400 font-medium">
                                            ({modalData.nim} • {modalData.kelas})
                                        </span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setModalId(null)}
                                    className="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-colors text-gray-500"
                                >
                                    <span className="material-symbols-outlined text-base">close</span>
                                </button>
                            </div>

                            <div id="modalContent">
                                {/* MANAGER */}
                                <div className="mb-5 p-5 rounded-2xl border border-outline-variant/20 shadow-sm">
                                    <div className="flex items-center gap-2 mb-4">
                                        <span
                                            className="material-symbols-outlined text-base"
                                            style={{ color: '#004d4d' }}
                                        >
                                            manage_accounts
                                        </span>
                                        <p className="font-black text-sm text-on-surface uppercase tracking-widest">
                                            Manager Proyek
                                        </p>
                                        <span
                                            className="ml-auto text-xs font-black px-3 py-1 rounded-xl"
                                            style={{ background: '#e6f7f2', color: '#004d4d' }}
                                        >
                                            Bobot 55%
                                        </span>
                                    </div>

                                    {modalData.manager ? (
                                        <>
                                            <p className="text-xs text-outline mb-3">
                                                Proyek:{' '}
                                                <span className="font-black text-[#004d4d]">
                                                    {modalData.manager.proyek}
                                                </span>
                                            </p>
                                            <div className="grid grid-cols-3 gap-3 mb-4">
                                                <NilaiBox
                                                    label="Learning Skills"
                                                    nilai={modalData.manager.learning_skills}
                                                    color="#004d4d"
                                                />
                                                <NilaiBox
                                                    label="Life Skills"
                                                    nilai={modalData.manager.life_skills}
                                                    color="#004d4d"
                                                />
                                                <NilaiBox
                                                    label="Lap. Project"
                                                    nilai={modalData.manager.laporan_project}
                                                    color="#004d4d"
                                                />
                                            </div>
                                            <div
                                                className="flex items-center justify-between p-4 rounded-2xl"
                                                style={{ background: '#004d4d' }}
                                            >
                                                <span className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                                                    Nilai Manager (skala 100)
                                                </span>
                                                <span className="text-white font-black text-xl">
                                                    {modalData.manager.nilai_manager !== null
                                                        ? Number(modalData.manager.nilai_manager).toFixed(1)
                                                        : '-'}
                                                </span>
                                            </div>
                                            {modalData.manager.catatan ? (
                                                <div className="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                    <p className="text-xs text-gray-500 italic">
                                                        &quot;{modalData.manager.catatan}&quot;
                                                    </p>
                                                </div>
                                            ) : null}
                                        </>
                                    ) : (
                                        <div className="py-6 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                            <span className="material-symbols-outlined text-3xl text-gray-300 mb-2">
                                                pending_actions
                                            </span>
                                            <p className="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                                Belum Dinilai
                                            </p>
                                        </div>
                                    )}
                                </div>

                                {/* DOSEN */}
                                <div className="mb-5 p-5 rounded-2xl border border-outline-variant/20 shadow-sm">
                                    <div className="flex items-center gap-2 mb-4">
                                        <span
                                            className="material-symbols-outlined text-base"
                                            style={{ color: '#2dce89' }}
                                        >
                                            school
                                        </span>
                                        <p className="font-black text-sm text-on-surface uppercase tracking-widest">
                                            Dosen Pengampu
                                        </p>
                                        <span className="ml-auto text-xs font-black px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700">
                                            Bobot 45%
                                        </span>
                                    </div>

                                    {modalData.dosen_list && modalData.dosen_list.length > 0 ? (
                                        <>
                                            {modalData.dosen_list.length > 1 && (
                                                <p className="text-[10px] font-black text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl mb-4 uppercase tracking-widest border border-emerald-100 flex items-center gap-1.5">
                                                    <span className="material-symbols-outlined text-sm">
                                                        functions
                                                    </span>{' '}
                                                    Rata-rata dari {modalData.dosen_list.length} mata kuliah
                                                </p>
                                            )}

                                            {modalData.dosen_list.map((pd, idx) => (
                                                <div
                                                    key={idx}
                                                    className={
                                                        idx < modalData.dosen_list.length - 1
                                                            ? 'mb-5 pb-5 border-b border-gray-100'
                                                            : ''
                                                    }
                                                >
                                                    <p className="text-xs text-outline mb-3">
                                                        Mata Kuliah:{' '}
                                                        <span className="font-black text-[#004d4d]">
                                                            {pd.matkul}
                                                        </span>
                                                    </p>
                                                    <div className="grid grid-cols-3 gap-3 mb-3">
                                                        <NilaiBox
                                                            label="Literacy Skills"
                                                            nilai={pd.literacy_skills}
                                                            color="#2dce89"
                                                        />
                                                        <NilaiBox
                                                            label="Presentasi"
                                                            nilai={pd.presentasi}
                                                            color="#2dce89"
                                                        />
                                                        <NilaiBox
                                                            label="Lap. Akhir"
                                                            nilai={pd.laporan_akhir}
                                                            color="#2dce89"
                                                        />
                                                    </div>
                                                    <div className="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                                        <span className="text-gray-500 text-[10px] font-black uppercase tracking-widest">
                                                            Sub-Nilai Matkul
                                                        </span>
                                                        <span className="text-[#004d4d] font-black text-lg">
                                                            {pd.nilai_dosen !== null
                                                                ? Number(pd.nilai_dosen).toFixed(1)
                                                                : '-'}
                                                        </span>
                                                    </div>
                                                    {pd.catatan ? (
                                                        <div className="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                            <p className="text-xs text-gray-500 italic">
                                                                &quot;{pd.catatan}&quot;
                                                            </p>
                                                        </div>
                                                    ) : null}
                                                </div>
                                            ))}

                                            {modalData.dosen_list.length > 1 &&
                                            modalData.dosen_avg !== null ? (
                                                <div
                                                    className="flex items-center justify-between p-4 rounded-2xl mt-4"
                                                    style={{ background: '#2dce89' }}
                                                >
                                                    <span className="text-white text-[10px] font-black uppercase tracking-widest">
                                                        Rata-rata Nilai Dosen
                                                    </span>
                                                    <span className="text-white font-black text-xl">
                                                        {Number(modalData.dosen_avg).toFixed(1)}
                                                    </span>
                                                </div>
                                            ) : modalData.dosen_list.length === 1 &&
                                              modalData.dosen_list[0].nilai_dosen !== null ? (
                                                <div
                                                    className="flex items-center justify-between p-4 rounded-2xl mt-4"
                                                    style={{ background: '#2dce89' }}
                                                >
                                                    <span className="text-white text-[10px] font-black uppercase tracking-widest">
                                                        Total Nilai Dosen
                                                    </span>
                                                    <span className="text-white font-black text-xl">
                                                        {Number(modalData.dosen_list[0].nilai_dosen).toFixed(
                                                            1,
                                                        )}
                                                    </span>
                                                </div>
                                            ) : null}
                                        </>
                                    ) : (
                                        <div className="py-6 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                            <span className="material-symbols-outlined text-3xl text-gray-300 mb-2">
                                                pending_actions
                                            </span>
                                            <p className="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                                Belum Dinilai
                                            </p>
                                        </div>
                                    )}
                                </div>

                                {/* NILAI AKHIR SUMMARY */}
                                {modalData.manager &&
                                    modalData.dosen_avg !== null &&
                                    modalData.manager.nilai_manager !== null &&
                                    (() => {
                                        const nilaiAkhir = (
                                            modalData.manager.nilai_manager * 0.55 +
                                            modalData.dosen_avg * 0.45
                                        ).toFixed(1);
                                        const numAkhir = Number(nilaiAkhir);
                                        const grade =
                                            numAkhir >= 85
                                                ? 'A'
                                                : numAkhir >= 75
                                                  ? 'B'
                                                  : numAkhir >= 65
                                                    ? 'C'
                                                    : numAkhir >= 55
                                                      ? 'D'
                                                      : 'E';
                                        const gradeColor =
                                            grade === 'A'
                                                ? '#10b981'
                                                : grade === 'B'
                                                  ? '#3b82f6'
                                                  : grade === 'C'
                                                    ? '#f59e0b'
                                                    : grade === 'D'
                                                      ? '#f97316'
                                                      : '#ef4444';

                                        return (
                                            <div
                                                className="p-6 rounded-[2rem] flex items-center justify-between shadow-xl mt-6 relative overflow-hidden"
                                                style={{
                                                    background: 'linear-gradient(135deg,#004d4d,#008080)',
                                                }}
                                            >
                                                <span
                                                    className="material-symbols-outlined absolute -right-4 -top-4 text-white/5"
                                                    style={{ fontSize: '120px' }}
                                                >
                                                    workspace_premium
                                                </span>
                                                <div className="relative z-10">
                                                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-1.5">
                                                        Kalkulasi Nilai Akhir
                                                    </p>
                                                    <p className="text-white text-xs font-medium opacity-80 bg-black/20 px-3 py-1 rounded-lg inline-block">
                                                        ({Number(modalData.manager.nilai_manager).toFixed(1)}{' '}
                                                        × 55%) + ({Number(modalData.dosen_avg).toFixed(1)} ×
                                                        45%)
                                                    </p>
                                                </div>
                                                <div className="flex items-center gap-4 relative z-10">
                                                    <p className="text-white font-black text-5xl tracking-tighter">
                                                        {nilaiAkhir}
                                                    </p>
                                                    <span
                                                        className="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 border-white/20"
                                                        style={{ background: gradeColor }}
                                                    >
                                                        {grade}
                                                    </span>
                                                </div>
                                            </div>
                                        );
                                    })()}
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
