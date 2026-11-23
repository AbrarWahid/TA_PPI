import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalKaprodiParameter({ parameters = [] }) {
    const { flash } = usePage().props;

    const [activeTab, setActiveTab] = useState('dosen');
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [form, setForm] = useState({
        id: null,
        jenis: 'presentasi',
        sub_kategori: '',
        indikator: [''],
    });

    const openModal = (param = null) => {
        if (param) {
            setIsEditing(true);
            setForm({
                id: param.id,
                jenis: param.jenis,
                sub_kategori: param.sub_kategori,
                indikator:
                    Array.isArray(param.indikator) && param.indikator.length > 0
                        ? [...param.indikator]
                        : [''],
            });
        } else {
            setIsEditing(false);
            let defaultJenis = 'presentasi';
            if (activeTab === 'mahasiswa') defaultJenis = 'saran_mahasiswa';
            if (activeTab === 'mentor') defaultJenis = 'saran_mentor';

            setForm({
                id: null,
                jenis: defaultJenis,
                sub_kategori: '',
                indikator: [''],
            });
        }
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
    };

    const addIndikator = () => {
        setForm((prev) => ({
            ...prev,
            indikator: [...prev.indikator, ''],
        }));
    };

    const removeIndikator = (index) => {
        if (form.indikator.length > 1) {
            setForm((prev) => ({
                ...prev,
                indikator: prev.indikator.filter((_, i) => i !== index),
            }));
        }
    };

    const handleIndikatorChange = (index, value) => {
        setForm((prev) => {
            const next = [...prev.indikator];
            next[index] = value;
            return { ...prev, indikator: next };
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const payload = {
            jenis: form.jenis,
            sub_kategori: form.sub_kategori,
            indikator: form.indikator,
        };

        if (isEditing && form.id) {
            router.put(`/external/kaprodi/parameter/${form.id}`, payload, {
                onSuccess: () => closeModal(),
            });
        } else {
            router.post('/external/kaprodi/parameter', payload, {
                onSuccess: () => closeModal(),
            });
        }
    };

    const handleDelete = (id, confirmMessage) => {
        if (window.confirm(confirmMessage)) {
            router.delete(`/external/kaprodi/parameter/${id}`);
        }
    };

    const paramsSaranMhs = (parameters || []).filter((p) => p.jenis === 'saran_mahasiswa');
    const paramsPresentasi = (parameters || []).filter((p) => p.jenis === 'presentasi');
    const paramsMakalah = (parameters || []).filter((p) => p.jenis === 'makalah');
    const paramsDisiplin = (parameters || []).filter((p) => p.jenis === 'disiplin_prestasi');
    const paramsKuisioner = (parameters || []).filter((p) => p.jenis === 'kuisioner_mentor');
    const paramsSaranMentor = (parameters || []).filter((p) => p.jenis === 'saran_mentor');

    return (
        <AppLayout title="Manajemen Formulir">
            <div className="space-y-6">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#e4e4e7] pb-4">
                    <div>
                        <h2 className="text-3xl font-black text-blue-900 tracking-wide">
                            Manajemen Formulir
                        </h2>
                        <p className="text-gray-600 mt-1 font-medium">
                            Atur sub-kategori dan indikator penilaian yang digunakan oleh Mahasiswa,
                            Dosen, dan Mentor.
                        </p>
                    </div>
                    <div className="flex gap-2 mt-4 md:mt-0">
                        {activeTab === 'mahasiswa' && (
                            <button
                                type="button"
                                onClick={() => openModal()}
                                className="bg-[#013981] hover:bg-[#012b63] text-white font-bold py-2 px-4 rounded-xl shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none"
                            >
                                + Tambah Form Saran
                            </button>
                        )}
                        {activeTab === 'dosen' && (
                            <button
                                type="button"
                                onClick={() => openModal()}
                                className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none"
                            >
                                + Tambah Parameter Dosen
                            </button>
                        )}
                        {activeTab === 'mentor' && (
                            <button
                                type="button"
                                onClick={() => openModal()}
                                className="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-xl shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none"
                            >
                                + Tambah Form Saran Mentor
                            </button>
                        )}
                    </div>
                </div>

                {flash?.success && (
                    <div
                        className="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm"
                        role="alert"
                    >
                        <p className="font-bold">Sukses!</p>
                        <p>{flash.success}</p>
                    </div>
                )}

                {/* TABS NAVIGATION */}
                <div className="flex border-b border-[#e4e4e7] mb-6 flex-wrap">
                    <button
                        type="button"
                        onClick={() => setActiveTab('mahasiswa')}
                        className={`px-6 py-3 transition-colors text-lg ${
                            activeTab === 'mahasiswa'
                                ? 'border-blue-900 text-blue-900 border-b-4 font-black'
                                : 'text-gray-500 hover:text-blue-700 font-bold'
                        }`}
                    >
                        Mahasiswa
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('dosen')}
                        className={`px-6 py-3 transition-colors text-lg ${
                            activeTab === 'dosen'
                                ? 'border-blue-900 text-blue-900 border-b-4 font-black'
                                : 'text-gray-500 hover:text-blue-700 font-bold'
                        }`}
                    >
                        Dosen
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('mentor')}
                        className={`px-6 py-3 transition-colors text-lg ${
                            activeTab === 'mentor'
                                ? 'border-blue-900 text-blue-900 border-b-4 font-black'
                                : 'text-gray-500 hover:text-blue-700 font-bold'
                        }`}
                    >
                        Mentor PT
                    </button>
                </div>

                {/* TAB CONTENT: MAHASISWA */}
                {activeTab === 'mahasiswa' && (
                    <div className="mb-10 animate-fade-in">
                        <div className="flex items-center gap-3 mb-4">
                            <div className="p-3 bg-yellow-100 text-yellow-900 rounded-lg">
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
                                Formulir Mahasiswa
                            </h2>
                        </div>
                        <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                            <div className="bg-yellow-500 p-4 text-yellow-950">
                                <h3 className="text-xl font-black uppercase">
                                    Kategori: Saran &amp; Masukan PT
                                </h3>
                            </div>
                            <div className="p-4 space-y-4">
                                {paramsSaranMhs.length === 0 ? (
                                    <div className="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                                        <svg
                                            className="w-16 h-16 mx-auto text-gray-400 mb-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            ></path>
                                        </svg>
                                        <h3 className="text-xl font-black text-gray-700 mb-2">
                                            Belum Ada Form Saran
                                        </h3>
                                        <p className="text-gray-500 font-medium">
                                            Klik tombol tambah di atas untuk membuat indikator form
                                            saran bagi mahasiswa.
                                        </p>
                                    </div>
                                ) : (
                                    paramsSaranMhs.map((param) => (
                                        <div
                                            key={param.id}
                                            className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-yellow-400 transition-colors"
                                        >
                                            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                <h4 className="font-black text-xl text-yellow-900 flex items-center gap-2">
                                                    <svg
                                                        className="w-5 h-5 text-yellow-600"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                                        ></path>
                                                    </svg>
                                                    {param.sub_kategori}
                                                </h4>
                                                <div className="flex gap-2 w-full sm:w-auto">
                                                    <button
                                                        type="button"
                                                        onClick={() => openModal(param)}
                                                        className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            handleDelete(
                                                                param.id,
                                                                'Yakin ingin menghapus form saran ini?',
                                                            )
                                                        }
                                                        className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                    Indikator Pertanyaan:
                                                </p>
                                                <div className="flex flex-col gap-2">
                                                    {(param.indikator || []).map((ind, idx) => (
                                                        <div
                                                            key={idx}
                                                            className="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-yellow-200 text-gray-700 shadow-sm flex items-start gap-2"
                                                        >
                                                            <span className="text-yellow-500 mt-0.5">
                                                                <svg
                                                                    className="w-4 h-4"
                                                                    fill="currentColor"
                                                                    viewBox="0 0 20 20"
                                                                >
                                                                    <path
                                                                        fillRule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clipRule="evenodd"
                                                                    ></path>
                                                                </svg>
                                                            </span>
                                                            <span>{ind}</span>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB CONTENT: DOSEN */}
                {activeTab === 'dosen' && (
                    <div className="mb-10 animate-fade-in">
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
                                Form Penilaian Presentasi PPI oleh Dosen Pembimbing
                            </h2>
                        </div>

                        <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
                            {/* Presentasi */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="bg-blue-900 p-4 text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Bagian A: Mutu Presentasi
                                    </h3>
                                </div>
                                <div className="p-4 space-y-4">
                                    {paramsPresentasi.length === 0 ? (
                                        <p className="text-gray-500 italic font-medium p-4 text-center">
                                            Belum ada parameter presentasi.
                                        </p>
                                    ) : (
                                        paramsPresentasi.map((param) => (
                                            <div
                                                key={param.id}
                                                className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-blue-300 transition-colors"
                                            >
                                                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                    <h4 className="font-black text-xl text-blue-900 flex items-center gap-2">
                                                        <svg
                                                            className="w-5 h-5 text-blue-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            ></path>
                                                        </svg>
                                                        {param.sub_kategori}
                                                    </h4>
                                                    <div className="flex gap-2 w-full sm:w-auto">
                                                        <button
                                                            type="button"
                                                            onClick={() => openModal(param)}
                                                            className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    param.id,
                                                                    'Yakin ingin menghapus parameter ini?',
                                                                )
                                                            }
                                                            className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                        Indikator Penilaian:
                                                    </p>
                                                    <div className="flex flex-wrap gap-2">
                                                        {(param.indikator || []).map((ind, idx) => (
                                                            <span
                                                                key={idx}
                                                                className="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white border-2 border-blue-200 text-blue-800 shadow-sm hover:border-blue-400 hover:bg-blue-50 transition-colors"
                                                            >
                                                                {ind}
                                                            </span>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>

                            {/* Makalah */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="bg-emerald-900 p-4 text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Bagian B: Penulisan Makalah
                                    </h3>
                                </div>
                                <div className="p-4 space-y-4">
                                    {paramsMakalah.length === 0 ? (
                                        <p className="text-gray-500 italic font-medium p-4 text-center">
                                            Belum ada parameter makalah.
                                        </p>
                                    ) : (
                                        paramsMakalah.map((param) => (
                                            <div
                                                key={param.id}
                                                className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-emerald-300 transition-colors"
                                            >
                                                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                    <h4 className="font-black text-xl text-emerald-900 flex items-center gap-2">
                                                        <svg
                                                            className="w-5 h-5 text-emerald-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                            ></path>
                                                        </svg>
                                                        {param.sub_kategori}
                                                    </h4>
                                                    <div className="flex gap-2 w-full sm:w-auto">
                                                        <button
                                                            type="button"
                                                            onClick={() => openModal(param)}
                                                            className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    param.id,
                                                                    'Yakin ingin menghapus parameter ini?',
                                                                )
                                                            }
                                                            className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                        Indikator Penilaian:
                                                    </p>
                                                    <div className="flex flex-wrap gap-2">
                                                        {(param.indikator || []).map((ind, idx) => (
                                                            <span
                                                                key={idx}
                                                                className="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white border-2 border-emerald-200 text-emerald-800 shadow-sm hover:border-emerald-400 hover:bg-emerald-50 transition-colors"
                                                            >
                                                                {ind}
                                                            </span>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB CONTENT: MENTOR PT */}
                {activeTab === 'mentor' && (
                    <div className="mb-10 animate-fade-in">
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
                                Parameter Penilaian oleh Mentor Perusahaan
                            </h2>
                        </div>

                        <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
                            {/* Disiplin & Prestasi */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="bg-purple-900 p-4 text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Form Disiplin &amp; Prestasi
                                    </h3>
                                </div>
                                <div className="p-4 space-y-4">
                                    {paramsDisiplin.length === 0 ? (
                                        <div className="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                                            <svg
                                                className="w-16 h-16 mx-auto text-gray-400 mb-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                ></path>
                                            </svg>
                                            <h3 className="text-xl font-black text-gray-700 mb-2">
                                                Belum Ada Form Disiplin
                                            </h3>
                                            <p className="text-gray-500 font-medium">
                                                Klik tombol tambah di atas untuk membuat indikator
                                                form disiplin bagi mentor.
                                            </p>
                                        </div>
                                    ) : (
                                        paramsDisiplin.map((param) => (
                                            <div
                                                key={param.id}
                                                className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-purple-400 transition-colors"
                                            >
                                                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                    <h4 className="font-black text-xl text-purple-900 flex items-center gap-2">
                                                        <svg
                                                            className="w-5 h-5 text-purple-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                            ></path>
                                                        </svg>
                                                        {param.sub_kategori}
                                                    </h4>
                                                    <div className="flex gap-2 w-full sm:w-auto">
                                                        <button
                                                            type="button"
                                                            onClick={() => openModal(param)}
                                                            className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    param.id,
                                                                    'Yakin ingin menghapus form disiplin ini?',
                                                                )
                                                            }
                                                            className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                        Indikator Pertanyaan:
                                                    </p>
                                                    <div className="flex flex-col gap-2">
                                                        {(param.indikator || []).map((ind, idx) => (
                                                            <div
                                                                key={idx}
                                                                className="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-purple-200 text-gray-700 shadow-sm flex items-start gap-2"
                                                            >
                                                                <span className="text-purple-500 mt-0.5">
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 20 20"
                                                                    >
                                                                        <path
                                                                            fillRule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                            clipRule="evenodd"
                                                                        ></path>
                                                                    </svg>
                                                                </span>
                                                                <span>{ind}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>

                            {/* Kuisioner Evaluasi */}
                            <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden">
                                <div className="bg-emerald-900 p-4 text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Evaluasi Kuisioner Soft &amp; Hard Skill
                                    </h3>
                                </div>
                                <div className="p-4 space-y-4">
                                    {paramsKuisioner.length === 0 ? (
                                        <div className="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                                            <svg
                                                className="w-16 h-16 mx-auto text-gray-400 mb-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                ></path>
                                            </svg>
                                            <h3 className="text-xl font-black text-gray-700 mb-2">
                                                Belum Ada Form Kuisioner
                                            </h3>
                                            <p className="text-gray-500 font-medium">
                                                Klik tombol tambah di atas untuk membuat indikator
                                                form kuisioner bagi mentor.
                                            </p>
                                        </div>
                                    ) : (
                                        paramsKuisioner.map((param) => (
                                            <div
                                                key={param.id}
                                                className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-emerald-400 transition-colors"
                                            >
                                                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                    <h4 className="font-black text-xl text-emerald-900 flex items-center gap-2">
                                                        <svg
                                                            className="w-5 h-5 text-emerald-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            ></path>
                                                        </svg>
                                                        {param.sub_kategori}
                                                    </h4>
                                                    <div className="flex gap-2 w-full sm:w-auto">
                                                        <button
                                                            type="button"
                                                            onClick={() => openModal(param)}
                                                            className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    param.id,
                                                                    'Yakin ingin menghapus form kuisioner ini?',
                                                                )
                                                            }
                                                            className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                        Indikator Pertanyaan:
                                                    </p>
                                                    <div className="flex flex-col gap-2">
                                                        {(param.indikator || []).map((ind, idx) => (
                                                            <div
                                                                key={idx}
                                                                className="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-emerald-200 text-gray-700 shadow-sm flex items-start gap-2"
                                                            >
                                                                <span className="text-emerald-500 mt-0.5">
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 20 20"
                                                                    >
                                                                        <path
                                                                            fillRule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                            clipRule="evenodd"
                                                                        ></path>
                                                                    </svg>
                                                                </span>
                                                                <span>{ind}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>

                            {/* Saran & Masukan dari Mentor */}
                            <div className="bg-white rounded-xl border-2 border-fuchsia-900 shadow-surface overflow-hidden col-span-1 xl:col-span-2 mt-8">
                                <div className="bg-fuchsia-900 p-4 text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Kategori: Saran &amp; Masukan Mentor
                                    </h3>
                                </div>
                                <div className="p-4 space-y-4">
                                    {paramsSaranMentor.length === 0 ? (
                                        <div className="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                                            <svg
                                                className="w-16 h-16 mx-auto text-gray-400 mb-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                ></path>
                                            </svg>
                                            <h3 className="text-xl font-black text-gray-700 mb-2">
                                                Belum Ada Form Saran
                                            </h3>
                                            <p className="text-gray-500 font-medium">
                                                Klik tombol tambah di atas untuk membuat indikator
                                                form saran bagi mentor.
                                            </p>
                                        </div>
                                    ) : (
                                        paramsSaranMentor.map((param) => (
                                            <div
                                                key={param.id}
                                                className="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-fuchsia-400 transition-colors"
                                            >
                                                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[#e4e4e7] pb-3 gap-3">
                                                    <h4 className="font-black text-xl text-fuchsia-900 flex items-center gap-2">
                                                        <svg
                                                            className="w-5 h-5 text-fuchsia-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                                            ></path>
                                                        </svg>
                                                        {param.sub_kategori}
                                                    </h4>
                                                    <div className="flex gap-2 w-full sm:w-auto">
                                                        <button
                                                            type="button"
                                                            onClick={() => openModal(param)}
                                                            className="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleDelete(
                                                                    param.id,
                                                                    'Yakin ingin menghapus form saran mentor ini?',
                                                                )
                                                            }
                                                            className="flex-1 sm:flex-none text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-surface transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                        Indikator Pertanyaan:
                                                    </p>
                                                    <div className="flex flex-col gap-2">
                                                        {(param.indikator || []).map((ind, idx) => (
                                                            <div
                                                                key={idx}
                                                                className="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-fuchsia-200 text-gray-700 shadow-sm flex items-start gap-2"
                                                            >
                                                                <span className="text-fuchsia-500 mt-0.5">
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 20 20"
                                                                    >
                                                                        <path
                                                                            fillRule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                            clipRule="evenodd"
                                                                        ></path>
                                                                    </svg>
                                                                </span>
                                                                <span>{ind}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Modal Form (For Dosen Parameters) */}
                {showModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={closeModal}
                            ></div>
                            <span
                                className="hidden sm:inline-block sm:align-middle sm:h-screen"
                                aria-hidden="true"
                            >
                                &#8203;
                            </span>
                            <div className="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-[#e4e4e7]">
                                <form onSubmit={handleSubmit}>
                                    <div className="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                                        <h3 className="text-xl font-black">
                                            {isEditing
                                                ? 'Edit Parameter'
                                                : 'Tambah Parameter Baru'}
                                        </h3>
                                        <button
                                            type="button"
                                            onClick={closeModal}
                                            className="text-white hover:text-red-400"
                                        >
                                            <svg
                                                className="h-6 w-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <div className="px-6 py-4 space-y-4">
                                        <div>
                                            <label className="block text-sm font-bold text-gray-700">
                                                Kategori Utama
                                            </label>
                                            <select
                                                name="jenis"
                                                value={form.jenis}
                                                onChange={(e) =>
                                                    setForm({ ...form, jenis: e.target.value })
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-medium"
                                            >
                                                {(activeTab === 'dosen' || isEditing) && (
                                                    <option value="presentasi">
                                                        Penilaian Presentasi PPI (A. Mutu
                                                        Presentasi)
                                                    </option>
                                                )}
                                                {(activeTab === 'dosen' || isEditing) && (
                                                    <option value="makalah">
                                                        Penilaian Presentasi PPI (B. Penulisan
                                                        Makalah)
                                                    </option>
                                                )}
                                                {(activeTab === 'mahasiswa' || isEditing) && (
                                                    <option value="saran_mahasiswa">
                                                        Saran &amp; Masukan PT
                                                    </option>
                                                )}
                                                {(activeTab === 'mentor' || isEditing) && (
                                                    <option value="disiplin_prestasi">
                                                        Disiplin &amp; Prestasi
                                                    </option>
                                                )}
                                                {(activeTab === 'mentor' || isEditing) && (
                                                    <option value="kuisioner_mentor">
                                                        Evaluasi Kuisioner
                                                    </option>
                                                )}
                                                {(activeTab === 'mentor' || isEditing) && (
                                                    <option value="saran_mentor">
                                                        Saran &amp; Masukan Mentor
                                                    </option>
                                                )}
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-bold text-gray-700">
                                                Nama Sub Kategori
                                            </label>
                                            <input
                                                type="text"
                                                name="sub_kategori"
                                                value={form.sub_kategori}
                                                onChange={(e) =>
                                                    setForm({
                                                        ...form,
                                                        sub_kategori: e.target.value,
                                                    })
                                                }
                                                required
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-medium"
                                                placeholder="Contoh: Teknik Presentasi"
                                            />
                                        </div>
                                        <div>
                                            <div className="flex justify-between items-center mb-2">
                                                <label className="block text-sm font-bold text-gray-700">
                                                    Indikator Penilaian
                                                </label>
                                                <button
                                                    type="button"
                                                    onClick={addIndikator}
                                                    className="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold hover:bg-blue-200"
                                                >
                                                    + Tambah Indikator
                                                </button>
                                            </div>
                                            {form.indikator.map((ind, index) => (
                                                <div
                                                    key={index}
                                                    className="flex items-center gap-2 mb-2"
                                                >
                                                    <input
                                                        type="text"
                                                        name={`indikator[${index}]`}
                                                        value={ind}
                                                        onChange={(e) =>
                                                            handleIndikatorChange(
                                                                index,
                                                                e.target.value,
                                                            )
                                                        }
                                                        required
                                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-medium"
                                                        placeholder="Nama indikator..."
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={() => removeIndikator(index)}
                                                        className="text-red-500 hover:text-red-700 p-1"
                                                        title="Hapus indikator"
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
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            ></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            ))}
                                            {form.indikator.length === 0 && (
                                                <p className="text-sm text-red-500 italic">
                                                    Tambahkan minimal 1 indikator.
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                    <div className="px-6 py-4 bg-gray-50 flex justify-end gap-2 border-t">
                                        <button
                                            type="button"
                                            onClick={closeModal}
                                            className="px-4 py-2 border-2 border-gray-300 rounded-md font-bold text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            className="px-4 py-2 bg-blue-600 border border-[#e4e4e7] text-white rounded-md font-bold shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
