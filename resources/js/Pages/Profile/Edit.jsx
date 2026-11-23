import React, { useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ProfileEdit({ user, status }) {
    const { flash } = usePage().props;
    const [showDeleteModal, setShowDeleteModal] = useState(false);

    const profileForm = useForm({
        name: user?.name || '',
        email: user?.email || '',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const deleteForm = useForm({
        password: '',
    });

    const handleProfileUpdate = (e) => {
        e.preventDefault();
        profileForm.patch('/profile');
    };

    const handlePasswordUpdate = (e) => {
        e.preventDefault();
        passwordForm.put('/password', {
            onSuccess: () => passwordForm.reset(),
        });
    };

    const handleDeleteAccount = (e) => {
        e.preventDefault();
        deleteForm.delete('/profile');
    };

    const savedBanner = status === 'profile-updated' || flash?.status === 'profile-updated';

    return (
        <AppLayout title="Profil & Keamanan">
            <div className="max-w-3xl mx-auto space-y-6">
                <div>
                    <p className="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">
                        PENGATURAN AKUN
                    </p>
                    <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                        Profil &amp; Keamanan
                    </h1>
                    <p className="text-xs text-[#6a6f7c] mt-1">
                        Perbarui informasi profil, alamat email, dan kata sandi akun Anda.
                    </p>
                </div>

                {/* UPDATE PROFILE */}
                <div className="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
                    <div className="max-w-xl">
                        <h2 className="text-base font-semibold text-[#0b0b0c]">Informasi Profil</h2>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Perbarui nama lengkap dan alamat email akun Anda.
                        </p>

                        <form onSubmit={handleProfileUpdate} className="mt-5 space-y-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    value={profileForm.data.name}
                                    onChange={(e) => profileForm.setData('name', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                                {profileForm.errors.name && (
                                    <p className="text-xs text-[#b91c1c] mt-1">{profileForm.errors.name}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Alamat Email
                                </label>
                                <input
                                    type="email"
                                    value={profileForm.data.email}
                                    onChange={(e) => profileForm.setData('email', e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                                {profileForm.errors.email && (
                                    <p className="text-xs text-[#b91c1c] mt-1">{profileForm.errors.email}</p>
                                )}
                            </div>

                            <div className="flex items-center gap-3 pt-1">
                                <button
                                    type="submit"
                                    disabled={profileForm.processing}
                                    className="px-4 py-2 bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                                >
                                    {profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </button>
                                {(profileForm.recentlySuccessful || savedBanner) && (
                                    <span className="text-xs font-medium text-[#15803d]">Tersimpan.</span>
                                )}
                            </div>
                        </form>
                    </div>
                </div>

                {/* UPDATE PASSWORD */}
                <div className="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
                    <div className="max-w-xl">
                        <h2 className="text-base font-semibold text-[#0b0b0c]">Ubah Kata Sandi</h2>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.
                        </p>

                        <form onSubmit={handlePasswordUpdate} className="mt-5 space-y-4">
                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Kata Sandi Saat Ini
                                </label>
                                <input
                                    type="password"
                                    value={passwordForm.data.current_password}
                                    onChange={(e) =>
                                        passwordForm.setData('current_password', e.target.value)
                                    }
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                                {passwordForm.errors.current_password && (
                                    <p className="text-xs text-[#b91c1c] mt-1">
                                        {passwordForm.errors.current_password}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Kata Sandi Baru
                                </label>
                                <input
                                    type="password"
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                                {passwordForm.errors.password && (
                                    <p className="text-xs text-[#b91c1c] mt-1">
                                        {passwordForm.errors.password}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#0b0b0c] mb-1.5">
                                    Konfirmasi Kata Sandi Baru
                                </label>
                                <input
                                    type="password"
                                    value={passwordForm.data.password_confirmation}
                                    onChange={(e) =>
                                        passwordForm.setData('password_confirmation', e.target.value)
                                    }
                                    className="w-full px-3.5 py-2.5 bg-white border border-[#e4e4e7] rounded-lg text-sm focus:outline-none focus:border-[#013981]"
                                />
                            </div>

                            <div className="flex items-center gap-3 pt-1">
                                <button
                                    type="submit"
                                    disabled={passwordForm.processing}
                                    className="px-4 py-2 bg-[#013981] hover:bg-[#012b63] text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                                >
                                    {passwordForm.processing ? 'Menyimpan...' : 'Perbarui Kata Sandi'}
                                </button>
                                {passwordForm.recentlySuccessful && (
                                    <span className="text-xs font-medium text-[#15803d]">
                                        Kata sandi diperbarui.
                                    </span>
                                )}
                            </div>
                        </form>
                    </div>
                </div>

                {/* DELETE ACCOUNT */}
                <div className="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
                    <div className="max-w-xl">
                        <h2 className="text-base font-semibold text-[#b91c1c]">Hapus Akun</h2>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Setelah akun dihapus, seluruh data terkait akun Anda akan dihapus secara permanen.
                        </p>

                        {!showDeleteModal ? (
                            <button
                                type="button"
                                onClick={() => setShowDeleteModal(true)}
                                className="mt-4 px-4 py-2 bg-red-50 border border-red-200 text-[#b91c1c] hover:bg-red-100 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                            >
                                Hapus Akun
                            </button>
                        ) : (
                            <form onSubmit={handleDeleteAccount} className="mt-4 space-y-3 p-4 bg-red-50/50 border border-red-200 rounded-xl">
                                <label className="block text-xs font-semibold text-[#0b0b0c]">
                                    Masukkan kata sandi Anda untuk konfirmasi penghapusan:
                                </label>
                                <input
                                    type="password"
                                    value={deleteForm.data.password}
                                    onChange={(e) => deleteForm.setData('password', e.target.value)}
                                    required
                                    placeholder="Kata sandi"
                                    className="w-full px-3.5 py-2 bg-white border border-[#e4e4e7] rounded-lg text-sm"
                                />
                                {deleteForm.errors.password && (
                                    <p className="text-xs text-[#b91c1c]">{deleteForm.errors.password}</p>
                                )}
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowDeleteModal(false)}
                                        className="px-3.5 py-2 bg-white border border-[#e4e4e7] text-xs font-semibold rounded-lg cursor-pointer"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={deleteForm.processing}
                                        className="px-3.5 py-2 bg-[#b91c1c] text-white text-xs font-semibold rounded-lg cursor-pointer"
                                    >
                                        Konfirmasi Hapus
                                    </button>
                                </div>
                            </form>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
