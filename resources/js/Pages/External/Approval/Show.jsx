import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalApprovalShow({ approval }) {
    const dbStatus = approval?.status || 'pending';
    const initialView =
        dbStatus === 'accepted'
            ? 'accepted'
            : dbStatus === 'rejected'
              ? 'rejected_done'
              : 'pending';

    const [view, setView] = useState(initialView);
    const [alasanInput, setAlasanInput] = useState('');
    const [alasanDb, setAlasanDb] = useState(approval?.alasan_reject ?? '');
    const [isLoading, setIsLoading] = useState(false);

    const token = approval?.token_verifikasi || '';

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const changeView = (newView) => {
        setView(newView);
    };

    const processAction = async (action) => {
        if (action === 'reject' && alasanInput.trim() === '') {
            alert('Harap isi alasan penolakan terlebih dahulu.');
            return;
        }

        setIsLoading(true);

        try {
            const response = await fetch(`/external/approval/${token}/process`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    action,
                    alasan: alasanInput,
                }),
            });

            const result = await response.json();

            if (result.status === 'success') {
                if (action === 'reject') {
                    setAlasanDb(alasanInput);
                }
                setView(action === 'accept' ? 'accepted' : 'rejected_done');
            } else {
                alert('Terjadi kesalahan sistem: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal terhubung ke server. Pastikan rute backend sesuai.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout title="Approval Tanda Tangan Digital">
            <div className="bg-gray-50 text-gray-900 font-sans antialiased min-h-[calc(100vh-8rem)] flex items-center justify-center p-4">
                <div className="w-full max-w-md relative">
                    {/* ============================================== */}
                    {/* STATE 1: KOTAK KONFIRMASI (PENDING)            */}
                    {/* ============================================== */}
                    {view === 'pending' && (
                        <div
                            className="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden absolute w-full"
                            style={{ transformOrigin: 'center' }}
                        >
                            <div className="bg-blue-600 p-6 text-center">
                                <div className="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                                    <svg
                                        className="w-8 h-8 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        strokeWidth="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        ></path>
                                    </svg>
                                </div>
                                <h2 className="text-xl font-bold text-white">
                                    Verifikasi Dokumen
                                </h2>
                                <p className="text-blue-100 text-sm mt-1">
                                    Permintaan Tanda Tangan Digital
                                </p>
                            </div>

                            <div className="p-6 space-y-4">
                                <div className="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p className="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">
                                        Nama Mahasiswa
                                    </p>
                                    <p className="font-bold text-gray-900">
                                        {approval?.mahasiswa?.nama_mhs ??
                                            approval?.mahasiswa_nama ??
                                            'Mahasiswa'}
                                    </p>

                                    <p className="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-4 mb-1">
                                        Dokumen
                                    </p>
                                    <p className="font-bold text-gray-900 uppercase">
                                        {approval?.jenis_form}
                                    </p>
                                </div>

                                <div className="flex gap-3 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => changeView('rejecting')}
                                        className="flex-1 py-3 px-4 rounded-xl font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors"
                                    >
                                        Tolak
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => processAction('accept')}
                                        disabled={isLoading}
                                        className="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors flex justify-center items-center gap-2"
                                    >
                                        {!isLoading ? (
                                            <span>Setujui</span>
                                        ) : (
                                            <svg
                                                className="animate-spin h-5 w-5 text-white"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    className="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    strokeWidth="4"
                                                ></circle>
                                                <path
                                                    className="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* ============================================== */}
                    {/* STATE 2: KOTAK ALASAN PENOLAKAN (REJECTING)    */}
                    {/* ============================================== */}
                    {view === 'rejecting' && (
                        <div className="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden absolute w-full z-10">
                            <div className="p-6">
                                <h3 className="text-lg font-bold text-gray-900 mb-2">
                                    Alasan Penolakan
                                </h3>
                                <p className="text-sm text-gray-500 mb-4">
                                    Silakan berikan alasan mengapa dokumen ini ditolak agar
                                    mahasiswa dapat memperbaikinya.
                                </p>

                                <textarea
                                    value={alasanInput}
                                    onChange={(e) => setAlasanInput(e.target.value)}
                                    rows="4"
                                    className="w-full border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 text-sm mb-4 p-3 border"
                                    placeholder="Misal: Laporan minggu ini kurang detail..."
                                ></textarea>

                                <div className="flex gap-3">
                                    <button
                                        type="button"
                                        onClick={() => changeView('pending')}
                                        className="flex-1 py-3 px-4 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => processAction('reject')}
                                        disabled={isLoading}
                                        className="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 transition-colors flex justify-center items-center gap-2"
                                    >
                                        {!isLoading ? (
                                            <span>Kirim Tolakan</span>
                                        ) : (
                                            <svg
                                                className="animate-spin h-5 w-5 text-white"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    className="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    strokeWidth="4"
                                                ></circle>
                                                <path
                                                    className="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* ============================================== */}
                    {/* STATE 3: KOTAK SUKSES DISETUJUI (ACCEPTED)     */}
                    {/* ============================================== */}
                    {view === 'accepted' && (
                        <div className="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden text-center p-8 absolute w-full z-20">
                            <div className="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg
                                    className="w-12 h-12 text-emerald-500"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                            </div>
                            <h2 className="text-2xl font-black text-gray-900 mb-2">
                                Disetujui!
                            </h2>
                            <p className="text-gray-500 text-sm">
                                Form penilaian ini sudah disetujui. Tanda Tangan Digital Anda telah
                                sah dan tersinkronisasi.
                            </p>
                        </div>
                    )}

                    {/* ============================================== */}
                    {/* STATE 4: KOTAK SUKSES DITOLAK (REJECTED DONE)  */}
                    {/* ============================================== */}
                    {view === 'rejected_done' && (
                        <div className="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden text-center p-8 absolute w-full z-20">
                            <div className="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg
                                    className="w-12 h-12 text-red-600"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    ></path>
                                </svg>
                            </div>
                            <h2 className="text-2xl font-black text-gray-900 mb-2">Ditolak</h2>
                            <p className="text-gray-500 text-sm mb-4">
                                Form penilaian ini telah ditolak. Mahasiswa terkait akan menerima
                                notifikasi.
                            </p>

                            {alasanDb !== '' && (
                                <div className="bg-red-50 border border-red-100 p-3 rounded-xl text-sm text-red-700 italic text-left">
                                    &quot;<span>{alasanDb}</span>&quot;
                                </div>
                            )}
                        </div>
                    )}

                    {/* Spacer to maintain height in absolute positioning */}
                    <div className="h-[420px] invisible"></div>
                </div>
            </div>
        </AppLayout>
    );
}
