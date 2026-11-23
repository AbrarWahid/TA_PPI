import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function PenilaianIndexManager({ penilaian = [], bobotManager = 55 }) {
    return (
        <AppLayout title="Penilaian Manager">
            <div className="max-w-[1400px] mx-auto space-y-6">
                {/* HEADER */}
                <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black italic uppercase text-[#004d4d] tracking-tighter">
                            Penilaian <span className="text-[#2dce89]">Manager</span>
                        </h1>
                        <p className="text-sm text-slate-500 mt-1 font-bold">
                            {penilaian.length} data penilaian telah terekam.
                        </p>
                    </div>
                    <Link
                        href="/penilaian/manager/create"
                        className="flex items-center gap-2 px-5 py-3 bg-[#004d4d] hover:bg-[#003d3d] text-white font-black rounded-2xl text-sm shadow-lg hover:-translate-y-0.5 transition-all w-fit"
                    >
                        <span className="material-symbols-outlined text-base">add</span> Tambah Penilaian
                    </Link>
                </div>

                {/* TABEL BAKU */}
                <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead className="bg-slate-50/80 border-b border-gray-100">
                                <tr>
                                    <th className="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 w-12">
                                        #
                                    </th>
                                    <th className="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        Mahasiswa
                                    </th>
                                    <th className="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        Proyek
                                    </th>
                                    <th className="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">
                                        Nilai Skala 100
                                    </th>
                                    <th className="px-7 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {penilaian.length > 0 ? (
                                    penilaian.map((p, i) => {
                                        const nilai = Number(p.nilai_manager || 0);
                                        const judulProyek =
                                            p.pengajuan_proyek?.judul_proyek ??
                                            p.pengajuanProyek?.judul_proyek ??
                                            '-';

                                        return (
                                            <tr key={p.id} className="hover:bg-slate-50/50 transition-colors">
                                                <td className="px-7 py-5 text-sm font-bold text-slate-400">
                                                    {i + 1}
                                                </td>
                                                <td className="px-7 py-5">
                                                    <p className="font-black text-[#004d4d] text-sm">
                                                        {p.mahasiswa?.nama ?? '-'}
                                                    </p>
                                                    <p className="text-[10px] font-bold text-slate-400 mt-0.5">
                                                        {p.mahasiswa?.nim ?? '-'}
                                                    </p>
                                                </td>
                                                <td className="px-7 py-5">
                                                    <span className="inline-flex px-3 py-1.5 bg-teal-50 text-[#004d4d] rounded-lg text-[10px] font-black max-w-[200px] truncate">
                                                        {judulProyek}
                                                    </span>
                                                </td>
                                                <td className="px-7 py-5 text-center">
                                                    <span className="font-black text-[#004d4d] text-lg">
                                                        {nilai.toFixed(1)}
                                                    </span>
                                                    <span className="block text-[10px] text-slate-400 font-bold mt-0.5">
                                                        / 100 &nbsp;(≈{' '}
                                                        {(nilai * (bobotManager / 100)).toFixed(1)} / {bobotManager})
                                                    </span>
                                                </td>
                                                <td className="px-7 py-5 text-center">
                                                    <span className="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl">
                                                        <span className="material-symbols-outlined text-[14px]">
                                                            done_all
                                                        </span>{' '}
                                                        Selesai
                                                    </span>
                                                </td>
                                            </tr>
                                        );
                                    })
                                ) : (
                                    <tr>
                                        <td colSpan={5} className="px-7 py-16 text-center">
                                            <span className="material-symbols-outlined text-4xl text-slate-300 block mb-2">
                                                grade
                                            </span>
                                            <p className="text-sm font-bold text-slate-400">Belum ada penilaian.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
