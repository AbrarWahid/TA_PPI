import React from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function MahasiswaNilai({
    nilaiManager,
    nilaiDosen = [],
    mahasiswa,
    bobotManager = 55,
    bobotDosen = 45,
}) {
    const hasNilai = Boolean(nilaiManager) || nilaiDosen.length > 0;

    const nmSkala100 = Number(nilaiManager?.nilai_manager || 0);
    const ndSkala100 =
        nilaiDosen.length > 0
            ? nilaiDosen.reduce((acc, cur) => acc + Number(cur.nilai_dosen || 0), 0) / nilaiDosen.length
            : 0;

    const nmBobot = nmSkala100 * (bobotManager / 100);
    const ndBobot = ndSkala100 * (bobotDosen / 100);
    const nilaiAkhir = nmBobot + ndBobot;
    const grade =
        nilaiAkhir >= 85
            ? 'A'
            : nilaiAkhir >= 75
              ? 'B'
              : nilaiAkhir >= 65
                ? 'C'
                : nilaiAkhir >= 55
                  ? 'D'
                  : 'E';

    const grupManager = nilaiManager
        ? [
              {
                  label: 'Learning Skills',
                  bobot: '20%',
                  items: [
                      ['Critical Thinking', nilaiManager.ls_critical_thinking],
                      ['Kolaborasi', nilaiManager.ls_kolaborasi],
                      ['Kreativitas & Inovasi', nilaiManager.ls_kreativitas],
                      ['Komunikasi', nilaiManager.ls_komunikasi],
                  ],
              },
              {
                  label: 'Life Skills',
                  bobot: '20%',
                  items: [
                      ['Fleksibilitas', nilaiManager.lf_fleksibilitas],
                      ['Kepemimpinan', nilaiManager.lf_kepemimpinan],
                      ['Produktivitas', nilaiManager.lf_produktivitas],
                      ['Social Skill', nilaiManager.lf_social_skill],
                  ],
              },
              {
                  label: 'Laporan Project',
                  bobot: '15%',
                  items: [
                      ['RPP', nilaiManager.lp_rpp],
                      ['Logbook', nilaiManager.lp_logbook],
                      ['Dokumen Projek', nilaiManager.lp_dokumen_projek],
                  ],
              },
          ]
        : [];

    return (
        <AppLayout title="Nilai & Transkrip — Magang Dalam Kampus">
            <div className="max-w-[1400px] mx-auto space-y-6">
                {/* HEADER */}
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-surface">
                    <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#013981]" />
                            <span className="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">
                                MAGANG DALAM KAMPUS · TRANSKRIP NILAI
                            </span>
                        </div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Transkrip &amp; Rincian Penilaian
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Rincian evaluasi berbasis rubrik dari Manager Proyek dan Dosen Pengampu.
                        </p>
                    </div>
                </div>

                {!hasNilai ? (
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface py-16 text-center">
                        <span className="material-symbols-outlined text-4xl text-[#d4d4d8] block mb-2">
                            grade
                        </span>
                        <p className="text-xs font-medium text-[#0b0b0c]">
                            Belum ada nilai yang dipublikasikan
                        </p>
                        <p className="text-[11px] text-[#6a6f7c] mt-0.5">
                            Nilai akan muncul setelah Manager Proyek atau Dosen Pengampu menyelesaikan evaluasi.
                        </p>
                    </div>
                ) : (
                    <>
                        {/* SUMMARY HERO CARD */}
                        <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 sm:p-7 shadow-surface flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div className="space-y-2">
                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                    Rekapitulasi Akhir
                                </p>
                                <h2 className="text-xl font-semibold text-[#0b0b0c]">
                                    {mahasiswa?.nama || '-'}
                                </h2>
                                <p className="font-mono text-xs text-[#6a6f7c]">
                                    NIM · {mahasiswa?.nim || '-'}
                                </p>
                                <div className="pt-3 flex flex-wrap items-center gap-4 text-xs text-[#6a6f7c]">
                                    <span>
                                        Manager ({bobotManager}%):{' '}
                                        <strong className="font-mono text-[#0b0b0c]">
                                            {nmBobot.toFixed(1)}
                                        </strong>{' '}
                                        ({nmSkala100.toFixed(1)}/100)
                                    </span>
                                    <span className="text-[#d4d4d8]">·</span>
                                    <span>
                                        Dosen ({bobotDosen}%):{' '}
                                        <strong className="font-mono text-[#0b0b0c]">
                                            {ndBobot.toFixed(1)}
                                        </strong>{' '}
                                        ({ndSkala100.toFixed(1)}/100)
                                    </span>
                                </div>
                            </div>
                            <div className="flex items-center gap-4 bg-[#fafafa] border border-[#e4e4e7] rounded-xl px-6 py-4 shrink-0">
                                <div>
                                    <p className="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">
                                        Nilai Akhir
                                    </p>
                                    <p className="font-mono text-3xl font-semibold text-[#0b0b0c] mt-0.5">
                                        {nilaiAkhir.toFixed(1)}
                                        <span className="text-xs text-[#6a6f7c] font-normal">/100</span>
                                    </p>
                                </div>
                                <div className="w-px h-10 bg-[#e4e4e7]" />
                                <div className="text-center">
                                    <p className="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">
                                        Predikat
                                    </p>
                                    <span className="inline-flex items-center justify-center font-mono text-xl font-semibold bg-[#013981] text-white w-10 h-10 rounded-lg mt-0.5">
                                        {grade}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            {/* I. MANAGER PROYEK */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between bg-[#fafafa]">
                                    <div>
                                        <h3 className="font-semibold text-[#0b0b0c] text-sm">
                                            I. Evaluasi Manager Proyek
                                        </h3>
                                        <p className="text-[11px] text-[#6a6f7c]">
                                            Bobot {bobotManager}% dari nilai akhir
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <span className="font-mono text-xs px-2.5 py-0.5 bg-white border border-[#e4e4e7] rounded-full text-[#0b0b0c]">
                                            {nmBobot.toFixed(1)} / {bobotManager}
                                        </span>
                                        <span className="font-mono text-[10px] text-[#6a6f7c] mt-0.5 block">
                                            Skala 100: {nmSkala100.toFixed(1)}
                                        </span>
                                    </div>
                                </div>
                                <div className="p-5 space-y-5">
                                    {nilaiManager ? (
                                        <>
                                            {grupManager.map((grup) => (
                                                <div key={grup.label}>
                                                    <div className="flex justify-between items-center mb-2.5">
                                                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                                            {grup.label}
                                                        </p>
                                                        <span className="font-mono text-[11px] text-[#6a6f7c]">
                                                            {grup.bobot}
                                                        </span>
                                                    </div>
                                                    <div className="space-y-2">
                                                        {grup.items.map(([label, value]) => (
                                                            <div
                                                                key={label}
                                                                className="flex items-center gap-3"
                                                            >
                                                                <span className="w-36 text-xs text-[#52525b] shrink-0">
                                                                    {label}
                                                                </span>
                                                                <div className="flex-1 bg-[#f4f4f5] h-1.5 rounded-full overflow-hidden">
                                                                    <div
                                                                        className="h-full rounded-full bg-[#013981]"
                                                                        style={{
                                                                            width: `${Math.min(Number(value || 0), 100)}%`,
                                                                        }}
                                                                    />
                                                                </div>
                                                                <span className="w-8 text-right font-mono text-xs font-medium text-[#0b0b0c]">
                                                                    {value ?? '-'}
                                                                </span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                            {nilaiManager.catatan_manager && (
                                                <div className="p-3.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-xs text-[#52525b] leading-relaxed">
                                                    <span className="font-semibold text-[#0b0b0c] block mb-1">
                                                        Catatan Manager:
                                                    </span>
                                                    {nilaiManager.catatan_manager}
                                                </div>
                                            )}
                                        </>
                                    ) : (
                                        <p className="text-xs text-[#6a6f7c] text-center py-6">
                                            Belum ada penilaian dari Manager Proyek.
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* II. DOSEN PENGAMPU */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="px-5 py-4 border-b border-[#e4e4e7] flex items-center justify-between bg-[#fafafa]">
                                    <div>
                                        <h3 className="font-semibold text-[#0b0b0c] text-sm">
                                            II. Evaluasi Dosen Pengampu
                                        </h3>
                                        <p className="text-[11px] text-[#6a6f7c]">
                                            Bobot {bobotDosen}% dari nilai akhir
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <span className="font-mono text-xs px-2.5 py-0.5 bg-white border border-[#e4e4e7] rounded-full text-[#0b0b0c]">
                                            {ndBobot.toFixed(1)} / {bobotDosen}
                                        </span>
                                        <span className="font-mono text-[10px] text-[#6a6f7c] mt-0.5 block">
                                            Skala 100: {ndSkala100.toFixed(1)}
                                        </span>
                                    </div>
                                </div>
                                <div className="p-5 space-y-5">
                                    {nilaiDosen.length === 0 ? (
                                        <p className="text-xs text-[#6a6f7c] text-center py-6">
                                            Belum ada penilaian dari Dosen Pengampu.
                                        </p>
                                    ) : (
                                        nilaiDosen.map((pd) => {
                                            const ndSingle = Number(pd.nilai_dosen || 0);
                                            const grupDosen = [
                                                {
                                                    label: 'Literacy Skills',
                                                    bobot: '15%',
                                                    items: [
                                                        ['Literasi Informasi', pd.lit_informasi],
                                                        ['Literasi Media', pd.lit_media],
                                                        ['Literasi Teknologi', pd.lit_teknologi],
                                                    ],
                                                },
                                                {
                                                    label: 'Presentasi',
                                                    bobot: '15%',
                                                    items: [
                                                        ['Konten', pd.pr_konten],
                                                        ['Tampilan Visual', pd.pr_visual],
                                                        ['Kosakata', pd.pr_kosakata],
                                                        ['Tanya Jawab', pd.pr_tanya_jawab],
                                                        ['Mata & Gerak', pd.pr_mata_gerak],
                                                    ],
                                                },
                                                {
                                                    label: 'Laporan Akhir',
                                                    bobot: '15%',
                                                    items: [
                                                        ['Penulisan Laporan', pd.la_penulisan],
                                                        ['Pilihan Kata', pd.la_pilihan_kata],
                                                        ['Konten', pd.la_konten],
                                                    ],
                                                },
                                            ];
                                            return (
                                                <div
                                                    key={pd.id}
                                                    className="pb-4 border-b border-[#f4f4f5] last:border-b-0 last:pb-0"
                                                >
                                                    <div className="flex items-center justify-between mb-3">
                                                        <p className="text-xs font-semibold text-[#0b0b0c]">
                                                            {pd.supervisi_matkul?.mata_kuliah?.nama_matkul || '-'}
                                                            <span className="font-mono text-[11px] text-[#6a6f7c] font-normal ml-1">
                                                                · Sem {pd.supervisi_matkul?.semester || ''}
                                                            </span>
                                                        </p>
                                                        <span className="font-mono text-[11px] text-[#0b0b0c] bg-[#f4f4f5] border border-[#e4e4e7] px-2 py-0.5 rounded">
                                                            {ndSingle.toFixed(1)} / 100
                                                        </span>
                                                    </div>
                                                    {grupDosen.map((grup) => (
                                                        <div key={grup.label} className="mb-3.5 last:mb-0">
                                                            <div className="flex justify-between items-center mb-2">
                                                                <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                                                                    {grup.label}
                                                                </p>
                                                                <span className="font-mono text-[11px] text-[#6a6f7c]">
                                                                    {grup.bobot}
                                                                </span>
                                                            </div>
                                                            <div className="space-y-2">
                                                                {grup.items.map(([label, value]) => (
                                                                    <div
                                                                        key={label}
                                                                        className="flex items-center gap-3"
                                                                    >
                                                                        <span className="w-36 text-xs text-[#52525b] shrink-0">
                                                                            {label}
                                                                        </span>
                                                                        <div className="flex-1 bg-[#f4f4f5] h-1.5 rounded-full overflow-hidden">
                                                                            <div
                                                                                className="h-full rounded-full bg-[#013981]"
                                                                                style={{
                                                                                    width: `${Math.min(Number(value || 0), 100)}%`,
                                                                                }}
                                                                            />
                                                                        </div>
                                                                        <span className="w-8 text-right font-mono text-xs font-medium text-[#0b0b0c]">
                                                                            {value ?? '-'}
                                                                        </span>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    ))}
                                                    {pd.catatan_dosen && (
                                                        <div className="mt-3 p-3.5 rounded-lg bg-[#fafafa] border border-[#e4e4e7] text-xs text-[#52525b] leading-relaxed">
                                                            <span className="font-semibold text-[#0b0b0c] block mb-1">
                                                                Catatan Dosen:
                                                            </span>
                                                            {pd.catatan_dosen}
                                                        </div>
                                                    )}
                                                </div>
                                            );
                                        })
                                    )}
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
