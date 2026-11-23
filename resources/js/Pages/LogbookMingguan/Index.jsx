import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookMingguanIndex({ rekapList = [], mingguBelumRekap = [] }) {
    const { auth, flash } = usePage().props;
    const user = auth?.user;
    const isMahasiswa = user?.role === 'mahasiswa';

    const [statusFilter, setStatusFilter] = useState('semua');
    const [proyekFilter, setProyekFilter] = useState('semua');
    const [mingguFilter, setMingguFilter] = useState('semua');

    const proyekUnik = Array.from(
        new Map(rekapList.filter((r) => r.proyek).map((r) => [r.proyek.id, r.proyek])).values(),
    ).sort((a, b) => (a.judul_proyek || '').localeCompare(b.judul_proyek || ''));

    const mingguUnik = Array.from(new Set(rekapList.map((r) => r.minggu_ke))).sort((a, b) => a - b);

    const filteredList = rekapList.filter((r) => {
        const matchStatus = statusFilter === 'semua' || r.status === statusFilter;
        const matchProyek =
            proyekFilter === 'semua' || String(r.pengajuan_proyek_id) === String(proyekFilter);
        const matchMinggu = mingguFilter === 'semua' || String(r.minggu_ke) === String(mingguFilter);
        return matchStatus && matchProyek && matchMinggu;
    });

    const isFiltered =
        statusFilter !== 'semua' || proyekFilter !== 'semua' || mingguFilter !== 'semua';

    const resetFilter = () => {
        setStatusFilter('semua');
        setProyekFilter('semua');
        setMingguFilter('semua');
    };

    const handleGenerate = (e, proyekId, mingguKe) => {
        e.preventDefault();
        router.post('/logbook-mingguan/generate', {
            pengajuan_proyek_id: proyekId,
            minggu_ke: mingguKe,
        });
    };

    const handleBatal = (e, rekap) => {
        e.preventDefault();
        if (
            window.confirm(
                `Batalkan rekap minggu ke-${rekap.minggu_ke}? File PDF akan dihapus dan kamu bisa generate ulang.`,
            )
        ) {
            router.delete(`/logbook-mingguan/${rekap.id}/batal`);
        }
    };

    const formatDateDMY = (dateStr) => {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return '-';
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    };

    const strLimit = (str, limit = 35) => {
        if (!str) return '';
        return str.length > limit ? `${str.substring(0, limit)}...` : str;
    };

    const getBadge = (status) => {
        switch (status) {
            case 'diajukan':
                return { badgeClass: 'bg-blue-100 text-blue-700', badgeLabel: 'Menunggu' };
            case 'disetujui':
                return { badgeClass: 'bg-green-100 text-green-700', badgeLabel: 'Disetujui' };
            case 'ditolak':
                return { badgeClass: 'bg-red-100 text-red-700', badgeLabel: 'Ditolak' };
            default:
                return { badgeClass: 'bg-gray-100 text-gray-600', badgeLabel: 'Draft' };
        }
    };

    return (
        <AppLayout title="Rekap Logbook Mingguan">
            <div className="max-w-5xl mx-auto px-4 py-6">
                {/* Header */}
                <div className="mb-6">
                    <Link
                        href="/logbook-harian"
                        className="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3 transition"
                    >
                        <span className="material-symbols-outlined text-base">arrow_back</span>
                        Kembali ke Logbook Harian
                    </Link>
                    <h1 className="text-2xl font-bold text-gray-800">Rekap Logbook Mingguan</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Kelola dan ajukan rekap logbook harian per minggu ke dosen pengampu.
                    </p>
                </div>

                {/* Flash Message */}
                {flash?.success && (
                    <div className="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        {flash.error}
                    </div>
                )}

                {/* Minggu Belum Direkap */}
                {mingguBelumRekap.length > 0 && (
                    <div className="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h2 className="text-sm font-semibold text-blue-700 mb-3">
                            <span className="material-symbols-outlined align-middle text-base">
                                pending_actions
                            </span>{' '}
                            Minggu Siap Direkap
                        </h2>
                        <div className="space-y-2">
                            {mingguBelumRekap.map((item, idx) => (
                                <div
                                    key={idx}
                                    className="flex items-center justify-between bg-white border border-blue-100 rounded-lg px-4 py-2"
                                >
                                    <div>
                                        <p className="text-sm font-medium text-gray-800">
                                            {item.proyek?.judul_proyek}
                                        </p>
                                        <p className="text-xs text-gray-500">
                                            Minggu ke-{item.minggu_ke}
                                        </p>
                                    </div>
                                    <form
                                        onSubmit={(e) =>
                                            handleGenerate(e, item.proyek?.id, item.minggu_ke)
                                        }
                                    >
                                        <button
                                            type="submit"
                                            className="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                                        >
                                            <span className="material-symbols-outlined text-sm">
                                                picture_as_pdf
                                            </span>
                                            Generate &amp; Ajukan
                                        </button>
                                    </form>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Daftar Rekap Mingguan */}
                <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    {/* FILTER BAR */}
                    <div className="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
                        {/* Filter Status Verifikasi */}
                        <div className="flex items-center gap-2">
                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Status:
                            </span>
                            {[
                                ['semua', 'Semua'],
                                ['diajukan', 'Menunggu'],
                                ['disetujui', 'Disetujui'],
                                ['ditolak', 'Ditolak'],
                            ].map(([val, lbl]) => (
                                <button
                                    key={val}
                                    type="button"
                                    onClick={() => setStatusFilter(val)}
                                    data-filter="status"
                                    data-value={val}
                                    className={`filter-btn px-3 py-1.5 rounded-lg text-[10px] font-semibold border transition-all ${
                                        statusFilter === val
                                            ? 'bg-[#004d4d] text-white border-[#004d4d]'
                                            : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#004d4d] hover:text-[#004d4d]'
                                    }`}
                                >
                                    {lbl}
                                </button>
                            ))}
                        </div>

                        {proyekUnik.length > 1 && (
                            <>
                                {/* Divider */}
                                <div className="h-5 w-px bg-gray-200"></div>

                                {/* Filter Proyek */}
                                <div className="flex items-center gap-2">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Proyek:
                                    </span>
                                    <select
                                        id="filterProyek"
                                        value={proyekFilter}
                                        onChange={(e) => setProyekFilter(e.target.value)}
                                        className="py-1.5 pl-3 pr-7 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 focus:outline-none focus:border-[#004d4d] appearance-none cursor-pointer"
                                    >
                                        <option value="semua">Semua</option>
                                        {proyekUnik.map((proyek) => (
                                            <option key={proyek.id} value={proyek.id}>
                                                {strLimit(proyek.judul_proyek, 35)}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </>
                        )}

                        {mingguUnik.length > 1 && (
                            <>
                                {/* Divider */}
                                <div className="h-5 w-px bg-gray-200"></div>

                                {/* Filter Minggu ke- */}
                                <div className="flex items-center gap-2">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Minggu:
                                    </span>
                                    <select
                                        id="filterMinggu"
                                        value={mingguFilter}
                                        onChange={(e) => setMingguFilter(e.target.value)}
                                        className="py-1.5 pl-3 pr-7 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 focus:outline-none focus:border-[#004d4d] appearance-none cursor-pointer"
                                    >
                                        <option value="semua">Semua</option>
                                        {mingguUnik.map((minggu) => (
                                            <option key={minggu} value={minggu}>
                                                Minggu ke-{minggu}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </>
                        )}

                        {/* Reset & Counter */}
                        <div className="ml-auto flex items-center gap-3">
                            <span
                                id="filterCounter"
                                className={`${
                                    !isFiltered ? 'hidden ' : ''
                                }text-[10px] font-semibold text-gray-400`}
                            >
                                <span id="visibleCount">{filteredList.length}</span> rekap ditemukan
                            </span>
                            <button
                                type="button"
                                onClick={resetFilter}
                                id="btnReset"
                                className={`${
                                    !isFiltered ? 'hidden ' : ''
                                }flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-semibold border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-all`}
                            >
                                <span className="material-symbols-outlined text-sm">
                                    filter_alt_off
                                </span>{' '}
                                Reset
                            </button>
                        </div>
                    </div>

                    {/* Daftar item */}
                    {rekapList.length === 0 ? (
                        <div className="px-5 py-10 text-center text-gray-400 text-sm">
                            <span className="material-symbols-outlined text-4xl block mb-2">
                                folder_open
                            </span>
                            Belum ada rekap mingguan.
                        </div>
                    ) : (
                        <>
                            <div className="divide-y divide-gray-100" id="rekapContainer">
                                {filteredList.map((rekap) => {
                                    const { badgeClass, badgeLabel } = getBadge(rekap.status);
                                    return (
                                        <div
                                            key={rekap.id}
                                            className="rekap-item px-5 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 transition-colors"
                                            data-status={rekap.status}
                                            data-proyek-id={rekap.pengajuan_proyek_id}
                                            data-minggu={rekap.minggu_ke}
                                        >
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-medium text-gray-800 truncate">
                                                    {rekap.proyek?.judul_proyek ?? '-'}
                                                </p>
                                                <p className="text-xs text-gray-500 mt-0.5">
                                                    Minggu ke-{rekap.minggu_ke} &nbsp;·&nbsp;
                                                    Diajukan: {formatDateDMY(rekap.diajukan_at)}
                                                </p>
                                                {rekap.catatan_dosen && (
                                                    <p className="text-xs text-amber-600 mt-1">
                                                        <span className="material-symbols-outlined text-xs align-middle">
                                                            comment
                                                        </span>{' '}
                                                        {rekap.catatan_dosen}
                                                    </p>
                                                )}
                                            </div>

                                            {/* Badge Status */}
                                            <div className="shrink-0">
                                                <span
                                                    className={`text-xs font-semibold px-2.5 py-1 rounded-full ${badgeClass}`}
                                                >
                                                    {badgeLabel}
                                                </span>
                                            </div>

                                            {/* Tombol Lihat PDF */}
                                            {rekap.pdf_path && (
                                                <a
                                                    href={`/logbook-mingguan/${rekap.id}`}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="shrink-0 flex items-center gap-1 text-xs font-medium text-gray-600 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded-lg transition"
                                                >
                                                    <span className="material-symbols-outlined text-sm">
                                                        open_in_new
                                                    </span>
                                                    Lihat PDF
                                                </a>
                                            )}

                                            {/* Tombol Batalkan — hanya untuk status diajukan/draft */}
                                            {['diajukan', 'draft'].includes(rekap.status) &&
                                                isMahasiswa && (
                                                    <form onSubmit={(e) => handleBatal(e, rekap)}>
                                                        <button
                                                            type="submit"
                                                            className="shrink-0 flex items-center gap-1 text-xs font-medium text-red-500 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 px-3 py-1.5 rounded-lg transition"
                                                        >
                                                            <span className="material-symbols-outlined text-sm">
                                                                undo
                                                            </span>
                                                            Batalkan
                                                        </button>
                                                    </form>
                                                )}
                                        </div>
                                    );
                                })}
                            </div>

                            {/* Empty filter state */}
                            <div
                                id="emptyFilter"
                                className={`${
                                    filteredList.length > 0 ? 'hidden ' : ''
                                }px-5 py-10 text-center text-gray-400 text-sm`}
                            >
                                <span className="material-symbols-outlined text-4xl block mb-2">
                                    search_off
                                </span>
                                Tidak ada rekap yang cocok dengan filter.
                                <button
                                    type="button"
                                    onClick={resetFilter}
                                    className="block mx-auto mt-3 text-[10px] font-bold text-[#004d4d] hover:underline uppercase tracking-widest"
                                >
                                    Reset Filter
                                </button>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
