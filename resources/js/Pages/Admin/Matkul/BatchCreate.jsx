import React, { useState, useRef } from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const FORMAT_COLUMNS = [
    ['kode_matkul', 'Kode unik matkul', true],
    ['nama_matkul', 'Nama lengkap mata kuliah', true],
    ['program_studi', 'mekatronika / otomasi / informatika', true],
    ['sks', 'Angka 1–10', true],
    ['semester', 'Angka 1–8', true],
];

export default function MatkulBatchCreate({ warnings: propWarnings = [] }) {
    const { flash } = usePage().props;
    const warnings = flash?.warnings || propWarnings || [];

    const fileInputRef = useRef(null);
    const [fileName, setFileName] = useState('');
    const [isDragging, setIsDragging] = useState(false);

    const { setData, post, errors } = useForm({
        file: null,
    });

    const handleFileChange = (e) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('file', file);
            setFileName(file.name);
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        setIsDragging(false);
        const file = e.dataTransfer.files?.[0];
        if (file) {
            setData('file', file);
            setFileName(file.name);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/matkul/batch', { forceFormData: true });
    };

    return (
        <AppLayout title="Import Massal Mata Kuliah">
            <div className="flex-1 flex flex-col">
                {/* Header */}
                <div className="flex justify-between items-end mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Import Massal <span className="text-[#2dce89]">Mata Kuliah</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            Upload file Excel/CSV untuk menambahkan banyak mata kuliah sekaligus
                        </p>
                    </div>
                    <Link
                        href="/admin/matkul"
                        className="flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black hover:bg-gray-200 transition-all"
                    >
                        <span className="material-symbols-outlined text-base">arrow_back</span>{' '}
                        KEMBALI
                    </Link>
                </div>

                {/* Flash Warnings */}
                {warnings.length > 0 && (
                    <div className="mb-6 bg-amber-50 border-2 border-amber-200 rounded-2xl p-5">
                        <div className="flex items-center gap-2 mb-3">
                            <span className="material-symbols-outlined text-amber-500">
                                warning
                            </span>
                            <p className="text-xs font-black text-amber-700 uppercase tracking-widest">
                                {warnings.length} Baris Dilewati
                            </p>
                        </div>
                        <ul className="space-y-1">
                            {warnings.map((w, i) => (
                                <li
                                    key={i}
                                    className="text-xs text-amber-700 font-medium flex items-start gap-2"
                                >
                                    <span className="mt-0.5 text-amber-400">•</span> {w}
                                </li>
                            ))}
                        </ul>
                    </div>
                )}

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Form Upload */}
                    <div className="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h2 className="text-sm font-black text-[#004d4d] uppercase tracking-widest mb-6 italic">
                            Upload File
                        </h2>

                        <form onSubmit={handleSubmit} encType="multipart/form-data">
                            {/* Drag & Drop Zone */}
                            <div
                                id="dropZone"
                                onClick={() => fileInputRef.current?.click()}
                                onDragOver={(e) => {
                                    e.preventDefault();
                                    setIsDragging(true);
                                }}
                                onDragLeave={() => setIsDragging(false)}
                                onDrop={handleDrop}
                                className={`border-2 border-dashed border-[#7fffd4] rounded-2xl p-12 text-center cursor-pointer hover:bg-teal-50/30 transition-all mb-6 ${
                                    isDragging ? 'bg-teal-50' : ''
                                }`}
                            >
                                <span className="material-symbols-outlined text-5xl text-[#7fffd4] mb-3 block">
                                    upload_file
                                </span>
                                <p className="text-sm font-black text-[#004d4d] italic mb-1">
                                    Drag &amp; drop file di sini
                                </p>
                                <p className="text-xs text-gray-400 font-medium">
                                    atau klik untuk memilih file
                                </p>
                                <p className="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-3">
                                    .xlsx / .xls / .csv — Maks. 5MB
                                </p>
                            </div>

                            <input
                                ref={fileInputRef}
                                type="file"
                                id="fileInput"
                                name="file"
                                accept=".xlsx,.xls,.csv"
                                className="hidden"
                                onChange={handleFileChange}
                            />

                            {/* Nama File Terpilih */}
                            <div
                                id="fileNameBox"
                                className={`${
                                    !fileName ? 'hidden ' : ''
                                }mb-6 flex items-center gap-3 bg-teal-50 border border-[#7fffd4] rounded-2xl px-5 py-4`}
                            >
                                <span className="material-symbols-outlined text-[#2dce89]">
                                    description
                                </span>
                                <span
                                    id="fileNameText"
                                    className="text-sm font-bold text-[#004d4d]"
                                >
                                    {fileName}
                                </span>
                            </div>

                            {errors.file && (
                                <p className="text-red-500 text-xs font-bold mb-4">{errors.file}</p>
                            )}

                            <button
                                type="submit"
                                className="w-full py-4 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg hover:scale-[1.01] transition-all"
                            >
                                <span className="material-symbols-outlined text-base align-middle mr-1">
                                    cloud_upload
                                </span>
                                PROSES IMPORT
                            </button>
                        </form>
                    </div>

                    {/* Panel Kanan */}
                    <div className="flex flex-col gap-4">
                        {/* Download Template */}
                        <div className="bg-[#004d4d] rounded-[2rem] p-6 text-white">
                            <span className="material-symbols-outlined text-[#7fffd4] text-3xl mb-3 block">
                                table_view
                            </span>
                            <h3 className="font-black italic uppercase tracking-tight text-sm mb-1">
                                Template CSV
                            </h3>
                            <p className="text-[10px] text-teal-300 font-medium mb-4 leading-relaxed">
                                Download template lalu isi sesuai format yang ditentukan.
                            </p>
                            <a
                                href="/admin/matkul/template"
                                className="flex items-center justify-center gap-2 w-full py-3 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all"
                            >
                                <span className="material-symbols-outlined text-sm">download</span>{' '}
                                DOWNLOAD
                            </a>
                        </div>

                        {/* Panduan Format */}
                        <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                            <h3 className="font-black italic uppercase tracking-widest text-[10px] text-gray-400 mb-4">
                                Format Kolom
                            </h3>
                            <div className="space-y-3">
                                {FORMAT_COLUMNS.map(([col, desc, req]) => (
                                    <div key={col} className="flex items-start gap-3">
                                        <span className="inline-flex items-center px-2 py-0.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-lg text-[10px] font-black font-mono whitespace-nowrap">
                                            {col}
                                        </span>
                                        <div>
                                            <p className="text-[10px] font-bold text-gray-500">
                                                {desc}
                                            </p>
                                            {req && (
                                                <span className="text-[9px] text-red-400 font-black uppercase">
                                                    Wajib
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
