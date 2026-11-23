import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function formatDate(d) {
    if (!d) return '-';
    const dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    const day = String(dt.getDate()).padStart(2, '0');
    const month = MONTHS[dt.getMonth()];
    const year = dt.getFullYear();
    return `${day} ${month} ${year}`;
}

export default function LaporanIndex({ laporan = [] }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isMahasiswa = user?.role === 'mahasiswa';

    return (
        <AppLayout title="Laporan">
            {/* HEADING */}
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-black italic uppercase text-[#004d4d]">
                        {isMahasiswa ? (
                            <>
                                Pengumpulan <span className="text-[#2dce89]">Laporan</span>
                            </>
                        ) : (
                            <>
                                Verifikasi <span className="text-[#2dce89]">Laporan</span>
                            </>
                        )}
                    </h1>
                    <p className="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
                        {isMahasiswa
                            ? 'Upload dan pantau status laporan kamu'
                            : 'Verifikasi laporan yang disubmit mahasiswa'}
                    </p>
                </div>
                {isMahasiswa && (
                    <Link
                        href="/laporan/create"
                        className="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition"
                    >
                        <span className="material-symbols-outlined text-base">upload_file</span>
                        Upload Laporan
                    </Link>
                )}
            </div>

            {/* TABLE */}
            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400">
                        Daftar Laporan
                    </p>
                    <span className="text-[10px] font-black text-gray-400">{laporan.length} entri</span>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-100">
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    No
                                </th>
                                {!isMahasiswa && (
                                    <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Mahasiswa
                                    </th>
                                )}
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Jenis Laporan
                                </th>
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Dosen Supervisi
                                </th>
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Tanggal Upload
                                </th>
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Status
                                </th>
                                <th className="text-left px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {laporan.length > 0 ? (
                                laporan.map((l, idx) => {
                                    const status = l.status_verifikasi ?? 'menunggu';
                                    const cfg =
                                        status === 'disetujui'
                                            ? {
                                                  cls: 'bg-emerald-50 text-emerald-700',
                                                  icon: 'check_circle',
                                                  label: 'Disetujui',
                                              }
                                            : status === 'ditolak'
                                              ? {
                                                    cls: 'bg-red-50 text-red-600',
                                                    icon: 'cancel',
                                                    label: 'Ditolak',
                                                }
                                              : {
                                                    cls: 'bg-amber-50 text-amber-600',
                                                    icon: 'hourglass_empty',
                                                    label: 'Menunggu',
                                                };

                                    return (
                                        <tr key={l.id} className="hover:bg-teal-50/30 transition-colors">
                                            <td className="px-6 py-4 text-[10px] font-black text-gray-300">
                                                {String(idx + 1).padStart(2, '0')}
                                            </td>

                                            {!isMahasiswa && (
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                                            {(l.mahasiswa?.nama ?? 'M')
                                                                .substring(0, 1)
                                                                .toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="font-black text-[#004d4d] text-sm">
                                                                {l.mahasiswa?.nama ?? '-'}
                                                            </p>
                                                            <p className="text-[10px] text-gray-400">
                                                                {l.mahasiswa?.nim ?? '-'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                            )}

                                            <td className="px-6 py-4">
                                                <span className="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black bg-teal-50 text-[#004d4d] uppercase tracking-wide">
                                                    <span className="material-symbols-outlined text-xs">
                                                        description
                                                    </span>
                                                    {l.jenis_laporan}
                                                </span>
                                            </td>

                                            <td className="px-6 py-4 text-sm text-slate-500 font-medium">
                                                {isMahasiswa
                                                    ? l.dosenSupervisi ?? '-'
                                                    : l.proyek?.judul_proyek ?? '-'}
                                            </td>

                                            <td className="px-6 py-4 text-xs text-slate-500 font-medium">
                                                {formatDate(l.created_at)}
                                            </td>

                                            <td className="px-6 py-4">
                                                <span
                                                    className={`inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black ${cfg.cls} uppercase tracking-wide`}
                                                >
                                                    <span className="material-symbols-outlined text-xs">
                                                        {cfg.icon}
                                                    </span>
                                                    {cfg.label}
                                                </span>
                                            </td>

                                            <td className="px-6 py-4">
                                                <Link
                                                    href={`/laporan/${l.id}`}
                                                    className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black bg-teal-50 text-[#004d4d] hover:bg-teal-100 transition uppercase tracking-wide"
                                                >
                                                    <span className="material-symbols-outlined text-sm">
                                                        visibility
                                                    </span>
                                                    Detail
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={7} className="px-6 py-20 text-center">
                                        <span className="material-symbols-outlined text-6xl text-gray-200 block mb-3">
                                            folder_open
                                        </span>
                                        <p className="text-sm text-gray-400 font-medium">
                                            Belum ada laporan
                                        </p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
