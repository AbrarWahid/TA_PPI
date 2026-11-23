import React from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function PeriodePblIndex({ periodeList = [], periodeAktif = null }) {
    const { flash } = usePage().props;

    const activePeriode = periodeAktif || periodeList.find((p) => p.is_aktif) || null;

    const labelSemester = (p) => {
        if (!p) return '';
        if (p.label_semester) return p.label_semester;
        const sem = p.semester || '';
        return sem.charAt(0).toUpperCase() + sem.slice(1);
    };

    const formatDateDMYShort = (dateStr) => {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return '';
        const months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
        ];
        const day = String(d.getDate()).padStart(2, '0');
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    };

    const handleSetAktif = (e, item) => {
        e.preventDefault();
        if (
            window.confirm(
                `Aktifkan periode ${item.nama_periode}? Periode aktif sebelumnya akan dinonaktifkan.`,
            )
        ) {
            router.patch(`/admin/periode-pbl/${item.id}/aktif`);
        }
    };

    const handleDelete = (e, item) => {
        e.preventDefault();
        if (
            window.confirm(
                `Hapus periode '${item.nama_periode}'? Tindakan ini tidak dapat dibatalkan.`,
            )
        ) {
            router.delete(`/admin/periode-pbl/${item.id}`);
        }
    };

    return (
        <AppLayout title="Manajemen Periode PBL">
            <div className="flex-1 flex flex-col">
                {/* Header */}
                <div className="flex justify-between items-end mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Manajemen <span className="text-[#2dce89]">Periode PBL</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            Konfigurasi Tahun Ajaran &amp; Sistem Penilaian
                        </p>
                    </div>
                    <Link
                        href="/admin/periode-pbl/create"
                        className="flex items-center gap-2 px-6 py-3.5 bg-[#004d4d] text-[#7fffd4] rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-xl shadow-teal-900/20"
                    >
                        <span className="material-symbols-outlined text-base">add</span> TAMBAH
                        PERIODE
                    </Link>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm font-bold flex items-center gap-2">
                        <span className="material-symbols-outlined text-base">check_circle</span>
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-bold flex items-center gap-2">
                        <span className="material-symbols-outlined text-base">error</span>
                        {flash.error}
                    </div>
                )}

                {/* Banner Periode Aktif */}
                {activePeriode ? (
                    <div className="mb-8 bg-gradient-to-r from-[#004d4d] to-[#007070] rounded-[1.5rem] p-6 shadow-lg shadow-teal-900/20 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-2xl bg-[#7fffd4]/20 flex items-center justify-center flex-shrink-0">
                                <span className="material-symbols-outlined text-[#7fffd4] text-2xl">
                                    event_available
                                </span>
                            </div>
                            <div>
                                <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-1">
                                    Periode Aktif Saat Ini
                                </p>
                                <p className="text-white text-xl font-black italic tracking-tight">
                                    {activePeriode.nama_periode}
                                </p>
                                <p className="text-white/60 text-[11px] font-medium mt-0.5 uppercase tracking-wider">
                                    T.A {activePeriode.tahun_ajaran} •{' '}
                                    {labelSemester(activePeriode)}
                                    {activePeriode.koordinator
                                        ? ` • Koordinator: ${activePeriode.koordinator.name}`
                                        : ''}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center gap-6 flex-shrink-0">
                            <div className="text-center">
                                <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                                    Bobot Nilai
                                </p>
                                <p className="text-white text-lg font-black tracking-tighter mt-0.5">
                                    P {activePeriode.bobot_manager}%{' '}
                                    <span className="text-white/40 text-sm font-medium">:</span> S{' '}
                                    {activePeriode.bobot_dosen}%
                                </p>
                                <p className="text-white/50 text-[10px] font-medium">
                                    Manager : Dosen
                                </p>
                            </div>
                            <div className="text-center">
                                <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                                    Logbook
                                </p>
                                <p className="text-white text-lg font-black tracking-tighter mt-0.5 capitalize">
                                    {activePeriode.frekuensi_logbook}
                                </p>
                            </div>
                            <Link
                                href={`/admin/periode-pbl/${activePeriode.id}/edit`}
                                className="flex items-center gap-1.5 px-4 py-2.5 bg-[#7fffd4]/10 hover:bg-[#7fffd4]/20 text-[#7fffd4] border border-[#7fffd4]/30 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all"
                            >
                                <span className="material-symbols-outlined text-sm">edit</span> Edit
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="mb-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-[1.5rem] p-6 flex items-center gap-4">
                        <div className="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <span className="material-symbols-outlined text-gray-400 text-2xl">
                                event_busy
                            </span>
                        </div>
                        <div>
                            <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-0.5">
                                Periode Aktif Saat Ini
                            </p>
                            <p className="text-gray-500 font-black italic">
                                Belum Ada Periode Aktif
                            </p>
                            <p className="text-gray-400 text-[11px] mt-0.5">
                                Tambahkan periode baru atau aktifkan salah satu periode yang ada.
                            </p>
                        </div>
                    </div>
                )}

                {/* Tabel Semua Periode */}
                <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100">
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">
                                    #
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Nama &amp; Waktu
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    Logbook
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    Bobot Nilai
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    Status
                                </th>
                                <th className="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {periodeList.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-8 py-20 text-center opacity-40">
                                        <span className="material-symbols-outlined text-5xl mb-2 text-gray-400 block">
                                            calendar_today
                                        </span>
                                        <p className="font-black italic text-gray-500">
                                            Belum ada data periode.
                                        </p>
                                    </td>
                                </tr>
                            ) : (
                                periodeList.map((item, idx) => (
                                    <tr
                                        key={item.id}
                                        className={`hover:bg-teal-50/30 transition-colors ${
                                            item.is_aktif ? 'bg-teal-50/20' : ''
                                        }`}
                                    >
                                        <td className="px-8 py-5 text-[10px] font-black text-gray-300">
                                            {idx + 1}
                                        </td>
                                        <td className="px-8 py-5">
                                            <div className="flex items-center gap-4">
                                                <div className="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#008080] to-[#004d4d] flex items-center justify-center text-white font-black text-xs shadow-md flex-shrink-0">
                                                    <span className="material-symbols-outlined text-xl">
                                                        event_note
                                                    </span>
                                                </div>
                                                <div>
                                                    <span className="font-bold text-[#004d4d] block leading-none">
                                                        {item.nama_periode}
                                                    </span>
                                                    <span className="text-[10px] text-gray-400 font-medium mt-1 block tracking-wider uppercase">
                                                        T.A {item.tahun_ajaran} •{' '}
                                                        {labelSemester(item)}
                                                        {item.koordinator
                                                            ? ` • ${item.koordinator.name}`
                                                            : ''}
                                                    </span>
                                                    {item.tanggal_mulai && item.tanggal_selesai && (
                                                        <span className="text-[10px] text-gray-300 font-medium block">
                                                            {formatDateDMYShort(
                                                                item.tanggal_mulai,
                                                            )}{' '}
                                                            –{' '}
                                                            {formatDateDMYShort(
                                                                item.tanggal_selesai,
                                                            )}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-5">
                                            <span className="text-xs text-gray-600 font-bold uppercase tracking-wider capitalize">
                                                {item.frekuensi_logbook}
                                            </span>
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            <span className="inline-flex items-center px-3 py-1.5 bg-gray-50 text-[#004d4d] border border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                                P {item.bobot_manager}% : S {item.bobot_dosen}%
                                            </span>
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            {item.is_aktif ? (
                                                <span className="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                    <span className="material-symbols-outlined text-xs">
                                                        check_circle
                                                    </span>{' '}
                                                    Aktif
                                                </span>
                                            ) : (
                                                <form onSubmit={(e) => handleSetAktif(e, item)}>
                                                    <button
                                                        type="submit"
                                                        className="px-3 py-1 bg-gray-100 hover:bg-[#7fffd4] text-gray-500 hover:text-[#004d4d] rounded-full text-[10px] font-black uppercase tracking-wider transition-all"
                                                    >
                                                        Set Aktif
                                                    </button>
                                                </form>
                                            )}
                                        </td>
                                        <td className="px-8 py-5">
                                            <div className="flex items-center justify-center gap-2">
                                                <Link
                                                    href={`/admin/periode-pbl/${item.id}/edit`}
                                                    className="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-[#7fffd4] rounded-xl transition-all border border-transparent"
                                                    title="Edit"
                                                >
                                                    <span className="material-symbols-outlined text-base">
                                                        edit
                                                    </span>
                                                </Link>
                                                {!item.is_aktif && (
                                                    <form onSubmit={(e) => handleDelete(e, item)}>
                                                        <button
                                                            type="submit"
                                                            className="p-2.5 text-gray-400 hover:text-white hover:bg-red-500 rounded-xl transition-all border border-transparent"
                                                            title="Hapus"
                                                        >
                                                            <span className="material-symbols-outlined text-base">
                                                                delete
                                                            </span>
                                                        </button>
                                                    </form>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
