import React from 'react';
import { usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function DosenDashboard({
    totalMahasiswa = 0,
    laporanMenunggu = 0,
    penilaianSelesai = 0,
    laporanTerbaru = [],
    supervisiList = [],
}) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const roleName = user?.is_kps ? 'Koordinator Prodi' : 'Dosen Pengampu';

    const getLaporanStatus = (mahasiswaId) => {
        const lap = laporanTerbaru.find((l) => l.mahasiswa_id === mahasiswaId);
        if (!lap) {
            return {
                cls: 'bg-[#f4f4f5] border-[#e4e4e7] text-[#6a6f7c]',
                label: 'Belum Ada',
            };
        }
        if (lap.status_verifikasi === 'disetujui') {
            return {
                cls: 'bg-emerald-50 border-emerald-200 text-[#15803d]',
                label: 'Disetujui',
            };
        }
        if (lap.status_verifikasi === 'ditolak') {
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
        <AppLayout title={`Dashboard ${roleName}`}>
            <div className="space-y-6 max-w-[1400px] mx-auto">
                {/* HEADER */}
                <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <p className="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1">
                            PORTAL AKADEMIK
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Dashboard {roleName}
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Selamat datang, <span className="text-[#0b0b0c] font-medium">{user?.name}</span>.
                            Kelola verifikasi laporan &amp; penilaian mahasiswa.
                        </p>
                    </div>
                    <a
                        href="/penilaian/dosen/create"
                        className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold transition-colors w-fit"
                    >
                        <span className="material-symbols-outlined text-[16px]">add</span>
                        <span>Input Penilaian</span>
                    </a>
                </div>

                {/* STAT CARDS */}
                <section className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Total Supervisi
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#6a6f7c]">school</span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {supervisiList.length}
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

                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Laporan Menunggu
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#b45309]">
                                pending_actions
                            </span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {laporanMenunggu}
                        </h3>
                    </div>

                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col justify-between">
                        <div className="flex items-center justify-between mb-4">
                            <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                Penilaian Selesai
                            </p>
                            <span className="material-symbols-outlined text-[18px] text-[#15803d]">
                                edit_note
                            </span>
                        </div>
                        <h3 className="text-3xl font-semibold font-mono tracking-tight text-[#0b0b0c]">
                            {penilaianSelesai}
                        </h3>
                    </div>
                </section>

                {/* MAIN CONTENT */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* Quick Actions */}
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h4 className="font-mono text-[11px] font-medium text-[#6a6f7c] mb-4 uppercase tracking-wider">
                            Aksi Cepat
                        </h4>
                        <div className="space-y-2.5">
                            <a
                                href="/laporan"
                                className="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group"
                            >
                                <div className="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                                    <span className="material-symbols-outlined text-[16px]">description</span>
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs font-semibold text-[#0b0b0c]">Verifikasi Laporan</p>
                                    <p className="font-mono text-[10px] text-[#b45309] mt-0.5">
                                        {laporanMenunggu} menunggu review
                                    </p>
                                </div>
                                <span className="material-symbols-outlined text-[16px] text-[#6a6f7c]">
                                    chevron_right
                                </span>
                            </a>

                            <a
                                href="/penilaian"
                                className="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group"
                            >
                                <div className="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                                    <span className="material-symbols-outlined text-[16px]">edit_note</span>
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs font-semibold text-[#0b0b0c]">Input Nilai</p>
                                    <p className="text-[11px] text-[#6a6f7c] mt-0.5">
                                        Formulir penilaian mahasiswa
                                    </p>
                                </div>
                                <span className="material-symbols-outlined text-[16px] text-[#6a6f7c]">
                                    chevron_right
                                </span>
                            </a>

                            <a
                                href="/supervisi-mingguan/verifikasi/daftar"
                                className="flex items-center gap-3.5 p-3.5 rounded-lg bg-[#fafafa] hover:bg-[#f4f4f5] border border-[#e4e4e7] transition-colors group"
                            >
                                <div className="w-8 h-8 rounded-md bg-white border border-[#e4e4e7] flex items-center justify-center group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] transition-colors shrink-0">
                                    <span className="material-symbols-outlined text-[16px]">
                                        assignment_turned_in
                                    </span>
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs font-semibold text-[#0b0b0c]">Verifikasi Supervisi</p>
                                    <p className="text-[11px] text-[#6a6f7c] mt-0.5">Review supervisi mingguan</p>
                                </div>
                                <span className="material-symbols-outlined text-[16px] text-[#6a6f7c]">
                                    chevron_right
                                </span>
                            </a>
                        </div>
                    </div>

                    {/* Supervisi Aktif */}
                    <div className="lg:col-span-2 bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col">
                        <div className="px-5 py-4 border-b border-[#e4e4e7] flex justify-between items-center">
                            <h4 className="text-sm font-semibold text-[#0b0b0c]">Supervisi Aktif</h4>
                            <span className="font-mono text-xs px-2.5 py-0.5 bg-[#f4f4f5] border border-[#e4e4e7] rounded-full text-[#0b0b0c]">
                                {supervisiList.length} mahasiswa
                            </span>
                        </div>

                        <div className="overflow-x-auto flex-1">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th className="px-5 py-3 w-12">#</th>
                                        <th className="px-5 py-3">Mahasiswa</th>
                                        <th className="px-5 py-3">Mata Kuliah</th>
                                        <th className="px-5 py-3 text-center">Status Laporan</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#f4f4f5]">
                                    {supervisiList.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-5 py-12 text-center">
                                                <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                                                    school
                                                </span>
                                                <p className="text-xs text-[#6a6f7c]">
                                                    Belum ada mahasiswa yang disupervisi.
                                                </p>
                                            </td>
                                        </tr>
                                    ) : (
                                        supervisiList.map((supervisi, idx) => {
                                            const badge = getLaporanStatus(supervisi.mahasiswa_id);
                                            return (
                                                <tr key={supervisi.id}>
                                                    <td className="px-5 py-3.5 font-mono text-xs text-[#6a6f7c]">
                                                        {String(idx + 1).padStart(2, '0')}
                                                    </td>
                                                    <td className="px-5 py-3.5">
                                                        <div className="flex items-center gap-2.5">
                                                            <div className="w-7 h-7 rounded-md bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] flex items-center justify-center font-mono text-xs font-medium shrink-0">
                                                                {(supervisi.mahasiswa?.nama || '?')
                                                                    .substring(0, 1)
                                                                    .toUpperCase()}
                                                            </div>
                                                            <div>
                                                                <p className="font-medium text-xs text-[#0b0b0c]">
                                                                    {supervisi.mahasiswa?.nama || '-'}
                                                                </p>
                                                                <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                                    {supervisi.mahasiswa?.nim || '-'}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-5 py-3.5">
                                                        <span className="font-mono inline-flex px-2.5 py-0.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded text-[11px]">
                                                            {supervisi.mata_kuliah?.nama_matkul || '-'}
                                                        </span>
                                                    </td>
                                                    <td className="px-5 py-3.5 text-center">
                                                        <span
                                                            className={`font-mono inline-flex px-2.5 py-0.5 rounded-full border text-[10px] font-medium ${badge.cls}`}
                                                        >
                                                            {badge.label}
                                                        </span>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
