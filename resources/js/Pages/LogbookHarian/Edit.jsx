import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookHarianEdit({ logbook_harian }) {
    const initialDate = logbook_harian.tanggal ? logbook_harian.tanggal.substring(0, 10) : '';
    const initialMasuk = logbook_harian.jam_masuk ? logbook_harian.jam_masuk.substring(0, 5) : '';
    const initialKeluar = logbook_harian.jam_keluar ? logbook_harian.jam_keluar.substring(0, 5) : '';

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        minggu_ke: logbook_harian.minggu_ke || 1,
        hari: logbook_harian.hari || 'Senin',
        tanggal: initialDate,
        jam_masuk: initialMasuk,
        jam_keluar: initialKeluar,
        aktivitas: logbook_harian.aktivitas || '',
        kendala: logbook_harian.kendala || '',
        dokumentasi: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/logbook-harian/${logbook_harian.id}`, {
            forceFormData: true,
        });
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Edit Logbook Harian">
            <div className="max-w-2xl mx-auto">
                <div className="flex items-center gap-4 mb-6">
                    <Link
                        href={`/logbook-harian/${logbook_harian.id}`}
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Edit Logbook Harian</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">Perbarui catatan aktivitas harian</p>
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6 sm:p-8 space-y-6">
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
                        {/* Proyek (read-only) */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">Proyek</label>
                            <div className="w-full px-3.5 py-2.5 bg-[#fafafa] border border-[#e4e4e7] rounded-lg text-sm text-[#0b0b0c] font-medium">
                                {logbook_harian.proyek?.judul_proyek || '-'}
                            </div>
                        </div>

                        {/* Minggu & Hari */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Minggu Ke <span className="text-[#b91c1c]">*</span>
                                </label>
                                <select
                                    value={data.minggu_ke}
                                    onChange={(e) => setData('minggu_ke', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                >
                                    {Array.from({ length: 20 }, (_, i) => i + 1).map((m) => (
                                        <option key={m} value={m}>
                                            Minggu {m}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Hari <span className="text-[#b91c1c]">*</span>
                                </label>
                                <select
                                    value={data.hari}
                                    onChange={(e) => setData('hari', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                >
                                    {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'].map((h) => (
                                        <option key={h} value={h}>
                                            {h}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Tanggal */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Tanggal <span className="text-[#b91c1c]">*</span>
                            </label>
                            <input
                                type="date"
                                value={data.tanggal}
                                onChange={(e) => setData('tanggal', e.target.value)}
                                required
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                            />
                        </div>

                        {/* Jam Masuk & Jam Keluar */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Jam Masuk <span className="text-[#b91c1c]">*</span>
                                </label>
                                <input
                                    type="time"
                                    value={data.jam_masuk}
                                    onChange={(e) => setData('jam_masuk', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Jam Keluar <span className="text-[#b91c1c]">*</span>
                                </label>
                                <input
                                    type="time"
                                    value={data.jam_keluar}
                                    onChange={(e) => setData('jam_keluar', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                            </div>
                        </div>

                        {/* Aktivitas */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Aktivitas Harian <span className="text-[#b91c1c]">*</span>
                            </label>
                            <textarea
                                rows={4}
                                value={data.aktivitas}
                                onChange={(e) => setData('aktivitas', e.target.value)}
                                required
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm resize-none focus:outline-none focus:border-[#013981]"
                            />
                        </div>

                        {/* Kendala */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Kendala <span className="text-[#6a6f7c] font-normal">(opsional)</span>
                            </label>
                            <textarea
                                rows={3}
                                value={data.kendala}
                                onChange={(e) => setData('kendala', e.target.value)}
                                placeholder="Tuliskan kendala atau hambatan yang dihadapi (jika ada)..."
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm resize-none focus:outline-none focus:border-[#013981]"
                            />
                        </div>

                        {/* Dokumentasi */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Dokumentasi{' '}
                                <span className="text-[#6a6f7c] font-normal">
                                    (opsional · jpg, png, pdf · maks 2MB)
                                </span>
                            </label>
                            {logbook_harian.dokumentasi && (
                                <div className="mb-2 text-xs text-[#013981] flex items-center gap-1">
                                    <span className="material-symbols-outlined text-sm">attach_file</span>
                                    File saat ini:{' '}
                                    <a
                                        href={`/storage/${logbook_harian.dokumentasi}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="underline font-semibold"
                                    >
                                        Lihat Dokumen
                                    </a>
                                </div>
                            )}
                            <input
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                onChange={(e) => setData('dokumentasi', e.target.files[0])}
                                className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-xs file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#eef4fc] file:text-[#013981]"
                            />
                        </div>

                        {/* Submit */}
                        <div className="flex gap-3 pt-2">
                            <Link
                                href={`/logbook-harian/${logbook_harian.id}`}
                                className="flex-1 py-2.5 text-center text-xs font-semibold text-[#0b0b0c] bg-[#f4f4f5] border border-[#e4e4e7] rounded-lg hover:bg-[#e4e4e7] transition-colors"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-1 py-2.5 text-xs font-semibold text-white bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 rounded-lg transition-all cursor-pointer"
                            >
                                {processing ? 'Memperbarui...' : 'Perbarui Logbook'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
