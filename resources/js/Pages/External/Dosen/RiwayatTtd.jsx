import React from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalDosenRiwayatTtd({ riwayat }) {
    const list = Array.isArray(riwayat) ? riwayat : riwayat?.data || [];
    const links = !Array.isArray(riwayat) && Array.isArray(riwayat?.links) ? riwayat.links : [];

    const totalAccepted = list.filter((item) => item.status === 'accepted').length;
    const totalRejected = list.filter((item) => item.status === 'rejected').length;

    const formatWaktu = (dateStr) => {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
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
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const mins = String(d.getMinutes()).padStart(2, '0');
        return `${day} ${month} ${year}, ${hours}:${mins}`;
    };

    return (
        <AppLayout title="Riwayat TTD & Approval">
            <div className="max-w-7xl mx-auto space-y-6 pb-12 font-sans">
                {/* Header */}
                <div className="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex justify-between items-center">
                    <div>
                        <h2 className="text-3xl font-bold text-gray-900 tracking-tight">
                            Riwayat TTD &amp; Approval
                        </h2>
                        <p className="text-gray-500 mt-1">
                            Log aktivitas persetujuan dokumen mahasiswa bimbingan Anda.
                        </p>
                    </div>
                    <div className="bg-blue-50 text-blue-600 p-4 rounded-2xl flex items-center gap-4 border border-blue-100">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-wider text-blue-400">
                                Total Disahkan
                            </p>
                            <p className="text-2xl font-black">{totalAccepted}</p>
                        </div>
                        <div className="w-px h-8 bg-blue-200"></div>
                        <div>
                            <p className="text-xs font-bold uppercase tracking-wider text-red-400">
                                Total Ditolak
                            </p>
                            <p className="text-2xl font-black text-red-600">{totalRejected}</p>
                        </div>
                    </div>
                </div>

                {/* Tabel Riwayat */}
                <div className="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm whitespace-nowrap">
                            <thead className="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold border-b border-gray-200">
                                <tr>
                                    <th className="px-6 py-4">Waktu Keputusan</th>
                                    <th className="px-6 py-4">Nama Mahasiswa</th>
                                    <th className="px-6 py-4">Jenis Dokumen</th>
                                    <th className="px-6 py-4 text-center">Status</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 text-gray-700 font-medium">
                                {list.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="px-6 py-12 text-center text-gray-400 italic"
                                        >
                                            Belum ada riwayat TTD.
                                        </td>
                                    </tr>
                                ) : (
                                    list.map((item) => (
                                        <tr
                                            key={item.id || item.token_verifikasi}
                                            className="hover:bg-gray-50 transition"
                                        >
                                            <td className="px-6 py-4 text-gray-500 text-xs">
                                                {formatWaktu(item.updated_at)}
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="font-bold text-gray-900">
                                                    {item.mahasiswa?.nama_mhs ?? 'Tanpa Nama'}
                                                </span>
                                                <br />
                                                <span className="text-xs text-gray-400">
                                                    {item.mahasiswa?.nim ?? '-'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                                    {String(item.jenis_form || '').replace(
                                                        /_/g,
                                                        ' ',
                                                    )}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                {item.status === 'accepted' ? (
                                                    <span className="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-md text-xs font-bold">
                                                        Verified (Acc)
                                                    </span>
                                                ) : (
                                                    <span className="bg-red-100 text-red-700 px-3 py-1 rounded-md text-xs font-bold">
                                                        Ditolak
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={`/external/approval/${item.token_verifikasi}`}
                                                    className="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition"
                                                >
                                                    Lihat Detail
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    <div className="p-4 border-t border-gray-100">
                        {links.length > 3 && (
                            <div className="flex flex-wrap items-center justify-end gap-1">
                                {links.map((link, idx) =>
                                    link.url ? (
                                        <Link
                                            key={idx}
                                            href={link.url}
                                            className={`px-3 py-1.5 rounded-lg text-xs font-semibold border ${
                                                link.active
                                                    ? 'bg-blue-600 text-white border-blue-600'
                                                    : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={idx}
                                            className="px-3 py-1.5 rounded-lg text-xs text-gray-400 border border-gray-100"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ),
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
