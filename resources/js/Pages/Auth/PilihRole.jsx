import React from 'react';
import { Head, router, usePage } from '@inertiajs/react';

export default function PilihRole() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const availableRoles = user?.available_roles || [];

    const bisaManager = availableRoles.includes('manager_proyek');
    const bisaDosen = availableRoles.includes('dosen_pengampu');
    const bisaKps = availableRoles.includes('kps');
    const totalRole = availableRoles.length;
    const prodiKps = user?.prodi_kps;

    const selectRole = (roleAktif) => {
        router.post('/pilih-role', { role_aktif: roleAktif });
    };

    const handleLogout = () => {
        router.post('/logout');
    };

    const gridColsClass =
        totalRole === 1
            ? 'grid-cols-1 max-w-xs mx-auto'
            : totalRole === 2
              ? 'grid-cols-1 sm:grid-cols-2'
              : 'grid-cols-1 sm:grid-cols-3';

    return (
        <>
            <Head title="Pilih Mode Peran — PBL Portal" />
            <div className="min-h-screen bg-[#fafafa] font-sans text-[#0b0b0c] antialiased flex items-center justify-center p-6">
                <div className="w-full max-w-2xl">
                    <div className="text-center mb-8">
                        <div className="w-10 h-10 rounded-lg bg-[#013981] flex items-center justify-center mx-auto mb-4 overflow-hidden">
                            <img
                                src="/images/logo.png"
                                alt="Logo"
                                className="w-6 h-6 object-contain"
                                onError={(e) => {
                                    e.currentTarget.style.display = 'none';
                                }}
                            />
                        </div>
                        <p className="font-mono text-[11px] uppercase tracking-[0.1em] text-[#6a6f7c] mb-1.5">
                            SESI MULTI-PERAN
                        </p>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Pilih mode kerja aktif
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1.5">
                            Halo, <span className="font-medium text-[#0b0b0c]">{user?.name}</span>. Pilih peran untuk
                            masuk ke dashboard.
                        </p>
                    </div>

                    <div className={`grid gap-3.5 mb-6 ${gridColsClass}`}>
                        {bisaManager && (
                            <button
                                type="button"
                                onClick={() => selectRole('manager_proyek')}
                                className="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full cursor-pointer"
                            >
                                <div>
                                    <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                                        <span className="material-symbols-outlined text-[20px]">rocket_launch</span>
                                    </div>
                                    <p className="font-semibold text-[#0b0b0c] text-base">Manager Proyek</p>
                                    <p className="text-xs text-[#6a6f7c] mt-1 leading-relaxed">
                                        Kelola pengajuan proyek PBL &amp; verifikasi logbook mingguan.
                                    </p>
                                </div>
                                <div className="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                                    <span className="font-mono text-[10px] text-[#6a6f7c] uppercase">Bobot 55%</span>
                                    <span className="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">
                                        arrow_forward
                                    </span>
                                </div>
                            </button>
                        )}

                        {bisaDosen && (
                            <button
                                type="button"
                                onClick={() => selectRole('dosen_pengampu')}
                                className="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full cursor-pointer"
                            >
                                <div>
                                    <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                                        <span className="material-symbols-outlined text-[20px]">school</span>
                                    </div>
                                    <p className="font-semibold text-[#0b0b0c] text-base">Dosen Pengampu</p>
                                    <p className="text-xs text-[#6a6f7c] mt-1 leading-relaxed">
                                        Supervisi mata kuliah, verifikasi laporan &amp; penilaian akademik.
                                    </p>
                                </div>
                                <div className="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                                    <span className="font-mono text-[10px] text-[#6a6f7c] uppercase">Bobot 45%</span>
                                    <span className="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">
                                        arrow_forward
                                    </span>
                                </div>
                            </button>
                        )}

                        {bisaKps && (
                            <button
                                type="button"
                                onClick={() => selectRole('kps')}
                                className="w-full bg-white border border-[#e4e4e7] hover:border-[#013981] rounded-xl p-6 text-left transition-all group shadow-surface hover:shadow-surface-md flex flex-col justify-between h-full cursor-pointer"
                            >
                                <div>
                                    <div className="w-10 h-10 rounded-lg bg-[#f4f4f5] border border-[#e4e4e7] group-hover:bg-[#013981] group-hover:text-white group-hover:border-[#013981] flex items-center justify-center mb-4 transition-colors">
                                        <span className="material-symbols-outlined text-[20px]">account_balance</span>
                                    </div>
                                    <p className="font-semibold text-[#0b0b0c] text-base">Koordinator Prodi</p>
                                    <p className="text-xs text-[#6a6f7c] mt-1 leading-relaxed">
                                        Monitoring mahasiswa, parameter penilaian &amp; ekspor transkrip.
                                    </p>
                                </div>
                                <div className="mt-5 pt-3 border-t border-[#f4f4f5] flex items-center justify-between">
                                    <span className="font-mono text-[10px] text-[#0b0b0c] bg-[#f4f4f5] px-2 py-0.5 rounded uppercase">
                                        {prodiKps ? prodiKps.toUpperCase() : 'KPS'}
                                    </span>
                                    <span className="material-symbols-outlined text-[16px] text-[#6a6f7c] group-hover:text-[#0b0b0c] group-hover:translate-x-0.5 transition-all">
                                        arrow_forward
                                    </span>
                                </div>
                            </button>
                        )}
                    </div>

                    <div className="text-center">
                        <button
                            type="button"
                            onClick={handleLogout}
                            className="text-xs text-[#6a6f7c] hover:text-[#b91c1c] font-medium transition-colors inline-flex items-center gap-1.5 cursor-pointer"
                        >
                            <span className="material-symbols-outlined text-[15px]">logout</span>
                            <span>Keluar dari akun ini</span>
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}
