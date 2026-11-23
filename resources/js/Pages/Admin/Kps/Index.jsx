import React from 'react';
import { router, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const prodiColor = {
    informatika: 'bg-blue-100 text-blue-700',
    otomasi: 'bg-emerald-100 text-emerald-700',
    mekatronika: 'bg-purple-100 text-purple-700',
};

const prodiKode = {
    informatika: 'TRIN',
    otomasi: 'TRO',
    mekatronika: 'TRMO',
};

const prodiList = ['informatika', 'otomasi', 'mekatronika'];

export default function KpsIndex({ kpsList = [], dosen = [], prodiMap = {} }) {
    const { flash } = usePage().props;

    const { data, setData, post } = useForm({
        user_id: '',
        prodi: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/kps', {
            onSuccess: () => setData({ user_id: '', prodi: '' }),
        });
    };

    const handleDelete = (e, kps) => {
        e.preventDefault();
        if (window.confirm('Hapus KPS ini?')) {
            router.delete(`/admin/kps/${kps.id}`);
        }
    };

    return (
        <AppLayout title="Manajemen KPS">
            <div className="flex-1 flex flex-col">
                {/* HEADER */}
                <div className="flex justify-between items-end mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Manajemen <span className="text-[#2dce89]">KPS</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            Koordinator Program Studi — {kpsList.length} dari 3 Prodi Terdaftar
                        </p>
                    </div>
                </div>

                {/* STAT CARDS */}
                <div className="grid grid-cols-3 gap-4 mb-8">
                    {prodiList.map((p) => {
                        const kpsProdi = kpsList.find((k) => k.prodi === p);
                        return (
                            <div
                                key={p}
                                className="bg-white border border-gray-100 p-6 rounded-[1.5rem] h-36 flex flex-col justify-between shadow-sm"
                            >
                                <div className="flex items-center justify-between">
                                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        {prodiKode[p].toUpperCase()}
                                    </p>
                                    {kpsProdi ? (
                                        <span className="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                                            Terisi
                                        </span>
                                    ) : (
                                        <span className="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-xl bg-gray-50 text-gray-400 border border-gray-100">
                                            Kosong
                                        </span>
                                    )}
                                </div>
                                {kpsProdi ? (
                                    <p className="text-sm font-black text-[#004d4d] leading-tight">
                                        {kpsProdi.user?.dosen?.nama_dosen ?? kpsProdi.user?.name}
                                    </p>
                                ) : (
                                    <p className="text-sm font-black text-gray-300 italic">
                                        Belum ada KPS
                                    </p>
                                )}
                            </div>
                        );
                    })}
                </div>

                <div className="grid grid-cols-5 gap-6">
                    {/* TABEL KPS AKTIF */}
                    <div className="col-span-3 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div className="px-7 py-5 border-b border-gray-100">
                            <h4 className="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                                <span className="material-symbols-outlined text-lg">
                                    account_balance
                                </span>
                                KPS Aktif
                            </h4>
                        </div>
                        <table className="w-full text-left">
                            <thead>
                                <tr className="bg-gray-50/50 border-b border-gray-100">
                                    <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Dosen
                                    </th>
                                    <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Prodi
                                    </th>
                                    <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {kpsList.length === 0 ? (
                                    <tr>
                                        <td colSpan={3} className="px-6 py-16 text-center">
                                            <span className="material-symbols-outlined text-4xl text-gray-200">
                                                account_balance
                                            </span>
                                            <p className="font-black italic text-gray-300 mt-2 text-sm">
                                                Belum ada KPS terdaftar.
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    kpsList.map((kps) => {
                                        const namaDosen =
                                            kps.user?.dosen?.nama_dosen ?? kps.user?.name ?? '';
                                        return (
                                            <tr
                                                key={kps.id}
                                                className="hover:bg-teal-50/30 transition-colors"
                                            >
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center text-white text-xs font-black shrink-0">
                                                            {namaDosen
                                                                .substring(0, 1)
                                                                .toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="font-black text-sm text-[#004d4d]">
                                                                {namaDosen}
                                                            </p>
                                                            <p className="text-[10px] text-gray-400 font-bold">
                                                                {kps.user?.dosen?.nidn ?? '-'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span
                                                        className={`text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl ${
                                                            prodiColor[kps.prodi] ??
                                                            'bg-gray-100 text-gray-500'
                                                        }`}
                                                    >
                                                        {prodiKode[kps.prodi] ?? kps.prodi}
                                                    </span>
                                                    <p className="text-[10px] text-gray-400 font-bold mt-1">
                                                        {prodiMap[kps.prodi] ?? kps.prodi}
                                                    </p>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <form onSubmit={(e) => handleDelete(e, kps)}>
                                                        <button
                                                            type="submit"
                                                            className="p-2 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500"
                                                        >
                                                            <span className="material-symbols-outlined text-base">
                                                                delete
                                                            </span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* FORM ASSIGN KPS */}
                    <div className="col-span-2">
                        <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-7">
                            <h4 className="text-sm font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2 mb-6">
                                <span className="material-symbols-outlined text-lg">
                                    add_circle
                                </span>
                                Tetapkan KPS
                            </h4>

                            {flash?.error && (
                                <div className="mb-4 p-3 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold flex items-center gap-2">
                                    <span className="material-symbols-outlined text-sm">error</span>
                                    {flash.error}
                                </div>
                            )}

                            {flash?.success && (
                                <div className="mb-4 p-3 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 text-xs font-bold flex items-center gap-2">
                                    <span className="material-symbols-outlined text-sm">
                                        check_circle
                                    </span>
                                    {flash.success}
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div>
                                    <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                        Pilih Dosen
                                    </label>
                                    <select
                                        name="user_id"
                                        value={data.user_id}
                                        onChange={(e) => setData('user_id', e.target.value)}
                                        className="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition"
                                    >
                                        <option value="">-- Pilih Dosen --</option>
                                        {dosen.map((d) => (
                                            <option key={d.id} value={d.user_id}>
                                                {d.nama_dosen} ({d.nidn})
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                        Program Studi
                                    </label>
                                    <select
                                        name="prodi"
                                        value={data.prodi}
                                        onChange={(e) => setData('prodi', e.target.value)}
                                        className="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-bold text-[#004d4d] focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 focus:border-[#2dce89] transition"
                                    >
                                        <option value="">-- Pilih Prodi --</option>
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
                                </div>

                                <button
                                    type="submit"
                                    className="w-full flex items-center justify-center gap-2 px-6 py-3 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg shadow-teal-900/20"
                                >
                                    <span className="material-symbols-outlined text-base">
                                        check
                                    </span>
                                    TETAPKAN KPS
                                </button>
                            </form>
                        </div>

                        {/* INFO */}
                        <div className="mt-4 p-5 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-3">
                            <span className="material-symbols-outlined text-amber-500 text-xl mt-0.5">
                                info
                            </span>
                            <p className="text-xs font-bold text-amber-700 leading-relaxed">
                                Setiap prodi hanya boleh memiliki{' '}
                                <span className="font-black">1 KPS</span>. Dosen yang ditetapkan
                                sebagai KPS akan mendapatkan akses tambahan saat memilih role login.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
