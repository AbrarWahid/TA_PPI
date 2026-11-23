import React from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalKaprodiExport({ mahasiswas = [] }) {
    const list = Array.isArray(mahasiswas) ? mahasiswas : mahasiswas?.data || [];

    return (
        <AppLayout title="Ekspor Transkrip">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
                    <div>
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                            KOORDINATOR PROGRAM STUDI
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Ekspor Transkrip PPI
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Pilih mahasiswa dari program studi Anda untuk mencetak transkrip resmi.
                        </p>
                    </div>
                    <span className="ps-badge">{list.length} mahasiswa</span>
                </div>

                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr>
                                    <th className="px-5 py-3.5">NIM</th>
                                    <th className="px-5 py-3.5">Nama Mahasiswa</th>
                                    <th className="px-5 py-3.5 text-center">Kelas</th>
                                    <th className="px-5 py-3.5 text-center">Status Logbook</th>
                                    <th className="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f4f4f5]">
                                {list.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="px-5 py-12 text-center text-[#6a6f7c]"
                                        >
                                            Belum ada data mahasiswa pada program studi ini.
                                        </td>
                                    </tr>
                                ) : (
                                    list.map((m) => (
                                        <tr key={m.nim}>
                                            <td className="px-5 py-3.5 font-mono text-[#0b0b0c]">
                                                {m.nim}
                                            </td>
                                            <td className="px-5 py-3.5 font-medium text-[#0b0b0c]">
                                                {m.nama_mhs}
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                <span className="font-mono px-2 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] text-[11px]">
                                                    {m.kelas || '-'}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                {m.nilai_logbook_lengkap ? (
                                                    <span className="font-mono px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[#15803d] text-[10px] font-medium">
                                                        Lengkap (20 Minggu)
                                                    </span>
                                                ) : (
                                                    <span className="font-mono px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-[#b45309] text-[10px] font-medium">
                                                        Belum Lengkap (20 Minggu)
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-5 py-3.5 text-right">
                                                {m.nilai_logbook_lengkap ? (
                                                    <a
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        href={`/external/kaprodi/export/${m.nim}`}
                                                        className="ps-btn-primary text-xs py-1.5 px-3"
                                                    >
                                                        <span className="material-symbols-outlined text-[15px]">
                                                            picture_as_pdf
                                                        </span>
                                                        <span>Buka / Cetak PDF</span>
                                                    </a>
                                                ) : (
                                                    <button
                                                        type="button"
                                                        disabled
                                                        className="inline-flex items-center gap-1.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#a1a1aa] px-3 py-1.5 rounded-lg text-xs cursor-not-allowed"
                                                    >
                                                        <span className="material-symbols-outlined text-[14px]">
                                                            lock
                                                        </span>
                                                        <span>Terkunci</span>
                                                    </button>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
