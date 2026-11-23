import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function MahasiswaEdit({ mahasiswa }) {
    const { data, setData, put, errors } = useForm({
        nim: mahasiswa?.nim ?? '',
        nama: mahasiswa?.nama ?? '',
        prodi: mahasiswa?.user?.prodi ?? '',
        kelas: mahasiswa?.kelas ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/mahasiswa/${mahasiswa.id}`);
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Edit Mahasiswa">
            <div className="max-w-lg">
                {/* HEADER */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/mahasiswa"
                        className="p-2.5 text-outline hover:text-primary hover:bg-primary-container rounded-xl transition-all"
                    >
                        <span className="material-symbols-outlined">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-black text-on-surface tracking-tight">
                            Edit <span className="text-[#2dce89]">Mahasiswa</span>
                        </h1>
                        <p className="text-sm text-on-surface-variant mt-0.5">
                            Perbarui data mahasiswa
                        </p>
                    </div>
                    {/* Avatar */}
                    <div className="ml-auto w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-on-primary font-black text-sm">
                        {(mahasiswa?.nama || '').substring(0, 2).toUpperCase()}
                    </div>
                </div>

                {allErrors.length > 0 && (
                    <div className="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                        <span className="material-symbols-outlined text-red-500 text-base mt-0.5">
                            error
                        </span>
                        <ul className="text-xs text-red-600 font-bold space-y-1">
                            {allErrors.map((error, i) => (
                                <li key={i}>{error}</li>
                            ))}
                        </ul>
                    </div>
                )}

                <div className="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm p-8 space-y-5">
                    <form onSubmit={handleSubmit}>
                        <div className="space-y-5">
                            <div>
                                <label className="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">
                                    NIM
                                </label>
                                <input
                                    type="text"
                                    name="nim"
                                    value={data.nim}
                                    onChange={(e) => setData('nim', e.target.value)}
                                    required
                                    className={`w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all ${
                                        errors.nim ? 'border-red-400 bg-red-50' : ''
                                    }`}
                                />
                                {errors.nim && (
                                    <p className="text-xs text-red-500 font-bold mt-1.5">
                                        {errors.nim}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    name="nama"
                                    value={data.nama}
                                    onChange={(e) => setData('nama', e.target.value)}
                                    required
                                    className={`w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all ${
                                        errors.nama ? 'border-red-400 bg-red-50' : ''
                                    }`}
                                />
                                {errors.nama && (
                                    <p className="text-xs text-red-500 font-bold mt-1.5">
                                        {errors.nama}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">
                                    Program Studi
                                </label>
                                <select
                                    name="prodi"
                                    value={data.prodi}
                                    onChange={(e) => setData('prodi', e.target.value)}
                                    required
                                    className="w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                >
                                    <option value="">Pilih Program Studi</option>
                                    <option value="informatika">
                                        D4 TRIN – Teknologi Rekayasa Informatika Industri
                                    </option>
                                    <option value="otomasi">
                                        D4 TRO – Teknologi Rekayasa Otomasi
                                    </option>
                                    <option value="mekatronika">
                                        D4 TRMO – Teknologi Rekayasa Mekatronika
                                    </option>
                                </select>
                                {errors.prodi && (
                                    <p className="text-xs text-red-500 font-bold mt-1.5">
                                        {errors.prodi}
                                    </p>
                                )}
                            </div>

                            {/* Field Kelas */}
                            <div>
                                <label className="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">
                                    Kelas{' '}
                                    <span className="text-outline/50 normal-case font-medium">
                                        (Opsional)
                                    </span>
                                </label>
                                <input
                                    type="text"
                                    name="kelas"
                                    value={data.kelas}
                                    onChange={(e) => setData('kelas', e.target.value)}
                                    placeholder="contoh: 3 AEA 3"
                                    className={`w-full px-5 py-3.5 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm font-medium text-on-surface placeholder:text-outline/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all ${
                                        errors.kelas ? 'border-red-400 bg-red-50' : ''
                                    }`}
                                />
                                {errors.kelas && (
                                    <p className="text-xs text-red-500 font-bold mt-1.5">
                                        {errors.kelas}
                                    </p>
                                )}
                            </div>

                            {/* Email (read-only) */}
                            <div>
                                <label className="block text-[10px] font-black text-outline uppercase tracking-widest mb-2">
                                    Email
                                </label>
                                <input
                                    type="text"
                                    value={mahasiswa?.user?.email ?? '-'}
                                    disabled
                                    className="w-full px-5 py-3.5 bg-surface-container border border-outline-variant/20 rounded-2xl text-sm text-on-surface-variant cursor-not-allowed"
                                />
                                <p className="text-[11px] text-outline mt-1.5">
                                    Email tidak dapat diubah
                                </p>
                            </div>

                            <div className="flex items-center gap-3 pt-2">
                                <button
                                    type="submit"
                                    className="flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-2xl text-sm font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg"
                                >
                                    <span className="material-symbols-outlined text-base">
                                        check
                                    </span>{' '}
                                    Update
                                </button>
                                <Link
                                    href="/mahasiswa"
                                    className="flex items-center gap-2 px-6 py-3 bg-surface-container text-on-surface-variant rounded-2xl text-sm font-black hover:bg-surface-container-high transition-all"
                                >
                                    Batal
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
