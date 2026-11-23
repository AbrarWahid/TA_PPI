import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalAdminDashboard({
    dosens = [],
    perusahaans = [],
    prodis: initialProdis = [],
    mentors = [],
    mahasiswas = [],
    jadwals = [],
}) {
    const [prodis, setProdis] = useState(initialProdis);
    const [expandedProdis, setExpandedProdis] = useState({});

    // State for Matkul Modal
    const [isMatkulModalOpen, setIsMatkulModalOpen] = useState(false);
    const [matkulForm, setMatkulForm] = useState({
        id: null,
        id_prodi: null,
        kd_mat: '',
        nama_komp: '',
        jam_min: '',
    });

    const [mappings, setMappings] = useState(() =>
        (jadwals || []).map((j) => ({
            id: j.id,
            dosenId: j.nidn,
            ptName: j.perusahaan,
            date: j.tanggal,
        }))
    );
    const [mapForm, setMapForm] = useState({ dosenId: '', ptName: '', date: '' });

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const toggleProdi = (idProdi) => {
        setExpandedProdis((prev) => ({ ...prev, [idProdi]: !prev[idProdi] }));
    };

    const addMapping = (e) => {
        e.preventDefault();
        fetch('/external/admin/jadwal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': getCsrf(),
            },
            body: JSON.stringify({
                nidn: mapForm.dosenId,
                perusahaan: mapForm.ptName,
                tanggal: mapForm.date,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    const next = [
                        ...mappings,
                        {
                            id: data.data.id,
                            dosenId: data.data.nidn,
                            ptName: data.data.perusahaan,
                            date: data.data.tanggal,
                        },
                    ];
                    next.sort((a, b) => new Date(a.date) - new Date(b.date));
                    setMappings(next);
                    setMapForm({ dosenId: '', ptName: '', date: '' });
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            });
    };

    const deleteMapping = (id, idx) => {
        if (!window.confirm('Yakin ingin menghapus jadwal ini?')) return;
        fetch(`/external/admin/jadwal/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrf(),
                Accept: 'application/json',
            },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setMappings((prev) => prev.filter((_, i) => i !== idx));
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            });
    };

    // Matkul Logic
    const openMatkulModal = (prodi) => {
        setMatkulForm({
            id: null,
            id_prodi: prodi.id_prodi,
            kd_mat: '',
            nama_komp: '',
            jam_min: '',
        });
        setIsMatkulModalOpen(true);
    };

    const editMatkul = (prodi, matkul) => {
        setMatkulForm({
            id: matkul.id,
            id_prodi: prodi.id_prodi,
            kd_mat: matkul.kd_mat,
            nama_komp: matkul.nama_komp,
            jam_min: matkul.jam_min,
        });
        setIsMatkulModalOpen(true);
    };

    const saveMatkul = (e) => {
        e.preventDefault();
        const url = matkulForm.id
            ? `/external/admin/matkul/${matkulForm.id}`
            : '/external/admin/matkul';
        const method = matkulForm.id ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrf(),
                Accept: 'application/json',
            },
            body: JSON.stringify(matkulForm),
        })
            .then((res) => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then((data) => {
                alert(data.message);
                window.location.reload();
            })
            .catch((err) => {
                alert('Gagal menyimpan mata kuliah.');
                console.error(err);
            });
    };

    const deleteMatkul = (id) => {
        if (!window.confirm('Yakin ingin menghapus mata kuliah ini?')) return;
        fetch(`/external/admin/matkul/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrf(),
                Accept: 'application/json',
            },
        })
            .then((res) => res.json())
            .then((data) => {
                alert(data.message);
                window.location.reload();
            });
    };

    const getMonthShort = (dateStr) => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleString('id-ID', { month: 'short' });
    };

    const getDay = (dateStr) => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleString('id-ID', { day: '2-digit' });
    };

    const getDosenName = (nidn) => {
        const dosen = dosens.find((d) => d.nidn == nidn);
        return dosen ? dosen.nama_dosen : 'Dosen Tidak Diketahui';
    };

    const getPTAlamat = (ptName) => {
        const pt = perusahaans.find((p) => p.nama_perusahaan == ptName);
        return pt ? pt.alamat : '-';
    };

    const getPTKontak = (ptName) => {
        const pt = perusahaans.find((p) => p.nama_perusahaan == ptName);
        return pt ? pt.kontak : '-';
    };

    const getMentorName = (ptName) => {
        const mentor = mentors.find((m) => m.perusahaan == ptName);
        return mentor ? mentor.nama_pem : '-';
    };

    const getMahasiswaList = (ptName, dosenId) => {
        const assigned = mahasiswas.filter(
            (m) => m.nidn == dosenId && m.pembimbing_industri?.perusahaan == ptName
        );
        if (assigned.length === 0) return [];
        return assigned.map((m) => ({
            nama_mhs: m.nama_mhs,
            prodi: m.program_studi?.nama_prodi || '-',
        }));
    };

    return (
        <AppLayout title="Pusat Kendali Administrator">
            <div className="space-y-8 pb-10">
                {/* Header Panel */}
                <div className="bg-white p-6 md:p-8 rounded-2xl border border-[#e4e4e7] shadow-surface flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                    {/* Dekorasi Latar */}
                    <div className="absolute -right-10 -top-10 opacity-10 pointer-events-none">
                        <svg className="w-64 h-64 text-blue-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z" />
                        </svg>
                    </div>

                    <div className="relative z-10">
                        <h2 className="text-4xl font-black text-blue-900 tracking-tight uppercase">
                            Pusat Kendali Sistem
                        </h2>
                        <p className="text-gray-600 mt-2 font-bold text-lg border-l-4 border-blue-500 pl-3">
                            Manajemen pengaturan global PPI, penugasan Dosen, dan batas jam minimum.
                        </p>
                    </div>
                    <div className="relative z-10">
                        <Link
                            href="/external/admin/users"
                            className="bg-[#013981] text-white font-black py-3 px-8 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-3 text-lg"
                        >
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="3"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            Kelola Semua Akun
                        </Link>
                    </div>
                </div>

                {/* PENGATURAN SISTEM (Grid 2 Kolom) */}
                <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    {/* BAGIAN KIRI: Setting Jam Minimum (Format Frame Accordion) */}
                    <div className="bg-white p-6 md:p-8 rounded-2xl border border-[#e4e4e7] shadow-surface flex flex-col h-full">
                        <div className="flex items-center gap-4 border-b border-[#e4e4e7] pb-4 mb-6">
                            <div className="bg-blue-100 p-3 rounded-xl border-2 border-blue-300 text-blue-800">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 className="text-2xl font-black text-blue-900">Standar Jam Minimum</h3>
                                <p className="text-sm font-bold text-gray-500 mt-1">
                                    Atur jam berdasarkan kurikulum tiap Program Studi.
                                </p>
                            </div>
                        </div>

                        {/* Area Scrollable untuk Frame Prodi */}
                        <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar max-h-[480px] space-y-4">
                            {prodis.map((prodi) => {
                                const expanded = !!expandedProdis[prodi.id_prodi];
                                const mataKuliahs = prodi.mata_kuliahs || [];
                                return (
                                    <div
                                        key={prodi.id_prodi}
                                        className="border-4 border-gray-200 rounded-xl bg-gray-50 overflow-hidden mb-4"
                                    >
                                        <div
                                            onClick={() => toggleProdi(prodi.id_prodi)}
                                            className="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors select-none"
                                        >
                                            <h4 className="font-black text-gray-800 text-lg">
                                                {prodi.nama_prodi}
                                            </h4>
                                            <svg
                                                className={`w-6 h-6 text-gray-600 transition-transform duration-300 ${
                                                    expanded ? 'rotate-180' : ''
                                                }`}
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="3"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                        </div>
                                        {expanded && (
                                            <div>
                                                <div className="p-4 pt-0 border-t border-[#e4e4e7] mt-2">
                                                    <div className="flex justify-end mb-3">
                                                        <button
                                                            type="button"
                                                            onClick={() => openMatkulModal(prodi)}
                                                            className="bg-blue-600 text-white text-xs font-black px-4 py-2 rounded-lg border-b border-[#e4e4e7] hover:translate-y-1 hover:border-b-0 transition-all"
                                                        >
                                                            + Tambah Matkul
                                                        </button>
                                                    </div>
                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        {mataKuliahs.map((matkul) => (
                                                            <div
                                                                key={matkul.id}
                                                                className="bg-white p-3 rounded-xl border-2 border-gray-200 focus-within:border-blue-500 transition-colors shadow-sm relative group"
                                                            >
                                                                <div className="flex justify-between items-start mb-1">
                                                                    <div>
                                                                        <span className="text-xs font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                                                                            {matkul.kd_mat}
                                                                        </span>
                                                                        <label className="block text-sm font-black text-gray-700 mt-1">
                                                                            {matkul.nama_komp}
                                                                        </label>
                                                                    </div>
                                                                    <div className="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                editMatkul(prodi, matkul)
                                                                            }
                                                                            className="text-yellow-500 hover:text-yellow-700 p-1"
                                                                            title="Edit"
                                                                        >
                                                                            <svg
                                                                                className="w-4 h-4"
                                                                                fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24"
                                                                            >
                                                                                <path
                                                                                    strokeLinecap="round"
                                                                                    strokeLinejoin="round"
                                                                                    strokeWidth="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                                                />
                                                                            </svg>
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                deleteMatkul(matkul.id)
                                                                            }
                                                                            className="text-red-500 hover:text-red-700 p-1"
                                                                            title="Hapus"
                                                                        >
                                                                            <svg
                                                                                className="w-4 h-4"
                                                                                fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24"
                                                                            >
                                                                                <path
                                                                                    strokeLinecap="round"
                                                                                    strokeLinejoin="round"
                                                                                    strokeWidth="2"
                                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                                />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div className="flex items-center rounded bg-gray-50 border border-gray-200 overflow-hidden mt-2">
                                                                    <div className="w-full p-2 font-black text-gray-900 text-center bg-transparent text-lg">
                                                                        {matkul.jam_min}
                                                                    </div>
                                                                    <span className="bg-gray-200 px-3 py-2 text-xs font-bold text-gray-600 border-l border-gray-300 h-full flex items-center">
                                                                        Jam
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                    {mataKuliahs.length === 0 && (
                                                        <div className="text-center text-gray-400 py-4 font-bold text-sm">
                                                            Belum ada mata kuliah.
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* BAGIAN KANAN: Mapping Dosen ke PT */}
                    <div className="bg-white p-8 rounded-2xl border border-[#e4e4e7] shadow-surface flex flex-col h-full">
                        <div className="flex items-center gap-4 border-b border-[#e4e4e7] pb-4 mb-6">
                            <div className="bg-blue-100 p-3 rounded-xl border-2 border-blue-300 text-blue-800">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 className="text-2xl font-black text-blue-900">Jadwal Penugasan Dosen</h3>
                                <p className="text-sm font-bold text-gray-500 mt-1">
                                    Tetapkan dosen pembimbing ke lokasi industri.
                                </p>
                            </div>
                        </div>

                        {/* Form Input Jadwal */}
                        <form
                            onSubmit={addMapping}
                            className="bg-blue-50 p-5 rounded-xl border-2 border-blue-200 mb-6 space-y-4 shadow-inner"
                        >
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-black text-blue-900 uppercase mb-1">
                                        Pilih Dosen
                                    </label>
                                    <select
                                        value={mapForm.dosenId}
                                        onChange={(e) =>
                                            setMapForm({ ...mapForm, dosenId: e.target.value })
                                        }
                                        required
                                        className="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white"
                                    >
                                        <option value="" disabled>
                                            -- Pilih Dosen --
                                        </option>
                                        {dosens.map((dosen) => (
                                            <option key={dosen.nidn} value={dosen.nidn}>
                                                {dosen.nama_dosen}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-black text-blue-900 uppercase mb-1">
                                        Pilih PT
                                    </label>
                                    <select
                                        value={mapForm.ptName}
                                        onChange={(e) =>
                                            setMapForm({ ...mapForm, ptName: e.target.value })
                                        }
                                        required
                                        className="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white"
                                    >
                                        <option value="" disabled>
                                            -- Pilih PT --
                                        </option>
                                        {perusahaans.map((pt) => (
                                            <option key={pt.id} value={pt.nama_perusahaan}>
                                                {pt.nama_perusahaan}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            <div className="flex flex-col md:flex-row gap-4 items-end">
                                <div className="w-full md:w-2/3">
                                    <label className="block text-xs font-black text-blue-900 uppercase mb-1">
                                        Tanggal Kunjungan
                                    </label>
                                    <input
                                        type="date"
                                        value={mapForm.date}
                                        onChange={(e) =>
                                            setMapForm({ ...mapForm, date: e.target.value })
                                        }
                                        required
                                        className="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    className="w-full md:w-1/3 bg-blue-600 text-white font-black py-3 px-4 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all h-[52px]"
                                >
                                    + Tetapkan
                                </button>
                            </div>
                        </form>

                        {/* List Jadwal */}
                        <div className="flex-1 overflow-y-auto max-h-[300px] pr-2 space-y-3 custom-scrollbar">
                            {mappings.length === 0 ? (
                                <div className="h-full flex flex-col items-center justify-center text-gray-400 p-8 border-2 border-dashed border-gray-300 rounded-xl">
                                    <svg
                                        className="w-12 h-12 mb-2 opacity-50"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <p className="font-bold text-center">
                                        Belum ada jadwal yang ditetapkan.
                                    </p>
                                </div>
                            ) : (
                                mappings.map((map, idx) => {
                                    const mhsList = getMahasiswaList(map.ptName, map.dosenId);
                                    const ptAlamat = getPTAlamat(map.ptName);
                                    const ptKontak = getPTKontak(map.ptName);
                                    return (
                                        <div
                                            key={map.id || idx}
                                            className="bg-white p-4 rounded-xl border-2 border-gray-300 flex justify-between items-center shadow-sm hover:border-blue-500 hover:shadow-md transition-all group"
                                        >
                                            <div className="flex items-start gap-4">
                                                {/* Tanggal Badge */}
                                                <div className="bg-blue-100 text-blue-900 rounded-lg p-2 text-center min-w-[60px] border border-blue-200">
                                                    <span className="block text-xs font-bold uppercase">
                                                        {getMonthShort(map.date)}
                                                    </span>
                                                    <span className="block text-xl font-black leading-none mt-1">
                                                        {getDay(map.date)}
                                                    </span>
                                                </div>
                                                {/* Detail */}
                                                <div className="flex-1">
                                                    <p className="font-black text-gray-800 text-lg group-hover:text-blue-700 transition-colors">
                                                        {map.ptName}
                                                    </p>
                                                    <div className="text-xs text-gray-500 mt-1 mb-2">
                                                        {ptAlamat && (
                                                            <p>
                                                                <span className="font-bold">Alamat:</span>{' '}
                                                                <span>{ptAlamat}</span>
                                                            </p>
                                                        )}
                                                        {ptKontak && (
                                                            <p>
                                                                <span className="font-bold">Kontak:</span>{' '}
                                                                <span>{ptKontak}</span>
                                                            </p>
                                                        )}
                                                        <p>
                                                            <span className="font-bold">Mentor PT:</span>{' '}
                                                            <span>{getMentorName(map.ptName)}</span>
                                                        </p>
                                                    </div>

                                                    <div className="mt-2 pt-2 border-t border-gray-100">
                                                        <p className="text-xs font-bold text-gray-700 mb-1">
                                                            Daftar Mahasiswa:
                                                        </p>
                                                        <ul className="text-xs text-gray-600 list-disc list-inside">
                                                            {mhsList.length > 0 ? (
                                                                mhsList.map((mhs, i) => (
                                                                    <li key={i}>
                                                                        <span className="font-bold">
                                                                            {mhs.nama_mhs}
                                                                        </span>{' '}
                                                                        <span className="text-gray-400">
                                                                            ({mhs.prodi})
                                                                        </span>
                                                                    </li>
                                                                ))
                                                            ) : (
                                                                <li className="text-gray-400 italic list-none text-xs">
                                                                    Belum ada mahasiswa terkait
                                                                </li>
                                                            )}
                                                        </ul>
                                                    </div>

                                                    <div className="flex items-center gap-1 text-blue-700 mt-3 bg-blue-50 w-fit px-2 py-1 rounded border border-blue-200">
                                                        <svg
                                                            className="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                            />
                                                        </svg>
                                                        <p className="font-bold text-sm">
                                                            {getDosenName(map.dosenId)}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => deleteMapping(map.id, idx)}
                                                className="text-red-500 hover:text-white hover:bg-red-500 font-bold p-2 rounded-lg border-2 border-transparent hover:border-red-700 transition-colors"
                                                title="Hapus Jadwal"
                                            >
                                                <svg
                                                    className="w-5 h-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </div>

                {/* Modal Matkul */}
                {isMatkulModalOpen && (
                    <div
                        className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                        onClick={() => setIsMatkulModalOpen(false)}
                    >
                        <div
                            className="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border border-[#e4e4e7]"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <h3 className="text-2xl font-black text-blue-900 mb-6">
                                {matkulForm.id ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah'}
                            </h3>
                            <form onSubmit={saveMatkul}>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-xs font-black text-gray-700 uppercase mb-1">
                                            Kode Matkul
                                        </label>
                                        <input
                                            type="text"
                                            value={matkulForm.kd_mat}
                                            onChange={(e) =>
                                                setMatkulForm({ ...matkulForm, kd_mat: e.target.value })
                                            }
                                            required
                                            className="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none"
                                            placeholder="Contoh: PRO"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-black text-gray-700 uppercase mb-1">
                                            Nama Mata Kuliah
                                        </label>
                                        <input
                                            type="text"
                                            value={matkulForm.nama_komp}
                                            onChange={(e) =>
                                                setMatkulForm({
                                                    ...matkulForm,
                                                    nama_komp: e.target.value,
                                                })
                                            }
                                            required
                                            className="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none"
                                            placeholder="Contoh: Praktik Produksi Otomasi"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-black text-gray-700 uppercase mb-1">
                                            Jam Minimum
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={matkulForm.jam_min}
                                            onChange={(e) =>
                                                setMatkulForm({
                                                    ...matkulForm,
                                                    jam_min:
                                                        e.target.value === ''
                                                            ? ''
                                                            : Number(e.target.value),
                                                })
                                            }
                                            required
                                            className="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none"
                                            placeholder="0"
                                        />
                                    </div>
                                </div>
                                <div className="mt-8 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={() => setIsMatkulModalOpen(false)}
                                        className="px-5 py-2 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        className="bg-blue-600 text-white font-black py-2 px-6 rounded-xl border-b border-[#e4e4e7] hover:translate-y-1 hover:border-b-0 transition-all"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>

            <style>{`
                .custom-scrollbar::-webkit-scrollbar { width: 8px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            `}</style>
        </AppLayout>
    );
}
