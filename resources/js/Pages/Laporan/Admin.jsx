import React, { useState, useMemo } from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const PRODI_KELAS_MAP = {
    informatika: 'AEC',
    mekatronika: 'AEA',
    otomasi: 'AEB',
};

export default function LaporanAdmin({
    mahasiswaProgress = [],
    proyekList = [],
    proyekId = null,
    filterProdi = null,
    filterKelas = null,
    prodiList = { informatika: 'Informatika', mekatronika: 'Mekatronika', otomasi: 'Otomasi' },
    kelasList = [],
    isKps = false,
    kpsProdi = null,
    totalMahasiswa = 0,
    sudahSelesai = 0,
    sedangBerjalan = 0,
    belumMulai = 0,
}) {
    const [searchMahasiswa, setSearchMahasiswa] = useState('');
    const [filterStatus, setFilterStatus] = useState('');

    const currentProdi = isKps ? kpsProdi || '' : filterProdi || '';
    const currentProyekId = proyekId || '';
    const currentKelas = filterKelas || '';

    const filteredKelasOptions = useMemo(() => {
        const prefix = PRODI_KELAS_MAP[currentProdi] || null;
        if (!prefix) return kelasList;
        return kelasList.filter((k) => String(k).includes(prefix));
    }, [kelasList, currentProdi]);

    const submitFilter = (nextParams) => {
        const params = {};
        if (nextParams.proyek_id) params.proyek_id = nextParams.proyek_id;
        if (nextParams.prodi) params.prodi = nextParams.prodi;
        if (nextParams.kelas) params.kelas = nextParams.kelas;
        router.get('/laporan-admin', params);
    };

    const filteredRows = useMemo(() => {
        const keyword = searchMahasiswa.toLowerCase().trim();
        const status = filterStatus.toLowerCase();
        return mahasiswaProgress.filter((m) => {
            const nama = (m.nama || '').toLowerCase();
            const nim = String(m.nim || '');
            const rowStatus = (m.status || '').toLowerCase();

            const matchSearch = !keyword || nama.includes(keyword) || nim.includes(keyword);
            const matchStatus = !status || rowStatus === status;
            return matchSearch && matchStatus;
        });
    }, [mahasiswaProgress, searchMahasiswa, filterStatus]);

    const hasClientFilter = searchMahasiswa.trim() !== '' || filterStatus !== '';

    return (
        <AppLayout title="Monitoring & Laporan">
            {/* HEADER */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                        Monitoring <span className="text-[#2dce89]">&amp; Laporan</span>
                    </h1>
                    <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                        Progress aktivitas logbook &amp; laporan mahasiswa
                    </p>
                </div>
                <a
                    href={`/laporan-admin?export=1${proyekId ? `&proyek_id=${proyekId}` : ''}`}
                    className="flex items-center gap-2 px-5 py-3 rounded-2xl text-[#7fffd4] text-xs font-black hover:opacity-80 transition-all shadow-xl shadow-teal-900/20"
                    style={{ background: '#004d4d' }}
                >
                    <span className="material-symbols-outlined text-base">download</span>
                    EXPORT CSV
                </a>
            </div>

            {/* STAT CARDS */}
            <div className="grid grid-cols-4 gap-5 mb-8">
                <div
                    className="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between shadow-xl h-32"
                    style={{ background: '#004d4d' }}
                >
                    <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest">
                        Total Mahasiswa
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-4xl font-black italic text-white tracking-tighter">
                            {totalMahasiswa}
                        </p>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/5"
                        style={{ fontSize: '90px' }}
                    >
                        group
                    </span>
                </div>

                <div
                    className="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between shadow-xl h-32"
                    style={{ background: '#2dce89' }}
                >
                    <p className="text-white/70 text-[10px] font-black uppercase tracking-widest">
                        Selesai
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-4xl font-black italic text-white tracking-tighter">
                            {sudahSelesai}
                        </p>
                        <span className="text-xs font-black text-white/60">Mahasiswa</span>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-white/20"
                        style={{ fontSize: '90px' }}
                    >
                        task_alt
                    </span>
                </div>

                <div className="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between h-32 bg-white border-2 border-outline-variant/20">
                    <p
                        className="text-[10px] font-black uppercase tracking-widest"
                        style={{ color: '#004d4d' }}
                    >
                        Berjalan
                    </p>
                    <div className="flex items-end justify-between">
                        <p
                            className="text-4xl font-black italic tracking-tighter"
                            style={{ color: '#004d4d' }}
                        >
                            {sedangBerjalan}
                        </p>
                        <span className="text-xs font-black text-gray-400">Mahasiswa</span>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-gray-100"
                        style={{ fontSize: '90px' }}
                    >
                        pending_actions
                    </span>
                </div>

                <div className="relative overflow-hidden rounded-[2rem] p-6 flex flex-col justify-between h-32 bg-white border-2 border-orange-100">
                    <p className="text-orange-400 text-[10px] font-black uppercase tracking-widest">
                        Belum Mulai
                    </p>
                    <div className="flex items-end justify-between">
                        <p className="text-4xl font-black italic text-orange-400 tracking-tighter">
                            {belumMulai}
                        </p>
                        <span className="text-xs font-black text-gray-400">Mahasiswa</span>
                    </div>
                    <span
                        className="material-symbols-outlined absolute -right-3 -bottom-3 text-orange-50"
                        style={{ fontSize: '90px' }}
                    >
                        hourglass_empty
                    </span>
                </div>
            </div>

            {/* FILTER BAR */}
            <form
                method="GET"
                action="/laporan-admin"
                id="formFilter"
                onSubmit={(e) => e.preventDefault()}
            >
                <div className="flex items-center gap-3 mb-6 flex-wrap">
                    {/* Filter Proyek */}
                    <select
                        name="proyek_id"
                        value={currentProyekId}
                        onChange={(e) =>
                            submitFilter({
                                proyek_id: e.target.value,
                                prodi: currentProdi,
                                kelas: currentKelas,
                            })
                        }
                        className="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all"
                    >
                        <option value="">Semua Proyek</option>
                        {proyekList.map((p) => (
                            <option key={p.id} value={p.id}>
                                {p.judul_proyek}
                            </option>
                        ))}
                    </select>

                    {/* Filter Prodi (Admin only; KPS dikunci otomatis) */}
                    {isKps ? (
                        <div className="px-5 py-3 rounded-2xl border border-teal-200 bg-teal-50 text-sm font-black text-[#004d4d]">
                            {prodiList[kpsProdi] ?? kpsProdi}
                            <input type="hidden" name="prodi" value={kpsProdi || ''} />
                        </div>
                    ) : (
                        <select
                            name="prodi"
                            value={currentProdi}
                            onChange={(e) =>
                                submitFilter({
                                    proyek_id: currentProyekId,
                                    prodi: e.target.value,
                                    kelas: '',
                                })
                            }
                            className="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all"
                        >
                            <option value="">Semua Prodi</option>
                            {Object.entries(prodiList).map(([key, label]) => (
                                <option key={key} value={key}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    )}

                    {/* Filter Kelas */}
                    <select
                        name="kelas"
                        value={currentKelas}
                        onChange={(e) =>
                            submitFilter({
                                proyek_id: currentProyekId,
                                prodi: currentProdi,
                                kelas: e.target.value,
                            })
                        }
                        className="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all"
                    >
                        <option value="">Semua Kelas</option>
                        {filteredKelasOptions.map((k) => (
                            <option key={k} value={k}>
                                {k}
                            </option>
                        ))}
                    </select>

                    {/* Search Nama / NIM (client-side) */}
                    <div className="relative">
                        <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">
                            search
                        </span>
                        <input
                            type="text"
                            id="searchMahasiswa"
                            value={searchMahasiswa}
                            onChange={(e) => setSearchMahasiswa(e.target.value)}
                            placeholder="Cari nama / NIM..."
                            className="pl-9 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 w-52 transition-all"
                        />
                    </div>

                    {/* Filter Status (client-side) */}
                    <select
                        id="filterStatus"
                        value={filterStatus}
                        onChange={(e) => setFilterStatus(e.target.value)}
                        className="px-5 py-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2dce89]/30 transition-all"
                    >
                        <option value="">Semua Status</option>
                        <option value="selesai">Selesai</option>
                        <option value="berjalan">Berjalan</option>
                        <option value="belum mulai">Belum Mulai</option>
                    </select>

                    {/* Reset filter */}
                    {(filterProdi || filterKelas || proyekId) && (
                        <Link
                            href="/laporan-admin"
                            className="px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs font-black text-gray-400 hover:text-red-400 hover:border-red-200 transition-all"
                        >
                            Reset
                        </Link>
                    )}

                    {/* Badge hasil filter */}
                    {hasClientFilter && (
                        <span id="resultCount" className="text-xs font-black text-gray-400 italic">
                            {filteredRows.length} hasil
                        </span>
                    )}
                </div>
            </form>

            {/* TABEL MONITORING */}
            <div className="bg-white rounded-[2rem] shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-gray-100 bg-gray-50/40">
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 w-10">
                                #
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Mahasiswa
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Proyek
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                Logbook
                                <br />
                                <span className="normal-case font-medium text-[9px]">Mingguan</span>
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                Harian
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                Supervisi
                                <br />
                                <span className="normal-case font-medium text-[9px]">Mingguan</span>
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Progress Logbook
                            </th>
                            <th className="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody id="monitoringTableBody" className="divide-y divide-gray-50">
                        {mahasiswaProgress.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-6 py-16 text-center">
                                    <span className="material-symbols-outlined text-5xl text-gray-200 block mb-3">
                                        monitoring
                                    </span>
                                    <p className="text-gray-400 font-black italic text-sm">
                                        Belum ada data aktivitas.
                                    </p>
                                </td>
                            </tr>
                        ) : (
                            mahasiswaProgress.map((m, i) => {
                                const keyword = searchMahasiswa.toLowerCase().trim();
                                const st = filterStatus.toLowerCase();
                                const matchSearch =
                                    !keyword ||
                                    (m.nama || '').toLowerCase().includes(keyword) ||
                                    String(m.nim || '').includes(keyword);
                                const matchStatus =
                                    !st || (m.status || '').toLowerCase() === st;

                                if (!matchSearch || !matchStatus) return null;

                                const statusColor =
                                    m.status === 'Selesai'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : m.status === 'Berjalan'
                                          ? 'bg-blue-100 text-blue-700'
                                          : m.status === 'Belum Mulai'
                                            ? 'bg-orange-100 text-orange-600'
                                            : 'bg-gray-100 text-gray-500';

                                const barColor =
                                    m.status === 'Selesai'
                                        ? '#2dce89'
                                        : m.status === 'Berjalan'
                                          ? '#004d4d'
                                          : '#e0e0e0';

                                return (
                                    <tr
                                        key={m.id}
                                        className="hover:bg-teal-50/20 transition-colors monitoring-row"
                                    >
                                        <td className="px-6 py-5 text-[10px] font-black text-gray-300">
                                            {i + 1}
                                        </td>

                                        {/* Mahasiswa */}
                                        <td className="px-6 py-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center font-black text-xs text-[#004d4d] shrink-0">
                                                    {(m.nama || '').substring(0, 2).toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="font-black text-gray-800 text-sm">
                                                        {m.nama}
                                                    </p>
                                                    <p className="text-[10px] text-gray-400 font-medium">
                                                        {m.nim} · {m.prodi}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {/* Proyek */}
                                        <td className="px-6 py-5">
                                            <p className="text-sm font-medium text-gray-600">{m.proyek}</p>
                                        </td>

                                        {/* Logbook Mingguan (/8) */}
                                        <td className="px-6 py-5 text-center">
                                            <span
                                                className="font-black text-sm"
                                                style={{ color: '#004d4d' }}
                                            >
                                                {m.logbook_verified}
                                            </span>
                                            <span className="text-gray-300 text-xs font-bold"> / 8</span>
                                            <p className="text-[9px] text-gray-400 mt-0.5">verified</p>
                                        </td>

                                        {/* Harian */}
                                        <td className="px-6 py-5 text-center">
                                            <span className="font-black text-sm text-gray-600">
                                                {m.total_harian}
                                            </span>
                                            <p className="text-[9px] text-gray-400 mt-0.5">entri</p>
                                        </td>

                                        {/* Supervisi Mingguan (/2) */}
                                        <td className="px-6 py-5 text-center">
                                            <span
                                                className="font-black text-sm"
                                                style={{ color: '#2dce89' }}
                                            >
                                                {m.laporan_verified}
                                            </span>
                                            <span className="text-gray-300 text-xs font-bold"> / 2</span>
                                            <p className="text-[9px] text-gray-400 mt-0.5">verified</p>
                                        </td>

                                        {/* Progress Bar */}
                                        <td className="px-6 py-5">
                                            <div className="flex items-center gap-2">
                                                <div className="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                                                    <div
                                                        className="h-full rounded-full transition-all"
                                                        style={{
                                                            width: `${m.progress_logbook}%`,
                                                            background: barColor,
                                                        }}
                                                    />
                                                </div>
                                                <span className="text-[10px] font-black text-gray-400 w-8 text-right">
                                                    {m.progress_logbook}%
                                                </span>
                                            </div>
                                        </td>

                                        {/* Status */}
                                        <td className="px-6 py-5 text-center">
                                            <span
                                                className={`inline-block px-3 py-1.5 rounded-xl text-[10px] font-black ${statusColor}`}
                                            >
                                                {m.status}
                                            </span>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>

                {/* Empty state filter */}
                {mahasiswaProgress.length > 0 && filteredRows.length === 0 && (
                    <div id="noResultMonitoring" className="px-6 py-16 text-center">
                        <span className="material-symbols-outlined text-5xl text-gray-200 block mb-3">
                            search_off
                        </span>
                        <p className="text-gray-400 font-black italic text-sm uppercase">
                            Tidak ada hasil yang cocok.
                        </p>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
