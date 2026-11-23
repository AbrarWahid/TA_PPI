import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiMingguanDaftarVerifikasi({ laporans = [] }) {
    const [activeModal, setActiveModal] = useState(null);
    const [status, setStatus] = useState('disetujui');
    const [catatanDosen, setCatatanDosen] = useState('');

    const menunggu = laporans.filter((l) => l.status === 'diajukan').length;
    const disetujui = laporans.filter((l) => l.status === 'disetujui').length;
    const ditolak = laporans.filter((l) => l.status === 'ditolak').length;

    const openModal = (laporan) => {
        setActiveModal(laporan);
        setStatus('disetujui');
        setCatatanDosen('');
    };

    const handleVerifikasi = (e) => {
        e.preventDefault();
        if (!activeModal) return;
        router.patch(
            `/supervisi-mingguan/${activeModal.id}/verifikasi`,
            {
                status,
                catatan_dosen: catatanDosen,
            },
            {
                onSuccess: () => setActiveModal(null),
            },
        );
    };

    const getStatusBadge = (st) => {
        if (st === 'disetujui') {
            return { cls: 'bg-emerald-50 text-[#15803d] border border-emerald-200', label: 'Disetujui' };
        }
        if (st === 'ditolak') {
            return { cls: 'bg-red-50 text-[#b91c1c] border border-red-200', label: 'Ditolak' };
        }
        if (st === 'diajukan') {
            return { cls: 'bg-amber-50 text-[#b45309] border border-amber-200', label: 'Menunggu' };
        }
        return { cls: 'bg-[#f4f4f5] text-[#6a6f7c] border border-[#e4e4e7]', label: 'Draft' };
    };

    return (
        <AppLayout title="Verifikasi Supervisi Mingguan">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div>
                    <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                        SUPERVISI AKADEMIK
                    </p>
                    <h1 className="text-2xl font-semibold text-[#0b0b0c]">
                        Verifikasi Supervisi Mingguan
                    </h1>
                    <p className="text-xs text-[#6a6f7c] mt-1">
                        Daftar laporan supervisi mahasiswa yang diajukan kepada Anda.
                    </p>
                </div>

                {/* STATS */}
                <div className="grid grid-cols-3 gap-4">
                    <div className="bg-white rounded-xl p-5 border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                            Menunggu
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#b45309] mt-2">{menunggu}</p>
                    </div>
                    <div className="bg-white rounded-xl p-5 border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                            Disetujui
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#15803d] mt-2">{disetujui}</p>
                    </div>
                    <div className="bg-white rounded-xl p-5 border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                            Ditolak
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#b91c1c] mt-2">{ditolak}</p>
                    </div>
                </div>

                {/* TABLE */}
                <div className="bg-white rounded-xl shadow-surface border border-[#e4e4e7] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr>
                                    <th className="px-5 py-3.5">Mahasiswa</th>
                                    <th className="px-5 py-3.5">Mata Kuliah</th>
                                    <th className="px-5 py-3.5 text-center">Minggu</th>
                                    <th className="px-5 py-3.5 text-center">Status</th>
                                    <th className="px-5 py-3.5 text-center">Diajukan</th>
                                    <th className="px-5 py-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f4f4f5]">
                                {laporans.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-14 text-center">
                                            <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                                                inbox
                                            </span>
                                            <p className="text-[#6a6f7c] text-xs">
                                                Belum ada laporan yang masuk
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    laporans.map((laporan) => {
                                        const badge = getStatusBadge(laporan.status);
                                        const mhsName =
                                            laporan.mahasiswa?.user?.name ||
                                            laporan.mahasiswa?.nama ||
                                            '-';
                                        const matkulName =
                                            laporan.supervisi_matkul?.mata_kuliah?.nama_matkul ||
                                            laporan.matkul?.nama_matkul ||
                                            '-';
                                        return (
                                            <tr key={laporan.id}>
                                                <td className="px-5 py-3.5">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-8 h-8 rounded-lg bg-[#013981] flex items-center justify-center text-white font-mono font-semibold text-xs shrink-0">
                                                            {mhsName.substring(0, 1).toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="font-semibold text-[#0b0b0c]">
                                                                {mhsName}
                                                            </p>
                                                            <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                                {laporan.mahasiswa?.nim || '-'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-5 py-3.5 font-medium text-[#0b0b0c]">
                                                    {matkulName}
                                                </td>
                                                <td className="px-5 py-3.5 text-center">
                                                    <span className="font-mono px-2.5 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] font-semibold">
                                                        {laporan.minggu_ke}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-3.5 text-center">
                                                    <span
                                                        className={`font-mono px-2.5 py-0.5 rounded-full text-[10px] font-medium ${badge.cls}`}
                                                    >
                                                        {badge.label}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-3.5 text-center font-mono text-[#6a6f7c]">
                                                    {laporan.updated_at
                                                        ? new Date(laporan.updated_at).toLocaleDateString(
                                                              'id-ID',
                                                          )
                                                        : '-'}
                                                </td>
                                                <td className="px-5 py-3.5 text-center">
                                                    <div className="flex items-center justify-center gap-2">
                                                        <Link
                                                            href={`/supervisi-mingguan/${laporan.id}`}
                                                            className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] text-xs font-medium hover:bg-[#f4f4f5]"
                                                        >
                                                            <span className="material-symbols-outlined text-sm">
                                                                visibility
                                                            </span>
                                                            Detail
                                                        </Link>
                                                        {laporan.pdf_path && (
                                                            <a
                                                                href={`/storage/${laporan.pdf_path}`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] text-xs font-medium hover:bg-[#f4f4f5]"
                                                            >
                                                                <span className="material-symbols-outlined text-sm">
                                                                    picture_as_pdf
                                                                </span>
                                                                PDF
                                                            </a>
                                                        )}
                                                        {laporan.status === 'diajukan' && (
                                                            <button
                                                                type="button"
                                                                onClick={() => openModal(laporan)}
                                                                className="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold transition cursor-pointer"
                                                            >
                                                                <span className="material-symbols-outlined text-sm">
                                                                    rate_review
                                                                </span>
                                                                Verifikasi
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* MODAL VERIFIKASI */}
                {activeModal && (
                    <div
                        className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4"
                        onClick={() => setActiveModal(null)}
                    >
                        <div
                            className="bg-white rounded-2xl shadow-surface-lg border border-[#e4e4e7] w-full max-w-md p-6"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-base font-semibold text-[#0b0b0c]">
                                    Verifikasi Laporan Supervisi
                                </h3>
                                <button
                                    type="button"
                                    onClick={() => setActiveModal(null)}
                                    className="text-[#6a6f7c] hover:text-[#0b0b0c]"
                                >
                                    <span className="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-xl p-3.5 mb-4 space-y-0.5">
                                <p className="font-mono text-[10px] text-[#6a6f7c] uppercase">Mahasiswa</p>
                                <p className="font-semibold text-sm text-[#0b0b0c]">
                                    {activeModal.mahasiswa?.user?.name || activeModal.mahasiswa?.nama || '-'}
                                </p>
                                <p className="text-xs text-[#6a6f7c]">
                                    Minggu ke-{activeModal.minggu_ke} —{' '}
                                    {activeModal.supervisi_matkul?.mata_kuliah?.nama_matkul ||
                                        activeModal.matkul?.nama_matkul ||
                                        '-'}
                                </p>
                            </div>
                            <form onSubmit={handleVerifikasi} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                        Keputusan
                                    </label>
                                    <div className="grid grid-cols-2 gap-2.5">
                                        <button
                                            type="button"
                                            onClick={() => setStatus('disetujui')}
                                            className={`py-2.5 px-3 rounded-lg border text-xs font-semibold cursor-pointer ${
                                                status === 'disetujui'
                                                    ? 'border-[#15803d] bg-emerald-50 text-[#15803d]'
                                                    : 'border-[#e4e4e7] bg-[#fafafa] text-[#6a6f7c]'
                                            }`}
                                        >
                                            Setujui
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setStatus('ditolak')}
                                            className={`py-2.5 px-3 rounded-lg border text-xs font-semibold cursor-pointer ${
                                                status === 'ditolak'
                                                    ? 'border-[#b91c1c] bg-red-50 text-[#b91c1c]'
                                                    : 'border-[#e4e4e7] bg-[#fafafa] text-[#6a6f7c]'
                                            }`}
                                        >
                                            Tolak
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                        Catatan (opsional)
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={catatanDosen}
                                        onChange={(e) => setCatatanDosen(e.target.value)}
                                        placeholder="Tambahkan catatan atau revisi untuk mahasiswa..."
                                        className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white text-xs focus:outline-none focus:border-[#013981] resize-none"
                                    />
                                </div>
                                <div className="flex gap-2.5 pt-1">
                                    <button
                                        type="button"
                                        onClick={() => setActiveModal(null)}
                                        className="flex-1 py-2.5 rounded-lg border border-[#e4e4e7] text-[#6a6f7c] font-semibold text-xs hover:bg-[#f4f4f5] cursor-pointer"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        className="flex-1 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white font-semibold text-xs transition cursor-pointer"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
