import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookHarianRekap({ logbook = [], mingguKe = 1, totalMinggu = 1 }) {
    const maxWeek = Math.max(Number(totalMinggu) || 1, Number(mingguKe) || 1);
    const weeks = Array.from({ length: maxWeek }, (_, i) => i + 1);

    const disetujuiCount = logbook.filter((e) => e.status_verifikasi === 'disetujui').length;
    const pendingCount = logbook.filter((e) => e.status_verifikasi === 'pending').length;

    const getStatusBadge = (status) => {
        if (status === 'disetujui') {
            return {
                cls: 'bg-emerald-50 border border-emerald-200 text-[#15803d]',
                label: 'Disetujui',
            };
        }
        if (status === 'ditolak') {
            return {
                cls: 'bg-red-50 border border-red-200 text-[#b91c1c]',
                label: 'Ditolak',
            };
        }
        return {
            cls: 'bg-amber-50 border border-amber-200 text-[#b45309]',
            label: 'Menunggu',
        };
    };

    return (
        <AppLayout title={`Rekap Minggu ke-${mingguKe}`}>
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center gap-4">
                    <Link
                        href="/logbook-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">
                            Rekap Minggu ke-{mingguKe}
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">
                            Gabungan aktivitas harian menjadi laporan mingguan
                        </p>
                    </div>
                </div>

                {/* Navigasi Minggu */}
                <div className="flex items-center gap-2 flex-wrap">
                    {weeks.map((w) => (
                        <Link
                            key={w}
                            href={`/logbook-harian-rekap?minggu_ke=${w}`}
                            className={`px-3.5 py-2 rounded-lg text-xs font-semibold transition-all ${
                                Number(mingguKe) === w
                                    ? 'bg-[#013981] text-white shadow-sm'
                                    : 'bg-white border border-[#e4e4e7] text-[#6a6f7c] hover:text-[#0b0b0c]'
                            }`}
                        >
                            Minggu {w}
                        </Link>
                    ))}
                </div>

                {logbook.length === 0 ? (
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface px-6 py-16 text-center">
                        <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                            inbox
                        </span>
                        <p className="text-[#6a6f7c] text-xs">Belum ada logbook untuk minggu ini.</p>
                    </div>
                ) : (
                    <>
                        {logbook.map((entry) => {
                            const badge = getStatusBadge(entry.status_verifikasi);
                            const ext = entry.dokumentasi
                                ? entry.dokumentasi.split('.').pop().toLowerCase()
                                : '';
                            const isImg = ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
                            return (
                                <div
                                    key={entry.id}
                                    className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden"
                                >
                                    <div className="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-[#eef4fc] flex items-center justify-center text-[#013981] font-mono font-semibold text-xs">
                                                {(entry.hari || '').substring(0, 3)}
                                            </div>
                                            <div>
                                                <p className="font-semibold text-[#0b0b0c] text-sm">
                                                    {entry.hari}
                                                </p>
                                                <p className="font-mono text-xs text-[#6a6f7c]">
                                                    {entry.tanggal
                                                        ? new Date(entry.tanggal).toLocaleDateString('id-ID', {
                                                              day: '2-digit',
                                                              month: 'short',
                                                              year: 'numeric',
                                                          })
                                                        : '-'}
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            className={`font-mono text-[10px] font-medium px-2.5 py-0.5 rounded-full ${badge.cls}`}
                                        >
                                            {badge.label}
                                        </span>
                                    </div>
                                    <div className="px-5 py-4">
                                        <p className="font-mono text-[10px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-1.5">
                                            Aktivitas
                                        </p>
                                        <p className="text-xs text-[#0b0b0c] leading-relaxed whitespace-pre-line">
                                            {entry.aktivitas}
                                        </p>

                                        {entry.dokumentasi && (
                                            <div className="mt-4 pt-4 border-t border-[#f4f4f5]">
                                                <p className="font-mono text-[10px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-2">
                                                    Dokumentasi
                                                </p>
                                                {isImg ? (
                                                    <img
                                                        src={`/storage/${entry.dokumentasi}`}
                                                        className="rounded-lg max-h-48 object-cover border border-[#e4e4e7]"
                                                        alt="Dokumentasi"
                                                    />
                                                ) : (
                                                    <a
                                                        href={`/storage/${entry.dokumentasi}`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-[#013981] hover:underline"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            picture_as_pdf
                                                        </span>
                                                        Lihat Dokumen
                                                    </a>
                                                )}
                                            </div>
                                        )}

                                        {entry.catatan_dosen && (
                                            <div className="mt-4 pt-4 border-t border-[#f4f4f5]">
                                                <p className="font-mono text-[10px] font-semibold text-[#b45309] uppercase tracking-wider mb-1.5">
                                                    Catatan Dosen
                                                </p>
                                                <p className="text-xs text-[#b45309] bg-amber-50 border border-amber-200 rounded-lg px-3.5 py-2.5">
                                                    {entry.catatan_dosen}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            );
                        })}

                        <div className="bg-[#013981] rounded-xl p-6 text-white">
                            <p className="text-white/75 font-mono text-[11px] font-semibold uppercase tracking-wider mb-3">
                                Ringkasan Minggu ke-{mingguKe}
                            </p>
                            <div className="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p className="text-3xl font-bold font-mono">{logbook.length}</p>
                                    <p className="text-white/70 text-[11px] font-medium mt-1">Hari Tercatat</p>
                                </div>
                                <div>
                                    <p className="text-3xl font-bold font-mono">{disetujuiCount}</p>
                                    <p className="text-white/70 text-[11px] font-medium mt-1">Disetujui</p>
                                </div>
                                <div>
                                    <p className="text-3xl font-bold font-mono">{pendingCount}</p>
                                    <p className="text-white/70 text-[11px] font-medium mt-1">Menunggu</p>
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
