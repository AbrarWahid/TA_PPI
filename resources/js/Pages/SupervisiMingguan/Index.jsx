import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiMingguanIndex({
    rekapList = [],
    mingguBelumRekap = [],
}) {
    const { auth, flash } = usePage().props;
    const user = auth?.user;
    const isMahasiswa = user?.role === 'mahasiswa';
    const isDosen = ['dosen_pengampu', 'dosen'].includes(user?.role_aktif ?? user?.role);

    const [openItems, setOpenItems] = useState({});
    const [activeModalId, setActiveModalId] = useState(null);
    const [verifStatus, setVerifStatus] = useState('');
    const [verifCatatan, setVerifCatatan] = useState('');

    const toggleOpen = (idx) => {
        setOpenItems((prev) => ({ ...prev, [idx]: !prev[idx] }));
    };

    const openVerifModal = (rekap) => {
        setVerifStatus('');
        setVerifCatatan('');
        setActiveModalId(rekap.id);
    };

    const closeVerifModal = () => {
        setActiveModalId(null);
    };

    const handleGenerate = (e, supervisiMatkulId, mingguKe) => {
        e.preventDefault();
        router.post('/supervisi-mingguan/generate', {
            supervisi_matkul_id: supervisiMatkulId,
            minggu_ke: mingguKe,
        });
    };

    const handleBatal = (e, rekap) => {
        e.preventDefault();
        if (window.confirm(`Batalkan rekap minggu ke-${rekap.minggu_ke} ini?`)) {
            router.delete(`/supervisi-mingguan/${rekap.id}/batal`);
        }
    };

    const handleVerifikasi = (e, rekapId) => {
        e.preventDefault();
        router.patch(
            `/supervisi-mingguan/${rekapId}/verifikasi`,
            {
                status: verifStatus,
                catatan_dosen: verifCatatan,
            },
            {
                onSuccess: () => closeVerifModal(),
            },
        );
    };

    const getStatusCfg = (status) => {
        switch (status) {
            case 'diajukan':
                return {
                    cls: 'bg-amber-50 text-amber-600',
                    dot: 'bg-amber-400',
                    label: 'Diajukan',
                };
            case 'disetujui':
                return {
                    cls: 'bg-emerald-50 text-emerald-600',
                    dot: 'bg-emerald-400',
                    label: 'Disetujui',
                };
            case 'ditolak':
                return {
                    cls: 'bg-red-50 text-red-500',
                    dot: 'bg-red-400',
                    label: 'Ditolak',
                };
            default:
                return {
                    cls: 'bg-gray-100 text-gray-400',
                    dot: 'bg-gray-300',
                    label: 'Draft',
                };
        }
    };

    return (
        <AppLayout title="Supervisi Mingguan">
            {/* FLASH MESSAGES */}
            {flash?.success && (
                <div className="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold">
                    <span className="material-symbols-outlined text-lg">check_circle</span>
                    {flash.success}
                </div>
            )}
            {flash?.error && (
                <div className="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3 rounded-2xl text-sm font-bold">
                    <span className="material-symbols-outlined text-lg">error</span>
                    {flash.error}
                </div>
            )}

            {/* HEADER */}
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-black text-[#004d4d]">Rekap Supervisi Mingguan</h1>
                    <p className="text-sm text-gray-400 mt-1">
                        Rekap dan pengajuan laporan supervisi per minggu
                    </p>
                </div>
                {isMahasiswa && (
                    <Link
                        href="/supervisi-harian"
                        className="flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 text-[#004d4d] rounded-2xl font-black text-sm hover:bg-gray-50 transition-all shadow-sm"
                    >
                        <span className="material-symbols-outlined text-lg">edit_note</span>
                        Lihat Harian
                    </Link>
                )}
            </div>

            {/* FORM GENERATE — hanya mahasiswa, hanya jika ada minggu belum direkap */}
            {isMahasiswa && mingguBelumRekap.length > 0 && (
                <div className="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <p className="text-sm font-black text-amber-700 mb-4 flex items-center gap-2">
                        <span className="material-symbols-outlined text-lg">summarize</span>
                        Minggu Belum Direkap ({mingguBelumRekap.length} item)
                    </p>

                    {mingguBelumRekap.map((item, idx) => {
                        const sm = item.supervisiMatkul || item.supervisi_matkul;
                        const matkul = sm?.mataKuliah || sm?.mata_kuliah;
                        const dosen = sm?.dosen;
                        const isOpen = !!openItems[idx];

                        return (
                            <div
                                key={idx}
                                className="bg-white rounded-2xl p-5 mb-4 border border-amber-100"
                            >
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="font-black text-[#004d4d] text-sm">
                                            Minggu ke-{item.minggu_ke}
                                        </p>
                                        <p className="text-xs text-gray-500 mt-0.5">
                                            {matkul?.nama_matkul ?? '-'} &mdash;{' '}
                                            {dosen?.nama_dosen ?? dosen?.user?.name ?? '-'}
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => toggleOpen(idx)}
                                        className="flex items-center gap-2 px-4 py-2 bg-[#004d4d] text-white rounded-xl text-xs font-black hover:bg-[#003d3d] transition"
                                    >
                                        <span className="material-symbols-outlined text-sm">
                                            {isOpen ? 'close' : 'add'}
                                        </span>
                                        <span>{isOpen ? 'Tutup' : 'Generate'}</span>
                                    </button>
                                </div>

                                {isOpen && (
                                    <div className="mt-4 pt-4 border-t border-amber-100">
                                        <form
                                            onSubmit={(e) =>
                                                handleGenerate(e, sm?.id, item.minggu_ke)
                                            }
                                            className="space-y-4"
                                        >
                                            <div className="bg-gray-50 rounded-xl px-4 py-3 space-y-1">
                                                <p className="text-xs font-black uppercase text-[#004d4d] tracking-wide">
                                                    Detail Laporan
                                                </p>
                                                <p className="text-sm font-bold text-gray-700">
                                                    {matkul?.nama_matkul ?? '-'}
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    Dosen:{' '}
                                                    {dosen?.nama_dosen ?? dosen?.user?.name ?? '-'}
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    Minggu ke-{item.minggu_ke}
                                                </p>
                                            </div>

                                            <button
                                                type="submit"
                                                className="w-full py-3 bg-[#004d4d] text-white rounded-xl font-black text-sm hover:bg-[#003d3d] transition flex items-center justify-center gap-2"
                                            >
                                                <span className="material-symbols-outlined text-sm">
                                                    picture_as_pdf
                                                </span>
                                                Generate PDF &amp; Ajukan ke Dosen
                                            </button>
                                        </form>
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}

            {/* Info jika mahasiswa tapi tidak ada data minggu belum rekap & tidak ada rekap sama sekali */}
            {isMahasiswa && mingguBelumRekap.length === 0 && rekapList.length === 0 && (
                <div className="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-start gap-3">
                    <span className="material-symbols-outlined text-blue-400 text-xl mt-0.5">
                        info
                    </span>
                    <div>
                        <p className="text-sm font-bold text-blue-700">Belum ada log harian</p>
                        <p className="text-xs text-blue-500 mt-0.5">
                            Isi supervisi harian terlebih dahulu, lalu kembali ke sini untuk
                            generate rekap mingguan.
                        </p>
                    </div>
                </div>
            )}

            {/* TABEL REKAP */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <p className="font-black text-[#004d4d] text-sm uppercase tracking-widest">
                        Daftar Rekap
                    </p>
                    <span className="text-xs text-gray-400">{rekapList.length} laporan</span>
                </div>

                {rekapList.length === 0 ? (
                    <div className="py-16 text-center">
                        <span className="material-symbols-outlined text-5xl text-gray-200">
                            summarize
                        </span>
                        <p className="text-gray-400 font-bold mt-3">
                            Belum ada rekap supervisi mingguan
                        </p>
                        {isMahasiswa && (
                            <p className="text-xs text-gray-400 mt-1">
                                Isi supervisi harian terlebih dahulu, lalu generate rekap di sini
                            </p>
                        )}
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-[#004d4d] text-[10px] uppercase tracking-widest text-white font-black italic">
                                    <th className="px-6 py-3 text-center">Minggu</th>
                                    {!isMahasiswa && (
                                        <th className="px-6 py-3 text-left">Mahasiswa</th>
                                    )}
                                    <th className="px-6 py-3 text-left">Mata Kuliah</th>
                                    <th className="px-6 py-3 text-left">Dosen</th>
                                    <th className="px-6 py-3 text-center">Status</th>
                                    <th className="px-6 py-3 text-center">Catatan Dosen</th>
                                    <th className="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {rekapList.map((rekap) => {
                                    const statusCfg = getStatusCfg(rekap.status);
                                    const sm = rekap.supervisi_matkul || rekap.supervisiMatkul;
                                    const smMatkul = sm?.mata_kuliah || sm?.mataKuliah;
                                    const namaMatkul =
                                        smMatkul?.nama_matkul ??
                                        rekap.matkul?.nama_matkul ??
                                        '-';
                                    const namaDosen =
                                        sm?.dosen?.nama_dosen ??
                                        rekap.dosen?.nama_dosen ??
                                        '-';
                                    const namaMhs =
                                        rekap.mahasiswa?.nama_mahasiswa ??
                                        rekap.mahasiswa?.nama ??
                                        '-';

                                    return (
                                        <tr
                                            key={rekap.id}
                                            className="hover:bg-gray-50/50 transition-colors"
                                        >
                                            {/* Minggu */}
                                            <td className="px-6 py-4 text-center">
                                                <span className="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#004d4d]/10 text-[#004d4d] font-black text-sm">
                                                    {rekap.minggu_ke}
                                                </span>
                                            </td>

                                            {/* Mahasiswa (hanya jika bukan role mahasiswa) */}
                                            {!isMahasiswa && (
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-8 h-8 rounded-xl bg-[#004d4d] flex items-center justify-center text-white font-black text-xs flex-shrink-0">
                                                            {(namaMhs !== '-' ? namaMhs : 'M')
                                                                .substring(0, 1)
                                                                .toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="font-bold text-gray-800">
                                                                {namaMhs}
                                                            </p>
                                                            <p className="text-xs text-gray-400">
                                                                {rekap.mahasiswa?.nim ?? '-'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                            )}

                                            {/* Mata Kuliah — via supervisiMatkul */}
                                            <td className="px-6 py-4">
                                                <p className="font-semibold text-gray-700 text-xs">
                                                    {namaMatkul}
                                                </p>
                                                {sm?.tahun_ajaran && (
                                                    <p className="text-[10px] text-gray-400 mt-0.5">
                                                        {sm.tahun_ajaran}{' '}
                                                        {sm.semester ? `/ Sem. ${sm.semester}` : ''}
                                                    </p>
                                                )}
                                            </td>

                                            {/* Dosen — via supervisiMatkul */}
                                            <td className="px-6 py-4 text-xs text-gray-600">
                                                {namaDosen}
                                            </td>

                                            {/* Status */}
                                            <td className="px-6 py-4 text-center">
                                                <span
                                                    className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black ${statusCfg.cls}`}
                                                >
                                                    <span
                                                        className={`w-1.5 h-1.5 rounded-full ${statusCfg.dot} inline-block`}
                                                    ></span>
                                                    {statusCfg.label}
                                                </span>
                                            </td>

                                            {/* Catatan Dosen */}
                                            <td className="px-6 py-4 text-center">
                                                {rekap.catatan_dosen ? (
                                                    <span
                                                        className="text-xs text-gray-500 italic max-w-[150px] inline-block truncate"
                                                        title={rekap.catatan_dosen}
                                                    >
                                                        {rekap.catatan_dosen}
                                                    </span>
                                                ) : (
                                                    <span className="text-xs text-gray-300">—</span>
                                                )}
                                            </td>

                                            {/* Aksi */}
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-center gap-2">
                                                    {/* Tombol PDF / Show */}
                                                    {rekap.pdf_path && (
                                                        <Link
                                                            href={`/supervisi-mingguan/${rekap.id}`}
                                                            className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-xs font-bold hover:bg-gray-200 transition"
                                                        >
                                                            <span className="material-symbols-outlined text-sm">
                                                                picture_as_pdf
                                                            </span>
                                                            PDF
                                                        </Link>
                                                    )}

                                                    {/* Tombol Batal — mahasiswa, status diajukan atau draft */}
                                                    {isMahasiswa &&
                                                        ['diajukan', 'draft'].includes(
                                                            rekap.status,
                                                        ) && (
                                                            <form
                                                                onSubmit={(e) =>
                                                                    handleBatal(e, rekap)
                                                                }
                                                            >
                                                                <button
                                                                    type="submit"
                                                                    className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-red-50 text-red-500 text-xs font-bold hover:bg-red-100 transition"
                                                                >
                                                                    <span className="material-symbols-outlined text-sm">
                                                                        undo
                                                                    </span>
                                                                    Batal
                                                                </button>
                                                            </form>
                                                        )}

                                                    {/* Tombol Verifikasi — dosen, status diajukan */}
                                                    {isDosen && rekap.status === 'diajukan' && (
                                                        <button
                                                            type="button"
                                                            onClick={() => openVerifModal(rekap)}
                                                            className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#004d4d] text-white text-xs font-bold hover:bg-[#003d3d] transition"
                                                        >
                                                            <span className="material-symbols-outlined text-sm">
                                                                verified
                                                            </span>
                                                            Verifikasi
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* MODALS VERIFIKASI — di luar tabel, valid HTML */}
            {rekapList.map((rekap) => {
                if (!(isDosen && rekap.status === 'diajukan')) return null;
                const sm = rekap.supervisi_matkul || rekap.supervisiMatkul;
                const smMatkul = sm?.mata_kuliah || sm?.mataKuliah;
                const modalMatkul = smMatkul?.nama_matkul ?? rekap.matkul?.nama_matkul ?? '-';
                const isOpen = activeModalId === rekap.id;

                return (
                    <div
                        key={rekap.id}
                        id={`modal-verif-${rekap.id}`}
                        className={`${
                            !isOpen ? 'hidden ' : ''
                        }fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm`}
                        onClick={(e) => {
                            if (e.target === e.currentTarget) closeVerifModal();
                        }}
                    >
                        <div
                            className="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className="flex items-center justify-between mb-6">
                                <h3 className="text-lg font-black text-[#004d4d]">
                                    Verifikasi Supervisi
                                </h3>
                                <button
                                    type="button"
                                    onClick={closeVerifModal}
                                    className="text-gray-300 hover:text-gray-500 transition"
                                >
                                    <span className="material-symbols-outlined">close</span>
                                </button>
                            </div>

                            {/* Info rekap */}
                            <div className="bg-gray-50 rounded-2xl p-4 mb-5 space-y-1">
                                <p className="text-xs text-gray-400 font-bold uppercase tracking-wide">
                                    Mahasiswa
                                </p>
                                <p className="font-black text-gray-800">
                                    {rekap.mahasiswa?.nama_mahasiswa ??
                                        rekap.mahasiswa?.nama ??
                                        '-'}
                                </p>
                                <p className="text-xs text-gray-500">
                                    Minggu ke-{rekap.minggu_ke} &mdash; {modalMatkul}
                                </p>
                                {rekap.pdf_path && (
                                    <Link
                                        href={`/supervisi-mingguan/${rekap.id}`}
                                        className="inline-flex items-center gap-1 mt-2 text-xs font-bold text-[#004d4d] hover:underline"
                                    >
                                        <span className="material-symbols-outlined text-sm">
                                            picture_as_pdf
                                        </span>
                                        Buka PDF laporan
                                    </Link>
                                )}
                            </div>

                            <form
                                onSubmit={(e) => handleVerifikasi(e, rekap.id)}
                                className="space-y-4"
                            >
                                {/* Pilihan keputusan */}
                                <div>
                                    <label className="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">
                                        Keputusan *
                                    </label>
                                    <div className="grid grid-cols-2 gap-3">
                                        <label className="flex items-center gap-2 p-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 cursor-pointer hover:border-emerald-400 transition">
                                            <input
                                                type="radio"
                                                name="status"
                                                value="disetujui"
                                                required
                                                checked={verifStatus === 'disetujui'}
                                                onChange={(e) => setVerifStatus(e.target.value)}
                                                className="accent-emerald-500"
                                            />
                                            <span className="text-sm font-bold text-emerald-700">
                                                ✓ Setujui
                                            </span>
                                        </label>
                                        <label className="flex items-center gap-2 p-3 rounded-2xl border-2 border-red-200 bg-red-50 cursor-pointer hover:border-red-400 transition">
                                            <input
                                                type="radio"
                                                name="status"
                                                value="ditolak"
                                                checked={verifStatus === 'ditolak'}
                                                onChange={(e) => setVerifStatus(e.target.value)}
                                                className="accent-red-500"
                                            />
                                            <span className="text-sm font-bold text-red-600">
                                                ✕ Tolak
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {/* Catatan */}
                                <div>
                                    <label className="block text-xs font-black text-gray-600 uppercase tracking-wide mb-2">
                                        Catatan untuk Mahasiswa
                                    </label>
                                    <textarea
                                        name="catatan_dosen"
                                        rows={3}
                                        value={verifCatatan}
                                        onChange={(e) => setVerifCatatan(e.target.value)}
                                        placeholder="Opsional — tuliskan revisi atau komentar..."
                                        className="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-[#7fffd4] resize-none"
                                    ></textarea>
                                </div>

                                <div className="flex gap-3 pt-2">
                                    <button
                                        type="button"
                                        onClick={closeVerifModal}
                                        className="flex-1 px-4 py-3 rounded-2xl border border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        className="flex-1 px-4 py-3 rounded-2xl bg-[#004d4d] text-white font-black text-sm hover:bg-[#003d3d] transition"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                );
            })}
        </AppLayout>
    );
}
