import React, { useState } from 'react';
import { usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ManagerDashboard({
    proyekList = [],
    totalProyek = 0,
    proyekDisetujui = 0,
    proyekPending = 0,
    totalMahasiswa = 0,
}) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const [openTeams, setOpenTeams] = useState({});

    const toggleTeam = (id) => {
        setOpenTeams((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    const getStatusBadge = (status) => {
        if (status === 'approved') {
            return {
                cls: 'bg-emerald-50 border border-emerald-200 text-[#15803d]',
                label: 'Disetujui',
            };
        }
        if (status === 'rejected') {
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
        <AppLayout title="Dashboard Manager Proyek">
            <div className="space-y-6 max-w-[1400px] mx-auto">
                {/* HEADER */}
                <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <p className="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1">
                            MANAJEMEN PROYEK PBL
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Dashboard Manager Proyek
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Selamat datang, <span className="font-medium text-[#0b0b0c]">{user?.name}</span>.
                            Kelola proyek dan verifikasi logbook tim.
                        </p>
                    </div>
                    <a
                        href="/pengajuan-proyek/create"
                        className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold transition-colors w-fit"
                    >
                        <span className="material-symbols-outlined text-[16px]">add</span>
                        <span>Ajukan Proyek</span>
                    </a>
                </div>

                {/* STAT CARDS */}
                <section className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Total Proyek
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#6a6f7c]">
                                folder_open
                            </span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {totalProyek}
                        </h3>
                    </div>

                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Disetujui
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#15803d]">
                                check_circle
                            </span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {proyekDisetujui}
                        </h3>
                    </div>

                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Menunggu
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#b45309]">pending</span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {proyekPending}
                        </h3>
                    </div>

                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Total Mahasiswa
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#6a6f7c]">group</span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {totalMahasiswa}
                        </h3>
                    </div>
                </section>

                {/* SHORTCUT VERIFIKASI LOGBOOK */}
                <section className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a
                        href="/logbook-mingguan/verifikasi"
                        className="flex items-center gap-4 bg-white p-5 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors group"
                    >
                        <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                            <span className="material-symbols-outlined text-[20px]">rate_review</span>
                        </div>
                        <div>
                            <p className="font-semibold text-sm text-[#0b0b0c]">Verifikasi Logbook Mingguan</p>
                            <p className="text-xs text-[#6a6f7c] mt-0.5">
                                Review &amp; ACC rekap mingguan mahasiswa
                            </p>
                        </div>
                        <span className="material-symbols-outlined text-[#6a6f7c] group-hover:text-[#0b0b0c] ml-auto transition-colors">
                            chevron_right
                        </span>
                    </a>

                    <a
                        href="/logbook-harian-rekap"
                        className="flex items-center gap-4 bg-white p-5 rounded-xl border border-[#e4e4e7] hover:border-[#013981] shadow-surface transition-colors group"
                    >
                        <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center text-[#0b0b0c] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                            <span className="material-symbols-outlined text-[20px]">calendar_month</span>
                        </div>
                        <div>
                            <p className="font-semibold text-sm text-[#0b0b0c]">Rekap Logbook Harian</p>
                            <p className="text-xs text-[#6a6f7c] mt-0.5">
                                Pantau aktivitas harian mahasiswa per minggu
                            </p>
                        </div>
                        <span className="material-symbols-outlined text-[#6a6f7c] group-hover:text-[#0b0b0c] ml-auto transition-colors">
                            chevron_right
                        </span>
                    </a>
                </section>

                {/* PROYEK LIST */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                    <div className="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                        <h4 className="text-sm font-semibold text-[#0b0b0c]">Daftar Proyek Saya</h4>
                        <a
                            href="/pengajuan-proyek"
                            className="font-mono text-xs text-[#6a6f7c] hover:text-[#0b0b0c] transition-colors"
                        >
                            Lihat semua →
                        </a>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th className="px-5 py-3 w-12">#</th>
                                    <th className="px-5 py-3">Judul Proyek</th>
                                    <th className="px-5 py-3">Periode</th>
                                    <th className="px-5 py-3 text-center">Tim</th>
                                    <th className="px-5 py-3 text-center">Status</th>
                                    <th className="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f4f4f5]">
                                {proyekList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-5 py-12 text-center">
                                            <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                                                work_off
                                            </span>
                                            <p className="text-xs text-[#6a6f7c]">Belum ada proyek yang diajukan.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    proyekList.map((proyek, idx) => {
                                        const teamCount = proyek.mahasiswa?.length || 0;
                                        const isOpen = Boolean(openTeams[proyek.id]);
                                        const badge = getStatusBadge(proyek.status);
                                        return (
                                            <React.Fragment key={proyek.id}>
                                                <tr>
                                                    <td className="px-5 py-4 font-mono text-xs text-[#6a6f7c]">
                                                        {idx + 1}
                                                    </td>
                                                    <td className="px-5 py-4">
                                                        <p className="font-semibold text-[#0b0b0c] text-xs">
                                                            {proyek.judul_proyek}
                                                        </p>
                                                    </td>
                                                    <td className="px-5 py-4">
                                                        <p className="font-mono text-xs text-[#0b0b0c]">
                                                            {proyek.tanggal_mulai || '-'}
                                                        </p>
                                                        <p className="font-mono text-[10px] text-[#6a6f7c]">
                                                            s.d. {proyek.tanggal_selesai || '-'}
                                                        </p>
                                                    </td>
                                                    <td className="px-5 py-4 text-center">
                                                        {teamCount > 0 ? (
                                                            <button
                                                                type="button"
                                                                onClick={() => toggleTeam(proyek.id)}
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 bg-[#f4f4f5] hover:bg-[#e4e4e7] border border-[#e4e4e7] text-[#0b0b0c] rounded-md font-mono text-xs transition-colors cursor-pointer"
                                                            >
                                                                {teamCount} Org
                                                                <span className="material-symbols-outlined text-[14px]">
                                                                    {isOpen ? 'expand_less' : 'expand_more'}
                                                                </span>
                                                            </button>
                                                        ) : (
                                                            <span className="text-xs text-[#a1a1aa]">-</span>
                                                        )}
                                                    </td>
                                                    <td className="px-5 py-4 text-center">
                                                        <span
                                                            className={`font-mono inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-medium ${badge.cls}`}
                                                        >
                                                            {badge.label}
                                                        </span>
                                                    </td>
                                                    <td className="px-5 py-4 text-center">
                                                        <a
                                                            href={`/pengajuan-proyek/${proyek.id}`}
                                                            className="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] hover:bg-[#013981] hover:text-white hover:border-[#013981] transition-colors"
                                                            title="Detail"
                                                        >
                                                            <span className="material-symbols-outlined text-[16px]">
                                                                visibility
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                {teamCount > 0 && isOpen && (
                                                    <tr className="bg-[#fafafa]">
                                                        <td colSpan={6} className="px-5 py-4">
                                                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                                                {proyek.mahasiswa.map((m) => (
                                                                    <div
                                                                        key={m.id}
                                                                        className="flex items-center gap-2.5 bg-white border border-[#e4e4e7] rounded-lg p-2.5"
                                                                    >
                                                                        <div className="w-7 h-7 rounded-md bg-[#013981] flex items-center justify-center text-white font-mono text-xs font-medium shrink-0">
                                                                            {(m.nama || '?').substring(0, 1).toUpperCase()}
                                                                        </div>
                                                                        <div className="min-w-0 flex-1">
                                                                            <p className="text-xs font-medium text-[#0b0b0c] truncate">
                                                                                {m.nama}
                                                                            </p>
                                                                            <p className="font-mono text-[10px] text-[#6a6f7c]">
                                                                                {m.nim} · {m.pivot?.prodi || '-'}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </React.Fragment>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
