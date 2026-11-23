import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function LogbookHarianCreate({ proyek = [], periode }) {
    const todayStr = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors } = useForm({
        pengajuan_proyek_id: proyek.length === 1 ? proyek[0].id : '',
        minggu_ke: '',
        hari: '',
        tanggal: todayStr,
        jam_masuk: '',
        jam_keluar: '',
        aktivitas: '',
        kendala: '',
        dokumentasi: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/logbook-harian', {
            forceFormData: true,
        });
    };

    const allErrors = Object.values(errors);
    const minDate = periode?.tanggal_mulai ? periode.tanggal_mulai.substring(0, 10) : undefined;
    const maxDate = periode?.tanggal_selesai ? periode.tanggal_selesai.substring(0, 10) : undefined;

    return (
        <AppLayout title="Input Logbook Harian">
            <div className="max-w-2xl mx-auto">
                <div className="flex items-center gap-4 mb-6">
                    <Link
                        href="/logbook-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Input Logbook Harian</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">Catat aktivitas harian proyek PBL</p>
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface p-6 sm:p-8 space-y-6">
                    {periode && (
                        <div className="bg-[#eef4fc] border border-[#d8e6f8] rounded-xl px-4 py-3 flex items-start gap-3">
                            <span className="material-symbols-outlined text-[#013981] text-lg shrink-0 mt-0.5">
                                event_available
                            </span>
                            <div className="text-xs text-[#0b0b0c]">
                                <span className="font-semibold block mb-0.5 text-[#013981]">
                                    Periode Aktif: {periode.nama_periode}
                                </span>
                                Input logbook hanya diperbolehkan dari <strong>{minDate}</strong> s.d.{' '}
                                <strong>{maxDate}</strong>
                            </div>
                        </div>
                    )}

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
                        {/* Proyek */}
                        <div>
                            <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                Proyek <span className="text-[#b91c1c]">*</span>
                            </label>
                            {proyek.length === 1 ? (
                                <div className="w-full px-3.5 py-2.5 bg-[#fafafa] border border-[#e4e4e7] rounded-lg text-sm text-[#0b0b0c] font-medium">
                                    {proyek[0].judul_proyek}
                                </div>
                            ) : (
                                <select
                                    value={data.pengajuan_proyek_id}
                                    onChange={(e) => setData('pengajuan_proyek_id', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                >
                                    <option value="">-- Pilih Proyek --</option>
                                    {proyek.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.judul_proyek}
                                        </option>
                                    ))}
                                </select>
                            )}
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
                                    <option value="">-- Pilih --</option>
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
                                    <option value="">-- Pilih Hari --</option>
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
                                min={minDate}
                                max={maxDate}
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
                                placeholder="Deskripsikan aktivitas yang dilakukan hari ini..."
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
                                href="/logbook-harian"
                                className="flex-1 py-2.5 text-center text-xs font-semibold text-[#0b0b0c] bg-[#f4f4f5] border border-[#e4e4e7] rounded-lg hover:bg-[#e4e4e7] transition-colors"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-1 py-2.5 text-xs font-semibold text-white bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 rounded-lg transition-all cursor-pointer"
                            >
                                {processing ? 'Menyimpan...' : 'Simpan Logbook'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
