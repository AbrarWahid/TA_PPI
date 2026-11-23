import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiHarianShow({ supervisiHarian }) {
    const namaMatkul = supervisiHarian.supervisi_matkul?.mata_kuliah?.nama_matkul || '-';
    const namaDosen =
        supervisiHarian.supervisi_matkul?.dosen?.nama_dosen ||
        supervisiHarian.supervisi_matkul?.dosen?.user?.name ||
        '-';
    const dateStr = supervisiHarian.tanggal
        ? new Date(supervisiHarian.tanggal).toLocaleDateString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
          })
        : '-';

    return (
        <AppLayout title="Detail Supervisi Harian">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="flex items-center gap-4">
                    <Link
                        href="/supervisi-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Detail Supervisi Harian</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">
                            {supervisiHarian.hari}, {dateStr}
                        </p>
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-3">
                        <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5 col-span-2">
                            <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                Mata Kuliah &amp; Dosen
                            </p>
                            <p className="text-sm font-semibold text-[#0b0b0c]">
                                {namaMatkul} — {namaDosen}
                            </p>
                        </div>
                        <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5">
                            <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                Minggu Ke
                            </p>
                            <p className="text-sm font-semibold text-[#0b0b0c]">
                                {supervisiHarian.minggu_ke}
                            </p>
                        </div>
                        <div className="bg-[#fafafa] border border-[#e4e4e7] rounded-lg p-3.5">
                            <p className="font-mono text-[10px] text-[#6a6f7c] uppercase tracking-wider mb-1">
                                Waktu
                            </p>
                            <p className="text-sm font-semibold text-[#0b0b0c]">
                                {supervisiHarian.jam_mulai?.substring(0, 5)} –{' '}
                                {supervisiHarian.jam_selesai?.substring(0, 5)}
                            </p>
                        </div>
                    </div>

                    <div className="pt-2">
                        <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-2">
                            Aktivitas
                        </p>
                        <p className="text-sm text-[#0b0b0c] leading-relaxed whitespace-pre-line">
                            {supervisiHarian.aktivitas}
                        </p>
                    </div>

                    {supervisiHarian.kendala && (
                        <div className="pt-3 border-t border-[#f4f4f5]">
                            <p className="font-mono text-[11px] font-semibold text-[#b91c1c] uppercase tracking-wider mb-1">
                                Kendala
                            </p>
                            <p className="text-xs text-[#52525b]">{supervisiHarian.kendala}</p>
                        </div>
                    )}

                    {supervisiHarian.dokumentasi && (
                        <div className="pt-3 border-t border-[#f4f4f5]">
                            <p className="font-mono text-[11px] font-semibold text-[#6a6f7c] uppercase tracking-wider mb-2">
                                Dokumentasi
                            </p>
                            <a
                                href={`/storage/${supervisiHarian.dokumentasi}`}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-1.5 text-xs font-semibold text-[#013981] hover:underline"
                            >
                                <span className="material-symbols-outlined text-sm">attach_file</span>
                                Lihat Dokumen
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
