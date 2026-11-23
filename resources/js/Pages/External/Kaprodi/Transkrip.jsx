import React from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function ExternalKaprodiTranskrip({
    mahasiswa,
    pm = 0,
    ps = 0,
    courses = [],
}) {
    const currentYear = new Date().getFullYear();
    const prodiName =
        mahasiswa?.program_studi?.nama_prodi ||
        mahasiswa?.programStudi?.nama_prodi ||
        '';
    const tanggalCetak = new Date().toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });

    return (
        <AppLayout title={`Daftar Nilai PPI - ${mahasiswa?.nim || ''}`}>
            <style>{`
                .transkrip-sheet { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; margin: 0; padding: 20px; background: #fff; }
                .transkrip-sheet .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px; }
                .transkrip-sheet .header h2 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
                .transkrip-sheet .header p { margin: 5px 0 0 0; font-size: 12pt; }
                .transkrip-sheet .student-info { width: 100%; margin-bottom: 20px; }
                .transkrip-sheet .student-info td { padding: 3px 5px; vertical-align: top; }
                .transkrip-sheet .student-info .label { width: 120px; font-weight: bold; }
                .transkrip-sheet table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; text-align: center; }
                .transkrip-sheet table.data-table th, .transkrip-sheet table.data-table td { border: 1px solid #000; padding: 6px; }
                .transkrip-sheet table.data-table th { background-color: #f2f2f2; font-weight: bold; }
                .transkrip-sheet table.data-table .text-left { text-align: left; }
                .transkrip-sheet .footer { width: 100%; margin-top: 40px; }
                .transkrip-sheet .footer td { width: 33%; text-align: center; vertical-align: bottom; height: 100px; }
                .transkrip-sheet .notes { font-size: 9pt; margin-top: 30px; border: 1px solid #000; padding: 10px; }
                @media print { .transkrip-sheet .btn-print { display: none; } }
                .transkrip-sheet .btn-print { background-color: #0056b3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; font-family: sans-serif; margin-bottom: 20px; }
            `}</style>

            <div className="transkrip-sheet">
                <a
                    href={`/external/kaprodi/export/${mahasiswa?.nim}?export=pdf`}
                    className="btn-print"
                >
                    Unduh PDF Resmi
                </a>

                <div className="header">
                    <h2>DAFTAR NILAI PPI EKSTERNAL (SEMESTER GENAP)</h2>
                    <p>Politeknik Manufaktur Bandung - Program Studi {prodiName}</p>
                </div>

                <table className="student-info">
                    <tbody>
                        <tr>
                            <td className="label">NIM</td>
                            <td>: {mahasiswa?.nim}</td>
                            <td className="label">Kelas</td>
                            <td>: {mahasiswa?.kelas}</td>
                        </tr>
                        <tr>
                            <td className="label">Nama Lengkap</td>
                            <td>: {mahasiswa?.nama_mhs}</td>
                            <td className="label">Tahun Ajaran</td>
                            <td>
                                : {currentYear - 1}/{currentYear}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table className="data-table">
                    <thead>
                        <tr>
                            <th rowSpan={2}>No</th>
                            <th rowSpan={2}>Kode MK</th>
                            <th rowSpan={2} className="text-left">
                                Mata Kuliah
                            </th>
                            <th colSpan={2}>P &amp; M (10%)</th>
                            <th colSpan={2}>P &amp; S (30%)</th>
                            <th colSpan={2}>Logbook MK (60%)</th>
                            <th rowSpan={2}>
                                Nilai Akhir
                                <br />
                                (NA)
                            </th>
                            <th rowSpan={2}>Predikat</th>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <th>Konversi</th>
                            <th>Nilai</th>
                            <th>Konversi</th>
                            <th>Rata-rata</th>
                            <th>Jml Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        {(courses || []).map((c, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                <td>{c.matkul?.kd_mat}</td>
                                <td className="text-left">{c.matkul?.nama_komp}</td>
                                <td>{Number(pm || 0).toFixed(2)}</td>
                                <td>{(Number(pm || 0) * 0.1).toFixed(2)}</td>
                                <td>{Number(ps || 0).toFixed(2)}</td>
                                <td>{(Number(ps || 0) * 0.3).toFixed(2)}</td>
                                <td>{Number(c.raw || 0).toFixed(2)}</td>
                                <td>{Number(c.jam || 0).toFixed(1)}</td>
                                <td style={{ fontWeight: 'bold' }}>
                                    {Number(c.akhir || 0).toFixed(2)}
                                </td>
                                <td style={{ fontWeight: 'bold' }}>{c.grade}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <div className="notes">
                    <strong>Formula Perhitungan (Sesuai Standar):</strong>
                    <br />
                    * Presentasi &amp; Makalah (P&amp;M) = (40% x N_Presentasi) + (60% x
                    N_Makalah)
                    <br />
                    * Prestasi &amp; Supervisi (P&amp;S) = (50% x N_Prestasi) + (50% x
                    N_Supervisi)
                    <br />* Nilai Akhir (NA) = (10% x P&amp;M) + (30% x P&amp;S) + (60% x
                    Rata-rata Mingguan MK)
                </div>

                <table className="footer">
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                Bandung, {tanggalCetak}
                                <br />
                                Kepala Program Studi
                                <br />
                                <br />
                                <br />
                                <br />
                                <br />
                                <strong>___________________________</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AppLayout>
    );
}
