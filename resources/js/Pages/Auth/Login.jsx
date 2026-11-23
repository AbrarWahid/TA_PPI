import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Login({ status }) {
    const { flash } = usePage().props;
    const [showPassword, setShowPassword] = useState(false);
    const [capsLock, setCapsLock] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    const handleKeyUp = (e) => {
        if (e.getModifierState) {
            setCapsLock(e.getModifierState('CapsLock'));
        }
    };

    const displayStatus = status || flash?.status || flash?.success;

    return (
        <>
            <Head title="Masuk | PBL & PPI Portal — Politeknik Manufaktur Bandung" />
            <div className="min-h-screen bg-slate-50 font-sans text-[#0b0b0c] antialiased flex flex-col lg:flex-row w-full">
                {/* LEFT PANEL — DUAL SCREEN POLMAN BANDUNG BRANDING */}
                <div
                    className="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-center p-12 xl:p-16 text-white"
                    style={{
                        background: 'linear-gradient(135deg, #02002e 0%, #013981 52%, #142c6e 100%)',
                    }}
                >
                    <div
                        className="absolute inset-0 bg-no-repeat bg-left-bottom bg-contain pointer-events-none z-[1]"
                        style={{ backgroundImage: "url('/images/akasia/sapolman.png')", backgroundSize: '100%' }}
                    />
                    <div className="relative z-10 space-y-5 max-w-lg">
                        <div className="inline-flex items-center gap-2 text-xs font-bold text-emerald-300 bg-emerald-400/15 border border-emerald-400/30 px-3.5 py-1.5 rounded-full uppercase tracking-wide">
                            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                            <span>Jurusan Automation Engineering</span>
                        </div>
                        <h1 className="text-4xl font-extrabold text-white leading-tight tracking-tight drop-shadow-sm">
                            Portal Akademik PBL &amp; Praktik Industri
                        </h1>
                        <p className="text-base text-white/85 leading-relaxed font-medium">
                            Kelola logbook aktivitas harian, supervisi mata kuliah, evaluasi mitra industri, dan
                            pengesahan transkrip nilai dalam satu platform terintegrasi.
                        </p>
                    </div>
                </div>

                {/* RIGHT PANEL — LOGIN CARD */}
                <div className="flex-1 flex flex-col justify-center p-6 sm:p-12 lg:p-16 relative min-h-screen">
                    {/* Top-Right Official Logos */}
                    <div className="flex items-center justify-between lg:justify-end gap-3.5 mb-8 lg:mb-0 lg:absolute lg:top-8 lg:right-12">
                        <Link href="/" className="inline-flex items-center gap-3.5">
                            <img
                                src="/images/logo-polman-teks.webp"
                                alt="Polman Bandung"
                                className="h-8 md:h-9 w-auto object-contain"
                            />
                            <img
                                src="/images/logo-blu-speed.png"
                                alt="BLU Speed"
                                className="h-9 md:h-10 w-auto object-contain"
                            />
                        </Link>
                    </div>

                    {/* Main Login Card */}
                    <div className="w-full max-w-md mx-auto">
                        <div className="w-full border border-slate-200/80 shadow-2xl shadow-slate-200/50 bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-9">
                            <div className="space-y-1.5 pb-6">
                                <h2 className="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                                    Selamat Datang
                                </h2>
                                <p className="text-sm text-slate-500 font-medium">
                                    Silakan masuk untuk mengakses dan mengelola portal akademik Anda.
                                </p>
                            </div>

                            {displayStatus && (
                                <div className="mb-5 flex items-center gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                                    <span className="material-symbols-outlined text-[#15803d] shrink-0 text-[18px]">
                                        check_circle
                                    </span>
                                    <p className="text-[#15803d] text-xs font-semibold">{displayStatus}</p>
                                </div>
                            )}

                            {(errors.email || errors.password) && (
                                <div className="mb-5 flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                                    <span className="material-symbols-outlined text-[#b91c1c] shrink-0 mt-0.5 text-[18px]">
                                        error
                                    </span>
                                    <div className="space-y-0.5">
                                        {errors.email && (
                                            <p className="text-[#b91c1c] text-xs font-semibold">{errors.email}</p>
                                        )}
                                        {errors.password && (
                                            <p className="text-[#b91c1c] text-xs font-semibold">{errors.password}</p>
                                        )}
                                    </div>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div className="space-y-2">
                                    <label
                                        htmlFor="email"
                                        className={`block text-sm font-semibold ${
                                            errors.email ? 'text-[#b91c1c]' : 'text-slate-700'
                                        }`}
                                    >
                                        Alamat Email
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        placeholder="Masukkan Email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                        autoFocus
                                        autoComplete="email"
                                        className={`w-full h-11 px-4 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all border ${
                                            errors.email
                                                ? 'border-red-300 bg-red-50 focus:border-[#b91c1c]'
                                                : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-[#013981] focus:ring-3 focus:ring-[#013981]/10'
                                        }`}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <div className="flex items-center justify-between">
                                        <label
                                            htmlFor="password"
                                            className={`block text-sm font-semibold ${
                                                errors.password ? 'text-[#b91c1c]' : 'text-slate-700'
                                            }`}
                                        >
                                            Kata Sandi
                                        </label>
                                        <a
                                            href="/forgot-password"
                                            className="text-xs font-semibold text-[#013981] hover:underline"
                                        >
                                            Lupa kata sandi?
                                        </a>
                                    </div>
                                    <div className="relative">
                                        <input
                                            id="password"
                                            name="password"
                                            type={showPassword ? 'text' : 'password'}
                                            placeholder="Masukkan Password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            onKeyUp={handleKeyUp}
                                            required
                                            autoComplete="current-password"
                                            className={`w-full h-11 pl-4 pr-11 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all border ${
                                                errors.password
                                                    ? 'border-red-300 bg-red-50 focus:border-[#b91c1c]'
                                                    : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-[#013981] focus:ring-3 focus:ring-[#013981]/10'
                                            }`}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors"
                                        >
                                            <span className="material-symbols-outlined text-[20px]">
                                                {showPassword ? 'visibility' : 'visibility_off'}
                                            </span>
                                        </button>
                                    </div>
                                    {capsLock && (
                                        <div className="flex items-center gap-1.5 text-amber-600 text-xs font-semibold mt-1">
                                            <span className="material-symbols-outlined text-[15px]">keyboard_capslock</span>
                                            <span>Caps Lock sedang aktif</span>
                                        </div>
                                    )}
                                </div>

                                <div className="flex items-center justify-between">
                                    <label className="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            name="remember"
                                            checked={data.remember}
                                            onChange={(e) => setData('remember', e.target.checked)}
                                            className="w-4 h-4 rounded border-slate-300 text-[#013981] focus:ring-[#013981]"
                                        />
                                        <span className="text-xs font-semibold text-slate-600">Ingat sesi saya</span>
                                    </label>
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full h-11 flex items-center justify-center gap-2 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all active:scale-[0.98]"
                                >
                                    <span>{processing ? 'Memproses...' : 'Masuk'}</span>
                                </button>
                            </form>

                            <div className="text-center text-sm text-slate-500 font-medium mt-6 pt-5 border-t border-slate-100">
                                Belum memiliki akun?{' '}
                                <Link href="/register" className="font-bold text-[#013981] hover:underline">
                                    Daftar sekarang
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
