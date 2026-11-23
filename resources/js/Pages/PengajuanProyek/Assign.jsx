import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

function labelProdi(prodi) {
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
}

function singkatProdi(prodi) {
    switch (prodi) {
        case 'informatika':
            return 'D4 TRIN';
        case 'otomasi':
            return 'D4 TRO';
        case 'mekatronika':
            return 'D4 TRMO';
        default:
            return prodi || '-';
    }
}

export default function PengajuanProyekAssign({ pengajuan_proyek, mahasiswaPerProdi = {} }) {
    const [search, setSearch] = useState('');
    const [filterProdi, setFilterProdi] = useState('all');

    const initialSelected = {};
    const initialProdiMap = {};
    (pengajuan_proyek?.mahasiswa || []).forEach((m) => {
        initialSelected[m.id] = true;
        initialProdiMap[m.id] = m.pivot?.prodi;
    });

    const [selected, setSelected] = useState(initialSelected);
    const [prodiMap, setProdiMap] = useState(initialProdiMap);

    const countSelected = (prodi) => {
        return Object.entries(selected).filter(([id, val]) => val === true && prodiMap[id] === prodi).length;
    };

    const toggle = (id, prodi, maxKuota, checked) => {
        if (checked) {
            if (countSelected(prodi) >= maxKuota) {
                return;
            }
            setSelected((prev) => ({ ...prev, [id]: true }));
            setProdiMap((prev) => ({ ...prev, [id]: prodi }));
        } else {
            setSelected((prev) => ({ ...prev, [id]: false }));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const selectedIds = Object.entries(selected)
            .filter(([, val]) => val === true)
            .map(([id]) => Number(id));
        router.post(`/pengajuan-proyek/${pengajuan_proyek.id}/assign`, {
            mahasiswa: selectedIds,
        });
    };

    return (
        <AppLayout title="Pilih Mahasiswa">
            <div className="max-w-4xl mx-auto space-y-8">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-black uppercase tracking-tight text-[#004d4d]">
                        Pilih <span className="text-[#2dce89]">Mahasiswa</span>
                    </h1>
                    <p className="text-sm text-gray-400 mt-1">
                        {pengajuan_proyek?.judul_proyek} &mdash; {pengajuan_proyek?.kode_pengajuan}
                    </p>
                    <p className="text-xs text-gray-400 mt-0.5">
                        Dosen Pengampu:{' '}
                        <span className="font-black text-[#004d4d]">{pengajuan_proyek?.manager?.name}</span>
                        <span className="text-gray-300 mx-1">·</span> otomatis dari pengaju proyek
                    </p>
                </div>

                {/* Filter Bar */}
                <div className="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3">
                    <div className="flex-1 relative">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-lg select-none">
                            search
                        </span>
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nama atau NIM mahasiswa..."
                            className="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#2dce89] focus:ring-1 focus:ring-[#2dce89] transition"
                        />
                    </div>
                    <div className="relative">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-lg select-none pointer-events-none">
                            filter_list
                        </span>
                        <select
                            value={filterProdi}
                            onChange={(e) => setFilterProdi(e.target.value)}
                            className="pl-9 pr-8 py-2.5 rounded-xl border border-gray-200 text-sm font-black text-gray-700 focus:outline-none focus:border-[#2dce89] focus:ring-1 focus:ring-[#2dce89] transition appearance-none bg-white cursor-pointer"
                        >
                            <option value="all">Semua Prodi</option>
                            {(pengajuan_proyek?.kebutuhan || []).map((k) => (
                                <option key={k.id || k.prodi} value={k.prodi}>
                                    {labelProdi(k.prodi)}
                                </option>
                            ))}
                        </select>
                        <span className="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none">
                            expand_more
                        </span>
                    </div>
                    {(search !== '' || filterProdi !== 'all') && (
                        <button
                            type="button"
                            onClick={() => {
                                setSearch('');
                                setFilterProdi('all');
                            }}
                            className="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-black text-gray-400 hover:border-gray-300 hover:text-gray-600 transition"
                        >
                            Reset
                        </button>
                    )}
                </div>

                <form onSubmit={handleSubmit} className="space-y-8">
                    {(pengajuan_proyek?.kebutuhan || []).map((kebutuhan) => {
                        const prodi = kebutuhan.prodi;
                        const jumlah = kebutuhan.jumlah_mahasiswa;
                        const daftarMhs = mahasiswaPerProdi[prodi] || [];
                        const showCard = filterProdi === 'all' || filterProdi === prodi;

                        if (!showCard) return null;

                        return (
                            <div
                                key={kebutuhan.id || prodi}
                                className="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden"
                            >
                                {/* Header Prodi */}
                                <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <p className="font-black text-[#004d4d] text-base">{labelProdi(prodi)}</p>
                                        <p className="text-xs text-gray-400 mt-0.5">
                                            Dibutuhkan{' '}
                                            <span className="font-black text-[#2dce89]">{jumlah} mahasiswa</span>
                                        </p>
                                    </div>
                                    <span className="px-3 py-1 rounded-xl text-xs font-black bg-[#004d4d] text-white uppercase tracking-widest">
                                        {singkatProdi(prodi)}
                                    </span>
                                </div>

                                {/* Daftar Mahasiswa */}
                                <div className="p-6">
                                    {daftarMhs.length === 0 ? (
                                        <div className="text-center py-8 text-gray-300">
                                            <span className="material-symbols-outlined text-4xl">person_off</span>
                                            <p className="text-sm mt-2">
                                                Belum ada mahasiswa terdaftar untuk prodi ini.
                                            </p>
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-1 gap-3">
                                            <p className="text-xs text-gray-400 font-bold mb-1">
                                                Pilih maksimal {jumlah} mahasiswa:
                                            </p>

                                            {daftarMhs.map((mhs) => {
                                                const mhsId = mhs.id;
                                                const q = search.toLowerCase();
                                                const matchesSearch =
                                                    search === '' ||
                                                    (mhs.nama || '').toLowerCase().includes(q) ||
                                                    (mhs.nim || '').toLowerCase().includes(q);

                                                if (!matchesSearch) return null;

                                                const isSelected = !!selected[mhsId];

                                                return (
                                                    <label
                                                        key={mhsId}
                                                        className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                                            isSelected
                                                                ? 'border-[#2dce89] bg-[#2dce89]/5'
                                                                : 'border-gray-100 hover:border-gray-200'
                                                        }`}
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            name="mahasiswa[]"
                                                            value={mhsId}
                                                            className="hidden"
                                                            checked={isSelected}
                                                            onChange={(e) =>
                                                                toggle(mhsId, prodi, jumlah, e.target.checked)
                                                            }
                                                        />

                                                        <div
                                                            className={`w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0 transition-colors ${
                                                                isSelected
                                                                    ? 'bg-[#2dce89] text-white'
                                                                    : 'bg-gray-100 text-gray-400'
                                                            }`}
                                                        >
                                                            {(mhs.nama || '').substring(0, 2).toUpperCase()}
                                                        </div>

                                                        <div className="flex-1">
                                                            <p className="font-black text-sm text-[#004d4d]">
                                                                {mhs.nama}
                                                            </p>
                                                            <p className="text-xs text-gray-400">{mhs.nim}</p>
                                                        </div>

                                                        <span
                                                            className={`material-symbols-outlined text-xl transition-all ${
                                                                isSelected ? 'text-[#2dce89]' : 'text-gray-200'
                                                            }`}
                                                        >
                                                            check_circle
                                                        </span>
                                                    </label>
                                                );
                                            })}

                                            <p className="text-xs text-gray-400 mt-1">
                                                Terpilih:{' '}
                                                <span className="font-black text-[#004d4d]">
                                                    {countSelected(prodi)}
                                                </span>{' '}
                                                / {jumlah}
                                            </p>

                                            {countSelected(prodi) > jumlah && (
                                                <p className="text-xs font-black text-red-500 flex items-center gap-1">
                                                    <span className="material-symbols-outlined text-base">
                                                        warning
                                                    </span>
                                                    Melebihi kuota! Maksimal {jumlah} mahasiswa untuk prodi ini.
                                                </p>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        );
                    })}

                    {/* Actions */}
                    <div className="flex items-center justify-between pt-2">
                        <Link
                            href={`/pengajuan-proyek/${pengajuan_proyek?.id}`}
                            className="px-6 py-3 rounded-2xl border-2 border-gray-200 text-sm font-black text-gray-400 hover:border-gray-300 transition"
                        >
                            Kembali
                        </Link>
                        <button
                            type="submit"
                            className="px-8 py-3 rounded-2xl text-sm font-black text-[#7fffd4] transition"
                            style={{ backgroundColor: '#004d4d' }}
                        >
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
