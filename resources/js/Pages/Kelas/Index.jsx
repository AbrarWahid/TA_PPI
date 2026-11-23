import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function KelasIndex({ kelas = [], periodeAktif = null }) {
    const totalMahasiswa = kelas.reduce((sum, k) => sum + Number(k.mahasiswa_count || 0), 0);

    return (
        <AppLayout title="Data Kelas">
            <div className="flex-1 flex flex-col">
                {/* HEADER */}
                <div className="flex justify-between items-end mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Master Data <span className="text-[#2dce89]">Kelas</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            {kelas.length} Kelas Terdaftar
                        </p>
                    </div>
                    <Link
                        href="/kelas/create"
                        className="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20"
                    >
                        <span className="material-symbols-outlined text-base">add</span> TAMBAH KELAS
                    </Link>
                </div>

                {/* STAT STRIP */}
                <div className="grid grid-cols-3 gap-4 mb-8">
                    <div className="bg-[#004d4d] text-white p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
                        <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Kelas</p>
                        <p className="text-5xl font-black italic tracking-tighter">{kelas.length}</p>
                    </div>
                    <div className="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
                        <p className="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Periode Aktif</p>
                        <p className="text-lg font-black italic tracking-tighter text-[#004d4d] leading-tight">
                            {periodeAktif?.nama_periode ?? '-'}
                        </p>
                    </div>
                    <div className="bg-white border-2 border-teal-100 p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
                        <p className="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
                        <p className="text-4xl font-black italic tracking-tighter text-[#004d4d]">
                            {totalMahasiswa}
                        </p>
                    </div>
                </div>

                {/* TABLE */}
                <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100">
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">#</th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Kelas</th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Jumlah Mahasiswa</th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {kelas.length > 0 ? (
                                kelas.map((k, idx) => (
                                    <tr key={k.id} className="hover:bg-teal-50/30 transition-colors group">
                                        <td className="px-8 py-5 text-[10px] font-black text-gray-300">{idx + 1}</td>

                                        <td className="px-8 py-5">
                                            <div className="flex items-center gap-4">
                                                <div className="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white shadow-md flex-shrink-0">
                                                    <span className="material-symbols-outlined text-xl">class</span>
                                                </div>
                                                <div>
                                                    <span className="font-black text-[#004d4d] block leading-none">{k.nama_kelas}</span>
                                                    <span className="text-[10px] text-gray-400 font-medium mt-0.5 block uppercase tracking-wider">
                                                        {k.kode_kelas}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td className="px-8 py-5 text-center">
                                            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-700 border border-teal-100 rounded-xl text-[10px] font-black">
                                                <span className="material-symbols-outlined text-sm">group</span>
                                                {k.mahasiswa_count ?? 0} Mahasiswa
                                            </span>
                                        </td>

                                        <td className="px-8 py-5 text-center">
                                            <Link
                                                href={`/kelas/${k.id}`}
                                                className="inline-flex items-center gap-1.5 px-4 py-2 bg-[#004d4d] text-[#7fffd4] rounded-xl text-[10px] font-black hover:scale-105 transition-all"
                                                title="Detail"
                                            >
                                                <span className="material-symbols-outlined text-sm">visibility</span> DETAIL
                                            </Link>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={4} className="px-8 py-20 text-center opacity-40">
                                        <span className="material-symbols-outlined text-5xl text-gray-300">class</span>
                                        <p className="font-black italic text-gray-400 mt-2">Belum ada data kelas.</p>
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
