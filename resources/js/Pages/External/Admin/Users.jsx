import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalAdminUsers({
    mappedUsers = [],
    companies: initialCompanies = [],
    perusahaans: initialPerusahaans = [],
    dosens: initialDosens = [],
    mentors: initialMentors = [],
}) {
    const parseProp = (val, fallback = []) => {
        if (typeof val === 'string') {
            try {
                return JSON.parse(val);
            } catch {
                return fallback;
            }
        }
        return Array.isArray(val) ? val : fallback;
    };

    const [activeRole, setActiveRole] = useState('mahasiswa');
    const [filterProdi, setFilterProdi] = useState('ALL');
    const [configProdiTarget, setConfigProdiTarget] = useState('TRIN');
    const [searchQuery, setSearchQuery] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const [showPerusahaanModal, setShowPerusahaanModal] = useState(false);
    const [showAssignModal, setShowAssignModal] = useState(false);
    const [showPasswordModal, setShowPasswordModal] = useState(false);

    const [companies, setCompanies] = useState(() => parseProp(initialCompanies));
    const [perusahaans, setPerusahaans] = useState(() => parseProp(initialPerusahaans));
    const dosens = parseProp(initialDosens);
    const mentors = parseProp(initialMentors);
    const [users, setUsers] = useState(() => parseProp(mappedUsers));

    const [activeClasses, setActiveClasses] = useState(() => {
        try {
            const saved = localStorage.getItem('polman_active_classes');
            if (saved) return JSON.parse(saved);
        } catch {}
        return {
            TRIN: ['1', '2', '3', '4'],
            TRO: ['1', '2', '3', '4'],
            TRMO: ['1', '2', '3', '4'],
        };
    });

    const [formUser, setFormUser] = useState({
        id: '',
        name: '',
        identifier: '',
        prodi: 'TRIN',
        kelas: '3AEC1',
        pt: '',
        nidn: '',
        id_pem: '',
        emailPrefix: '',
    });
    const [formPerusahaan, setFormPerusahaan] = useState({
        nama_perusahaan: '',
        alamat: '',
        kontak: '',
    });
    const [formAssign, setFormAssign] = useState({
        nim: '',
        name: '',
        identifier: '',
        nidn: '',
        id_pem: '',
    });
    const [formPassword, setFormPassword] = useState({
        id: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const [visiblePwMap, setVisiblePwMap] = useState({});
    const [showPw1, setShowPw1] = useState(false);
    const [showPw2, setShowPw2] = useState(false);

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const setRole = (role) => {
        setActiveRole(role);
        setFilterProdi('ALL');
        setSearchQuery('');
    };

    const countUsers = (role) => users.filter((u) => u.role === role).length;

    const toggleClassNumber = (prodi, num) => {
        const current = activeClasses[prodi] || [];
        const nextList = current.includes(num)
            ? current.filter((n) => n !== num)
            : [...current, num];
        setActiveClasses({
            ...activeClasses,
            [prodi]: nextList,
        });
    };

    const saveClassConfig = () => {
        localStorage.setItem('polman_active_classes', JSON.stringify(activeClasses));
        alert('Konfigurasi kelas berhasil disimpan!');
    };

    const getGeneratedClassesForProdi = (prodiName) => {
        const p = (prodiName || 'TRIN').toUpperCase();
        let code = 'AEC';
        if (p === 'TRO') code = 'AEB';
        if (p === 'TRMO') code = 'AEA';

        const nums = [...(activeClasses[p] || ['1', '2', '3', '4'])];
        nums.sort((a, b) => Number(a) - Number(b));

        return nums.map((n) => `3${code}${n}`);
    };

    const getGeneratedClasses = () => getGeneratedClassesForProdi(formUser.prodi);

    const onProdiChange = (newProdi) => {
        const generated = getGeneratedClassesForProdi(newProdi);
        setFormUser((prev) => ({
            ...prev,
            prodi: newProdi,
            kelas: generated.length > 0 ? generated[0] : prev.kelas,
        }));
    };

    const getFilteredUsers = () => {
        return users.filter((u) => {
            const matchRole = u.role === activeRole;
            const matchProdi =
                activeRole === 'mahasiswa' && filterProdi !== 'ALL'
                    ? u.prodi === filterProdi
                    : true;
            const query = searchQuery.toLowerCase();
            const matchSearch =
                (u.name || '').toLowerCase().includes(query) ||
                (u.identifier && u.identifier.toLowerCase().includes(query)) ||
                (u.email || '').toLowerCase().includes(query) ||
                (u.pt && u.pt.toLowerCase().includes(query)) ||
                (u.dosen_name && u.dosen_name.toLowerCase().includes(query)) ||
                (u.mentor_name && u.mentor_name.toLowerCase().includes(query));

            return matchRole && matchProdi && matchSearch;
        });
    };

    const getFilteredPerusahaans = () => {
        const query = searchQuery.toLowerCase();
        if (!query) return perusahaans;
        return perusahaans.filter((p) => {
            return (
                (p.nama_perusahaan || '').toLowerCase().includes(query) ||
                (p.alamat && p.alamat.toLowerCase().includes(query)) ||
                (p.kontak && p.kontak.toLowerCase().includes(query))
            );
        });
    };

    const openAddModal = () => {
        setIsEdit(false);
        setFormUser({
            id: '',
            name: '',
            emailPrefix: '',
            identifier: '',
            kelas: '3AEC1',
            pt: companies.length > 0 ? companies[0] : '',
            prodi: 'TRIN',
            nidn: '',
            id_pem: '',
        });
        setShowModal(true);
    };

    const openEditModal = (user) => {
        setIsEdit(true);
        setFormUser({
            id: user.id,
            name: user.name,
            emailPrefix: (user.email || '').split('@')[0],
            identifier: user.identifier
                ? user.identifier.replace('NIM: ', '').replace('NIP: ', '')
                : '',
            kelas: user.kelas || '3AEC1',
            pt: user.pt || (companies.length > 0 ? companies[0] : ''),
            prodi: user.prodi || 'TRIN',
            nidn: user.nidn || '',
            id_pem: user.id_pem || '',
        });
        setShowModal(true);
    };

    const openPerusahaanModal = () => {
        setFormPerusahaan({ nama_perusahaan: '', alamat: '', kontak: '' });
        setShowPerusahaanModal(true);
    };

    const openAssignModal = (user) => {
        setFormAssign({
            nim: user.nim || (user.identifier ? user.identifier.replace('NIM: ', '') : ''),
            name: user.name,
            identifier: user.identifier,
            nidn: user.nidn || '',
            id_pem: user.id_pem || '',
        });
        setShowAssignModal(true);
    };

    const openPasswordModal = (user) => {
        setFormPassword({
            id: user.id,
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
        });
        setShowPw1(false);
        setShowPw2(false);
        setShowPasswordModal(true);
    };

    const savePassword = async (e) => {
        e.preventDefault();
        if (formPassword.password !== formPassword.password_confirmation) {
            alert('Password dan konfirmasi password tidak cocok!');
            return;
        }
        try {
            const response = await fetch(`/external/admin/users/${formPassword.id}/password`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify({ password: formPassword.password }),
            });
            const result = await response.json();
            if (response.ok && result.success) {
                alert(`Password untuk ${formPassword.name} berhasil diubah!`);
                setUsers((prev) =>
                    prev.map((u) =>
                        u.id === formPassword.id
                            ? { ...u, plain_password: formPassword.password }
                            : u
                    )
                );
                setShowPasswordModal(false);
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            alert('Gagal mengubah password.');
            console.error(error);
        }
    };

    const getEmailDomain = () => {
        if (activeRole === 'mahasiswa' || activeRole === 'perusahaan') return '@mhs.polman';
        if (activeRole === 'dosen') return '@dosen.polman';
        if (activeRole === 'mentor') return '@industri.id';
        return '@admin.polman';
    };

    const saveUser = async (e) => {
        e.preventDefault();
        const roleToSave = activeRole === 'perusahaan' ? 'mahasiswa' : activeRole;
        const fullEmail = formUser.emailPrefix + getEmailDomain();

        const payload = {
            role: roleToSave,
            name: formUser.name,
            email: fullEmail,
            identifier: formUser.identifier,
            prodi: roleToSave === 'mahasiswa' ? formUser.prodi : null,
            kelas: roleToSave === 'mahasiswa' ? formUser.kelas : null,
            pt:
                roleToSave === 'mahasiswa' || roleToSave === 'mentor'
                    ? formUser.pt
                    : null,
            nidn: roleToSave === 'mahasiswa' ? formUser.nidn : null,
            id_pem: roleToSave === 'mahasiswa' ? formUser.id_pem : null,
        };

        try {
            const method = isEdit ? 'PUT' : 'POST';
            const url = isEdit
                ? `/external/admin/users/${formUser.id}`
                : '/external/admin/users';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (response.ok && result.success) {
                alert(
                    isEdit
                        ? `Akun ${roleToSave} berhasil diperbarui!`
                        : `Akun ${roleToSave} baru berhasil ditambahkan!`
                );
                window.location.reload();
            } else {
                alert('Gagal menyimpan: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            alert('Gagal menyimpan data.');
            console.error(error);
        }
    };

    const deleteUser = async (id) => {
        if (window.confirm('Yakin ingin menghapus akun ini secara permanen?')) {
            try {
                const response = await fetch(`/external/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    setUsers((prev) => prev.filter((u) => u.id !== id));
                } else {
                    alert('Gagal menghapus: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Gagal menghapus data.');
                console.error(error);
            }
        }
    };

    const savePerusahaan = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch('/external/admin/perusahaan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify(formPerusahaan),
            });

            const result = await response.json();
            if (response.ok && result.success) {
                alert('Perusahaan baru berhasil ditambahkan!');
                if (result.data) {
                    setPerusahaans((prev) => [...prev, result.data]);
                    if (!companies.includes(result.data.nama_perusahaan)) {
                        setCompanies((prev) => [...prev, result.data.nama_perusahaan]);
                    }
                } else {
                    window.location.reload();
                }
                setShowPerusahaanModal(false);
            } else {
                alert('Gagal menyimpan perusahaan: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            alert('Gagal menyimpan data perusahaan.');
            console.error(error);
        }
    };

    const deletePerusahaan = async (id) => {
        if (window.confirm('Yakin ingin menghapus perusahaan ini?')) {
            try {
                const response = await fetch(`/external/admin/perusahaan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    setPerusahaans((prev) => prev.filter((p) => p.id !== id));
                } else {
                    alert('Gagal menghapus: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Gagal menghapus data perusahaan.');
                console.error(error);
            }
        }
    };

    const saveAssignPembimbing = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch('/external/admin/mahasiswa/assign-pembimbing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify({
                    nim: formAssign.nim,
                    nidn: formAssign.nidn,
                    id_pem: formAssign.id_pem,
                }),
            });

            const result = await response.json();
            if (response.ok && result.success) {
                alert('Dosen & Mentor pembimbing berhasil diperbarui!');
                window.location.reload();
            } else {
                alert('Gagal memperbarui pembimbing: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            alert('Gagal mengirim data penugasan pembimbing.');
            console.error(error);
        }
    };

    const filteredUsers = getFilteredUsers();
    const filteredPerusahaans = getFilteredPerusahaans();

    return (
        <AppLayout title="Manajemen Akun & Perusahaan">
            <div className="space-y-6">
                {/* Header Panel */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-[#e4e4e7] pb-4">
                    <div>
                        <h2 className="text-3xl font-black text-blue-900 tracking-wide">
                            Kelola Akun &amp; Perusahaan
                        </h2>
                        <p className="text-gray-600 mt-1 font-medium">
                            Tambah, hapus, filter akun, serta atur pembimbing dosen &amp; mentor untuk mahasiswa.
                        </p>
                    </div>
                    <div className="mt-4 md:mt-0 flex flex-wrap gap-3">
                        <button
                            type="button"
                            onClick={openPerusahaanModal}
                            className="bg-emerald-600 text-white font-bold py-2 px-5 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="3"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0v10"
                                />
                            </svg>
                            + Perusahaan Baru
                        </button>
                        <button
                            type="button"
                            onClick={openAddModal}
                            className="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="3"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            + Tambah Akun
                        </button>
                    </div>
                </div>

                {/* MAIN CARD */}
                <div className="bg-white rounded-xl border border-[#e4e4e7] shadow-surface overflow-hidden flex flex-col md:flex-row min-h-[600px]">
                    {/* SIDEBAR FILTER (Kiri) */}
                    <div className="w-full md:w-72 bg-blue-50 border-r border-[#e4e4e7] p-4 flex flex-col gap-6 flex-shrink-0">
                        {/* Kategori Role Utama */}
                        <div>
                            <p className="text-xs font-black text-blue-900 uppercase tracking-widest mb-3 border-b border-[#e4e4e7] pb-1">
                                Kategori Data
                            </p>
                            <div className="space-y-2">
                                <button
                                    type="button"
                                    onClick={() => setRole('mahasiswa')}
                                    className={`w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center ${
                                        activeRole === 'mahasiswa'
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'bg-white text-gray-600 hover:bg-blue-100'
                                    }`}
                                >
                                    Mahasiswa{' '}
                                    <span className="text-xs bg-black/10 px-2 py-0.5 rounded-full">
                                        {countUsers('mahasiswa')}
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setRole('dosen')}
                                    className={`w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center ${
                                        activeRole === 'dosen'
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'bg-white text-gray-600 hover:bg-blue-100'
                                    }`}
                                >
                                    Dosen{' '}
                                    <span className="text-xs bg-black/10 px-2 py-0.5 rounded-full">
                                        {countUsers('dosen')}
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setRole('mentor')}
                                    className={`w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center ${
                                        activeRole === 'mentor'
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'bg-white text-gray-600 hover:bg-blue-100'
                                    }`}
                                >
                                    Mentor PT{' '}
                                    <span className="text-xs bg-black/10 px-2 py-0.5 rounded-full">
                                        {countUsers('mentor')}
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setRole('admin')}
                                    className={`w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center ${
                                        activeRole === 'admin'
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'bg-white text-gray-600 hover:bg-blue-100'
                                    }`}
                                >
                                    Admin{' '}
                                    <span className="text-xs bg-black/10 px-2 py-0.5 rounded-full">
                                        {countUsers('admin')}
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setRole('perusahaan')}
                                    className={`w-full text-left px-4 py-2 rounded-lg font-bold border border-emerald-200 transition-all flex justify-between items-center ${
                                        activeRole === 'perusahaan'
                                            ? 'bg-emerald-600 text-white shadow-md'
                                            : 'bg-white text-gray-600 hover:bg-emerald-100'
                                    }`}
                                >
                                    Perusahaan / PT{' '}
                                    <span className="text-xs bg-black/10 px-2 py-0.5 rounded-full">
                                        {perusahaans.length}
                                    </span>
                                </button>
                            </div>
                        </div>

                        {/* Sub-Filter & Manajemen Kelas */}
                        {activeRole === 'mahasiswa' && (
                            <div className="space-y-4">
                                <div>
                                    <p className="text-xs font-black text-blue-900 uppercase tracking-widest mb-3 border-b border-[#e4e4e7] pb-1">
                                        Filter Program Studi
                                    </p>
                                    <div className="space-y-2">
                                        {[
                                            ['ALL', 'Semua Prodi'],
                                            ['TRIN', 'TRIN (AEC)'],
                                            ['TRO', 'TRO (AEB)'],
                                            ['TRMO', 'TRMO (AEA)'],
                                        ].map(([val, label]) => (
                                            <label
                                                key={val}
                                                className="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded"
                                            >
                                                <input
                                                    type="radio"
                                                    name="prodi"
                                                    value={val}
                                                    checked={filterProdi === val}
                                                    onChange={() => setFilterProdi(val)}
                                                    className="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span className="font-bold text-sm text-gray-700">
                                                    {label}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                {/* Card Pengaturan Daftar Kelas per Prodi */}
                                <div className="bg-white border-2 border-blue-300 rounded-lg p-3 shadow-sm space-y-2">
                                    <div className="flex justify-between items-center border-b pb-1">
                                        <p className="text-xs font-black text-blue-900 uppercase">
                                            Atur Nomor Kelas
                                        </p>
                                        <select
                                            value={configProdiTarget}
                                            onChange={(e) => setConfigProdiTarget(e.target.value)}
                                            className="text-xs font-bold border rounded px-1 py-0.5 bg-blue-50"
                                        >
                                            <option value="TRIN">TRIN</option>
                                            <option value="TRO">TRO</option>
                                            <option value="TRMO">TRMO</option>
                                        </select>
                                    </div>
                                    <p className="text-[11px] text-gray-500">
                                        Pilih nomor kelas aktif untuk{' '}
                                        <span className="font-bold text-blue-900">
                                            {configProdiTarget}
                                        </span>{' '}
                                        (Tingkat 3):
                                    </p>
                                    <div className="grid grid-cols-4 gap-1">
                                        {['1', '2', '3', '4'].map((num) => (
                                            <label
                                                key={num}
                                                className="flex flex-col items-center justify-center p-1 border rounded bg-gray-50 cursor-pointer hover:bg-blue-50"
                                            >
                                                <span className="text-xs font-bold">{num}</span>
                                                <input
                                                    type="checkbox"
                                                    value={num}
                                                    checked={(
                                                        activeClasses[configProdiTarget] || []
                                                    ).includes(num)}
                                                    onChange={() =>
                                                        toggleClassNumber(configProdiTarget, num)
                                                    }
                                                    className="w-3.5 h-3.5 text-blue-600"
                                                />
                                            </label>
                                        ))}
                                    </div>
                                    <button
                                        type="button"
                                        onClick={saveClassConfig}
                                        className="w-full bg-blue-600 text-white text-xs font-bold py-1.5 rounded hover:bg-blue-700 transition-colors shadow"
                                    >
                                        Simpan Konfigurasi Kelas
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* Fitur Pencarian Cepat */}
                        <div className="mt-auto">
                            <p className="text-xs font-black text-blue-900 uppercase tracking-widest mb-2">
                                Pencarian
                            </p>
                            <div className="relative">
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder="Cari nama/identitas..."
                                    className="w-full pl-8 pr-3 py-2 text-sm font-bold border-2 border-blue-300 rounded-lg outline-none focus:border-blue-600 bg-white"
                                />
                                <svg
                                    className="w-4 h-4 text-gray-400 absolute left-2.5 top-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {/* AREA TABEL (Kanan) */}
                    <div className="flex-1 p-0 overflow-hidden flex flex-col">
                        <div
                            className={`p-4 flex justify-between items-center text-white transition-colors ${
                                activeRole === 'perusahaan' ? 'bg-emerald-800' : 'bg-blue-900'
                            }`}
                        >
                            <h3 className="text-xl font-black uppercase">
                                {activeRole === 'perusahaan'
                                    ? 'Daftar Perusahaan / Industri'
                                    : 'Daftar ' + activeRole}
                            </h3>
                            <span className="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">
                                {activeRole === 'perusahaan'
                                    ? `${filteredPerusahaans.length} Data Ditampilkan`
                                    : `${filteredUsers.length} Data Ditampilkan`}
                            </span>
                        </div>

                        <div className="overflow-y-auto flex-1 p-4 bg-white">
                            {/* TABEL AKUN USER */}
                            {activeRole !== 'perusahaan' ? (
                                <table className="w-full text-left border-collapse">
                                    <thead>
                                        <tr className="border-b border-[#e4e4e7] text-gray-600">
                                            <th className="p-3 font-black text-sm uppercase">
                                                Nama / Identitas
                                            </th>
                                            <th className="p-3 font-black text-sm uppercase">
                                                Email Login
                                            </th>
                                            <th className="p-3 font-black text-sm uppercase">
                                                Password
                                            </th>
                                            {activeRole === 'mahasiswa' && (
                                                <th className="p-3 font-black text-sm uppercase whitespace-nowrap">
                                                    Prodi &amp; Kelas
                                                </th>
                                            )}
                                            {activeRole === 'mahasiswa' && (
                                                <th className="p-3 font-black text-sm uppercase">
                                                    Dosen Pembimbing
                                                </th>
                                            )}
                                            {(activeRole === 'mahasiswa' ||
                                                activeRole === 'mentor') && (
                                                <th className="p-3 font-black text-sm uppercase">
                                                    Mentor / Penempatan PT
                                                </th>
                                            )}
                                            <th className="p-3 font-black text-sm uppercase text-center w-28">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filteredUsers.length === 0 ? (
                                            <tr>
                                                <td
                                                    colSpan={
                                                        activeRole === 'mahasiswa'
                                                            ? 7
                                                            : activeRole === 'mentor'
                                                              ? 5
                                                              : 4
                                                    }
                                                    className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-200 mt-4 rounded-xl"
                                                >
                                                    Data tidak ditemukan.
                                                </td>
                                            </tr>
                                        ) : (
                                            filteredUsers.map((user) => {
                                                const showPw = !!visiblePwMap[user.id];
                                                return (
                                                    <tr
                                                        key={user.id}
                                                        className="border-b border-gray-100 hover:bg-blue-50 transition-colors"
                                                    >
                                                        <td className="p-3">
                                                            <p className="font-black text-blue-900 text-base">
                                                                {user.name}
                                                            </p>
                                                            {user.identifier && (
                                                                <p className="text-xs font-bold text-gray-500 uppercase">
                                                                    {user.identifier}
                                                                </p>
                                                            )}
                                                        </td>
                                                        <td className="p-3 font-bold text-gray-700 text-sm">
                                                            {user.email}
                                                        </td>

                                                        {/* Kolom Password */}
                                                        <td className="p-3">
                                                            <div className="flex items-center gap-1.5">
                                                                <span
                                                                    className={`font-mono text-xs font-bold ${
                                                                        showPw
                                                                            ? 'text-gray-800'
                                                                            : 'text-gray-400'
                                                                    }`}
                                                                >
                                                                    {showPw
                                                                        ? user.plain_password || '—'
                                                                        : '••••••••'}
                                                                </span>
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        setVisiblePwMap((prev) => ({
                                                                            ...prev,
                                                                            [user.id]: !prev[user.id],
                                                                        }))
                                                                    }
                                                                    className="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded hover:bg-gray-100"
                                                                    title="Lihat/Sembunyikan Password"
                                                                >
                                                                    {!showPw ? (
                                                                        <svg
                                                                            className="w-4 h-4"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            viewBox="0 0 24 24"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                                            />
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                                            />
                                                                        </svg>
                                                                    ) : (
                                                                        <svg
                                                                            className="w-4 h-4"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            viewBox="0 0 24 24"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                                                            />
                                                                        </svg>
                                                                    )}
                                                                </button>
                                                            </div>
                                                        </td>

                                                        {activeRole === 'mahasiswa' && (
                                                            <td className="p-3 whitespace-nowrap">
                                                                <span className="inline-block bg-blue-100 text-blue-800 border border-blue-300 px-2.5 py-1 rounded text-xs font-black shadow-sm">
                                                                    {user.prodi} - {user.kelas}
                                                                </span>
                                                            </td>
                                                        )}

                                                        {activeRole === 'mahasiswa' && (
                                                            <td className="p-3">
                                                                <span
                                                                    className={`border px-2 py-1 rounded text-xs font-bold whitespace-nowrap ${
                                                                        user.dosen_name
                                                                            ? 'bg-purple-100 border-purple-300 text-purple-900'
                                                                            : 'bg-gray-100 border-gray-300 text-gray-400 italic'
                                                                    }`}
                                                                >
                                                                    {user.dosen_name ||
                                                                        'Belum Ditentukan'}
                                                                </span>
                                                            </td>
                                                        )}

                                                        {(activeRole === 'mahasiswa' ||
                                                            activeRole === 'mentor') && (
                                                            <td className="p-3">
                                                                {activeRole === 'mahasiswa' ? (
                                                                    <div>
                                                                        <p className="font-bold text-gray-800 text-xs">
                                                                            {user.pt ||
                                                                                'PT Belum Set'}
                                                                        </p>
                                                                        {user.mentor_name && (
                                                                            <p className="text-[11px] text-amber-700 font-semibold">
                                                                                Mentor:{' '}
                                                                                {user.mentor_name}
                                                                            </p>
                                                                        )}
                                                                    </div>
                                                                ) : (
                                                                    <span className="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold whitespace-nowrap">
                                                                        {user.pt ||
                                                                            'Belum Ditetapkan'}
                                                                    </span>
                                                                )}
                                                            </td>
                                                        )}

                                                        <td className="p-3 text-center">
                                                            <div className="flex items-center justify-center gap-1">
                                                                {activeRole === 'mahasiswa' && (
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            openAssignModal(user)
                                                                        }
                                                                        className="text-blue-700 hover:text-blue-900 p-2 bg-blue-100 border border-blue-300 rounded hover:bg-blue-200 transition-colors"
                                                                        title="Atur Pembimbing Dosen & Mentor"
                                                                    >
                                                                        <svg
                                                                            className="w-4 h-4"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            viewBox="0 0 24 24"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                                                            />
                                                                        </svg>
                                                                    </button>
                                                                )}
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        openEditModal(user)
                                                                    }
                                                                    className="text-yellow-600 hover:text-yellow-800 p-2 bg-yellow-50 border border-yellow-200 rounded hover:bg-yellow-100 transition-colors"
                                                                    title="Edit Akun"
                                                                >
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth="2"
                                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        openPasswordModal(user)
                                                                    }
                                                                    className="text-purple-600 hover:text-purple-800 p-2 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100 transition-colors"
                                                                    title="Ubah Password"
                                                                >
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth="2"
                                                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        deleteUser(user.id)
                                                                    }
                                                                    className="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors"
                                                                    title="Hapus Akun"
                                                                >
                                                                    <svg
                                                                        className="w-4 h-4"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        )}
                                    </tbody>
                                </table>
                            ) : (
                                /* TABEL PERUSAHAAN */
                                <table className="w-full text-left border-collapse">
                                    <thead>
                                        <tr className="border-b border-[#e4e4e7] text-gray-600">
                                            <th className="p-3 font-black text-sm uppercase">
                                                Nama Perusahaan / PT
                                            </th>
                                            <th className="p-3 font-black text-sm uppercase">
                                                Alamat
                                            </th>
                                            <th className="p-3 font-black text-sm uppercase">
                                                Kontak
                                            </th>
                                            <th className="p-3 font-black text-sm uppercase text-center w-24">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filteredPerusahaans.length === 0 ? (
                                            <tr>
                                                <td
                                                    colSpan={4}
                                                    className="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-200 mt-4 rounded-xl"
                                                >
                                                    Belum ada data perusahaan. Klik tombol &quot;+
                                                    Perusahaan Baru&quot; untuk membuat.
                                                </td>
                                            </tr>
                                        ) : (
                                            filteredPerusahaans.map((p) => (
                                                <tr
                                                    key={p.id}
                                                    className="border-b border-gray-100 hover:bg-emerald-50 transition-colors"
                                                >
                                                    <td className="p-3 font-black text-emerald-900 text-base">
                                                        {p.nama_perusahaan}
                                                    </td>
                                                    <td className="p-3 font-bold text-gray-700 text-sm">
                                                        {p.alamat || '-'}
                                                    </td>
                                                    <td className="p-3 font-bold text-gray-700 text-sm">
                                                        {p.kontak || '-'}
                                                    </td>
                                                    <td className="p-3 text-center">
                                                        <button
                                                            type="button"
                                                            onClick={() => deletePerusahaan(p.id)}
                                                            className="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors"
                                                        >
                                                            <svg
                                                                className="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))
                                        )}
                                    </tbody>
                                </table>
                            )}
                        </div>
                    </div>
                </div>

                {/* MODAL TAMBAH & EDIT AKUN */}
                {showModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={() => setShowModal(false)}
                            />

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border border-[#e4e4e7] rounded-xl">
                                <div className="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        {(isEdit ? 'Edit Akun ' : 'Tambah Akun ') +
                                            (activeRole === 'perusahaan'
                                                ? 'Mahasiswa'
                                                : activeRole)}
                                    </h3>
                                    <button
                                        type="button"
                                        onClick={() => setShowModal(false)}
                                        className="hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            className="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <form onSubmit={saveUser} className="p-6 bg-gray-50 space-y-4">
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            {activeRole === 'mentor'
                                                ? 'Nama Industri / Perusahaan'
                                                : 'Nama Lengkap'}
                                        </label>
                                        <input
                                            type="text"
                                            value={formUser.name}
                                            onChange={(e) =>
                                                setFormUser({ ...formUser, name: e.target.value })
                                            }
                                            required
                                            className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600"
                                            placeholder="Masukkan nama..."
                                        />
                                    </div>

                                    {/* Input Khusus Mahasiswa */}
                                    {(activeRole === 'mahasiswa' ||
                                        activeRole === 'perusahaan') && (
                                        <div className="space-y-4">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="col-span-2">
                                                    <label className="block text-sm font-bold text-gray-700 mb-1">
                                                        NIM Mahasiswa
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={formUser.identifier}
                                                        onChange={(e) =>
                                                            setFormUser({
                                                                ...formUser,
                                                                identifier: e.target.value,
                                                            })
                                                        }
                                                        required
                                                        className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600"
                                                        placeholder="Contoh: 223443026"
                                                    />
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-bold text-gray-700 mb-1">
                                                        Program Studi
                                                    </label>
                                                    <select
                                                        value={formUser.prodi}
                                                        onChange={(e) =>
                                                            onProdiChange(e.target.value)
                                                        }
                                                        required
                                                        className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white"
                                                    >
                                                        <option value="" disabled>
                                                            Pilih Prodi
                                                        </option>
                                                        <option value="TRIN">TRIN</option>
                                                        <option value="TRO">TRO</option>
                                                        <option value="TRMO">TRMO</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-bold text-gray-700 mb-1">
                                                        Pilih Kelas
                                                    </label>
                                                    <select
                                                        value={formUser.kelas}
                                                        onChange={(e) =>
                                                            setFormUser({
                                                                ...formUser,
                                                                kelas: e.target.value,
                                                            })
                                                        }
                                                        required
                                                        className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white"
                                                    >
                                                        <option value="" disabled>
                                                            -- Pilih Kelas --
                                                        </option>
                                                        {getGeneratedClasses().map((cls) => (
                                                            <option key={cls} value={cls}>
                                                                {cls}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>
                                            </div>

                                            {/* Pilihan Dosen Pembimbing */}
                                            <div>
                                                <label className="block text-sm font-bold text-gray-700 mb-1">
                                                    Dosen Pembimbing (Opsional)
                                                </label>
                                                <select
                                                    value={formUser.nidn}
                                                    onChange={(e) =>
                                                        setFormUser({
                                                            ...formUser,
                                                            nidn: e.target.value,
                                                        })
                                                    }
                                                    className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white"
                                                >
                                                    <option value="">-- Belum Ditentukan --</option>
                                                    {dosens.map((d) => (
                                                        <option key={d.nidn} value={d.nidn}>
                                                            {d.nama_dosen} ({d.nidn})
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>

                                            {/* Pilihan Mentor Industri */}
                                            <div>
                                                <label className="block text-sm font-bold text-gray-700 mb-1">
                                                    Mentor Industri (Opsional)
                                                </label>
                                                <select
                                                    value={formUser.id_pem}
                                                    onChange={(e) =>
                                                        setFormUser({
                                                            ...formUser,
                                                            id_pem: e.target.value,
                                                        })
                                                    }
                                                    className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white"
                                                >
                                                    <option value="">-- Belum Ditentukan --</option>
                                                    {mentors.map((m) => (
                                                        <option key={m.id_pem} value={m.id_pem}>
                                                            {m.nama_pem} - {m.perusahaan}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>
                                        </div>
                                    )}

                                    {/* Input Khusus Dosen */}
                                    {activeRole === 'dosen' && (
                                        <div>
                                            <label className="block text-sm font-bold text-gray-700 mb-1">
                                                NIP Dosen
                                            </label>
                                            <input
                                                type="text"
                                                value={formUser.identifier}
                                                onChange={(e) =>
                                                    setFormUser({
                                                        ...formUser,
                                                        identifier: e.target.value,
                                                    })
                                                }
                                                required
                                                className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600"
                                                placeholder="Masukkan NIP..."
                                            />
                                        </div>
                                    )}

                                    {/* Input Penempatan PT */}
                                    {(activeRole === 'mahasiswa' ||
                                        activeRole === 'mentor' ||
                                        activeRole === 'perusahaan') && (
                                        <div>
                                            <label className="block text-sm font-bold text-gray-700 mb-1">
                                                {activeRole === 'mentor'
                                                    ? 'Penempatan Sebagai Mentor di PT'
                                                    : 'Penempatan Magang (PT)'}
                                            </label>
                                            <select
                                                value={formUser.pt}
                                                onChange={(e) =>
                                                    setFormUser({ ...formUser, pt: e.target.value })
                                                }
                                                required
                                                className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white"
                                            >
                                                <option value="" disabled>
                                                    -- Pilih Perusahaan dari Database --
                                                </option>
                                                {companies.map((comp) => (
                                                    <option key={comp} value={comp}>
                                                        {comp}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    )}

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Email Login
                                        </label>
                                        <div className="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors bg-white">
                                            <input
                                                type="text"
                                                value={formUser.emailPrefix}
                                                onChange={(e) =>
                                                    setFormUser({
                                                        ...formUser,
                                                        emailPrefix: e.target.value,
                                                    })
                                                }
                                                required
                                                className="w-full p-2 font-black outline-none"
                                                placeholder="username"
                                            />
                                            <span className="bg-gray-100 px-3 py-2 text-sm font-bold text-gray-600 border-l-2 border-gray-300 whitespace-nowrap">
                                                {getEmailDomain()}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="pt-4 border-t border-[#e4e4e7] flex justify-end">
                                        <button
                                            type="submit"
                                            className="bg-[#013981] hover:bg-[#012b63] text-white font-medium px-6 py-2 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                                        >
                                            <span>
                                                {isEdit ? 'Simpan Perubahan' : 'Simpan Akun Baru'}
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}

                {/* MODAL ATUR PEMBIMBING */}
                {showAssignModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={() => setShowAssignModal(false)}
                            />

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border border-[#e4e4e7] rounded-xl">
                                <div className="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                                    <div>
                                        <h3 className="text-xl font-black uppercase">
                                            Atur Pembimbing Mahasiswa
                                        </h3>
                                        <p className="text-xs text-blue-200 font-medium">
                                            {formAssign.name} ({formAssign.identifier})
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setShowAssignModal(false)}
                                        className="hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            className="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <form
                                    onSubmit={saveAssignPembimbing}
                                    className="p-6 bg-gray-50 space-y-4"
                                >
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Dosen Pembimbing
                                        </label>
                                        <select
                                            value={formAssign.nidn}
                                            onChange={(e) =>
                                                setFormAssign({
                                                    ...formAssign,
                                                    nidn: e.target.value,
                                                })
                                            }
                                            className="w-full border-2 border-gray-300 rounded p-2.5 font-bold outline-none focus:border-blue-600 bg-white"
                                        >
                                            <option value="">-- Belum Ditentukan --</option>
                                            {dosens.map((d) => (
                                                <option key={d.nidn} value={d.nidn}>
                                                    {d.nama_dosen} (NIP: {d.nidn})
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Mentor Industri (Pembimbing Lapangan)
                                        </label>
                                        <select
                                            value={formAssign.id_pem}
                                            onChange={(e) =>
                                                setFormAssign({
                                                    ...formAssign,
                                                    id_pem: e.target.value,
                                                })
                                            }
                                            className="w-full border-2 border-gray-300 rounded p-2.5 font-bold outline-none focus:border-blue-600 bg-white"
                                        >
                                            <option value="">-- Belum Ditentukan --</option>
                                            {mentors.map((m) => (
                                                <option key={m.id_pem} value={m.id_pem}>
                                                    {m.nama_pem} ({m.perusahaan})
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="pt-4 border-t border-[#e4e4e7] flex justify-end gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setShowAssignModal(false)}
                                            className="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg border border-gray-300 hover:bg-gray-300 transition-colors"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            className="bg-blue-600 text-white font-black px-6 py-2 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                                        >
                                            Simpan Pembimbing
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}

                {/* MODAL UBAH PASSWORD */}
                {showPasswordModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-password-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={() => setShowPasswordModal(false)}
                            />

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-md w-full border border-[#e4e4e7] rounded-xl">
                                <div className="bg-purple-900 px-6 py-4 flex justify-between items-center text-white">
                                    <div>
                                        <h3 className="text-xl font-black uppercase">
                                            Ubah Password
                                        </h3>
                                        <p className="text-xs text-purple-200 font-medium">
                                            {formPassword.name} ({formPassword.email})
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setShowPasswordModal(false)}
                                        className="hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            className="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <form
                                    onSubmit={savePassword}
                                    className="p-6 bg-gray-50 space-y-4"
                                >
                                    <div className="bg-purple-50 border-2 border-purple-200 rounded-lg p-3 text-sm text-purple-800 font-bold">
                                        <svg
                                            className="w-4 h-4 inline mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        Password baru akan langsung aktif setelah disimpan.
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Password Baru <span className="text-red-500">*</span>
                                        </label>
                                        <div className="relative">
                                            <input
                                                type={showPw1 ? 'text' : 'password'}
                                                value={formPassword.password}
                                                onChange={(e) =>
                                                    setFormPassword({
                                                        ...formPassword,
                                                        password: e.target.value,
                                                    })
                                                }
                                                required
                                                minLength={1}
                                                className="w-full border-2 border-gray-300 rounded p-2 pr-10 font-bold outline-none focus:border-purple-600"
                                                placeholder="Masukkan password baru (cth: 123)..."
                                            />
                                            <button
                                                type="button"
                                                onClick={() => setShowPw1(!showPw1)}
                                                className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-colors"
                                            >
                                                {!showPw1 ? (
                                                    <svg
                                                        className="w-5 h-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                        />
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                        />
                                                    </svg>
                                                ) : (
                                                    <svg
                                                        className="w-5 h-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                                        />
                                                    </svg>
                                                )}
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Konfirmasi Password{' '}
                                            <span className="text-red-500">*</span>
                                        </label>
                                        <div className="relative">
                                            <input
                                                type={showPw2 ? 'text' : 'password'}
                                                value={formPassword.password_confirmation}
                                                onChange={(e) =>
                                                    setFormPassword({
                                                        ...formPassword,
                                                        password_confirmation: e.target.value,
                                                    })
                                                }
                                                required
                                                minLength={1}
                                                className="w-full border-2 border-gray-300 rounded p-2 pr-10 font-bold outline-none focus:border-purple-600"
                                                placeholder="Ulangi password baru (cth: 123)..."
                                            />
                                            <button
                                                type="button"
                                                onClick={() => setShowPw2(!showPw2)}
                                                className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-colors"
                                            >
                                                {!showPw2 ? (
                                                    <svg
                                                        className="w-5 h-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                        />
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                        />
                                                    </svg>
                                                ) : (
                                                    <svg
                                                        className="w-5 h-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                                        />
                                                    </svg>
                                                )}
                                            </button>
                                        </div>
                                    </div>

                                    <div className="pt-4 border-t border-[#e4e4e7] flex justify-end gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setShowPasswordModal(false)}
                                            className="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg border border-gray-300 hover:bg-gray-300 transition-colors"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            className="bg-purple-600 text-white font-black px-6 py-2 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                                        >
                                            Simpan Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}

                {/* MODAL TAMBAH PERUSAHAAN */}
                {showPerusahaanModal && (
                    <div
                        className="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <div
                                className="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                                onClick={() => setShowPerusahaanModal(false)}
                            />

                            <div className="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border border-[#e4e4e7] rounded-xl">
                                <div className="bg-emerald-900 px-6 py-4 flex justify-between items-center text-white">
                                    <h3 className="text-xl font-black uppercase">
                                        Tambah Perusahaan Baru
                                    </h3>
                                    <button
                                        type="button"
                                        onClick={() => setShowPerusahaanModal(false)}
                                        className="hover:text-red-400 transition-colors"
                                    >
                                        <svg
                                            className="w-8 h-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <form
                                    onSubmit={savePerusahaan}
                                    className="p-6 bg-gray-50 space-y-4"
                                >
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Nama Perusahaan / PT *
                                        </label>
                                        <input
                                            type="text"
                                            value={formPerusahaan.nama_perusahaan}
                                            onChange={(e) =>
                                                setFormPerusahaan({
                                                    ...formPerusahaan,
                                                    nama_perusahaan: e.target.value,
                                                })
                                            }
                                            required
                                            className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600"
                                            placeholder="Contoh: PT Astra Honda Motor"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Alamat Industri
                                        </label>
                                        <textarea
                                            value={formPerusahaan.alamat}
                                            onChange={(e) =>
                                                setFormPerusahaan({
                                                    ...formPerusahaan,
                                                    alamat: e.target.value,
                                                })
                                            }
                                            rows={2}
                                            className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600"
                                            placeholder="Kota / Alamat lengkap perusahaan..."
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">
                                            Kontak / No. Telepon / Email
                                        </label>
                                        <input
                                            type="text"
                                            value={formPerusahaan.kontak}
                                            onChange={(e) =>
                                                setFormPerusahaan({
                                                    ...formPerusahaan,
                                                    kontak: e.target.value,
                                                })
                                            }
                                            className="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600"
                                            placeholder="Contoh: 021-1234567 / hr@astra.co.id"
                                        />
                                    </div>

                                    <div className="pt-4 border-t border-[#e4e4e7] flex justify-end">
                                        <button
                                            type="submit"
                                            className="bg-emerald-600 text-white font-black px-6 py-2 rounded-lg border-2 border-emerald-950 shadow-surface hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all"
                                        >
                                            Simpan Perusahaan Baru
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
