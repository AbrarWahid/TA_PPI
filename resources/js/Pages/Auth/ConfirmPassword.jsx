import React from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/confirm-password', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Konfirmasi Kata Sandi — Portal Akademik AE" />
            <div className="bg-slate-50 font-sans text-slate-900 antialiased min-h-screen flex items-center justify-center p-6">
                <div className="w-full max-w-[440px] bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight mb-2">
                        Konfirmasi Kata Sandi
                    </h1>
                    <p className="text-sm text-slate-500 leading-relaxed mb-6">
                        Ini adalah area aman aplikasi. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
                    </p>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label htmlFor="password" className="block text-xs font-bold text-slate-700 mb-1.5">
                                Kata Sandi
                            </label>
                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                autoFocus
                                className="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:border-[#013981] outline-none text-sm font-medium text-slate-900"
                            />
                            {errors.password && (
                                <p className="mt-1.5 text-xs text-red-600 font-medium">{errors.password}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span>{processing ? 'Memproses...' : 'Konfirmasi'}</span>
                        </button>
                    </form>
                </div>
            </div>
        </>
    );
}
