import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ApprovalAkun({ pendingDosen = [], approvedDosen = [] }) {
    const { flash } = usePage().props;

    const [popupUser, setPopupUser] = useState(null);
    const [aksesRole, setAksesRole] = useState('keduanya');

    const [searchApproved, setSearchApproved] = useState('');
    const [filterAksesRole, setFilterAksesRole] = useState('');

    const bukaPopup = (user) => {
        setPopupUser(user);
        setAksesRole('keduanya');
    };

    const tutupPopup = () => {
        setPopupUser(null);
    };

    const handleApproveSubmit = (e) => {
        e.preventDefault();
        if (!popupUser) return;
        router.patch(
            `/approval-dosen/${popupUser.id}/approve-role`,
            { akses_role: aksesRole },
            { onSuccess: () => tutupPopup() },
        );
    };

    const handleReject = (e, user) => {
        e.preventDefault();
        if (window.confirm(`Tolak dan hapus akun ${user.name}?`)) {
            router.patch(`/approval-dosen/${user.id}/reject`);
        }
    };

    const formatDateDMYShort = (dateStr) => {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return '-';
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

    const filteredApproved = approvedDosen.filter((u) => {
        const keyword = searchApproved.toLowerCase().trim();
        const nama = (u.name || '').toLowerCase();
        const nidn = (u.dosen?.nidn || '').toLowerCase();
        const akses = u.akses_role || 'keduanya';

        const matchSearch = !keyword || nama.includes(keyword) || nidn.includes(keyword);
        const matchRole = !filterAksesRole || akses === filterAksesRole;
        return matchSearch && matchRole;
    });

    const getAksesInfo = (aksesVal) => {
        const akses = aksesVal || 'keduanya';
        if (akses === 'manager_proyek') {
            return {
                aksesLabel: 'Manager Proyek',
                aksesColor: 'bg-blue-100 text-blue-700',
            };
        }
        if (akses === 'dosen_pengampu') {
            return {
                aksesLabel: 'Dosen Pengampu',
                aksesColor: 'bg-purple-100 text-purple-700',
            };
        }
        return {
            aksesLabel: 'Keduanya',
            aksesColor: 'bg-teal-100 text-teal-700',
        };
    };

    return (
        <AppLayout title="Approval Akun">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Daftar <span className="text-[#2dce89]">Tunggu</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        Persetujuan Akun Dosen Baru
                    </p>
                </div>
                <span
                    className="px-5 py-2.5 rounded-2xl text-xs font-black text-white shadow-lg"
                    style={{
                        background: pendingDosen.length > 0 ? '#f59e0b' : '#004d4d',
                    }}
                >
                    {pendingDosen.length} Permintaan
                </span>
            </div>

            {flash?.success && (
                <div className="mb-6 px-5 py-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold flex items-center gap-2">
                    <span className="material-symbols-outlined text-base">check_circle</span>
                    {flash.success}
                </div>
            )}

            {/* PENDING */}
            <div className="mb-10">
                {pendingDosen.length === 0 ? (
                    <div className="bg-white border border-gray-100 rounded-[2rem] p-16 text-center shadow-sm">
                        <span className="material-symbols-outlined text-6xl text-gray-200 block mb-3">
                            how_to_reg
                        </span>
                        <p className="font-black italic text-gray-300 uppercase tracking-widest text-sm">
                            Antrean Kosong
                        </p>
                        <p className="text-xs text-gray-300 mt-2">
                            Tidak ada permintaan akun baru.
                        </p>
                    </div>
                ) : (
                    <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/50">
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Email
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        NIDN
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Nama
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                        Terdaftar
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">
                                        Tindakan
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {pendingDosen.map((u) => (
                                    <tr
                                        key={u.id}
                                        className="hover:bg-gray-50/50 transition-colors"
                                    >
                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-2 text-gray-500">
                                                <span className="material-symbols-outlined text-[#2dce89] text-base">
                                                    mail
                                                </span>
                                                <span className="text-sm font-bold">{u.email}</span>
                                            </div>
                                        </td>
                                        <td className="px-7 py-5">
                                            <span className="px-3 py-1 rounded-lg bg-gray-100 font-mono text-xs font-black text-gray-600">
                                                {u.dosen?.nidn ?? '-'}
                                            </span>
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-3">
                                                <div
                                                    className="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs text-[#7fffd4] shrink-0"
                                                    style={{ background: '#004d4d' }}
                                                >
                                                    {(u.name || '').substring(0, 2).toUpperCase()}
                                                </div>
                                                <span className="font-black text-gray-800 text-sm">
                                                    {u.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-7 py-5 text-center text-xs text-gray-400 font-bold">
                                            {formatDateDMYShort(u.created_at)}
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center justify-end gap-2">
                                                <form onSubmit={(e) => handleReject(e, u)}>
                                                    <button
                                                        type="submit"
                                                        className="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 rounded-xl text-xs font-black border border-red-200 hover:bg-red-500 hover:text-white transition-all"
                                                    >
                                                        <span className="material-symbols-outlined text-sm">
                                                            close
                                                        </span>
                                                        Tolak
                                                    </button>
                                                </form>
                                                <button
                                                    type="button"
                                                    onClick={() => bukaPopup(u)}
                                                    className="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black text-[#004d4d] shadow-lg hover:scale-105 transition-all"
                                                    style={{ background: '#7fffd4' }}
                                                >
                                                    <span className="material-symbols-outlined text-sm">
                                                        check
                                                    </span>
                                                    Setujui
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* SUDAH DISETUJUI */}
            <div>
                {/* Heading + Filter Bar */}
                <div className="flex items-center justify-between mb-5">
                    <div className="flex items-center gap-3">
                        <h2 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Sudah <span className="text-[#2dce89]">Disetujui</span>
                        </h2>
                        <span
                            id="approvedCount"
                            className="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700"
                        >
                            {filteredApproved.length}
                        </span>
                    </div>

                    {/* FILTER BAR */}
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">
                                search
                            </span>
                            <input
                                type="text"
                                id="searchApproved"
                                value={searchApproved}
                                onChange={(e) => setSearchApproved(e.target.value)}
                                placeholder="Cari nama / NIDN..."
                                className="pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 w-52 transition-all"
                            />
                        </div>
                        <select
                            id="filterAksesRole"
                            value={filterAksesRole}
                            onChange={(e) => setFilterAksesRole(e.target.value)}
                            className="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/40 transition-all"
                        >
                            <option value="">Semua Role</option>
                            <option value="dosen_pengampu">Dosen Pengampu</option>
                            <option value="manager_proyek">Manager Proyek</option>
                            <option value="keduanya">Keduanya</option>
                        </select>
                    </div>
                </div>

                {approvedDosen.length === 0 ? (
                    <div className="bg-white border border-gray-100 rounded-[2rem] p-8 text-center shadow-sm">
                        <p className="text-sm text-gray-300 font-black italic">
                            Belum ada dosen yang disetujui.
                        </p>
                    </div>
                ) : (
                    <div className="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/50">
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Nama
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Email
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        NIDN
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                        Akses Role
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                        Status
                                    </th>
                                    <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Terdaftar
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {filteredApproved.map((u) => {
                                    const { aksesLabel, aksesColor } = getAksesInfo(u.akses_role);
                                    const akses = u.akses_role || 'keduanya';
                                    return (
                                        <tr
                                            key={u.id}
                                            className="hover:bg-gray-50/50 transition-colors approved-row"
                                            data-nama={(u.name || '').toLowerCase()}
                                            data-nidn={(u.dosen?.nidn || '').toLowerCase()}
                                            data-akses={akses}
                                        >
                                            <td className="px-7 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center font-black text-xs text-[#004d4d] shrink-0">
                                                        {(u.name || '')
                                                            .substring(0, 2)
                                                            .toUpperCase()}
                                                    </div>
                                                    <span className="font-semibold text-gray-800 text-sm">
                                                        {u.name}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-7 py-4 text-sm text-gray-500">
                                                {u.email}
                                            </td>
                                            <td className="px-7 py-4 font-mono text-sm text-gray-500">
                                                {u.dosen?.nidn ?? '-'}
                                            </td>
                                            <td className="px-7 py-4 text-center">
                                                <span
                                                    className={`px-3 py-1 rounded-full text-[10px] font-black ${aksesColor}`}
                                                >
                                                    {aksesLabel}
                                                </span>
                                            </td>
                                            <td className="px-7 py-4 text-center">
                                                <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">
                                                    <span className="material-symbols-outlined text-xs">
                                                        check_circle
                                                    </span>
                                                    Aktif
                                                </span>
                                            </td>
                                            <td className="px-7 py-4 text-sm text-gray-400">
                                                {formatDateDMYShort(u.created_at)}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>

                        {/* Empty state saat filter tidak cocok */}
                        <div
                            id="noResultApproved"
                            className={`${
                                filteredApproved.length > 0 ? 'hidden ' : ''
                            }px-10 py-16 text-center`}
                        >
                            <span className="material-symbols-outlined text-4xl text-gray-300 block mb-3">
                                search_off
                            </span>
                            <p className="text-gray-400 font-black italic text-sm uppercase">
                                Tidak ada hasil yang cocok.
                            </p>
                        </div>
                    </div>
                )}
            </div>

            {/* POPUP PILIH ROLE */}
            <div
                id="popupApprove"
                className={`${
                    !popupUser ? 'hidden ' : ''
                }fixed inset-0 z-50 flex items-center justify-center p-6`}
                style={{ background: 'rgba(0,0,0,0.4)' }}
                onClick={(e) => {
                    if (e.target === e.currentTarget) tutupPopup();
                }}
            >
                <div className="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8">
                    <div className="text-center mb-6">
                        <div
                            className="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                            style={{ background: '#004d4d' }}
                        >
                            <span className="material-symbols-outlined text-[#7fffd4] text-2xl">
                                manage_accounts
                            </span>
                        </div>
                        <h3 className="text-xl font-black text-[#004d4d] uppercase tracking-tight italic">
                            Atur Akses <span className="text-[#2dce89]">Role</span>
                        </h3>
                        <p className="text-xs text-gray-400 font-bold mt-1" id="popupNama">
                            {popupUser?.name ?? '—'}
                        </p>
                    </div>

                    <form id="formApprove" onSubmit={handleApproveSubmit}>
                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                            Pilih akses yang diberikan:
                        </p>
                        <div className="grid grid-cols-1 gap-3 mb-6">
                            <label
                                className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                    aksesRole === 'dosen_pengampu'
                                        ? 'border-[#2dce89] bg-teal-50/50'
                                        : 'border-gray-100 hover:border-[#2dce89]'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="akses_role"
                                    value="dosen_pengampu"
                                    checked={aksesRole === 'dosen_pengampu'}
                                    onChange={(e) => setAksesRole(e.target.value)}
                                    className="accent-[#004d4d]"
                                />
                                <div className="w-10 h-10 rounded-xl bg-[#2dce89] flex items-center justify-center shrink-0">
                                    <span className="material-symbols-outlined text-[#004d4d] text-xl">
                                        school
                                    </span>
                                </div>
                                <div>
                                    <p className="font-black text-[#004d4d] text-sm uppercase">
                                        Dosen Pengampu
                                    </p>
                                    <p className="text-[10px] text-gray-400">
                                        Verifikasi laporan &amp; beri nilai (45%)
                                    </p>
                                </div>
                            </label>
                            <label
                                className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                    aksesRole === 'manager_proyek'
                                        ? 'border-[#2dce89] bg-teal-50/50'
                                        : 'border-gray-100 hover:border-[#2dce89]'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="akses_role"
                                    value="manager_proyek"
                                    checked={aksesRole === 'manager_proyek'}
                                    onChange={(e) => setAksesRole(e.target.value)}
                                    className="accent-[#004d4d]"
                                />
                                <div
                                    className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                    style={{ background: '#004d4d' }}
                                >
                                    <span className="material-symbols-outlined text-[#7fffd4] text-xl">
                                        rocket_launch
                                    </span>
                                </div>
                                <div>
                                    <p className="font-black text-[#004d4d] text-sm uppercase">
                                        Manager Proyek
                                    </p>
                                    <p className="text-[10px] text-gray-400">
                                        Kelola &amp; ajukan proyek PBL (55%)
                                    </p>
                                </div>
                            </label>
                            <label
                                className={`flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                                    aksesRole === 'keduanya'
                                        ? 'border-[#2dce89] bg-teal-50/50'
                                        : 'border-gray-100 hover:border-[#2dce89]'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="akses_role"
                                    value="keduanya"
                                    checked={aksesRole === 'keduanya'}
                                    onChange={(e) => setAksesRole(e.target.value)}
                                    className="accent-[#004d4d]"
                                />
                                <div className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-[#004d4d] to-[#2dce89]">
                                    <span className="material-symbols-outlined text-white text-xl">
                                        swap_horiz
                                    </span>
                                </div>
                                <div>
                                    <p className="font-black text-[#004d4d] text-sm uppercase">
                                        Keduanya
                                    </p>
                                    <p className="text-[10px] text-gray-400">
                                        Dosen bisa switch role saat login
                                    </p>
                                </div>
                            </label>
                        </div>
                        <div className="flex gap-3">
                            <button
                                type="button"
                                onClick={tutupPopup}
                                className="flex-1 px-5 py-3 rounded-2xl border border-gray-200 text-xs font-black text-gray-500 hover:bg-gray-50 transition-all"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                className="flex-1 px-5 py-3 rounded-2xl text-xs font-black text-[#004d4d] shadow-lg hover:scale-105 transition-all"
                                style={{ background: '#7fffd4' }}
                            >
                                <span className="material-symbols-outlined text-sm align-middle mr-1">
                                    check
                                </span>
                                Setujui &amp; Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
