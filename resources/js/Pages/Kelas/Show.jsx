import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const prodiMap = {
    informatika: 'D4 TRIN – Informatika Industri',
    otomasi: 'D4 TRO – Teknik Rekayasa Otomasi',
    mekatronika: 'D4 TRMO – Teknik Rekayasa Mekatronika',
};

const prodiColor = {
    informatika: 'bg-blue-100 text-blue-700',
    otomasi: 'bg-emerald-100 text-emerald-700',
    mekatronika: 'bg-purple-100 text-purple-700',
};

export default function KelasShow({ kelas }) {
    const mahasiswaList = kelas?.mahasiswa || [];

    return (
        <AppLayout title="Detail Kelas">
            <div className="flex-1 flex flex-col">
                {/* HEADER */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/kelas"
                        className="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 hover:bg-teal-50 hover:border-teal-100 transition-all shadow-sm"
                    >
                        <span className="material-symbols-outlined text-[#004d4d]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Kelas <span className="text-[#2dce89]">{kelas?.nama_kelas}</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            {kelas?.kode_kelas} &middot; {mahasiswaList.length} Mahasiswa
                        </p>
                    </div>
                </div>

                {/* STAT CARDS */}
                <div className="grid grid-cols-3 gap-4 mb-8">
                    <div className="bg-[#004d4d] text-white p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
                        <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Mahasiswa</p>
                        <p className="text-5xl font-black italic tracking-tighter">{mahasiswaList.length}</p>
                    </div>
                    <div className="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
                        <p className="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Kode Kelas</p>
                        <p className="text-2xl font-black italic tracking-tighter text-[#004d4d]">{kelas?.kode_kelas}</p>
                    </div>
                    <div className="bg-white border border-gray-100 p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm">
                        <p className="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">Nama Kelas</p>
                        <p className="text-2xl font-black italic tracking-tighter text-[#004d4d]">{kelas?.nama_kelas}</p>
                    </div>
                </div>

                {/* TABLE */}
                <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div className="px-7 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h4 className="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                            <span className="material-symbols-outlined text-lg">group</span> Daftar Mahasiswa
                        </h4>
                        <span className="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-xl">
                            {mahasiswaList.length} Mahasiswa
                        </span>
                    </div>
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100">
                                <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 w-12">No</th>
                                <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">NIM</th>
                                <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Nama Mahasiswa</th>
                                <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Program Studi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {mahasiswaList.length > 0 ? (
                                mahasiswaList.map((mhs, idx) => {
                                    const prodiKey = mhs.user?.prodi ?? null;
                                    const prodiNama = prodiMap[prodiKey] ?? '-';
                                    const prodiCls = prodiColor[prodiKey] ?? 'bg-gray-100 text-gray-500';

                                    return (
                                        <tr key={mhs.id} className="hover:bg-teal-50/30 transition-colors">
                                            <td className="px-8 py-5 text-[10px] font-black text-gray-300">
                                                {String(idx + 1).padStart(2, '0')}
                                            </td>
                                            <td className="px-8 py-5">
                                                <span className="font-mono text-xs font-bold text-slate-500 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                                    {mhs.nim}
                                                </span>
                                            </td>
                                            <td className="px-8 py-5">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                                        {(mhs.nama || '').substring(0, 1).toUpperCase()}
                                                    </div>
                                                    <span className="font-black text-sm text-[#004d4d]">{mhs.nama}</span>
                                                </div>
                                            </td>
                                            <td className="px-8 py-5">
                                                {prodiKey ? (
                                                    <span className={`text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl ${prodiCls}`}>
                                                        {prodiNama}
                                                    </span>
                                                ) : (
                                                    <span className="text-xs text-gray-300 font-bold">-</span>
                                                )}
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={4} className="px-8 py-20 text-center">
                                        <span className="material-symbols-outlined text-5xl text-gray-300">group_off</span>
                                        <p className="font-black italic text-gray-400 mt-2">Belum ada mahasiswa di kelas ini.</p>
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
