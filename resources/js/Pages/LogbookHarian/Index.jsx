import React, { useState, useMemo, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const addDays = (dateStr, days) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length !== 3) return '';
    const dt = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    dt.setDate(dt.getDate() + days);
    const y = dt.getFullYear();
    const m = String(dt.getMonth() + 1).padStart(2, '0');
    const day = String(dt.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const getDayName = (dateStr, fallbackIndex = 0) => {
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    if (!dateStr) {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][fallbackIndex] || 'Senin';
    }
    const parts = dateStr.split('-');
    const date = new Date(
        parseInt(parts[0], 10),
        parseInt(parts[1], 10) - 1,
        parseInt(parts[2], 10)
    );
    return dayNames[date.getDay()];
};

const formatDisplayDate = (dateStr) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    const date = new Date(
        parseInt(parts[0], 10),
        parseInt(parts[1], 10) - 1,
        parseInt(parts[2], 10)
    );
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const getWeekDayCount = (startDate, endDate) => {
    if (!startDate || !endDate) return 5;
    const start = new Date(`${startDate}T00:00:00`);
    const end = new Date(`${endDate}T00:00:00`);
    const count = Math.round((end - start) / 86400000) + 1;
    return count >= 5 && count <= 7 ? count : 5;
};

const calcMinutes = (jamMulai, jamSelesai) => {
    if (!jamMulai || !jamSelesai) return 0;
    const [h1, m1] = jamMulai.split(':').map(Number);
    const [h2, m2] = jamSelesai.split(':').map(Number);
    let diff = h2 * 60 + m2 - (h1 * 60 + m1);
    if (diff < 0) diff += 24 * 60;
    return diff;
};

const emptyActivity = () => ({
    jamMulai: '',
    jamSelesai: '',
    kegiatan: '',
    waktu: 0,
});

const createEmptyDay = (index, dateStr = '') => ({
    id: index,
    date: dateStr,
    name: getDayName(dateStr, index),
    status: 'Kerja',
    notes: '',
    kendala: '',
    status_verifikasi: null,
    catatan_dosen: null,
    expanded: false,
    activities: [emptyActivity()],
    fotos: [],
    fotoPreviews: [],
    fotoFiles: [],
    uploadingFoto: false,
});

const getPeriodeWeekCount = (periode, existingEntries = []) => {
    if (periode?.tanggal_mulai && periode?.tanggal_selesai) {
        const start = new Date(`${String(periode.tanggal_mulai).substring(0, 10)}T00:00:00`);
        const end = new Date(`${String(periode.tanggal_selesai).substring(0, 10)}T00:00:00`);
        const totalDays = Math.floor((end - start) / 86400000) + 1;
        if (totalDays > 0) {
            return Math.max(1, Math.ceil(totalDays / 7));
        }
    }
    const maxEntryWeek = Array.isArray(existingEntries)
        ? existingEntries.reduce((max, e) => Math.max(max, Number(e.minggu_ke || 0)), 0)
        : 0;
    return Math.max(1, maxEntryWeek || 16);
};

const clampDate = (dateStr, maxDateStr) => {
    if (!dateStr || !maxDateStr) return dateStr;
    return dateStr > maxDateStr ? maxDateStr : dateStr;
};

const buildInitialWeeks = (existingEntries, proyekId, currentNim, existingFotosByDate, periode) => {
    let savedWeekDates = {};
    if (currentNim && proyekId) {
        try {
            savedWeekDates = JSON.parse(
                localStorage.getItem(`logbook_internal_dates_${currentNim}_${proyekId}`) || '{}'
            );
        } catch {}
    }

    const filteredEntries = Array.isArray(existingEntries)
        ? existingEntries.filter((e) => String(e.pengajuan_proyek_id) === String(proyekId))
        : [];

    const totalWeeks = getPeriodeWeekCount(periode, filteredEntries);
    const periodeStart = periode?.tanggal_mulai ? String(periode.tanggal_mulai).substring(0, 10) : '';
    const periodeEnd = periode?.tanggal_selesai ? String(periode.tanggal_selesai).substring(0, 10) : '';

    const entriesByDate = {};
    const weekStartFromEntry = {};
    const weekEndFromEntry = {};

    filteredEntries.forEach((item) => {
        const tgl = item.tanggal ? String(item.tanggal).substring(0, 10) : '';
        if (!tgl) return;
        if (!entriesByDate[tgl]) entriesByDate[tgl] = [];
        entriesByDate[tgl].push(item);

        if (item.minggu_ke) {
            if (!weekStartFromEntry[item.minggu_ke] || tgl < weekStartFromEntry[item.minggu_ke]) {
                weekStartFromEntry[item.minggu_ke] = tgl;
            }
            if (!weekEndFromEntry[item.minggu_ke] || tgl > weekEndFromEntry[item.minggu_ke]) {
                weekEndFromEntry[item.minggu_ke] = tgl;
            }
        }
    });

    const weeksArr = [];
    for (let i = 1; i <= totalWeeks; i++) {
        const defaultStart = periodeStart ? addDays(periodeStart, (i - 1) * 7) : '';
        const defaultEnd = defaultStart ? clampDate(addDays(defaultStart, 4), periodeEnd) : '';

        const weekStartDate =
            savedWeekDates[i]?.startDate || weekStartFromEntry[i] || defaultStart;
        let weekEndDate =
            savedWeekDates[i]?.endDate ||
            weekEndFromEntry[i] ||
            defaultEnd ||
            (weekStartDate ? clampDate(addDays(weekStartDate, 4), periodeEnd) : '');

        let dayCount = 5;
        if (weekStartDate && weekEndDate) {
            const start = new Date(`${weekStartDate}T00:00:00`);
            const end = new Date(`${weekEndDate}T00:00:00`);
            const diff = Math.round((end - start) / 86400000) + 1;
            dayCount = diff >= 1 && diff <= 7 ? diff : 5;
        }

        const daysArr = [];
        for (let d = 0; d < dayCount; d++) {
            const dayDateStr = weekStartDate ? addDays(weekStartDate, d) : '';
            let dayStatus = 'Kerja';
            let dayNotes = '';
            let dayKendala = '';
            let dayVerif = null;
            let dayCatatan = null;
            const dayActivities = [];

            if (dayDateStr && entriesByDate[dayDateStr]) {
                const items = entriesByDate[dayDateStr];
                if (items.length > 0) {
                    dayStatus = items[0].status || 'Kerja';
                    dayKendala = items[0].kendala || '';
                    dayVerif = items[0].status_verifikasi || 'pending';
                    dayCatatan = items[0].catatan_dosen || null;

                    if (dayStatus !== 'Kerja') {
                        dayNotes = items[0].aktivitas || '';
                    } else {
                        items.forEach((e) => {
                            const jm = e.jam_masuk ? String(e.jam_masuk).substring(0, 5) : '';
                            const jk = e.jam_keluar ? String(e.jam_keluar).substring(0, 5) : '';
                            const dur = Number(e.durasi_mnt || 0) || calcMinutes(jm, jk);
                            dayActivities.push({
                                jamMulai: jm,
                                jamSelesai: jk,
                                kegiatan: e.aktivitas || '',
                                waktu: dur,
                            });
                        });
                    }
                }
            }

            if (dayActivities.length === 0) {
                dayActivities.push(emptyActivity());
            }

            const fotoKey = `${proyekId}_${dayDateStr}`;
            const dayFotos =
                dayDateStr && existingFotosByDate && existingFotosByDate[fotoKey]
                    ? existingFotosByDate[fotoKey]
                    : [];

            daysArr.push({
                id: d,
                date: dayDateStr,
                name: getDayName(dayDateStr, d),
                status: dayStatus,
                notes: dayNotes,
                kendala: dayKendala,
                status_verifikasi: dayVerif,
                catatan_dosen: dayCatatan,
                expanded: false,
                activities: dayActivities,
                fotos: dayFotos,
                fotoPreviews: [],
                fotoFiles: [],
                uploadingFoto: false,
            });
        }

        const hasData = daysArr.some(
            (day) =>
                day.status !== 'Kerja' ||
                day.status_verifikasi !== null ||
                (day.notes && day.notes.trim() !== '') ||
                day.activities.some((a) => a.waktu > 0 || (a.kegiatan && a.kegiatan.trim() !== ''))
        );

        weeksArr.push({
            id: i,
            expanded: i === 1 || hasData,
            startDate: weekStartDate,
            endDate: weekEndDate,
            days: daysArr,
        });
    }

    return weeksArr;
};

export default function LogbookHarianIndex({
    entries = [],
    proyek = [],
    periode = null,
    fotosByDate = {},
    mahasiswa = null,
}) {
    const currentNim = mahasiswa?.nim || mahasiswa?.id || 'mhs';
    const [selectedProyekId, setSelectedProyekId] = useState(
        proyek.length > 0 ? String(proyek[0].id) : ''
    );
    const [weeks, setWeeks] = useState(() =>
        buildInitialWeeks(
            entries,
            proyek.length > 0 ? String(proyek[0].id) : '',
            currentNim,
            fotosByDate,
            periode
        )
    );

    useEffect(() => {
        setWeeks(buildInitialWeeks(entries, selectedProyekId, currentNim, fotosByDate, periode));
    }, [selectedProyekId, entries, periode]);

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const saveWeekDatesToStorage = (weeksList) => {
        if (!currentNim || !selectedProyekId) return;
        const map = {};
        weeksList.forEach((w) => {
            if (w.startDate) {
                map[w.id] = { startDate: w.startDate, endDate: w.endDate };
            }
        });
        try {
            localStorage.setItem(
                `logbook_internal_dates_${currentNim}_${selectedProyekId}`,
                JSON.stringify(map)
            );
        } catch {}
    };

    const updateWeek = (wIndex, updater) => {
        setWeeks((prev) => prev.map((w, idx) => (idx === wIndex ? updater(w) : w)));
    };

    const updateDay = (wIndex, dIndex, updater) => {
        setWeeks((prev) =>
            prev.map((w, wi) => {
                if (wi !== wIndex) return w;
                return {
                    ...w,
                    days: w.days.map((d, di) => (di === dIndex ? updater(d) : d)),
                };
            })
        );
    };

    const onWeekDateChange = (wIndex, newStartDate) => {
        setWeeks((prev) => {
            const next = prev.map((w, idx) => {
                if (idx !== wIndex) return w;
                if (!newStartDate) return { ...w, startDate: '' };
                const dayCount = getWeekDayCount(newStartDate, w.endDate);
                const endDate = addDays(newStartDate, dayCount - 1);
                const newDays = Array.from({ length: dayCount }, (_, d) => {
                    const dateStr = addDays(newStartDate, d);
                    const existing = w.days[d] || createEmptyDay(d, dateStr);
                    return {
                        ...existing,
                        id: d,
                        date: dateStr,
                        name: getDayName(dateStr, d),
                    };
                });
                return { ...w, startDate: newStartDate, endDate, days: newDays };
            });
            saveWeekDatesToStorage(next);
            return next;
        });
    };

    const onWeekEndDateChange = (wIndex, newEndDate) => {
        setWeeks((prev) => {
            const next = prev.map((w, idx) => {
                if (idx !== wIndex) return w;
                if (!w.startDate || !newEndDate) return { ...w, endDate: newEndDate };
                const start = new Date(`${w.startDate}T00:00:00`);
                const end = new Date(`${newEndDate}T00:00:00`);
                const dayCount = Math.round((end - start) / 86400000) + 1;
                if (dayCount < 5 || dayCount > 7) {
                    alert('Rentang tanggal per minggu harus antara 5 sampai 7 hari.');
                    return { ...w, endDate: addDays(w.startDate, w.days.length - 1) };
                }
                const newDays = Array.from({ length: dayCount }, (_, d) => {
                    const dateStr = addDays(w.startDate, d);
                    const existing = w.days[d] || createEmptyDay(d, dateStr);
                    return {
                        ...existing,
                        id: d,
                        date: dateStr,
                        name: getDayName(dateStr, d),
                    };
                });
                return { ...w, endDate: newEndDate, days: newDays };
            });
            saveWeekDatesToStorage(next);
            return next;
        });
    };

    const toggleWeekExpanded = (wIndex) => {
        updateWeek(wIndex, (w) => ({ ...w, expanded: !w.expanded }));
    };

    const addActivityRow = (wIndex, dIndex) => {
        updateDay(wIndex, dIndex, (d) => ({
            ...d,
            activities: [...d.activities, emptyActivity()],
        }));
    };

    const removeActivityRow = (wIndex, dIndex, aIndex) => {
        updateDay(wIndex, dIndex, (d) => {
            if (d.activities.length <= 1) return d;
            return {
                ...d,
                activities: d.activities.filter((_, idx) => idx !== aIndex),
            };
        });
    };

    const updateActivityField = (wIndex, dIndex, aIndex, field, value) => {
        updateDay(wIndex, dIndex, (d) => ({
            ...d,
            activities: d.activities.map((act, idx) => {
                if (idx !== aIndex) return act;
                const updated = { ...act, [field]: value };
                if (field === 'jamMulai' || field === 'jamSelesai') {
                    updated.waktu = calcMinutes(updated.jamMulai, updated.jamSelesai);
                }
                return updated;
            }),
        }));
    };

    const handleFotoSelect = (wIndex, dIndex, event) => {
        const files = Array.from(event.target.files || []);
        files.forEach((file) => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                updateDay(wIndex, dIndex, (d) => ({
                    ...d,
                    fotoPreviews: [...d.fotoPreviews, { url: e.target.result, name: file.name }],
                    fotoFiles: [...d.fotoFiles, file],
                }));
            };
            reader.readAsDataURL(file);
        });
        event.target.value = '';
    };

    const removePreviewFoto = (wIndex, dIndex, pIndex) => {
        updateDay(wIndex, dIndex, (d) => ({
            ...d,
            fotoPreviews: d.fotoPreviews.filter((_, idx) => idx !== pIndex),
            fotoFiles: d.fotoFiles.filter((_, idx) => idx !== pIndex),
        }));
    };

    const deleteSavedFoto = async (wIndex, dIndex, fIndex) => {
        const day = weeks[wIndex].days[dIndex];
        const foto = day.fotos[fIndex];
        if (!foto?.id) {
            alert('Foto warisan ini hanya bisa diubah dengan mengunggah foto baru.');
            return;
        }
        if (!window.confirm('Hapus foto ini secara permanen?')) return;
        try {
            const res = await fetch(`/logbook-harian/foto/${foto.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
            });
            if (res.ok) {
                updateDay(wIndex, dIndex, (d) => ({
                    ...d,
                    fotos: d.fotos.filter((_, idx) => idx !== fIndex),
                }));
            } else {
                const err = await res.json();
                alert('Gagal menghapus foto: ' + (err.message || 'Error'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    };

    const getDayTotalMinutes = (day) => {
        if (day.status !== 'Kerja') return 0;
        return day.activities.reduce((sum, act) => sum + (Number(act.waktu) || 0), 0);
    };

    const getWeekTotalMinutes = (week) =>
        week.days.reduce((sum, day) => sum + getDayTotalMinutes(day), 0);

    const getTotalHours = () => {
        const totalMin = weeks.reduce((sum, w) => sum + getWeekTotalMinutes(w), 0);
        return (totalMin / 60).toFixed(1);
    };

    const getTotalCompletedWeeks = () =>
        weeks.filter((w) =>
            w.days.some(
                (d) =>
                    d.status !== 'Kerja' ||
                    d.status_verifikasi !== null ||
                    d.activities.some((a) => a.waktu > 0 || (a.kegiatan && a.kegiatan.trim() !== ''))
            )
        ).length;

    const stats = useMemo(() => {
        let totalHari = 0;
        let disetujui = 0;
        let pending = 0;
        weeks.forEach((w) => {
            w.days.forEach((d) => {
                if (d.status_verifikasi) {
                    totalHari += 1;
                    if (d.status_verifikasi === 'disetujui') disetujui += 1;
                    if (d.status_verifikasi === 'pending') pending += 1;
                }
            });
        });
        return { totalHari, disetujui, pending };
    }, [weeks]);

    const saveDayLogbook = async (wIndex, dIndex) => {
        if (!selectedProyekId) {
            alert('Pilih proyek PBL terlebih dahulu.');
            return;
        }
        const week = weeks[wIndex];
        const day = week.days[dIndex];
        if (!day.date) {
            alert(
                'Harap pilih Tanggal Awal (startDate) pada Minggu ke-' +
                    week.id +
                    ' terlebih dahulu sebelum menyimpan!'
            );
            return;
        }

        const formattedDate = day.date;
        updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: true }));

        try {
            const formData = new FormData();
            formData.append('pengajuan_proyek_id', selectedProyekId);
            formData.append('tanggal', formattedDate);
            formData.append('minggu_ke', String(week.id));
            formData.append('hari', day.name);
            formData.append('status', day.status);
            formData.append('kegiatan', day.notes || '');
            formData.append('kendala', day.kendala || '');
            formData.append('activities_json', JSON.stringify(day.activities));

            if (day.fotoFiles && day.fotoFiles.length > 0) {
                day.fotoFiles.forEach((file) => {
                    formData.append('fotos[]', file);
                });
            }

            const response = await fetch('/logbook-harian/daily', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrf(),
                    Accept: 'application/json',
                },
                body: formData,
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch {
                alert(
                    'Gagal menyimpan: Server mengembalikan respons tidak terduga (kode ' +
                        response.status +
                        '). Coba refresh halaman.'
                );
                updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: false }));
                return;
            }

            if (response.ok) {
                saveWeekDatesToStorage(weeks);
                updateDay(wIndex, dIndex, (d) => ({
                    ...d,
                    status_verifikasi: 'pending',
                    fotos:
                        result.new_fotos && result.new_fotos.length > 0
                            ? d.fotos.concat(result.new_fotos)
                            : d.fotos,
                    fotoPreviews: [],
                    fotoFiles: [],
                    uploadingFoto: false,
                }));
                const msg =
                    result.new_fotos && result.new_fotos.length > 0
                        ? ` (+${result.new_fotos.length} foto berhasil diupload)`
                        : '';
                alert(
                    `Logbook PBL Minggu ke-${week.id} hari ${day.name} (${formattedDate}) berhasil disimpan!${msg}`
                );
            } else {
                let errMsg = result.message || 'Terjadi kesalahan';
                if (result.errors) {
                    errMsg += ': ' + Object.values(result.errors).flat().join(', ');
                }
                alert('Gagal menyimpan: ' + errMsg);
                updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: false }));
            }
        } catch (error) {
            alert('Gagal menyimpan data: ' + error.message);
            updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: false }));
        }
    };

    const minDate = periode?.tanggal_mulai ? String(periode.tanggal_mulai).substring(0, 10) : undefined;
    const maxDate = periode?.tanggal_selesai ? String(periode.tanggal_selesai).substring(0, 10) : undefined;

    return (
        <AppLayout title="Logbook Magang Dalam Kampus">
            <div className="space-y-8 max-w-[1400px] mx-auto pb-16 font-sans">
                {/* Unified Header Panel (Identical to Magang Luar Kampus / External) */}
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-surface">
                    <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#013981]" />
                            <span className="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">
                                MAGANG DALAM KAMPUS (PBL) · LOGBOOK
                            </span>
                        </div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Logbook Harian &amp; Mingguan
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Catat aktivitas harian proyek kampus, akumulasi jam kerja, kendala, dan dokumentasi kegiatan.
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-3">
                        <Link href="/logbook-harian-rekap" className="ps-btn-secondary">
                            <span className="material-symbols-outlined text-[16px]">summarize</span>
                            <span>Lihat Rekap</span>
                        </Link>
                        <Link href="/logbook-mingguan" className="ps-btn-secondary">
                            <span className="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                            <span>Logbook Mingguan</span>
                        </Link>
                        <div className="bg-[#fafafa] border border-[#e4e4e7] px-4 py-2.5 rounded-xl flex items-center gap-3 shrink-0">
                            <div className="w-8 h-8 bg-[#013981] text-white rounded-lg flex items-center justify-center">
                                <span className="material-symbols-outlined text-[17px]">check_circle</span>
                            </div>
                            <div>
                                <span className="font-mono text-[#6a6f7c] block text-[10px] uppercase tracking-wider">
                                    Progres Keseluruhan
                                </span>
                                <span className="font-mono text-[#0b0b0c] text-sm font-semibold">
                                    {getTotalCompletedWeeks()} / {weeks.length} Minggu
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Proyek Selector & Active Period Banner */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div className="bg-white border border-[#e4e4e7] rounded-xl p-4 flex items-center justify-between gap-4 shadow-surface">
                        <div className="flex items-center gap-3 min-w-0">
                            <div className="w-9 h-9 rounded-lg bg-[#eef4fc] text-[#013981] flex items-center justify-center shrink-0">
                                <span className="material-symbols-outlined text-[20px]">rocket_launch</span>
                            </div>
                            <div className="min-w-0">
                                <p className="font-mono text-[10px] uppercase tracking-wider text-[#6a6f7c]">
                                    Proyek PBL Aktif
                                </p>
                                {proyek.length === 0 ? (
                                    <p className="text-sm font-semibold text-amber-700">
                                        Belum ditugaskan ke proyek PBL
                                    </p>
                                ) : proyek.length === 1 ? (
                                    <p className="text-sm font-semibold text-[#0b0b0c] truncate">
                                        {proyek[0].judul_proyek}
                                    </p>
                                ) : (
                                    <select
                                        value={selectedProyekId}
                                        onChange={(e) => setSelectedProyekId(e.target.value)}
                                        className="mt-0.5 text-sm font-semibold text-[#0b0b0c] border border-[#e4e4e7] rounded-lg px-2.5 py-1 bg-white"
                                    >
                                        {proyek.map((p) => (
                                            <option key={p.id} value={p.id}>
                                                {p.judul_proyek}
                                            </option>
                                        ))}
                                    </select>
                                )}
                            </div>
                        </div>
                    </div>

                    {periode && (
                        <div className="bg-[#eef4fc] border border-[#d8e6f8] rounded-xl p-4 flex items-center gap-3">
                            <span className="material-symbols-outlined text-[#013981] text-xl shrink-0">
                                event_available
                            </span>
                            <div className="text-xs text-[#0b0b0c]">
                                <span className="font-semibold block text-[#013981]">
                                    Periode Aktif: {periode.nama_periode} ({weeks.length} Minggu)
                                </span>
                                Input tanggal logbook diperbolehkan antara <strong>{minDate}</strong> s.d.{' '}
                                <strong>{maxDate}</strong>
                            </div>
                        </div>
                    )}
                </div>

                {/* Unified Stat Cards */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Total Jam Kerja
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getTotalHours()}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">Jam</span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Total Hari Tercatat
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{stats.totalHari}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">Hari</span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Disetujui
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#15803d] mt-2">
                            {stats.disetujui}
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Menunggu Verifikasi
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#b45309] mt-2">
                            {stats.pending}
                        </p>
                    </div>
                </div>

                {/* LEVEL 1: HIERARKI MINGGUAN (20 MINGGU — IDENTIK DENGAN EXTERNAL) */}
                <div className="space-y-4">
                    {weeks.map((week, wIndex) => {
                        const weekTotalMin = getWeekTotalMinutes(week);
                        return (
                            <div
                                key={week.id}
                                className="bg-white rounded-2xl border border-[#e4e4e7] shadow-surface overflow-hidden transition-all"
                            >
                                {/* Header Minggu */}
                                <div
                                    onClick={() => toggleWeekExpanded(wIndex)}
                                    className="px-6 py-5 bg-[#fafafa] border-b border-[#e4e4e7] flex flex-col md:flex-row justify-between items-start md:items-center cursor-pointer select-none group hover:bg-[#f4f4f5] transition-colors"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="w-11 h-11 rounded-xl bg-[#013981] text-white font-mono font-bold text-base flex items-center justify-center shrink-0">
                                            M{String(week.id).padStart(2, '0')}
                                        </div>
                                        <div>
                                            <div className="flex items-center gap-2.5 flex-wrap">
                                                <h3 className="text-lg font-bold text-[#0b0b0c]">
                                                    Minggu ke-{String(week.id).padStart(2, '0')}
                                                </h3>
                                                {weekTotalMin > 0 && (
                                                    <span className="px-2.5 py-0.5 text-xs font-semibold bg-[#eef4fc] text-[#013981] border border-[#d8e6f8] rounded-full">
                                                        {(weekTotalMin / 60).toFixed(1)} Jam
                                                    </span>
                                                )}
                                            </div>
                                            <p className="text-xs text-[#6a6f7c] mt-0.5">
                                                {week.startDate
                                                    ? `${formatDisplayDate(week.startDate)} — ${formatDisplayDate(week.endDate)}`
                                                    : 'Pilih rentang tanggal untuk mulai mengisi logbook minggu ini'}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        className="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-4 md:mt-0 w-full md:w-auto"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <div className="flex items-center gap-3 bg-white p-2 rounded-lg border-2 border-gray-300 w-full sm:w-auto">
                                            <span className="text-xs font-black text-gray-500 uppercase pl-2">
                                                Tanggal:
                                            </span>
                                            <input
                                                type="date"
                                                value={week.startDate}
                                                min={minDate}
                                                max={maxDate}
                                                onChange={(e) => onWeekDateChange(wIndex, e.target.value)}
                                                className="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer"
                                            />
                                            <span className="text-gray-300 font-bold">-</span>
                                            <input
                                                type="date"
                                                value={week.endDate}
                                                onChange={(e) => onWeekEndDateChange(wIndex, e.target.value)}
                                                min={week.startDate ? addDays(week.startDate, 4) : minDate}
                                                max={week.startDate ? addDays(week.startDate, 6) : maxDate}
                                                className="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer"
                                            />
                                        </div>
                                        <div
                                            onClick={() => toggleWeekExpanded(wIndex)}
                                            className="bg-white border border-[#e4e4e7] p-2 rounded-lg text-blue-900 shadow-surface hidden sm:block cursor-pointer"
                                        >
                                            <svg
                                                className={`w-6 h-6 transition-transform duration-300 ${
                                                    week.expanded ? 'rotate-180' : ''
                                                }`}
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="2.5"
                                                viewBox="0 0 24 24"
                                            >
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {/* Isi Minggu (Daftar Hari) */}
                                {week.expanded && (
                                    <div className="p-4 md:p-6 bg-white space-y-4">
                                        {week.days.map((day, dIndex) => {
                                            const dayTotalMinutes = getDayTotalMinutes(day);
                                            const isLocked = day.status_verifikasi === 'disetujui';
                                            return (
                                                <div
                                                    key={day.id}
                                                    className={`border-2 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 ${
                                                        day.expanded
                                                            ? 'border-gray-400 shadow-surface'
                                                            : 'hover:border-gray-300'
                                                    }`}
                                                >
                                                    {/* Header Hari */}
                                                    <div
                                                        className="px-5 py-4 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center cursor-pointer select-none group"
                                                        onClick={() =>
                                                            updateDay(wIndex, dIndex, (d) => ({
                                                                ...d,
                                                                expanded: !d.expanded,
                                                            }))
                                                        }
                                                    >
                                                        <div className="flex items-center gap-4 flex-wrap">
                                                            <div className="p-1 rounded text-gray-400 group-hover:text-gray-800 transition-colors">
                                                                <svg
                                                                    className={`w-6 h-6 transition-transform duration-300 ${
                                                                        day.expanded ? 'rotate-90 text-gray-800' : ''
                                                                    }`}
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    strokeWidth="3"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path
                                                                        strokeLinecap="round"
                                                                        strokeLinejoin="round"
                                                                        d="M9 5l7 7-7 7"
                                                                    />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h4 className="text-lg font-black text-gray-800">
                                                                    {day.name}
                                                                </h4>
                                                                <p className="text-xs font-bold text-gray-400">
                                                                    {formatDisplayDate(day.date)}
                                                                </p>
                                                            </div>

                                                            <span
                                                                className={`px-3 py-1 text-xs font-black uppercase tracking-widest rounded border-2 ${
                                                                    day.status === 'Kerja'
                                                                        ? 'bg-blue-50 text-blue-800 border-blue-200'
                                                                        : day.status === 'Libur'
                                                                          ? 'bg-gray-100 text-gray-700 border-gray-300'
                                                                          : day.status === 'Izin'
                                                                            ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                                                            : 'bg-red-50 text-red-800 border-red-200'
                                                                }`}
                                                            >
                                                                {day.status}
                                                            </span>

                                                            {day.status_verifikasi && (
                                                                <span
                                                                    className={`px-2.5 py-0.5 text-[11px] font-bold rounded-full border ${
                                                                        day.status_verifikasi === 'disetujui'
                                                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                                            : day.status_verifikasi === 'ditolak'
                                                                              ? 'bg-red-50 text-red-700 border-red-200'
                                                                              : 'bg-amber-50 text-amber-700 border-amber-200'
                                                                    }`}
                                                                >
                                                                    {day.status_verifikasi === 'disetujui'
                                                                        ? 'Disetujui'
                                                                        : day.status_verifikasi === 'ditolak'
                                                                          ? 'Ditolak'
                                                                          : 'Menunggu Verifikasi'}
                                                                </span>
                                                            )}
                                                        </div>

                                                        <div className="flex items-center mt-3 sm:mt-0 ml-14 sm:ml-0">
                                                            <span className="text-sm font-bold text-gray-500 bg-white border-2 border-gray-200 px-3 py-1.5 rounded-lg">
                                                                Total:{' '}
                                                                <span className="text-gray-900 font-black ml-1">
                                                                    {dayTotalMinutes} menit
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {/* Form Hari (Expand — 100% Seragam dengan External) */}
                                                    {day.expanded && (
                                                        <div className="p-4 md:p-5 border-t border-[#e4e4e7] bg-white space-y-5">
                                                            {day.catatan_dosen && (
                                                                <div className="bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded-r-lg">
                                                                    <p className="text-xs font-bold text-amber-800 uppercase mb-0.5">
                                                                        Catatan Manager / Dosen
                                                                    </p>
                                                                    <p className="text-sm text-gray-800">
                                                                        {day.catatan_dosen}
                                                                    </p>
                                                                </div>
                                                            )}

                                                            {/* Pilihan Status Kehadiran */}
                                                            <div className="flex flex-wrap gap-2 bg-gray-50 p-1.5 rounded-lg border-2 border-gray-200 w-fit">
                                                                {['Kerja', 'Libur', 'Izin', 'Sakit'].map((st) => (
                                                                    <button
                                                                        key={st}
                                                                        type="button"
                                                                        disabled={isLocked}
                                                                        onClick={() =>
                                                                            updateDay(wIndex, dIndex, (d) => ({
                                                                                ...d,
                                                                                status: st,
                                                                            }))
                                                                        }
                                                                        className={`px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase disabled:opacity-60 ${
                                                                            day.status === st
                                                                                ? st === 'Kerja'
                                                                                    ? 'bg-[#013981] text-white border-[#013981]'
                                                                                    : st === 'Libur'
                                                                                      ? 'bg-gray-700 text-white border-black'
                                                                                      : st === 'Izin'
                                                                                        ? 'bg-yellow-400 text-yellow-900 border-yellow-700'
                                                                                        : 'bg-red-500 text-white border-red-900'
                                                                                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                                                        }`}
                                                                    >
                                                                        {st}
                                                                    </button>
                                                                ))}
                                                            </div>

                                                            {/* Tabel Aktivitas Multi-Baris (Status = Kerja) */}
                                                            {day.status === 'Kerja' ? (
                                                                <div>
                                                                    <div className="overflow-x-auto rounded-xl border-2 border-gray-300 bg-white">
                                                                        <table className="w-full text-left text-sm whitespace-nowrap">
                                                                            <thead>
                                                                                <tr className="bg-gray-100 text-gray-800 border-b border-[#e4e4e7]">
                                                                                    <th className="px-4 py-3 font-black w-1/4 text-center border-r border-[#e4e4e7]">
                                                                                        JAM{' '}
                                                                                        <span className="font-bold text-[10px] text-gray-500 block uppercase mt-0.5 tracking-widest">
                                                                                            Mulai - Selesai
                                                                                        </span>
                                                                                    </th>
                                                                                    <th className="px-4 py-3 font-black w-7/12 border-r border-[#e4e4e7]">
                                                                                        KEGIATAN / DESKRIPSI
                                                                                    </th>
                                                                                    <th className="px-4 py-3 font-black w-1/6 text-center border-r border-[#e4e4e7]">
                                                                                        WAKTU{' '}
                                                                                        <span className="font-bold text-[10px] text-gray-500 block uppercase mt-0.5 tracking-widest">
                                                                                            (Menit)
                                                                                        </span>
                                                                                    </th>
                                                                                    <th className="px-2 py-3 w-10" />
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody className="divide-y divide-gray-100">
                                                                                {day.activities.map((act, aIndex) => (
                                                                                    <tr
                                                                                        key={aIndex}
                                                                                        className="hover:bg-blue-50/50 transition-colors"
                                                                                    >
                                                                                        <td className="px-3 py-3 border-r border-[#e4e4e7] align-top">
                                                                                            <div className="flex items-center justify-center gap-1">
                                                                                                <input
                                                                                                    type="time"
                                                                                                    disabled={isLocked}
                                                                                                    value={act.jamMulai}
                                                                                                    onChange={(e) =>
                                                                                                        updateActivityField(
                                                                                                            wIndex,
                                                                                                            dIndex,
                                                                                                            aIndex,
                                                                                                            'jamMulai',
                                                                                                            e.target.value
                                                                                                        )
                                                                                                    }
                                                                                                    className="bg-white border border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 w-24 text-center"
                                                                                                />
                                                                                                <span className="text-gray-400 font-black">
                                                                                                    -
                                                                                                </span>
                                                                                                <input
                                                                                                    type="time"
                                                                                                    disabled={isLocked}
                                                                                                    value={act.jamSelesai}
                                                                                                    onChange={(e) =>
                                                                                                        updateActivityField(
                                                                                                            wIndex,
                                                                                                            dIndex,
                                                                                                            aIndex,
                                                                                                            'jamSelesai',
                                                                                                            e.target.value
                                                                                                        )
                                                                                                    }
                                                                                                    className="bg-white border border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 w-24 text-center"
                                                                                                />
                                                                                            </div>
                                                                                        </td>
                                                                                        <td className="px-3 py-3 border-r border-[#e4e4e7] align-top">
                                                                                            <textarea
                                                                                                disabled={isLocked}
                                                                                                value={act.kegiatan}
                                                                                                onChange={(e) =>
                                                                                                    updateActivityField(
                                                                                                        wIndex,
                                                                                                        dIndex,
                                                                                                        aIndex,
                                                                                                        'kegiatan',
                                                                                                        e.target.value
                                                                                                    )
                                                                                                }
                                                                                                rows={2}
                                                                                                className="w-full bg-white border border-gray-200 rounded p-2 text-sm font-semibold text-gray-800 resize-none"
                                                                                                placeholder="Ketik deskripsi kegiatan proyek hari ini..."
                                                                                            />
                                                                                        </td>
                                                                                        <td className="px-4 py-3 border-r border-[#e4e4e7] align-top text-center pt-5">
                                                                                            <span className="font-mono font-black text-lg text-gray-800">
                                                                                                {act.waktu} &apos;
                                                                                            </span>
                                                                                        </td>
                                                                                        <td className="px-2 py-3 align-top pt-4 text-center">
                                                                                            {!isLocked &&
                                                                                                day.activities.length > 1 && (
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        onClick={() =>
                                                                                                            removeActivityRow(
                                                                                                                wIndex,
                                                                                                                dIndex,
                                                                                                                aIndex
                                                                                                            )
                                                                                                        }
                                                                                                        className="text-red-500 hover:text-red-700 p-1"
                                                                                                        title="Hapus Baris"
                                                                                                    >
                                                                                                        <span className="material-symbols-outlined text-[18px]">
                                                                                                            delete
                                                                                                        </span>
                                                                                                    </button>
                                                                                                )}
                                                                                        </td>
                                                                                    </tr>
                                                                                ))}
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    {!isLocked && (
                                                                        <div className="mt-3">
                                                                            <button
                                                                                type="button"
                                                                                onClick={() =>
                                                                                    addActivityRow(wIndex, dIndex)
                                                                                }
                                                                                className="ps-btn-secondary text-xs"
                                                                            >
                                                                                <span className="material-symbols-outlined text-[16px]">
                                                                                    add
                                                                                </span>
                                                                                <span>Tambah Baris Kegiatan</span>
                                                                            </button>
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            ) : (
                                                                <div>
                                                                    <label className="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                                                                        Keterangan ({day.status})
                                                                    </label>
                                                                    <textarea
                                                                        disabled={isLocked}
                                                                        value={day.notes}
                                                                        onChange={(e) =>
                                                                            updateDay(wIndex, dIndex, (d) => ({
                                                                                ...d,
                                                                                notes: e.target.value,
                                                                            }))
                                                                        }
                                                                        rows={2}
                                                                        placeholder={`Tuliskan keterangan ${day.status.toLowerCase()}...`}
                                                                        className="w-full border border-gray-300 rounded-lg p-3 text-sm"
                                                                    />
                                                                </div>
                                                            )}

                                                            {/* Kendala (Opsional — khusus PBL Internal) */}
                                                            <div>
                                                                <label className="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                                                    Kendala / Hambatan (Opsional)
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    disabled={isLocked}
                                                                    value={day.kendala}
                                                                    onChange={(e) =>
                                                                        updateDay(wIndex, dIndex, (d) => ({
                                                                            ...d,
                                                                            kendala: e.target.value,
                                                                        }))
                                                                    }
                                                                    placeholder="Tuliskan kendala teknis/non-teknis jika ada..."
                                                                    className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                                                />
                                                            </div>

                                                            {/* Multi-Foto Dokumentasi Kegiatan (Seragam dengan External) */}
                                                            <div className="p-4 bg-[#fafafa] border border-[#e4e4e7] rounded-xl space-y-3">
                                                                <div className="flex flex-wrap items-center justify-between gap-2">
                                                                    <div>
                                                                        <p className="text-xs font-bold text-[#0b0b0c] uppercase tracking-wider">
                                                                            Foto Dokumentasi Kegiatan
                                                                        </p>
                                                                        <p className="text-[11px] text-[#6a6f7c]">
                                                                            Unggah foto bukti pelaksanaan proyek (JPG/PNG/WEBP, maks 5MB per foto)
                                                                        </p>
                                                                    </div>
                                                                    {!isLocked && (
                                                                        <label className="ps-btn-secondary cursor-pointer text-xs">
                                                                            <span className="material-symbols-outlined text-[16px]">
                                                                                add_a_photo
                                                                            </span>
                                                                            <span>Pilih Foto</span>
                                                                            <input
                                                                                type="file"
                                                                                accept="image/*"
                                                                                multiple
                                                                                className="hidden"
                                                                                onChange={(e) =>
                                                                                    handleFotoSelect(wIndex, dIndex, e)
                                                                                }
                                                                            />
                                                                        </label>
                                                                    )}
                                                                </div>

                                                                {(day.fotos.length > 0 ||
                                                                    day.fotoPreviews.length > 0) && (
                                                                    <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                                                        {day.fotos.map((foto, fIdx) => (
                                                                            <div
                                                                                key={`saved-${foto.id || fIdx}`}
                                                                                className="relative group rounded-lg overflow-hidden border border-[#e4e4e7] bg-white aspect-square"
                                                                            >
                                                                                <a
                                                                                    href={foto.url}
                                                                                    target="_blank"
                                                                                    rel="noreferrer"
                                                                                >
                                                                                    <img
                                                                                        src={foto.url}
                                                                                        alt={foto.filename}
                                                                                        className="w-full h-full object-cover"
                                                                                    />
                                                                                </a>
                                                                                {!isLocked && foto.id && (
                                                                                    <button
                                                                                        type="button"
                                                                                        onClick={() =>
                                                                                            deleteSavedFoto(
                                                                                                wIndex,
                                                                                                dIndex,
                                                                                                fIdx
                                                                                            )
                                                                                        }
                                                                                        className="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                                                                        title="Hapus Foto"
                                                                                    >
                                                                                        <span className="material-symbols-outlined text-[14px]">
                                                                                            close
                                                                                        </span>
                                                                                    </button>
                                                                                )}
                                                                            </div>
                                                                        ))}
                                                                        {day.fotoPreviews.map((prev, pIdx) => (
                                                                            <div
                                                                                key={`prev-${pIdx}`}
                                                                                className="relative group rounded-lg overflow-hidden border-2 border-[#013981] bg-white aspect-square"
                                                                            >
                                                                                <img
                                                                                    src={prev.url}
                                                                                    alt={prev.name}
                                                                                    className="w-full h-full object-cover"
                                                                                />
                                                                                <span className="absolute bottom-1 left-1 bg-[#013981] text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                                                                    BARU
                                                                                </span>
                                                                                <button
                                                                                    type="button"
                                                                                    onClick={() =>
                                                                                        removePreviewFoto(
                                                                                            wIndex,
                                                                                            dIndex,
                                                                                            pIdx
                                                                                        )
                                                                                    }
                                                                                    className="absolute top-1 right-1 bg-black/70 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                                                                    title="Batal Pilih"
                                                                                >
                                                                                    <span className="material-symbols-outlined text-[14px]">
                                                                                        close
                                                                                    </span>
                                                                                </button>
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>

                                                            {/* Tombol Simpan Hari Ini */}
                                                            {!isLocked && (
                                                                <div className="flex justify-end pt-2">
                                                                    <button
                                                                        type="button"
                                                                        disabled={day.uploadingFoto || !selectedProyekId}
                                                                        onClick={() => saveDayLogbook(wIndex, dIndex)}
                                                                        className="ps-btn-primary disabled:opacity-60"
                                                                    >
                                                                        <span className="material-symbols-outlined text-[18px]">
                                                                            save
                                                                        </span>
                                                                        <span>
                                                                            {day.uploadingFoto
                                                                                ? 'Menyimpan...'
                                                                                : 'Simpan Hari Ini'}
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            )}
                                                        </div>
                                                    )}
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>
            </div>
        </AppLayout>
    );
}
