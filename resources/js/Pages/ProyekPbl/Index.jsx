import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function formatDate(d) {
    if (!d) return '-';
    const dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    const day = String(dt.getDate()).padStart(2, '0');
    const month = MONTHS[dt.getMonth()];
    const year = dt.getFullYear();
    return `${day} ${month} ${year}`;
}

function getTotalMahasiswa(p) {
    if (!p?.kebutuhan) return 0;
    return p.kebutuhan.reduce((acc, k) => acc + Number(k.jumlah_mahasiswa || 0), 0);
}

function getProdiShort(prodi) {
    switch (prodi) {
        case 'mekatronika':
            return 'Mekatronika';
        case 'otomasi':
            return 'Otomasi';
        case 'informatika_industri':
            return 'Informatika Industri';
        default:
            return prodi;
    }
}

function getProdiColor(prodi) {
    switch (prodi) {
        case 'mekatronika':
            return 'bg-blue-100 text-blue-700';
        case 'otomasi':
            return 'bg-purple-100 text-purple-700';
        case 'informatika_industri':
            return 'bg-orange-100 text-orange-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
}

export default function ProyekPblIndex({ proyek = [] }) {
    const totalMahasiswa = proyek.reduce((sum, p) => sum + getTotalMahasiswa(p), 0);
    const totalProdi = new Set(
        proyek.flatMap((p) => (p.kebutuhan || []).map((k) => k.prodi)).filter(Boolean),
    ).size;

    return (
        <AppLayout title="Proyek PBL">
            {/* HEADING */}
            <div className="mb-8">
                <h1 className="text-3xl font-black italic uppercase text-[#004d4d]">
                    Proyek <span className="text-[#2dce89]">PBL</span>
                </h1>
                <p className="text-sm text-slate-400 mt-1 uppercase tracking-widest font-medium">
                    Daftar proyek yang telah disetujui
                </p>
            </div>

            {/* STAT CARDS */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div className="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
                    <div className="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d] mb-4">
                        <span
                            className="material-symbols-outlined text-2xl"
                            style={{ fontVariationSettings: "'FILL' 1" }}
                        >
                            assignment
                        </span>
                    </div>
                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Proyek</p>
                    <h3 className="text-5xl font-black italic text-[#004d4d] mt-1">{proyek.length}</h3>
                </div>

                <div className="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
                    <div className="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-[#004d4d] mb-4">
                        <span
                            className="material-symbols-outlined text-2xl"
                            style={{ fontVariationSettings: "'FILL' 1" }}
                        >
                            group
                        </span>
                    </div>
                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400">
                        Total Mahasiswa
                    </p>
                    <h3 className="text-5xl font-black italic text-[#004d4d] mt-1">{totalMahasiswa}</h3>
                </div>

                <div className="bg-[#004d4d] rounded-[1.5rem] shadow-sm p-6 hover:-translate-y-0.5 transition-transform">
                    <div className="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-white mb-4">
                        <span
                            className="material-symbols-outlined text-2xl"
                            style={{ fontVariationSettings: "'FILL' 1" }}
                        >
                            school
                        </span>
                    </div>
                    <p className="text-[10px] font-black uppercase tracking-widest text-teal-100/70">
                        Prodi Terlibat
                    </p>
                    <h3 className="text-5xl font-black italic text-white mt-1">{totalProdi}</h3>
                </div>
            </div>

            {/* LIST PROYEK */}
            {proyek.length === 0 ? (
                <div className="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm py-20 text-center">
                    <span className="material-symbols-outlined text-6xl text-gray-200">assignment</span>
                    <p className="mt-4 text-gray-400 text-sm font-medium">Belum ada proyek yang disetujui.</p>
                    <Link
                        href="/pengajuan-proyek"
                        className="mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black bg-[#004d4d] text-[#7fffd4] rounded-2xl hover:opacity-90 transition"
                    >
                        <span className="material-symbols-outlined text-base">arrow_forward</span>
                        Lihat Pengajuan
                    </Link>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {proyek.map((item) => (
                        <div
                            key={item.id}
                            className="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm p-6 hover:shadow-md hover:-translate-y-0.5 transition-all"
                        >
                            {/* Header */}
                            <div className="flex items-start justify-between gap-3 mb-3">
                                <div className="flex-1 min-w-0">
                                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 font-mono">
                                        {item.kode_pengajuan}
                                    </p>
                                    <h3 className="font-black text-[#004d4d] text-base leading-tight">
                                        {item.judul_proyek}
                                    </h3>
                                </div>
                                <span className="flex-shrink-0 inline-flex items-center gap-1 text-[10px] font-black px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 uppercase tracking-wide">
                                    <span
                                        className="material-symbols-outlined text-xs"
                                        style={{ fontVariationSettings: "'FILL' 1" }}
                                    >
                                        check_circle
                                    </span>{' '}
                                    Aktif
                                </span>
                            </div>

                            {/* Deskripsi */}
                            <p className="text-sm text-slate-400 leading-relaxed mb-4 line-clamp-2">
                                {item.deskripsi}
                            </p>

                            {/* Kebutuhan Prodi */}
                            <div className="flex flex-wrap gap-1.5 mb-4">
                                {(item.kebutuhan || []).map((k) => (
                                    <span
                                        key={k.id || k.prodi}
                                        className={`text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wide ${getProdiColor(
                                            k.prodi,
                                        )}`}
                                    >
                                        {getProdiShort(k.prodi)} · {k.jumlah_mahasiswa} mhs
                                    </span>
                                ))}
                            </div>

                            {/* Meta */}
                            <div className="border-t border-gray-100 pt-4 flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-slate-400 font-medium">
                                <span className="flex items-center gap-1">
                                    <span className="material-symbols-outlined text-sm text-[#004d4d]">
                                        person
                                    </span>
                                    {item.manager?.name}
                                </span>
                                <span className="flex items-center gap-1">
                                    <span className="material-symbols-outlined text-sm text-[#004d4d]">
                                        calendar_today
                                    </span>
                                    {formatDate(item.tanggal_mulai)} – {formatDate(item.tanggal_selesai)}
                                </span>
                                <span className="flex items-center gap-1">
                                    <span className="material-symbols-outlined text-sm text-[#004d4d]">
                                        group
                                    </span>
                                    {getTotalMahasiswa(item)} mahasiswa
                                </span>
                                {item.anggaran ? (
                                    <span className="flex items-center gap-1">
                                        <span className="material-symbols-outlined text-sm text-[#004d4d]">
                                            payments
                                        </span>
                                        Rp {Number(item.anggaran).toLocaleString('id-ID')}
                                    </span>
                                ) : null}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
