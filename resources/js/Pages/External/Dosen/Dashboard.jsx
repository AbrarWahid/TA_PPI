import React, { useState, useEffect, useMemo } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalDosenDashboard({
    dosen,
    mahasiswas = [],
    jadwals = [],
    parameters = [],
    notifikasiTTD = [],
    pendingApprovals = [],
}) {
    const paramsPresentasi = useMemo(
        () => (parameters || []).filter((p) => p.jenis === 'presentasi'),
        [parameters],
    );
    const paramsMakalah = useMemo(
        () => (parameters || []).filter((p) => p.jenis === 'makalah'),
        [parameters],
    );

    const buildEmptyForm = (presList, makList) => {
        const empty = {
            judul: '',
            presentasi: {},
            makalah: {},
            catatanA: '',
            catatanB: '',
        };
        presList.forEach((p) => {
            empty.presentasi[p.id] = (p.indikator || []).map(() => 0);
        });
        makList.forEach((p) => {
            empty.makalah[p.id] = (p.indikator || []).map(() => 0);
        });
        return empty;
    };

    const buildFilledForm = (nPres, nMak, presList, makList) => {
        const filled = buildEmptyForm(presList, makList);
        filled.judul = 'Laporan Presentasi PPI';
        presList.forEach((p) => {
            filled.presentasi[p.id] = (p.indikator || []).map(() => nPres);
        });
        makList.forEach((p) => {
            filled.makalah[p.id] = (p.indikator || []).map(() => nMak);
        });
        return filled;
    };

    const mapServerStudents = (serverStudents, serverJadwals, presList, makList) => {
        if (Array.isArray(serverStudents) && serverStudents.length > 0) {
            return serverStudents.map((mhs) => {
                const penDosen = (mhs.penilaians || []).find((p) => p.nidn !== null);
                const status = penDosen ? 'Sudah Dinilai' : 'Belum Dinilai';
                const nPres = penDosen ? penDosen.n_presentasi || 0 : 0;
                const nMak = penDosen ? penDosen.n_makalah || 0 : 0;

                const ptName =
                    mhs.pembimbing_industri && mhs.pembimbing_industri.perusahaan
                        ? mhs.pembimbing_industri.perusahaan
                        : (mhs.pembimbing_industri && mhs.pembimbing_industri.perusahaan) ||
                          (mhs.pembimbingIndustri && mhs.pembimbingIndustri.perusahaan) ||
                          'PT Magang PPI';

                const jadwalForPT = (serverJadwals || []).find((j) => j.perusahaan === ptName);
                const monitoringDate = jadwalForPT ? jadwalForPT.tanggal : null;

                return {
                    id: mhs.id_mhs || mhs.nim,
                    name: mhs.nama_mhs || mhs.name || 'Mahasiswa',
                    nim: mhs.nim || '-',
                    kelas: mhs.kelas || '-',
                    company: ptName,
                    monitoringDate,
                    status,
                    scores: penDosen ? buildFilledForm(nPres, nMak, presList, makList) : null,
                };
            });
        }
        return [
            {
                id: 1,
                name: 'Alief Muhammad S',
                nim: '223443026',
                kelas: '3 AEC-2',
                company: 'PT Solusi Intek Indonesia',
                monitoringDate: '2026-08-25',
                status: 'Belum Dinilai',
                scores: null,
            },
            {
                id: 2,
                name: 'Budi Santoso',
                nim: '223443027',
                kelas: '3 AEC-2',
                company: 'PT Dirgantara Indonesia',
                monitoringDate: '2026-08-28',
                status: 'Belum Dinilai',
                scores: null,
            },
        ];
    };

    const [students, setStudents] = useState(() =>
        mapServerStudents(mahasiswas, jadwals, paramsPresentasi, paramsMakalah),
    );
    const [showModal, setShowModal] = useState(false);
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [form, setForm] = useState(() => buildEmptyForm(paramsPresentasi, paramsMakalah));

    useEffect(() => {
        setStudents(mapServerStudents(mahasiswas, jadwals, paramsPresentasi, paramsMakalah));
    }, [mahasiswas, jadwals, paramsPresentasi, paramsMakalah]);

    const approvals =
        Array.isArray(pendingApprovals) && pendingApprovals.length > 0
            ? pendingApprovals
            : Array.isArray(notifikasiTTD)
              ? notifikasiTTD
              : [];

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    const openModal = (student) => {
        const nextForm = student.scores
            ? JSON.parse(JSON.stringify(student.scores))
            : buildEmptyForm(paramsPresentasi, paramsMakalah);

        if (!nextForm.presentasi) nextForm.presentasi = {};
        if (!nextForm.makalah) nextForm.makalah = {};

        paramsPresentasi.forEach((p) => {
            if (!nextForm.presentasi[p.id]) {
                nextForm.presentasi[p.id] = (p.indikator || []).map(() => 0);
            }
        });
        paramsMakalah.forEach((p) => {
            if (!nextForm.makalah[p.id]) {
                nextForm.makalah[p.id] = (p.indikator || []).map(() => 0);
            }
        });

        setSelectedStudent(student);
        setForm(nextForm);
        setShowModal(true);
    };

    const closeModal = () => {
        if (!selectedStudent) return;

        const savedScores = JSON.parse(JSON.stringify(form));
        let hasValue = false;
        if (form.judul && form.judul.trim() !== '') hasValue = true;

        for (const pId in form.presentasi) {
            if ((form.presentasi[pId] || []).some((val) => Number(val) > 0)) hasValue = true;
        }
        for (const pId in form.makalah) {
            if ((form.makalah[pId] || []).some((val) => Number(val) > 0)) hasValue = true;
        }

        setStudents((prev) =>
            prev.map((s) => {
                if (s.id !== selectedStudent.id) return s;
                const nextStatus =
                    s.status !== 'Sudah Dinilai'
                        ? hasValue
                            ? 'Draft'
                            : 'Belum Dinilai'
                        : s.status;
                return {
                    ...s,
                    scores: savedScores,
                    status: nextStatus,
                };
            }),
        );

        setShowModal(false);
        setSelectedStudent(null);
    };

    const handleScoreChange = (section, paramId, index, value) => {
        const num = value === '' ? 0 : Number(value);
        setForm((prev) => {
            const sectionObj = { ...(prev[section] || {}) };
            const arr = [...(sectionObj[paramId] || [])];
            arr[index] = num;
            sectionObj[paramId] = arr;
            return { ...prev, [section]: sectionObj };
        });
    };

    const avgA = useMemo(() => {
        if (!form.presentasi) return (0.0).toFixed(1);
        let sum = 0;
        let count = 0;
        for (const pId in form.presentasi) {
            (form.presentasi[pId] || []).forEach((val) => {
                sum += parseFloat(val) || 0;
                count++;
            });
        }
        return count > 0 ? (sum / count).toFixed(1) : (0.0).toFixed(1);
    }, [form.presentasi]);

    const avgB = useMemo(() => {
        if (!form.makalah) return (0.0).toFixed(1);
        let sum = 0;
        let count = 0;
        for (const pId in form.makalah) {
            (form.makalah[pId] || []).forEach((val) => {
                sum += parseFloat(val) || 0;
                count++;
            });
        }
        return count > 0 ? (sum / count).toFixed(1) : (0.0).toFixed(1);
    }, [form.makalah]);

    const nilaiAkhir = useMemo(() => {
        return (parseFloat(avgA) * 0.4 + parseFloat(avgB) * 0.6).toFixed(1);
    }, [avgA, avgB]);

    const getGradeText = () => {
        const n = parseFloat(nilaiAkhir);
        if (n >= 85) return 'Sangat Memuaskan';
        if (n >= 78) return 'Memuaskan';
        if (n >= 70) return 'Baik';
        if (n >= 63) return 'Cukup Baik';
        if (n >= 55) return 'Kurang';
        return 'Sangat Kurang';
    };

    const getGradeColor = () => {
        const n = parseFloat(nilaiAkhir);
        if (n >= 78) return 'text-green-600';
        if (n >= 63) return 'text-yellow-600';
        return 'text-red-600';
    };

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const submitPenilaian = async () => {
        if (!selectedStudent) return;

        const payload = {
            nim: selectedStudent.nim,
            n_presentasi: parseFloat(avgA),
            n_makalah: parseFloat(avgB),
            detail_presentasi: form.presentasi,
            detail_makalah: form.makalah,
        };

        try {
            const response = await fetch('/external/dosen/penilaian', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (response.ok) {
                const savedScores = JSON.parse(JSON.stringify(form));
                setStudents((prev) =>
                    prev.map((s) =>
                        s.id === selectedStudent.id
                            ? { ...s, scores: savedScores, status: 'Sudah Dinilai' }
                            : s,
                    ),
                );
                alert(
                    `Data disimpan ke Database! Nilai Akhir untuk ${selectedStudent.name} adalah ${nilaiAkhir}.`,
                );
            } else {
                alert('Gagal menyimpan penilaian: ' + (result.message || 'Error'));
            }
        } catch (e) {
            const savedScores = JSON.parse(JSON.stringify(form));
            setStudents((prev) =>
                prev.map((s) =>
                    s.id === selectedStudent.id
                        ? { ...s, scores: savedScores, status: 'Sudah Dinilai' }
                        : s,
                ),
            );
            alert(
                `Data disimulasikan disimpan! Nilai Akhir untuk ${selectedStudent.name} adalah ${nilaiAkhir}.`,
            );
        }

        setShowModal(false);
        setSelectedStudent(null);
    };

    return (
        <AppLayout title="Dashboard Dosen Pembimbing">
            <div className="space-y-6">
                {/* Header Panel */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#e4e4e7] pb-4">
                    <div>
                        <h2 className="text-3xl font-black text-blue-900 tracking-wide">
                            Monitoring &amp; Penilaian
                        </h2>
                        <p className="text-gray-600 mt-1 font-medium">
                            Jadwal kunjungan PT dan penilaian presentasi mahasiswa bimbingan.
                        </p>
                    </div>
                </div>

                {/* Statistik Ringkas (Ditambah Draft) */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div className="bg-blue-100 p-4 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-sm font-bold text-blue-900 uppercase">
                            Total Bimbingan
                        </h3>
                        <p className="text-3xl font-black text-blue-700 mt-1">{students.length}</p>
                    </div>
                    <div className="bg-red-50 p-4 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-sm font-bold text-red-900 uppercase">Belum Dinilai</h3>
                        <div className="flex items-center gap-2 mt-1">
                            <span className="w-3 h-3 rounded-full bg-red-500 border border-[#e4e4e7] animate-pulse"></span>
                            <p className="text-3xl font-black text-red-700">
                                {students.filter((s) => s.status === 'Belum Dinilai').length}
                            </p>
                        </div>
                    </div>
                    <div className="bg-orange-50 p-4 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-sm font-bold text-orange-900 uppercase">
                            Draft (Cicilan)
                        </h3>
                        <div className="flex items-center gap-2 mt-1">
                            <span className="w-3 h-3 rounded-full bg-orange-500 border border-[#e4e4e7]"></span>
                            <p className="text-3xl font-black text-orange-700">
                                {students.filter((s) => s.status === 'Draft').length}
                            </p>
                        </div>
                    </div>
                    <div className="bg-green-50 p-4 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <h3 className="text-sm font-bold text-green-900 uppercase">
                            Sudah Dinilai
                        </h3>
                        <div className="flex items-center gap-2 mt-1">
                            <span className="w-3 h-3 rounded-full bg-green-500 border border-[#e4e4e7]"></span>
                            <p className="text-3xl font-black text-green-700">
                                {students.filter((s) => s.status === 'Sudah Dinilai').length}
                            </p>
                        </div>
                    </div>
                </div>

                {/* ========================================== */}
                {/* KOTAK NOTIFIKASI PENGAJUAN TTD DIGITAL     */}
                {/* ========================================== */}
                {approvals.length > 0 && (
                    <div className="bg-white rounded-xl border-2 border-red-200 shadow-sm overflow-hidden mt-8 p-6">
                        <h3 className="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            {/* Ikon Lonceng/Peringatan */}
                            <svg
                                className="w-6 h-6 text-red-500 animate-pulse"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2.5"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                ></path>
                            </svg>
                            Menunggu Persetujuan Tanda Tangan ({approvals.length})
                        </h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {approvals.map((approval) => (
                                <div
                                    key={approval.id || approval.token_verifikasi}
                                    className="bg-red-50 border-2 border-red-100 p-5 rounded-2xl shadow-sm relative overflow-hidden"
                                >
                                    <div className="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">
                                        NEW
                                    </div>

                                    <p className="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">
                                        NIM Mahasiswa
                                    </p>
                                    <p className="font-bold text-gray-900">
                                        {approval.mahasiswa_nim}
                                    </p>

                                    <p className="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-3 mb-1">
                                        Jenis Dokumen
                                    </p>
                                    <p className="font-bold text-blue-600 uppercase">
                                        {approval.jenis_form}
                                    </p>

                                    {/* Tombol ini akan mengarahkan dosen ke halaman Accept/Reject */}
                                    <Link
                                        href={`/external/approval/${approval.token_verifikasi}`}
                                        className="mt-4 block w-full text-center bg-gray-900 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-gray-800 transition shadow-sm"
                                    >
                                        Review &amp; Proses
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Daftar Mahasiswa Bimbingan */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden mt-8">
                    <div className="bg-blue-50 p-4 border-b border-[#e4e4e7]">
                        <h3 className="text-xl font-black text-blue-900">
                            Jadwal Monitoring &amp; Daftar Mahasiswa
                        </h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-gray-100 border-b border-[#e4e4e7]">
                                    <th className="p-4 font-bold text-gray-700">Nama Mahasiswa</th>
                                    <th className="p-4 font-bold text-gray-700">
                                        Tempat Magang &amp; Jadwal
                                    </th>
                                    <th className="p-4 font-bold text-gray-700 text-center">
                                        Status
                                    </th>
                                    <th className="p-4 font-bold text-gray-700 text-center">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {students.map((student) => (
                                    <tr
                                        key={student.id}
                                        className="border-b border-gray-200 hover:bg-blue-50 transition-colors"
                                    >
                                        {/* Info Mahasiswa */}
                                        <td className="p-4">
                                            <p className="font-black text-blue-900 text-lg">
                                                {student.name}
                                            </p>
                                            <p className="text-sm font-bold text-gray-500">
                                                {student.nim + ' | ' + student.kelas}
                                            </p>
                                        </td>

                                        {/* Jadwal & PT */}
                                        <td className="p-4">
                                            <p className="font-bold text-gray-800">
                                                {student.company}
                                            </p>
                                            <div className="flex items-center gap-2 mt-1">
                                                <svg
                                                    className="w-4 h-4 text-blue-600"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    ></path>
                                                </svg>
                                                <span className="text-xs font-bold text-blue-600">
                                                    {student.monitoringDate
                                                        ? formatDate(student.monitoringDate)
                                                        : 'Belum Dijadwalkan Admin'}
                                                </span>
                                            </div>
                                        </td>

                                        {/* Indikator Status Dinamis (Merah/Oren/Hijau) */}
                                        <td className="p-4 text-center">
                                            <div className="flex items-center justify-center gap-2 bg-gray-50 border border-gray-200 py-2 px-3 rounded-lg w-fit mx-auto">
                                                <span
                                                    className={`w-4 h-4 rounded-full border-2 border-white shadow-sm ${
                                                        student.status === 'Belum Dinilai'
                                                            ? 'bg-red-500'
                                                            : student.status === 'Draft'
                                                              ? 'bg-orange-500'
                                                              : 'bg-green-500'
                                                    }`}
                                                ></span>
                                                <span className="text-sm font-bold text-gray-700">
                                                    {student.status}
                                                </span>
                                            </div>
                                        </td>

                                        {/* Tombol Aksi Dinamis */}
                                        <td className="p-4 text-center">
                                            {student.status === 'Belum Dinilai' && (
                                                <button
                                                    type="button"
                                                    onClick={() => openModal(student)}
                                                    className="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm"
                                                >
                                                    Beri Penilaian
                                                </button>
                                            )}
                                            {student.status === 'Draft' && (
                                                <button
                                                    type="button"
                                                    onClick={() => openModal(student)}
                                                    className="bg-orange-500 text-white px-4 py-2 rounded-lg font-bold border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm"
                                                >
                                                    Lanjut Isi Draft
                                                </button>
                                            )}
                                            {student.status === 'Sudah Dinilai' && (
                                                <button
                                                    type="button"
                                                    onClick={() => openModal(student)}
                                                    className="bg-white text-green-700 px-4 py-2 rounded-lg font-bold border-2 border-green-600 shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm"
                                                >
                                                    Lihat / Edit Nilai
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* MODAL FORM PENILAIAN */}
                {showModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            {/* Background Overlay dipasang klik closeModal agar auto-draft bekerja saat di-klik sembarang */}
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={closeModal}
                            ></div>

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border border-[#e4e4e7] rounded-xl">
                                <div className="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                                    <h3 className="text-2xl font-black">
                                        Penilaian Presentasi PPI
                                    </h3>
                                    {/* Tombol X dipasang closeModal */}
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

                                <div className="p-6 bg-gray-50 max-h-[80vh] overflow-y-auto">
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
                                        <div className="w-full flex gap-4">
                                            <div className="flex-1">
                                                <p className="text-xs font-bold text-gray-500 uppercase mb-1">
                                                    Judul Presentasi/Makalah
                                                </p>
                                                <input
                                                    type="text"
                                                    value={form.judul || ''}
                                                    onChange={(e) =>
                                                        setForm({ ...form, judul: e.target.value })
                                                    }
                                                    className="w-full border-2 border-gray-300 rounded p-2 font-bold text-gray-800 outline-none focus:border-blue-500"
                                                    placeholder="Masukkan judul laporan akhir..."
                                                />
                                            </div>
                                            <div className="w-1/4">
                                                <p className="text-xs font-bold text-gray-500 uppercase mb-1">
                                                    Status Data
                                                </p>
                                                <p
                                                    className={`text-lg font-black ${
                                                        selectedStudent?.status === 'Belum Dinilai'
                                                            ? 'text-red-600'
                                                            : selectedStudent?.status === 'Draft'
                                                              ? 'text-orange-500'
                                                              : 'text-green-600'
                                                    }`}
                                                >
                                                    {selectedStudent?.status}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        {/* BAGIAN A: MUTU PRESENTASI */}
                                        <div className="bg-white p-5 rounded-lg border border-[#e4e4e7] shadow-surface">
                                            <div className="flex justify-between items-center mb-4 border-b border-[#e4e4e7] pb-2">
                                                <h4 className="font-black text-blue-900 text-lg">
                                                    A. Mutu Presentasi (40%)
                                                </h4>
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300">
                                                    {'Rata-rata: ' + avgA}
                                                </span>
                                            </div>

                                            <div className="space-y-4">
                                                {paramsPresentasi.map((param, pIndex) => (
                                                    <div key={param.id ?? pIndex}>
                                                        <h5 className="font-bold text-gray-700 bg-gray-100 p-2 rounded">
                                                            {param.sub_kategori}
                                                        </h5>
                                                        <div className="grid grid-cols-2 gap-2 mt-2">
                                                            {(param.indikator || []).map(
                                                                (ind, iIndex) => (
                                                                    <div key={iIndex}>
                                                                        <label className="text-xs font-bold text-gray-600">
                                                                            {ind}
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            min="0"
                                                                            max="100"
                                                                            value={
                                                                                form.presentasi?.[
                                                                                    param.id
                                                                                ]?.[iIndex] ?? 0
                                                                            }
                                                                            onChange={(e) =>
                                                                                handleScoreChange(
                                                                                    'presentasi',
                                                                                    param.id,
                                                                                    iIndex,
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"
                                                                        />
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                                <div>
                                                    <label className="text-xs font-bold text-gray-600 block mb-1">
                                                        Catatan Evaluasi (Opsional)
                                                    </label>
                                                    <textarea
                                                        value={form.catatanA || ''}
                                                        onChange={(e) =>
                                                            setForm({
                                                                ...form,
                                                                catatanA: e.target.value,
                                                            })
                                                        }
                                                        rows="2"
                                                        className="w-full border-2 border-gray-300 rounded p-2 text-sm font-medium"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {/* BAGIAN B: MUTU PENULISAN */}
                                        <div className="bg-white p-5 rounded-lg border border-[#e4e4e7] shadow-surface">
                                            <div className="flex justify-between items-center mb-4 border-b border-[#e4e4e7] pb-2">
                                                <h4 className="font-black text-blue-900 text-lg">
                                                    B. Penulisan Makalah (60%)
                                                </h4>
                                                <span className="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300">
                                                    {'Rata-rata: ' + avgB}
                                                </span>
                                            </div>

                                            <div className="space-y-4">
                                                {paramsMakalah.map((param, pIndex) => (
                                                    <div key={param.id ?? pIndex}>
                                                        <h5 className="font-bold text-gray-700 bg-gray-100 p-2 rounded">
                                                            {param.sub_kategori}
                                                        </h5>
                                                        <div className="grid grid-cols-2 gap-2 mt-2">
                                                            {(param.indikator || []).map(
                                                                (ind, iIndex) => (
                                                                    <div key={iIndex}>
                                                                        <label className="text-xs font-bold text-gray-600">
                                                                            {ind}
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            min="0"
                                                                            max="100"
                                                                            value={
                                                                                form.makalah?.[
                                                                                    param.id
                                                                                ]?.[iIndex] ?? 0
                                                                            }
                                                                            onChange={(e) =>
                                                                                handleScoreChange(
                                                                                    'makalah',
                                                                                    param.id,
                                                                                    iIndex,
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"
                                                                        />
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                                <div>
                                                    <label className="text-xs font-bold text-gray-600 block mb-1">
                                                        Catatan Evaluasi (Opsional)
                                                    </label>
                                                    <textarea
                                                        value={form.catatanB || ''}
                                                        onChange={(e) =>
                                                            setForm({
                                                                ...form,
                                                                catatanB: e.target.value,
                                                            })
                                                        }
                                                        rows="2"
                                                        className="w-full border-2 border-gray-300 rounded p-2 text-sm font-medium"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Footer Modal */}
                                <div className="bg-gray-200 border-t border-[#e4e4e7] p-6 flex flex-col md:flex-row justify-between items-center gap-6">
                                    <div className="w-full md:w-auto">
                                        <p className="text-xs font-bold text-gray-600 mb-1 uppercase">
                                            Kriteria Penilaian:
                                        </p>
                                        <div className="grid grid-cols-3 gap-x-4 gap-y-1 text-xs font-bold text-gray-500 bg-white p-2 border border-gray-300 rounded">
                                            <span>85-100: Sangat Memuaskan</span>
                                            <span>70-77: Baik</span>
                                            <span>55-62: Kurang</span>
                                            <span>78-84: Memuaskan</span>
                                            <span>63-69: Cukup Baik</span>
                                            <span>0-54: Sangat Kurang</span>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-6 w-full md:w-auto justify-end">
                                        <div className="text-right">
                                            <p className="text-sm font-bold text-gray-600 uppercase">
                                                Nilai Akhir
                                            </p>
                                            <div className="flex items-end gap-2">
                                                <span className="text-4xl font-black text-blue-900">
                                                    {nilaiAkhir}
                                                </span>
                                                <span
                                                    className={`text-lg font-bold ${getGradeColor()}`}
                                                >
                                                    {'(' + getGradeText() + ')'}
                                                </span>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={submitPenilaian}
                                            className="bg-[#013981] hover:bg-[#012b63] text-white font-medium px-8 py-3 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg"
                                        >
                                            <span>
                                                {selectedStudent?.status === 'Sudah Dinilai'
                                                    ? 'Update Nilai'
                                                    : 'Simpan Final'}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
