import React, { useState, useRef } from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const KETENTUAN_KOLOM = [
    ['nidn', 'Nomor Induk Dosen (unik)', 'tag'],
    ['nama_dosen', 'Nama lengkap dosen', 'person'],
    ['email', 'Email aktif (unik)', 'mail'],
    ['akses_role', 'dosen_pengampu / manager_proyek / keduanya', 'manage_accounts'],
];

export default function DosenBatchCreate() {
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

    const clearFile = () => {
        setData('file', null);
        setFileName('');
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
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
        post('/dosen/batch', { forceFormData: true });
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Import Dosen">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Import <span className="text-[#2dce89]">Dosen</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        Upload file Excel atau CSV sekaligus
                    </p>
                </div>
                <Link
                    href="/dosen"
                    className="flex items-center gap-2 px-5 py-2.5 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all"
                >
                    <span className="material-symbols-outlined text-sm">arrow_back</span> Kembali
                </Link>
            </div>

            {allErrors.length > 0 && (
                <div className="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-xs font-bold">
                    <p className="font-black mb-1 flex items-center gap-2">
                        <span className="material-symbols-outlined text-base">error</span> Ada
                        kesalahan:
                    </p>
                    <ul className="list-disc list-inside space-y-1 mt-1">
                        {allErrors.map((e, i) => (
                            <li key={i}>{e}</li>
                        ))}
                    </ul>
                </div>
            )}

            <div className="grid grid-cols-3 gap-6">
                {/* UPLOAD CARD */}
                <div className="col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                    <p className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6">
                        Upload File
                    </p>

                    <form onSubmit={handleSubmit} encType="multipart/form-data" id="uploadForm">
                        {/* Drop zone */}
                        <div
                            id="dropZone"
                            onClick={() => fileInputRef.current?.click()}
                            onDragOver={(e) => {
                                e.preventDefault();
                                setIsDragging(true);
                            }}
                            onDragLeave={() => setIsDragging(false)}
                            onDrop={handleDrop}
                            className={`relative border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center cursor-pointer hover:border-[#2dce89] hover:bg-teal-50/30 transition-all ${
                                fileName || isDragging ? 'border-[#2dce89] bg-teal-50/30' : ''
                            }`}
                        >
                            <span className="material-symbols-outlined text-5xl text-gray-300 block mb-3">
                                upload_file
                            </span>
                            <p className="text-sm font-black text-gray-400">
                                Klik atau drag &amp; drop file di sini
                            </p>
                            <p className="text-[10px] font-bold text-gray-300 mt-1 uppercase tracking-widest">
                                Format: .xlsx, .xls, .csv — Maks. 2MB
                            </p>
                            <input
                                ref={fileInputRef}
                                type="file"
                                id="fileInput"
                                name="file"
                                accept=".xlsx,.xls,.csv"
                                className="hidden"
                                onChange={handleFileChange}
                            />
                        </div>

                        {/* Preview nama file */}
                        <div
                            id="filePreview"
                            className={`${
                                !fileName ? 'hidden ' : ''
                            }mt-4 px-5 py-3 rounded-xl bg-teal-50 border border-[#2dce89]/30 flex items-center gap-3`}
                        >
                            <span className="material-symbols-outlined text-[#004d4d] text-base">
                                description
                            </span>
                            <p className="text-xs font-black text-[#004d4d] flex-1" id="fileName">
                                {fileName || '—'}
                            </p>
                            <button
                                type="button"
                                onClick={clearFile}
                                className="text-gray-300 hover:text-red-400 transition-colors"
                            >
                                <span className="material-symbols-outlined text-base">close</span>
                            </button>
                        </div>

                        <button
                            type="submit"
                            className="mt-6 w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl text-sm font-black text-[#004d4d] shadow-lg hover:scale-[1.02] transition-all"
                            style={{ background: '#7fffd4' }}
                        >
                            <span className="material-symbols-outlined text-base">
                                cloud_upload
                            </span>
                            Import Sekarang
                        </button>
                    </form>
                </div>

                {/* PANDUAN CARD */}
                <div className="flex flex-col gap-5">
                    {/* Download template */}
                    <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                        <p className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">
                            Template File
                        </p>
                        <p className="text-[11px] text-gray-400 font-medium mb-4 leading-relaxed">
                            Download template, isi data, lalu upload kembali.
                        </p>
                        <a
                            href="/dosen/template"
                            className="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border-2 border-dashed border-[#2dce89] text-[#004d4d] text-xs font-black hover:bg-teal-50 transition-all"
                        >
                            <span className="material-symbols-outlined text-sm">download</span>
                            Download Template
                        </a>
                    </div>

                    {/* Ketentuan */}
                    <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                        <p className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">
                            Ketentuan Kolom
                        </p>
                        <div className="space-y-3">
                            {KETENTUAN_KOLOM.map(([col, desc, icon]) => (
                                <div key={col} className="flex items-start gap-3">
                                    <span className="material-symbols-outlined text-[#2dce89] text-sm mt-0.5">
                                        {icon}
                                    </span>
                                    <div>
                                        <p className="text-[10px] font-black text-[#004d4d] font-mono uppercase">
                                            {col}
                                        </p>
                                        <p className="text-[10px] text-gray-400 font-medium">
                                            {desc}
                                        </p>
                                    </div>
                                </div>
                            ))}
                            <div className="mt-4 pt-4 border-t border-gray-100 space-y-1">
                                <p className="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                                    <span className="material-symbols-outlined text-[10px]">
                                        info
                                    </span>
                                    Password otomatis = password123
                                </p>
                                <p className="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                                    <span className="material-symbols-outlined text-[10px]">
                                        info
                                    </span>
                                    Status akun = active (langsung aktif)
                                </p>
                                <p className="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                                    <span className="material-symbols-outlined text-[10px]">
                                        info
                                    </span>
                                    NIDN/email duplikat otomatis dilewati
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
