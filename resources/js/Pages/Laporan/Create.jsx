import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LaporanCreate({ supervisiList = [] }) {
    const { data, setData, post, errors } = useForm({
        jenis_laporan: '',
        file: null,
    });

    const allErrors = Object.values(errors);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/laporan', { forceFormData: true });
    };

    return (
        <AppLayout title="Upload Laporan">
            {/* HEADING */}
            <div className="mb-8 flex items-center gap-4">
                <Link
                    href="/laporan"
                    className="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 shadow-sm hover:bg-teal-50 transition text-[#004d4d]"
                >
                    <span className="material-symbols-outlined text-xl">arrow_back</span>
                </Link>
                <div>
                    <h1 className="text-3xl font-black italic uppercase text-[#004d4d]">
                        Upload <span className="text-[#2dce89]">Laporan</span>
                    </h1>
                    <p className="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
                        Submit laporan untuk diverifikasi dosen
                    </p>
                </div>
            </div>

            <div className="max-w-xl">
                <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                    {allErrors.length > 0 && (
                        <div className="mb-6 px-5 py-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm">
                            <div className="flex items-center gap-2 mb-2 font-black text-red-700">
                                <span className="material-symbols-outlined text-base">error</span> Terdapat
                                kesalahan
                            </div>
                            <ul className="list-disc list-inside space-y-0.5 text-xs">
                                {allErrors.map((error, i) => (
                                    <li key={i}>{error}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} encType="multipart/form-data" className="space-y-5">
                        {/* Info Supervisi (otomatis) */}
                        <div className="px-4 py-3 rounded-2xl bg-teal-50 border border-teal-100 text-sm text-teal-800">
                            <p className="text-[10px] font-black uppercase tracking-widest text-teal-500 mb-1">
                                Laporan diajukan ke
                            </p>
                            {supervisiList.map((s) => {
                                const mk = s.mata_kuliah || s.mataKuliah;
                                return (
                                    <p key={s.id} className="font-semibold">
                                        {s.dosen?.nama_dosen ?? '-'}{' '}
                                        <span className="font-normal text-teal-600">
                                            · {mk?.nama_matkul ?? '-'}
                                        </span>
                                    </p>
                                );
                            })}
                        </div>

                        {/* Jenis Laporan */}
                        <div>
                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Jenis Laporan
                            </label>
                            <select
                                name="jenis_laporan"
                                value={data.jenis_laporan}
                                onChange={(e) => setData('jenis_laporan', e.target.value)}
                                className="w-full px-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 text-sm text-[#004d4d] font-medium focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-300 transition"
                            >
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Supervisi">Supervisi</option>
                                <option value="Laporan Teknik">Laporan Teknik</option>
                                <option value="PAB">PAB</option>
                            </select>
                        </div>

                        {/* File Upload */}
                        <div>
                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                File Laporan
                            </label>
                            <div className="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-teal-300 hover:bg-teal-50/30 transition-all">
                                <span className="material-symbols-outlined text-4xl text-gray-300 block mb-2">
                                    upload_file
                                </span>
                                <p className="text-sm text-gray-400 font-medium mb-4">
                                    Klik untuk pilih file
                                </p>
                                <input
                                    type="file"
                                    name="file"
                                    accept=".pdf,.doc,.docx"
                                    onChange={(e) => setData('file', e.target.files[0])}
                                    className="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#004d4d] file:text-[#7fffd4] cursor-pointer"
                                />
                                <p className="text-[10px] text-gray-300 font-medium mt-3 uppercase tracking-widest">
                                    Format: PDF, DOC, DOCX · Maks 5MB
                                </p>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex gap-3 pt-2">
                            <button
                                type="submit"
                                className="flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#004d4d] text-[#7fffd4] text-sm font-black hover:opacity-90 transition"
                            >
                                <span className="material-symbols-outlined text-base">upload</span>
                                Upload
                            </button>
                            <Link
                                href="/laporan"
                                className="flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-100 text-slate-500 text-sm font-black hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
