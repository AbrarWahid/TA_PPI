import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function PeriodePblEdit({ periode_pbl, dosenList = [], tahunAjaranList = [] }) {
    const { data, setData, put, processing, errors } = useForm({
        nama_periode: periode_pbl.nama_periode || '',
        tahun_ajaran: periode_pbl.tahun_ajaran || '2026/2027',
        semester: periode_pbl.semester || 'ganjil',
        koordinator_id: periode_pbl.koordinator_id || '',
        tanggal_mulai: periode_pbl.tanggal_mulai ? periode_pbl.tanggal_mulai.substring(0, 10) : '',
        tanggal_selesai: periode_pbl.tanggal_selesai ? periode_pbl.tanggal_selesai.substring(0, 10) : '',
        frekuensi_logbook: periode_pbl.frekuensi_logbook || 'harian',
        bobot_manager: periode_pbl.bobot_manager ?? 55,
        bobot_dosen: periode_pbl.bobot_dosen ?? 45,
        is_aktif: Boolean(periode_pbl.is_aktif),
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/admin/periode-pbl/${periode_pbl.id}`);
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Edit Periode PBL">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="flex items-center gap-4">
                    <Link
                        href="/admin/periode-pbl"
                        className="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-[#e4e4e7] text-[#6a6f7c] hover:text-[#0b0b0c]"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Edit Periode PBL</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">Perbarui konfigurasi periode PBL</p>
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6 sm:p-8 space-y-5">
                    {allErrors.length > 0 && (
                        <div className="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-[#b91c1c]">
                            <ul className="list-disc list-inside space-y-1">
                                {allErrors.map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Nama Periode
                            </label>
                            <input
                                type="text"
                                value={data.nama_periode}
                                onChange={(e) => setData('nama_periode', e.target.value)}
                                required
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Tahun Ajaran
                                </label>
                                <select
                                    value={data.tahun_ajaran}
                                    onChange={(e) => setData('tahun_ajaran', e.target.value)}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                >
                                    {tahunAjaranList.map((ta) => (
                                        <option key={ta} value={ta}>
                                            {ta}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Semester
                                </label>
                                <select
                                    value={data.semester}
                                    onChange={(e) => setData('semester', e.target.value)}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                >
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Koordinator PBL
                            </label>
                            <select
                                value={data.koordinator_id}
                                onChange={(e) => setData('koordinator_id', e.target.value)}
                                required
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                            >
                                {dosenList.map((d) => (
                                    <option key={d.id} value={d.id}>
                                        {d.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Tanggal Mulai
                                </label>
                                <input
                                    type="date"
                                    value={data.tanggal_mulai}
                                    onChange={(e) => setData('tanggal_mulai', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    value={data.tanggal_selesai}
                                    onChange={(e) => setData('tanggal_selesai', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Bobot Manager (%)
                                </label>
                                <input
                                    type="number"
                                    value={data.bobot_manager}
                                    onChange={(e) => {
                                        const val = Number(e.target.value);
                                        setData((prev) => ({
                                            ...prev,
                                            bobot_manager: val,
                                            bobot_dosen: 100 - val,
                                        }));
                                    }}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Bobot Dosen (%)
                                </label>
                                <input
                                    type="number"
                                    value={data.bobot_dosen}
                                    onChange={(e) => setData('bobot_dosen', Number(e.target.value))}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Frekuensi Logbook
                                </label>
                                <select
                                    value={data.frekuensi_logbook}
                                    onChange={(e) => setData('frekuensi_logbook', e.target.value)}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                >
                                    <option value="harian">Harian</option>
                                    <option value="mingguan">Mingguan</option>
                                </select>
                            </div>
                        </div>

                        <label className="inline-flex items-center gap-2 cursor-pointer pt-1">
                            <input
                                type="checkbox"
                                checked={data.is_aktif}
                                onChange={(e) => setData('is_aktif', e.target.checked)}
                                className="w-4 h-4 rounded border-slate-300 text-[#013981]"
                            />
                            <span className="text-xs font-semibold text-[#0b0b0c]">
                                Periode Aktif
                            </span>
                        </label>

                        <div className="flex gap-3 pt-2">
                            <Link
                                href="/admin/periode-pbl"
                                className="flex-1 py-2.5 text-center bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded-lg text-xs font-semibold"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-1 py-2.5 bg-[#013981] hover:bg-[#012b63] text-white rounded-lg text-xs font-semibold cursor-pointer"
                            >
                                {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
