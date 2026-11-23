import React from 'react';
import { usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function AdminDashboard({ stats = {}, logbookPending = [], laporanPending = [] }) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const quickActions = [
        ['/approval-dosen', 'how_to_reg', 'Approval Akun', 'Kelola pendaftaran'],
        ['/pengajuan-proyek', 'approval', 'Approval Proyek', 'Review pengajuan'],
        ['/laporan-admin', 'monitoring', 'Monitoring', 'Progres aktivitas'],
        ['/penilaian', 'grade', 'Rekap Nilai', 'Asesmen & transkrip'],
    ];

    return (
        <AppLayout title="Dashboard Admin">
            <div className="max-w-[1400px] mx-auto space-y-6">
                {/* HEADER */}
                <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <p className="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1">
                            PUSAT KENDALI SISTEM
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">Command Center</h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Selamat datang kembali,{' '}
                            <span className="text-[#0b0b0c] font-medium">{user?.name}</span>.
                        </p>
                    </div>
                    <div className="flex items-center gap-2.5 flex-wrap">
                        {(stats.pending_akun || 0) > 0 && (
                            <a
                                href="/approval-dosen"
                                className="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 border border-amber-200 text-[#b45309] rounded-lg text-xs font-medium hover:bg-amber-100/70 transition-colors"
                            >
                                <span className="material-symbols-outlined text-[16px]">how_to_reg</span>
                                <span>{stats.pending_akun} Pending Akun</span>
                            </a>
                        )}
                        {(stats.pending_proyek || 0) > 0 && (
                            <a
                                href="/pengajuan-proyek"
                                className="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#e4e4e7] text-[#0b0b0c] rounded-lg text-xs font-medium hover:bg-[#f4f4f5] transition-colors"
                            >
                                <span className="material-symbols-outlined text-[16px]">pending_actions</span>
                                <span>{stats.pending_proyek} Proyek Pending</span>
                            </a>
                        )}
                    </div>
                </div>

                {/* STAT CARDS */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white border border-[#e4e4e7] rounded-xl p-5 flex flex-col justify-between shadow-surface">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Total Mahasiswa
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#6a6f7c]">group</span>
                        </div>
                        <div>
                            <h2 className="text-3xl font-semibold tracking-tight text-[#0b0b0c] font-mono">
                                {stats.total_mahasiswa ?? 0}
                            </h2>
                            <p className="text-[11px] text-[#6a6f7c] mt-1">Mahasiswa terdaftar</p>
                        </div>
                    </div>

                    <div className="bg-white border border-[#e4e4e7] rounded-xl p-5 flex flex-col justify-between shadow-surface">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Proyek Aktif
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#15803d]">
                                rocket_launch
                            </span>
                        </div>
                        <div>
                            <h2 className="text-3xl font-semibold tracking-tight text-[#0b0b0c] font-mono">
                                {stats.total_proyek_aktif ?? 0}
                            </h2>
                            <p className="text-[11px] text-[#6a6f7c] mt-1">Berstatus disetujui</p>
                        </div>
                    </div>

                    <div
                        className={`bg-white border ${
                            (stats.pending_akun || 0) > 0 ? 'border-amber-300' : 'border-[#e4e4e7]'
                        } rounded-xl p-5 flex flex-col justify-between shadow-surface`}
                    >
                        <div className="flex items-center justify-between mb-4">
                            <p
                                className={`font-mono text-[11px] uppercase tracking-wider ${
                                    (stats.pending_akun || 0) > 0 ? 'text-[#b45309]' : 'text-[#6a6f7c]'
                                }`}
                            >
                                Approval Akun
                            </p>
                            <span
                                className={`material-symbols-outlined text-[18px] ${
                                    (stats.pending_akun || 0) > 0 ? 'text-[#b45309]' : 'text-[#6a6f7c]'
                                }`}
                            >
                                how_to_reg
                            </span>
                        </div>
                        <div>
                            <h2
                                className={`text-3xl font-semibold tracking-tight font-mono ${
                                    (stats.pending_akun || 0) > 0 ? 'text-[#b45309]' : 'text-[#0b0b0c]'
                                }`}
                            >
                                {stats.pending_akun ?? 0}
                            </h2>
                            <p className="text-[11px] text-[#6a6f7c] mt-1">Menunggu persetujuan</p>
                        </div>
                    </div>

                    <div
                        className={`bg-white border ${
                            (stats.pending_proyek || 0) > 0 ? 'border-[#013981]' : 'border-[#e4e4e7]'
                        } rounded-xl p-5 flex flex-col justify-between shadow-surface`}
                    >
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Approval Proyek
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#6a6f7c]">approval</span>
                        </div>
                        <div>
                            <h2 className="text-3xl font-semibold tracking-tight text-[#0b0b0c] font-mono">
                                {stats.pending_proyek ?? 0}
                            </h2>
                            <p className="text-[11px] text-[#6a6f7c] mt-1">Menunggu review</p>
                        </div>
                    </div>
                </div>

                {/* PROGRESS BAR MONITORING */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] p-5 flex flex-col sm:flex-row sm:items-center gap-6 shadow-surface">
                    <div className="shrink-0">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                            Progres Penilaian
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c]">
                            {stats.progress_pct ?? 0}%
                        </p>
                    </div>
                    <div className="flex-1">
                        <div className="w-full h-2 rounded-full bg-[#f4f4f5] overflow-hidden">
                            <div
                                className="h-full rounded-full bg-[#013981] transition-all"
                                style={{ width: `${stats.progress_pct ?? 0}%` }}
                            />
                        </div>
                        <p className="text-xs text-[#6a6f7c] mt-2">
                            Proporsi mahasiswa yang telah memiliki komponen nilai lengkap (Manager + Dosen).
                        </p>
                    </div>
                    <a
                        href="/penilaian"
                        className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold transition-colors shrink-0"
                    >
                        <span>Lihat Rekap</span>
                        <span className="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>

                {/* AKTIVITAS TERBARU */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {/* Logbook Pending */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-[#e4e4e7]">
                            <div>
                                <p className="text-sm font-semibold text-[#0b0b0c]">Logbook Pending</p>
                                <p className="text-xs text-[#6a6f7c] mt-0.5">Rekap mingguan belum diverifikasi</p>
                            </div>
                            <a
                                href="/logbook-mingguan"
                                className="font-mono text-xs font-medium text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors"
                            >
                                Lihat semua →
                            </a>
                        </div>

                        {logbookPending.length === 0 ? (
                            <div className="px-5 py-12 text-center">
                                <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-1.5">
                                    task_alt
                                </span>
                                <p className="text-xs text-[#6a6f7c]">Semua logbook telah diverifikasi.</p>
                            </div>
                        ) : (
                            <ul className="divide-y divide-[#f4f4f5]">
                                {logbookPending.map((lb) => (
                                    <li
                                        key={lb.id}
                                        className="flex items-center justify-between px-5 py-3.5 hover:bg-[#fafafa] transition-colors"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-[#013981] flex items-center justify-center font-mono font-medium text-xs text-white shrink-0">
                                                {(lb.mahasiswa?.nama || '?').substring(0, 2).toUpperCase()}
                                            </div>
                                            <div>
                                                <p className="text-xs font-semibold text-[#0b0b0c]">
                                                    {lb.mahasiswa?.nama || '-'}
                                                </p>
                                                <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                    Minggu ke-{lb.minggu_ke}
                                                </p>
                                            </div>
                                        </div>
                                        <span className="font-mono px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 border border-amber-200 text-[#b45309]">
                                            Pending
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>

                    {/* Laporan Pending */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-[#e4e4e7]">
                            <div>
                                <p className="text-sm font-semibold text-[#0b0b0c]">Laporan Pending</p>
                                <p className="text-xs text-[#6a6f7c] mt-0.5">Laporan MK/PPI belum diverifikasi</p>
                            </div>
                            <a
                                href="/laporan"
                                className="font-mono text-xs font-medium text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors"
                            >
                                Lihat semua →
                            </a>
                        </div>

                        {laporanPending.length === 0 ? (
                            <div className="px-5 py-12 text-center">
                                <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-1.5">
                                    task_alt
                                </span>
                                <p className="text-xs text-[#6a6f7c]">Semua laporan telah diverifikasi.</p>
                            </div>
                        ) : (
                            <ul className="divide-y divide-[#f4f4f5]">
                                {laporanPending.map((lap) => (
                                    <li
                                        key={lap.id}
                                        className="flex items-center justify-between px-5 py-3.5 hover:bg-[#fafafa] transition-colors"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center font-mono font-medium text-xs text-[#0b0b0c] shrink-0">
                                                {(lap.mahasiswa?.nama || '?').substring(0, 2).toUpperCase()}
                                            </div>
                                            <div>
                                                <p className="text-xs font-semibold text-[#0b0b0c]">
                                                    {lap.mahasiswa?.nama || '-'}
                                                </p>
                                                <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                    {lap.jenis_laporan}
                                                </p>
                                            </div>
                                        </div>
                                        <span className="font-mono px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c]">
                                            Pending
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>

                {/* QUICK ACTIONS */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                    {quickActions.map(([qaUrl, qaIcon, qaTitle, qaSub]) => (
                        <a
                            key={qaTitle}
                            href={qaUrl}
                            className="flex items-center gap-3.5 p-4 bg-white rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors group"
                        >
                            <div className="w-9 h-9 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center text-[#0b0b0c] transition-colors shrink-0">
                                <span className="material-symbols-outlined text-[18px]">{qaIcon}</span>
                            </div>
                            <div className="min-w-0">
                                <p className="font-semibold text-xs text-[#0b0b0c] truncate">{qaTitle}</p>
                                <p className="text-[11px] text-[#6a6f7c] truncate">{qaSub}</p>
                            </div>
                        </a>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
