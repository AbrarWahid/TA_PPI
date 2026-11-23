import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiHarianCreate({ supervisiMatkulList = [] }) {
    const todayStr = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors } = useForm({
        supervisi_matkul_id: supervisiMatkulList.length === 1 ? supervisiMatkulList[0].id : '',
        minggu_ke: '',
        hari: '',
        tanggal: todayStr,
        jam_mulai: '',
        jam_selesai: '',
        aktivitas: '',
        kendala: '',
        dokumentasi: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/supervisi-harian', {
            forceFormData: true,
        });
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Tambah Supervisi Harian">
            <div className="max-w-2xl mx-auto">
                <div className="flex items-center gap-4 mb-6">
                    <Link
                        href="/supervisi-harian"
                        className="w-9 h-9 flex items-center justify-center text-[#6a6f7c] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] rounded-lg transition-all border border-[#e4e4e7] bg-white"
                    >
                        <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c]">Tambah Supervisi Harian</h1>
                        <p className="text-xs text-[#6a6f7c] mt-0.5">Catat aktivitas supervisi hari ini</p>
                    </div>
                </div>

                {supervisiMatkulList.length === 0 ? (
                    <div className="bg-amber-50 border border-amber-200 rounded-xl p-5 flex items-start gap-3">
                        <span className="material-symbols-outlined text-[#b45309] mt-0.5">warning</span>
                        <div>
                            <p className="font-semibold text-[#b45309] text-sm">Belum ada supervisi matkul</p>
                            <p className="text-xs text-[#b45309] mt-1">
                                Admin belum menugaskan Anda ke mata kuliah &amp; dosen manapun.
                            </p>
                        </div>
                    </div>
                ) : (
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
                                    Mata Kuliah &amp; Dosen <span className="text-[#b91c1c]">*</span>
                                </label>
                                <select
                                    value={data.supervisi_matkul_id}
                                    onChange={(e) => setData('supervisi_matkul_id', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                >
                                    <option value="">— Pilih Mata Kuliah &amp; Dosen —</option>
                                    {supervisiMatkulList.map((sm) => (
                                        <option key={sm.id} value={sm.id}>
                                            {sm.mata_kuliah?.nama_matkul || '-'} —{' '}
                                            {sm.dosen?.nama_dosen || sm.dosen?.user?.name || '-'}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                        Minggu Ke <span className="text-[#b91c1c]">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min={1}
                                        value={data.minggu_ke}
                                        onChange={(e) => setData('minggu_ke', e.target.value)}
                                        required
                                        placeholder="Contoh: 1"
                                        className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm"
                                    />
                                </div>
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
                                        <option value="">— Pilih Hari —</option>
                                        {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'].map((h) => (
                                            <option key={h} value={h}>
                                                {h}
                                            </option>
                                        ))}
                                    </select>
                                </div>
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
                                    placeholder="Deskripsikan kegiatan supervisi yang dilakukan..."
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
                                    placeholder="Kendala yang dihadapi (jika ada)..."
                                    className="w-full px-3.5 py-2.5 rounded-lg border border-[#e4e4e7] bg-white focus:border-[#013981] outline-none text-sm resize-none"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Dokumentasi{' '}
                                    <span className="text-[#6a6f7c] font-normal">(foto/pdf, maks 5MB)</span>
                                </label>
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
                                    {processing ? 'Menyimpan...' : 'Simpan Entri'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
