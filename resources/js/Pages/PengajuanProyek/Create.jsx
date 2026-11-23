import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function PengajuanProyekCreate() {
    const { data, setData, post, errors } = useForm({
        judul_proyek: '',
        deskripsi: '',
        tujuan: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        anggaran: '',
        kebutuhan: [{ prodi: '', jumlah: 1 }],
    });

    const tambah = () => {
        if (data.kebutuhan.length < 3) {
            setData('kebutuhan', [...data.kebutuhan, { prodi: '', jumlah: 1 }]);
        }
    };

    const hapus = (idx) => {
        setData(
            'kebutuhan',
            data.kebutuhan.filter((_, i) => i !== idx),
        );
    };

    const updateKebutuhan = (idx, field, val) => {
        const next = [...data.kebutuhan];
        next[idx] = { ...next[idx], [field]: val };
        setData('kebutuhan', next);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/pengajuan-proyek');
    };

    return (
        <AppLayout title="Ajukan Proyek">
            <div className="flex-1 flex flex-col">
                {/* HEADER */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/pengajuan-proyek"
                        className="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100"
                    >
                        <span className="material-symbols-outlined">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Ajukan <span className="text-[#2dce89]">Proyek Baru</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            Isi form pengajuan proyek PBL
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-3 gap-6">
                        {/* KOLOM KIRI (2/3) */}
                        <div className="col-span-2 space-y-6">
                            {/* Info Proyek */}
                            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                                <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span className="material-symbols-outlined text-[#2dce89]">info</span> Informasi Proyek
                                </h2>

                                <div className="mb-5">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                        Judul Proyek
                                    </label>
                                    <input
                                        type="text"
                                        name="judul_proyek"
                                        value={data.judul_proyek}
                                        onChange={(e) => setData('judul_proyek', e.target.value)}
                                        required
                                        className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                        placeholder="Nama proyek PBL"
                                    />
                                    {errors.judul_proyek && (
                                        <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                            {errors.judul_proyek}
                                        </span>
                                    )}
                                </div>

                                <div className="mb-5">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                        Deskripsi
                                    </label>
                                    <textarea
                                        name="deskripsi"
                                        rows={3}
                                        value={data.deskripsi}
                                        onChange={(e) => setData('deskripsi', e.target.value)}
                                        required
                                        className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm text-gray-700 resize-none"
                                        placeholder="Deskripsi singkat proyek"
                                    />
                                    {errors.deskripsi && (
                                        <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                            {errors.deskripsi}
                                        </span>
                                    )}
                                </div>

                                <div className="mb-5">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                        Tujuan
                                    </label>
                                    <textarea
                                        name="tujuan"
                                        rows={3}
                                        value={data.tujuan}
                                        onChange={(e) => setData('tujuan', e.target.value)}
                                        required
                                        className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm text-gray-700 resize-none"
                                        placeholder="Tujuan proyek"
                                    />
                                    {errors.tujuan && (
                                        <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                            {errors.tujuan}
                                        </span>
                                    )}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Tanggal Mulai
                                        </label>
                                        <input
                                            type="date"
                                            name="tanggal_mulai"
                                            value={data.tanggal_mulai}
                                            onChange={(e) => setData('tanggal_mulai', e.target.value)}
                                            required
                                            className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                        />
                                        {errors.tanggal_mulai && (
                                            <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                                {errors.tanggal_mulai}
                                            </span>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Tanggal Selesai
                                        </label>
                                        <input
                                            type="date"
                                            name="tanggal_selesai"
                                            value={data.tanggal_selesai}
                                            onChange={(e) => setData('tanggal_selesai', e.target.value)}
                                            required
                                            className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                        />
                                        {errors.tanggal_selesai && (
                                            <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                                {errors.tanggal_selesai}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Kebutuhan Tim */}
                            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                                <div className="flex justify-between items-center mb-6">
                                    <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest flex items-center gap-2">
                                        <span className="material-symbols-outlined text-[#2dce89]">groups</span> Kebutuhan Tim
                                    </h2>
                                    <button
                                        type="button"
                                        onClick={tambah}
                                        className="flex items-center gap-1.5 px-4 py-2 bg-teal-50 text-[#004d4d] border border-teal-100 rounded-xl text-[10px] font-black hover:bg-[#004d4d] hover:text-[#7fffd4] transition-all"
                                    >
                                        <span className="material-symbols-outlined text-sm">add</span> TAMBAH PRODI
                                    </button>
                                </div>

                                {errors.kebutuhan && (
                                    <p className="text-red-500 text-xs font-bold mb-4">{errors.kebutuhan}</p>
                                )}

                                {data.kebutuhan.map((item, idx) => (
                                    <div key={idx} className="flex items-center gap-3 mb-3">
                                        <div className="flex-1">
                                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                                Program Studi
                                            </label>
                                            <select
                                                name={`kebutuhan[${idx}][prodi]`}
                                                value={item.prodi}
                                                onChange={(e) => updateKebutuhan(idx, 'prodi', e.target.value)}
                                                className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                            >
                                                <option value="">-- Pilih Prodi --</option>
                                                <option value="mekatronika">
                                                    D4 TRMO - Teknologi Rekayasa Mekatronika
                                                </option>
                                                <option value="otomasi">
                                                    D4 TRO - Teknologi Rekayasa Otomasi
                                                </option>
                                                <option value="informatika">
                                                    D4 TRIN - Teknologi Rekayasa Informatika Industri
                                                </option>
                                            </select>
                                        </div>
                                        <div className="w-32">
                                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                                Jumlah
                                            </label>
                                            <input
                                                type="number"
                                                name={`kebutuhan[${idx}][jumlah]`}
                                                value={item.jumlah}
                                                onChange={(e) =>
                                                    updateKebutuhan(
                                                        idx,
                                                        'jumlah',
                                                        e.target.value === '' ? '' : Number(e.target.value),
                                                    )
                                                }
                                                min={1}
                                                max={50}
                                                className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                                placeholder="0"
                                            />
                                        </div>
                                        <div className="pt-6">
                                            {data.kebutuhan.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => hapus(idx)}
                                                    className="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent hover:border-red-500"
                                                >
                                                    <span className="material-symbols-outlined text-base">
                                                        delete
                                                    </span>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* KOLOM KANAN (1/3) */}
                        <div className="space-y-6">
                            {/* Anggaran */}
                            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                                <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span className="material-symbols-outlined text-[#2dce89]">payments</span> Anggaran
                                </h2>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Estimasi Anggaran (Rp)
                                </label>
                                <input
                                    type="number"
                                    name="anggaran"
                                    value={data.anggaran}
                                    onChange={(e) => setData('anggaran', e.target.value)}
                                    min={0}
                                    className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#7fffd4] outline-none text-sm font-bold text-[#004d4d]"
                                    placeholder="0"
                                />
                                {errors.anggaran && (
                                    <span className="text-red-500 text-[10px] font-bold mt-1 block">
                                        {errors.anggaran}
                                    </span>
                                )}
                            </div>

                            {/* Submit */}
                            <div className="bg-[#004d4d] rounded-[2rem] p-8">
                                <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-2">
                                    Siap Diajukan?
                                </p>
                                <p className="text-white/60 text-xs mb-6">
                                    Pengajuan akan dikirim ke Admin untuk disetujui.
                                </p>
                                <button
                                    type="submit"
                                    className="w-full py-4 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all shadow-lg"
                                >
                                    <span className="material-symbols-outlined text-sm align-middle mr-1">
                                        send
                                    </span>
                                    KIRIM PENGAJUAN
                                </button>
                                <Link
                                    href="/pengajuan-proyek"
                                    className="w-full mt-3 py-3.5 bg-white/10 text-white/60 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-white/20 transition-all text-center block"
                                >
                                    BATAL
                                </Link>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
