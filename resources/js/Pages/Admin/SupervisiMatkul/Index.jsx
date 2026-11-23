import React from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function SupervisiMatkulIndex({
    supervisi = [],
    grouped: groupedProp = null,
    totalSupervisi = 0,
}) {
    const groupedEntries = React.useMemo(() => {
        if (groupedProp && typeof groupedProp === 'object' && !Array.isArray(groupedProp)) {
            return Object.entries(groupedProp);
        }
        const map = new Map();
        supervisi.forEach((s) => {
            const key = `${s.mata_kuliah_id}_${s.dosen_id}_${s.tahun_ajaran}_${s.semester}`;
            if (!map.has(key)) {
                map.set(key, []);
            }
            map.get(key).push(s);
        });
        return Array.from(map.entries());
    }, [supervisi, groupedProp]);

    const handleDelete = (e, s) => {
        e.preventDefault();
        if (window.confirm(`Hapus supervisi ${s.mahasiswa?.nama ?? ''}?`)) {
            router.delete(`/admin/supervisi/${s.id}`);
        }
    };

    return (
        <AppLayout title="Supervisi Mata Kuliah">
            <div className="flex justify-between items-end mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Supervisi <span className="text-[#2dce89]">Mata Kuliah</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        {totalSupervisi} data supervisi terdaftar
                    </p>
                </div>
                <Link
                    href="/admin/supervisi/create"
                    className="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20"
                >
                    <span className="material-symbols-outlined text-base">add</span> TAMBAH
                    SUPERVISI
                </Link>
            </div>

            {groupedEntries.length === 0 ? (
                <div className="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
                    <span className="material-symbols-outlined text-6xl text-gray-200 block mb-3">
                        school
                    </span>
                    <p className="text-sm text-gray-400 font-medium">Belum ada data supervisi.</p>
                </div>
            ) : (
                <>
                    {/* Stats bar */}
                    <div className="grid grid-cols-3 gap-4 mb-6">
                        <div className="bg-[#004d4d] rounded-2xl p-5">
                            <p className="text-[10px] font-black uppercase tracking-widest text-teal-100/50 mb-1">
                                Total Supervisi
                            </p>
                            <p className="text-3xl font-black text-white">{totalSupervisi}</p>
                        </div>
                        <div className="bg-white rounded-2xl border border-gray-100 p-5">
                            <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">
                                Mata Kuliah
                            </p>
                            <p className="text-3xl font-black text-[#004d4d]">
                                {groupedEntries.length}
                            </p>
                        </div>
                        <div className="bg-white rounded-2xl border border-gray-100 p-5">
                            <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">
                                Mahasiswa
                            </p>
                            <p className="text-3xl font-black text-[#004d4d]">{totalSupervisi}</p>
                        </div>
                    </div>

                    {/* Cards */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {groupedEntries.map(([key, items]) => {
                            const first = items[0];
                            const matkul = first?.mata_kuliah || first?.mataKuliah;
                            const dosen = first?.dosen;

                            return (
                                <div
                                    key={key}
                                    className="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                                >
                                    {/* Card Header */}
                                    <div className="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                                        <div className="flex items-center gap-4">
                                            <div className="w-11 h-11 rounded-2xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                                                <span
                                                    className="material-symbols-outlined text-[#004d4d] text-xl"
                                                    style={{
                                                        fontVariationSettings: "'FILL' 1",
                                                    }}
                                                >
                                                    menu_book
                                                </span>
                                            </div>
                                            <div>
                                                <p className="font-black text-[#004d4d] text-sm">
                                                    {matkul?.nama_matkul}
                                                </p>
                                                <p className="text-[10px] text-gray-400 font-bold mt-0.5">
                                                    {matkul?.kode_matkul} &nbsp;·&nbsp;{' '}
                                                    {matkul?.sks} SKS
                                                </p>
                                            </div>
                                        </div>
                                        <span className="px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 text-[10px] font-black">
                                            {first?.tahun_ajaran} / Sem {first?.semester}
                                        </span>
                                    </div>

                                    {/* Dosen */}
                                    <div className="px-6 py-3 bg-gray-50/50 flex items-center gap-3 border-b border-gray-50">
                                        <span className="material-symbols-outlined text-gray-400 text-base">
                                            school
                                        </span>
                                        <p className="text-xs font-bold text-gray-500">
                                            Dosen Pengampu:{' '}
                                            <span className="text-[#004d4d]">
                                                {dosen?.user?.name ?? dosen?.nama_dosen}
                                            </span>
                                        </p>
                                    </div>

                                    {/* Mahasiswa List */}
                                    <div className="px-6 py-4 space-y-3">
                                        <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">
                                            {items.length} Mahasiswa
                                        </p>
                                        {items.map((s) => (
                                            <div
                                                key={s.id}
                                                className="flex items-center justify-between"
                                            >
                                                <div className="flex items-center gap-3">
                                                    <div className="w-8 h-8 rounded-xl bg-[#004d4d] text-[#7fffd4] flex items-center justify-center font-black text-[10px] flex-shrink-0">
                                                        {(s.mahasiswa?.nama || '')
                                                            .substring(0, 2)
                                                            .toUpperCase()}
                                                    </div>
                                                    <div>
                                                        <p className="text-sm font-black text-[#004d4d]">
                                                            {s.mahasiswa?.nama}
                                                        </p>
                                                        <p className="text-[10px] text-gray-400">
                                                            {s.mahasiswa?.nim}
                                                        </p>
                                                    </div>
                                                </div>
                                                <form onSubmit={(e) => handleDelete(e, s)}>
                                                    <button
                                                        type="submit"
                                                        className="p-2 text-gray-300 hover:text-white hover:bg-red-500 rounded-xl transition-all"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            delete
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </>
            )}
        </AppLayout>
    );
}
