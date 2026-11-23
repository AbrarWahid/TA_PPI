import React, { useState, useEffect, useRef } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalKaprodiDashboard({
    sarans = [],
    disiplins = [],
    kuisioners = [],
    saranMentors = [],
    user,
    kelasAktif = [],
    mahasiswas = [],
}) {
    const [filterKelas, setFilterKelas] = useState('');
    const [filterNim, setFilterNim] = useState('');

    const [showRekapanModal, setShowRekapanModal] = useState(false);
    const [selectedMahasiswaNim, setSelectedMahasiswaNim] = useState(null);
    const [selectedMahasiswaNama, setSelectedMahasiswaNama] = useState('');
    const [rekapanData, setRekapanData] = useState(null);
    const [rekapanLoading, setRekapanLoading] = useState(false);

    const chartInstanceRef = useRef(null);

    useEffect(() => {
        if (!window.Chart && !document.getElementById('chartjs-cdn-script')) {
            const script = document.createElement('script');
            script.id = 'chartjs-cdn-script';
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.async = true;
            document.head.appendChild(script);
        }
    }, []);

    const parseJsonData = (raw) => {
        if (!raw) return null;
        if (typeof raw === 'object') return raw;
        try {
            const parsed = JSON.parse(raw);
            if (parsed && typeof parsed === 'object') {
                return parsed;
            }
        } catch (e) {
            return null;
        }
        return null;
    };

    const matchesFilter = (mhs) => {
        const kls = mhs?.kelas || '';
        const nim = mhs?.nim || '';
        return (
            (filterKelas === '' || kls === filterKelas) &&
            (filterNim === '' || nim === filterNim)
        );
    };

    const renderRekapanChart = (data) => {
        if (!data || !data.rekapanJam || data.rekapanJam.length === 0) return;

        const canvas = document.getElementById('kaprodiRekapanChart');
        if (!canvas || !window.Chart) return;

        if (chartInstanceRef.current) {
            chartInstanceRef.current.destroy();
        }

        const ctx = canvas.getContext('2d');
        const labels = [];
        const dataPoints = [];
        const backgroundColors = [];

        const colors = [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#ec4899',
            '#06b6d4',
            '#84cc16',
            '#f43f5e',
            '#6366f1',
        ];

        data.rekapanJam.forEach((item, index) => {
            labels.push(item.kode + ' - ' + item.nama);
            dataPoints.push(item.total);
            backgroundColors.push(colors[index % colors.length]);
        });

        chartInstanceRef.current = new window.Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [
                    {
                        data: dataPoints,
                        backgroundColor: backgroundColors,
                        borderWidth: 1,
                        borderColor: '#ffffff',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Total Jam Logbook per Mata Kuliah',
                        font: { size: 16 },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                if (context.parsed !== null) label += context.parsed + ' Jam';
                                return label;
                            },
                        },
                    },
                },
            },
        });
    };

    const openRekapanModal = async (nim, nama) => {
        setSelectedMahasiswaNim(nim);
        setSelectedMahasiswaNama(nama);
        setShowRekapanModal(true);
        setRekapanData(null);
        setRekapanLoading(true);

        try {
            const response = await fetch('/external/api/rekapan-jam/' + nim);
            const data = await response.json();
            setRekapanData(data);
            setTimeout(() => {
                renderRekapanChart(data);
            }, 200);
        } catch (e) {
            alert('Gagal memuat data rekapan jam.');
        } finally {
            setRekapanLoading(false);
        }
    };

    const closeRekapanModal = () => {
        setShowRekapanModal(false);
        setSelectedMahasiswaNim(null);
        setSelectedMahasiswaNama('');
    };

    return (
        <AppLayout title="Dashboard Kaprodi">
            <div className="space-y-6">
                {/* Header Panel */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#e4e4e7] pb-4">
                    <div>
                        <h2 className="text-3xl font-black text-blue-900 tracking-wide">
                            Dashboard KPS
                        </h2>
                        <p className="text-gray-600 mt-1 font-medium">
                            Pantau dan kelola saran masukan serta form evaluasi industri.
                        </p>
                    </div>
                </div>

                {/* FILTER BAR NEO-BRUTALISM */}
                <div className="bg-white p-4 rounded-xl border border-[#e4e4e7] shadow-surface mb-8">
                    <h3 className="text-black font-black uppercase tracking-widest mb-3 text-sm flex items-center gap-2">
                        <svg
                            className="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                            ></path>
                        </svg>
                        Filter Data Spesifik
                    </h3>
                    <div className="flex flex-col sm:flex-row gap-4">
                        <div className="flex-1">
                            <select
                                value={filterKelas}
                                onChange={(e) => {
                                    setFilterKelas(e.target.value);
                                    setFilterNim('');
                                }}
                                className="w-full p-3 bg-white border border-[#e4e4e7] rounded-lg font-bold outline-none cursor-pointer focus:ring-2 focus:ring-black"
                            >
                                <option value="">-- SEMUA BATCH / KELAS --</option>
                                {(kelasAktif || []).map((kls) => (
                                    <option key={kls} value={kls}>
                                        {kls}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex-1">
                            <select
                                value={filterNim}
                                onChange={(e) => setFilterNim(e.target.value)}
                                className="w-full p-3 bg-white border border-[#e4e4e7] rounded-lg font-bold outline-none cursor-pointer focus:ring-2 focus:ring-black"
                            >
                                <option value="">-- SEMUA MAHASISWA --</option>
                                {(mahasiswas || [])
                                    .filter(
                                        (m) => filterKelas === '' || filterKelas === m.kelas,
                                    )
                                    .map((m) => (
                                        <option key={m.nim} value={m.nim}>
                                            {m.nama_mhs} ({m.kelas})
                                        </option>
                                    ))}
                            </select>
                        </div>
                    </div>
                </div>

                {/* ACTION BUTTONS: CETAK REKAP PDF */}
                <div className="mb-8 p-6 bg-white rounded-xl border border-[#e4e4e7] shadow-surface flex flex-col md:flex-row gap-6 justify-between items-center">
                    <div>
                        <h3 className="text-xl font-black uppercase tracking-widest text-black">
                            Cetak Transkrip / Rekapitulasi
                        </h3>
                        <p className="text-gray-600 font-bold mt-1 text-sm">
                            Download rekap nilai akhir PPI ke dalam format PDF.
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-3">
                        {/* Tombol Cetak Seluruh Kelas */}
                        <a
                            href="/external/kaprodi/rekap-all?export=pdf"
                            className="flex items-center gap-2 bg-red-600 text-white font-black px-5 py-2 border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                        >
                            <svg
                                className="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                ></path>
                            </svg>
                            Semua Kelas
                        </a>

                        {/* Looping Tombol per Kelas Aktif (3AEC1, 3AEC2, dsb) */}
                        {(kelasAktif || []).map((kls) => (
                            <a
                                key={kls}
                                href={`/external/kaprodi/rekap-all?export=pdf&kelas=${encodeURIComponent(
                                    kls,
                                )}`}
                                className="flex items-center gap-2 bg-blue-600 text-white font-black px-5 py-2 border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                            >
                                <svg
                                    className="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    ></path>
                                </svg>
                                Kelas {kls}
                            </a>
                        ))}
                    </div>
                </div>

                {/* MAIN CATEGORY: DARI MAHASISWA */}
                <div className="mb-10">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-3 bg-blue-100 text-blue-900 rounded-lg">
                            <svg
                                className="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                ></path>
                            </svg>
                        </div>
                        <h2 className="text-2xl font-black text-gray-800">
                            Data dari Mahasiswa
                        </h2>
                    </div>

                    {/* MAIN CARD: SARAN */}
                    <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                        <div className="bg-blue-900 p-4 flex justify-between items-center text-white">
                            <h3 className="text-xl font-black uppercase">
                                Daftar Saran dan Masukan
                            </h3>
                            <span className="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">
                                {sarans.length} Data
                            </span>
                        </div>

                        <div className="p-6 bg-gray-50 max-h-[600px] overflow-y-auto">
                            {sarans.length === 0 ? (
                                <div className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                                    Belum ada saran atau masukan dari mahasiswa.
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {sarans
                                        .filter((saran) => matchesFilter(saran.mahasiswa))
                                        .map((saran, idx) => {
                                            const saranData = parseJsonData(saran.saran);
                                            const prodiName =
                                                saran.mahasiswa?.program_studi?.nama_prodi ||
                                                saran.mahasiswa?.programStudi?.nama_prodi;
                                            return (
                                                <div
                                                    key={saran.id_saran || saran.id || idx}
                                                    className="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-yellow-400 transition-colors"
                                                >
                                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                                                        <div className="flex items-center gap-2">
                                                            <span className="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-1 rounded text-xs font-black">
                                                                {saran.tanggal}
                                                            </span>
                                                            <span className="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded text-xs font-black">
                                                                {saran.perusahaan}
                                                            </span>
                                                        </div>
                                                        <div className="text-sm font-bold text-gray-500">
                                                            Dari:{' '}
                                                            {saran.mahasiswa ? (
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        openRekapanModal(
                                                                            saran.mahasiswa.nim,
                                                                            saran.mahasiswa
                                                                                .nama_mhs,
                                                                        )
                                                                    }
                                                                    className="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none"
                                                                >
                                                                    {saran.mahasiswa.nama_mhs}
                                                                </button>
                                                            ) : (
                                                                <span className="text-gray-800">
                                                                    Mahasiswa
                                                                </span>
                                                            )}{' '}
                                                            {prodiName && (
                                                                <span className="text-xs text-gray-400">
                                                                    ({prodiName})
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                    {saranData ? (
                                                        <div className="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                                                            {Object.entries(saranData).map(
                                                                ([question, answer]) => (
                                                                    <div key={question}>
                                                                        <p className="text-xs font-black text-blue-900 uppercase tracking-wider mb-1">
                                                                            {question}
                                                                        </p>
                                                                        <p className="text-gray-800 font-medium leading-relaxed bg-white p-3 rounded-lg border border-gray-200">
                                                                            {answer}
                                                                        </p>
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>
                                                    ) : (
                                                        <p className="text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                                                            &quot;{saran.saran}&quot;
                                                        </p>
                                                    )}
                                                </div>
                                            );
                                        })}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* MAIN CATEGORY: DARI MENTOR */}
                <div className="mb-10">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-3 bg-purple-100 text-purple-900 rounded-lg">
                            <svg
                                className="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                ></path>
                            </svg>
                        </div>
                        <h2 className="text-2xl font-black text-gray-800">
                            Data dari Mentor (Perusahaan)
                        </h2>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* MAIN CARD: DISIPLIN */}
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                            <div className="bg-purple-900 p-4 flex justify-between items-center text-white">
                                <h3 className="text-lg font-black uppercase">
                                    Form Disiplin &amp; Prestasi
                                </h3>
                                <span className="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">
                                    {disiplins.length} Data
                                </span>
                            </div>
                            <div className="p-6 bg-gray-50 h-full max-h-[600px] overflow-y-auto">
                                {disiplins.length === 0 ? (
                                    <div className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                                        Belum ada data disiplin dan prestasi.
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {disiplins
                                            .filter((disiplin) =>
                                                matchesFilter(disiplin.mahasiswa),
                                            )
                                            .map((disiplin, idx) => {
                                                const disiplinData = parseJsonData(
                                                    disiplin.penilaian,
                                                );
                                                return (
                                                    <div
                                                        key={disiplin.id || idx}
                                                        className="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-purple-400 transition-colors"
                                                    >
                                                        <div className="flex flex-col gap-2 mb-3">
                                                            <div className="flex justify-between items-center">
                                                                <span className="bg-purple-100 text-purple-800 border border-purple-300 px-2 py-1 rounded text-xs font-black">
                                                                    {disiplin.tanggal}
                                                                </span>
                                                            </div>
                                                            <div className="text-sm font-bold text-gray-500">
                                                                Mhs:{' '}
                                                                {disiplin.mahasiswa ? (
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            openRekapanModal(
                                                                                disiplin.mahasiswa
                                                                                    .nim,
                                                                                disiplin.mahasiswa
                                                                                    .nama_mhs,
                                                                            )
                                                                        }
                                                                        className="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none"
                                                                    >
                                                                        {
                                                                            disiplin.mahasiswa
                                                                                .nama_mhs
                                                                        }
                                                                    </button>
                                                                ) : (
                                                                    <span className="text-gray-800">
                                                                        Mahasiswa
                                                                    </span>
                                                                )}
                                                                <br />
                                                                Mentor:{' '}
                                                                <span className="text-gray-800">
                                                                    {disiplin.mentor?.nama_pem ??
                                                                        'Mentor'}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div className="mt-4 border-t pt-4">
                                                            {disiplinData ? (
                                                                <div className="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-2">
                                                                    {Object.entries(
                                                                        disiplinData,
                                                                    ).map(([question, answer]) => (
                                                                        <div
                                                                            key={question}
                                                                            className="flex justify-between items-center border-b border-gray-200 pb-2 mb-2 last:border-0 last:mb-0 last:pb-0"
                                                                        >
                                                                            <span className="text-xs font-black text-purple-900 uppercase tracking-wider">
                                                                                {question}
                                                                            </span>
                                                                            <span className="text-gray-800 font-bold bg-white px-3 py-1 rounded border border-gray-200">
                                                                                {answer}
                                                                            </span>
                                                                        </div>
                                                                    ))}
                                                                </div>
                                                            ) : (
                                                                <p className="text-gray-500 italic text-sm">
                                                                    Data penilaian dalam format lama
                                                                    atau kosong.
                                                                </p>
                                                            )}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* MAIN CARD: KUISIONER */}
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                            <div className="bg-emerald-900 p-4 flex justify-between items-center text-white">
                                <h3 className="text-lg font-black uppercase">
                                    Evaluasi Kuisioner
                                </h3>
                                <span className="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">
                                    {kuisioners.length} Data
                                </span>
                            </div>
                            <div className="p-6 bg-gray-50 h-full max-h-[600px] overflow-y-auto">
                                {kuisioners.length === 0 ? (
                                    <div className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                                        Belum ada data evaluasi kuisioner.
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {kuisioners
                                            .filter((kuis) => matchesFilter(kuis.mahasiswa))
                                            .map((kuis, idx) => {
                                                const kuisData = parseJsonData(kuis.penilaian);
                                                return (
                                                    <div
                                                        key={kuis.id || idx}
                                                        className="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-emerald-400 transition-colors"
                                                    >
                                                        <div className="flex flex-col gap-2 mb-3">
                                                            <div className="flex justify-between items-center">
                                                                <span className="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded text-xs font-black">
                                                                    {kuis.tanggal}
                                                                </span>
                                                            </div>
                                                            <div className="text-sm font-bold text-gray-500">
                                                                Mhs:{' '}
                                                                {kuis.mahasiswa ? (
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            openRekapanModal(
                                                                                kuis.mahasiswa.nim,
                                                                                kuis.mahasiswa
                                                                                    .nama_mhs,
                                                                            )
                                                                        }
                                                                        className="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none"
                                                                    >
                                                                        {kuis.mahasiswa.nama_mhs}
                                                                    </button>
                                                                ) : (
                                                                    <span className="text-gray-800">
                                                                        Mahasiswa
                                                                    </span>
                                                                )}
                                                                <br />
                                                                Mentor:{' '}
                                                                <span className="text-gray-800">
                                                                    {kuis.mentor?.nama_pem ??
                                                                        'Mentor'}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div className="mt-4 border-t pt-4">
                                                            {kuisData ? (
                                                                <div className="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-2">
                                                                    {Object.entries(kuisData).map(
                                                                        ([question, answer]) => (
                                                                            <div
                                                                                key={question}
                                                                                className="flex justify-between items-center border-b border-gray-200 pb-2 mb-2 last:border-0 last:mb-0 last:pb-0"
                                                                            >
                                                                                <span className="text-xs font-black text-emerald-900 uppercase tracking-wider">
                                                                                    {question}
                                                                                </span>
                                                                                <span className="text-gray-800 font-bold bg-white px-3 py-1 rounded border border-gray-200">
                                                                                    {answer} / 4
                                                                                </span>
                                                                            </div>
                                                                        ),
                                                                    )}
                                                                </div>
                                                            ) : (
                                                                <p className="text-gray-500 italic text-sm">
                                                                    Data evaluasi dalam format lama
                                                                    atau kosong.
                                                                </p>
                                                            )}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* MAIN CARD: SARAN MENTOR */}
                <div className="mb-10">
                    <div className="bg-white rounded-xl border-2 border-fuchsia-900 shadow-surface overflow-hidden">
                        <div className="bg-fuchsia-900 p-4 flex justify-between items-center text-white">
                            <h3 className="text-xl font-black uppercase">
                                Daftar Saran dan Masukan dari Mentor
                            </h3>
                            <span className="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">
                                {(saranMentors || []).length} Data
                            </span>
                        </div>

                        <div className="p-6 bg-gray-50 max-h-[600px] overflow-y-auto">
                            {!saranMentors || saranMentors.length === 0 ? (
                                <div className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                                    Belum ada saran atau masukan dari mentor ke mahasiswa.
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {saranMentors
                                        .filter((saran) => matchesFilter(saran.mahasiswa))
                                        .map((saran, idx) => {
                                            const saranData = parseJsonData(saran.saran);
                                            const mentorName =
                                                saran.pembimbing_industri?.nama_pem ||
                                                saran.pembimbingIndustri?.nama_pem ||
                                                'Mentor';
                                            const prodiName =
                                                saran.mahasiswa?.program_studi?.nama_prodi ||
                                                saran.mahasiswa?.programStudi?.nama_prodi;
                                            return (
                                                <div
                                                    key={saran.id_saran_mhs || saran.id || idx}
                                                    className="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-fuchsia-400 transition-colors"
                                                >
                                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                                                        <div className="flex items-center gap-2">
                                                            <span className="bg-fuchsia-100 text-fuchsia-800 border border-fuchsia-300 px-2 py-1 rounded text-xs font-black">
                                                                {saran.tanggal}
                                                            </span>
                                                        </div>
                                                        <div className="text-sm font-bold text-gray-500">
                                                            Dari:{' '}
                                                            <span className="text-gray-800">
                                                                {mentorName}
                                                            </span>
                                                            <br />
                                                            Ke:{' '}
                                                            {saran.mahasiswa ? (
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        openRekapanModal(
                                                                            saran.mahasiswa.nim,
                                                                            saran.mahasiswa
                                                                                .nama_mhs,
                                                                        )
                                                                    }
                                                                    className="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none"
                                                                >
                                                                    {saran.mahasiswa.nama_mhs}
                                                                </button>
                                                            ) : (
                                                                <span className="text-gray-800">
                                                                    Mahasiswa
                                                                </span>
                                                            )}{' '}
                                                            {prodiName && (
                                                                <span className="text-xs text-gray-400">
                                                                    ({prodiName})
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                    {saranData ? (
                                                        <div className="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                                                            {Object.entries(saranData).map(
                                                                ([question, answer]) => (
                                                                    <div key={question}>
                                                                        <p className="text-xs font-black text-fuchsia-900 uppercase tracking-wider mb-1">
                                                                            {question}
                                                                        </p>
                                                                        <p className="text-gray-800 font-medium leading-relaxed bg-white p-3 rounded-lg border border-gray-200">
                                                                            {answer}
                                                                        </p>
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>
                                                    ) : (
                                                        <p className="text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                                                            &quot;{saran.saran}&quot;
                                                        </p>
                                                    )}
                                                </div>
                                            );
                                        })}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* MODAL REKAPAN JAM */}
                {showRekapanModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={closeRekapanModal}
                            ></div>

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border-4 border-cyan-900 rounded-xl flex flex-col max-h-[90vh]">
                                <div className="bg-cyan-900 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                                    <h3 className="text-xl md:text-2xl font-black">
                                        Rekapan Jam Logbook:{' '}
                                        <span>{selectedMahasiswaNama}</span>
                                    </h3>
                                    <button
                                        type="button"
                                        onClick={closeRekapanModal}
                                        className="hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            className="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>

                                <div className="p-6 bg-gray-50 flex-1 overflow-y-auto">
                                    {rekapanLoading && (
                                        <div className="text-center py-10">
                                            <p className="text-gray-500 font-bold">
                                                Memuat data...
                                            </p>
                                        </div>
                                    )}

                                    {!rekapanLoading && rekapanData && (
                                        <div className="space-y-6">
                                            <div className="bg-cyan-50 border border-cyan-200 text-cyan-800 p-3 rounded-lg font-bold flex justify-between">
                                                <span>Total Keseluruhan</span>
                                                <span>
                                                    <span>
                                                        {rekapanData?.totalJamKeseluruhan}
                                                    </span>{' '}
                                                    Jam
                                                </span>
                                            </div>

                                            {rekapanData?.rekapanJam?.length > 0 && (
                                                <div className="w-full flex justify-center bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                                                    <div
                                                        className="relative w-full max-w-lg"
                                                        style={{ height: '350px' }}
                                                    >
                                                        <canvas id="kaprodiRekapanChart"></canvas>
                                                    </div>
                                                </div>
                                            )}

                                            <div className="overflow-x-auto rounded-xl border border-gray-200">
                                                <table className="w-full text-left text-sm whitespace-nowrap min-w-max">
                                                    <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                                        <tr>
                                                            <th className="px-4 py-3 border-r border-gray-200 sticky left-0 bg-gray-50 z-10">
                                                                Mata Kuliah
                                                            </th>
                                                            <th className="px-4 py-3 text-center border-r border-gray-200 font-bold text-gray-900">
                                                                Total (Jam)
                                                            </th>
                                                            {Array.from(
                                                                { length: 20 },
                                                                (_, i) => i + 1,
                                                            ).map((i) => (
                                                                <th
                                                                    key={i}
                                                                    className="px-2 py-3 text-center border-r border-gray-200 text-[10px]"
                                                                >
                                                                    M<span>{i}</span>
                                                                </th>
                                                            ))}
                                                        </tr>
                                                    </thead>
                                                    <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                                        {(rekapanData?.rekapanJam || []).map(
                                                            (item, index) => (
                                                                <tr
                                                                    key={index}
                                                                    className="hover:bg-gray-50"
                                                                >
                                                                    <td className="px-4 py-2 border-r border-gray-100 sticky left-0 bg-white z-10 min-w-[200px]">
                                                                        <div
                                                                            className="truncate max-w-[250px]"
                                                                            title={item.nama}
                                                                        >
                                                                            <span className="font-bold text-gray-900">
                                                                                {item.kode}
                                                                            </span>
                                                                            <br />
                                                                            <span className="text-xs text-gray-500">
                                                                                {item.nama}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="px-4 py-2 text-center border-r border-gray-100 font-black text-cyan-600 bg-cyan-50/30">
                                                                        {item.total}
                                                                    </td>
                                                                    {Array.from(
                                                                        { length: 20 },
                                                                        (_, i) => i + 1,
                                                                    ).map((i) => (
                                                                        <td
                                                                            key={i}
                                                                            className="px-2 py-2 text-center border-r border-gray-100 text-[10px]"
                                                                        >
                                                                            {item.mingguan &&
                                                                            item.mingguan[i] > 0 ? (
                                                                                <span className="text-gray-800">
                                                                                    {
                                                                                        item
                                                                                            .mingguan[
                                                                                            i
                                                                                        ]
                                                                                    }
                                                                                </span>
                                                                            ) : (
                                                                                <span className="text-gray-300">
                                                                                    -
                                                                                </span>
                                                                            )}
                                                                        </td>
                                                                    ))}
                                                                </tr>
                                                            ),
                                                        )}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <div className="bg-gray-200 border-t border-[#e4e4e7] p-4 flex justify-end flex-shrink-0 rounded-b-xl">
                                    <button
                                        type="button"
                                        onClick={closeRekapanModal}
                                        className="bg-gray-500 text-white font-black px-8 py-3 rounded-xl border-2 border-gray-800 shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
