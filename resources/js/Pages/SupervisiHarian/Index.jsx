import React from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiHarianIndex({
    supervisiList = [],
    mingguBelumRekap = [],
    statusRekap = {},
}) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isMahasiswa = user?.role === 'mahasiswa';

    const getRekapBadge = (supervisiMatkulId, mingguKe) => {
        const key = `${supervisiMatkulId}-${mingguKe}`;
        const st = statusRekap?.[key];
        if (st === 'disetujui') {
            return {
                cls: 'bg-emerald-50 text-[#15803d] border border-emerald-200',
                label: 'Disetujui',
            };
        }
        if (st === 'diajukan') {
            return {
                cls: 'bg-amber-50 text-[#b45309] border border-amber-200',
                label: 'Diajukan',
            };
        }
        if (st === 'ditolak') {
            return {
                cls: 'bg-red-50 text-[#b91c1c] border border-red-200',
                label: 'Ditolak',
            };
        }
        return {
            cls: 'bg-[#f4f4f5] text-[#6a6f7c] border border-[#e4e4e7]',
            label: 'Belum Rekap',
        };
    };

    const handleDelete = (item) => {
        if (window.confirm(`Hapus entri supervisi ${item.hari} ini?`)) {
            router.delete(`/supervisi-harian/${item.id}`);
        }
    };

    return (
        <AppLayout title="Supervisi Harian">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
                    <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#013981]" />
                            <span className="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">
                                SUPERVISI AKADEMIK · HARIAN
                            </span>
                        </div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Supervisi Harian
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Catatan aktivitas konsultasi dan supervisi mata kuliah per hari.
                        </p>
                    </div>
                    <div className="flex items-center gap-2.5">
                        <Link
                            href="/supervisi-mingguan"
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-[#e4e4e7] hover:bg-[#f4f4f5] text-[#0b0b0c] text-xs font-semibold transition-colors"
                        >
                            <span className="material-symbols-outlined text-[16px]">summarize</span>
                            <span>Rekap Mingguan</span>
                        </Link>
                        {isMahasiswa && (
                            <Link
                                href="/supervisi-harian/create"
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold transition-colors"
                            >
                                <span className="material-symbols-outlined text-[16px]">add</span>
                                <span>Tambah Entri</span>
                            </Link>
                        )}
                    </div>
                </div>

                {isMahasiswa && mingguBelumRekap.length > 0 && (
                    <div className="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                        <span className="material-symbols-outlined text-[#b45309] mt-0.5">info</span>
                        <div>
                            <p className="text-xs font-semibold text-[#b45309]">
                                Ada minggu yang siap direkap!
                            </p>
                            <div className="mt-2 flex flex-wrap gap-2">
                                {mingguBelumRekap.map((item, idx) => (
                                    <Link
                                        key={idx}
                                        href="/supervisi-mingguan"
                                        className="inline-flex items-center gap-1 px-3 py-1 bg-white border border-amber-200 text-[#b45309] rounded-lg text-xs font-medium hover:bg-amber-100/60 transition"
                                    >
                                        <span className="material-symbols-outlined text-xs">summarize</span>
                                        Minggu {item.minggu_ke} —{' '}
                                        {item.supervisiMatkul?.mata_kuliah?.nama_matkul || 'Matkul'}
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                <div className="bg-white rounded-xl shadow-surface border border-[#e4e4e7] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr>
                                    <th className="px-5 py-3.5">Tanggal</th>
                                    <th className="px-5 py-3.5">Minggu</th>
                                    {isMahasiswa ? (
                                        <th className="px-5 py-3.5 text-center">Status Rekap</th>
                                    ) : (
                                        <th className="px-5 py-3.5">Mahasiswa</th>
                                    )}
                                    <th className="px-5 py-3.5">Mata Kuliah</th>
                                    <th className="px-5 py-3.5">Dosen</th>
                                    <th className="px-5 py-3.5">Jam</th>
                                    <th className="px-5 py-3.5">Aktivitas</th>
                                    <th className="px-5 py-3.5 text-center">Dokumen</th>
                                    {isMahasiswa && <th className="px-5 py-3.5 text-center">Aksi</th>}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f4f4f5]">
                                {supervisiList.length === 0 ? (
                                    <tr>
                                        <td colSpan={9} className="px-5 py-14 text-center text-[#6a6f7c]">
                                            <span className="material-symbols-outlined text-3xl text-[#d4d4d8] mb-2 block">
                                                event_note
                                            </span>
                                            <p className="font-medium text-xs text-[#0b0b0c]">
                                                Belum ada entri supervisi harian
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    supervisiList.map((item) => {
                                        const namaMatkul =
                                            item.supervisi_matkul?.mata_kuliah?.nama_matkul || '-';
                                        const namaDosen =
                                            item.supervisi_matkul?.dosen?.nama_dosen ||
                                            item.supervisi_matkul?.dosen?.user?.name ||
                                            '-';
                                        const rekapBadge = getRekapBadge(
                                            item.supervisi_matkul_id,
                                            item.minggu_ke,
                                        );
                                        const dateStr = item.tanggal
                                            ? new Date(item.tanggal).toLocaleDateString('id-ID', {
                                                  day: '2-digit',
                                                  month: 'short',
                                                  year: 'numeric',
                                              })
                                            : '-';
                                        return (
                                            <tr key={item.id}>
                                                <td className="px-5 py-3.5">
                                                    <p className="font-semibold text-[#0b0b0c]">
                                                        {item.hari}
                                                    </p>
                                                    <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                        {dateStr}
                                                    </p>
                                                </td>
                                                <td className="px-5 py-3.5">
                                                    <span className="font-mono px-2.5 py-0.5 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded text-[11px]">
                                                        Minggu {item.minggu_ke}
                                                    </span>
                                                </td>
                                                {isMahasiswa ? (
                                                    <td className="px-5 py-3.5 text-center">
                                                        <span
                                                            className={`font-mono px-2.5 py-0.5 rounded-full text-[10px] font-medium ${rekapBadge.cls}`}
                                                        >
                                                            {rekapBadge.label}
                                                        </span>
                                                    </td>
                                                ) : (
                                                    <td className="px-5 py-3.5">
                                                        <p className="font-semibold text-[#0b0b0c]">
                                                            {item.mahasiswa?.nama || '-'}
                                                        </p>
                                                        <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                            {item.mahasiswa?.nim || '-'}
                                                        </p>
                                                    </td>
                                                )}
                                                <td className="px-5 py-3.5">
                                                    <p className="font-medium text-[#0b0b0c] max-w-[160px] truncate">
                                                        {namaMatkul}
                                                    </p>
                                                </td>
                                                <td className="px-5 py-3.5 text-[#6a6f7c]">{namaDosen}</td>
                                                <td className="px-5 py-3.5 font-mono text-[#0b0b0c] whitespace-nowrap">
                                                    {item.jam_mulai?.substring(0, 5)} –{' '}
                                                    {item.jam_selesai?.substring(0, 5)}
                                                </td>
                                                <td className="px-5 py-3.5">
                                                    <p className="text-[#0b0b0c] max-w-[200px] line-clamp-2">
                                                        {item.aktivitas}
                                                    </p>
                                                    {item.kendala && (
                                                        <p className="text-[11px] text-[#b91c1c] mt-0.5 line-clamp-1">
                                                            Kendala: {item.kendala}
                                                        </p>
                                                    )}
                                                </td>
                                                <td className="px-5 py-3.5 text-center">
                                                    {item.dokumentasi ? (
                                                        <a
                                                            href={`/storage/${item.dokumentasi}`}
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="inline-flex items-center gap-1 px-2.5 py-1 bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded-md text-[11px] font-medium hover:bg-[#e4e4e7]"
                                                        >
                                                            <span className="material-symbols-outlined text-xs">
                                                                attach_file
                                                            </span>{' '}
                                                            Lihat
                                                        </a>
                                                    ) : (
                                                        <span className="text-[#a1a1aa]">—</span>
                                                    )}
                                                </td>
                                                {isMahasiswa && (
                                                    <td className="px-5 py-3.5">
                                                        <div className="flex items-center justify-center gap-1.5">
                                                            <Link
                                                                href={`/supervisi-harian/${item.id}/edit`}
                                                                className="w-7 h-7 flex items-center justify-center rounded-md bg-[#fafafa] border border-[#e4e4e7] text-[#0b0b0c] hover:bg-[#f4f4f5] transition"
                                                                title="Edit"
                                                            >
                                                                <span className="material-symbols-outlined text-[15px]">
                                                                    edit
                                                                </span>
                                                            </Link>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleDelete(item)}
                                                                className="w-7 h-7 flex items-center justify-center rounded-md bg-red-50 border border-red-200 text-[#b91c1c] hover:bg-red-100 transition cursor-pointer"
                                                                title="Hapus"
                                                            >
                                                                <span className="material-symbols-outlined text-[15px]">
                                                                    delete
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                )}
                                            </tr>
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
