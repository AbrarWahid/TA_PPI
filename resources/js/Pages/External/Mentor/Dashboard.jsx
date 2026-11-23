import React, { useState, useEffect, useRef } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalMentorDashboard({
    mentor,
    mahasiswas = [],
    parameterSaranMentor = [],
    parameterDisiplin = [],
    parameterKuisioner = [],
    notifikasiTTD = [],
}) {
    const getGradeCodeForScore = (score) => {
        const numericScore = Number(score);
        if (numericScore >= 85) return 'A';
        if (numericScore >= 80) return 'AB';
        if (numericScore >= 70) return 'B';
        if (numericScore >= 65) return 'BC';
        if (numericScore >= 55) return 'C';
        if (numericScore >= 40) return 'D';
        return 'E';
    };

    const mapServerStudents = (serverStudents) => {
        const studentsArray = Array.isArray(serverStudents)
            ? serverStudents
            : Object.values(serverStudents || {});

        if (studentsArray.length === 0) return [];

        return studentsArray.map((mhs) => {
            const hasPenilaian = mhs.penilaians && mhs.penilaians.length > 0;
            const dataLogbookStudent = (mhs.logbooks || []).map((l) => ({
                id: l.id_log,
                minggu: l.minggu_ke || 1,
                tanggal: l.tanggal,
                matkul: l.kd_mat || '',
                deskripsi: l.kegiatan,
                durasiTotal: l.durasi_mnt ? ((l.durasi_mnt || 0) / 60).toFixed(1) : '0.0',
                status: l.status,
                nilai: l.nilai,
                predikat_nilai: l.predikat_nilai,
                catatan_mentor: l.catatan_mentor,
                fotos: (l.fotos || []).map((f) => f.foto_path),
            }));

            const nilaiLogbook = Array.from({ length: 20 }, () => ({
                status: 'Dinilai',
                catatan_mentor: '',
                nilai: null,
                predikat: '',
            }));

            dataLogbookStudent.forEach((logbook) => {
                const weekIndex = logbook.minggu - 1;
                if (
                    weekIndex < 0 ||
                    weekIndex >= nilaiLogbook.length ||
                    logbook.nilai === null ||
                    logbook.nilai === undefined
                )
                    return;

                nilaiLogbook[weekIndex] = {
                    status: 'Dinilai',
                    catatan_mentor: logbook.catatan_mentor || '',
                    nilai: logbook.nilai,
                    predikat: logbook.predikat_nilai || getGradeCodeForScore(logbook.nilai),
                };
            });

            const disiplinArr = mhs.disiplin_mahasiswas || mhs.disiplinMahasiswas || [];
            const kuisionerArr = mhs.kuisioner_mentors || mhs.kuisionerMentors || [];
            const saranArr = mhs.saran_mahasiswas || mhs.saranMahasiswas || [];

            return {
                id: mhs.id_mhs || mhs.nim,
                name: mhs.nama_mhs || '',
                nim: mhs.nim,
                kelas: mhs.kelas || '-',
                status: hasPenilaian ? 'Selesai' : 'Aktif',
                dataLogbookStudent,
                dataDisiplin:
                    disiplinArr.length > 0 && disiplinArr[0].penilaian ? disiplinArr[0] : null,
                dataKuisioner:
                    kuisionerArr.length > 0 && kuisionerArr[0].penilaian ? kuisionerArr[0] : null,
                saran_mentor: saranArr.length > 0,
                saran_mentor_teks: saranArr.length > 0 ? saranArr[0].saran : '',
                nilaiLogbook,
            };
        });
    };

    const [students, setStudents] = useState(() => mapServerStudents(mahasiswas));
    const [expandedIds, setExpandedIds] = useState({});
    const [expandedWeeks, setExpandedWeeks] = useState({});
    const [activeModal, setActiveModal] = useState(null);
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [rekapanData, setRekapanData] = useState(null);
    const [rekapanLoading, setRekapanLoading] = useState(false);
    const [iframeLoading, setIframeLoading] = useState(true);

    const [isEditingDisiplin, setIsEditingDisiplin] = useState(false);
    const [isEditingKuisioner, setIsEditingKuisioner] = useState(false);
    const [isEditingSaran, setIsEditingSaran] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const [formLogbook, setFormLogbook] = useState(() =>
        Array.from({ length: 20 }, () => ({
            status: 'Dinilai',
            catatan_mentor: '',
            nilai: null,
            predikat: '',
        })),
    );
    const [formDisiplin, setFormDisiplin] = useState({ answers: {} });
    const [formKuisioner, setFormKuisioner] = useState({ answers: {} });
    const [formSaran, setFormSaran] = useState({
        tanggal: new Date().toISOString().split('T')[0],
        saran: '',
        answers: {},
    });

    const chartInstanceRef = useRef(null);

    const paramSaranList = Array.isArray(parameterSaranMentor)
        ? parameterSaranMentor
        : Object.values(parameterSaranMentor || {});
    const paramDisiplinList = Array.isArray(parameterDisiplin)
        ? parameterDisiplin
        : Object.values(parameterDisiplin || {});
    const paramKuisionerList = Array.isArray(parameterKuisioner)
        ? parameterKuisioner
        : Object.values(parameterKuisioner || {});

    useEffect(() => {
        setStudents(mapServerStudents(mahasiswas));
    }, [mahasiswas]);

    useEffect(() => {
        if (!window.Chart && !document.getElementById('chartjs-cdn-script')) {
            const script = document.createElement('script');
            script.id = 'chartjs-cdn-script';
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.async = true;
            document.head.appendChild(script);
        }
    }, []);

    const toggleStudentExpanded = (id) => {
        setExpandedIds((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    const toggleWeekExpanded = (week) => {
        setExpandedWeeks((prev) => ({ ...prev, [week]: !prev[week] }));
    };

    const getModalTitle = () => {
        if (activeModal === 'logbook') return 'Validasi Isi & Nilai Logbook Mingguan';
        if (activeModal === 'disiplin') return 'Form Penilaian Disiplin, Prestasi & Supervisi';
        if (activeModal === 'kuisioner') return 'Kuisioner Penilaian Hardskill & Softskill';
        if (activeModal === 'saran') return 'Form Saran Mahasiswa';
        if (activeModal === 'rekapan') return 'Rekapan Jam Logbook Mingguan';
        if (activeModal === 'transkrip_full') return 'Transkrip Nilai Lengkap';
        return '';
    };

    const renderRekapanChart = (data) => {
        if (!data || !data.rekapanJam || data.rekapanJam.length === 0) return;
        const canvas = document.getElementById('mentorRekapanChart');
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
                },
            },
        });
    };

    const openModal = async (type, student) => {
        setActiveModal(type);
        setSelectedStudent(student);
        setIframeLoading(true);
        setExpandedWeeks({});

        setIsEditingDisiplin(false);
        setIsEditingKuisioner(false);
        setIsEditingSaran(false);

        if (type === 'logbook') {
            setFormLogbook(student.nilaiLogbook.map((item) => ({ ...item })));
        } else if (type === 'disiplin') {
            setFormDisiplin({ answers: {} });
        } else if (type === 'kuisioner') {
            setFormKuisioner({ answers: {} });
        } else if (type === 'rekapan' && student) {
            setRekapanData(null);
            setRekapanLoading(true);
            try {
                const response = await fetch('/external/api/rekapan-jam/' + student.nim);
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
        }
    };

    const closeModal = () => {
        setActiveModal(null);
        setSelectedStudent(null);
        setIsEditingDisiplin(false);
        setIsEditingKuisioner(false);
        setIsEditingSaran(false);
    };

    const enableEditDisiplin = () => {
        if (window.confirm('Apakah Anda ingin mengedit nilai mahasiswa ini?')) {
            setIsEditingDisiplin(true);
            try {
                const existing = JSON.parse(selectedStudent.dataDisiplin.penilaian);
                setFormDisiplin({ answers: { ...existing } });
            } catch (e) {
                setFormDisiplin({ answers: {} });
            }
        }
    };

    const enableEditKuisioner = () => {
        if (window.confirm('Apakah Anda ingin mengedit nilai kuisioner mahasiswa ini?')) {
            setIsEditingKuisioner(true);
            try {
                const existing = JSON.parse(selectedStudent.dataKuisioner.penilaian);
                setFormKuisioner({ answers: { ...existing } });
            } catch (e) {
                setFormKuisioner({ answers: {} });
            }
        }
    };

    const enableEditSaran = () => {
        if (window.confirm('Apakah Anda ingin mengedit saran untuk mahasiswa ini?')) {
            setIsEditingSaran(true);
            try {
                const existing = JSON.parse(selectedStudent.saran_mentor_teks);
                if (typeof existing === 'object' && existing !== null) {
                    setFormSaran((prev) => ({ ...prev, answers: { ...existing } }));
                } else {
                    setFormSaran((prev) => ({
                        ...prev,
                        saran: selectedStudent.saran_mentor_teks,
                    }));
                }
            } catch (e) {
                setFormSaran((prev) => ({
                    ...prev,
                    saran: selectedStudent.saran_mentor_teks,
                }));
            }
        }
    };

    const hasLogbookData = (weekNum) => {
        if (!selectedStudent) return false;
        return selectedStudent.dataLogbookStudent.some((entry) => entry.minggu === weekNum);
    };

    const getLogbookData = (weekNum) => {
        if (!selectedStudent) return [];
        return selectedStudent.dataLogbookStudent.filter((entry) => entry.minggu === weekNum);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const options = { weekday: 'short', day: '2-digit', month: 'short' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    const handleLogbookScoreChange = (weekNumber, rawValue) => {
        const nilai = rawValue === '' ? null : Number(rawValue);
        const predikat =
            rawValue === null || rawValue === '' ? '' : getGradeCodeForScore(rawValue);
        setFormLogbook((prev) =>
            prev.map((item, idx) =>
                idx === weekNumber - 1 ? { ...item, nilai, predikat } : item,
            ),
        );
    };

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const saveData = async (forcedType = null) => {
        if (!selectedStudent) return;
        const typeToSave = forcedType || activeModal;

        if (typeToSave === 'disiplin' || typeToSave === 'kuisioner') {
            if (
                !window.confirm(
                    'Apakah Anda ingin menyimpan/memperbarui penilaian mahasiswa ini?',
                )
            ) {
                return;
            }
        }

        if (typeToSave === 'logbook') {
            try {
                const validLogbooks = selectedStudent.dataLogbookStudent
                    .map((l) => {
                        const weekIndex = l.minggu - 1;
                        const formEntry = formLogbook[weekIndex];
                        return {
                            id: l.id,
                            status: formEntry ? formEntry.status : 'Dinilai',
                            catatan_mentor: formEntry ? formEntry.catatan_mentor : '',
                            nilai: formEntry ? formEntry.nilai : null,
                        };
                    })
                    .filter((l) => l.nilai !== null && l.nilai !== '');

                const res = await fetch('/external/mentor/logbook', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                    body: JSON.stringify({
                        nim: selectedStudent.nim,
                        logbooks: validLogbooks,
                    }),
                });
                const data = await res.json();
                if (res.ok) {
                    const updatedNilai = [...formLogbook];
                    setStudents((prev) =>
                        prev.map((s) =>
                            s.id === selectedStudent.id
                                ? { ...s, nilaiLogbook: updatedNilai, status: 'Selesai' }
                                : s,
                        ),
                    );
                    alert(data.message || 'Evaluasi Logbook berhasil disimpan!');
                    closeModal();
                } else {
                    alert(data.message || 'Gagal menyimpan evaluasi logbook');
                }
            } catch (e) {
                console.error(e);
            }
        } else if (typeToSave === 'disiplin') {
            try {
                const serialized = JSON.stringify(formDisiplin.answers);
                const res = await fetch('/external/mentor/disiplin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                    body: JSON.stringify({
                        nim: selectedStudent.nim,
                        penilaian: serialized,
                    }),
                });
                const data = await res.json();
                if (res.ok) {
                    setStudents((prev) =>
                        prev.map((s) =>
                            s.id === selectedStudent.id
                                ? { ...s, dataDisiplin: { penilaian: serialized } }
                                : s,
                        ),
                    );
                    setIsEditingDisiplin(false);
                    alert(data.message);
                    closeModal();
                } else {
                    alert(data.message);
                }
            } catch (e) {
                console.error(e);
            }
        } else if (typeToSave === 'kuisioner') {
            try {
                const serialized = JSON.stringify(formKuisioner.answers);
                const res = await fetch('/external/mentor/kuisioner', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                    body: JSON.stringify({
                        nim: selectedStudent.nim,
                        penilaian: serialized,
                    }),
                });
                const data = await res.json();
                if (res.ok) {
                    setStudents((prev) =>
                        prev.map((s) =>
                            s.id === selectedStudent.id
                                ? { ...s, dataKuisioner: { penilaian: serialized } }
                                : s,
                        ),
                    );
                    setIsEditingKuisioner(false);
                    alert(data.message);
                    closeModal();
                } else {
                    alert(data.message);
                }
            } catch (e) {
                console.error(e);
            }
        }
    };

    const submitSaranMentor = async (e) => {
        if (e) e.preventDefault();
        if (!selectedStudent) return;

        if (
            !window.confirm(
                'Apakah Anda ingin mengirim/memperbarui saran untuk mahasiswa ini?',
            )
        ) {
            return;
        }

        setIsSubmitting(true);

        let finalSaran = '';
        if (Object.keys(formSaran.answers).length > 0) {
            finalSaran = JSON.stringify(formSaran.answers);
        } else {
            finalSaran = formSaran.saran;
            if (!finalSaran.trim()) {
                setIsSubmitting(false);
                return;
            }
        }

        try {
            const response = await fetch('/external/mentor/saran', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify({
                    nim: selectedStudent.nim,
                    tanggal: formSaran.tanggal,
                    saran: finalSaran,
                }),
            });

            const result = await response.json();

            if (!response.ok) {
                alert(result.message || 'Gagal mengirim saran.');
            } else {
                alert(result.message || 'Saran berhasil disimpan.');
                setStudents((prev) =>
                    prev.map((s) =>
                        s.id === selectedStudent.id
                            ? { ...s, saran_mentor: true, saran_mentor_teks: finalSaran }
                            : s,
                    ),
                );
                setIsEditingSaran(false);
                setFormSaran((prev) => ({ ...prev, saran: '', answers: {} }));
                closeModal();
            }
        } catch (error) {
            console.error('Error submitting saran:', error);
            alert('Terjadi kesalahan sistem.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const renderSaranTeks = (teks) => {
        if (!teks) return '';
        try {
            const data = JSON.parse(teks);
            if (typeof data === 'object' && data !== null) {
                let html = '';
                for (const [key, value] of Object.entries(data)) {
                    html += `<div class="mb-2"><p class="text-xs font-black text-emerald-900 uppercase tracking-wider mb-1">${key}</p><p class="text-gray-800 text-sm font-medium bg-gray-50 p-2 rounded border border-gray-100">${value}</p></div>`;
                }
                return html;
            }
        } catch (e) {
            return `<p class="text-gray-700 italic">${teks}</p>`;
        }
        return `<p class="text-gray-700 italic">${teks}</p>`;
    };

    return (
        <AppLayout title="Dashboard Mentor Industri">
            <div className="space-y-6">
                {/* Header Panel */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#e4e4e7] pb-4">
                    <div>
                        <h2 className="text-3xl font-black text-blue-900 tracking-wide">
                            Validasi &amp; Penilaian Industri
                        </h2>
                        <p className="text-gray-600 mt-1 font-medium">
                            Evaluasi kinerja, kedisiplinan, dan hardskill/softskill mahasiswa magang.
                        </p>
                    </div>
                </div>

                {/* Statistik Mentor */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-blue-100 p-6 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-lg font-bold text-blue-900">Mahasiswa Aktif</h3>
                        <p className="text-4xl font-black text-blue-700 mt-2">
                            {students.length}
                        </p>
                    </div>
                    <div className="bg-orange-100 p-6 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-lg font-bold text-orange-900">Menunggu Validasi</h3>
                        <p className="text-4xl font-black text-orange-700 mt-2">
                            {students.filter((s) => s.status === 'Pending').length}
                        </p>
                    </div>
                    <div className="bg-green-100 p-6 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-lg font-bold text-green-900">Selesai Evaluasi</h3>
                        <p className="text-4xl font-black text-green-700 mt-2">
                            {students.filter((s) => s.status === 'Selesai').length}
                        </p>
                    </div>
                </div>

                {/* Daftar Mahasiswa (Accordion Style) */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface mt-8 overflow-hidden">
                    <div className="bg-blue-50 p-4 border-b border-[#e4e4e7]">
                        <h3 className="text-xl font-black text-blue-900">
                            Daftar Mahasiswa di Perusahaan Anda
                        </h3>
                    </div>

                    <div className="divide-y divide-blue-100">
                        {students.map((student) => {
                            const expanded = !!expandedIds[student.id];
                            return (
                                <div key={student.id} className="bg-white transition-colors">
                                    {/* Header Baris (Clickable) */}
                                    <div
                                        onClick={() => toggleStudentExpanded(student.id)}
                                        className="p-4 cursor-pointer hover:bg-blue-50 flex justify-between items-center"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 bg-blue-200 border border-[#e4e4e7] rounded-lg flex items-center justify-center font-black text-blue-900 text-xl shadow-surface">
                                                <span>{(student.name || '').charAt(0)}</span>
                                            </div>
                                            <div>
                                                <h4 className="text-xl font-black text-blue-900">
                                                    {student.name}
                                                </h4>
                                                <p className="text-sm font-bold text-gray-500">
                                                    {student.nim + ' | ' + student.kelas}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <span
                                                className={`px-3 py-1 rounded text-xs font-bold border-2 ${
                                                    student.status === 'Selesai'
                                                        ? 'bg-green-100 text-green-800 border-green-300'
                                                        : 'bg-orange-100 text-orange-800 border-orange-300'
                                                }`}
                                            >
                                                {student.status}
                                            </span>
                                            <svg
                                                className={`w-6 h-6 text-gray-500 transition-transform ${
                                                    expanded ? 'rotate-180' : ''
                                                }`}
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </div>
                                    </div>

                                    {/* Panel Aksi Tersembunyi */}
                                    {expanded && (
                                        <div className="p-4 bg-gray-50 border-t border-[#e4e4e7] flex flex-col md:flex-row gap-4 justify-center">
                                            <button
                                                type="button"
                                                onClick={() => openModal('logbook', student)}
                                                className="flex-1 bg-white border border-[#e4e4e7] p-4 rounded-xl shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group"
                                            >
                                                <h5 className="font-black text-blue-900 text-lg group-hover:text-blue-600">
                                                    Validasi Logbook
                                                </h5>
                                                <p className="text-xs font-bold text-gray-500 mt-1">
                                                    Cek deskripsi kegiatan &amp; Beri Nilai
                                                </p>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => openModal('disiplin', student)}
                                                className="flex-1 bg-white border border-[#e4e4e7] p-4 rounded-xl shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group"
                                            >
                                                <h5 className="font-black text-blue-900 text-lg group-hover:text-blue-600">
                                                    Disiplin &amp; Prestasi
                                                </h5>
                                                <p className="text-xs font-bold text-gray-500 mt-1">
                                                    Evaluasi kerajinan &amp; supervisi kerja
                                                </p>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => openModal('kuisioner', student)}
                                                className="flex-1 bg-white border border-[#e4e4e7] p-4 rounded-xl shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group"
                                            >
                                                <h5 className="font-black text-blue-900 text-lg group-hover:text-blue-600">
                                                    Form Kuisioner
                                                </h5>
                                                <p className="text-xs font-bold text-gray-500 mt-1">
                                                    Penilaian Hardskill &amp; Softskill (1-4)
                                                </p>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => openModal('saran', student)}
                                                className="flex-1 bg-white border border-[#e4e4e7] p-4 rounded-xl shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group"
                                            >
                                                <h5 className="font-black text-yellow-700 text-lg group-hover:text-yellow-600">
                                                    Saran Mahasiswa
                                                </h5>
                                                <p className="text-xs font-bold text-gray-500 mt-1">
                                                    Berikan masukan &amp; saran ke mahasiswa
                                                </p>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    openModal('transkrip_full', student)
                                                }
                                                className="flex-1 bg-white border border-[#e4e4e7] p-4 rounded-xl shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group text-left block w-full"
                                            >
                                                <h5 className="font-black text-cyan-700 text-lg group-hover:text-cyan-600">
                                                    Transkrip &amp; Logbook
                                                </h5>
                                                <p className="text-xs font-bold text-gray-500 mt-1">
                                                    Lihat grafik jam &amp; rekap nilai
                                                </p>
                                            </button>
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* MODAL GLOBAL */}
                {activeModal !== null && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={closeModal}
                            ></div>

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border border-[#e4e4e7] rounded-xl flex flex-col max-h-[90vh]">
                                {/* Modal Header */}
                                <div className="bg-blue-900 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                                    <h3 className="text-xl md:text-2xl font-black">
                                        {getModalTitle()}
                                    </h3>
                                    <button
                                        type="button"
                                        onClick={closeModal}
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

                                {/* Modal Body (Scrollable) */}
                                <div className="p-6 bg-gray-50 flex-1 overflow-y-auto">
                                    {/* Info Mahasiswa */}
                                    <div className="mb-6 bg-white p-4 rounded-lg border-2 border-blue-200 flex flex-wrap gap-6">
                                        <div>
                                            <p className="text-xs font-bold text-gray-500 uppercase">
                                                Nama Mahasiswa
                                            </p>
                                            <p className="font-black text-blue-900 text-lg">
                                                {selectedStudent?.name}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-bold text-gray-500 uppercase">
                                                NIM / Kelas
                                            </p>
                                            <p className="font-black text-blue-900 text-lg">
                                                {selectedStudent?.nim +
                                                    ' / ' +
                                                    selectedStudent?.kelas}
                                            </p>
                                        </div>
                                    </div>

                                    {/* KONTEN 1: LOGBOOK */}
                                    {activeModal === 'logbook' && (
                                        <div className="bg-white p-6 rounded-xl border border-[#e4e4e7] shadow-surface">
                                            <div className="flex justify-between items-center border-b border-[#e4e4e7] pb-2 mb-4">
                                                <p className="text-sm font-bold text-gray-600">
                                                    Periksa deskripsi kegiatan mahasiswa, lalu
                                                    masukkan nilai 0–100. Anda dapat mengubah nilai
                                                    kapan saja dengan menekan tombol Simpan.
                                                </p>
                                            </div>

                                            <div className="space-y-4">
                                                {Array.from({ length: 20 }, (_, idx) => idx + 1).map(
                                                    (w) => {
                                                        const weekExpanded = !!expandedWeeks[w];
                                                        const hasData = hasLogbookData(w);
                                                        const entries = getLogbookData(w);
                                                        return (
                                                            <div
                                                                key={w}
                                                                className="border-2 border-blue-200 rounded-lg overflow-hidden bg-white"
                                                            >
                                                                <div
                                                                    className="bg-blue-50 p-3 flex justify-between items-center cursor-pointer hover:bg-blue-100 transition-colors"
                                                                    onClick={() =>
                                                                        toggleWeekExpanded(w)
                                                                    }
                                                                >
                                                                    <div className="flex items-center gap-3">
                                                                        <svg
                                                                            className={`w-5 h-5 text-blue-800 transition-transform ${
                                                                                weekExpanded
                                                                                    ? 'rotate-90'
                                                                                    : ''
                                                                            }`}
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            viewBox="0 0 24 24"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M9 5l7 7-7 7"
                                                                            ></path>
                                                                        </svg>
                                                                        <span className="font-black text-blue-900">
                                                                            {'Minggu ke-' + w}
                                                                        </span>
                                                                        {hasData && (
                                                                            <span className="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full font-bold ml-2">
                                                                                Ada Data
                                                                            </span>
                                                                        )}
                                                                    </div>

                                                                    <div
                                                                        className="flex items-center gap-3"
                                                                        onClick={(e) =>
                                                                            e.stopPropagation()
                                                                        }
                                                                    >
                                                                        <label className="text-sm font-bold text-gray-700">
                                                                            Nilai:
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            min="0"
                                                                            max="100"
                                                                            step="0.01"
                                                                            placeholder="0"
                                                                            value={
                                                                                formLogbook[w - 1]
                                                                                    ?.nilai ?? ''
                                                                            }
                                                                            onChange={(e) =>
                                                                                handleLogbookScoreChange(
                                                                                    w,
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            disabled={!hasData}
                                                                            className={`w-20 border-2 border-blue-300 rounded p-1.5 text-center font-bold outline-none ${
                                                                                hasData
                                                                                    ? 'bg-white focus:border-blue-600 text-blue-700'
                                                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                                            }`}
                                                                        />
                                                                        <span className="min-w-20 px-2 py-1.5 rounded border-2 border-emerald-300 bg-emerald-50 text-center text-sm font-black text-emerald-800">
                                                                            {formLogbook[w - 1]
                                                                                ?.predikat || '-'}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                {weekExpanded && (
                                                                    <div className="border-t border-[#e4e4e7]">
                                                                        {!hasData ? (
                                                                            <div className="p-4 text-center text-gray-400 font-bold text-sm italic">
                                                                                Mahasiswa belum
                                                                                mengisi logbook pada
                                                                                minggu ini.
                                                                            </div>
                                                                        ) : (
                                                                            <div className="overflow-x-auto p-4">
                                                                                <table className="w-full text-left text-sm border-collapse">
                                                                                    <thead>
                                                                                        <tr className="border-b border-[#e4e4e7] text-gray-600">
                                                                                            <th className="pb-2 font-bold w-1/5">
                                                                                                Tanggal
                                                                                            </th>
                                                                                            <th className="pb-2 font-bold w-1/5">
                                                                                                Mata
                                                                                                Kuliah
                                                                                            </th>
                                                                                            <th className="pb-2 font-bold w-2/5">
                                                                                                Deskripsi
                                                                                                Pekerjaan
                                                                                            </th>
                                                                                            <th className="pb-2 font-bold w-1/5 text-center">
                                                                                                Durasi
                                                                                            </th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        {entries.map(
                                                                                            (
                                                                                                entry,
                                                                                            ) => (
                                                                                                <tr
                                                                                                    key={
                                                                                                        entry.id
                                                                                                    }
                                                                                                    className="border-b border-gray-100 hover:bg-gray-50"
                                                                                                >
                                                                                                    <td className="py-3 font-semibold text-gray-700">
                                                                                                        {formatDate(
                                                                                                            entry.tanggal,
                                                                                                        )}
                                                                                                    </td>
                                                                                                    <td className="py-3">
                                                                                                        <span className="bg-blue-900 text-white px-2 py-1 rounded text-xs font-bold">
                                                                                                            {
                                                                                                                entry.matkul
                                                                                                            }
                                                                                                        </span>
                                                                                                    </td>
                                                                                                    <td className="py-3 text-gray-800 font-medium leading-relaxed pr-4">
                                                                                                        {
                                                                                                            entry.deskripsi
                                                                                                        }
                                                                                                    </td>
                                                                                                    <td className="py-3 text-center font-black text-blue-700">
                                                                                                        {entry.durasiTotal +
                                                                                                            ' J'}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            ),
                                                                                        )}
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        );
                                                    },
                                                )}
                                            </div>
                                        </div>
                                    )}

                                    {/* KONTEN 2: DISIPLIN & PRESTASI */}
                                    {activeModal === 'disiplin' && (
                                        <div className="bg-white p-5 md:p-8 rounded-xl border border-[#e4e4e7] shadow-surface">
                                            <div className="flex justify-between items-center mb-6 border-l-4 border-purple-500 pl-3">
                                                <h3 className="text-2xl font-black text-purple-900">
                                                    Form Disiplin &amp; Prestasi
                                                </h3>
                                                {selectedStudent?.dataDisiplin &&
                                                    !isEditingDisiplin && (
                                                        <button
                                                            type="button"
                                                            onClick={enableEditDisiplin}
                                                            className="bg-amber-500 hover:bg-amber-600 text-white text-xs font-black px-3 py-1.5 rounded-lg border border-[#e4e4e7] shadow-surface"
                                                        >
                                                            Edit Nilai
                                                        </button>
                                                    )}
                                            </div>

                                            {/* Tampilan Status Sudah Dinilai */}
                                            {selectedStudent?.dataDisiplin && !isEditingDisiplin ? (
                                                <div className="space-y-4">
                                                    <div className="p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                                        <svg
                                                            className="w-6 h-6 text-emerald-600 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            strokeWidth="2.5"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            ></path>
                                                        </svg>
                                                        <div className="w-full">
                                                            <h4 className="font-black text-emerald-900">
                                                                Sudah Dinilai
                                                            </h4>
                                                            <p className="text-sm font-medium text-emerald-700 mb-3">
                                                                Anda sudah memberikan penilaian
                                                                untuk form ini. Klik tombol
                                                                &quot;Edit Nilai&quot; di atas jika
                                                                ingin mengubahnya.
                                                            </p>
                                                            <div
                                                                className="bg-white border border-emerald-200 rounded-lg p-4 space-y-3"
                                                                dangerouslySetInnerHTML={{
                                                                    __html: renderSaranTeks(
                                                                        selectedStudent.dataDisiplin
                                                                            .penilaian,
                                                                    ),
                                                                }}
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ) : (
                                                /* Form Input / Edit */
                                                <form
                                                    onSubmit={(e) => {
                                                        e.preventDefault();
                                                        saveData('disiplin');
                                                    }}
                                                    className="space-y-4"
                                                >
                                                    {paramDisiplinList.length > 0 ? (
                                                        <div className="space-y-4">
                                                            {paramDisiplinList.map((param) => (
                                                                <div
                                                                    key={param.id}
                                                                    className="bg-purple-50 p-4 rounded-xl border border-purple-200"
                                                                >
                                                                    <h4 className="font-black text-purple-900 mb-3">
                                                                        {param.sub_kategori}
                                                                    </h4>
                                                                    <div className="space-y-4">
                                                                        {(
                                                                            param.indikator || []
                                                                        ).map((indikator, idx) => (
                                                                            <div
                                                                                key={idx}
                                                                                className="flex justify-between items-center bg-white p-2 rounded border border-purple-100"
                                                                            >
                                                                                <span className="text-sm font-bold text-gray-700">
                                                                                    {indikator}
                                                                                </span>
                                                                                <input
                                                                                    type="number"
                                                                                    min="0"
                                                                                    max="100"
                                                                                    required
                                                                                    value={
                                                                                        formDisiplin
                                                                                            .answers[
                                                                                            indikator
                                                                                        ] ?? ''
                                                                                    }
                                                                                    onChange={(
                                                                                        e,
                                                                                    ) =>
                                                                                        setFormDisiplin(
                                                                                            (
                                                                                                prev,
                                                                                            ) => ({
                                                                                                ...prev,
                                                                                                answers:
                                                                                                    {
                                                                                                        ...prev.answers,
                                                                                                        [indikator]:
                                                                                                            e
                                                                                                                .target
                                                                                                                .value ===
                                                                                                            ''
                                                                                                                ? ''
                                                                                                                : Number(
                                                                                                                      e
                                                                                                                          .target
                                                                                                                          .value,
                                                                                                                  ),
                                                                                                    },
                                                                                            }),
                                                                                        )
                                                                                    }
                                                                                    className="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold focus:border-purple-600 outline-none"
                                                                                />
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    ) : (
                                                        <p className="text-gray-500 italic">
                                                            Belum ada indikator disiplin dari
                                                            Kaprodi.
                                                        </p>
                                                    )}
                                                </form>
                                            )}
                                        </div>
                                    )}

                                    {/* KONTEN 3: KUISIONER */}
                                    {activeModal === 'kuisioner' && (
                                        <div className="bg-white p-5 md:p-8 rounded-xl border border-[#e4e4e7] shadow-surface">
                                            <div className="flex justify-between items-center mb-6 border-l-4 border-emerald-500 pl-3">
                                                <h3 className="text-2xl font-black text-emerald-900">
                                                    Evaluasi Kuisioner
                                                </h3>
                                                {selectedStudent?.dataKuisioner &&
                                                    !isEditingKuisioner && (
                                                        <button
                                                            type="button"
                                                            onClick={enableEditKuisioner}
                                                            className="bg-amber-500 hover:bg-amber-600 text-white text-xs font-black px-3 py-1.5 rounded-lg border border-[#e4e4e7] shadow-surface"
                                                        >
                                                            Edit Nilai
                                                        </button>
                                                    )}
                                            </div>

                                            <div className="flex flex-wrap items-center gap-4 border-b border-[#e4e4e7] pb-2 mb-4">
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">
                                                    4: Sangat Baik
                                                </span>
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">
                                                    3: Baik
                                                </span>
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">
                                                    2: Cukup
                                                </span>
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">
                                                    1: Kurang
                                                </span>
                                            </div>

                                            {/* Tampilan Status Sudah Dinilai */}
                                            {selectedStudent?.dataKuisioner &&
                                            !isEditingKuisioner ? (
                                                <div className="space-y-4">
                                                    <div className="p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                                        <svg
                                                            className="w-6 h-6 text-emerald-600 flex-shrink-0"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            strokeWidth="2.5"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            ></path>
                                                        </svg>
                                                        <div className="w-full">
                                                            <h4 className="font-black text-emerald-900">
                                                                Sudah Dinilai
                                                            </h4>
                                                            <p className="text-sm font-medium text-emerald-700 mb-3">
                                                                Anda sudah memberikan penilaian
                                                                untuk form ini. Klik tombol
                                                                &quot;Edit Nilai&quot; di atas jika
                                                                ingin mengubahnya.
                                                            </p>
                                                            <div
                                                                className="bg-white border border-emerald-200 rounded-lg p-4 space-y-3"
                                                                dangerouslySetInnerHTML={{
                                                                    __html: renderSaranTeks(
                                                                        selectedStudent
                                                                            .dataKuisioner
                                                                            .penilaian,
                                                                    ),
                                                                }}
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ) : (
                                                /* Form Input / Edit */
                                                <form
                                                    onSubmit={(e) => {
                                                        e.preventDefault();
                                                        saveData('kuisioner');
                                                    }}
                                                    className="space-y-4"
                                                >
                                                    {paramKuisionerList.length > 0 ? (
                                                        <div className="space-y-4">
                                                            {paramKuisionerList.map((param) => (
                                                                <div
                                                                    key={param.id}
                                                                    className="bg-emerald-50 p-4 rounded-xl border border-emerald-200"
                                                                >
                                                                    <h4 className="font-black text-emerald-900 mb-3 bg-white p-2 rounded inline-block">
                                                                        {param.sub_kategori}
                                                                    </h4>
                                                                    <div className="space-y-4">
                                                                        {(
                                                                            param.indikator || []
                                                                        ).map((indikator, idx) => (
                                                                            <div
                                                                                key={idx}
                                                                                className="flex justify-between items-center bg-white p-2 rounded border border-emerald-100"
                                                                            >
                                                                                <span className="text-sm font-bold text-gray-700">
                                                                                    {indikator}
                                                                                </span>
                                                                                <select
                                                                                    required
                                                                                    value={
                                                                                        formKuisioner
                                                                                            .answers[
                                                                                            indikator
                                                                                        ] ?? ''
                                                                                    }
                                                                                    onChange={(
                                                                                        e,
                                                                                    ) =>
                                                                                        setFormKuisioner(
                                                                                            (
                                                                                                prev,
                                                                                            ) => ({
                                                                                                ...prev,
                                                                                                answers:
                                                                                                    {
                                                                                                        ...prev.answers,
                                                                                                        [indikator]:
                                                                                                            e
                                                                                                                .target
                                                                                                                .value ===
                                                                                                            ''
                                                                                                                ? ''
                                                                                                                : Number(
                                                                                                                      e
                                                                                                                          .target
                                                                                                                          .value,
                                                                                                                  ),
                                                                                                    },
                                                                                            }),
                                                                                        )
                                                                                    }
                                                                                    className="border-2 border-gray-300 rounded p-1 font-bold focus:border-emerald-600 outline-none"
                                                                                >
                                                                                    <option value="">
                                                                                        -
                                                                                    </option>
                                                                                    <option value="1">
                                                                                        1
                                                                                    </option>
                                                                                    <option value="2">
                                                                                        2
                                                                                    </option>
                                                                                    <option value="3">
                                                                                        3
                                                                                    </option>
                                                                                    <option value="4">
                                                                                        4
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    ) : (
                                                        <p className="text-gray-500 italic">
                                                            Belum ada indikator kuisioner dari
                                                            Kaprodi.
                                                        </p>
                                                    )}
                                                </form>
                                            )}
                                        </div>
                                    )}

                                    {/* KONTEN 4: SARAN MAHASISWA */}
                                    {activeModal === 'saran' && (
                                        <div className="bg-white p-5 md:p-8 rounded-xl border border-[#e4e4e7] shadow-surface relative overflow-hidden">
                                            <div className="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                                                <svg
                                                    className="w-32 h-32 text-yellow-600"
                                                    fill="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                                </svg>
                                            </div>

                                            <div className="relative z-10">
                                                <div className="flex justify-between items-center mb-6 border-l-4 border-yellow-400 pl-3">
                                                    <h3 className="text-2xl font-black text-yellow-700">
                                                        Saran &amp; Masukan untuk Mahasiswa
                                                    </h3>
                                                    {selectedStudent?.saran_mentor &&
                                                        !isEditingSaran && (
                                                            <button
                                                                type="button"
                                                                onClick={enableEditSaran}
                                                                className="bg-amber-500 hover:bg-amber-600 text-white text-xs font-black px-3 py-1.5 rounded-lg border border-[#e4e4e7] shadow-surface"
                                                            >
                                                                Edit Saran
                                                            </button>
                                                        )}
                                                </div>

                                                {/* Tampilan Status Sudah Dikirim */}
                                                {selectedStudent?.saran_mentor &&
                                                !isEditingSaran ? (
                                                    <div className="space-y-4">
                                                        <div className="p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                                            <svg
                                                                className="w-6 h-6 text-emerald-600 flex-shrink-0"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                strokeWidth="2.5"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                ></path>
                                                            </svg>
                                                            <div className="w-full">
                                                                <h4 className="font-black text-emerald-900">
                                                                    Saran Sudah Dikirim
                                                                </h4>
                                                                <p className="text-sm font-medium text-emerald-700 mb-3">
                                                                    Anda sudah memberikan saran
                                                                    untuk mahasiswa ini. Klik tombol
                                                                    &quot;Edit Saran&quot; di atas
                                                                    jika ingin mengubahnya.
                                                                </p>
                                                                <div
                                                                    className="bg-white border border-emerald-200 rounded-lg p-4 space-y-3"
                                                                    dangerouslySetInnerHTML={{
                                                                        __html: renderSaranTeks(
                                                                            selectedStudent.saran_mentor_teks,
                                                                        ),
                                                                    }}
                                                                ></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    /* Form Saran / Edit */
                                                    <form
                                                        onSubmit={submitSaranMentor}
                                                        className="space-y-4"
                                                    >
                                                        <div className="mb-4">
                                                            <label className="block text-xs font-black text-yellow-900 uppercase mb-1">
                                                                Tanggal
                                                            </label>
                                                            <input
                                                                type="date"
                                                                value={formSaran.tanggal}
                                                                onChange={(e) =>
                                                                    setFormSaran((prev) => ({
                                                                        ...prev,
                                                                        tanggal: e.target.value,
                                                                    }))
                                                                }
                                                                required
                                                                className="w-full md:w-1/2 border-2 border-yellow-300 rounded-lg p-2 font-bold focus:border-yellow-600 outline-none"
                                                            />
                                                        </div>

                                                        {paramSaranList.length > 0 ? (
                                                            <div className="space-y-4">
                                                                {paramSaranList.map((param) => (
                                                                    <div
                                                                        key={param.id}
                                                                        className="bg-yellow-50 p-4 rounded-xl border border-yellow-200"
                                                                    >
                                                                        <h4 className="font-black text-yellow-900 mb-3">
                                                                            {param.sub_kategori}
                                                                        </h4>
                                                                        <div className="space-y-4">
                                                                            {(
                                                                                param.indikator ||
                                                                                []
                                                                            ).map(
                                                                                (
                                                                                    indikator,
                                                                                    idx,
                                                                                ) => (
                                                                                    <div key={idx}>
                                                                                        <label className="block text-xs font-black text-yellow-800 uppercase mb-1">
                                                                                            {
                                                                                                indikator
                                                                                            }
                                                                                        </label>
                                                                                        <textarea
                                                                                            value={
                                                                                                formSaran
                                                                                                    .answers[
                                                                                                    indikator
                                                                                                ] ||
                                                                                                ''
                                                                                            }
                                                                                            onChange={(
                                                                                                e,
                                                                                            ) =>
                                                                                                setFormSaran(
                                                                                                    (
                                                                                                        prev,
                                                                                                    ) => ({
                                                                                                        ...prev,
                                                                                                        answers:
                                                                                                            {
                                                                                                                ...prev.answers,
                                                                                                                [indikator]:
                                                                                                                    e
                                                                                                                        .target
                                                                                                                        .value,
                                                                                                            },
                                                                                                    }),
                                                                                                )
                                                                                            }
                                                                                            required
                                                                                            rows="3"
                                                                                            className="w-full border-2 border-yellow-300 rounded-lg p-3 font-medium focus:border-yellow-600 outline-none"
                                                                                        ></textarea>
                                                                                    </div>
                                                                                ),
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        ) : (
                                                            <div>
                                                                <label className="block text-xs font-black text-yellow-900 uppercase mb-1">
                                                                    Isi Saran / Masukan
                                                                </label>
                                                                <textarea
                                                                    value={formSaran.saran}
                                                                    onChange={(e) =>
                                                                        setFormSaran((prev) => ({
                                                                            ...prev,
                                                                            saran: e.target.value,
                                                                        }))
                                                                    }
                                                                    required
                                                                    rows="4"
                                                                    className="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none"
                                                                    placeholder="Tuliskan evaluasi, saran membangun, atau pesan..."
                                                                ></textarea>
                                                            </div>
                                                        )}

                                                        <div className="flex justify-end mt-4">
                                                            <button
                                                                type="submit"
                                                                disabled={isSubmitting}
                                                                className="bg-[#013981] text-white font-black py-2.5 px-6 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-2 disabled:opacity-50"
                                                            >
                                                                {!isSubmitting ? (
                                                                    <span>
                                                                        Kirim / Perbarui Saran
                                                                    </span>
                                                                ) : (
                                                                    <span>Menyimpan...</span>
                                                                )}
                                                            </button>
                                                        </div>
                                                    </form>
                                                )}
                                            </div>
                                        </div>
                                    )}

                                    {/* KONTEN 5: REKAPAN JAM */}
                                    {activeModal === 'rekapan' && (
                                        <div className="bg-white p-6 rounded-xl border-2 border-cyan-900 shadow-surface">
                                            <div className="flex justify-between items-center border-b border-[#e4e4e7] pb-2 mb-4">
                                                <h4 className="text-xl font-black text-cyan-900">
                                                    Rekapan Jam Logbook
                                                </h4>
                                            </div>

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
                                                                <canvas id="mentorRekapanChart"></canvas>
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
                                                                {(
                                                                    rekapanData?.rekapanJam || []
                                                                ).map((item, index) => (
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
                                                                                item.mingguan[i] >
                                                                                    0 ? (
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
                                                                ))}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    {/* KONTEN 6: TRANSKRIP FULL */}
                                    {activeModal === 'transkrip_full' && (
                                        <div className="h-[75vh] w-full rounded-xl overflow-hidden border-2 border-cyan-900 shadow-sm relative bg-white">
                                            {iframeLoading && (
                                                <div className="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                                    <svg
                                                        className="w-10 h-10 text-cyan-600 animate-spin mb-3"
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
                                                        ></circle>
                                                        <path
                                                            className="opacity-75"
                                                            fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                        ></path>
                                                    </svg>
                                                    <p className="text-gray-500 font-bold">
                                                        Memuat Transkrip...
                                                    </p>
                                                </div>
                                            )}
                                            <iframe
                                                src={
                                                    '/external/transkrip/' +
                                                    selectedStudent?.nim +
                                                    '?mode=modal'
                                                }
                                                className="w-full h-full border-0"
                                                onLoad={() => setIframeLoading(false)}
                                                title="Transkrip Nilai Lengkap"
                                            ></iframe>
                                        </div>
                                    )}
                                </div>

                                {/* Footer Modal */}
                                <div className="bg-gray-200 border-t border-[#e4e4e7] p-4 flex justify-end flex-shrink-0 rounded-b-xl">
                                    {(activeModal === 'logbook' ||
                                        (activeModal === 'disiplin' &&
                                            (!selectedStudent?.dataDisiplin ||
                                                isEditingDisiplin)) ||
                                        (activeModal === 'kuisioner' &&
                                            (!selectedStudent?.dataKuisioner ||
                                                isEditingKuisioner))) && (
                                        <button
                                            type="button"
                                            onClick={() => saveData()}
                                            className="bg-[#013981] hover:bg-[#012b63] text-white font-medium px-8 py-3 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg"
                                        >
                                            Simpan Penilaian
                                        </button>
                                    )}
                                    {(activeModal === 'saran' ||
                                        activeModal === 'rekapan' ||
                                        activeModal === 'transkrip_full' ||
                                        (activeModal === 'disiplin' &&
                                            selectedStudent?.dataDisiplin &&
                                            !isEditingDisiplin) ||
                                        (activeModal === 'kuisioner' &&
                                            selectedStudent?.dataKuisioner &&
                                            !isEditingKuisioner)) && (
                                        <button
                                            type="button"
                                            onClick={closeModal}
                                            className="bg-gray-500 text-white font-black px-8 py-3 rounded-xl border-2 border-gray-800 shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg"
                                        >
                                            Tutup
                                        </button>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
