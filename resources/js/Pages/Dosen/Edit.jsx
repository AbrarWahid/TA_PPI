import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function DosenEdit({ dosen }) {
    const { data, setData, put, errors } = useForm({
        nidn: dosen?.nidn ?? '',
        nama_dosen: dosen?.nama_dosen ?? '',
        akses_role: dosen?.user?.akses_role ?? 'dosen_pengampu',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/dosen/${dosen.id}`);
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Edit Dosen">
            <div className="max-w-xl">
                <div className="flex items-center gap-3 mb-8">
                    <Link
                        href="/dosen"
                        className="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors"
                    >
                        <span className="material-symbols-outlined text-on-surface-variant">
                            arrow_back
                        </span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold text-on-surface">Edit Dosen</h1>
                        <p className="text-sm text-on-surface-variant mt-0.5">
                            Perbarui data dosen
                        </p>
                    </div>
                </div>

                <div className="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-6">
                    {allErrors.length > 0 && (
                        <div className="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                            <ul className="list-disc list-inside space-y-1">
                                {allErrors.map((error, i) => (
                                    <li key={i}>{error}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                NIDN
                            </label>
                            <input
                                type="text"
                                name="nidn"
                                value={data.nidn}
                                onChange={(e) => setData('nidn', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface-container-low text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="nama_dosen"
                                value={data.nama_dosen}
                                onChange={(e) => setData('nama_dosen', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface-container-low text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Email
                            </label>
                            <input
                                type="email"
                                value={dosen?.user?.email ?? '-'}
                                className="w-full px-4 py-3 rounded-xl border border-outline-variant/30 bg-surface-container text-sm text-on-surface-variant cursor-not-allowed"
                                disabled
                            />
                            <p className="text-xs text-on-surface-variant/60 mt-1">
                                Email tidak dapat diubah
                            </p>
                        </div>

                        {/* Akses Role */}
                        <div>
                            <label className="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-3">
                                Akses Role
                            </label>
                            <div className="grid grid-cols-1 gap-3">
                                <label
                                    className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                        data.akses_role === 'dosen_pengampu'
                                            ? 'border-[#2dce89] bg-teal-50/50'
                                            : 'border-gray-100 hover:border-[#2dce89]'
                                    }`}
                                >
                                    <input
                                        type="radio"
                                        name="akses_role"
                                        value="dosen_pengampu"
                                        checked={data.akses_role === 'dosen_pengampu'}
                                        onChange={(e) => setData('akses_role', e.target.value)}
                                        className="accent-[#004d4d]"
                                    />
                                    <div className="w-10 h-10 rounded-xl bg-[#2dce89] flex items-center justify-center shrink-0">
                                        <span className="material-symbols-outlined text-[#004d4d] text-xl">
                                            school
                                        </span>
                                    </div>
                                    <div>
                                        <p className="font-black text-[#004d4d] text-sm uppercase">
                                            Dosen Pengampu
                                        </p>
                                        <p className="text-[10px] text-gray-400">
                                            Verifikasi laporan &amp; beri nilai (45%)
                                        </p>
                                    </div>
                                </label>

                                <label
                                    className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                        data.akses_role === 'manager_proyek'
                                            ? 'border-[#2dce89] bg-teal-50/50'
                                            : 'border-gray-100 hover:border-[#2dce89]'
                                    }`}
                                >
                                    <input
                                        type="radio"
                                        name="akses_role"
                                        value="manager_proyek"
                                        checked={data.akses_role === 'manager_proyek'}
                                        onChange={(e) => setData('akses_role', e.target.value)}
                                        className="accent-[#004d4d]"
                                    />
                                    <div
                                        className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                        style={{ background: '#004d4d' }}
                                    >
                                        <span className="material-symbols-outlined text-[#7fffd4] text-xl">
                                            rocket_launch
                                        </span>
                                    </div>
                                    <div>
                                        <p className="font-black text-[#004d4d] text-sm uppercase">
                                            Manager Proyek
                                        </p>
                                        <p className="text-[10px] text-gray-400">
                                            Kelola &amp; ajukan proyek PBL (55%)
                                        </p>
                                    </div>
                                </label>

                                <label
                                    className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                        data.akses_role === 'keduanya'
                                            ? 'border-[#2dce89] bg-teal-50/50'
                                            : 'border-gray-100 hover:border-[#2dce89]'
                                    }`}
                                >
                                    <input
                                        type="radio"
                                        name="akses_role"
                                        value="keduanya"
                                        checked={data.akses_role === 'keduanya'}
                                        onChange={(e) => setData('akses_role', e.target.value)}
                                        className="accent-[#004d4d]"
                                    />
                                    <div className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-[#004d4d] to-[#2dce89]">
                                        <span className="material-symbols-outlined text-white text-xl">
                                            swap_horiz
                                        </span>
                                    </div>
                                    <div>
                                        <p className="font-black text-[#004d4d] text-sm uppercase">
                                            Keduanya
                                        </p>
                                        <p className="text-[10px] text-gray-400">
                                            Dosen bisa switch role saat login
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div className="flex gap-3 pt-1">
                            <button
                                type="submit"
                                className="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary text-sm font-medium hover:bg-secondary transition-colors"
                            >
                                <span className="material-symbols-outlined text-base">check</span>
                                Update
                            </button>
                            <Link
                                href="/dosen"
                                className="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border border-outline-variant text-on-surface-variant hover:bg-surface-container transition-colors"
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
