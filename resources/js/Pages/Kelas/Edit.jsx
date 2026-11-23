import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function KelasEdit({ kelas }) {
    const { data, setData, put, errors } = useForm({
        kode_kelas: kelas?.kode_kelas || '',
        nama_kelas: kelas?.nama_kelas || '',
    });

    const allErrors = Object.values(errors);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/kelas/${kelas.id}`);
    };

    return (
        <AppLayout title="Edit Kelas">
            <div className="flex-1 flex flex-col max-w-xl">
                {/* HEADER */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/kelas"
                        className="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-gray-100 hover:bg-teal-50 hover:border-teal-100 transition-all shadow-sm"
                    >
                        <span className="material-symbols-outlined text-[#004d4d]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Edit <span className="text-[#2dce89]">{kelas?.nama_kelas}</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            Perbarui data kelas
                        </p>
                    </div>
                </div>

                {/* WARNING */}
                <div className="mb-6 flex items-start gap-3 p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                    <span className="material-symbols-outlined text-amber-500 text-xl mt-0.5">warning</span>
                    <p className="text-xs font-bold text-amber-700 leading-relaxed">
                        Mengubah <span className="font-black">Nama Kelas</span> akan memutus relasi dengan mahasiswa yang memiliki kelas tersebut.
                        Pastikan nama kelas sesuai dengan data di kolom kelas mahasiswa.
                    </p>
                </div>

                {/* FORM CARD */}
                <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                    {allErrors.length > 0 && (
                        <div className="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold">
                            <ul className="space-y-1">
                                {allErrors.map((error, idx) => (
                                    <li key={idx} className="flex items-center gap-2">
                                        <span className="material-symbols-outlined text-sm">error</span> {error}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Kode Kelas
                            </label>
                            <input
                                type="text"
                                name="kode_kelas"
                                value={data.kode_kelas}
                                onChange={(e) => setData('kode_kelas', e.target.value)}
                                placeholder="contoh: 3AEC4"
                                className="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] placeholder:text-gray-300 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition"
                            />
                        </div>

                        <div>
                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Nama Kelas
                            </label>
                            <input
                                type="text"
                                name="nama_kelas"
                                value={data.nama_kelas}
                                onChange={(e) => setData('nama_kelas', e.target.value)}
                                placeholder="contoh: 3 AEC 4"
                                className="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] placeholder:text-gray-300 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition"
                            />
                            <p className="text-[10px] text-gray-400 font-bold mt-1.5 tracking-wide">
                                Nama kelas harus sama persis dengan data di kolom kelas mahasiswa (contoh: &quot;3 AEA 3&quot;)
                            </p>
                        </div>

                        <div className="flex gap-3 pt-2">
                            <button
                                type="submit"
                                className="flex items-center gap-2 px-6 py-3 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg shadow-teal-900/20"
                            >
                                <span className="material-symbols-outlined text-base">check</span> SIMPAN
                            </button>
                            <Link
                                href="/kelas"
                                className="flex items-center gap-2 px-6 py-3 rounded-2xl text-xs font-black border border-gray-200 text-gray-400 hover:bg-gray-50 transition-all"
                            >
                                BATAL
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
