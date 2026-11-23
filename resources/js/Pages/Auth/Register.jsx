import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

const prodis = [
    { id: 'informatika', label: 'Informatika', icon: 'terminal', prefix: '3AEC' },
    { id: 'otomasi', label: 'Otomasi', icon: 'settings_suggest', prefix: '3AEB' },
    { id: 'mekatronika', label: 'Mekatronika', icon: 'precision_manufacturing', prefix: '3AEA' },
];

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        nim: '',
        role: 'mahasiswa',
        prodi: '',
        kelas_register: '',
        password: '',
        password_confirmation: '',
    });

    const handleRoleChange = (role) => {
        setData((prev) => ({
            ...prev,
            role,
            prodi: role === 'dosen' ? '' : prev.prodi,
            kelas_register: role === 'dosen' ? '' : prev.kelas_register,
        }));
    };

    const handleProdiChange = (prodiId) => {
        setData((prev) => ({
            ...prev,
            prodi: prodiId,
            kelas_register: '',
        }));
    };

    const selectedProdiObj = prodis.find((p) => p.id === data.prodi);
    const kelasOptions = selectedProdiObj
        ? [1, 2, 3, 4].map((num) => `${selectedProdiObj.prefix}-${num}`)
        : [];

    const getPasswordStrength = (pw) => {
        if (!pw) return { score: 0, label: '', color: 'bg-slate-200' };
        let s = 0;
        if (pw.length >= 8) s++;
        if (/[A-Z]/.test(pw)) s++;
        if (/[0-9]/.test(pw)) s++;
        if (/[^A-Za-z0-9]/.test(pw)) s++;
        const map = [
            { label: 'Sangat Lemah', color: 'bg-red-500', text: 'text-red-600' },
            { label: 'Lemah', color: 'bg-red-500', text: 'text-red-600' },
            { label: 'Cukup', color: 'bg-amber-500', text: 'text-amber-600' },
            { label: 'Kuat', color: 'bg-blue-600', text: 'text-blue-600' },
            { label: 'Sangat Kuat', color: 'bg-emerald-600', text: 'text-emerald-600' },
        ];
        return { score: s, ...map[s] };
    };

    const strength = getPasswordStrength(data.password);
    const allErrors = Object.values(errors);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <>
            <Head title="Daftar Akun | PBL & PPI Portal — Politeknik Manufaktur Bandung" />
            <div className="min-h-screen bg-slate-50 font-sans text-[#0b0b0c] antialiased flex flex-col lg:flex-row w-full">
                {/* LEFT PANEL */}
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
                            Registrasi Sivitas Akademika
                        </h1>
                        <p className="text-base text-white/85 leading-relaxed font-medium">
                            Daftarkan akun mahasiswa atau dosen untuk mengakses pencatatan logbook, supervisi mata
                            kuliah, dan penilaian terintegrasi.
                        </p>
                    </div>
                </div>

                {/* RIGHT PANEL */}
                <div className="flex-1 flex flex-col justify-center p-6 sm:p-10 lg:p-12 relative min-h-screen overflow-y-auto">
                    <div className="flex items-center justify-between lg:justify-end gap-3.5 mb-6">
                        <Link href="/" className="inline-flex items-center gap-3.5">
                            <img
                                src="/images/logo-polman-teks.webp"
                                alt="Polman Bandung"
                                className="h-8 w-auto object-contain"
                            />
                            <img
                                src="/images/logo-blu-speed.png"
                                alt="BLU Speed"
                                className="h-9 w-auto object-contain"
                            />
                        </Link>
                    </div>

                    <div className="w-full max-w-md mx-auto">
                        <div className="w-full border border-slate-200/80 shadow-2xl shadow-slate-200/50 bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-8">
                            <div className="space-y-1 pb-5">
                                <h2 className="text-2xl font-bold text-slate-900 tracking-tight">Buat Akun Baru</h2>
                                <p className="text-xs sm:text-sm text-slate-500 font-medium">
                                    Lengkapi data kredensial PBL &amp; PPI Anda.
                                </p>
                            </div>

                            {allErrors.length > 0 && (
                                <div className="mb-4 flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                                    <span className="material-symbols-outlined text-[#b91c1c] shrink-0 mt-0.5 text-[18px]">
                                        error
                                    </span>
                                    <div className="space-y-0.5">
                                        {allErrors.map((err, i) => (
                                            <p key={i} className="text-[#b91c1c] text-xs font-semibold">
                                                {err}
                                            </p>
                                        ))}
                                    </div>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-3.5">
                                {/* ROLE */}
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Daftar Sebagai
                                    </label>
                                    <div className="grid grid-cols-2 gap-2.5">
                                        {[
                                            ['mahasiswa', 'Mahasiswa', 'school'],
                                            ['dosen', 'Dosen', 'co_present'],
                                        ].map(([rId, rLabel, rIcon]) => {
                                            const active = data.role === rId;
                                            return (
                                                <button
                                                    key={rId}
                                                    type="button"
                                                    onClick={() => handleRoleChange(rId)}
                                                    className={`transition-all border rounded-xl py-2.5 px-2 flex flex-col items-center gap-1 cursor-pointer ${
                                                        active
                                                            ? 'border-[#013981] bg-[#013981] text-white'
                                                            : 'border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-900'
                                                    }`}
                                                >
                                                    <span className="material-symbols-outlined text-[18px]">
                                                        {rIcon}
                                                    </span>
                                                    <span className="font-bold text-xs">{rLabel}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>

                                {data.role === 'dosen' && (
                                    <div className="flex items-start gap-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                        <span className="material-symbols-outlined text-[#b45309] shrink-0 text-[16px]">
                                            info
                                        </span>
                                        <p className="text-[#b45309] text-xs font-medium">
                                            Akun dosen memerlukan <strong>persetujuan Admin</strong> sebelum dapat
                                            digunakan.
                                        </p>
                                    </div>
                                )}

                                {/* NAMA */}
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Nama Lengkap
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                        placeholder="Masukkan nama lengkap"
                                        className="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-[#0b0b0c] placeholder:text-slate-400 outline-none focus:border-[#013981] focus:bg-white transition-all"
                                    />
                                </div>

                                {/* EMAIL & NIM/NIDN */}
                                <div className="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Alamat Email
                                        </label>
                                        <input
                                            type="email"
                                            name="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                            placeholder="nama@polman.ac.id"
                                            className="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-[#0b0b0c] placeholder:text-slate-400 outline-none focus:border-[#013981] focus:bg-white transition-all"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                            {data.role === 'mahasiswa' ? 'NIM' : 'NIDN'}
                                        </label>
                                        <input
                                            type="text"
                                            name="nim"
                                            value={data.nim}
                                            onChange={(e) => setData('nim', e.target.value)}
                                            required
                                            placeholder={data.role === 'mahasiswa' ? '23010xxx' : '04120xxx'}
                                            className="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-[#0b0b0c] placeholder:text-slate-400 outline-none focus:border-[#013981] focus:bg-white transition-all"
                                        />
                                    </div>
                                </div>

                                {/* PRODI & KELAS (Mahasiswa) */}
                                {data.role === 'mahasiswa' && (
                                    <>
                                        <div>
                                            <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                                Program Studi
                                            </label>
                                            <div className="grid grid-cols-3 gap-2">
                                                {prodis.map((p) => {
                                                    const active = data.prodi === p.id;
                                                    return (
                                                        <button
                                                            key={p.id}
                                                            type="button"
                                                            onClick={() => handleProdiChange(p.id)}
                                                            className={`transition-all border rounded-xl py-2.5 px-2 flex flex-col items-center gap-1 cursor-pointer ${
                                                                active
                                                                    ? 'border-[#013981] bg-[#013981] text-white'
                                                                    : 'border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-900'
                                                            }`}
                                                        >
                                                            <span className="material-symbols-outlined text-[18px]">
                                                                {p.icon}
                                                            </span>
                                                            <span className="font-bold text-[11px]">{p.label}</span>
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        {kelasOptions.length > 0 && (
                                            <div>
                                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                                    Pilih Kelas
                                                </label>
                                                <div className="grid grid-cols-4 gap-2">
                                                    {kelasOptions.map((kls) => {
                                                        const active = data.kelas_register === kls;
                                                        return (
                                                            <button
                                                                key={kls}
                                                                type="button"
                                                                onClick={() => setData('kelas_register', kls)}
                                                                className={`transition-all border rounded-xl py-2 px-1 flex items-center justify-center font-semibold text-xs cursor-pointer ${
                                                                    active
                                                                        ? 'border-[#013981] bg-[#013981] text-white'
                                                                        : 'border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-900'
                                                                }`}
                                                            >
                                                                {kls}
                                                            </button>
                                                        );
                                                    })}
                                                </div>
                                            </div>
                                        )}
                                    </>
                                )}

                                {/* PASSWORD */}
                                <div className="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Kata Sandi
                                        </label>
                                        <input
                                            type="password"
                                            name="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            required
                                            autoComplete="new-password"
                                            placeholder="••••••••"
                                            className="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-[#0b0b0c] placeholder:text-slate-400 outline-none focus:border-[#013981] focus:bg-white transition-all"
                                        />
                                        {data.password && (
                                            <div className="mt-1.5">
                                                <div className="flex gap-1">
                                                    {[1, 2, 3, 4].map((idx) => (
                                                        <div
                                                            key={idx}
                                                            className={`h-1 flex-1 rounded-full transition-all duration-300 ${
                                                                idx <= strength.score
                                                                    ? strength.color
                                                                    : 'bg-slate-200'
                                                            }`}
                                                        />
                                                    ))}
                                                </div>
                                                <p className={`text-[10px] font-semibold mt-0.5 ${strength.text}`}>
                                                    {strength.label}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Konfirmasi
                                        </label>
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            value={data.password_confirmation}
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            required
                                            autoComplete="new-password"
                                            placeholder="••••••••"
                                            className="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] font-medium text-[#0b0b0c] placeholder:text-slate-400 outline-none focus:border-[#013981] focus:bg-white transition-all"
                                        />
                                        {data.password_confirmation && (
                                            <p
                                                className={`text-[10px] font-semibold mt-1.5 ${
                                                    data.password === data.password_confirmation
                                                        ? 'text-emerald-600'
                                                        : 'text-red-600'
                                                }`}
                                            >
                                                {data.password === data.password_confirmation
                                                    ? 'Kata sandi cocok'
                                                    : 'Belum cocok'}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="pt-2">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full h-11 flex items-center justify-center gap-2 bg-[#013981] hover:bg-[#012b63] disabled:opacity-60 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all active:scale-[0.98]"
                                    >
                                        <span>{processing ? 'Mendaftarkan...' : 'Daftar Akun'}</span>
                                    </button>
                                </div>
                            </form>

                            <div className="text-center text-sm text-slate-500 font-medium mt-5 pt-4 border-t border-slate-100">
                                Sudah memiliki akun?{' '}
                                <Link href="/login" className="font-bold text-[#013981] hover:underline">
                                    Masuk di sini
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
