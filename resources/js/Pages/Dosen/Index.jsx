import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function DosenIndex({ dosens = [] }) {
    const [searchQuery, setSearchQuery] = useState('');
    const [roleFilter, setRoleFilter] = useState('semua');
    const [statusFilter, setStatusFilter] = useState('semua');

    const cDosen = dosens.filter((d) => d.user && d.user.akses_role === 'dosen_pengampu').length;
    const cManager = dosens.filter((d) => d.user && d.user.akses_role === 'manager_proyek').length;
    const cKeduanya = dosens.filter((d) => d.user && d.user.akses_role === 'keduanya').length;

    const q = searchQuery.toLowerCase().trim();

    const filteredDosens = dosens.filter((dosen) => {
        const name = (dosen.nama_dosen || '').toLowerCase();
        const nidn = String(dosen.nidn || '');
        const email = (dosen.user?.email || '').toLowerCase();
        const aksesRole = dosen.user?.akses_role ?? 'dosen_pengampu';
        const status = dosen.user?.status ?? 'active';

        const matchSearch = !q || name.includes(q) || nidn.includes(q) || email.includes(q);
        const matchRole = roleFilter === 'semua' || aksesRole === roleFilter;
        const matchStatus = statusFilter === 'semua' || status === statusFilter;

        return matchSearch && matchRole && matchStatus;
    });

    const isFiltered = !!q || roleFilter !== 'semua' || statusFilter !== 'semua';

    const resetFilter = () => {
        setSearchQuery('');
        setRoleFilter('semua');
        setStatusFilter('semua');
    };

    const handleDelete = (e, dosen) => {
        e.preventDefault();
        if (window.confirm('Hapus dosen ini?')) {
            router.delete(`/dosen/${dosen.id}`);
        }
    };

    return (
        <AppLayout title="Data Dosen">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Master Data <span className="text-[#2dce89]">Dosen</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        {dosens.length} dosen terdaftar
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href="/dosen/batch"
                        className="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-black hover:scale-105 transition-all shadow-lg text-[#004d4d]"
                        style={{ background: '#7fffd4' }}
                    >
                        <span className="material-symbols-outlined text-base">table_rows</span>{' '}
                        Input Massal
                    </Link>
                    <Link
                        href="/dosen/create"
                        className="flex items-center gap-2 px-5 py-3 bg-primary text-on-primary rounded-2xl text-xs font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg"
                    >
                        <span className="material-symbols-outlined text-base">person_add</span>{' '}
                        Tambah Dosen
                    </Link>
                </div>
            </div>

            {/* STAT CARDS */}
            <div className="grid grid-cols-4 gap-5 mb-8">
                <div
                    className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36"
                    style={{ background: '#004d4d' }}
                >
                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                        Total Dosen
                    </p>
                    <p className="text-5xl font-black italic tracking-tighter text-white">
                        {dosens.length}
                    </p>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5"
                        style={{ fontSize: '100px' }}
                    >
                        school
                    </span>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-amber-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-amber-500 text-base">
                            school
                        </span>
                        <p className="text-amber-500 text-[10px] font-black uppercase tracking-widest">
                            Dosen Pengampu
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cDosen}
                    </p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-blue-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-blue-500 text-base">
                            manage_accounts
                        </span>
                        <p className="text-blue-500 text-[10px] font-black uppercase tracking-widest">
                            Manager Proyek
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cManager}
                    </p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-teal-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-teal-500 text-base">
                            swap_horiz
                        </span>
                        <p className="text-teal-500 text-[10px] font-black uppercase tracking-widest">
                            Keduanya
                        </p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">
                        {cKeduanya}
                    </p>
                </div>
            </div>

            {/* TABLE */}
            <div className="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                {/* FILTER BAR */}
                <div className="px-7 py-5 border-b border-outline-variant/10 flex flex-wrap items-center gap-3">
                    {/* Search: Nama / NIDN / Email */}
                    <div className="relative w-64">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">
                            search
                        </span>
                        <input
                            type="text"
                            id="searchDosen"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Cari nama, NIDN, atau email..."
                            className="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary"
                        />
                    </div>

                    {/* Divider */}
                    <div className="h-6 w-px bg-outline-variant/20"></div>

                    {/* Filter Akses Role */}
                    <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black text-outline uppercase tracking-widest">
                            Role:
                        </span>
                        {[
                            ['semua', 'Semua'],
                            ['dosen_pengampu', 'Dosen Pengampu'],
                            ['manager_proyek', 'Manager'],
                            ['keduanya', 'Keduanya'],
                        ].map(([val, lbl]) => (
                            <button
                                key={val}
                                type="button"
                                onClick={() => setRoleFilter(val)}
                                data-filter="role"
                                data-value={val}
                                className={`filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ${
                                    roleFilter === val
                                        ? 'bg-primary text-on-primary border-primary'
                                        : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary'
                                }`}
                            >
                                {lbl}
                            </button>
                        ))}
                    </div>

                    {/* Divider */}
                    <div className="h-6 w-px bg-outline-variant/20"></div>

                    {/* Filter Status Akun */}
                    <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black text-outline uppercase tracking-widest">
                            Status:
                        </span>
                        {[
                            ['semua', 'Semua'],
                            ['active', 'Aktif'],
                            ['pending', 'Pending'],
                        ].map(([val, lbl]) => (
                            <button
                                key={val}
                                type="button"
                                onClick={() => setStatusFilter(val)}
                                data-filter="status"
                                data-value={val}
                                className={`filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ${
                                    statusFilter === val
                                        ? 'bg-primary text-on-primary border-primary'
                                        : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary'
                                }`}
                            >
                                {lbl}
                            </button>
                        ))}
                    </div>

                    {/* Reset Filter */}
                    <button
                        type="button"
                        onClick={resetFilter}
                        id="btnReset"
                        className={`${
                            !isFiltered ? 'hidden ' : ''
                        }ml-auto flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:border-red-200 transition-all`}
                    >
                        <span className="material-symbols-outlined text-sm">filter_alt_off</span>{' '}
                        Reset
                    </button>

                    {/* Counter hasil */}
                    <span
                        id="filterCounter"
                        className={`ml-auto text-[10px] font-black text-outline ${
                            !isFiltered ? 'hidden' : ''
                        }`}
                    >
                        — <span id="visibleCount">{filteredDosens.length}</span> dosen ditemukan
                    </span>
                </div>

                {/* TABLE */}
                <table className="w-full text-left" id="dosenTable">
                    <thead>
                        <tr className="border-b border-outline-variant/10 bg-surface-container-low/30">
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">
                                #
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Identitas
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                NIDN
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">
                                Akses Role
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Status
                            </th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-outline-variant/10" id="dosenTbody">
                        {dosens.length === 0 ? (
                            <tr id="emptyData">
                                <td colSpan={6} className="px-7 py-20 text-center">
                                    <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                                        person_search
                                    </span>
                                    <p className="font-black italic text-on-surface-variant/40 text-sm">
                                        Belum ada data dosen.
                                    </p>
                                </td>
                            </tr>
                        ) : (
                            filteredDosens.map((dosen, idx) => {
                                const aksesRole = dosen.user?.akses_role ?? 'dosen_pengampu';
                                const status = dosen.user?.status ?? 'active';
                                const avatarColor =
                                    aksesRole === 'manager_proyek'
                                        ? 'bg-blue-100 text-blue-700'
                                        : aksesRole === 'keduanya'
                                          ? 'bg-teal-100 text-teal-700'
                                          : 'bg-amber-100 text-amber-700';

                                return (
                                    <tr
                                        key={dosen.id}
                                        className="dosen-row hover:bg-surface-container-lowest transition-colors group"
                                        data-name={(dosen.nama_dosen || '').toLowerCase()}
                                        data-nidn={dosen.nidn}
                                        data-email={(dosen.user?.email ?? '').toLowerCase()}
                                        data-role={aksesRole}
                                        data-status={status}
                                    >
                                        <td className="px-7 py-5 text-[10px] font-black text-outline/40 row-num">
                                            {idx + 1}
                                        </td>

                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-3">
                                                <div
                                                    className={`w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 ${avatarColor}`}
                                                >
                                                    {(dosen.nama_dosen || '')
                                                        .substring(0, 2)
                                                        .toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="font-black text-on-surface text-sm leading-none">
                                                        {dosen.nama_dosen}
                                                    </p>
                                                    <p className="text-[10px] text-outline font-medium mt-0.5">
                                                        {dosen.user?.email ?? '-'}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td className="px-7 py-5">
                                            <span className="inline-flex px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-xl text-[10px] font-black font-mono tracking-wider">
                                                {dosen.nidn}
                                            </span>
                                        </td>

                                        <td className="px-7 py-5">
                                            {aksesRole === 'keduanya' ? (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-xl text-[10px] font-black">
                                                    <span className="material-symbols-outlined text-sm">
                                                        swap_horiz
                                                    </span>{' '}
                                                    Keduanya
                                                </span>
                                            ) : aksesRole === 'manager_proyek' ? (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-[10px] font-black">
                                                    <span className="material-symbols-outlined text-sm">
                                                        manage_accounts
                                                    </span>{' '}
                                                    Manager Proyek
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl text-[10px] font-black">
                                                    <span className="material-symbols-outlined text-sm">
                                                        school
                                                    </span>{' '}
                                                    Dosen Pengampu
                                                </span>
                                            )}
                                        </td>

                                        <td className="px-7 py-5 text-center">
                                            {status === 'active' ? (
                                                <span className="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[10px] font-black">
                                                    <span className="material-symbols-outlined text-sm">
                                                        check_circle
                                                    </span>{' '}
                                                    Aktif
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-500 border border-amber-100 rounded-xl text-[10px] font-black">
                                                    <span className="material-symbols-outlined text-sm">
                                                        schedule
                                                    </span>{' '}
                                                    Pending
                                                </span>
                                            )}
                                        </td>

                                        <td className="px-7 py-5">
                                            <div className="flex items-center justify-center gap-2">
                                                <Link
                                                    href={`/dosen/${dosen.id}/edit`}
                                                    className="p-2.5 text-outline hover:text-primary hover:bg-primary-container rounded-xl transition-all"
                                                    title="Edit"
                                                >
                                                    <span className="material-symbols-outlined text-base">
                                                        edit
                                                    </span>
                                                </Link>
                                                <form onSubmit={(e) => handleDelete(e, dosen)}>
                                                    <button
                                                        type="submit"
                                                        className="p-2.5 text-outline hover:text-white hover:bg-red-500 rounded-xl transition-all"
                                                        title="Hapus"
                                                    >
                                                        <span className="material-symbols-outlined text-base">
                                                            delete
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>

                {/* Empty filter state */}
                {dosens.length > 0 && (
                    <div
                        id="emptyFilter"
                        className={`${
                            filteredDosens.length > 0 ? 'hidden ' : ''
                        }px-7 py-16 text-center`}
                    >
                        <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                            search_off
                        </span>
                        <p className="font-black italic text-on-surface-variant/40 text-sm">
                            Tidak ada dosen ditemukan.
                        </p>
                        <button
                            type="button"
                            onClick={resetFilter}
                            className="mt-4 text-[10px] font-black text-primary hover:underline uppercase tracking-widest"
                        >
                            Reset Filter
                        </button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
