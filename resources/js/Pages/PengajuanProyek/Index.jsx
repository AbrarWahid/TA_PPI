import React, { useState, useMemo } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
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

export default function PengajuanProyekIndex({ pengajuan = [] }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isAdmin = user?.role === 'admin';
    const canCreate =
        user?.role === 'manager_proyek' ||
        (user?.role === 'dosen' && user?.role_aktif === 'manager_proyek');

    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('semua');
    const [managerFilter, setManagerFilter] = useState('semua');

    const cPending = pengajuan.filter((p) => p.status === 'pending').length;
    const cApproved = pengajuan.filter((p) => p.status === 'approved').length;
    const cRejected = pengajuan.filter((p) => p.status === 'rejected').length;

    const managerList = useMemo(() => {
        const map = new Map();
        pengajuan.forEach((p) => {
            if (p.manager && !map.has(p.manager.id)) {
                map.set(p.manager.id, p.manager);
            }
        });
        return Array.from(map.values()).sort((a, b) => (a.name || '').localeCompare(b.name || ''));
    }, [pengajuan]);

    const filtered = useMemo(() => {
        const q = search.toLowerCase().trim();
        return pengajuan.filter((p) => {
            const judul = (p.judul_proyek || '').toLowerCase();
            const kode = (p.kode_pengajuan || '').toLowerCase();
            const matchSearch = !q || judul.includes(q) || kode.includes(q);
            const matchStatus = statusFilter === 'semua' || p.status === statusFilter;
            const matchManager =
                managerFilter === 'semua' || String(p.manager_id) === String(managerFilter);
            return matchSearch && matchStatus && matchManager;
        });
    }, [pengajuan, search, statusFilter, managerFilter]);

    const isFiltered =
        search.trim() !== '' || statusFilter !== 'semua' || managerFilter !== 'semua';

    const resetFilter = () => {
        setSearch('');
        setManagerFilter('semua');
        setStatusFilter('semua');
    };

    const handleDelete = (e, p) => {
        e.preventDefault();
        if (window.confirm('Hapus proyek ini?')) {
            router.delete(`/pengajuan-proyek/${p.id}`);
        }
    };

    return (
        <AppLayout title="Approval Proyek">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Approval <span className="text-[#2dce89]">Proyek</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        {pengajuan.length} pengajuan ditemukan
                    </p>
                </div>
                {canCreate && (
                    <Link
                        href="/pengajuan-proyek/create"
                        className="flex items-center gap-2 px-5 py-3 bg-primary text-on-primary rounded-2xl text-xs font-black hover:opacity-90 hover:scale-105 transition-all shadow-lg"
                    >
                        <span className="material-symbols-outlined text-base">add</span> Ajukan Proyek
                    </Link>
                )}
            </div>

            {/* STAT CARDS */}
            <div className="grid grid-cols-4 gap-5 mb-8">
                <div
                    className="relative overflow-hidden rounded-[2rem] p-7 flex flex-col justify-between shadow-xl h-36"
                    style={{ background: '#004d4d' }}
                >
                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">Total Pengajuan</p>
                    <p className="text-5xl font-black italic tracking-tighter text-white">{pengajuan.length}</p>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5"
                        style={{ fontSize: '100px' }}
                    >
                        rocket_launch
                    </span>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-amber-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                        <p className="text-amber-500 text-[10px] font-black uppercase tracking-widest">Menunggu</p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">{cPending}</p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-emerald-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                        <p className="text-emerald-600 text-[10px] font-black uppercase tracking-widest">Disetujui</p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">{cApproved}</p>
                </div>
                <div className="bg-white rounded-[2rem] border-2 border-red-200 p-7 flex flex-col justify-between h-36 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                        <p className="text-red-500 text-[10px] font-black uppercase tracking-widest">Ditolak</p>
                    </div>
                    <p className="text-5xl font-black italic tracking-tighter text-on-surface">{cRejected}</p>
                </div>
            </div>

            {/* FILTER BAR */}
            <div className="bg-white rounded-[2rem] border border-outline-variant/20 shadow-sm px-7 py-5 mb-5">
                <div className="flex flex-wrap items-center gap-3">
                    {/* Search judul / kode */}
                    <div className="relative w-64">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">
                            search
                        </span>
                        <input
                            type="text"
                            id="searchProyek"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari judul atau kode proyek..."
                            className="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-medium text-on-surface focus:outline-none focus:border-primary"
                        />
                    </div>

                    {/* Divider */}
                    <div className="h-6 w-px bg-outline-variant/20"></div>

                    {/* Filter Status */}
                    <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black text-outline uppercase tracking-widest">Status:</span>
                        {[
                            ['semua', 'Semua'],
                            ['pending', 'Menunggu'],
                            ['approved', 'Disetujui'],
                            ['rejected', 'Ditolak'],
                        ].map(([val, lbl]) => {
                            const isActive = statusFilter === val;
                            return (
                                <button
                                    key={val}
                                    type="button"
                                    onClick={() => setStatusFilter(val)}
                                    data-filter="status"
                                    data-value={val}
                                    className={`filter-btn px-3 py-2 rounded-xl text-[10px] font-black border transition-all ${
                                        isActive
                                            ? 'bg-primary text-on-primary border-primary'
                                            : 'bg-surface-container text-on-surface-variant border-outline-variant/20 hover:border-primary hover:text-primary'
                                    }`}
                                >
                                    {lbl}
                                    {val === 'pending' && cPending > 0 && (
                                        <span className="ml-1 px-1.5 py-0.5 rounded-full bg-amber-400 text-white text-[9px] font-black">
                                            {cPending}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>

                    {/* Divider */}
                    <div className="h-6 w-px bg-outline-variant/20"></div>

                    {/* Filter Manager */}
                    {managerList.length > 1 && (
                        <div className="flex items-center gap-2">
                            <span className="text-[10px] font-black text-outline uppercase tracking-widest">
                                Manager:
                            </span>
                            <select
                                id="filterManager"
                                value={managerFilter}
                                onChange={(e) => setManagerFilter(e.target.value)}
                                className="py-2 pl-3 pr-8 bg-surface-container-low rounded-xl border border-outline-variant/20 text-xs font-black text-on-surface focus:outline-none focus:border-primary appearance-none cursor-pointer"
                            >
                                <option value="semua">Semua</option>
                                {managerList.map((mgr) => (
                                    <option key={mgr.id} value={mgr.id}>
                                        {mgr.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}

                    {/* Reset & Counter */}
                    <div className="ml-auto flex items-center gap-3">
                        {isFiltered && (
                            <>
                                <span id="filterCounter" className="text-[10px] font-black text-outline">
                                    <span id="visibleCount">{filtered.length}</span> proyek ditemukan
                                </span>
                                <button
                                    type="button"
                                    onClick={resetFilter}
                                    id="btnReset"
                                    className="flex items-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-black border border-outline-variant/20 text-outline hover:text-red-500 hover:border-red-200 transition-all"
                                >
                                    <span className="material-symbols-outlined text-sm">filter_alt_off</span> Reset
                                </button>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* TABLE */}
            <div className="bg-white rounded-[2rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-outline-variant/10 bg-surface-container-low/30">
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline w-10">#</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Proyek</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Manager</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline">Periode</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Mahasiswa</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Status</th>
                            <th className="px-7 py-4 text-[10px] font-black uppercase tracking-widest text-outline text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-outline-variant/10">
                        {pengajuan.length === 0 ? (
                            <tr>
                                <td colSpan={7} className="px-7 py-20 text-center">
                                    <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                                        rocket_launch
                                    </span>
                                    <p className="font-black italic text-on-surface-variant/40 text-sm">
                                        Belum ada pengajuan proyek.
                                    </p>
                                </td>
                            </tr>
                        ) : (
                            filtered.map((p, idx) => {
                                const statusColor =
                                    p.status === 'approved'
                                        ? 'bg-emerald-50 text-emerald-600 border-emerald-100'
                                        : p.status === 'rejected'
                                          ? 'bg-red-50 text-red-500 border-red-100'
                                          : 'bg-amber-50 text-amber-500 border-amber-100';
                                const statusIcon =
                                    p.status === 'approved'
                                        ? 'check_circle'
                                        : p.status === 'rejected'
                                          ? 'cancel'
                                          : 'pending';
                                const statusLabel =
                                    p.status === 'approved'
                                        ? 'Disetujui'
                                        : p.status === 'rejected'
                                          ? 'Ditolak'
                                          : 'Menunggu';

                                return (
                                    <tr
                                        key={p.id}
                                        className="proyek-row hover:bg-surface-container-lowest transition-colors group"
                                    >
                                        <td className="px-7 py-5 text-[10px] font-black text-outline/40 row-num">
                                            {idx + 1}
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shrink-0">
                                                    <span className="material-symbols-outlined text-on-primary text-base">
                                                        rocket_launch
                                                    </span>
                                                </div>
                                                <div>
                                                    <p className="font-black text-on-surface text-sm leading-none">
                                                        {p.judul_proyek}
                                                    </p>
                                                    <p className="text-[10px] text-outline font-mono mt-0.5 uppercase">
                                                        {p.kode_pengajuan}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-7 py-5">
                                            <p className="text-sm font-bold text-on-surface-variant">
                                                {p.manager?.name ?? '-'}
                                            </p>
                                        </td>
                                        <td className="px-7 py-5">
                                            <p className="text-xs font-bold text-on-surface-variant">
                                                {formatDate(p.tanggal_mulai)}
                                            </p>
                                            <p className="text-[10px] text-outline">
                                                s/d {formatDate(p.tanggal_selesai)}
                                            </p>
                                        </td>
                                        <td className="px-7 py-5 text-center">
                                            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-container text-primary rounded-xl text-[10px] font-black">
                                                <span className="material-symbols-outlined text-sm">group</span>
                                                {getTotalMahasiswa(p)}
                                            </span>
                                        </td>
                                        <td className="px-7 py-5 text-center">
                                            <span
                                                className={`inline-flex items-center gap-1.5 px-3 py-1.5 border rounded-xl text-[10px] font-black ${statusColor}`}
                                            >
                                                <span className="material-symbols-outlined text-sm">
                                                    {statusIcon}
                                                </span>
                                                {statusLabel}
                                            </span>
                                        </td>
                                        <td className="px-7 py-5">
                                            <div className="flex items-center justify-center gap-2">
                                                <Link
                                                    href={`/pengajuan-proyek/${p.id}`}
                                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-on-primary shadow-md hover:scale-105 transition-all"
                                                    style={{ background: '#004d4d' }}
                                                >
                                                    <span className="material-symbols-outlined text-sm">
                                                        visibility
                                                    </span>{' '}
                                                    Detail
                                                </Link>
                                                {isAdmin && (
                                                    <form onSubmit={(e) => handleDelete(e, p)}>
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
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>

                {pengajuan.length > 0 && filtered.length === 0 && (
                    <div id="emptyFilter" className="px-7 py-16 text-center">
                        <span className="material-symbols-outlined text-5xl text-outline-variant/40 block mb-3">
                            search_off
                        </span>
                        <p className="font-black italic text-on-surface-variant/40 text-sm">
                            Tidak ada proyek ditemukan.
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
