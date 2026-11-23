import React, { useState, useEffect } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const labelProdi = (prodi) => {
    switch (prodi) {
        case 'informatika':
            return 'D4 TRIN - Teknologi Rekayasa Informatika Industri';
        case 'otomasi':
            return 'D4 TRO - Teknologi Rekayasa Otomasi';
        case 'mekatronika':
            return 'D4 TRMO - Teknologi Rekayasa Mekatronika';
        default:
            return prodi || '-';
    }
};

export default function MatkulIndex({ matkuls = [] }) {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, errors, reset } = useForm({
        kode_matkul: '',
        program_studi: '',
        nama_matkul: '',
        sks: '',
        semester: '',
    });

    useEffect(() => {
        if (Object.keys(errors).length > 0) {
            setIsModalOpen(true);
        }
    }, [errors]);

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/matkul', {
            onSuccess: () => {
                reset();
                closeModal();
            },
        });
    };

    const handleDelete = (e, item) => {
        e.preventDefault();
        if (window.confirm(`Hapus mata kuliah ${item.nama_matkul}?`)) {
            router.delete(`/admin/matkul/${item.id}`);
        }
    };

    return (
        <AppLayout title="Master Data Mata Kuliah">
            <div className="flex-1 flex flex-col">
                <div className="flex justify-between items-end mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Master Data <span className="text-[#2dce89]">Mata Kuliah</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            {matkuls.length ?? 0} Mata Kuliah Terdaftar
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <Link
                            href="/admin/matkul/batch"
                            className="flex items-center gap-2 px-6 py-3.5 rounded-2xl text-xs font-black hover:scale-105 transition-all"
                            style={{ background: '#7fffd4', color: '#004d4d' }}
                        >
                            <span className="material-symbols-outlined text-base">
                                upload_file
                            </span>{' '}
                            INPUT MASSAL
                        </Link>
                        <button
                            type="button"
                            onClick={openModal}
                            className="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20"
                        >
                            <span className="material-symbols-outlined text-base">add</span> TAMBAH
                            MATKUL
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div className="bg-[#004d4d] text-white p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
                        <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                            Total Mata Kuliah
                        </p>
                        <p className="text-4xl font-black italic tracking-tighter">
                            {matkuls.length ?? 0}
                        </p>
                    </div>
                    <div className="bg-white border-2 border-[#7fffd4] p-6 rounded-[1.5rem] flex flex-col justify-between shadow-sm">
                        <p className="text-[#004d4d] text-[10px] font-black uppercase tracking-widest">
                            Manajemen
                        </p>
                        <p className="text-xl font-black italic tracking-tighter text-[#004d4d]">
                            Semester Berjalan
                        </p>
                    </div>
                </div>

                <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100">
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">
                                    #
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Kode &amp; Mata Kuliah
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Program Studi
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    SKS
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    Semester
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {matkuls.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-8 py-20 text-center opacity-40">
                                        <span className="material-symbols-outlined text-5xl mb-2 text-gray-400 block">
                                            library_books
                                        </span>
                                        <p className="font-black italic text-gray-500">
                                            Belum ada data mata kuliah.
                                        </p>
                                    </td>
                                </tr>
                            ) : (
                                matkuls.map((item, idx) => (
                                    <tr
                                        key={item.id}
                                        className="hover:bg-teal-50/30 transition-colors group"
                                    >
                                        <td className="px-8 py-5 text-[10px] font-black text-gray-300">
                                            {idx + 1}
                                        </td>
                                        <td className="px-8 py-5">
                                            <div className="flex items-center gap-4">
                                                <div className="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white font-black text-xs shadow-md flex-shrink-0">
                                                    <span className="material-symbols-outlined text-xl">
                                                        menu_book
                                                    </span>
                                                </div>
                                                <div>
                                                    <span className="font-bold text-[#004d4d] block leading-none">
                                                        {item.nama_matkul}
                                                    </span>
                                                    <span className="text-[10px] text-gray-400 font-medium mt-1 block tracking-wider uppercase">
                                                        {item.kode_matkul}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-5">
                                            <span className="text-xs font-bold text-gray-500 italic">
                                                {labelProdi(item.program_studi)}
                                            </span>
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            <span className="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                                {item.sks} SKS
                                            </span>
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            <span className="text-xs font-bold text-[#004d4d]">
                                                Sem {item.semester}
                                            </span>
                                        </td>
                                        <td className="px-8 py-5">
                                            <div className="flex items-center justify-center">
                                                <form onSubmit={(e) => handleDelete(e, item)}>
                                                    <button
                                                        type="submit"
                                                        className="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500"
                                                    >
                                                        <span className="material-symbols-outlined text-base">
                                                            delete
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* MODAL TAMBAH */}
            <div
                id="matkulModal"
                className={`${
                    !isModalOpen ? 'hidden ' : ''
                }fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center`}
            >
                <div className="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg mx-4 border border-gray-100 overflow-hidden">
                    <div className="bg-[#004d4d] p-6 text-center">
                        <h2 className="text-xl font-black text-white italic uppercase tracking-tighter">
                            Tambah <span className="text-[#7fffd4]">Mata Kuliah</span>
                        </h2>
                    </div>
                    <form onSubmit={handleSubmit} className="p-8">
                        <div className="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Kode Matkul
                                </label>
                                <input
                                    type="text"
                                    name="kode_matkul"
                                    value={data.kode_matkul}
                                    onChange={(e) => setData('kode_matkul', e.target.value)}
                                    required
                                    placeholder="MK01"
                                    className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                />
                                {errors.kode_matkul && (
                                    <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                        {errors.kode_matkul}
                                    </span>
                                )}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Program Studi
                                </label>
                                <select
                                    name="program_studi"
                                    value={data.program_studi}
                                    onChange={(e) => setData('program_studi', e.target.value)}
                                    required
                                    className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                >
                                    <option value="">Pilih Prodi</option>
                                    <option value="informatika">
                                        D4 Teknologi Rekayasa Informatika Industri
                                    </option>
                                    <option value="otomasi">D4 Teknologi Rekayasa Otomasi</option>
                                    <option value="mekatronika">
                                        D4 Teknologi Rekayasa Mekatronika
                                    </option>
                                </select>
                                {errors.program_studi && (
                                    <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                        {errors.program_studi}
                                    </span>
                                )}
                            </div>
                        </div>
                        <div className="mb-5">
                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                Nama Matkul
                            </label>
                            <input
                                type="text"
                                name="nama_matkul"
                                value={data.nama_matkul}
                                onChange={(e) => setData('nama_matkul', e.target.value)}
                                required
                                placeholder="Pemrograman Web"
                                className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                            />
                            {errors.nama_matkul && (
                                <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                    {errors.nama_matkul}
                                </span>
                            )}
                        </div>
                        <div className="grid grid-cols-2 gap-4 mb-8">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    SKS
                                </label>
                                <input
                                    type="number"
                                    name="sks"
                                    value={data.sks}
                                    onChange={(e) => setData('sks', e.target.value)}
                                    required
                                    min={1}
                                    max={10}
                                    placeholder="3"
                                    className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                />
                                {errors.sks && (
                                    <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                        {errors.sks}
                                    </span>
                                )}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Semester
                                </label>
                                <input
                                    type="number"
                                    name="semester"
                                    value={data.semester}
                                    onChange={(e) => setData('semester', e.target.value)}
                                    required
                                    min={1}
                                    max={8}
                                    placeholder="1"
                                    className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                />
                                {errors.semester && (
                                    <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                        {errors.semester}
                                    </span>
                                )}
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="flex-1 py-3.5 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-gray-200 transition-all"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                className="flex-1 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg hover:scale-[1.02] transition-all"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
