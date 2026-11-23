import React, { useState } from 'react';
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
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][fallbackIndex] || '';
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

const emptyActivity = () => ({
    jamMulai: '',
    jamSelesai: '',
    kegiatan: '',
    kode: 'SUP',
    waktu: 0,
});

const createEmptyDay = (index, dateStr = '') => ({
    id: index,
    date: dateStr,
    name: getDayName(dateStr, index),
    status: 'Kerja',
    notes: '',
    expanded: false,
    activities: [emptyActivity()],
    fotos: [],
    fotoPreviews: [],
    fotoFiles: [],
    uploadingFoto: false,
});

const buildInitialWeeks = (existingLogbooks, currentNim, existingFotosByDate) => {
    let savedWeekDates = {};
    if (currentNim) {
        try {
            savedWeekDates = JSON.parse(
                localStorage.getItem('logbook_week_dates_' + currentNim) || '{}'
            );
        } catch {}
    }

    const logbooksByDate = {};
    const weekStartFromLogbook = {};
    const weekEndFromLogbook = {};
    const weekDataFromLogbook = {};

    if (Array.isArray(existingLogbooks)) {
        existingLogbooks.forEach((item) => {
            const tgl = item.tanggal ? String(item.tanggal).substring(0, 10) : '';
            if (tgl) {
                if (!logbooksByDate[tgl]) {
                    logbooksByDate[tgl] = [];
                }
                logbooksByDate[tgl].push(item);

                if (item.minggu_ke) {
                    if (
                        !weekStartFromLogbook[item.minggu_ke] ||
                        tgl < weekStartFromLogbook[item.minggu_ke]
                    ) {
                        weekStartFromLogbook[item.minggu_ke] = tgl;
                    }
                    if (
                        !weekEndFromLogbook[item.minggu_ke] ||
                        tgl > weekEndFromLogbook[item.minggu_ke]
                    ) {
                        weekEndFromLogbook[item.minggu_ke] = tgl;
                    }

                    if (!weekDataFromLogbook[item.minggu_ke]) {
                        weekDataFromLogbook[item.minggu_ke] = {
                            nilai: null,
                            catatan_mentor: null,
                        };
                    }
                    if (item.nilai !== null && item.nilai !== undefined) {
                        weekDataFromLogbook[item.minggu_ke].nilai = item.nilai;
                    }
                    if (item.catatan_mentor) {
                        weekDataFromLogbook[item.minggu_ke].catatan_mentor =
                            item.catatan_mentor;
                    }
                }
            }
        });
    }

    const weeksArr = [];
    for (let i = 1; i <= 20; i++) {
        const weekStartDate =
            savedWeekDates[i]?.startDate || weekStartFromLogbook[i] || '';
        let weekEndDate =
            savedWeekDates[i]?.endDate ||
            weekEndFromLogbook[i] ||
            (weekStartDate ? addDays(weekStartDate, 4) : '');
        const dayCount = getWeekDayCount(weekStartDate, weekEndDate);
        if (
            weekStartDate &&
            getWeekDayCount(weekStartDate, weekEndDate) === 5 &&
            weekEndDate !== addDays(weekStartDate, 4)
        ) {
            weekEndDate = addDays(weekStartDate, 4);
        }

        const daysArr = [];
        for (let d = 0; d < dayCount; d++) {
            const dayDateStr = weekStartDate ? addDays(weekStartDate, d) : '';
            let dayStatus = 'Kerja';
            let dayNotes = '';
            const dayActivities = [];

            if (dayDateStr && logbooksByDate[dayDateStr]) {
                const entries = logbooksByDate[dayDateStr];
                if (entries.length > 0) {
                    dayStatus = entries[0].status || 'Kerja';

                    if (dayStatus === 'Dinilai') {
                        if (
                            entries[0].jam_mulai !== null ||
                            (entries[0].durasi_mnt && entries[0].durasi_mnt > 0)
                        ) {
                            dayStatus = 'Kerja';
                        } else {
                            dayStatus = 'Libur';
                        }
                    }

                    if (dayStatus !== 'Kerja') {
                        dayNotes = entries[0].kegiatan || '';
                    } else {
                        entries.forEach((e) => {
                            if ((e.durasi_mnt && e.durasi_mnt > 0) || e.kegiatan) {
                                dayActivities.push({
                                    jamMulai: e.jam_mulai
                                        ? String(e.jam_mulai).substring(0, 5)
                                        : '',
                                    jamSelesai: e.jam_selesai
                                        ? String(e.jam_selesai).substring(0, 5)
                                        : '',
                                    kegiatan: e.kegiatan || '',
                                    kode: e.kd_mat || 'SUP',
                                    waktu: Number(e.durasi_mnt || 0),
                                });
                            }
                        });
                    }
                }
            }

            if (dayActivities.length === 0) {
                dayActivities.push(emptyActivity());
            }

            let dayFotos = [];
            if (dayDateStr && existingFotosByDate && existingFotosByDate[dayDateStr]) {
                dayFotos = existingFotosByDate[dayDateStr];
            }

            daysArr.push({
                id: d,
                date: dayDateStr,
                name: getDayName(dayDateStr, d),
                status: dayStatus,
                notes: dayNotes,
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
                (day.notes && day.notes.trim() !== '') ||
                day.activities.some(
                    (a) => a.waktu > 0 || (a.kegiatan && a.kegiatan.trim() !== '')
                )
        );

        weeksArr.push({
            id: i,
            expanded: i === 1 || hasData,
            startDate: weekStartDate,
            endDate: weekEndDate,
            nilai: weekDataFromLogbook[i] ? weekDataFromLogbook[i].nilai : null,
            catatan_mentor: weekDataFromLogbook[i]
                ? weekDataFromLogbook[i].catatan_mentor
                : null,
            days: daysArr,
        });
    }

    return weeksArr;
};

export default function ExternalMahasiswaDashboard({
    mahasiswa,
    logbooks = [],
    fotosByDate = {},
    perusahaan = null,
    sarans = [],
    saranDariMentor = null,
    parameterSaran = [],
}) {
    const currentNim = mahasiswa?.nim || '';
    const [weeks, setWeeks] = useState(() =>
        buildInitialWeeks(logbooks, currentNim, fotosByDate)
    );
    const [saranForm, setSaranForm] = useState({
        tanggal: new Date().toISOString().split('T')[0],
        saran: '',
        answers: {},
    });

    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const saveWeekDatesToStorage = (weeksToSave) => {
        if (!currentNim) return;
        const dates = {};
        weeksToSave.forEach((w) => {
            if (w.startDate || w.endDate) {
                dates[w.id] = { startDate: w.startDate, endDate: w.endDate };
            }
        });
        try {
            localStorage.setItem(
                'logbook_week_dates_' + currentNim,
                JSON.stringify(dates)
            );
        } catch {}
    };

    const syncWeekDaysObj = (week) => {
        const dayCount = getWeekDayCount(week.startDate, week.endDate);
        const nextDays = week.days.slice(0, dayCount).map((d) => ({ ...d }));

        for (let index = 0; index < dayCount; index++) {
            const date = week.startDate ? addDays(week.startDate, index) : '';
            if (!nextDays[index]) {
                nextDays.push(createEmptyDay(index, date));
            } else {
                nextDays[index] = {
                    ...nextDays[index],
                    id: index,
                    date,
                    name: getDayName(date, index),
                };
            }
        }
        return { ...week, days: nextDays };
    };

    const toggleWeekExpanded = (wIndex) => {
        setWeeks((prev) =>
            prev.map((w, idx) =>
                idx === wIndex ? { ...w, expanded: !w.expanded } : w
            )
        );
    };

    const onWeekDateChange = (wIndex, newStartDate) => {
        setWeeks((prev) => {
            const next = prev.map((w, idx) => {
                if (idx !== wIndex) return w;
                const updated = {
                    ...w,
                    startDate: newStartDate,
                    endDate: newStartDate ? addDays(newStartDate, 4) : w.endDate,
                };
                return syncWeekDaysObj(updated);
            });
            saveWeekDatesToStorage(next);
            return next;
        });
    };

    const onWeekEndDateChange = (wIndex, newEndDate) => {
        setWeeks((prev) => {
            const targetWeek = prev[wIndex];
            if (!targetWeek.startDate) {
                alert('Pilih tanggal mulai terlebih dahulu.');
                return prev.map((w, idx) =>
                    idx === wIndex ? { ...w, endDate: '' } : w
                );
            }

            const minEndDate = addDays(targetWeek.startDate, 4);
            const maxEndDate = addDays(targetWeek.startDate, 6);
            let finalEndDate = newEndDate;
            if (!finalEndDate || finalEndDate < minEndDate || finalEndDate > maxEndDate) {
                finalEndDate = minEndDate;
                alert('Rentang logbook harus 5 sampai 7 hari (maksimal 7 hari).');
            }

            const next = prev.map((w, idx) => {
                if (idx !== wIndex) return w;
                return syncWeekDaysObj({ ...w, endDate: finalEndDate });
            });
            saveWeekDatesToStorage(next);
            return next;
        });
    };

    const updateDay = (wIndex, dIndex, updater) => {
        setWeeks((prev) =>
            prev.map((w, wi) => {
                if (wi !== wIndex) return w;
                const nextDays = w.days.map((d, di) =>
                    di === dIndex ? updater(d) : d
                );
                return { ...w, days: nextDays };
            })
        );
    };

    const calcTime = (jamMulai, jamSelesai) => {
        if (jamMulai && jamSelesai) {
            const start = new Date(`1970-01-01T${jamMulai}:00`);
            const end = new Date(`1970-01-01T${jamSelesai}:00`);
            let diffMinutes = (end - start) / 1000 / 60;
            if (diffMinutes < 0) diffMinutes += 24 * 60;
            return Math.round(diffMinutes);
        }
        return 0;
    };

    const updateActivityField = (wIndex, dIndex, aIndex, field, value) => {
        updateDay(wIndex, dIndex, (day) => {
            const nextActivities = day.activities.map((act, ai) => {
                if (ai !== aIndex) return act;
                const updated = { ...act, [field]: value };
                if (field === 'jamMulai' || field === 'jamSelesai') {
                    updated.waktu = calcTime(updated.jamMulai, updated.jamSelesai);
                }
                return updated;
            });
            return { ...day, activities: nextActivities };
        });
    };

    const addActivity = (wIndex, dIndex) => {
        updateDay(wIndex, dIndex, (day) => ({
            ...day,
            activities: [...day.activities, emptyActivity()],
        }));
    };

    const removeActivity = (wIndex, dIndex, aIndex) => {
        updateDay(wIndex, dIndex, (day) => ({
            ...day,
            activities: day.activities.filter((_, ai) => ai !== aIndex),
        }));
    };

    const onFotosSelected = (event, wIndex, dIndex) => {
        const files = Array.from(event.target.files || []);
        const invalid = files.filter((f) => f.size > 5 * 1024 * 1024);
        if (invalid.length > 0) {
            alert(
                `${invalid.length} file melebihi batas 5MB dan akan diabaikan:\n` +
                    invalid.map((f) => f.name).join('\n')
            );
        }
        const valid = files.filter((f) => f.size <= 5 * 1024 * 1024);
        valid.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                updateDay(wIndex, dIndex, (day) => ({
                    ...day,
                    fotoPreviews: [
                        ...day.fotoPreviews,
                        { dataUrl: e.target.result, name: file.name },
                    ],
                    fotoFiles: [...day.fotoFiles, file],
                }));
            };
            reader.readAsDataURL(file);
        });
        event.target.value = '';
    };

    const removeFotoPreviewByIndex = (wIndex, dIndex, pIdx) => {
        updateDay(wIndex, dIndex, (day) => ({
            ...day,
            fotoPreviews: day.fotoPreviews.filter((_, i) => i !== pIdx),
            fotoFiles: day.fotoFiles.filter((_, i) => i !== pIdx),
        }));
    };

    const deleteFotoById = async (wIndex, dIndex, fotoId) => {
        if (!window.confirm('Hapus foto ini?')) return;
        try {
            const response = await fetch(`/external/mahasiswa/logbook/foto/${fotoId}`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
            });
            const result = await response.json();
            if (response.ok) {
                updateDay(wIndex, dIndex, (day) => ({
                    ...day,
                    fotos: day.fotos.filter((f) => f.id !== fotoId),
                }));
            } else {
                alert('Gagal hapus foto: ' + (result.message || 'Error'));
            }
        } catch (e) {
            alert('Gagal hapus foto: ' + e.message);
        }
    };

    const getDayTotalMinutes = (day) => {
        if (day.status !== 'Kerja') return 0;
        return day.activities.reduce((sum, act) => sum + (Number(act.waktu) || 0), 0);
    };

    const getWeekTotalMinutes = (week) =>
        week.days.reduce((sum, day) => sum + getDayTotalMinutes(day), 0);

    const getTotalHours = () => {
        let totalMinutes = 0;
        weeks.forEach((week) => {
            totalMinutes += getWeekTotalMinutes(week);
        });
        return (totalMinutes / 60).toFixed(1);
    };

    const getHoursByMatkul = (kode) => {
        let totalMinutes = 0;
        weeks.forEach((week) => {
            week.days.forEach((day) => {
                if (day.status === 'Kerja') {
                    day.activities.forEach((act) => {
                        if (act.kode === kode) totalMinutes += Number(act.waktu) || 0;
                    });
                }
            });
        });
        return (totalMinutes / 60).toFixed(1);
    };

    const getTotalCompletedWeeks = () =>
        weeks.filter((week) => getWeekTotalMinutes(week) > 0).length;

    const saveDay = async (wIndex, dIndex) => {
        const week = weeks[wIndex];
        const day = week.days[dIndex];

        if (!week.startDate) {
            alert(
                'Mohon isi tanggal mulai minggu terlebih dahulu (Tanggal di header minggu).'
            );
            return;
        }

        const formattedDate = day.date || addDays(week.startDate, dIndex);
        if (!formattedDate) {
            alert('Format tanggal mulai minggu tidak valid.');
            return;
        }

        const formData = new FormData();
        formData.append('tanggal', formattedDate);
        formData.append('status', day.status);
        formData.append('kegiatan', day.notes || '');
        formData.append('minggu_ke', week.id);
        formData.append('_token', getCsrf());

        if (day.status === 'Kerja') {
            const activitiesData = day.activities.map((a) => ({
                kode: a.kode,
                kegiatan: a.kegiatan || '-',
                waktu: a.waktu,
                jamMulai: a.jamMulai,
                jamSelesai: a.jamSelesai,
            }));
            formData.append('activities_json', JSON.stringify(activitiesData));
        } else {
            formData.append('activities_json', JSON.stringify([]));
        }

        day.fotoFiles.forEach((file) => {
            formData.append('fotos[]', file);
        });

        updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: true }));

        try {
            const response = await fetch('/external/mahasiswa/logbook', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: formData,
            });

            let result;
            try {
                result = await response.json();
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
                    `Logbook Minggu ke-${week.id} hari ${day.name} (${formattedDate}) berhasil disimpan!${msg}`
                );
            } else {
                let errMsg = result.message || 'Terjadi kesalahan';
                if (result.errors) {
                    errMsg += ': ' + Object.values(result.errors).flat().join(', ');
                }
                alert('Gagal menyimpan: ' + errMsg);
                console.error(result);
                updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: false }));
            }
        } catch (error) {
            alert('Gagal menyimpan data: ' + error.message);
            console.error(error);
            updateDay(wIndex, dIndex, (d) => ({ ...d, uploadingFoto: false }));
        }
    };

    const submitSaran = async (e) => {
        e.preventDefault();
        let finalSaran = '';
        if (Object.keys(saranForm.answers).length > 0) {
            finalSaran = JSON.stringify(saranForm.answers);
        } else {
            finalSaran = saranForm.saran;
            if (!finalSaran.trim()) return;
        }

        try {
            const payload = {
                tanggal: saranForm.tanggal,
                saran: finalSaran,
                perusahaan: perusahaan.nama_perusahaan,
            };

            const response = await fetch('/external/mahasiswa/saran', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                window.location.reload();
            } else {
                let errMsg = result.message || 'Terjadi kesalahan';
                if (result.errors) {
                    errMsg += ': ' + Object.values(result.errors).flat().join(', ');
                }
                alert('Gagal mengirim saran: ' + errMsg);
            }
        } catch (error) {
            alert('Gagal mengirim saran: ' + error.message);
        }
    };

    return (
        <AppLayout title="Logbook Mahasiswa">
            <div className="space-y-8 max-w-[1400px] mx-auto pb-16 font-sans">
                {/* Unified Header Panel (Matching Magang Dalam Kampus) */}
                <div className="bg-white border border-[#e4e4e7] rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-surface">
                    <div>
                        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#f4f4f5] border border-[#e4e4e7] mb-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#013981]" />
                            <span className="font-mono text-[10px] uppercase tracking-wider text-[#0b0b0c]">
                                MAGANG LUAR KAMPUS (INDUSTRI) · LOGBOOK
                            </span>
                        </div>
                        <h1 className="text-2xl font-semibold text-[#0b0b0c] tracking-tight">
                            Logbook Harian &amp; Mingguan
                        </h1>
                        <p className="text-xs text-[#6a6f7c] mt-1">
                            Catat aktivitas harian di industri, akumulasi jam kompetensi, dan dokumentasi kegiatan.
                        </p>
                    </div>
                    <div className="bg-[#fafafa] border border-[#e4e4e7] px-4 py-3 rounded-xl flex items-center gap-3.5 shrink-0">
                        <div className="w-9 h-9 bg-[#013981] text-white rounded-lg flex items-center justify-center">
                            <span className="material-symbols-outlined text-[18px]">
                                check_circle
                            </span>
                        </div>
                        <div>
                            <span className="font-mono text-[#6a6f7c] block text-[10px] uppercase tracking-wider">
                                Progres Keseluruhan
                            </span>
                            <span className="font-mono text-[#0b0b0c] text-base font-semibold">
                                {getTotalCompletedWeeks()} / 20 Minggu
                            </span>
                        </div>
                    </div>
                </div>

                {/* Unified Stat Cards (Matching Magang Dalam Kampus) */}
                <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Total Jam Kerja
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getTotalHours()}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">
                                Jam
                            </span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            PII
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getHoursByMatkul('PII')}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">
                                Jam
                            </span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Supervisi
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getHoursByMatkul('SUP')}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">
                                Jam
                            </span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            K3 IT
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getHoursByMatkul('K3')}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">
                                Jam
                            </span>
                        </p>
                    </div>
                    <div className="bg-white p-5 rounded-xl border border-[#e4e4e7] shadow-surface">
                        <p className="font-mono text-[11px] text-[#6a6f7c] uppercase tracking-wider">
                            Lap. Teknik
                        </p>
                        <p className="text-3xl font-semibold font-mono text-[#0b0b0c] mt-2">
                            <span>{getHoursByMatkul('LTD')}</span>
                            <span className="text-xs font-normal text-[#6a6f7c] ml-1">
                                Jam
                            </span>
                        </p>
                    </div>
                </div>

                {/* Area Saran & Masukan untuk Perusahaan */}
                {perusahaan && (
                    <>
                        <div className="bg-white p-6 md:p-8 rounded-2xl border border-[#e4e4e7] shadow-surface relative overflow-hidden transition-all">
                            {/* Dekorasi Latar */}
                            <div className="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                                <svg
                                    className="w-32 h-32 text-yellow-600"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                </svg>
                            </div>

                            <div className="relative z-10">
                                <h3 className="text-2xl font-black text-yellow-700 tracking-tight flex items-center gap-2">
                                    <svg
                                        className="w-7 h-7"
                                        fill="none"
                                        stroke="currentColor"
                                        strokeWidth="2.5"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                        />
                                    </svg>
                                    Saran &amp; Masukan untuk PT
                                </h3>
                                {sarans.length === 0 && (
                                    <p className="text-gray-600 font-bold mb-6 mt-1 border-l-4 border-yellow-400 pl-3">
                                        Berikan masukan konstruktif Anda untuk{' '}
                                        <span className="text-gray-900 font-black">
                                            {perusahaan.nama_perusahaan}
                                        </span>
                                        .
                                    </p>
                                )}

                                {/* Pesan jika sudah mengisi */}
                                {sarans.length > 0 && (
                                    <div className="mb-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                        <svg
                                            className="w-6 h-6 text-emerald-600 flex-shrink-0"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="2.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <div>
                                            <h4 className="font-black text-emerald-900">
                                                Terima Kasih!
                                            </h4>
                                            <p className="text-sm font-medium text-emerald-700">
                                                Anda sudah memberikan saran dan masukan untuk perusahaan ini.
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {sarans.length === 0 && (
                                    <form
                                        onSubmit={submitSaran}
                                        className="space-y-4 bg-yellow-50 p-5 md:p-6 rounded-xl border-2 border-yellow-200"
                                    >
                                        <div className="mb-4">
                                            <label className="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">
                                                Alamat PT
                                            </label>
                                            <input
                                                type="text"
                                                value={perusahaan.alamat || ''}
                                                disabled
                                                className="w-full border-2 border-yellow-200 rounded-lg p-2.5 font-bold bg-yellow-100/50 text-gray-500 cursor-not-allowed"
                                            />
                                        </div>
                                        {!parameterSaran || parameterSaran.length === 0 ? (
                                            <div>
                                                <label className="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">
                                                    Isi Saran / Masukan
                                                </label>
                                                <textarea
                                                    value={saranForm.saran}
                                                    onChange={(e) =>
                                                        setSaranForm({
                                                            ...saranForm,
                                                            saran: e.target.value,
                                                        })
                                                    }
                                                    required
                                                    rows={3}
                                                    className="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none text-gray-800 bg-white"
                                                    placeholder="Tuliskan saran yang membangun..."
                                                />
                                            </div>
                                        ) : (
                                            parameterSaran.map((param, pIdx) => (
                                                <div key={param.id || pIdx} className="mb-4">
                                                    <h4 className="font-bold text-yellow-900 mb-2 border-b-2 border-yellow-200 pb-1">
                                                        {param.sub_kategori}
                                                    </h4>
                                                    {(param.indikator || []).map((ind, iIdx) => (
                                                        <div key={iIdx} className="mb-3">
                                                            <label className="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">
                                                                {ind}
                                                            </label>
                                                            <textarea
                                                                value={
                                                                    saranForm.answers[ind] || ''
                                                                }
                                                                onChange={(e) =>
                                                                    setSaranForm({
                                                                        ...saranForm,
                                                                        answers: {
                                                                            ...saranForm.answers,
                                                                            [ind]: e.target.value,
                                                                        },
                                                                    })
                                                                }
                                                                required
                                                                rows={2}
                                                                className="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none text-gray-800 bg-white"
                                                                placeholder="Jawaban Anda..."
                                                            />
                                                        </div>
                                                    ))}
                                                </div>
                                            ))
                                        )}
                                        <div className="flex justify-end pt-2">
                                            <button
                                                type="submit"
                                                className="bg-[#013981] text-white font-black py-3 px-8 rounded-xl border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-2"
                                            >
                                                <svg
                                                    className="w-5 h-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    strokeWidth="3"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                                    />
                                                </svg>
                                                Kirim Masukan
                                            </button>
                                        </div>
                                    </form>
                                )}

                                {/* Riwayat Saran */}
                                {sarans.length > 0 && (
                                    <div className="mt-8 pt-6 border-t border-[#e4e4e7]">
                                        <h4 className="text-sm font-black text-gray-700 mb-4 uppercase tracking-wider flex items-center gap-2">
                                            <svg
                                                className="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="2.5"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            Riwayat Masukan Anda
                                        </h4>
                                        <div className="space-y-4 max-h-64 overflow-y-auto custom-scrollbar pr-2">
                                            {sarans.map((saran) => (
                                                <div
                                                    key={saran.id}
                                                    className="bg-white p-4 rounded-xl border-2 border-gray-200 shadow-sm hover:border-yellow-400 transition-colors"
                                                >
                                                    <div className="flex justify-between items-center mb-2">
                                                        <span className="text-xs font-black bg-yellow-100 text-yellow-800 border border-yellow-300 px-2.5 py-1 rounded-md">
                                                            {saran.tanggal}
                                                        </span>
                                                    </div>
                                                    <p className="text-gray-700 text-sm font-medium leading-relaxed">
                                                        {saran.saran}
                                                    </p>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Saran dari Mentor (Jika Ada) */}
                        {saranDariMentor && (
                            <div className="mt-6 bg-white p-6 md:p-8 rounded-2xl border border-[#e4e4e7] shadow-surface relative overflow-hidden transition-all">
                                <div className="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                                    <svg
                                        className="w-32 h-32 text-emerald-600"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1.177-7.86l-2.765-2.767L7 12.431l3.118 3.121a1 1 0 001.414 0l5.952-5.95-1.062-1.062-5.6 5.6z" />
                                    </svg>
                                </div>
                                <div className="relative z-10">
                                    <h3 className="text-2xl font-black text-emerald-700 tracking-tight flex items-center gap-2 mb-4">
                                        <svg
                                            className="w-7 h-7"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="2.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0015.5 3H15m3 15h-3m-3-12h3m-3 0V3m-3 4H5"
                                            />
                                        </svg>
                                        Saran &amp; Evaluasi dari Mentor
                                    </h3>
                                    <div className="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                                        <p className="text-xs font-black text-emerald-800 uppercase tracking-widest mb-2 flex justify-between items-center">
                                            <span>Tanggal Diterima</span>
                                            <span>{saranDariMentor.tanggal}</span>
                                        </p>
                                        <p className="text-gray-800 font-medium whitespace-pre-wrap italic">
                                            {saranDariMentor.saran}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}
                    </>
                )}

                {/* HIERARKI DATA: MINGGU -> HARI -> TABEL AKTIVITAS */}
                <div className="space-y-6">
                    {weeks.map((week, wIndex) => {
                        const weekTotalMinutes = getWeekTotalMinutes(week);
                        return (
                            /* LEVEL 1: CARD MINGGU */
                            <div
                                key={week.id}
                                className="bg-white rounded-2xl border border-[#e4e4e7] shadow-surface overflow-hidden transition-all duration-300"
                            >
                                {/* Header Minggu */}
                                <div
                                    className="p-5 md:p-6 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center cursor-pointer hover:bg-blue-50 transition-colors border-b border-[#e4e4e7] group"
                                    onClick={() => toggleWeekExpanded(wIndex)}
                                >
                                    <div className="flex items-center gap-5">
                                        {/* Icon Indikator */}
                                        <div
                                            className={`w-12 h-12 rounded-xl border border-[#e4e4e7] flex items-center justify-center transition-colors shadow-surface ${
                                                weekTotalMinutes > 0
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-white text-blue-900 group-hover:bg-blue-100'
                                            }`}
                                        >
                                            <svg
                                                className="w-6 h-6"
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="2.5"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 className="text-2xl font-black text-blue-900 flex items-center gap-3">
                                                <span>
                                                    {'Minggu ke-' +
                                                        String(week.id).padStart(2, '0')}
                                                </span>
                                                {week.nilai !== null && (
                                                    <span className="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 text-sm font-bold rounded-lg shadow-sm">
                                                        Nilai: {week.nilai}
                                                    </span>
                                                )}
                                            </h3>
                                            <p className="text-sm font-bold text-gray-500 mt-1">
                                                <span>
                                                    {weekTotalMinutes > 0
                                                        ? `${(weekTotalMinutes / 60).toFixed(
                                                              1
                                                          )} Jam diselesaikan`
                                                        : 'Belum ada pengisian data'}
                                                </span>
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
                                                onChange={(e) =>
                                                    onWeekDateChange(wIndex, e.target.value)
                                                }
                                                className="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer"
                                            />
                                            <span className="text-gray-300 font-bold">-</span>
                                            <input
                                                type="date"
                                                value={week.endDate}
                                                onChange={(e) =>
                                                    onWeekEndDateChange(wIndex, e.target.value)
                                                }
                                                min={
                                                    week.startDate
                                                        ? addDays(week.startDate, 4)
                                                        : undefined
                                                }
                                                max={
                                                    week.startDate
                                                        ? addDays(week.startDate, 6)
                                                        : undefined
                                                }
                                                className="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer"
                                            />
                                        </div>
                                        <div
                                            onClick={() => toggleWeekExpanded(wIndex)}
                                            className="bg-white border border-[#e4e4e7] p-2 rounded-lg text-blue-900 shadow-surface group-hover:translate-y-px group-hover:translate-x-px group-hover:shadow-none transition-all hidden sm:block cursor-pointer"
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
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {/* Isi Minggu (Daftar Hari) */}
                                {week.expanded && (
                                    <div>
                                        <div className="p-4 md:p-6 bg-white space-y-4">
                                            {/* Catatan Mentor untuk Minggu ini */}
                                            {week.catatan_mentor && (
                                                <div className="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg mb-4">
                                                    <p className="text-xs font-black text-emerald-800 uppercase tracking-widest mb-1 flex items-center gap-1">
                                                        <svg
                                                            className="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            strokeWidth="2.5"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                                            />
                                                        </svg>
                                                        Catatan Mentor
                                                    </p>
                                                    <p className="text-gray-800 text-sm font-medium italic">
                                                        {week.catatan_mentor}
                                                    </p>
                                                </div>
                                            )}

                                            {week.days.map((day, dIndex) => {
                                                const dayTotalMinutes =
                                                    getDayTotalMinutes(day);
                                                return (
                                                    /* LEVEL 2: CARD HARI */
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
                                                                updateDay(
                                                                    wIndex,
                                                                    dIndex,
                                                                    (d) => ({
                                                                        ...d,
                                                                        expanded: !d.expanded,
                                                                    })
                                                                )
                                                            }
                                                        >
                                                            <div className="flex items-center gap-4">
                                                                <div className="p-1 rounded text-gray-400 group-hover:text-gray-800 transition-colors">
                                                                    <svg
                                                                        className={`w-6 h-6 transition-transform duration-300 ${
                                                                            day.expanded
                                                                                ? 'rotate-90 text-gray-800'
                                                                                : ''
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
                                                                        {formatDisplayDate(
                                                                            day.date
                                                                        )}
                                                                    </p>
                                                                </div>

                                                                {/* Indikator Status Neo-Brutal */}
                                                                <span
                                                                    className={`px-3 py-1 text-xs font-black uppercase tracking-widest rounded border-2 ${
                                                                        day.status === 'Kerja'
                                                                            ? 'bg-blue-50 text-blue-800 border-blue-200'
                                                                            : day.status ===
                                                                                'Libur'
                                                                              ? 'bg-gray-100 text-gray-700 border-gray-300'
                                                                              : day.status ===
                                                                                  'Izin'
                                                                                ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                                                                : 'bg-red-50 text-red-800 border-red-200'
                                                                    }`}
                                                                >
                                                                    {day.status}
                                                                </span>
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

                                                        {/* Form Hari (Expand) */}
                                                        {day.expanded && (
                                                            <div>
                                                                <div className="p-4 md:p-5 border-t border-[#e4e4e7] bg-white">
                                                                    {/* Pilihan Status Hari Ini */}
                                                                    <div className="flex flex-wrap gap-2 mb-5 bg-gray-50 p-1.5 rounded-lg border-2 border-gray-200 w-fit">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateDay(
                                                                                    wIndex,
                                                                                    dIndex,
                                                                                    (d) => ({
                                                                                        ...d,
                                                                                        status: 'Kerja',
                                                                                    })
                                                                                )
                                                                            }
                                                                            className={`px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase ${
                                                                                day.status ===
                                                                                'Kerja'
                                                                                    ? 'bg-blue-600 text-white border-blue-900 shadow-surface'
                                                                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                                                            }`}
                                                                        >
                                                                            Kerja
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateDay(
                                                                                    wIndex,
                                                                                    dIndex,
                                                                                    (d) => ({
                                                                                        ...d,
                                                                                        status: 'Libur',
                                                                                    })
                                                                                )
                                                                            }
                                                                            className={`px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase ${
                                                                                day.status ===
                                                                                'Libur'
                                                                                    ? 'bg-gray-700 text-white border-black shadow-surface'
                                                                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                                                            }`}
                                                                        >
                                                                            Libur
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateDay(
                                                                                    wIndex,
                                                                                    dIndex,
                                                                                    (d) => ({
                                                                                        ...d,
                                                                                        status: 'Izin',
                                                                                    })
                                                                                )
                                                                            }
                                                                            className={`px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase ${
                                                                                day.status ===
                                                                                'Izin'
                                                                                    ? 'bg-yellow-400 text-yellow-900 border-yellow-700 shadow-surface'
                                                                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                                                            }`}
                                                                        >
                                                                            Izin
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateDay(
                                                                                    wIndex,
                                                                                    dIndex,
                                                                                    (d) => ({
                                                                                        ...d,
                                                                                        status: 'Sakit',
                                                                                    })
                                                                                )
                                                                            }
                                                                            className={`px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase ${
                                                                                day.status ===
                                                                                'Sakit'
                                                                                    ? 'bg-red-500 text-white border-red-900 shadow-surface'
                                                                                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'
                                                                            }`}
                                                                        >
                                                                            Sakit
                                                                        </button>
                                                                    </div>

                                                                    {/* JIKA STATUS KERJA (Tabel Logbook Clean & Rapi) */}
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
                                                                                            <th className="px-4 py-3 font-black w-2/4 border-r border-[#e4e4e7]">
                                                                                                KEGIATAN / DESKRIPSI
                                                                                            </th>
                                                                                            <th className="px-4 py-3 font-black w-1/6 text-center leading-tight border-r border-[#e4e4e7]">
                                                                                                KODE
                                                                                                <br />
                                                                                                MATKUL
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
                                                                                        {day.activities.map(
                                                                                            (
                                                                                                act,
                                                                                                aIndex
                                                                                            ) => (
                                                                                                <tr
                                                                                                    key={
                                                                                                        aIndex
                                                                                                    }
                                                                                                    className="hover:bg-blue-50/50 transition-colors group"
                                                                                                >
                                                                                                    {/* Jam Input */}
                                                                                                    <td className="px-3 py-3 border-r border-[#e4e4e7] align-top">
                                                                                                        <div className="flex items-center justify-center gap-1">
                                                                                                            <input
                                                                                                                type="time"
                                                                                                                value={
                                                                                                                    act.jamMulai
                                                                                                                }
                                                                                                                onChange={(
                                                                                                                    e
                                                                                                                ) =>
                                                                                                                    updateActivityField(
                                                                                                                        wIndex,
                                                                                                                        dIndex,
                                                                                                                        aIndex,
                                                                                                                        'jamMulai',
                                                                                                                        e
                                                                                                                            .target
                                                                                                                            .value
                                                                                                                    )
                                                                                                                }
                                                                                                                className="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none w-24 text-center transition-colors shadow-sm"
                                                                                                            />
                                                                                                            <span className="text-gray-400 font-black">
                                                                                                                -
                                                                                                            </span>
                                                                                                            <input
                                                                                                                type="time"
                                                                                                                value={
                                                                                                                    act.jamSelesai
                                                                                                                }
                                                                                                                onChange={(
                                                                                                                    e
                                                                                                                ) =>
                                                                                                                    updateActivityField(
                                                                                                                        wIndex,
                                                                                                                        dIndex,
                                                                                                                        aIndex,
                                                                                                                        'jamSelesai',
                                                                                                                        e
                                                                                                                            .target
                                                                                                                            .value
                                                                                                                    )
                                                                                                                }
                                                                                                                className="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none w-24 text-center transition-colors shadow-sm"
                                                                                                            />
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    {/* Kegiatan Input */}
                                                                                                    <td className="px-3 py-3 border-r border-[#e4e4e7] align-top">
                                                                                                        <textarea
                                                                                                            value={
                                                                                                                act.kegiatan
                                                                                                            }
                                                                                                            onChange={(
                                                                                                                e
                                                                                                            ) =>
                                                                                                                updateActivityField(
                                                                                                                    wIndex,
                                                                                                                    dIndex,
                                                                                                                    aIndex,
                                                                                                                    'kegiatan',
                                                                                                                    e
                                                                                                                        .target
                                                                                                                        .value
                                                                                                                )
                                                                                                            }
                                                                                                            rows={2}
                                                                                                            className="w-full bg-white border-2 border-gray-200 rounded p-2 text-sm font-semibold text-gray-800 focus:border-blue-600 outline-none resize-none transition-colors shadow-sm"
                                                                                                            placeholder="Ketik deskripsi kegiatan..."
                                                                                                        />
                                                                                                    </td>
                                                                                                    {/* Kode Matkul */}
                                                                                                    <td className="px-3 py-3 border-r border-[#e4e4e7] align-top text-center pt-4">
                                                                                                        <select
                                                                                                            value={
                                                                                                                act.kode
                                                                                                            }
                                                                                                            onChange={(
                                                                                                                e
                                                                                                            ) =>
                                                                                                                updateActivityField(
                                                                                                                    wIndex,
                                                                                                                    dIndex,
                                                                                                                    aIndex,
                                                                                                                    'kode',
                                                                                                                    e
                                                                                                                        .target
                                                                                                                        .value
                                                                                                                )
                                                                                                            }
                                                                                                            className="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-black text-gray-800 text-center focus:border-blue-600 outline-none w-full max-w-[80px] appearance-none cursor-pointer shadow-sm transition-all mx-auto block"
                                                                                                        >
                                                                                                            <option value="SUP">
                                                                                                                SUP
                                                                                                            </option>
                                                                                                            <option value="PII">
                                                                                                                PII
                                                                                                            </option>
                                                                                                            <option value="K3">
                                                                                                                K3
                                                                                                            </option>
                                                                                                            <option value="LTD">
                                                                                                                LTD
                                                                                                            </option>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    {/* Waktu Terkalkulasi */}
                                                                                                    <td className="px-4 py-3 border-r border-[#e4e4e7] align-top text-center pt-5">
                                                                                                        <span className="font-mono font-black text-lg text-gray-800">
                                                                                                            {act.waktu}{' '}
                                                                                                            &apos;
                                                                                                        </span>
                                                                                                    </td>
                                                                                                    {/* Aksi Hapus */}
                                                                                                    <td className="px-2 py-3 align-top pt-4 text-center">
                                                                                                        {day.activities
                                                                                                            .length >
                                                                                                            1 && (
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                onClick={() =>
                                                                                                                    removeActivity(
                                                                                                                        wIndex,
                                                                                                                        dIndex,
                                                                                                                        aIndex
                                                                                                                    )
                                                                                                                }
                                                                                                                className="text-gray-400 hover:text-red-600 transition-colors p-1.5 hover:bg-red-50 rounded"
                                                                                                                title="Hapus baris"
                                                                                                            >
                                                                                                                <svg
                                                                                                                    className="w-5 h-5"
                                                                                                                    fill="none"
                                                                                                                    stroke="currentColor"
                                                                                                                    strokeWidth="2.5"
                                                                                                                    viewBox="0 0 24 24"
                                                                                                                >
                                                                                                                    <path
                                                                                                                        strokeLinecap="round"
                                                                                                                        strokeLinejoin="round"
                                                                                                                        d="M6 18L18 6M6 6l12 12"
                                                                                                                    />
                                                                                                                </svg>
                                                                                                            </button>
                                                                                                        )}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            )
                                                                                        )}

                                                                                        {/* Footer Total Harian (Bersih) */}
                                                                                        <tr className="bg-gray-100 border-t border-[#e4e4e7]">
                                                                                            <td
                                                                                                colSpan={3}
                                                                                                className="px-4 py-3 text-right font-black text-gray-700 uppercase tracking-widest text-xs border-r border-[#e4e4e7]"
                                                                                            >
                                                                                                JUMLAH MENIT
                                                                                            </td>
                                                                                            <td className="px-4 py-3 text-center border-r border-[#e4e4e7]">
                                                                                                <span className="font-mono font-black text-gray-900 text-lg">
                                                                                                    {
                                                                                                        dayTotalMinutes
                                                                                                    }{' '}
                                                                                                    &apos;
                                                                                                </span>
                                                                                            </td>
                                                                                            <td />
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>

                                                                            {/* Tombol Tambah Baris */}
                                                                            <div className="mt-4">
                                                                                <button
                                                                                    type="button"
                                                                                    onClick={() =>
                                                                                        addActivity(
                                                                                            wIndex,
                                                                                            dIndex
                                                                                        )
                                                                                    }
                                                                                    className="text-sm font-bold text-gray-600 bg-white hover:text-blue-700 hover:border-blue-400 border-2 border-dashed border-gray-300 px-4 py-2 rounded-lg transition-all flex items-center gap-2 w-full justify-center"
                                                                                >
                                                                                    <svg
                                                                                        className="w-4 h-4"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        strokeWidth="3"
                                                                                        viewBox="0 0 24 24"
                                                                                    >
                                                                                        <path
                                                                                            strokeLinecap="round"
                                                                                            strokeLinejoin="round"
                                                                                            d="M12 4v16m8-8H4"
                                                                                        />
                                                                                    </svg>
                                                                                    Tambah Baris Kegiatan Baru
                                                                                </button>
                                                                            </div>

                                                                            {/* === AREA MULTI UPLOAD FOTO DOKUMENTASI === */}
                                                                            <div className="mt-5 border-t-2 border-dashed border-blue-100 pt-4">
                                                                                <div className="flex items-center justify-between mb-3">
                                                                                    <p className="text-xs font-black text-blue-700 uppercase tracking-widest flex items-center gap-2">
                                                                                        <svg
                                                                                            className="w-4 h-4"
                                                                                            fill="none"
                                                                                            stroke="currentColor"
                                                                                            strokeWidth="2.5"
                                                                                            viewBox="0 0 24 24"
                                                                                        >
                                                                                            <path
                                                                                                strokeLinecap="round"
                                                                                                strokeLinejoin="round"
                                                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                                                            />
                                                                                        </svg>
                                                                                        Foto Dokumentasi Kegiatan
                                                                                    </p>
                                                                                    <span className="text-[10px] font-bold text-blue-400 bg-blue-50 px-2 py-1 rounded-full border border-blue-200">
                                                                                        {
                                                                                            day.fotos
                                                                                                .length
                                                                                        }{' '}
                                                                                        foto tersimpan
                                                                                    </span>
                                                                                </div>

                                                                                {/* Grid thumbnail tersimpan */}
                                                                                {day.fotos.length >
                                                                                    0 && (
                                                                                    <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mb-3">
                                                                                        {day.fotos.map(
                                                                                            (
                                                                                                foto,
                                                                                                fIdx
                                                                                            ) => (
                                                                                                <div
                                                                                                    key={
                                                                                                        foto.id
                                                                                                    }
                                                                                                    className="relative group"
                                                                                                >
                                                                                                    <a
                                                                                                        href={
                                                                                                            foto.url
                                                                                                        }
                                                                                                        target="_blank"
                                                                                                        rel="noreferrer"
                                                                                                    >
                                                                                                        <img
                                                                                                            src={
                                                                                                                foto.url
                                                                                                            }
                                                                                                            alt={
                                                                                                                foto.filename
                                                                                                            }
                                                                                                            className="w-full aspect-square object-cover rounded-lg border-2 border-blue-200 group-hover:opacity-75 transition-opacity shadow-sm"
                                                                                                        />
                                                                                                    </a>
                                                                                                    <span className="absolute top-1 left-1 bg-blue-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded leading-none">
                                                                                                        {fIdx +
                                                                                                            1}
                                                                                                    </span>
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        onClick={(
                                                                                                            e
                                                                                                        ) => {
                                                                                                            e.preventDefault();
                                                                                                            deleteFotoById(
                                                                                                                wIndex,
                                                                                                                dIndex,
                                                                                                                foto.id
                                                                                                            );
                                                                                                        }}
                                                                                                        className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                                                                        title="Hapus foto ini"
                                                                                                    >
                                                                                                        <svg
                                                                                                            className="w-3 h-3"
                                                                                                            fill="none"
                                                                                                            stroke="currentColor"
                                                                                                            strokeWidth="3"
                                                                                                            viewBox="0 0 24 24"
                                                                                                        >
                                                                                                            <path
                                                                                                                strokeLinecap="round"
                                                                                                                strokeLinejoin="round"
                                                                                                                d="M6 18L18 6M6 6l12 12"
                                                                                                            />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </div>
                                                                                            )
                                                                                        )}
                                                                                    </div>
                                                                                )}

                                                                                {/* Preview file baru yang dipilih (belum upload) */}
                                                                                {day.fotoPreviews
                                                                                    .length > 0 && (
                                                                                    <div className="mb-3">
                                                                                        <p className="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-2">
                                                                                            📎 Antrian upload (
                                                                                            <span>
                                                                                                {
                                                                                                    day
                                                                                                        .fotoPreviews
                                                                                                        .length
                                                                                                }
                                                                                            </span>{' '}
                                                                                            foto baru — belum disimpan)
                                                                                        </p>
                                                                                        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                                                                            {day.fotoPreviews.map(
                                                                                                (
                                                                                                    prev,
                                                                                                    pIdx
                                                                                                ) => (
                                                                                                    <div
                                                                                                        key={
                                                                                                            pIdx
                                                                                                        }
                                                                                                        className="relative group"
                                                                                                    >
                                                                                                        <img
                                                                                                            src={
                                                                                                                prev.dataUrl
                                                                                                            }
                                                                                                            alt={
                                                                                                                prev.name
                                                                                                            }
                                                                                                            className="w-full aspect-square object-cover rounded-lg border-2 border-yellow-300 shadow-sm"
                                                                                                        />
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            onClick={(
                                                                                                                e
                                                                                                            ) => {
                                                                                                                e.preventDefault();
                                                                                                                removeFotoPreviewByIndex(
                                                                                                                    wIndex,
                                                                                                                    dIndex,
                                                                                                                    pIdx
                                                                                                                );
                                                                                                            }}
                                                                                                            className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                                                                        >
                                                                                                            <svg
                                                                                                                className="w-3 h-3"
                                                                                                                fill="none"
                                                                                                                stroke="currentColor"
                                                                                                                strokeWidth="3"
                                                                                                                viewBox="0 0 24 24"
                                                                                                            >
                                                                                                                <path
                                                                                                                    strokeLinecap="round"
                                                                                                                    strokeLinejoin="round"
                                                                                                                    d="M6 18L18 6M6 6l12 12"
                                                                                                                />
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                        <span className="absolute bottom-1 left-1 bg-yellow-400 text-yellow-900 text-[9px] font-black px-1 rounded leading-tight">
                                                                                                            BARU
                                                                                                        </span>
                                                                                                    </div>
                                                                                                )
                                                                                            )}
                                                                                        </div>
                                                                                    </div>
                                                                                )}

                                                                                {/* Input upload multi-foto */}
                                                                                <label
                                                                                    htmlFor={`foto-kerja-${week.id}-${day.id}`}
                                                                                    className={`flex flex-col items-center justify-center gap-1.5 border-2 border-dashed rounded-xl px-4 py-4 cursor-pointer transition-all ${
                                                                                        day
                                                                                            .fotoPreviews
                                                                                            .length >
                                                                                        0
                                                                                            ? 'border-yellow-300 bg-yellow-50 hover:bg-yellow-100'
                                                                                            : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50'
                                                                                    }`}
                                                                                >
                                                                                    <svg
                                                                                        className="w-6 h-6 text-gray-400"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        strokeWidth="1.5"
                                                                                        viewBox="0 0 24 24"
                                                                                    >
                                                                                        <path
                                                                                            strokeLinecap="round"
                                                                                            strokeLinejoin="round"
                                                                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                                                                                        />
                                                                                    </svg>
                                                                                    <span className="text-xs font-bold text-gray-500">
                                                                                        Klik atau seret foto ke sini
                                                                                    </span>
                                                                                    <span className="text-[10px] text-gray-400">
                                                                                        Bisa pilih banyak sekaligus · JPG/PNG/WEBP/GIF · Maks 5MB/foto
                                                                                    </span>
                                                                                    <input
                                                                                        id={`foto-kerja-${week.id}-${day.id}`}
                                                                                        type="file"
                                                                                        accept="image/*"
                                                                                        multiple
                                                                                        className="hidden"
                                                                                        onChange={(
                                                                                            e
                                                                                        ) =>
                                                                                            onFotosSelected(
                                                                                                e,
                                                                                                wIndex,
                                                                                                dIndex
                                                                                            )
                                                                                        }
                                                                                    />
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    ) : (
                                                                        /* JIKA STATUS LIBUR/IZIN/SAKIT */
                                                                        <div className="bg-gray-50 border-2 border-gray-200 p-5 rounded-xl space-y-4">
                                                                            <div>
                                                                                <label className="block text-sm font-black text-gray-600 mb-2 uppercase tracking-wide">
                                                                                    Keterangan / Alasan (Opsional)
                                                                                </label>
                                                                                <textarea
                                                                                    value={day.notes}
                                                                                    onChange={(e) =>
                                                                                        updateDay(
                                                                                            wIndex,
                                                                                            dIndex,
                                                                                            (d) => ({
                                                                                                ...d,
                                                                                                notes: e
                                                                                                    .target
                                                                                                    .value,
                                                                                            })
                                                                                        )
                                                                                    }
                                                                                    rows={2}
                                                                                    className="w-full bg-white border-2 border-gray-300 rounded-lg p-3 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none resize-none transition-colors"
                                                                                    placeholder="Ketik keterangan di sini jika diperlukan..."
                                                                                />
                                                                            </div>

                                                                            {/* === AREA MULTI UPLOAD FOTO (Libur/Izin/Sakit) === */}
                                                                            <div className="border-t-2 border-dashed border-gray-200 pt-4">
                                                                                <div className="flex items-center justify-between mb-3">
                                                                                    <p className="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                                                                        <svg
                                                                                            className="w-4 h-4"
                                                                                            fill="none"
                                                                                            stroke="currentColor"
                                                                                            strokeWidth="2.5"
                                                                                            viewBox="0 0 24 24"
                                                                                        >
                                                                                            <path
                                                                                                strokeLinecap="round"
                                                                                                strokeLinejoin="round"
                                                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                                                            />
                                                                                        </svg>
                                                                                        Foto Bukti / Dokumentasi (Opsional)
                                                                                    </p>
                                                                                    <span className="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full border border-gray-200">
                                                                                        {
                                                                                            day.fotos
                                                                                                .length
                                                                                        }{' '}
                                                                                        foto tersimpan
                                                                                    </span>
                                                                                </div>

                                                                                {day.fotos.length >
                                                                                    0 && (
                                                                                    <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mb-3">
                                                                                        {day.fotos.map(
                                                                                            (
                                                                                                foto,
                                                                                                fIdx
                                                                                            ) => (
                                                                                                <div
                                                                                                    key={
                                                                                                        foto.id
                                                                                                    }
                                                                                                    className="relative group"
                                                                                                >
                                                                                                    <a
                                                                                                        href={
                                                                                                            foto.url
                                                                                                        }
                                                                                                        target="_blank"
                                                                                                        rel="noreferrer"
                                                                                                    >
                                                                                                        <img
                                                                                                            src={
                                                                                                                foto.url
                                                                                                            }
                                                                                                            alt={
                                                                                                                foto.filename
                                                                                                            }
                                                                                                            className="w-full aspect-square object-cover rounded-lg border-2 border-gray-300 group-hover:opacity-75 transition-opacity shadow-sm"
                                                                                                        />
                                                                                                    </a>
                                                                                                    <span className="absolute top-1 left-1 bg-gray-700 text-white text-[10px] font-black px-1.5 py-0.5 rounded leading-none">
                                                                                                        {fIdx +
                                                                                                            1}
                                                                                                    </span>
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        onClick={(
                                                                                                            e
                                                                                                        ) => {
                                                                                                            e.preventDefault();
                                                                                                            deleteFotoById(
                                                                                                                wIndex,
                                                                                                                dIndex,
                                                                                                                foto.id
                                                                                                            );
                                                                                                        }}
                                                                                                        className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                                                                    >
                                                                                                        <svg
                                                                                                            className="w-3 h-3"
                                                                                                            fill="none"
                                                                                                            stroke="currentColor"
                                                                                                            strokeWidth="3"
                                                                                                            viewBox="0 0 24 24"
                                                                                                        >
                                                                                                            <path
                                                                                                                strokeLinecap="round"
                                                                                                                strokeLinejoin="round"
                                                                                                                d="M6 18L18 6M6 6l12 12"
                                                                                                            />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </div>
                                                                                            )
                                                                                        )}
                                                                                    </div>
                                                                                )}

                                                                                {day.fotoPreviews
                                                                                    .length > 0 && (
                                                                                    <div className="mb-3">
                                                                                        <p className="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-2">
                                                                                            📎 Antrian upload (
                                                                                            <span>
                                                                                                {
                                                                                                    day
                                                                                                        .fotoPreviews
                                                                                                        .length
                                                                                                }
                                                                                            </span>{' '}
                                                                                            foto baru — belum disimpan)
                                                                                        </p>
                                                                                        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                                                                            {day.fotoPreviews.map(
                                                                                                (
                                                                                                    prev,
                                                                                                    pIdx
                                                                                                ) => (
                                                                                                    <div
                                                                                                        key={
                                                                                                            pIdx
                                                                                                        }
                                                                                                        className="relative group"
                                                                                                    >
                                                                                                        <img
                                                                                                            src={
                                                                                                                prev.dataUrl
                                                                                                            }
                                                                                                            alt={
                                                                                                                prev.name
                                                                                                            }
                                                                                                            className="w-full aspect-square object-cover rounded-lg border-2 border-yellow-300 shadow-sm"
                                                                                                        />
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            onClick={(
                                                                                                                e
                                                                                                            ) => {
                                                                                                                e.preventDefault();
                                                                                                                removeFotoPreviewByIndex(
                                                                                                                    wIndex,
                                                                                                                    dIndex,
                                                                                                                    pIdx
                                                                                                                );
                                                                                                            }}
                                                                                                            className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                                                                        >
                                                                                                            <svg
                                                                                                                className="w-3 h-3"
                                                                                                                fill="none"
                                                                                                                stroke="currentColor"
                                                                                                                strokeWidth="3"
                                                                                                                viewBox="0 0 24 24"
                                                                                                            >
                                                                                                                <path
                                                                                                                    strokeLinecap="round"
                                                                                                                    strokeLinejoin="round"
                                                                                                                    d="M6 18L18 6M6 6l12 12"
                                                                                                                />
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                        <span className="absolute bottom-1 left-1 bg-yellow-400 text-yellow-900 text-[9px] font-black px-1 rounded leading-tight">
                                                                                                            BARU
                                                                                                        </span>
                                                                                                    </div>
                                                                                                )
                                                                                            )}
                                                                                        </div>
                                                                                    </div>
                                                                                )}

                                                                                <label
                                                                                    htmlFor={`foto-abs-${week.id}-${day.id}`}
                                                                                    className={`flex flex-col items-center justify-center gap-1.5 border-2 border-dashed rounded-xl px-4 py-4 cursor-pointer transition-all ${
                                                                                        day
                                                                                            .fotoPreviews
                                                                                            .length >
                                                                                        0
                                                                                            ? 'border-yellow-300 bg-yellow-50 hover:bg-yellow-100'
                                                                                            : 'border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50'
                                                                                    }`}
                                                                                >
                                                                                    <svg
                                                                                        className="w-6 h-6 text-gray-400"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        strokeWidth="1.5"
                                                                                        viewBox="0 0 24 24"
                                                                                    >
                                                                                        <path
                                                                                            strokeLinecap="round"
                                                                                            strokeLinejoin="round"
                                                                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                                                                                        />
                                                                                    </svg>
                                                                                    <span className="text-xs font-bold text-gray-500">
                                                                                        Klik atau seret foto ke sini
                                                                                    </span>
                                                                                    <span className="text-[10px] text-gray-400">
                                                                                        Bisa pilih banyak sekaligus · JPG/PNG/WEBP/GIF · Maks 5MB/foto
                                                                                    </span>
                                                                                    <input
                                                                                        id={`foto-abs-${week.id}-${day.id}`}
                                                                                        type="file"
                                                                                        accept="image/*"
                                                                                        multiple
                                                                                        className="hidden"
                                                                                        onChange={(
                                                                                            e
                                                                                        ) =>
                                                                                            onFotosSelected(
                                                                                                e,
                                                                                                wIndex,
                                                                                                dIndex
                                                                                            )
                                                                                        }
                                                                                    />
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    )}

                                                                    {/* TOMBOL SIMPAN HARIAN */}
                                                                    <div className="mt-6 pt-5 border-t border-[#e4e4e7] flex justify-end">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                saveDay(
                                                                                    wIndex,
                                                                                    dIndex
                                                                                )
                                                                            }
                                                                            disabled={
                                                                                day.uploadingFoto
                                                                            }
                                                                            className="bg-[#013981] hover:bg-[#012b63] text-white font-medium py-2.5 px-8 rounded-lg border border-[#e4e4e7] shadow-surface hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 text-sm flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0"
                                                                        >
                                                                            {!day.uploadingFoto ? (
                                                                                <svg
                                                                                    className="w-5 h-5"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    strokeWidth="3"
                                                                                    viewBox="0 0 24 24"
                                                                                >
                                                                                    <path
                                                                                        strokeLinecap="round"
                                                                                        strokeLinejoin="round"
                                                                                        d="M5 13l4 4L19 7"
                                                                                    />
                                                                                </svg>
                                                                            ) : (
                                                                                <svg
                                                                                    className="w-5 h-5 animate-spin"
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
                                                                                    />
                                                                                    <path
                                                                                        className="opacity-75"
                                                                                        fill="currentColor"
                                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                                                                    />
                                                                                </svg>
                                                                            )}
                                                                            <span>
                                                                                {day.uploadingFoto
                                                                                    ? 'Mengupload...'
                                                                                    : 'Simpan Hari Ini'}
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
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
