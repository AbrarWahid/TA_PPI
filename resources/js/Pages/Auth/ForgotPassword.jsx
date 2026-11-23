import React from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function ForgotPassword({ status }) {
    const { flash } = usePage().props;
    const statusMsg = status || flash?.status;

    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <>
            <Head title="Lupa Kata Sandi — Portal Akademik AE" />
            <div className="bg-slate-50 font-sans text-slate-900 antialiased min-h-screen flex items-center justify-center p-6">
                <div className="w-full max-w-[440px] bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
                    <div className="flex items-center gap-2.5 mb-6">
                        <div className="w-9 h-9 rounded-lg bg-[#013981] flex items-center justify-center overflow-hidden">
                            <img src="/images/logo.png" alt="Logo Polman" className="w-6 h-6 object-contain" />
                        </div>
                        <div className="leading-none">
                            <span className="text-sm font-bold tracking-tight text-slate-900 block">
                                Portal Akademik AE
                            </span>
                            <span className="text-[11px] font-medium text-slate-500 block mt-0.5">
                                Politeknik Manufaktur Bandung
                            </span>
                        </div>
                    </div>

                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight mb-2">
                        Lupa Kata Sandi?
                    </h1>
                    <p className="text-sm text-slate-500 leading-relaxed mb-6">
                        Masukkan alamat email akun Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                    </p>

                    {statusMsg && (
                        <div className="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-700">
                            {statusMsg}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label htmlFor="email" className="block text-xs font-bold text-slate-700 mb-1.5">
                                Alamat Email
                            </label>
                            <input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoFocus
                                placeholder="nama@mhs.polman-bandung.ac.id"
                                className="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:border-[#013981] outline-none text-sm font-medium text-slate-900"
                            />
                            {errors.email && (
                                <p className="mt-1.5 text-xs text-red-600 font-medium">{errors.email}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span>{processing ? 'Mengirim...' : 'Kirim Tautan Reset Password'}</span>
                        </button>
                    </form>

                    <div className="mt-6 pt-5 border-t border-slate-100 text-center">
                        <Link
                            href="/login"
                            className="text-xs font-semibold text-slate-500 hover:text-[#013981] transition-colors"
                        >
                            Kembali ke Halaman Masuk
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
}
