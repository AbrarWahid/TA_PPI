import React from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/reset-password', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Atur Ulang Kata Sandi — Portal Akademik AE" />
            <div className="bg-slate-50 font-sans text-slate-900 antialiased min-h-screen flex items-center justify-center p-6">
                <div className="w-full max-w-[440px] bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight mb-2">
                        Atur Ulang Kata Sandi
                    </h1>
                    <p className="text-sm text-slate-500 leading-relaxed mb-6">
                        Silakan buat kata sandi baru untuk akun Anda.
                    </p>

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
                                className="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:border-[#013981] outline-none text-sm font-medium text-slate-900"
                            />
                            {errors.email && (
                                <p className="mt-1.5 text-xs text-red-600 font-medium">{errors.email}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-xs font-bold text-slate-700 mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                autoComplete="new-password"
                                className="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:border-[#013981] outline-none text-sm font-medium text-slate-900"
                            />
                            {errors.password && (
                                <p className="mt-1.5 text-xs text-red-600 font-medium">{errors.password}</p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="password_confirmation"
                                className="block text-xs font-bold text-slate-700 mb-1.5"
                            >
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                required
                                autoComplete="new-password"
                                className="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:border-[#013981] outline-none text-sm font-medium text-slate-900"
                            />
                            {errors.password_confirmation && (
                                <p className="mt-1.5 text-xs text-red-600 font-medium">
                                    {errors.password_confirmation}
                                </p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span>{processing ? 'Menyimpan...' : 'Simpan Kata Sandi Baru'}</span>
                        </button>
                    </form>
                </div>
            </div>
        </>
    );
}
