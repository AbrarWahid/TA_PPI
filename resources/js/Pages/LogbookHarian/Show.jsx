import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookHarianShow({ logbook_harian }) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const [showForm, setShowForm] = useState(false);
    const [action, setAction] = useState('disetujui');
    const [catatanDosen, setCatatanDosen] = useState('');

    const getStatusBadge = (status) => {
        if (status === 'disetujui') {
            return {
                cls: 'bg-emerald-50 border border-emerald-200 text-[#15803d]',
                label: 'Disetujui',
            };
        }
        if (status === 'ditolak') {
            return {
                cls: 'bg-red-50 border border-red-200 text-[#b91c1c]',
                label: 'Ditolak',
            };
        }
        return {
            cls: 'bg-amber-50 border border-amber-200 text-[#b45309]',
            label: 'Pending',
        };
    };

    const badge = getStatusBadge(logbook_harian.status_verifikasi);
    const formattedDate = logbook_harian.tanggal
        ? new Date(logbook_harian.tanggal).toLocaleDateString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
          })
        : '-';

    const ext = logbook_harian.dokumentasi
        ? logbook_harian.dokumentasi.split('.').pop().toLowerCase()
        : '';
    const isImage = ['jpg', 'jpeg', 'png', 'webp'].includes(ext);

    const handleVerifikasi = (e) => {
        e.preventDefault();
        router.patch(`/logbook-harian/${logbook_harian.id}/verifikasi`, {
            status_verifikasi: action,
            catatan_dosen: catatanDosen,
        });
    };

    return (
        <AppLayout title="Detail Logbook Harian">
            <div className="max-w-2xl mx-auto">
                <div className="flex items-center gap-4 mb-6">
                    <Link
                        href="/logbook-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Detail Logbook</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">
                            {logbook_harian.hari}, {formattedDate}
                        </p>
                    </div>
                    <div className="ml-auto">
                        <span className={`font-mono text-[10px] font-medium px-3 py-1 rounded-full ${badge.cls}`}>
                            {badge.label}
                        </span>
                    </div>
                </div>

                <div className="space-y-4">
                    {/* Info Ringkas */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6">
                        <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-4">
                            Informasi
                        </p>
                        <div className="grid grid-cols-2 gap-3">
                            <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5 col-span-2">
                                <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                    Proyek
                                </p>
                                <p className="text-sm font-semibold text-[#0b0b0c]">
                                    {logbook_harian.proyek?.judul_proyek || '-'}
                                </p>
                            </div>
                            <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5">
                                <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                    Minggu Ke
                                </p>
                                <p className="text-sm font-semibold text-[#0b0b0c]">{logbook_harian.minggu_ke}</p>
                            </div>
                            <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5">
                                <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                    Hari &amp; Jam
                                </p>
                                <p className="text-sm font-semibold text-[#0b0b0c]">
                                    {logbook_harian.hari} ({logbook_harian.jam_masuk?.substring(0, 5) || '-'} –{' '}
                                    {logbook_harian.jam_keluar?.substring(0, 5) || '-'})
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Aktivitas */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6">
                        <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-3">
                            Aktivitas Harian
                        </p>
                        <p className="text-sm text-[#0b0b0c] leading-relaxed whitespace-pre-line">
                            {logbook_harian.aktivitas}
                        </p>
                        {logbook_harian.kendala && (
                            <div className="mt-4 pt-4 border-t border-[#f4f4f5]">
                                <p className="font-mono text-[11px] font-semibold text-[#b91c1c] uppercase tracking-wider mb-1">
                                    Kendala
                                </p>
                                <p className="text-xs text-[#52525b]">{logbook_harian.kendala}</p>
                            </div>
                        )}
                    </div>

                    {/* Dokumentasi */}
                    {logbook_harian.dokumentasi && (
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6">
                            <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-3">
                                Dokumentasi
                            </p>
                            {isImage ? (
                                <img
                                    src={`/storage/${logbook_harian.dokumentasi}`}
                                    className="rounded-lg max-h-64 object-cover border border-[#e4e4e7] w-full"
                                    alt="Dokumentasi"
                                />
                            ) : (
                                <a
                                    href={`/storage/${logbook_harian.dokumentasi}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-2 text-xs font-semibold text-[#013981] hover:underline"
                                >
                                    <span className="material-symbols-outlined text-sm">picture_as_pdf</span> Lihat
                                    Dokumen
                                </a>
                            )}
                        </div>
                    )}

                    {/* Catatan Dosen */}
                    {logbook_harian.catatan_dosen && (
                        <div className="bg-amber-50 rounded-xl border border-amber-200 p-5">
                            <p className="font-mono text-[10px] font-semibold text-[#b45309] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <span className="material-symbols-outlined text-sm">comment</span> Catatan Dosen
                            </p>
                            <p className="text-xs text-[#b45309] leading-relaxed">{logbook_harian.catatan_dosen}</p>
                        </div>
                    )}

                    {/* Tombol Edit — Mahasiswa & pending */}
                    {user?.role === 'mahasiswa' && logbook_harian.status_verifikasi === 'pending' && (
                        <div className="flex gap-3 pt-2">
                            <Link
                                href={`/logbook-harian/${logbook_harian.id}/edit`}
                                className="flex-1 py-2.5 text-center text-xs font-semibold text-white bg-[#013981] hover:bg-[#012b63] rounded-lg transition-colors flex items-center justify-center gap-2"
                            >
                                <span className="material-symbols-outlined text-sm">edit</span> Edit Logbook
                            </Link>
                        </div>
                    )}

                    {/* Verifikasi — Dosen/Manager & pending */}
                    {['dosen', 'manager_proyek'].includes(user?.role) &&
                        logbook_harian.status_verifikasi === 'pending' && (
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6">
                                <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-4">
                                    Tindakan Verifikasi
                                </p>
                                {!showForm ? (
                                    <div className="flex gap-3">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setAction('disetujui');
                                                setShowForm(true);
                                            }}
                                            className="flex-1 py-2.5 bg-emerald-50 border border-emerald-200 text-[#15803d] font-semibold text-xs rounded-lg hover:bg-emerald-100 transition-colors cursor-pointer"
                                        >
                                            Setujui
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setAction('ditolak');
                                                setShowForm(true);
                                            }}
                                            className="flex-1 py-2.5 bg-red-50 border border-red-200 text-[#b91c1c] font-semibold text-xs rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                                        >
                                            Tolak
                                        </button>
                                    </div>
                                ) : (
                                    <form onSubmit={handleVerifikasi} className="space-y-3">
                                        <textarea
                                            rows={3}
                                            value={catatanDosen}
                                            onChange={(e) => setCatatanDosen(e.target.value)}
                                            placeholder="Catatan (opsional)..."
                                            className="w-full px-3.5 py-2.5 border border-[#e4e4e7] rounded-lg text-xs resize-none focus:outline-none focus:border-[#013981]"
                                        />
                                        <div className="flex gap-3">
                                            <button
                                                type="button"
                                                onClick={() => setShowForm(false)}
                                                className="flex-1 py-2.5 bg-[#f4f4f5] text-[#0b0b0c] font-semibold text-xs rounded-lg hover:bg-[#e4e4e7] transition-colors cursor-pointer"
                                            >
                                                Batal
                                            </button>
                                            <button
                                                type="submit"
                                                className="flex-1 py-2.5 bg-[#013981] text-white font-semibold text-xs rounded-lg hover:bg-[#012b63] transition-all cursor-pointer"
                                            >
                                                Konfirmasi ({action === 'disetujui' ? 'Setujui' : 'Tolak'})
                                            </button>
                                        </div>
                                    </form>
                                )}
                            </div>
                        )}
                </div>
            </div>
        </AppLayout>
    );
}
