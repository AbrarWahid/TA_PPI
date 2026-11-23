import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookMingguanVerifikasi({ rekapList = [] }) {
    const [tab, setTab] = useState('semua');
    const [openForms, setOpenForms] = useState({});
    const [catatanMap, setCatatanMap] = useState({});

    const jumlahMenunggu = rekapList.filter((r) => r.status === 'diajukan').length;
    const jumlahDisetujui = rekapList.filter((r) => r.status === 'disetujui').length;
    const jumlahDitolak = rekapList.filter((r) => r.status === 'ditolak').length;

    const filtered = rekapList.filter((r) => {
        if (tab === 'semua') return true;
        if (tab === 'menunggu') return r.status === 'diajukan';
        return r.status === tab;
    });

    const toggleOpen = (id) => {
        setOpenForms((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    const handleVerifikasi = (id, aksi) => {
        router.patch(`/logbook-mingguan/${id}/verifikasi`, {
            aksi,
            catatan_dosen: catatanMap[id] || '',
        });
    };

    const getBadge = (status) => {
        if (status === 'disetujui') {
            return { cls: 'bg-emerald-50 text-[#15803d] border border-emerald-200', label: 'Disetujui' };
        }
        if (status === 'ditolak') {
            return { cls: 'bg-red-50 text-[#b91c1c] border border-red-200', label: 'Ditolak' };
        }
        return { cls: 'bg-amber-50 text-[#b45309] border border-amber-200', label: 'Menunggu' };
    };

    return (
        <AppLayout title="Verifikasi Logbook Mingguan">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                            MANAJEMEN PROYEK PBL
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">
                            Verifikasi Logbook Mingguan
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Daftar rekap logbook mingguan mahasiswa yang perlu diverifikasi.
                        </p>
                    </div>
                    <span className="font-mono text-xs text-[#0b0b0c] bg-white border border-[#e4e4e7] px-3.5 py-2 rounded-lg shadow-surface">
                        Total: {rekapList.length} Pengajuan
                    </span>
                </div>

                {/* STAT CARDS */}
                <div className="grid grid-cols-3 gap-4">
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Menunggu</p>
                        <h3 className="text-3xl font-semibold font-mono text-[#b45309] mt-2">
                            {jumlahMenunggu}
                        </h3>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Disetujui</p>
                        <h3 className="text-3xl font-semibold font-mono text-[#15803d] mt-2">
                            {jumlahDisetujui}
                        </h3>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">Ditolak</p>
                        <h3 className="text-3xl font-semibold font-mono text-[#b91c1c] mt-2">
                            {jumlahDitolak}
                        </h3>
                    </div>
                </div>

                {/* FILTER TAB */}
                <div className="flex gap-1.5 bg-white border border-[#e4e4e7] rounded-xl p-1.5 shadow-surface w-fit">
                    {[
                        ['semua', `Semua (${rekapList.length})`],
                        ['menunggu', `Menunggu (${jumlahMenunggu})`],
                        ['disetujui', `Disetujui (${jumlahDisetujui})`],
                        ['ditolak', `Ditolak (${jumlahDitolak})`],
                    ].map(([key, label]) => (
                        <button
                            key={key}
                            type="button"
                            onClick={() => setTab(key)}
                            className={`text-xs font-semibold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer ${
                                tab === key
                                    ? 'bg-[#013981] text-white'
                                    : 'text-[#6a6f7c] hover:text-[#0b0b0c]'
                            }`}
                        >
                            {label}
                        </button>
                    ))}
                </div>

                {/* LIST */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                    <div className="px-5 py-4 border-b border-[#e4e4e7]">
                        <h4 className="text-sm font-semibold text-[#0b0b0c]">Daftar Pengajuan</h4>
                    </div>

                    {filtered.length === 0 ? (
                        <div className="px-6 py-14 text-center">
                            <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                                task_alt
                            </span>
                            <p className="text-xs text-[#6a6f7c]">Tidak ada rekap pada kategori ini.</p>
                        </div>
                    ) : (
                        <div className="divide-y divide-[#f4f4f5]">
                            {filtered.map((rekap) => {
                                const badge = getBadge(rekap.status);
                                const isOpen = Boolean(openForms[rekap.id]);
                                const mhsName =
                                    rekap.mahasiswa?.user?.name || rekap.mahasiswa?.nama || '-';
                                return (
                                    <div key={rekap.id} className="px-5 py-4">
                                        <div className="flex items-center justify-between gap-4">
                                            <div className="flex items-center gap-3.5 flex-1 min-w-0">
                                                <div className="w-9 h-9 rounded-lg bg-[#013981] flex items-center justify-center text-white text-xs font-mono font-semibold shrink-0">
                                                    {mhsName.substring(0, 1).toUpperCase()}
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <p className="font-semibold text-xs text-[#0b0b0c]">
                                                        {mhsName}
                                                    </p>
                                                    <p className="font-mono text-[11px] text-[#6a6f7c] mt-0.5">
                                                        {rekap.proyek?.judul_proyek || '-'} · Minggu ke-
                                                        {rekap.minggu_ke}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-2 shrink-0">
                                                <span
                                                    className={`font-mono text-[10px] font-medium px-2.5 py-0.5 rounded-full ${badge.cls}`}
                                                >
                                                    {badge.label}
                                                </span>

                                                {rekap.pdf_path && (
                                                    <a
                                                        href={`/logbook-mingguan/${rekap.id}`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="inline-flex items-center gap-1 text-xs font-medium text-[#0b0b0c] hover:text-[#013981] border border-[#e4e4e7] bg-white px-3 py-1.5 rounded-lg transition-all"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            open_in_new
                                                        </span>
                                                        PDF
                                                    </a>
                                                )}

                                                {rekap.status === 'diajukan' && (
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleOpen(rekap.id)}
                                                        className="inline-flex items-center gap-1 text-xs font-semibold bg-[#013981] hover:bg-[#012b63] text-white px-3.5 py-1.5 rounded-lg transition-all cursor-pointer"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            rate_review
                                                        </span>
                                                        Verifikasi
                                                    </button>
                                                )}
                                            </div>
                                        </div>

                                        {rekap.status !== 'diajukan' && rekap.catatan_dosen && (
                                            <div className="mt-3 ml-12 pl-3 border-l-2 border-[#013981]">
                                                <p className="font-mono text-[10px] text-[#6a6f7c] uppercase">
                                                    Catatan Manager
                                                </p>
                                                <p className="text-xs text-[#0b0b0c]">{rekap.catatan_dosen}</p>
                                            </div>
                                        )}

                                        {rekap.status === 'diajukan' && isOpen && (
                                            <div className="mt-4 ml-12 bg-[#fafafa] border border-[#e4e4e7] rounded-xl p-4 space-y-3">
                                                <div>
                                                    <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                                        Catatan (opsional)
                                                    </label>
                                                    <textarea
                                                        rows={2}
                                                        value={catatanMap[rekap.id] || ''}
                                                        onChange={(e) =>
                                                            setCatatanMap((prev) => ({
                                                                ...prev,
                                                                [rekap.id]: e.target.value,
                                                            }))
                                                        }
                                                        className="w-full text-xs border border-[#e4e4e7] bg-white rounded-lg px-3.5 py-2.5 focus:outline-none focus:border-[#013981]"
                                                        placeholder="Tulis catatan untuk mahasiswa..."
                                                    />
                                                </div>
                                                <div className="flex gap-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => handleVerifikasi(rekap.id, 'disetujui')}
                                                        className="inline-flex items-center gap-1.5 bg-[#15803d] hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-all cursor-pointer"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            check_circle
                                                        </span>
                                                        ACC / Setujui
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleVerifikasi(rekap.id, 'ditolak')}
                                                        className="inline-flex items-center gap-1.5 bg-[#b91c1c] hover:bg-red-800 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-all cursor-pointer"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            cancel
                                                        </span>
                                                        Tolak
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleOpen(rekap.id)}
                                                        className="text-xs font-semibold text-[#6a6f7c] hover:text-[#0b0b0c] px-3.5 py-2 rounded-lg border border-[#e4e4e7] bg-white cursor-pointer"
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
