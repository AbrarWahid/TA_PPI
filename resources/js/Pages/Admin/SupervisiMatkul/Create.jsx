import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
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
            return prodi || '';
    }
};

export default function SupervisiMatkulCreate({
    mahasiswaList = [],
    matkulList = [],
    dosenList = [],
    prodiList = [],
    periode = null,
}) {
    const [activeProdi, setActiveProdi] = useState('all');
    const [matkulProdiInfo, setMatkulProdiInfo] = useState('');

    const { data, setData, post, errors } = useForm({
        mata_kuliah_id: '',
        dosen_id: '',
        tahun_ajaran: periode?.tahun_ajaran ?? '',
        semester: periode?.semester ?? '',
        mahasiswa_ids: [],
    });

    const visibleMahasiswa = mahasiswaList.filter(
        (m) => activeProdi === 'all' || m.user?.prodi === activeProdi,
    );

    const applyProdiFilter = (prodi) => {
        setActiveProdi(prodi);
        if (prodi !== 'all') {
            const visibleIds = new Set(
                mahasiswaList.filter((m) => m.user?.prodi === prodi).map((m) => m.id),
            );
            setData(
                'mahasiswa_ids',
                data.mahasiswa_ids.filter((id) => visibleIds.has(id)),
            );
        }
    };

    const handleMatkulChange = (e) => {
        const val = e.target.value;
        const selectedMk = matkulList.find((mk) => String(mk.id) === String(val));
        const prodi = selectedMk?.program_studi || '';

        if (prodi) {
            setActiveProdi(prodi);
            setMatkulProdiInfo(prodi);
            const visibleIds = new Set(
                mahasiswaList.filter((m) => m.user?.prodi === prodi).map((m) => m.id),
            );
            setData((prev) => ({
                ...prev,
                mata_kuliah_id: val,
                mahasiswa_ids: prev.mahasiswa_ids.filter((id) => visibleIds.has(id)),
            }));
        } else {
            setActiveProdi('all');
            setMatkulProdiInfo('');
            setData('mata_kuliah_id', val);
        }
    };

    const toggleMahasiswa = (id) => {
        setData(
            'mahasiswa_ids',
            data.mahasiswa_ids.includes(id)
                ? data.mahasiswa_ids.filter((item) => item !== id)
                : [...data.mahasiswa_ids, id],
        );
    };

    const allVisibleChecked =
        visibleMahasiswa.length > 0 &&
        visibleMahasiswa.every((m) => data.mahasiswa_ids.includes(m.id));

    const handleSelectAllChange = (e) => {
        const checked = e.target.checked;
        const visibleIds = visibleMahasiswa.map((m) => m.id);
        if (checked) {
            const union = Array.from(new Set([...data.mahasiswa_ids, ...visibleIds]));
            setData('mahasiswa_ids', union);
        } else {
            const visibleSet = new Set(visibleIds);
            setData(
                'mahasiswa_ids',
                data.mahasiswa_ids.filter((id) => !visibleSet.has(id)),
            );
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/supervisi');
    };

    const allErrors = Object.values(errors);

    return (
        <AppLayout title="Tambah Supervisi">
            <div className="max-w-2xl mx-auto">
                <Link
                    href="/admin/supervisi"
                    className="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-[#004d4d] mb-6 transition-colors"
                >
                    <span className="material-symbols-outlined text-sm">arrow_back</span> Kembali
                </Link>

                <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase mb-1">
                    Tambah <span className="text-[#2dce89]">Supervisi</span>
                </h1>
                <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-8">
                    Assign Mahasiswa ke Mata Kuliah &amp; Dosen Pengampu
                </p>

                {allErrors.length > 0 && (
                    <div className="bg-red-50 border border-red-100 rounded-2xl px-5 py-4 mb-6">
                        <ul className="text-xs text-red-600 font-bold space-y-1">
                            {allErrors.map((e, i) => (
                                <li key={i}>• {e}</li>
                            ))}
                        </ul>
                    </div>
                )}

                {!periode && (
                    <div className="bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3">
                        <span className="material-symbols-outlined text-yellow-500">warning</span>
                        <p className="text-xs font-bold text-yellow-700">
                            Belum ada Periode PBL yang aktif. Tahun ajaran dan semester tidak dapat
                            diisi otomatis.
                        </p>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-7 space-y-6">
                        {/* Mata Kuliah */}
                        <div>
                            <label className="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">
                                Mata Kuliah
                            </label>
                            <select
                                name="mata_kuliah_id"
                                id="matkulSelect"
                                required
                                value={data.mata_kuliah_id}
                                onChange={handleMatkulChange}
                                className="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white"
                            >
                                <option value="">-- Pilih Mata Kuliah --</option>
                                {matkulList.map((mk) => (
                                    <option
                                        key={mk.id}
                                        value={mk.id}
                                        data-prodi={mk.program_studi}
                                    >
                                        {mk.nama_matkul} ({mk.kode_matkul}) —{' '}
                                        {labelProdi(mk.program_studi)}
                                    </option>
                                ))}
                            </select>
                            <p
                                id="matkulProdiInfo"
                                className={`text-[10px] text-teal-600 font-bold mt-1.5 ${
                                    !matkulProdiInfo ? 'hidden' : ''
                                }`}
                            >
                                <span className="material-symbols-outlined text-xs align-middle">
                                    info
                                </span>{' '}
                                Menampilkan mahasiswa prodi:{' '}
                                <span id="matkulProdiLabel">{matkulProdiInfo}</span>
                            </p>
                        </div>

                        {/* Dosen Pengampu */}
                        <div>
                            <label className="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">
                                Dosen Pengampu
                            </label>
                            <select
                                name="dosen_id"
                                required
                                value={data.dosen_id}
                                onChange={(e) => setData('dosen_id', e.target.value)}
                                className="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white"
                            >
                                <option value="">-- Pilih Dosen --</option>
                                {dosenList.map((d) => (
                                    <option key={d.id} value={d.id}>
                                        {d.user?.name ?? d.nama_dosen}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Tahun Ajaran & Semester — dari Periode PBL aktif */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">
                                    Tahun Ajaran
                                </label>
                                <input
                                    type="text"
                                    name="tahun_ajaran"
                                    value={data.tahun_ajaran}
                                    onChange={(e) => setData('tahun_ajaran', e.target.value)}
                                    required
                                    placeholder="2026/2027"
                                    readOnly={!!periode}
                                    className={`w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 ${
                                        periode ? 'bg-gray-50 cursor-not-allowed' : ''
                                    }`}
                                />
                                {periode && (
                                    <p className="text-[10px] text-gray-400 font-medium mt-1">
                                        Dari Periode PBL aktif
                                    </p>
                                )}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-2">
                                    Semester
                                </label>
                                <input
                                    type="text"
                                    value={
                                        periode?.semester
                                            ? periode.semester.charAt(0).toUpperCase() +
                                              periode.semester.slice(1)
                                            : ''
                                    }
                                    readOnly
                                    className="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm text-[#004d4d] font-semibold bg-gray-50 cursor-not-allowed"
                                />
                                <input type="hidden" name="semester" value={data.semester} />
                                {periode && (
                                    <p className="text-[10px] text-gray-400 font-medium mt-1">
                                        Dari Periode PBL aktif
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Multi-select Mahasiswa */}
                        <div>
                            <label className="block text-xs font-black text-[#004d4d] uppercase tracking-widest mb-1">
                                Mahasiswa
                            </label>
                            <p className="text-[10px] text-gray-400 font-medium mb-3">
                                Pilih satu atau lebih mahasiswa yang akan di-assign.
                            </p>

                            {/* Filter Prodi */}
                            <div className="flex flex-wrap gap-2 mb-4" id="prodiFilterBar">
                                <button
                                    type="button"
                                    data-prodi="all"
                                    onClick={() => applyProdiFilter('all')}
                                    className={`prodi-filter px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${
                                        activeProdi === 'all'
                                            ? 'bg-[#004d4d] text-[#7fffd4]'
                                            : 'bg-gray-100 text-gray-500 hover:bg-teal-50 hover:text-teal-700'
                                    }`}
                                >
                                    Semua
                                </button>
                                {prodiList.map((prodi) => (
                                    <button
                                        key={prodi}
                                        type="button"
                                        data-prodi={prodi}
                                        onClick={() => applyProdiFilter(prodi)}
                                        className={`prodi-filter px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${
                                            activeProdi === prodi
                                                ? 'bg-[#004d4d] text-[#7fffd4]'
                                                : 'bg-gray-100 text-gray-500 hover:bg-teal-50 hover:text-teal-700'
                                        }`}
                                    >
                                        {prodi}
                                    </button>
                                ))}
                            </div>

                            {/* Select All */}
                            <div className="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                                <input
                                    type="checkbox"
                                    id="selectAll"
                                    checked={allVisibleChecked}
                                    onChange={handleSelectAllChange}
                                    className="w-4 h-4 rounded accent-teal-600 cursor-pointer"
                                />
                                <label
                                    htmlFor="selectAll"
                                    className="text-xs font-black text-gray-500 cursor-pointer uppercase tracking-widest"
                                >
                                    Pilih Semua
                                </label>
                            </div>

                            {/* Checklist */}
                            <div
                                className="space-y-2 max-h-64 overflow-y-auto pr-1"
                                id="mahasiswaList"
                            >
                                {visibleMahasiswa.map((m) => (
                                    <label
                                        key={m.id}
                                        data-prodi={m.user?.prodi}
                                        className="mahasiswa-item flex items-center gap-3 p-3 rounded-2xl border border-gray-100 hover:border-teal-300 hover:bg-teal-50/30 cursor-pointer transition-all group"
                                    >
                                        <input
                                            type="checkbox"
                                            name="mahasiswa_ids[]"
                                            value={m.id}
                                            checked={data.mahasiswa_ids.includes(m.id)}
                                            onChange={() => toggleMahasiswa(m.id)}
                                            className="mahasiswa-check w-4 h-4 rounded accent-teal-600"
                                        />
                                        <div className="w-8 h-8 rounded-xl bg-[#004d4d] text-[#7fffd4] flex items-center justify-center font-black text-[10px] flex-shrink-0">
                                            {(m.nama || '').substring(0, 2).toUpperCase()}
                                        </div>
                                        <div>
                                            <p className="text-sm font-black text-[#004d4d] group-hover:text-teal-700">
                                                {m.nama}
                                            </p>
                                            <p className="text-[10px] text-gray-400">
                                                {m.nim} &nbsp;·&nbsp; {m.user?.prodi}
                                            </p>
                                        </div>
                                    </label>
                                ))}
                            </div>
                            {errors.mahasiswa_ids && (
                                <p className="text-xs text-red-500 font-bold mt-2">
                                    {errors.mahasiswa_ids}
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end gap-3 mt-6">
                        <Link
                            href="/admin/supervisi"
                            className="px-6 py-3 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            className="px-8 py-3 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg shadow-teal-900/20"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
