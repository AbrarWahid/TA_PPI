import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function MahasiswaIndex({
    mahasiswas,
    kelasList = [],
    allMhs = [],
    search: initialSearch = '',
    prodi: initialProdi = '',
    kelas: initialKelas = '',
    perPage: initialPerPage = 20,
}) {
    const { flash } = usePage().props;
    const warnings = flash?.warnings || [];

    const [search, setSearch] = useState(initialSearch || '');
    const [prodi, setProdi] = useState(initialProdi || '');
    const [kelas, setKelas] = useState(initialKelas || '');
    const [perPage, setPerPage] = useState(initialPerPage || 20);

    const isPaginator =
        mahasiswas &&
        typeof mahasiswas === 'object' &&
        !Array.isArray(mahasiswas) &&
        Array.isArray(mahasiswas.data);

    const list = isPaginator ? mahasiswas.data : Array.isArray(mahasiswas) ? mahasiswas : [];

    const startNo = isPaginator
        ? ((mahasiswas.current_page || 1) - 1) * (mahasiswas.per_page || Number(perPage) || 20)
        : 0;

    const totalPages = isPaginator ? mahasiswas.last_page || 1 : 1;
    const currentPage = isPaginator ? mahasiswas.current_page || 1 : 1;

    const submitFilter = (overrides = {}) => {
        const params = {
            search,
            prodi,
            kelas,
            per_page: perPage,
            ...overrides,
        };
        router.get('/mahasiswa', params, { preserveState: true });
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();
        submitFilter();
    };

    const handleProdiChange = (e) => {
        const val = e.target.value;
        setProdi(val);
        submitFilter({ prodi: val });
    };

    const handleKelasChange = (e) => {
        const val = e.target.value;
        setKelas(val);
        submitFilter({ kelas: val });
    };

    const handlePerPageChange = (e) => {
        const val = e.target.value;
        setPerPage(val);
        submitFilter({ per_page: val });
    };

    const handleDelete = (e, mhs) => {
        e.preventDefault();
        if (window.confirm('Hapus mahasiswa ini?')) {
            router.delete(`/mahasiswa/${mhs.id}`);
        }
    };

    const cTRMO = allMhs.filter((m) => m.user && m.user.prodi === 'mekatronika').length;
    const cTRO = allMhs.filter((m) => m.user && m.user.prodi === 'otomasi').length;
    const cTRIN = allMhs.filter((m) => m.user && m.user.prodi === 'informatika').length;

    const getProdiInfo = (prodiVal) => {
        switch (prodiVal) {
            case 'mekatronika':
                return {
                    prodiLabel: 'D4 TRMO – Teknik Rekayasa Mekatronika',
                    badgeColor: 'bg-purple-100 text-purple-700',
                };
            case 'otomasi':
                return {
                    prodiLabel: 'D4 TRO – Teknik Rekayasa Otomasi',
                    badgeColor: 'bg-blue-100 text-blue-700',
                };
            case 'informatika':
                return {
                    prodiLabel: 'D4 TRIN – Informatika Industri',
                    badgeColor: 'bg-primary-container text-primary',
                };
            default:
                return {
                    prodiLabel: prodiVal || '-',
                    badgeColor: 'bg-surface-container text-on-surface-variant',
                };
        }
    };

    const buildPageUrl = (page) => {
        if (isPaginator && Array.isArray(mahasiswas.links)) {
            const found = mahasiswas.links.find((l) => String(l.label) === String(page));
            if (found?.url) return found.url;
        }
        const qs = new URLSearchParams();
        if (initialSearch) qs.set('search', initialSearch);
        if (initialProdi) qs.set('prodi', initialProdi);
        if (initialKelas) qs.set('kelas', initialKelas);
        if (initialPerPage) qs.set('per_page', initialPerPage);
        qs.set('page', page);
        return `/mahasiswa?${qs.toString()}`;
    };

    return (
        <AppLayout title="Data Mahasiswa">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Master Data <span className="text-[#2dce89]">Mahasiswa</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        {allMhs.length} mahasiswa terdaftar
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href="/mahasiswa/batch"
                        className="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg text-[#004d4d]"
                        style={{ background: '#7fffd4' }}
                    >
                        <span className="material-symbols-outlined text-base">table_rows</span>{' '}
                        Input Massal
                    </Link>
                    <Link
                        href="/mahasiswa/create"
                        className="flex items-center gap-2 px-5 py-3 bg-primary text-on-primary rounded-2xl text-xs font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg"
                    >
                        <span className="material-symbols-outlined text-base">person_add</span>{' '}
                        Tambah Mahasiswa
                    </Link>
                </div>
            </div>

            {/* FLASH MESSAGES */}
            {flash?.success && (
                <div className="mb-6 px-5 py-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-xs font-bold flex items-center gap-2">
                    <span className="material-symbols-outlined text-base">check_circle</span>
                    {flash.success}
                </div>
            )}
            {warnings.length > 0 && (
                <div className="mb-6 px-5 py-4 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-bold">
                    <p className="font-black mb-1 flex items-center gap-2">
                        <span className="material-symbols-outlined text-base">warning</span>
                        Beberapa baris dilewati:
                    </p>
                    <ul className="list-disc list-inside space-y-1 mt-1">
                        {warnings.map((w, i) => (
                            <li key={i}>{w}</li>
                        ))}
                    </ul>
                </div>
            )}

            {/* STAT CARDS */}
            <div className="grid grid-cols-4 gap-5 mb-8">
                <div
                    className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36"
                    style={{ background: '#004d4d' }}
                >
                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                        Total Mahasiswa
                    </p>
                    <p className="text-5xl font-black italic tracking-tighter text-white">
                        {allMhs.length}
                    </p>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5"
                        style={{ fontSize: '100px' }}
                    >
                        group
                    </span>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-purple-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>
                        <p className="text-purple-500 text-[10px] font-black uppercase tracking-widest">
                            Prodi TRMO
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cTRMO}
                    </p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-blue-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                        <p className="text-blue-500 text-[10px] font-black uppercase tracking-widest">
                            Prodi TRO
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cTRO}
                    </p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-primary-container p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-secondary inline-block"></span>
                        <p className="text-secondary text-[10px] font-black uppercase tracking-widest">
                            Prodi TRIN
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cTRIN}
                    </p>
                </div>
            </div>

            {/* TABLE */}
            <div className="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                {/* Filter Bar */}
                <form
                    onSubmit={handleFormSubmit}
                    className="px-7 py-5 border-b border-outline-variant/10 flex items-center gap-3 flex-wrap"
                >
                    {/* Search */}
                    <div className="relative flex-1 min-w-[200px]">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">
                            search
                        </span>
                        <input
                            type="text"
                            name="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari mahasiswa..."
                            className="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary"
                        />
                    </div>

                    {/* Dropdown Prodi */}
                    <select
                        name="prodi"
                        value={prodi}
                        onChange={handleProdiChange}
                        className="px-4 py-2.5 bg-white border border-outline-variant/20 rounded-xl text-xs font-bold text-on-surface focus:outline-none focus:border-primary min-w-[180px]"
                    >
                        <option value="">Semua Prodi</option>
                        <option value="informatika">
                            Teknologi Rekayasa Informatika Industri
                        </option>
                        <option value="mekatronika">Teknologi Rekayasa Mekatronika</option>
                        <option value="otomasi">Teknologi Rekayasa Otomasi</option>
                    </select>

                    {/* Dropdown Kelas */}
                    <select
                        name="kelas"
                        value={kelas}
                        onChange={handleKelasChange}
                        className="px-4 py-2.5 bg-white border border-outline-variant/20 rounded-xl text-xs font-bold text-on-surface focus:outline-none focus:border-primary min-w-[140px]"
                    >
                        <option value="">Semua Kelas</option>
                        {kelasList.map((k) => (
                            <option key={k} value={k}>
                                {k}
                            </option>
                        ))}
                    </select>

                    {/* Dropdown Per Halaman */}
                    <select
                        name="per_page"
                        value={Number(perPage)}
                        onChange={handlePerPageChange}
                        className="px-4 py-2.5 bg-white border border-outline-variant/20 rounded-xl text-xs font-bold text-on-surface focus:outline-none focus:border-primary"
                    >
                        {[10, 20, 50, 100].map((pp) => (
                            <option key={pp} value={pp}>
                                {pp} / hal
                            </option>
                        ))}
                    </select>

                    {/* Tombol Search */}
                    <button
                        type="submit"
                        className="px-4 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-black hover:opacity-90 transition"
                    >
                        Cari
                    </button>

                    {(initialSearch || initialProdi || initialKelas || search || prodi || kelas) && (
                        <Link
                            href="/mahasiswa"
                            className="px-4 py-2.5 bg-surface-container text-on-surface-variant rounded-xl text-xs font-black hover:bg-red-50 hover:text-red-500 transition"
                        >
                            Reset
                        </Link>
                    )}
                </form>

                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-outline-variant/10 bg-surface-container-low/30">
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">
                                #
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Identitas
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                NIM
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Program Studi
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Kelas
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-outline-variant/10">
                        {list.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-7 py-20 text-center">
                                    <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                                        person_search
                                    </span>
                                    <p className="font-black italic text-on-surface-variant/40 text-sm">
                                        Tidak ada mahasiswa ditemukan.
                                    </p>
                                </td>
                            </tr>
                        ) : (
                            list.map((mhs, idx) => {
                                const prodiVal = mhs.user?.prodi ?? '';
                                const { prodiLabel, badgeColor } = getProdiInfo(prodiVal);
                                return (
                                    <tr
                                        key={mhs.id}
                                        className="hover:bg-surface-container-lowest transition-colors"
                                    >
                                        <td className="px-7 py-5 text-[10px] font-black text-outline/40">
                                            {startNo + idx + 1}
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary font-black text-xs shrink-0">
                                                    {(mhs.nama || '')
                                                        .substring(0, 2)
                                                        .toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="font-black text-on-surface text-sm leading-none">
                                                        {mhs.nama}
                                                    </p>
                                                    <p className="text-[10px] text-outline font-medium mt-0.5">
                                                        {mhs.user?.email ?? '-'}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-7 py-5">
                                            <span className="inline-flex px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-xl text-[10px] font-black font-mono tracking-wider">
                                                {mhs.nim}
                                            </span>
                                        </td>
                                        <td className="px-7 py-5">
                                            <span
                                                className={`inline-flex px-3 py-1.5 rounded-xl text-[10px] font-black ${badgeColor}`}
                                            >
                                                {prodiLabel}
                                            </span>
                                        </td>
                                        <td className="px-7 py-5">
                                            {mhs.kelas ? (
                                                <span className="inline-flex px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-xl text-[10px] font-black">
                                                    {mhs.kelas}
                                                </span>
                                            ) : (
                                                <span className="text-outline/30 text-xs">—</span>
                                            )}
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center justify-center gap-2">
                                                <Link
                                                    href={`/mahasiswa/${mhs.id}/edit`}
                                                    className="p-2.5 text-outline hover:text-primary hover:bg-primary-container rounded-xl transition-all"
                                                    title="Edit"
                                                >
                                                    <span className="material-symbols-outlined text-base">
                                                        edit
                                                    </span>
                                                </Link>
                                                <form onSubmit={(e) => handleDelete(e, mhs)}>
                                                    <button
                                                        type="submit"
                                                        className="p-2.5 text-outline hover:text-white hover:bg-red-500 rounded-xl transition-all"
                                                        title="Hapus"
                                                    >
                                                        <span className="material-symbols-outlined text-base">
                                                            delete
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>

                {/* Pagination */}
                {isPaginator && totalPages > 1 && (
                    <div className="px-7 py-5 border-t border-outline-variant/10 flex items-center justify-between">
                        <p className="text-xs text-outline font-medium">
                            Menampilkan{' '}
                            <strong>
                                {mahasiswas.from}&ndash;{mahasiswas.to}
                            </strong>{' '}
                            dari <strong>{mahasiswas.total}</strong> data
                        </p>
                        <div className="flex items-center gap-1">
                            {/* Prev */}
                            {currentPage <= 1 ? (
                                <span className="px-3 py-2 text-xs text-outline/30 font-black">
                                    &lsaquo; Sebelumnya
                                </span>
                            ) : (
                                <Link
                                    href={
                                        mahasiswas.prev_page_url || buildPageUrl(currentPage - 1)
                                    }
                                    className="px-3 py-2 text-xs font-black text-on-surface-variant hover:text-primary transition"
                                >
                                    &lsaquo; Sebelumnya
                                </Link>
                            )}

                            {/* Page numbers */}
                            {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                                <Link
                                    key={page}
                                    href={buildPageUrl(page)}
                                    className={`w-8 h-8 flex items-center justify-center rounded-xl text-xs font-black transition ${
                                        page === currentPage
                                            ? 'bg-primary text-on-primary'
                                            : 'text-on-surface-variant hover:bg-surface-container'
                                    }`}
                                >
                                    {page}
                                </Link>
                            ))}

                            {/* Next */}
                            {currentPage < totalPages ? (
                                <Link
                                    href={
                                        mahasiswas.next_page_url || buildPageUrl(currentPage + 1)
                                    }
                                    className="px-3 py-2 text-xs font-black text-on-surface-variant hover:text-primary transition"
                                >
                                    Selanjutnya &rsaquo;
                                </Link>
                            ) : (
                                <span className="px-3 py-2 text-xs text-outline/30 font-black">
                                    Selanjutnya &rsaquo;
                                </span>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
