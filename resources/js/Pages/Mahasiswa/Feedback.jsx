import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function MahasiswaFeedback({
    mahasiswa,
    feedbackList = [],
    totalFeedback = 0,
    feedbackPerTipe = { penilaian: 0, logbook: 0, laporan: 0 },
}) {
    const [tipeActive, setTipeActive] = useState('semua');

    const filtered = feedbackList.filter(
        (fb) => tipeActive === 'semua' || fb.tipe === tipeActive,
    );

    const getColorConfig = (color) => {
        if (color === 'secondary') {
            return {
                border: 'border-l-emerald-500',
                iconBg: 'bg-emerald-50 text-[#15803d]',
                badge: 'bg-emerald-50 text-[#15803d] border-emerald-200',
            };
        }
        if (color === 'amber') {
            return {
                border: 'border-l-amber-500',
                iconBg: 'bg-amber-50 text-[#b45309]',
                badge: 'bg-amber-50 text-[#b45309] border-amber-200',
            };
        }
        if (color === 'rose') {
            return {
                border: 'border-l-red-500',
                iconBg: 'bg-red-50 text-[#b91c1c]',
                badge: 'bg-red-50 text-[#b91c1c] border-red-200',
            };
        }
        return {
            border: 'border-l-[#013981]',
            iconBg: 'bg-[#eef4fc] text-[#013981]',
            badge: 'bg-[#eef4fc] text-[#013981] border-[#d8e6f8]',
        };
    };

    return (
        <AppLayout title="Feedback Hub">
            <div className="max-w-[1400px] mx-auto space-y-6">
                <div>
                    <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                        EVALUASI &amp; MASUKAN
                    </p>
                    <h1 className="text-2xl font-semibold text-[#0b0b0c]">Feedback Hub</h1>
                    <p className="text-xs text-[#6a6f7c] mt-1">
                        Catatan evaluasi dan masukan dari Dosen Pengampu serta Manager Proyek.
                    </p>
                </div>

                {!mahasiswa ? (
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface py-16 text-center">
                        <span className="material-symbols-outlined text-4xl text-[#d4d4d8] block mb-2">
                            person_off
                        </span>
                        <p className="text-xs text-[#6a6f7c]">Data mahasiswa tidak ditemukan.</p>
                    </div>
                ) : totalFeedback === 0 ? (
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface py-16 text-center">
                        <span className="material-symbols-outlined text-4xl text-[#d4d4d8] block mb-2">
                            mark_chat_read
                        </span>
                        <h3 className="text-sm font-semibold text-[#0b0b0c] mb-1">Belum ada feedback</h3>
                        <p className="text-xs text-[#6a6f7c]">
                            Feedback akan muncul setelah penilaian, verifikasi logbook, atau laporan selesai.
                        </p>
                    </div>
                ) : (
                    <>
                        {/* STAT CARDS */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Total Feedback
                                </p>
                                <h3 className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                                    {totalFeedback}
                                </h3>
                            </div>
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Penilaian
                                </p>
                                <h3 className="text-3xl font-semibold font-mono text-[#013981] mt-2">
                                    {feedbackPerTipe.penilaian ?? 0}
                                </h3>
                            </div>
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Logbook
                                </p>
                                <h3 className="text-3xl font-semibold font-mono text-[#b45309] mt-2">
                                    {feedbackPerTipe.logbook ?? 0}
                                </h3>
                            </div>
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-5">
                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Laporan
                                </p>
                                <h3 className="text-3xl font-semibold font-mono text-[#b91c1c] mt-2">
                                    {feedbackPerTipe.laporan ?? 0}
                                </h3>
                            </div>
                        </div>

                        {/* FILTER TABS */}
                        <div className="flex gap-2 flex-wrap">
                            {[
                                ['semua', 'Semua', totalFeedback],
                                ['penilaian', 'Penilaian', feedbackPerTipe.penilaian ?? 0],
                                ['logbook', 'Logbook', feedbackPerTipe.logbook ?? 0],
                                ['laporan', 'Laporan', feedbackPerTipe.laporan ?? 0],
                            ].map(([key, label, count]) => (
                                <button
                                    key={key}
                                    type="button"
                                    onClick={() => setTipeActive(key)}
                                    className={`inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer ${
                                        tipeActive === key
                                            ? 'bg-[#013981] text-white border-[#013981]'
                                            : 'bg-white text-[#6a6f7c] border-[#e4e4e7] hover:text-[#0b0b0c]'
                                    }`}
                                >
                                    <span>{label}</span>
                                    <span
                                        className={`font-mono text-[10px] px-2 py-0.5 rounded-full ${
                                            tipeActive === key
                                                ? 'bg-white/20 text-white'
                                                : 'bg-[#f4f4f5] text-[#6a6f7c]'
                                        }`}
                                    >
                                        {count}
                                    </span>
                                </button>
                            ))}
                        </div>

                        {/* FEEDBACK LIST */}
                        <div className="space-y-3">
                            {filtered.length === 0 ? (
                                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface py-12 text-center">
                                    <span className="material-symbols-outlined text-3xl text-[#d4d4d8] block mb-2">
                                        filter_list_off
                                    </span>
                                    <p className="text-xs text-[#6a6f7c]">
                                        Tidak ada feedback pada kategori ini.
                                    </p>
                                </div>
                            ) : (
                                filtered.map((fb, idx) => {
                                    const cfg = getColorConfig(fb.color);
                                    const dateStr = fb.tanggal
                                        ? new Date(fb.tanggal).toLocaleDateString('id-ID', {
                                              day: '2-digit',
                                              month: 'short',
                                              year: 'numeric',
                                          })
                                        : '';
                                    return (
                                        <div
                                            key={idx}
                                            className={`bg-white border border-[#e4e4e7] border-l-4 ${cfg.border} rounded-xl p-5 shadow-surface`}
                                        >
                                            <div className="flex items-start gap-4">
                                                <div
                                                    className={`w-10 h-10 rounded-lg ${cfg.iconBg} flex items-center justify-center shrink-0`}
                                                >
                                                    <span className="material-symbols-outlined text-[20px]">
                                                        {fb.icon}
                                                    </span>
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <div className="flex items-center gap-2 flex-wrap mb-1">
                                                        <span className="text-sm font-semibold text-[#0b0b0c]">
                                                            {fb.sumber}
                                                        </span>
                                                        <span
                                                            className={`font-mono text-[10px] px-2.5 py-0.5 rounded-full border font-medium ${cfg.badge}`}
                                                        >
                                                            {fb.badge}
                                                        </span>
                                                    </div>
                                                    <p className="text-xs text-[#6a6f7c] mb-2.5 flex items-center gap-1">
                                                        <span className="material-symbols-outlined text-xs">
                                                            folder
                                                        </span>
                                                        {fb.konteks}
                                                    </p>
                                                    <div className="bg-[#fafafa] rounded-lg px-4 py-3 text-xs text-[#0b0b0c] leading-relaxed border border-[#e4e4e7]">
                                                        &ldquo;{fb.pesan}&rdquo;
                                                    </div>
                                                </div>
                                                <div className="shrink-0 text-right">
                                                    <p className="font-mono text-[11px] text-[#6a6f7c]">
                                                        {dateStr}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
