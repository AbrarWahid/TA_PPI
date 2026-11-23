import React from 'react';
import { usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function MahasiswaDashboard({
    mhs,
    nilaiAkhir,
    grade = '-',
    logbookTerbaru = [],
    proyekDitugaskan = [],
    supervisiList = [],
    lbStats = { total: 0, disetujui: 0, pending: 0, ditolak: 0 },
    svStats = { total: 0, disetujui: 0, pending: 0, ditolak: 0 },
}) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const quickActions = [
        ['/logbook-harian/create', 'edit_note', 'Input Logbook', 'Catat aktivitas harian'],
        ['/supervisi-harian', 'fact_check', 'Supervisi', 'Cek status supervisi'],
        ['/logbook-harian', 'history', 'Riwayat Logbook', 'Lihat seluruh catatan'],
        ['/penilaian', 'analytics', 'Nilai Saya', 'Lihat rekap penilaian'],
    ];

    const getStatusBadge = (status) => {
        if (status === 'disetujui') {
            return {
                cls: 'bg-emerald-50 border-emerald-200 text-[#15803d]',
                label: 'Disetujui',
            };
        }
        if (status === 'ditolak') {
            return {
                cls: 'bg-red-50 border-red-200 text-[#b91c1c]',
                label: 'Ditolak',
            };
        }
        return {
            cls: 'bg-amber-50 border-amber-200 text-[#b45309]',
            label: 'Menunggu',
        };
    };

    return (
        <AppLayout title="Dashboard Mahasiswa">
            <div className="space-y-6 max-w-[1400px] mx-auto">
                {/* TOP OVERVIEW CARD */}
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 sm:p-7 flex flex-col lg:flex-row justify-between gap-6 shadow-surface">
                    <div className="flex-1 flex flex-col justify-between">
                        <div>
                            <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-[0.1em] mb-1">
                                PORTAL MAHASISWA
                            </p>
                            <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                                {user?.name}
                            </h1>
                            <p className="font-mono text-xs text-[#6a6f7c] mt-1">
                                NIM · {mhs?.nim || '-'}
                            </p>
                        </div>
                        <div className="mt-6 pt-5 border-t border-[#f4f4f5]">
                            <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider mb-1.5">
                                Nilai Akhir PBL
                            </p>
                            {nilaiAkhir !== null && nilaiAkhir !== undefined ? (
                                <div className="flex items-baseline gap-3">
                                    <span className="text-4xl font-semibold font-mono text-[#0b0b0c] leading-none">
                                        {Number(nilaiAkhir).toFixed(1)}
                                    </span>
                                    <span className="font-mono px-2.5 py-0.5 bg-[#013981] text-white font-medium rounded-full text-xs">
                                        Grade {grade}
                                    </span>
                                </div>
                            ) : (
                                <>
                                    <p className="text-2xl font-semibold font-mono text-[#a1a1aa] leading-none">
                                        —
                                    </p>
                                    <p className="text-xs text-[#6a6f7c] mt-1">
                                        Nilai akhir belum dipublikasikan
                                    </p>
                                </>
                            )}
                        </div>
                    </div>

                    <div className="hidden lg:block w-px bg-[#e4e4e7]" />

                    <div className="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {/* Stat Logbook */}
                        <div className="bg-[#fafafa] rounded-xl p-5 border border-[#e4e4e7]">
                            <div className="flex items-center justify-between mb-3">
                                <span className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Logbook Harian
                                </span>
                                <span className="material-symbols-outlined text-[#6a6f7c] text-[18px]">
                                    menu_book
                                </span>
                            </div>
                            <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mb-4">
                                {lbStats.total ?? 0}
                            </p>
                            <div className="grid grid-cols-3 gap-2 text-xs pt-3 border-t border-[#e4e4e7]">
                                <div>
                                    <span className="font-mono font-semibold text-[#15803d] block">
                                        {lbStats.disetujui ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Disetujui</span>
                                </div>
                                <div>
                                    <span className="font-mono font-semibold text-[#b45309] block">
                                        {lbStats.pending ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Menunggu</span>
                                </div>
                                <div>
                                    <span className="font-mono font-semibold text-[#b91c1c] block">
                                        {lbStats.ditolak ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Ditolak</span>
                                </div>
                            </div>
                        </div>

                        {/* Stat Supervisi */}
                        <div className="bg-[#fafafa] rounded-xl p-5 border border-[#e4e4e7]">
                            <div className="flex items-center justify-between mb-3">
                                <span className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Supervisi Harian
                                </span>
                                <span className="material-symbols-outlined text-[#6a6f7c] text-[18px]">
                                    fact_check
                                </span>
                            </div>
                            <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mb-4">
                                {svStats.total ?? 0}
                            </p>
                            <div className="grid grid-cols-3 gap-2 text-xs pt-3 border-t border-[#e4e4e7]">
                                <div>
                                    <span className="font-mono font-semibold text-[#15803d] block">
                                        {svStats.disetujui ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Disetujui</span>
                                </div>
                                <div>
                                    <span className="font-mono font-semibold text-[#b45309] block">
                                        {svStats.pending ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Menunggu</span>
                                </div>
                                <div>
                                    <span className="font-mono font-semibold text-[#b91c1c] block">
                                        {svStats.ditolak ?? 0}
                                    </span>
                                    <span className="text-[#6a6f7c] text-[10px]">Ditolak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* QUICK ACTIONS */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                    {quickActions.map(([qaUrl, qaIcon, qaTitle, qaSub]) => (
                        <a
                            key={qaTitle}
                            href={qaUrl}
                            className="bg-white p-4 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors flex items-center gap-3.5 group"
                        >
                            <div className="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                                <span className="material-symbols-outlined text-[18px]">{qaIcon}</span>
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-semibold text-[#0b0b0c] truncate">{qaTitle}</p>
                                <p className="text-[11px] text-[#6a6f7c] truncate">{qaSub}</p>
                            </div>
                        </a>
                    ))}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {/* PROYEK SAYA */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
                        <div className="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                            <h4 className="text-sm font-semibold text-[#0b0b0c]">Proyek Saya</h4>
                            <span className="font-mono text-xs text-[#6a6f7c]">
                                {proyekDitugaskan.length} proyek
                            </span>
                        </div>
                        <div className="flex-1 divide-y divide-[#f4f4f5]">
                            {proyekDitugaskan.length === 0 ? (
                                <div className="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                                    Belum ada proyek yang ditugaskan.
                                </div>
                            ) : (
                                proyekDitugaskan.map((proyek) => (
                                    <div
                                        key={proyek.id}
                                        className="px-5 py-4 hover:bg-[#fafafa] transition-colors"
                                    >
                                        <div className="flex items-center gap-3.5">
                                            <div className="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] shrink-0">
                                                <span className="material-symbols-outlined text-[18px]">
                                                    engineering
                                                </span>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="font-semibold text-[#0b0b0c] text-xs truncate">
                                                    {proyek.judul_proyek}
                                                </p>
                                                <p className="text-xs text-[#6a6f7c] mt-0.5">
                                                    Manager: {proyek.manager?.name || '-'}
                                                </p>
                                                <p className="font-mono text-[10px] text-[#6a6f7c] mt-0.5">
                                                    {proyek.tanggal_mulai_formatted || proyek.tanggal_mulai || '-'} —{' '}
                                                    {proyek.tanggal_selesai_formatted || proyek.tanggal_selesai || '-'}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>

                    {/* SUPERVISI MATA KULIAH */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
                        <div className="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                            <h4 className="text-sm font-semibold text-[#0b0b0c]">Supervisi Mata Kuliah</h4>
                            <span className="font-mono text-xs text-[#6a6f7c]">
                                {supervisiList.length} matkul
                            </span>
                        </div>
                        <div className="flex-1 divide-y divide-[#f4f4f5]">
                            {supervisiList.length === 0 ? (
                                <div className="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                                    Belum ada supervisi yang ditugaskan.
                                </div>
                            ) : (
                                supervisiList.map((s) => (
                                    <div key={s.id} className="px-5 py-4 hover:bg-[#fafafa] transition-colors">
                                        <div className="flex items-center gap-3.5">
                                            <div className="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] shrink-0">
                                                <span className="material-symbols-outlined text-[18px]">
                                                    menu_book
                                                </span>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="font-semibold text-[#0b0b0c] text-xs">
                                                    {s.mata_kuliah?.nama_matkul || '-'}
                                                </p>
                                                <p className="font-mono text-[11px] text-[#6a6f7c] mt-0.5">
                                                    {s.mata_kuliah?.kode_matkul || ''} · {s.mata_kuliah?.sks || ''} SKS
                                                </p>
                                            </div>
                                            <div className="text-right shrink-0">
                                                <p className="text-xs font-medium text-[#0b0b0c]">
                                                    {s.dosen?.nama_dosen || '-'}
                                                </p>
                                                <p className="text-[11px] text-[#6a6f7c]">Dosen Pengampu</p>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>
                </div>

                {/* LOGBOOK HARIAN TERBARU */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                    <div className="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                        <h4 className="text-sm font-semibold text-[#0b0b0c]">Logbook Terbaru</h4>
                        <a
                            href="/logbook-harian"
                            className="font-mono text-xs text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors"
                        >
                            Lihat semua →
                        </a>
                    </div>
                    <div className="divide-y divide-[#f4f4f5]">
                        {logbookTerbaru.length === 0 ? (
                            <div className="px-5 py-10 text-center text-[#6a6f7c] text-xs">
                                Belum ada catatan logbook.{' '}
                                <a
                                    href="/logbook-harian/create"
                                    className="text-[#0b0b0c] font-medium hover:underline ml-1"
                                >
                                    Buat sekarang →
                                </a>
                            </div>
                        ) : (
                            logbookTerbaru.map((logbook) => {
                                const badge = getStatusBadge(logbook.status_verifikasi);
                                const dateObj = logbook.tanggal ? new Date(logbook.tanggal) : null;
                                const dayNum = dateObj ? String(dateObj.getDate()).padStart(2, '0') : '-';
                                const dateStr = dateObj
                                    ? dateObj.toLocaleDateString('id-ID', {
                                          day: '2-digit',
                                          month: 'short',
                                          year: 'numeric',
                                      })
                                    : logbook.tanggal;
                                return (
                                    <div
                                        key={logbook.id}
                                        className="px-5 py-3.5 hover:bg-[#fafafa] transition-colors"
                                    >
                                        <div className="flex items-center gap-3.5">
                                            <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex flex-col items-center justify-center text-[#0b0b0c] shrink-0">
                                                <span className="font-mono text-[9px] uppercase text-[#6a6f7c]">
                                                    {logbook.hari
                                                        ? logbook.hari.substring(0, 3)
                                                        : `M${logbook.minggu_ke}`}
                                                </span>
                                                <span className="font-mono text-xs font-semibold leading-none mt-0.5">
                                                    {dayNum}
                                                </span>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="font-medium text-[#0b0b0c] text-xs line-clamp-1">
                                                    {logbook.aktivitas || 'Aktivitas tidak ada'}
                                                </p>
                                                <p className="font-mono text-[11px] text-[#6a6f7c] mt-0.5">
                                                    Minggu ke-{logbook.minggu_ke} · {dateStr}
                                                </p>
                                            </div>
                                            <div className="shrink-0">
                                                <span
                                                    className={`font-mono text-[10px] font-medium px-2.5 py-0.5 rounded-full border ${badge.cls}`}
                                                >
                                                    {badge.label}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
