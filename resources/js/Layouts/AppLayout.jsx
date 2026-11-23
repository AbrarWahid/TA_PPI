import React, { useState, useRef, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';

export default function AppLayout({ title = 'Dashboard', children }) {
    const { auth, sidebarCounts, navigation, flash } = usePage().props;
    const user = auth?.user;

    const path = navigation?.path || 'dashboard';
    const routeName = navigation?.route_name || '';

    const [mobileOpen, setMobileOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    const [notifications, setNotifications] = useState(user?.notifications || []);
    const [unreadCount, setUnreadCount] = useState(user?.unread_count || 0);
    const notifRef = useRef(null);

    const isAdmin = user?.role === 'admin';
    const isDosen = user?.role === 'dosen';
    const isManager = user?.role === 'manager_proyek';
    const isMentor = user?.role === 'mentor';
    const roleAktif = user?.role_aktif || 'dosen_pengampu';
    const isDosenAsManager = isDosen && roleAktif === 'manager_proyek';
    const isDosenAsDosen = isDosen && roleAktif === 'dosen_pengampu';
    const isDosenAsKps = isDosen && roleAktif === 'kps';
    const aksesRoleDosen = user?.akses_role || 'keduanya';

    const masterActive =
        path.startsWith('mahasiswa') ||
        path.startsWith('dosen') ||
        path.startsWith('admin/prodi') ||
        path.startsWith('admin/matkul') ||
        path.startsWith('kelas') ||
        path.startsWith('admin/kps');

    const kurikulumActive = path.startsWith('rubrik') || path.startsWith('bobot');
    const masterKpsActive =
        path.startsWith('mahasiswa') ||
        path.startsWith('admin/matkul') ||
        path.startsWith('kelas');

    const lbInternalAktif = path.startsWith('logbook');
    const lbEksternalAktif = routeName === 'external.mahasiswa.dashboard';
    const nInternalAktif = path.startsWith('nilai-saya');
    const nEksternalAktif = routeName?.startsWith('external.mahasiswa.nilai');

    const [openMaster, setOpenMaster] = useState(masterActive);
    const [openKurikulum, setOpenKurikulum] = useState(kurikulumActive);
    const [openMasterKps, setOpenMasterKps] = useState(masterKpsActive);
    const [openMhsLogbook, setOpenMhsLogbook] = useState(lbInternalAktif || lbEksternalAktif);
    const [openMhsNilai, setOpenMhsNilai] = useState(nInternalAktif || nEksternalAktif);

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (notifRef.current && !notifRef.current.contains(e.target)) {
                setNotifOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const navActive = 'bg-[#013981] text-white font-semibold shadow-sm';
    const navIdle = 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium';
    const subActive = 'text-[#013981] font-semibold bg-[#eef4fc]';
    const subIdle = 'text-slate-500 hover:text-slate-900 hover:bg-slate-100 font-medium';

    const switchRole = (targetRole) => {
        router.post('/pilih-role', { role_aktif: targetRole });
    };

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const handleNotifClick = async (notif) => {
        try {
            await fetch(`/external/notifikasi/${notif.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            });
            setNotifications((prev) =>
                prev.map((n) => (n.id === notif.id ? { ...n, read_at: new Date().toISOString() } : n)),
            );
            setUnreadCount((c) => Math.max(0, c - 1));
        } catch (err) {
            console.error(err);
        }
        if (notif.url && notif.url !== '#') {
            window.location.href = notif.url;
        }
    };

    const handleDeleteNotif = async (e, notifId) => {
        e.stopPropagation();
        if (!window.confirm('Hapus notifikasi ini? (Data approval riwayat Anda akan tetap tersimpan)')) {
            return;
        }
        try {
            const res = await fetch(`/external/notifikasi/hapus/${notifId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            });
            if (res.ok) {
                setNotifications((prev) => prev.filter((n) => n.id !== notifId));
            }
        } catch (err) {
            console.error(err);
        }
    };

    const getExternalMenu = () => {
        if (user?.role === 'admin') {
            return [
                ['/external/admin/dashboard', 'external.admin.dashboard', 'Pusat Kendali', 'apartment'],
                ['/external/admin/users', 'external.admin.users', 'Kelola Akun', 'manage_accounts'],
            ];
        }
        if (user?.role === 'dosen') {
            const items = [
                ['/external/dosen/dashboard', 'external.dosen.dashboard', 'Dashboard TTD', 'apartment'],
                ['/external/dosen/riwayat-ttd', 'external.dosen.riwayat_ttd', 'Riwayat TTD', 'history'],
            ];
            if (user?.role_aktif_kps) {
                items.push(
                    ['/external/kaprodi/dashboard', 'external.kaprodi.dashboard', 'Pusat Kendali KPS', 'tune'],
                    ['/external/kaprodi/parameter', 'external.kaprodi.parameter', 'Manajemen Formulir', 'dynamic_form'],
                    ['/external/kaprodi/export', 'external.kaprodi.export', 'Ekspor Data', 'download'],
                );
            }
            return items;
        }
        if (user?.role === 'mentor') {
            return [
                ['/external/mentor/dashboard', 'external.mentor.dashboard', 'Dashboard Mentor', 'apartment'],
                ['/external/mentor/riwayat-ttd', 'external.mentor.riwayat_ttd', 'Riwayat TTD', 'history'],
            ];
        }
        return [];
    };

    const externalMenuItems = getExternalMenu();
    const initials = (user?.name || 'U').substring(0, 2).toUpperCase();
    const firstInitial = (user?.name || 'U').substring(0, 1).toUpperCase();

    return (
        <>
            <Head title={title} />
            <div className="bg-[#fafafa] font-sans text-[#0b0b0c] antialiased h-screen flex overflow-hidden">
                {/* MOBILE OVERLAY */}
                {mobileOpen && (
                    <div
                        onClick={() => setMobileOpen(false)}
                        className="fixed inset-0 bg-black/25 backdrop-blur-[1px] z-30 lg:hidden"
                    />
                )}

                {/* SIDEBAR */}
                <aside
                    className={`${
                        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
                    } fixed lg:static inset-y-0 left-0 w-[264px] bg-white border-r border-[#e4e4e7] flex flex-col z-40 flex-shrink-0 h-screen transition-transform duration-200 ease-out`}
                >
                    {/* Brand Header */}
                    <div className="h-16 px-5 flex items-center justify-between border-b border-[#e4e4e7] flex-shrink-0">
                        <Link href="/dashboard" className="flex items-center gap-2.5 group">
                            <div className="w-8 h-8 rounded-lg bg-[#013981] border border-[#013981] flex items-center justify-center overflow-hidden flex-shrink-0">
                                <img
                                    src="/images/logo.png"
                                    alt="AE Polman"
                                    className="w-5 h-5 object-contain"
                                    onError={(e) => {
                                        e.currentTarget.style.display = 'none';
                                    }}
                                />
                            </div>
                            <div className="leading-none">
                                <span className="text-base font-bold tracking-tight text-slate-900 block">
                                    PBL Portal
                                </span>
                                <span className="font-mono text-[10px] text-[#6a6f7c] tracking-wider uppercase block mt-0.5">
                                    AE Polman
                                </span>
                            </div>
                        </Link>
                        <span className="font-mono text-[10px] text-[#6a6f7c] bg-[#f4f4f5] border border-[#e4e4e7] px-1.5 py-0.5 rounded">
                            v2.4
                        </span>
                    </div>

                    {/* Navigation */}
                    <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                        {/* ADMIN */}
                        {isAdmin && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Main Menu
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>

                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />

                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Manajemen
                                </p>

                                <Link
                                    href="/approval-dosen"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('approval-dosen') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">how_to_reg</span>
                                    <div className="flex items-center gap-2 flex-1">
                                        <span>Approval Akun</span>
                                        {sidebarCounts?.pending_akun > 0 && (
                                            <span className="ml-auto font-mono text-[10px] px-1.5 py-0.5 rounded bg-amber-50 border border-amber-200 text-[#b45309] font-medium">
                                                {sidebarCounts.pending_akun}
                                            </span>
                                        )}
                                    </div>
                                </Link>

                                <Link
                                    href="/pengajuan-proyek"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('pengajuan-proyek') || path.startsWith('proyek-pbl')
                                            ? navActive
                                            : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">verified</span>
                                    <div className="flex items-center gap-2 flex-1">
                                        <span>Approval Proyek</span>
                                        {sidebarCounts?.pending_proyek > 0 && (
                                            <span className="ml-auto font-mono text-[10px] px-1.5 py-0.5 rounded bg-[#f4f4f5] border border-[#e4e4e7] text-[#0b0b0c] font-medium">
                                                {sidebarCounts.pending_proyek}
                                            </span>
                                        )}
                                    </div>
                                </Link>

                                <Link
                                    href="/admin/periode-pbl"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('admin/periode-pbl') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">calendar_month</span>
                                    <span className="flex-1">Periode PBL</span>
                                </Link>

                                <div>
                                    <button
                                        type="button"
                                        onClick={() => setOpenMaster(!openMaster)}
                                        className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            masterActive
                                                ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]'
                                                : navIdle
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="material-symbols-outlined text-[20px]">database</span>
                                            <span>Master Data</span>
                                        </div>
                                        <span
                                            className={`material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150 ${
                                                openMaster ? 'rotate-90' : ''
                                            }`}
                                        >
                                            chevron_right
                                        </span>
                                    </button>
                                    {openMaster && (
                                        <div className="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                                            <Link
                                                href="/mahasiswa"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('mahasiswa') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Mahasiswa</span>
                                            </Link>
                                            <Link
                                                href="/dosen"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('dosen') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Dosen</span>
                                            </Link>
                                            <Link
                                                href="/admin/prodi"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('admin/prodi') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Program Studi</span>
                                            </Link>
                                            <Link
                                                href="/admin/matkul"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('admin/matkul') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Mata Kuliah</span>
                                            </Link>
                                            <Link
                                                href="/kelas"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('kelas') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Kelas</span>
                                            </Link>
                                            <Link
                                                href="/admin/kps"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('admin/kps') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Manajemen KPS</span>
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />

                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PBL (Internal)
                                </p>

                                <div>
                                    <button
                                        type="button"
                                        onClick={() => setOpenKurikulum(!openKurikulum)}
                                        className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            kurikulumActive
                                                ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]'
                                                : navIdle
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="material-symbols-outlined text-[20px]">menu_book</span>
                                            <span>Kurikulum PBL</span>
                                        </div>
                                        <span
                                            className={`material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150 ${
                                                openKurikulum ? 'rotate-90' : ''
                                            }`}
                                        >
                                            chevron_right
                                        </span>
                                    </button>
                                    {openKurikulum && (
                                        <div className="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                                            <Link
                                                href="/rubrik-penilaian"
                                                className={`block px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('rubrik') ? subActive : subIdle
                                                }`}
                                            >
                                                Rubrik Penilaian
                                            </Link>
                                            <Link
                                                href="/bobot-penilaian"
                                                className={`block px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('bobot') ? subActive : subIdle
                                                }`}
                                            >
                                                Bobot Penilaian
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                <Link
                                    href="/admin/supervisi"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('admin/supervisi') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">
                                        supervisor_account
                                    </span>
                                    <span>Supervisi Matkul</span>
                                </Link>

                                <Link
                                    href="/laporan-admin"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('laporan-admin') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">monitoring</span>
                                    <span>Monitoring &amp; Laporan</span>
                                </Link>

                                <Link
                                    href="/penilaian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('penilaian') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">grade</span>
                                    <span>Asesmen &amp; Nilai</span>
                                </Link>
                            </>
                        )}

                        {/* DOSEN AS MANAGER PROYEK */}
                        {isDosenAsManager && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PBL (Internal)
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>
                                <Link
                                    href="/penilaian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('penilaian') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">grade</span>
                                    <span>Penilaian</span>
                                </Link>
                                <Link
                                    href="/pengajuan-proyek"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('pengajuan') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">rocket_launch</span>
                                    <span>Pengajuan Proyek</span>
                                </Link>
                                <Link
                                    href="/logbook-mingguan/verifikasi"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('logbook-mingguan') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">rate_review</span>
                                    <span>Verifikasi Logbook</span>
                                </Link>
                                {aksesRoleDosen === 'keduanya' && (
                                    <>
                                        <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />
                                        <button
                                            type="button"
                                            onClick={() => switchRole('dosen_pengampu')}
                                            className="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors"
                                        >
                                            <span className="material-symbols-outlined text-[20px]">swap_horiz</span>
                                            <span>Ganti ke Dosen Pengampu</span>
                                        </button>
                                    </>
                                )}
                            </>
                        )}

                        {/* DOSEN AS KPS */}
                        {isDosenAsKps && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Main Menu
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>

                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />

                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Master Data
                                </p>
                                <div>
                                    <button
                                        type="button"
                                        onClick={() => setOpenMasterKps(!openMasterKps)}
                                        className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            masterKpsActive
                                                ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]'
                                                : navIdle
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="material-symbols-outlined text-[20px]">database</span>
                                            <span>Master Data</span>
                                        </div>
                                        <span
                                            className={`material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150 ${
                                                openMasterKps ? 'rotate-90' : ''
                                            }`}
                                        >
                                            chevron_right
                                        </span>
                                    </button>
                                    {openMasterKps && (
                                        <div className="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                                            <Link
                                                href="/mahasiswa"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('mahasiswa') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Mahasiswa</span>
                                            </Link>
                                            <Link
                                                href="/admin/matkul"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('admin/matkul') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Mata Kuliah</span>
                                            </Link>
                                            <Link
                                                href="/kelas"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    path.startsWith('kelas') ? subActive : subIdle
                                                }`}
                                            >
                                                <span>Data Kelas</span>
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />

                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PBL (Internal)
                                </p>
                                <Link
                                    href="/admin/supervisi"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('admin/supervisi') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">
                                        supervisor_account
                                    </span>
                                    <span>Supervisi Matkul</span>
                                </Link>
                                <Link
                                    href="/laporan-admin"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('laporan-admin') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">monitoring</span>
                                    <span>Monitoring &amp; Laporan</span>
                                </Link>
                                <Link
                                    href="/penilaian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('penilaian') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">grade</span>
                                    <span>Asesmen &amp; Nilai</span>
                                </Link>

                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />

                                {(user?.available_roles || [])
                                    .filter((r) => r !== 'kps')
                                    .map((r) => (
                                        <button
                                            key={r}
                                            type="button"
                                            onClick={() => switchRole(r)}
                                            className="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors"
                                        >
                                            <span className="material-symbols-outlined text-[20px]">swap_horiz</span>
                                            <span>
                                                Ganti ke{' '}
                                                {r === 'dosen_pengampu' ? 'Dosen Pengampu' : 'Manager Proyek'}
                                            </span>
                                        </button>
                                    ))}
                            </>
                        )}

                        {/* DOSEN AS DOSEN PENGAMPU */}
                        {isDosenAsDosen && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PBL (Internal)
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>
                                <Link
                                    href="/penilaian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('penilaian') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">grade</span>
                                    <span>Penilaian</span>
                                </Link>
                                <Link
                                    href="/supervisi-mingguan/verifikasi/daftar"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('supervisi-mingguan') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">
                                        assignment_turned_in
                                    </span>
                                    <span>Verifikasi Supervisi</span>
                                </Link>
                                {aksesRoleDosen === 'keduanya' && (
                                    <>
                                        <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />
                                        <button
                                            type="button"
                                            onClick={() => switchRole('manager_proyek')}
                                            className="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#6a6f7c] hover:bg-[#f4f4f5] hover:text-[#0b0b0c] transition-colors"
                                        >
                                            <span className="material-symbols-outlined text-[20px]">swap_horiz</span>
                                            <span>Ganti ke Manager Proyek</span>
                                        </button>
                                    </>
                                )}
                            </>
                        )}

                        {/* MANAGER PROYEK PERMANEN */}
                        {isManager && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PBL (Internal)
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>
                                <Link
                                    href="/penilaian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('penilaian') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">grade</span>
                                    <span>Penilaian</span>
                                </Link>
                                <Link
                                    href="/pengajuan-proyek"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('pengajuan') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">rocket_launch</span>
                                    <span>Pengajuan Proyek</span>
                                </Link>
                                <Link
                                    href="/logbook-mingguan/verifikasi"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('logbook-mingguan') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">rate_review</span>
                                    <span>Verifikasi Logbook</span>
                                </Link>
                            </>
                        )}

                        {/* MENTOR INDUSTRI */}
                        {isMentor && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Menu Mentor
                                </p>
                                {externalMenuItems.map(([extUrl, extRoute, extLabel, extIcon]) => (
                                    <Link
                                        key={extRoute}
                                        href={extUrl}
                                        className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            routeName?.startsWith(extRoute) ? navActive : navIdle
                                        }`}
                                    >
                                        <span className="material-symbols-outlined text-[20px]">{extIcon}</span>
                                        <span>{extLabel}</span>
                                    </Link>
                                ))}
                            </>
                        )}

                        {/* MAHASISWA */}
                        {!isAdmin && !isDosen && !isManager && !isMentor && (
                            <>
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    Menu PPI
                                </p>
                                <Link
                                    href="/dashboard"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path === 'dashboard' ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">dashboard</span>
                                    <span>Dashboard</span>
                                </Link>

                                {/* Logbook Magang Dropdown */}
                                <div>
                                    <button
                                        type="button"
                                        onClick={() => setOpenMhsLogbook(!openMhsLogbook)}
                                        className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            lbInternalAktif || lbEksternalAktif
                                                ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]'
                                                : navIdle
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="material-symbols-outlined text-[20px]">menu_book</span>
                                            <span>Logbook Magang</span>
                                        </div>
                                        <span
                                            className={`material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150 ${
                                                openMhsLogbook ? 'rotate-90' : ''
                                            }`}
                                        >
                                            chevron_right
                                        </span>
                                    </button>
                                    {openMhsLogbook && (
                                        <div className="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                                            <Link
                                                href="/logbook-harian"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    lbInternalAktif ? subActive : subIdle
                                                }`}
                                            >
                                                <span className="material-symbols-outlined text-[15px]">school</span>
                                                <span>Magang Dalam Kampus</span>
                                            </Link>
                                            <Link
                                                href="/external/mahasiswa/dashboard"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    lbEksternalAktif ? subActive : subIdle
                                                }`}
                                            >
                                                <span className="material-symbols-outlined text-[15px]">apartment</span>
                                                <span>Magang Luar Kampus (Industri)</span>
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                {/* Nilai & Transkrip Dropdown */}
                                <div>
                                    <button
                                        type="button"
                                        onClick={() => setOpenMhsNilai(!openMhsNilai)}
                                        className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            nInternalAktif || nEksternalAktif
                                                ? 'text-[#0b0b0c] font-medium bg-[#f4f4f5]'
                                                : navIdle
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="material-symbols-outlined text-[20px]">
                                                workspace_premium
                                            </span>
                                            <span>Nilai &amp; Transkrip</span>
                                        </div>
                                        <span
                                            className={`material-symbols-outlined text-[16px] text-[#6a6f7c] transition-transform duration-150 ${
                                                openMhsNilai ? 'rotate-90' : ''
                                            }`}
                                        >
                                            chevron_right
                                        </span>
                                    </button>
                                    {openMhsNilai && (
                                        <div className="ml-5 mt-1 space-y-0.5 border-l border-[#e4e4e7] pl-3">
                                            <Link
                                                href="/nilai-saya"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    nInternalAktif ? subActive : subIdle
                                                }`}
                                            >
                                                <span className="material-symbols-outlined text-[15px]">school</span>
                                                <span>Magang Dalam Kampus</span>
                                            </Link>
                                            <Link
                                                href="/external/mahasiswa/nilai"
                                                className={`flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] transition-colors ${
                                                    nEksternalAktif ? subActive : subIdle
                                                }`}
                                            >
                                                <span className="material-symbols-outlined text-[15px]">apartment</span>
                                                <span>Magang Luar Kampus (Industri)</span>
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                <Link
                                    href="/supervisi-harian"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('supervisi') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">fact_check</span>
                                    <span>Supervisi</span>
                                </Link>
                                <Link
                                    href="/feedback-hub"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('feedback') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">forum</span>
                                    <span>Feedback Hub</span>
                                </Link>
                                <Link
                                    href="/profile"
                                    className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                        path.startsWith('profile') ? navActive : navIdle
                                    }`}
                                >
                                    <span className="material-symbols-outlined text-[20px]">settings</span>
                                    <span>Pengaturan Akun</span>
                                </Link>
                            </>
                        )}

                        {/* MODUL EXTERNAL (Admin & Dosen) */}
                        {(isAdmin || isDosen) && (
                            <>
                                <div className="h-px bg-[#e4e4e7] my-3.5 mx-2" />
                                <p className="px-3 font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2">
                                    PPI Industri (Eksternal)
                                </p>
                                {externalMenuItems.map(([extUrl, extRoute, extLabel, extIcon]) => (
                                    <Link
                                        key={extRoute}
                                        href={extUrl}
                                        className={`w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors ${
                                            routeName?.startsWith(extRoute) ? navActive : navIdle
                                        }`}
                                    >
                                        <span className="material-symbols-outlined text-[20px]">{extIcon}</span>
                                        <span>{extLabel}</span>
                                    </Link>
                                ))}
                            </>
                        )}
                    </nav>

                    {/* User Footer */}
                    <div className="p-3 border-t border-[#e4e4e7] bg-[#fafafa]">
                        <div className="flex items-center gap-2.5 px-2.5 py-2 rounded-lg bg-white border border-[#e4e4e7]">
                            <div className="w-8 h-8 rounded-md bg-[#013981] flex items-center justify-center font-mono font-medium text-white text-xs flex-shrink-0">
                                {initials}
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-[13px] font-bold text-slate-900 truncate leading-tight">
                                    {user?.name}
                                </p>
                                <p className="text-[11px] text-slate-500 font-semibold uppercase tracking-wide truncate mt-0.5">
                                    {user?.role_label}
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={handleLogout}
                                className="p-1.5 rounded-md text-[#6a6f7c] hover:text-[#b91c1c] hover:bg-red-50 transition-colors"
                                title="Logout"
                            >
                                <span className="material-symbols-outlined text-[20px]">logout</span>
                            </button>
                        </div>
                    </div>
                </aside>

                {/* MAIN AREA */}
                <div className="flex-1 flex flex-col h-screen overflow-hidden min-w-0">
                    {/* TOPBAR */}
                    <header className="bg-white/90 backdrop-blur-md border-b border-[#e4e4e7] px-5 lg:px-8 h-16 flex items-center justify-between flex-shrink-0 z-10">
                        <div className="flex items-center gap-3 min-w-0">
                            <button
                                type="button"
                                onClick={() => setMobileOpen(true)}
                                className="lg:hidden p-1.5 -ml-1.5 rounded-lg text-[#52525b] hover:text-[#0b0b0c] hover:bg-[#f4f4f5] transition-colors"
                            >
                                <span className="material-symbols-outlined text-[20px]">menu</span>
                            </button>
                            <div className="flex items-center gap-2 text-sm min-w-0">
                                <span className="text-xs font-semibold text-slate-400 hidden sm:inline">
                                    AE Polman Bandung
                                </span>
                                <span className="text-[#d4d4d8] hidden sm:inline">/</span>
                                <span className="font-bold text-slate-900 truncate">{title}</span>
                            </div>
                        </div>

                        <div className="flex items-center gap-2.5">
                            {flash?.success && (
                                <div className="hidden md:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-[#15803d] rounded-lg text-xs font-medium">
                                    <span className="material-symbols-outlined text-[15px]">check_circle</span>
                                    <span>{flash.success}</span>
                                </div>
                            )}
                            {flash?.error && (
                                <div className="hidden md:flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 text-[#b91c1c] rounded-lg text-xs font-medium">
                                    <span className="material-symbols-outlined text-[15px]">error</span>
                                    <span>{flash.error}</span>
                                </div>
                            )}

                            <div className="relative hidden md:block">
                                <span className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#a1a1aa] text-[16px]">
                                    search
                                </span>
                                <input
                                    type="text"
                                    placeholder="Cari mahasiswa, proyek..."
                                    className="pl-8 pr-3.5 py-1.5 bg-[#fafafa] border border-[#e4e4e7] rounded-lg focus:border-[#013981] focus:bg-white outline-none text-sm w-60 text-[#0b0b0c] placeholder:text-[#a1a1aa] transition-colors"
                                />
                            </div>

                            {isDosen || isMentor ? (
                                <div className="relative" ref={notifRef}>
                                    <button
                                        type="button"
                                        onClick={() => setNotifOpen(!notifOpen)}
                                        className="relative w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c] focus:outline-none"
                                    >
                                        <span className="material-symbols-outlined text-[18px]">notifications</span>
                                        {unreadCount > 0 && (
                                            <span className="absolute top-1.5 right-1.5 flex h-2 w-2">
                                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75" />
                                                <span className="relative inline-flex rounded-full h-2 w-2 bg-[#b91c1c]" />
                                            </span>
                                        )}
                                    </button>

                                    {notifOpen && (
                                        <div className="absolute right-0 mt-2 w-[320px] sm:w-[360px] bg-white flex flex-col border border-[#e4e4e7] rounded-xl shadow-surface-lg overflow-hidden z-50">
                                            <div className="bg-[#fafafa] border-b border-[#e4e4e7] px-4 py-3 flex justify-between items-center">
                                                <h3 className="font-semibold text-xs text-[#0b0b0c] m-0">
                                                    Notifikasi TTD
                                                </h3>
                                                {unreadCount > 0 && (
                                                    <span className="font-mono bg-[#013981] text-white text-[10px] font-medium px-2 py-0.5 rounded-full">
                                                        {unreadCount} Baru
                                                    </span>
                                                )}
                                            </div>
                                            <div className="max-h-[340px] overflow-y-auto w-full divide-y divide-[#f4f4f5]">
                                                {notifications.length === 0 ? (
                                                    <div className="px-4 py-8 text-center">
                                                        <span className="material-symbols-outlined text-2xl text-[#d4d4d8] block mb-1.5">
                                                            notifications_off
                                                        </span>
                                                        <p className="text-xs text-[#6a6f7c]">
                                                            Tidak ada notifikasi baru.
                                                        </p>
                                                    </div>
                                                ) : (
                                                    notifications.map((n) => {
                                                        const isUnread = !n.read_at;
                                                        return (
                                                            <div
                                                                key={n.id}
                                                                className={`relative group transition-colors duration-150 ${
                                                                    isUnread
                                                                        ? 'bg-[#fafafa] hover:bg-[#f4f4f5]'
                                                                        : 'bg-white hover:bg-[#fafafa]'
                                                                }`}
                                                            >
                                                                <div
                                                                    onClick={() => handleNotifClick(n)}
                                                                    className="flex items-start px-4 py-3.5 w-full pr-10 cursor-pointer"
                                                                >
                                                                    <div
                                                                        className={`flex-shrink-0 rounded-lg w-8 h-8 flex items-center justify-center mr-3 mt-0.5 border ${
                                                                            isUnread
                                                                                ? 'bg-[#013981] text-white border-[#013981]'
                                                                                : 'bg-[#f4f4f5] text-[#6a6f7c] border-[#e4e4e7]'
                                                                        }`}
                                                                    >
                                                                        <span className="material-symbols-outlined text-[16px]">
                                                                            description
                                                                        </span>
                                                                    </div>
                                                                    <div className="flex-1 min-w-0">
                                                                        <p
                                                                            className={`text-xs font-semibold truncate ${
                                                                                isUnread
                                                                                    ? 'text-[#0b0b0c]'
                                                                                    : 'text-[#6a6f7c]'
                                                                            }`}
                                                                        >
                                                                            {n.title}
                                                                        </p>
                                                                        <p
                                                                            className={`text-xs mt-0.5 whitespace-normal leading-relaxed ${
                                                                                isUnread
                                                                                    ? 'text-[#52525b]'
                                                                                    : 'text-[#a1a1aa]'
                                                                            }`}
                                                                        >
                                                                            {n.message}
                                                                        </p>
                                                                        <p className="font-mono text-[10px] mt-1.5 text-[#6a6f7c]">
                                                                            {n.time_human}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                {n.status !== 'pending' && (
                                                                    <button
                                                                        type="button"
                                                                        onClick={(e) => handleDeleteNotif(e, n.id)}
                                                                        className="absolute top-3.5 right-2.5 opacity-0 group-hover:opacity-100 transition-opacity duration-150 text-[#6a6f7c] hover:text-[#b91c1c] bg-white border border-[#e4e4e7] hover:bg-red-50 rounded-md p-1"
                                                                    >
                                                                        <span className="material-symbols-outlined text-[14px]">
                                                                            close
                                                                        </span>
                                                                    </button>
                                                                )}
                                                            </div>
                                                        );
                                                    })
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <button
                                    type="button"
                                    className="w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c] relative"
                                >
                                    <span className="material-symbols-outlined text-[20px]">notifications</span>
                                    <span className="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-[#15803d] rounded-full" />
                                </button>
                            )}

                            <Link
                                href="/profile"
                                className="w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c]"
                                title="Pengaturan Akun"
                            >
                                <span className="material-symbols-outlined text-[20px]">settings</span>
                            </Link>

                            <div className="w-px h-5 bg-[#e4e4e7] mx-0.5 hidden sm:block" />

                            <div className="hidden sm:flex items-center gap-2.5">
                                <div className="text-right">
                                    <p className="text-sm font-bold text-slate-900 leading-tight">{user?.name}</p>
                                    <p className="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">
                                        {user?.role_label}
                                    </p>
                                </div>
                                <div className="w-8 h-8 rounded-lg bg-[#013981] flex items-center justify-center text-white font-mono font-medium text-xs flex-shrink-0">
                                    {firstInitial}
                                </div>
                            </div>
                        </div>
                    </header>

                    {/* PAGE CONTENT */}
                    <main className="app-main flex-1 overflow-y-auto p-6 lg:p-8 bg-[#fafafa]">
                        {flash?.success && (
                            <div className="md:hidden mb-4 flex items-center gap-2 px-3.5 py-2.5 bg-emerald-50 border border-emerald-200 text-[#15803d] rounded-lg text-xs font-medium">
                                <span className="material-symbols-outlined text-[16px]">check_circle</span>
                                <span>{flash.success}</span>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="md:hidden mb-4 flex items-center gap-2 px-3.5 py-2.5 bg-red-50 border border-red-200 text-[#b91c1c] rounded-lg text-xs font-medium">
                                <span className="material-symbols-outlined text-[16px]">error</span>
                                <span>{flash.error}</span>
                            </div>
                        )}

                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}
