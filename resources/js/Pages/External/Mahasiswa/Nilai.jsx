import React, { useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const getLogbookGrade = (score) => {
    if (score >= 85) return 'A';
    if (score >= 80) return 'AB';
    if (score >= 70) return 'B';
    if (score >= 65) return 'BC';
    if (score >= 55) return 'C';
    if (score >= 40) return 'D';
    return 'E';
};

const formatScore = (score) => Number(score).toFixed(2);

const getAverage = (arr) => {
    if (!arr || arr.length === 0) return 0;
    const sum = arr.reduce((total, item) => total + (parseFloat(item.nilai) || 0), 0);
    return Number((sum / arr.length).toFixed(1));
};

export default function ExternalMahasiswaNilai({
    mahasiswa,
    penilaianDosen = null,
    penilaianMentor = null,
    logbooks = [],
    disiplin = null,
    kuisioner = null,
    parameters = [],
    approvals = {},
}) {
    const { auth } = usePage().props;
    const isMahasiswaRole = auth?.user?.role === 'mahasiswa';
    const isModalMode =
        typeof window !== 'undefined' &&
        new URLSearchParams(window.location.search).get('mode') === 'modal';

    // Hitung nilai akhir (1:1 dengan nilai.blade.php)
    const nilaiDosenPresentasi = Number(penilaianDosen?.n_presentasi ?? 0);
    const nilaiDosenMakalah = Number(penilaianDosen?.n_makalah ?? 0);
    const nilaiMentorPrestasi = Number(penilaianMentor?.n_prestasi ?? 0);
    const nilaiMentorSupervisi = Number(penilaianMentor?.n_supervisi ?? 0);

    const isComplete = Boolean(penilaianDosen && penilaianMentor);
    let total = 0;
    if (penilaianDosen) {
        total += (nilaiDosenPresentasi + nilaiDosenMakalah) / 2;
    }
    if (penilaianMentor) {
        total += (nilaiMentorPrestasi + nilaiMentorSupervisi) / 2;
    }
    const nilaiAkhir = isComplete ? Number((total / 2).toFixed(1)) : null;

    let nilaiHuruf = '-';
    let statusFinal = 'BELUM LENGKAP';
    if (isComplete) {
        if (nilaiAkhir >= 85) {
            nilaiHuruf = 'A';
            statusFinal = 'LULUS';
        } else if (nilaiAkhir >= 75) {
            nilaiHuruf = 'B';
            statusFinal = 'LULUS';
        } else if (nilaiAkhir >= 65) {
            nilaiHuruf = 'C';
            statusFinal = 'LULUS';
        } else if (nilaiAkhir >= 55) {
            nilaiHuruf = 'D';
            statusFinal = 'TIDAK LULUS';
        } else {
            nilaiHuruf = 'E';
            statusFinal = 'TIDAK LULUS';
        }
    }

    // Total durasi logbook per matkul (dalam jam)
    const totalJamKeseluruhan = Number(
        (
            logbooks.reduce((sum, lb) => sum + Number(lb.durasi_mnt || 0), 0) / 60
        ).toFixed(1)
    );

    const rekapanMap = {};
    logbooks.forEach((lb) => {
        const matkulKode = lb.kd_mat || 'Lainnya';
        const matkulRel = lb.mata_kuliah || lb.mataKuliah;
        const matkulNama = matkulRel ? matkulRel.nama_komp : matkulKode;
        const minggu = Number(lb.minggu_ke);
        const durasiMnt = Number(lb.durasi_mnt || 0);

        if (!rekapanMap[matkulKode]) {
            const mingguanMnt = {};
            for (let i = 1; i <= 20; i++) mingguanMnt[i] = 0;
            rekapanMap[matkulKode] = {
                kode: matkulKode,
                nama: matkulNama,
                total_mnt: 0,
                mingguan_mnt: mingguanMnt,
            };
        }

        if (minggu >= 1 && minggu <= 20) {
            rekapanMap[matkulKode].mingguan_mnt[minggu] += durasiMnt;
        }
        rekapanMap[matkulKode].total_mnt += durasiMnt;
    });

    const rekapanJam = Object.values(rekapanMap).map((v) => {
        const mingguan = {};
        for (let i = 1; i <= 20; i++) {
            mingguan[i] = Number((v.mingguan_mnt[i] / 60).toFixed(1));
        }
        return {
            kode: v.kode,
            nama: v.nama,
            total: Number((v.total_mnt / 60).toFixed(1)),
            mingguan,
        };
    });

    // Build initial mentor logbook scores (Minggu 1..20)
    const buildInitialMentorLogbook = () => {
        const list = [];
        for (let i = 1; i <= 20; i++) {
            const lbMinggu = logbooks.filter(
                (lb) => Number(lb.minggu_ke) === i && lb.nilai !== null && lb.nilai !== undefined
            );
            if (lbMinggu.length > 0) {
                const avg =
                    lbMinggu.reduce((s, x) => s + Number(x.nilai), 0) / lbMinggu.length;
                const nilaiLb = Number(avg.toFixed(2));
                const predikatLb =
                    lbMinggu.map((x) => x.predikat_nilai).find((p) => Boolean(p)) || null;
                list.push({ minggu: i, nilai: nilaiLb, predikat: predikatLb });
            } else {
                list.push({ minggu: i, nilai: null, predikat: null });
            }
        }
        return list;
    };

    const [mentorLogbook, setMentorLogbook] = useState(buildInitialMentorLogbook);
    const [logbookUpdatedAt, setLogbookUpdatedAt] = useState('Memuat...');
    const [isGeneratingPdf, setIsGeneratingPdf] = useState(false);

    // Catatan Mentor
    const logsWithCatatan = logbooks.filter(
        (lb) => lb.catatan_mentor && String(lb.catatan_mentor).trim() !== ''
    );
    const mentorCatatan =
        logsWithCatatan.length > 0
            ? logsWithCatatan[logsWithCatatan.length - 1].catatan_mentor
            : 'Tidak ada catatan khusus.';

    // Disiplin List
    const disiplinList = [];
    if (disiplin && disiplin.penilaian) {
        let dData = disiplin.penilaian;
        if (typeof dData === 'string') {
            try {
                dData = JSON.parse(dData);
            } catch {
                dData = null;
            }
        }
        if (dData && typeof dData === 'object') {
            Object.entries(dData).forEach(([k, v]) => {
                disiplinList.push({ kriteria: k, nilai: v });
            });
        }
    }

    // Hardskill & Softskill Lists
    const hardskillList = [];
    const softskillList = [];
    if (kuisioner && kuisioner.penilaian) {
        let kData = kuisioner.penilaian;
        if (typeof kData === 'string') {
            try {
                kData = JSON.parse(kData);
            } catch {
                kData = null;
            }
        }
        if (kData && typeof kData === 'object') {
            Object.entries(kData).forEach(([k, v]) => {
                let isHard = false;
                (parameters || []).forEach((param) => {
                    if (
                        param.jenis === 'kuisioner_mentor' &&
                        param.sub_kategori === 'HARDSKILL' &&
                        Array.isArray(param.indikator) &&
                        param.indikator.includes(k)
                    ) {
                        isHard = true;
                    }
                });
                if (isHard) {
                    hardskillList.push({ kriteria: k, nilai: v });
                } else {
                    softskillList.push({ kriteria: k, nilai: v });
                }
            });
        }
    }

    // Presentasi & Makalah Lists (Dosen)
    const presentasiList = [];
    const makalahList = [];
    if (penilaianDosen) {
        const detailPres = penilaianDosen.detail_presentasi || {};
        const detailMak = penilaianDosen.detail_makalah || {};
        (parameters || []).forEach((param) => {
            if (param.jenis === 'presentasi' && detailPres[param.id] !== undefined) {
                (param.indikator || []).forEach((indName, index) => {
                    presentasiList.push({
                        kriteria: indName,
                        nilai: detailPres[param.id][index] ?? 0,
                    });
                });
            } else if (param.jenis === 'makalah' && detailMak[param.id] !== undefined) {
                (param.indikator || []).forEach((indName, index) => {
                    makalahList.push({
                        kriteria: indName,
                        nilai: detailMak[param.id][index] ?? 0,
                    });
                });
            }
        });
    }

    // Approval State
    const getInitialApprovalItem = (key) => ({
        status: approvals?.[key]?.status || 'belum_mengajukan',
        alasan: approvals?.[key]?.alasan_reject || '',
        token: approvals?.[key]?.token_verifikasi || '',
    });

    const [approvalState, setApprovalState] = useState(() => ({
        disiplin: getInitialApprovalItem('disiplin'),
        kuisioner: getInitialApprovalItem('kuisioner'),
        presentasi: getInitialApprovalItem('presentasi'),
        makalah: getInitialApprovalItem('makalah'),
    }));

    const chartCanvasRef = useRef(null);
    const chartInstanceRef = useRef(null);

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const refreshLogbookScores = async () => {
        try {
            const response = await fetch('/external/mahasiswa/nilai/logbook', {
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) return;

            const result = await response.json();
            const scoresByWeek = new Map(
                (result.logbook || []).map((item) => [item.minggu, item])
            );
            setMentorLogbook((prev) =>
                prev.map((item) => {
                    const score = scoresByWeek.get(item.minggu);
                    return score
                        ? {
                              minggu: item.minggu,
                              nilai: Number(score.nilai),
                              predikat:
                                  score.predikat || getLogbookGrade(Number(score.nilai)),
                          }
                        : { minggu: item.minggu, nilai: null, predikat: null };
                })
            );
            if (result.updated_at) {
                setLogbookUpdatedAt(
                    new Date(result.updated_at).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    })
                );
            }
        } catch (error) {
            console.error('Gagal memperbarui nilai logbook:', error);
        }
    };

    useEffect(() => {
        refreshLogbookScores();
        const interval = setInterval(refreshLogbookScores, 15000);
        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        if (rekapanJam.length === 0 || !chartCanvasRef.current) return;

        const initChart = () => {
            if (!window.Chart || !chartCanvasRef.current) return;
            if (chartInstanceRef.current) {
                chartInstanceRef.current.destroy();
            }

            const ctx = chartCanvasRef.current.getContext('2d');
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

            const labels = rekapanJam.map((item) => `${item.kode} - ${item.nama}`);
            const dataPoints = rekapanJam.map((item) => item.total);
            const backgroundColors = rekapanJam.map(
                (_, idx) => colors[idx % colors.length]
            );

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
                    animation: { duration: 0 },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Total Jam Logbook per Mata Kuliah',
                            font: { size: 16 },
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' Jam';
                                    }
                                    return label;
                                },
                            },
                        },
                    },
                },
            });
        };

        if (window.Chart) {
            initChart();
        } else {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = initChart;
            document.head.appendChild(script);
        }

        return () => {
            if (chartInstanceRef.current) {
                chartInstanceRef.current.destroy();
            }
        };
    }, [logbooks.length]);

    const getAverageLogbook = () => {
        const validScores = mentorLogbook.filter((item) => item.nilai !== null);
        if (validScores.length === 0) return 0;
        const sum = validScores.reduce((acc, item) => acc + Number(item.nilai), 0);
        return Number((sum / validScores.length).toFixed(2));
    };

    const ajukanQR = async (formName) => {
        setApprovalState((prev) => ({
            ...prev,
            [formName]: { ...prev[formName], status: 'pending' },
        }));
        try {
            const response = await fetch('/external/mahasiswa/nilai/ajukan-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({ jenis_form: formName }),
            });
            const result = await response.json();

            if (result.status !== 'success') {
                alert(result.message);
                setApprovalState((prev) => ({
                    ...prev,
                    [formName]: { ...prev[formName], status: 'belum_mengajukan' },
                }));
            } else {
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan jaringan saat mengajukan TTD.');
            setApprovalState((prev) => ({
                ...prev,
                [formName]: { ...prev[formName], status: 'belum_mengajukan' },
            }));
        }
    };

    const batalkanQR = async (formName) => {
        if (!window.confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?'))
            return;

        const token = approvalState[formName]?.token;
        setApprovalState((prev) => ({
            ...prev,
            [formName]: { ...prev[formName], status: 'belum_mengajukan' },
        }));

        try {
            const response = await fetch('/external/mahasiswa/nilai/batal-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    jenis_form: formName,
                    token,
                }),
            });

            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message);
            }
            window.location.reload();
        } catch (error) {
            console.error('Gagal batal di database', error);
            alert('Terjadi kesalahan saat membatalkan pengajuan.');
            window.location.reload();
        }
    };

    const resetQR = async (formName) => {
        if (
            !window.confirm(
                'Apakah Anda ingin mereset status Verified ini? Data approval akan dihapus agar mahasiswa bisa mengajukan TTD ulang.'
            )
        )
            return;

        const token = approvalState[formName]?.token;
        try {
            const response = await fetch('/external/mahasiswa/nilai/batal-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    jenis_form: formName,
                    token,
                }),
            });

            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Gagal mereset pengajuan.');
            } else {
                window.location.reload();
            }
        } catch (error) {
            console.error('Gagal reset di database', error);
            alert('Terjadi kesalahan jaringan saat mereset pengajuan.');
            window.location.reload();
        }
    };

    const generatePDF = () => {
        const element = document.getElementById('transkrip-content');
        const nimStr = mahasiswa?.nim || '-';
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'Transkrip_Nilai_PPI_' + nimStr + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' },
        };

        setIsGeneratingPdf(true);

        const loadScriptAndGenerate = () => {
            window
                .html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    setIsGeneratingPdf(false);
                });
        };

        if (typeof window.html2pdf === 'undefined') {
            const script = document.createElement('script');
            script.src =
                'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = loadScriptAndGenerate;
            document.head.appendChild(script);
        } else {
            loadScriptAndGenerate();
        }
    };

    const getQrImageUrl = (token) => {
        const base =
            typeof window !== 'undefined' ? window.location.origin : '';
        return (
            'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' +
            encodeURIComponent(`${base}/external/approval/${token}`)
        );
    };

    const renderTtdBox = (formKey, title, includeResetOnAccepted = false) => {
        if (!isMahasiswaRole) return null;
        const item = approvalState[formKey] || {
            status: 'belum_mengajukan',
            alasan: '',
            token: '',
        };

        return (
            <div className="mt-5 flex justify-end">
                <div className="border border-gray-200 bg-gray-50/80 rounded-2xl p-5 w-56 text-center flex flex-col items-center justify-center min-h-[160px] shadow-sm relative">
                    <p className="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
                        {title}
                    </p>

                    {['belum_mengajukan'].includes(item.status) && (
                        <div className="w-full">
                            <button
                                type="button"
                                onClick={() => ajukanQR(formKey)}
                                className="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-gray-800 transition shadow-sm mb-2"
                            >
                                Ajukan TTD
                            </button>
                            <span className="text-[10px] text-gray-400 font-medium italic">
                                Belum disahkan
                            </span>
                        </div>
                    )}

                    {item.status === 'pending' && (
                        <div className="w-full flex flex-col items-center justify-center">
                            <span className="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">
                                Menunggu Acc
                            </span>
                            <button
                                type="button"
                                onClick={() => batalkanQR(formKey)}
                                className="flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full transition-colors shadow-sm"
                                title="Batalkan Pengajuan"
                            >
                                <svg
                                    className="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                            <span className="text-[9px] text-gray-400 mt-1">Batal</span>
                        </div>
                    )}

                    {item.status === 'rejected' && (
                        <div className="w-full">
                            <span className="bg-red-100 text-red-800 border border-red-200 font-black px-3 py-1 rounded text-xs block mb-2">
                                DITOLAK
                            </span>
                            <p
                                className="text-xs text-red-600 italic mb-3 leading-tight line-clamp-2"
                                title={item.alasan}
                            >
                                {item.alasan}
                            </p>
                            <button
                                type="button"
                                onClick={() => ajukanQR(formKey)}
                                className="w-full border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold py-1.5 rounded-lg transition"
                            >
                                Ajukan Ulang
                            </button>
                        </div>
                    )}

                    {item.status === 'accepted' && (
                        <div className="w-full flex flex-col items-center">
                            <div className="w-24 h-24 bg-white border border-gray-200 p-1.5 mb-2 rounded-lg shadow-sm">
                                <img
                                    src={getQrImageUrl(item.token)}
                                    alt="QR Code"
                                    className="w-full h-full object-contain"
                                />
                            </div>
                            <span className="text-emerald-600 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1">
                                <svg
                                    className="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2.5"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                Verified
                            </span>
                            {includeResetOnAccepted && (
                                <button
                                    type="button"
                                    onClick={() => resetQR(formKey)}
                                    className="text-[10px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-bold px-2.5 py-1 rounded-md transition shadow-sm flex items-center gap-1 mt-1"
                                >
                                    <svg
                                        className="w-3 h-3"
                                        fill="none"
                                        stroke="currentColor"
                                        strokeWidth="2.5"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                        />
                                    </svg>
                                    Reset / Acc Ulang
                                </button>
                            )}
                        </div>
                    )}
                </div>
            </div>
        );
    };

    return (
        <AppLayout title="Transkrip Nilai PPI">
            <div
                id="transkrip-content"
                className="space-y-8 max-w-[1400px] mx-auto pb-16 font-sans"
            >
                {/* Unified Header Panel (Matching Magang Dalam Kampus) */}
                <div
                    className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-surface"
                    data-html2canvas-ignore="true"
                >
                    <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#013981]" />
                            <span className="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">
                                MAGANG LUAR KAMPUS (INDUSTRI) · TRANSKRIP NILAI
                            </span>
                        </div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Transkrip &amp; Rincian Penilaian
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Rincian evaluasi dari Dosen Pembimbing dan Mentor Industri (PT).
                        </p>
                    </div>

                    {!isModalMode && (
                        <div className="flex flex-wrap gap-2.5">
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="ps-btn-secondary"
                            >
                                <span className="material-symbols-outlined text-[16px]">
                                    arrow_back
                                </span>
                                <span>Kembali</span>
                            </button>
                            <button
                                type="button"
                                onClick={generatePDF}
                                disabled={isGeneratingPdf}
                                className="ps-btn-primary"
                            >
                                {isGeneratingPdf ? (
                                    <>
                                        <svg
                                            className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                className="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                strokeWidth="4"
                                            />
                                            <path
                                                className="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            />
                                        </svg>
                                        <span>Memproses...</span>
                                    </>
                                ) : (
                                    <>
                                        <span className="material-symbols-outlined text-[16px]">
                                            download
                                        </span>
                                        <span>Download PDF</span>
                                    </>
                                )}
                            </button>
                        </div>
                    )}
                </div>

                {/* Unified Summary Hero Card (Matching Magang Dalam Kampus) */}
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 sm:p-7 shadow-surface flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div className="space-y-1.5">
                        <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c]">
                            Status Evaluasi PPI
                        </p>
                        <h2 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            {statusFinal}
                        </h2>
                        <p className="text-xs text-[#6a6f7c] pt-1">
                            Mahasiswa:{' '}
                            <span className="text-[#0b0b0c] font-semibold">
                                {mahasiswa?.nama_mhs || '-'}
                            </span>
                            <span className="mx-2 text-[#d4d4d8]">·</span>
                            NIM:{' '}
                            <span className="font-mono text-[#0b0b0c] font-medium">
                                {mahasiswa?.nim || '-'}
                            </span>
                        </p>
                    </div>
                    <div className="flex items-center gap-4 bg-[#fafafa] border border-[#e4e4e7] rounded-xl px-6 py-4 shrink-0">
                        <div>
                            <p className="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">
                                Nilai Akhir
                            </p>
                            <p className="font-mono text-3xl font-semibold text-[#0b0b0c] mt-0.5">
                                <span>{nilaiAkhir ?? '-'}</span>
                                <span className="text-xs text-[#6a6f7c] font-normal">
                                    /100
                                </span>
                            </p>
                        </div>
                        <div className="w-px h-10 bg-[#e4e4e7]" />
                        <div className="text-center">
                            <p className="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">
                                Predikat
                            </p>
                            <span className="inline-flex items-center justify-center font-mono text-xl font-semibold bg-[#013981] text-white w-10 h-10 rounded-lg mt-0.5">
                                {nilaiHuruf}
                            </span>
                        </div>
                    </div>
                </div>

                {/* BAGIAN 1: PENILAIAN INDUSTRI (MENTOR) */}
                <div className="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div className="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
                        <div className="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg
                                className="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 className="text-2xl font-bold text-gray-900 tracking-tight">
                                I. Penilaian Kinerja Industri
                            </h3>
                            <p className="text-sm font-medium text-gray-500 mt-0.5">
                                Penilai: Mentor PT / Pembimbing Lapangan
                            </p>
                        </div>
                    </div>

                    <div className="p-6 md:p-8 space-y-10">
                        {/* A. Logbook Mingguan (Tanpa TTD) */}
                        <div>
                            <div className="flex justify-between items-end mb-4">
                                <div>
                                    <h4 className="text-lg font-bold text-gray-800">
                                        A. Validasi Logbook Harian (Skala 0-100)
                                    </h4>
                                    <p className="text-xs font-medium text-gray-400 mt-1">
                                        Nilai tersinkron otomatis dari penilaian mentor.
                                    </p>
                                </div>
                                <div className="text-right">
                                    <span className="bg-emerald-50 text-emerald-700 font-black px-4 py-1.5 rounded-lg border border-emerald-200 text-sm">
                                        Rata-rata: <span>{getAverageLogbook()}</span>
                                    </span>
                                    <p className="text-[10px] font-medium text-gray-400 mt-2">
                                        Pembaruan: {logbookUpdatedAt}
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                                {mentorLogbook.map((item) => (
                                    <div
                                        key={item.minggu}
                                        className="border border-gray-200 rounded-xl p-3 flex justify-between items-center bg-gray-50/50 hover:border-emerald-300 transition-colors"
                                    >
                                        <span className="text-xs font-semibold text-gray-500">
                                            Minggu <span>{item.minggu}</span>
                                        </span>
                                        {item.nilai !== null ? (
                                            <div className="flex items-center gap-2">
                                                <span className="font-black text-gray-900 text-lg">
                                                    {formatScore(item.nilai)}
                                                </span>
                                                <span className="px-2 py-0.5 rounded border border-emerald-300 bg-emerald-50 text-xs font-black text-emerald-800">
                                                    {item.predikat ||
                                                        getLogbookGrade(item.nilai)}
                                                </span>
                                            </div>
                                        ) : (
                                            <span className="font-bold text-gray-300 text-sm">
                                                -
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* B. Tabel Disiplin & Supervisi */}
                        <div>
                            <h4 className="text-lg font-bold text-gray-800 mb-4">
                                B. Disiplin &amp; Prestasi Kerja (Skala 0-100)
                            </h4>
                            <div className="rounded-2xl border border-gray-200 overflow-hidden">
                                <table className="w-full text-left text-sm whitespace-nowrap">
                                    <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                        <tr>
                                            <th className="px-6 py-4 w-16 text-center">No</th>
                                            <th className="px-6 py-4 border-l border-gray-200">
                                                Kriteria Penilaian
                                            </th>
                                            <th className="px-6 py-4 w-32 text-center border-l border-gray-200">
                                                Nilai Raw
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                        {disiplinList.map((item, index) => (
                                            <tr
                                                key={index}
                                                className="hover:bg-gray-50 transition-colors"
                                            >
                                                <td className="px-6 py-3 text-center text-gray-400">
                                                    {index + 1}
                                                </td>
                                                <td className="px-6 py-3 border-l border-gray-100">
                                                    {item.kriteria}
                                                </td>
                                                <td className="px-6 py-3 text-center border-l border-gray-100 font-bold text-gray-900">
                                                    {item.nilai}
                                                </td>
                                            </tr>
                                        ))}
                                        <tr className="bg-gray-50 border-t border-[#e4e4e7]">
                                            <td
                                                colSpan={2}
                                                className="px-6 py-4 text-right font-bold text-gray-900 uppercase tracking-widest text-xs"
                                            >
                                                Total Rata-rata Prestasi &amp; Supervisi
                                            </td>
                                            <td className="px-6 py-4 text-center border-l border-gray-200 font-black text-blue-600 text-base">
                                                {getAverage(disiplinList)}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {/* TTD BOX DISIPLIN */}
                            {renderTtdBox('disiplin', 'TTD DISIPLIN', true)}
                        </div>

                        {/* C. Tabel Kuisioner Hardskill & Softskill */}
                        <div>
                            <h4 className="text-lg font-bold text-gray-800 mb-4">
                                C. Kuisioner Kompetensi (Skala 1-4)
                            </h4>
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {/* Hardskill */}
                                <div className="rounded-2xl border border-gray-200 overflow-hidden">
                                    <table className="w-full text-left text-sm whitespace-nowrap">
                                        <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                            <tr>
                                                <th className="px-4 py-3 border-r border-gray-200">
                                                    Hardskill
                                                </th>
                                                <th className="px-4 py-3 w-24 text-center">
                                                    Nilai (1-4)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                            {hardskillList.map((item, index) => (
                                                <tr
                                                    key={index}
                                                    className="hover:bg-gray-50 transition-colors"
                                                >
                                                    <td className="px-4 py-3 border-r border-gray-100 whitespace-normal">
                                                        {item.kriteria}
                                                    </td>
                                                    <td className="px-4 py-3 text-center font-bold text-gray-900">
                                                        {item.nilai}
                                                    </td>
                                                </tr>
                                            ))}
                                            <tr className="bg-gray-50 border-t border-gray-200">
                                                <td className="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">
                                                    Rata-rata
                                                </td>
                                                <td className="px-4 py-3 text-center font-black text-blue-600">
                                                    {getAverage(hardskillList)}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {/* Softskill */}
                                <div className="rounded-2xl border border-gray-200 overflow-hidden">
                                    <table className="w-full text-left text-sm whitespace-nowrap">
                                        <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                            <tr>
                                                <th className="px-4 py-3 border-r border-gray-200">
                                                    Softskill
                                                </th>
                                                <th className="px-4 py-3 w-24 text-center">
                                                    Nilai (1-4)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                            {softskillList.map((item, index) => (
                                                <tr
                                                    key={index}
                                                    className="hover:bg-gray-50 transition-colors"
                                                >
                                                    <td className="px-4 py-3 border-r border-gray-100 whitespace-normal">
                                                        {item.kriteria}
                                                    </td>
                                                    <td className="px-4 py-3 text-center font-bold text-gray-900">
                                                        {item.nilai}
                                                    </td>
                                                </tr>
                                            ))}
                                            <tr className="bg-gray-50 border-t border-gray-200">
                                                <td className="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">
                                                    Rata-rata
                                                </td>
                                                <td className="px-4 py-3 text-center font-black text-blue-600">
                                                    {getAverage(softskillList)}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {/* TTD BOX KUISIONER */}
                            {renderTtdBox('kuisioner', 'TTD KUISIONER')}
                        </div>

                        {/* D. Catatan Mentor */}
                        <div className="pt-4 border-t border-gray-100">
                            <div className="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100">
                                <p className="text-sm font-semibold text-emerald-800 uppercase tracking-wider mb-2">
                                    D. Catatan Industri / Evaluasi Khusus
                                </p>
                                <p className="text-sm text-gray-700 font-medium italic">
                                    {mentorCatatan}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* BAGIAN 2: PENILAIAN DOSEN PEMBIMBING */}
                <div className="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div className="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
                        <div className="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                            <svg
                                className="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 className="text-2xl font-bold text-gray-900 tracking-tight">
                                II. Penilaian Akademik
                            </h3>
                            <p className="text-sm font-medium text-gray-500 mt-0.5">
                                Penilai: Dosen Pembimbing Polman
                            </p>
                        </div>
                    </div>

                    <div className="p-6 md:p-8 space-y-8">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {/* Mutu Presentasi */}
                            <div>
                                <h4 className="text-lg font-bold text-gray-800 mb-4">
                                    A. Mutu Presentasi (Skala 0-100)
                                </h4>
                                <div className="rounded-2xl border border-gray-200 overflow-hidden mb-4">
                                    <table className="w-full text-left text-sm whitespace-nowrap">
                                        <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                            <tr>
                                                <th className="px-4 py-3 border-r border-gray-200">
                                                    Aspek Evaluasi
                                                </th>
                                                <th className="px-4 py-3 w-24 text-center">
                                                    Nilai Raw
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                            {presentasiList.map((item, index) => (
                                                <tr
                                                    key={index}
                                                    className="hover:bg-gray-50 transition-colors"
                                                >
                                                    <td className="px-4 py-3 border-r border-gray-100 whitespace-normal">
                                                        {item.kriteria}
                                                    </td>
                                                    <td className="px-4 py-3 text-center font-bold text-gray-900">
                                                        {item.nilai}
                                                    </td>
                                                </tr>
                                            ))}
                                            <tr className="bg-gray-50 border-t border-gray-200">
                                                <td className="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">
                                                    Rata-rata Presentasi
                                                </td>
                                                <td className="px-4 py-3 text-center font-black text-indigo-600">
                                                    {getAverage(presentasiList)}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {/* TTD BOX PRESENTASI */}
                                {renderTtdBox('presentasi', 'TTD PRESENTASI')}
                            </div>

                            {/* Penulisan Makalah */}
                            <div>
                                <h4 className="text-lg font-bold text-gray-800 mb-4">
                                    B. Penulisan Makalah (Skala 0-100)
                                </h4>
                                <div className="rounded-2xl border border-gray-200 overflow-hidden mb-4">
                                    <table className="w-full text-left text-sm whitespace-nowrap">
                                        <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                            <tr>
                                                <th className="px-4 py-3 border-r border-gray-200">
                                                    Aspek Evaluasi
                                                </th>
                                                <th className="px-4 py-3 w-24 text-center">
                                                    Nilai Raw
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                            {makalahList.map((item, index) => (
                                                <tr
                                                    key={index}
                                                    className="hover:bg-gray-50 transition-colors"
                                                >
                                                    <td className="px-4 py-3 border-r border-gray-100 whitespace-normal">
                                                        {item.kriteria}
                                                    </td>
                                                    <td className="px-4 py-3 text-center font-bold text-gray-900">
                                                        {item.nilai}
                                                    </td>
                                                </tr>
                                            ))}
                                            <tr className="bg-gray-50 border-t border-gray-200">
                                                <td className="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">
                                                    Rata-rata Makalah
                                                </td>
                                                <td className="px-4 py-3 text-center font-black text-indigo-600">
                                                    {getAverage(makalahList)}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {/* TTD BOX MAKALAH */}
                                {renderTtdBox('makalah', 'TTD MAKALAH')}
                            </div>
                        </div>

                        {/* Catatan Dosen */}
                        <div className="pt-4 border-t border-gray-100">
                            <div className="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 w-full">
                                <p className="text-sm font-semibold text-indigo-800 uppercase tracking-wider mb-2">
                                    C. Catatan Dosen Pembimbing
                                </p>
                                <p className="text-sm text-gray-700 font-medium italic">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* BAGIAN 3: REKAPAN JAM LOGBOOK */}
                <div className="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div className="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
                        <div className="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg
                                className="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 className="text-2xl font-bold text-gray-900 tracking-tight">
                                III. Rekapan Jam Logbook
                            </h3>
                            <p className="text-sm font-medium text-gray-500 mt-0.5">
                                Total Jam per Mata Kuliah (Mingguan &amp; Keseluruhan)
                            </p>
                        </div>
                    </div>

                    <div className="p-6 md:p-8 space-y-8">
                        <div className="flex justify-between items-end mb-4">
                            <h4 className="text-lg font-bold text-gray-800">
                                Detail Jam per Mata Kuliah
                            </h4>
                            <span className="bg-blue-50 text-blue-700 font-black px-4 py-1.5 rounded-lg border border-blue-200 text-sm">
                                Total Keseluruhan: <span>{totalJamKeseluruhan}</span> Jam
                            </span>
                        </div>

                        {/* Canvas Chart */}
                        {rekapanJam.length > 0 && (
                            <div className="w-full bg-white border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm flex flex-col items-center justify-center">
                                <div
                                    className="relative w-full max-w-lg"
                                    style={{ height: '380px' }}
                                >
                                    <canvas id="rekapanChart" ref={chartCanvasRef} />
                                </div>
                            </div>
                        )}

                        <div className="overflow-x-auto rounded-2xl border border-gray-200">
                            <table className="w-full text-left text-sm whitespace-nowrap min-w-max">
                                <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                    <tr>
                                        <th className="px-4 py-4 border-r border-gray-200 sticky left-0 bg-gray-50 z-10 min-w-[200px]">
                                            Mata Kuliah
                                        </th>
                                        <th className="px-4 py-4 text-center border-r border-gray-200 font-bold text-gray-900">
                                            Total (Jam)
                                        </th>
                                        {Array.from({ length: 20 }, (_, i) => i + 1).map(
                                            (i) => (
                                                <th
                                                    key={i}
                                                    className="px-2 py-4 text-center border-r border-gray-200 text-[10px]"
                                                >
                                                    M<span>{i}</span>
                                                </th>
                                            )
                                        )}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                    {rekapanJam.length > 0 ? (
                                        rekapanJam.map((item, index) => (
                                            <tr
                                                key={index}
                                                className="hover:bg-gray-50 transition-colors"
                                            >
                                                <td className="px-4 py-3 border-r border-gray-100 sticky left-0 bg-white z-10 min-w-[200px]">
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
                                                <td className="px-4 py-3 text-center border-r border-gray-100 font-black text-blue-600 bg-blue-50/30">
                                                    {item.total}
                                                </td>
                                                {Array.from(
                                                    { length: 20 },
                                                    (_, i) => i + 1
                                                ).map((i) => (
                                                    <td
                                                        key={i}
                                                        className="px-2 py-3 text-center border-r border-gray-50 text-xs"
                                                    >
                                                        {item.mingguan[i] > 0 ? (
                                                            <span className="text-gray-900 font-semibold">
                                                                {item.mingguan[i]}
                                                            </span>
                                                        ) : (
                                                            <span className="text-gray-300">
                                                                -
                                                            </span>
                                                        )}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                colSpan={22}
                                                className="px-4 py-8 text-center text-gray-500 italic"
                                            >
                                                Belum ada data logbook.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                @media print {
                    body * { visibility: hidden; }
                    #transkrip-content, #transkrip-content * { visibility: visible; }
                    #transkrip-content { position: absolute; left: 0; top: 0; width: 100%; }
                    button, a[href] { display: none !important; }
                    .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
                }
            `}</style>
        </AppLayout>
    );
}
