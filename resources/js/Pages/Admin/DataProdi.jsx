import React from 'react';
import AppLayout from '@/Layouts/AppLayout';

const prodiList = [
    {
        id: 1,
        kode_prodi: 'TRIN',
        short: 'D4 TRIN',
        nama_prodi: 'D4 Teknologi Rekayasa Informatika Industri',
        badgeCls: 'bg-[#eef4fc] text-[#013981] border-[#d8e6f8]',
    },
    {
        id: 2,
        kode_prodi: 'TRO',
        short: 'D4 TRO',
        nama_prodi: 'D4 Teknologi Rekayasa Otomasi',
        badgeCls: 'bg-emerald-50 text-[#15803d] border-emerald-200',
    },
    {
        id: 3,
        kode_prodi: 'TRMO',
        short: 'D4 TRMO',
        nama_prodi: 'D4 Teknologi Rekayasa Mekatronika',
        badgeCls: 'bg-purple-50 text-purple-700 border-purple-200',
    },
];

export default function DataProdi({ prodis }) {
    const displayProdis = prodis && prodis.length > 0 ? prodis : prodiList;

    return (
        <AppLayout title="Master Data Program Studi">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div>
                    <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                        MASTER DATA AKADEMIK
                    </p>
                    <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                        Data Program Studi
                    </h1>
                    <p className="text-xs text-[#6a6f7c] mt-1">
                        3 Program Studi Sarjana Terapan (D4) Jurusan Automation Engineering.
                    </p>
                </div>

                {/* STAT CARDS */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {prodiList.map((p) => (
                        <div
                            key={p.kode_prodi}
                            className="bg-white rounded-xl border border-[#e4e4e7] p-5 shadow-surface flex items-center gap-4"
                        >
                            <div className="w-12 h-11 rounded-lg bg-[#013981] flex items-center justify-center text-white font-mono font-bold text-xs shrink-0">
                                {p.kode_prodi}
                            </div>
                            <div>
                                <p className="font-semibold text-[#0b0b0c] text-sm">{p.short}</p>
                                <p className="text-xs text-[#6a6f7c] mt-0.5">{p.nama_prodi}</p>
                            </div>
                        </div>
                    ))}
                </div>

                {/* TABLE */}
                <div className="bg-white rounded-xl shadow-surface border border-[#e4e4e7] overflow-hidden">
                    <table className="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr>
                                <th className="px-6 py-3.5 w-16">#</th>
                                <th className="px-6 py-3.5">Kode</th>
                                <th className="px-6 py-3.5">Nama Program Studi</th>
                                <th className="px-6 py-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#f4f4f5]">
                            {displayProdis.map((p, idx) => (
                                <tr key={p.id || idx}>
                                    <td className="px-6 py-4 font-mono text-[#6a6f7c]">{idx + 1}</td>
                                    <td className="px-6 py-4">
                                        <span className="font-mono px-3 py-1 rounded-lg bg-[#013981] text-white text-xs font-semibold">
                                            {p.kode_prodi}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 font-semibold text-[#0b0b0c] text-sm">
                                        {p.nama_prodi}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <span className="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-[#15803d] border border-emerald-200 rounded-full font-mono text-[10px] font-medium">
                                            <span className="material-symbols-outlined text-xs">
                                                check_circle
                                            </span>
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
