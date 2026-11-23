import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiHarianEdit({ supervisiHarian, supervisiMatkulList = [] }) {
    const initialDate = supervisiHarian.tanggal ? supervisiHarian.tanggal.substring(0, 10) : '';
    const initialMulai = supervisiHarian.jam_mulai ? supervisiHarian.jam_mulai.substring(0, 5) : '';
    const initialSelesai = supervisiHarian.jam_selesai ? supervisiHarian.jam_selesai.substring(0, 5) : '';

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        supervisi_matkul_id: supervisiHarian.supervisi_matkul_id || '',
        hari: supervisiHarian.hari || 'Senin',
        tanggal: initialDate,
        jam_mulai: initialMulai,
        jam_selesai: initialSelesai,
        aktivitas: supervisiHarian.aktivitas || '',
        kendala: supervisiHarian.kendala || '',
        dokumentasi: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/supervisi-harian/${supervisiHarian.id}`, {
            forceFormData: true,
        });
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Edit Supervisi Harian">
            <div className="max-w-2xl mx-auto">
                <div className="flex items-center gap-4 mb-6">
                    <Link
                        href="/supervisi-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Edit Supervisi Harian</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">Perbarui catatan entri supervisi</p>
                    </div>
                </div>

                <div className="bg-white rounded-xl shadow-surface border border-[#e4e4e7] p-6 sm:p-8 space-y-6">
                    {allErrors.length > 0 && (
                        <div className="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-[#b91c1c]">
                            <ul className="list-disc list-inside space-y-1 font-medium">
                                {allErrors.map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Mata Kuliah &amp; Dosen
                            </label>
                            <select
                                value={data.supervisi_matkul_id}
                                onChange={(e) => setData('supervisi_matkul_id', e.target.value)}
                                className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                            >
                                {supervisiMatkulList.map((sm) => (
                                    <option key={sm.id} value={sm.id}>
                                        {sm.mata_kuliah?.nama_matkul || '-'} —{' '}
                                        {sm.dosen?.nama_dosen || sm.dosen?.user?.name || '-'}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="px-4 py-3 bg-[#fafafa] border border-[#e4e4e7] rounded-lg flex items-center gap-3">
                            <span className="material-symbols-outlined text-[#013981] text-lg">
                                calendar_today
                            </span>
                            <div>
                                <p className="text-xs font-semibold text-[#0b0b0c]">
                                    Minggu ke-{supervisiHarian.minggu_ke}
                                </p>
                                <p className="text-[11px] text-[#6a6f7c]">Minggu tidak dapat diubah di sini</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Hari <span className="text-[#b91c1c]">*</span>
                                </label>
                                <select
                                    value={data.hari}
                                    onChange={(e) => setData('hari', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                >
                                    {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'].map((h) => (
                                        <option key={h} value={h}>
                                            {h}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Tanggal <span className="text-[#b91c1c]">*</span>
                                </label>
                                <input
                                    type="date"
                                    value={data.tanggal}
                                    onChange={(e) => setData('tanggal', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Jam Mulai <span className="text-[#b91c1c]">*</span>
                                </label>
                                <input
                                    type="time"
                                    value={data.jam_mulai}
                                    onChange={(e) => setData('jam_mulai', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Jam Selesai <span className="text-[#b91c1c]">*</span>
                                </label>
                                <input
                                    type="time"
                                    value={data.jam_selesai}
                                    onChange={(e) => setData('jam_selesai', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Aktivitas / Kegiatan <span className="text-[#b91c1c]">*</span>
                            </label>
                            <textarea
                                rows={4}
                                value={data.aktivitas}
                                onChange={(e) => setData('aktivitas', e.target.value)}
                                required
                                className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm resize-none"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Kendala <span className="text-[#6a6f7c] font-normal">(opsional)</span>
                            </label>
                            <textarea
                                rows={3}
                                value={data.kendala}
                                onChange={(e) => setData('kendala', e.target.value)}
                                className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm resize-none"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Dokumentasi
                            </label>
                            {supervisiHarian.dokumentasi && (
                                <div className="mb-2 flex items-center gap-2 text-xs text-[#013981]">
                                    <span className="material-symbols-outlined text-sm">attach_file</span>
                                    <a
                                        href={`/storage/${supervisiHarian.dokumentasi}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="font-semibold underline"
                                    >
                                        File saat ini (klik untuk lihat)
                                    </a>
                                </div>
                            )}
                            <input
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                onChange={(e) => setData('dokumentasi', e.target.files[0])}
                                className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#eef4fc] file:text-[#013981]"
                            />
                        </div>

                        <div className="flex gap-3 pt-2">
                            <Link
                                href="/supervisi-harian"
                                className="flex-1 py-2.5 text-center bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] rounded-lg font-semibold text-xs hover:bg-[#e4e4e7] transition-all"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-1 py-2.5 bg-[#013981] hover:bg-[#012b63] text-white rounded-lg font-semibold text-xs transition-all cursor-pointer"
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
