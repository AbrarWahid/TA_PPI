import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function formatDate(d) {
    if (!d) return '';
    const dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    const day = String(dt.getDate()).padStart(2, '0');
    const month = MONTHS[dt.getMonth()];
    const year = dt.getFullYear();
    return `${day} ${month} ${year}`;
}

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

function formatRupiah(val) {
    if (!val) return '-';
    return 'Rp ' + Number(val).toLocaleString('id-ID');
}

export default function PengajuanProyekShow({ pengajuan_proyek, mahasiswaLoginId = null }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isAdmin = user?.role === 'admin';
    const isMahasiswa = user?.role === 'mahasiswa';

    const [showForm, setShowForm] = useState(false);
    const [action, setAction] = useState('');
    const [catatanAdmin, setCatatanAdmin] = useState('');

    const totalKebutuhan = (pengajuan_proyek?.kebutuhan || []).reduce(
        (acc, k) => acc + Number(k.jumlah_mahasiswa || 0),
        0,
    );

    const dosenPengampuName =
        pengajuan_proyek?.dosen_pengampu?.user?.name ||
        pengajuan_proyek?.dosenPengampu?.user?.name ||
        '-';

    const diprosesOleh =
        pengajuan_proyek?.diproses_oleh_user ||
        pengajuan_proyek?.diprosesOleh ||
        (typeof pengajuan_proyek?.diproses_oleh === 'object' ? pengajuan_proyek?.diproses_oleh : null);

    const handleActionSubmit = (e) => {
        e.preventDefault();
        const url =
            action === 'approve'
                ? `/pengajuan-proyek/${pengajuan_proyek.id}/approve`
                : `/pengajuan-proyek/${pengajuan_proyek.id}/reject`;
        router.patch(url, { catatan_admin: catatanAdmin });
    };

    const mahasiswaList = pengajuan_proyek?.mahasiswa || [];

    return (
        <AppLayout title="Detail Pengajuan">
            <div className="flex-1 flex flex-col">
                {/* HEADER */}
                <div className="flex items-center gap-4 mb-8">
                    {/* Tombol back sesuai role */}
                    {isMahasiswa ? (
                        <Link
                            href="/dashboard"
                            className="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100"
                        >
                            <span className="material-symbols-outlined">arrow_back</span>
                        </Link>
                    ) : (
                        <Link
                            href="/pengajuan-proyek"
                            className="p-2.5 text-gray-400 hover:text-[#004d4d] hover:bg-teal-50 rounded-xl transition-all border border-gray-100"
                        >
                            <span className="material-symbols-outlined">arrow_back</span>
                        </Link>
                    )}

                    <div>
                        <h1 className="text-3xl font-black text-[#004d4d] tracking-tighter italic uppercase">
                            Detail <span className="text-[#2dce89]">Pengajuan</span>
                        </h1>
                        <p className="text-gray-400 text-xs font-bold mt-1 uppercase tracking-widest">
                            {pengajuan_proyek?.kode_pengajuan}
                        </p>
                    </div>
                    <div className="ml-auto flex items-center gap-3">
                        {pengajuan_proyek?.status === 'approved' ? (
                            <>
                                <span className="inline-flex items-center gap-2 px-5 py-2.5 bg-green-50 text-green-600 border border-green-100 rounded-2xl text-xs font-black uppercase">
                                    <span className="material-symbols-outlined text-base">check_circle</span>{' '}
                                    Disetujui
                                </span>
                                {isAdmin && (
                                    <Link
                                        href={`/pengajuan-proyek/${pengajuan_proyek.id}/assign`}
                                        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-black uppercase text-white transition"
                                        style={{ backgroundColor: '#004d4d' }}
                                    >
                                        <span className="material-symbols-outlined text-base">group_add</span> Pilih
                                        Mahasiswa
                                    </Link>
                                )}
                            </>
                        ) : pengajuan_proyek?.status === 'rejected' ? (
                            <span className="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-500 border border-red-100 rounded-2xl text-xs font-black uppercase">
                                <span className="material-symbols-outlined text-base">cancel</span> Ditolak
                            </span>
                        ) : (
                            <span className="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-50 text-amber-500 border border-amber-100 rounded-2xl text-xs font-black uppercase">
                                <span className="material-symbols-outlined text-base">pending</span> Menunggu Review
                            </span>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-3 gap-6">
                    {/* KOLOM KIRI (2/3) */}
                    <div className="col-span-2 space-y-6">
                        {/* Info Proyek */}
                        <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                            <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                                <span className="material-symbols-outlined text-[#2dce89]">info</span> Informasi Proyek
                            </h2>
                            <h3 className="text-2xl font-black text-[#004d4d] italic mb-2">
                                {pengajuan_proyek?.judul_proyek}
                            </h3>
                            <p className="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-6">
                                Diajukan oleh: {pengajuan_proyek?.manager?.name ?? '-'}
                            </p>
                            <div className="grid grid-cols-2 gap-6 mb-6">
                                <div className="bg-gray-50 rounded-2xl p-4">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        Tanggal Mulai
                                    </p>
                                    <p className="font-black text-[#004d4d]">
                                        {formatDate(pengajuan_proyek?.tanggal_mulai)}
                                    </p>
                                </div>
                                <div className="bg-gray-50 rounded-2xl p-4">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        Tanggal Selesai
                                    </p>
                                    <p className="font-black text-[#004d4d]">
                                        {formatDate(pengajuan_proyek?.tanggal_selesai)}
                                    </p>
                                </div>
                                {/* Anggaran hanya tampil untuk non-mahasiswa */}
                                {!isMahasiswa && (
                                    <div className="bg-gray-50 rounded-2xl p-4">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Anggaran
                                        </p>
                                        <p className="font-black text-[#004d4d]">
                                            {pengajuan_proyek?.anggaran
                                                ? formatRupiah(pengajuan_proyek.anggaran)
                                                : '-'}
                                        </p>
                                    </div>
                                )}
                                <div className="bg-gray-50 rounded-2xl p-4">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        Total Kebutuhan
                                    </p>
                                    <p className="font-black text-[#004d4d]">{totalKebutuhan} Mahasiswa</p>
                                </div>
                            </div>
                            <div className="mb-5">
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Deskripsi
                                </p>
                                <p className="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">
                                    {pengajuan_proyek?.deskripsi}
                                </p>
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    Tujuan
                                </p>
                                <p className="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">
                                    {pengajuan_proyek?.tujuan}
                                </p>
                            </div>
                        </div>

                        {/* Kebutuhan Tim */}
                        <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                            <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                                <span className="material-symbols-outlined text-[#2dce89]">groups</span> Kebutuhan Tim
                            </h2>
                            <div className="space-y-3">
                                {(pengajuan_proyek?.kebutuhan || []).map((kebutuhan) => (
                                    <div
                                        key={kebutuhan.id || kebutuhan.prodi}
                                        className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-xl bg-[#004d4d] flex items-center justify-center">
                                                <span className="material-symbols-outlined text-[#7fffd4] text-base">
                                                    school
                                                </span>
                                            </div>
                                            <span className="font-bold text-[#004d4d] text-sm">
                                                {labelProdi(kebutuhan.prodi)}
                                            </span>
                                        </div>
                                        <span className="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-700 border border-teal-100 rounded-xl text-[10px] font-black">
                                            {kebutuhan.jumlah_mahasiswa} mahasiswa
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Mahasiswa Ditugaskan */}
                        {pengajuan_proyek?.status === 'approved' && (
                            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                                <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span className="material-symbols-outlined text-[#2dce89]">manage_accounts</span>{' '}
                                    Mahasiswa Ditugaskan
                                </h2>
                                {mahasiswaList.length === 0 ? (
                                    <div className="text-center py-8 text-gray-300">
                                        <span className="material-symbols-outlined text-4xl">person_search</span>
                                        <p className="text-sm mt-2 font-bold">
                                            Belum ada mahasiswa yang ditugaskan.
                                        </p>
                                        {isAdmin && (
                                            <Link
                                                href={`/pengajuan-proyek/${pengajuan_proyek.id}/assign`}
                                                className="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-2xl text-xs font-black uppercase text-white transition"
                                                style={{ backgroundColor: '#004d4d' }}
                                            >
                                                <span className="material-symbols-outlined text-base">group_add</span>{' '}
                                                Pilih Sekarang
                                            </Link>
                                        )}
                                    </div>
                                ) : (
                                    <div className="space-y-3">
                                        {mahasiswaList.map((mhs) => {
                                            const isMe =
                                                mahasiswaLoginId !== null && mhs.id === mahasiswaLoginId;
                                            return (
                                                <div
                                                    key={mhs.id}
                                                    className={`flex items-center gap-4 p-4 rounded-2xl transition-all ${
                                                        isMe
                                                            ? 'bg-teal-50 border-2 border-teal-200'
                                                            : 'bg-gray-50'
                                                    }`}
                                                >
                                                    <div
                                                        className="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white flex-shrink-0"
                                                        style={{
                                                            backgroundColor: isMe ? '#2dce89' : '#004d4d',
                                                        }}
                                                    >
                                                        {(mhs.nama || '').substring(0, 2).toUpperCase()}
                                                    </div>
                                                    <div className="flex-1">
                                                        <p className="font-black text-sm text-[#004d4d] flex items-center gap-2">
                                                            {mhs.nama}
                                                            {isMe && (
                                                                <span className="text-[9px] font-black bg-teal-500 text-white px-2 py-0.5 rounded-full uppercase tracking-widest">
                                                                    Anda
                                                                </span>
                                                            )}
                                                        </p>
                                                        <p className="text-xs text-gray-400">{mhs.nim}</p>
                                                    </div>
                                                    <span
                                                        className="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest"
                                                        style={{
                                                            backgroundColor: '#e6f7f4',
                                                            color: '#004d4d',
                                                        }}
                                                    >
                                                        {labelProdi(mhs.pivot?.prodi)}
                                                    </span>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>

                    {/* KOLOM KANAN (1/3) */}
                    <div className="space-y-6">
                        {/* Catatan Admin */}
                        {pengajuan_proyek?.catatan_admin && (
                            <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                                <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <span className="material-symbols-outlined text-[#2dce89]">comment</span> Catatan
                                    Admin
                                </h2>
                                <p className="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-2xl p-4">
                                    {pengajuan_proyek.catatan_admin}
                                </p>
                                {diprosesOleh && (
                                    <p className="text-[10px] text-gray-400 font-bold mt-3 uppercase tracking-widest">
                                        Oleh: {diprosesOleh.name} · {formatDate(pengajuan_proyek.diproses_at)}
                                    </p>
                                )}
                            </div>
                        )}

                        {/* Aksi Admin */}
                        {isAdmin && pengajuan_proyek?.status === 'pending' && (
                            <div className="bg-[#004d4d] rounded-[2rem] p-6 space-y-3">
                                <p className="text-[#7fffd4] text-[10px] font-black uppercase tracking-widest mb-4">
                                    Tindakan Admin
                                </p>
                                {!showForm ? (
                                    <div className="space-y-3">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowForm(true);
                                                setAction('approve');
                                            }}
                                            className="w-full py-3.5 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase tracking-wider hover:scale-[1.02] transition-all"
                                        >
                                            <span className="material-symbols-outlined text-sm align-middle mr-1">
                                                check_circle
                                            </span>{' '}
                                            SETUJUI
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowForm(true);
                                                setAction('reject');
                                            }}
                                            className="w-full py-3.5 bg-red-500/20 text-red-300 rounded-2xl text-xs font-black uppercase tracking-wider hover:bg-red-500 hover:text-white transition-all"
                                        >
                                            <span className="material-symbols-outlined text-sm align-middle mr-1">
                                                cancel
                                            </span>{' '}
                                            TOLAK
                                        </button>
                                    </div>
                                ) : (
                                    <div>
                                        <form onSubmit={handleActionSubmit}>
                                            <label className="block text-[10px] font-black text-[#7fffd4] uppercase tracking-widest mb-2">
                                                Catatan{' '}
                                                <span>{action === 'reject' ? '(Wajib)' : '(Opsional)'}</span>
                                            </label>
                                            <textarea
                                                name="catatan_admin"
                                                rows={3}
                                                value={catatanAdmin}
                                                onChange={(e) => setCatatanAdmin(e.target.value)}
                                                className="w-full px-4 py-3 bg-white/10 text-white border border-white/20 rounded-2xl outline-none text-sm resize-none placeholder:text-white/30 focus:ring-2 focus:ring-[#7fffd4]"
                                                placeholder="Tulis catatan..."
                                            />
                                            <div className="flex gap-2 mt-3">
                                                <button
                                                    type="button"
                                                    onClick={() => setShowForm(false)}
                                                    className="flex-1 py-3 bg-white/10 text-white/60 rounded-2xl text-xs font-black uppercase hover:bg-white/20 transition-all"
                                                >
                                                    Batal
                                                </button>
                                                <button
                                                    type="submit"
                                                    className="flex-1 py-3 bg-[#7fffd4] text-[#004d4d] rounded-2xl text-xs font-black uppercase hover:scale-[1.02] transition-all"
                                                >
                                                    Konfirmasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* Info Card */}
                        <div className="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">
                            <h2 className="text-xs font-black text-[#004d4d] uppercase tracking-widest mb-4">
                                Info Pengajuan
                            </h2>
                            <div className="space-y-3">
                                <div className="flex justify-between items-center">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Kode
                                    </span>
                                    <span className="text-xs font-black text-[#004d4d]">
                                        {pengajuan_proyek?.kode_pengajuan}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Dibuat
                                    </span>
                                    <span className="text-xs font-bold text-gray-600">
                                        {formatDate(pengajuan_proyek?.created_at)}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Manager
                                    </span>
                                    <span className="text-xs font-bold text-gray-600">
                                        {pengajuan_proyek?.manager?.name ?? '-'}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Dosen Pengampu
                                    </span>
                                    <span className="text-xs font-bold text-gray-600">
                                        {dosenPengampuName}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
