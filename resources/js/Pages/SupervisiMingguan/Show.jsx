import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiMingguanShow({ supervisiMingguan, logs = [] }) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const [status, setStatus] = useState('disetujui');
    const [catatanDosen, setCatatanDosen] = useState('');

    const getStatusBadge = (st) => {
        if (st === 'disetujui') {
            return { cls: 'bg-emerald-50 border border-emerald-200 text-[#15803d]', label: 'Disetujui' };
        }
        if (st === 'ditolak') {
            return { cls: 'bg-red-50 border border-red-200 text-[#b91c1c]', label: 'Ditolak' };
        }
        if (st === 'diajukan') {
            return {
                cls: 'bg-amber-50 border border-amber-200 text-[#b45309]',
                label: 'Menunggu Verifikasi',
            };
        }
        return { cls: 'bg-[#f4f4f5] border border-[#e4e4e7] text-[#6a6f7c]', label: 'Draft' };
    };

    const badge = getStatusBadge(supervisiMingguan.status);
    const namaMahasiswa =
        supervisiMingguan.mahasiswa?.user?.name || supervisiMingguan.mahasiswa?.nama || '-';
    const namaDosen =
        supervisiMingguan.supervisi_matkul?.dosen?.nama_dosen ||
        supervisiMingguan.dosen?.nama_dosen ||
        '-';
    const namaMatkul =
        supervisiMingguan.supervisi_matkul?.mata_kuliah?.nama_matkul ||
        supervisiMingguan.matkul?.nama_matkul ||
        '-';

    const handleVerifikasi = (e) => {
        e.preventDefault();
        router.patch(`/supervisi-mingguan/${supervisiMingguan.id}/verifikasi`, {
            status,
            catatan_dosen: catatanDosen,
        });
    };

    return (
        <AppLayout title="Detail Laporan Supervisi">
            <div className="max-w-5xl mx-auto space-y-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link
                            href="/supervisi-mingguan"
                            className="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-[#e4e4e7] hover:bg-[#f4f4f5] transition text-[#6a6f7c]"
                        >
                            <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                        </Link>
                        <div>
                            <h1 className="text-2xl font-semibold text-[#0b0b0c]">
                                Detail Laporan Mingguan
                            </h1>
                            <p className="text-xs text-[#6a6f7c] mt-0.5">
                                Minggu ke-{supervisiMingguan.minggu_ke}
                            </p>
                        </div>
                    </div>
                    <span className={`font-mono px-3 py-1 rounded-full text-xs font-medium ${badge.cls}`}>
                        {badge.label}
                    </span>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* INFO LAPORAN */}
                    <div className="lg:col-span-2 space-y-5">
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6">
                            <p className="font-mono text-[11px] font-semibold uppercase tracking-wider text-[#6a6f7c] mb-4">
                                Informasi Laporan
                            </p>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="font-mono text-[10px] text-[#6a6f7c] uppercase mb-1">
                                        Mahasiswa
                                    </p>
                                    <p className="font-semibold text-sm text-[#0b0b0c]">{namaMahasiswa}</p>
                                    <p className="font-mono text-xs text-[#6a6f7c]">
                                        {supervisiMingguan.mahasiswa?.nim || '-'}
                                    </p>
                                </div>
                                <div>
                                    <p className="font-mono text-[10px] text-[#6a6f7c] uppercase mb-1">
                                        Dosen Pengampu
                                    </p>
                                    <p className="font-semibold text-sm text-[#0b0b0c]">{namaDosen}</p>
                                </div>
                                <div>
                                    <p className="font-mono text-[10px] text-[#6a6f7c] uppercase mb-1">
                                        Mata Kuliah
                                    </p>
                                    <p className="font-semibold text-sm text-[#0b0b0c]">{namaMatkul}</p>
                                </div>
                                <div>
                                    <p className="font-mono text-[10px] text-[#6a6f7c] uppercase mb-1">
                                        Minggu Ke
                                    </p>
                                    <p className="font-semibold font-mono text-sm text-[#0b0b0c]">
                                        {supervisiMingguan.minggu_ke}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Log Harian */}
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                            <div className="px-5 py-4 border-b border-[#e4e4e7]">
                                <p className="font-semibold text-xs text-[#0b0b0c]">
                                    Log Harian Minggu Ke-{supervisiMingguan.minggu_ke}
                                </p>
                            </div>
                            {logs.length === 0 ? (
                                <div className="py-10 text-center">
                                    <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-1">
                                        event_note
                                    </span>
                                    <p className="text-[#6a6f7c] text-xs">Tidak ada log harian</p>
                                </div>
                            ) : (
                                <div className="divide-y divide-[#f4f4f5]">
                                    {logs.map((log) => (
                                        <div key={log.id} className="px-5 py-4 space-y-2">
                                            <div className="flex items-center justify-between">
                                                <p className="font-semibold text-xs text-[#0b0b0c]">
                                                    {log.hari},{' '}
                                                    {log.tanggal
                                                        ? new Date(log.tanggal).toLocaleDateString('id-ID')
                                                        : '-'}
                                                </p>
                                                <span className="font-mono text-[11px] text-[#6a6f7c]">
                                                    {log.jam_mulai?.substring(0, 5)} –{' '}
                                                    {log.jam_selesai?.substring(0, 5)}
                                                </span>
                                            </div>
                                            <p className="text-xs text-[#52525b]">{log.aktivitas}</p>
                                            {log.kendala && (
                                                <p className="text-xs text-[#b91c1c]">
                                                    Kendala: {log.kendala}
                                                </p>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* SIDEBAR KANAN */}
                    <div className="space-y-5">
                        {supervisiMingguan.pdf_path && (
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] font-semibold uppercase tracking-wider text-[#6a6f7c] mb-3">
                                    Dokumen PDF
                                </p>
                                <a
                                    href={`/storage/${supervisiMingguan.pdf_path}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white font-semibold text-xs transition"
                                >
                                    <span className="material-symbols-outlined text-base">
                                        picture_as_pdf
                                    </span>
                                    Buka PDF
                                </a>
                            </div>
                        )}

                        {supervisiMingguan.catatan_dosen && (
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] font-semibold uppercase tracking-wider text-[#6a6f7c] mb-2">
                                    Catatan Dosen
                                </p>
                                <p className="text-xs text-[#0b0b0c] leading-relaxed">
                                    {supervisiMingguan.catatan_dosen}
                                </p>
                            </div>
                        )}

                        {user?.role === 'dosen' && supervisiMingguan.status === 'diajukan' && (
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] font-semibold uppercase tracking-wider text-[#6a6f7c] mb-4">
                                    Verifikasi
                                </p>
                                <form onSubmit={handleVerifikasi} className="space-y-3">
                                    <div className="grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setStatus('disetujui')}
                                            className={`py-2 px-3 rounded-lg border text-xs font-semibold cursor-pointer ${
                                                status === 'disetujui'
                                                    ? 'border-[#15803d] bg-emerald-50 text-[#15803d]'
                                                    : 'border-[#e4e4e7] bg-[#fafafa] text-[#6a6f7c]'
                                            }`}
                                        >
                                            Setujui
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setStatus('ditolak')}
                                            className={`py-2 px-3 rounded-lg border text-xs font-semibold cursor-pointer ${
                                                status === 'ditolak'
                                                    ? 'border-[#b91c1c] bg-red-50 text-[#b91c1c]'
                                                    : 'border-[#e4e4e7] bg-[#fafafa] text-[#6a6f7c]'
                                            }`}
                                        >
                                            Tolak
                                        </button>
                                    </div>
                                    <textarea
                                        rows={3}
                                        value={catatanDosen}
                                        onChange={(e) => setCatatanDosen(e.target.value)}
                                        placeholder="Catatan untuk mahasiswa (opsional)..."
                                        className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white text-xs focus:outline-none focus:border-[#013981] resize-none"
                                    />
                                    <button
                                        type="submit"
                                        className="w-full px-4 py-2.5 rounded-lg bg-[#013981] hover:bg-[#012b63] text-white font-semibold text-xs transition cursor-pointer"
                                    >
                                        Simpan Verifikasi
                                    </button>
                                </form>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
