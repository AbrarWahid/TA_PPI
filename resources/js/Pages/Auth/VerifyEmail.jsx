import React from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
    const { flash } = usePage().props;
    const currentStatus = status || flash?.status;
    const { post, processing } = useForm({});

    const handleResend = (e) => {
        e.preventDefault();
        post('/email/verification-notification');
    };

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <>
            <Head title="Verifikasi Email — Portal Akademik AE" />
            <div className="bg-slate-50 font-sans text-slate-900 antialiased min-h-screen flex items-center justify-center p-6">
                <div className="w-full max-w-[440px] bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight mb-2">
                        Verifikasi Alamat Email
                    </h1>
                    <p className="text-sm text-slate-500 leading-relaxed mb-6">
                        Terima kasih telah mendaftar! Silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan.
                    </p>

                    {currentStatus === 'verification-link-sent' && (
                        <div className="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-700">
                            Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.
                        </div>
                    )}

                    <form onSubmit={handleResend} className="space-y-3">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span>{processing ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi'}</span>
                        </button>
                    </form>

                    <div className="mt-6 pt-5 border-t border-slate-100 text-center">
                        <button
                            type="button"
                            onClick={handleLogout}
                            className="text-xs font-semibold text-slate-500 hover:text-red-600 transition-colors"
                        >
                            Keluar (Logout)
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}
