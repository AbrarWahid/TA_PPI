<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Logbook Mingguan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; padding: 18px 24px; }
        .kop { width: 100%; border-bottom: 3px solid #004d4d; padding-bottom: 8px; margin-bottom: 0; display: table; }
        .kop-logo { display: table-cell; width: 70px; vertical-align: middle; text-align: center; }
        .kop-logo-box { width: 56px; height: 56px; border: 2px solid #004d4d; border-radius: 6px; display: inline-block; line-height: 28px; font-size: 7px; font-weight: bold; color: #004d4d; text-align: center; padding-top: 6px; }
        .kop-tengah { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10px; }
        .kop-tengah .inst { font-size: 13px; font-weight: bold; color: #004d4d; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-tengah .prodi { font-size: 10px; font-weight: bold; color: #333; text-transform: uppercase; margin-top: 2px; }
        .kop-tengah .alamat { font-size: 8px; color: #666; margin-top: 3px; }
        .kop-kode { display: table-cell; width: 80px; vertical-align: middle; text-align: center; }
        .kop-kode table { border-collapse: collapse; width: 100%; }
        .kop-kode td { border: 1px solid #004d4d; padding: 4px 6px; font-size: 9px; font-weight: bold; color: #004d4d; text-align: center; }
        .cover { text-align: center; margin: 14px 0; padding: 12px; border: 1.5px solid #004d4d; border-radius: 4px; background: #f0fafa; }
        .cover .doc-type { font-size: 14px; font-weight: bold; color: #004d4d; text-transform: uppercase; letter-spacing: 1px; }
        .cover .doc-sub { font-size: 10px; font-weight: bold; color: #333; text-transform: uppercase; margin-top: 2px; }
        .cover .periode { font-size: 9px; color: #555; margin-top: 6px; }
        .cover .minggu-badge { display: inline-block; margin-top: 6px; padding: 3px 14px; background: #004d4d; color: white; font-size: 9px; font-weight: bold; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .cover .nama { font-size: 12px; font-weight: bold; color: #004d4d; margin-top: 10px; }
        .cover .nim-kelas { font-size: 9px; color: #555; margin-top: 2px; }
        .info-section { margin-bottom: 12px; }
        .info-section table { width: 100%; border-collapse: collapse; }
        .info-section td { padding: 3px 6px 3px 0; font-size: 9.5px; vertical-align: top; }
        .info-section .lbl { width: 30%; font-weight: bold; color: #004d4d; text-transform: uppercase; font-size: 8.5px; }
        .info-section .sep { width: 3%; }
        .info-section .val { width: 67%; }
        .section-title { background: #004d4d; color: white; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 10px; margin-bottom: 0; }
        table.kegiatan { width: 100%; border-collapse: collapse; margin-top: 0; margin-bottom: 12px; }
        table.kegiatan thead tr { background: #e6f4f1; }
        table.kegiatan th { padding: 6px 8px; text-align: center; font-size: 8.5px; font-weight: bold; text-transform: uppercase; color: #004d4d; border: 1px solid #b2d8d8; letter-spacing: 0.3px; }
        table.kegiatan td { padding: 5px 8px; border: 1px solid #d1e8e8; font-size: 9px; vertical-align: top; }
        table.kegiatan tr:nth-child(even) td { background: #f8fdfd; }
        table.kegiatan .td-no   { text-align: center; width: 4%; }
        table.kegiatan .td-hari { text-align: center; width: 8%; font-weight: bold; }
        table.kegiatan .td-tgl  { text-align: center; width: 11%; }
        table.kegiatan .td-jam  { text-align: center; width: 14%; }
        table.kegiatan .td-act  { width: 38%; }
        table.kegiatan .td-kendala { width: 18%; color: #c0392b; }
        table.kegiatan .td-dok  { text-align: center; width: 7%; }
        table.kegiatan .tr-total td { background: #004d4d; color: white; font-weight: bold; font-size: 8.5px; padding: 4px 8px; border: 1px solid #003d3d; }
        table.identitas { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.identitas td { border: 1px solid #b2d8d8; padding: 4px 8px; font-size: 9px; vertical-align: middle; }
        table.identitas .lbl-id { font-weight: bold; color: #004d4d; font-size: 8px; text-transform: uppercase; }
        table.ket { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.ket td { border: 1px solid #b2d8d8; padding: 6px 10px; font-size: 8.5px; vertical-align: top; }
        .ket-title { font-weight: bold; color: #004d4d; text-transform: uppercase; font-size: 8px; margin-bottom: 4px; }
        .ttd-wrap { display: table; width: 100%; margin-top: 10px; }
        .ttd-col  { display: table-cell; width: 50%; text-align: center; padding: 0 20px; }
        .ttd-place { font-size: 9px; color: #555; margin-bottom: 4px; }
        .ttd-line { border-top: 1px solid #004d4d; margin: 50px auto 6px; width: 80%; }
        .ttd-name { font-size: 9.5px; font-weight: bold; color: #004d4d; text-transform: uppercase; }
        .ttd-sub  { font-size: 8px; color: #888; margin-top: 2px; }
        .narasi-section { margin-top: 16px; page-break-before: always; }
        .narasi-day { margin-bottom: 10px; }
        .narasi-day-title { font-size: 10px; font-weight: bold; color: #004d4d; border-bottom: 1px solid #b2d8d8; padding-bottom: 3px; margin-bottom: 5px; }
        .narasi-day p { font-size: 9.5px; line-height: 1.6; color: #333; text-align: justify; }
        .narasi-kendala { font-size: 9px; color: #c0392b; margin-top: 4px; font-style: italic; }
        .cetak-info { font-size: 8px; color: #aaa; text-align: right; margin-top: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="kop">
        <div class="kop-logo">
            <div class="kop-logo-box">POLMAN<br>BANDUNG</div>
        </div>
        <div class="kop-tengah">
            <div class="inst">Politeknik Manufaktur Bandung</div>
            <div class="prodi">Program Studi Automation Engineering</div>
            <div class="alamat">Jl. Kanayakan No. 21, Dago 40135, Bandung, Indonesia &nbsp;|&nbsp; polman-bandung.ac.id</div>
        </div>
        <div class="kop-kode">
            <table>
                <tr><td>AE</td></tr>
                <tr><td>LB-{{ str_pad($mingguKe, 2, '0', STR_PAD_LEFT) }}</td></tr>
            </table>
        </div>
    </div>

    <div class="cover">
        <div class="doc-type">Laporan Logbook Mingguan</div>
        <div class="doc-sub">Program Studi Automation Engineering &mdash; PBL</div>
        <div class="periode">Proyek: <strong>{{ $proyek->judul_proyek ?? '-' }}</strong></div>
        <div><span class="minggu-badge">Minggu ke-{{ $mingguKe }}</span></div>
        <div class="nama">{{ $mahasiswa->nama ?? ($mahasiswa->user->name ?? '-') }}</div>
        <div class="nim-kelas">NIM: {{ $mahasiswa->nim ?? '-' }}</div>
    </div>

    <div class="cetak-info">Dicetak pada: {{ now()->format('d F Y, H:i') }}</div>

    <div class="info-section">
        <table>
            <tr>
                <td class="lbl">Nama Mahasiswa</td><td class="sep">:</td>
                <td class="val">{{ $mahasiswa->nama ?? ($mahasiswa->user->name ?? '-') }}</td>
            </tr>
            <tr>
                <td class="lbl">NIM</td><td class="sep">:</td>
                <td class="val">{{ $mahasiswa->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Judul Proyek</td><td class="sep">:</td>
                <td class="val">{{ $proyek->judul_proyek ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Manager Proyek</td><td class="sep">:</td>
                <td class="val">{{ $proyek->manager->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Minggu ke</td><td class="sep">:</td>
                <td class="val">{{ $mingguKe }}</td>
            </tr>
            <tr>
                <td class="lbl">Periode</td><td class="sep">:</td>
                <td class="val">
                    @if($harian->isNotEmpty())
                        {{ \Carbon\Carbon::parse($harian->first()->tanggal)->format('d F Y') }}
                        s.d.
                        {{ \Carbon\Carbon::parse($harian->last()->tanggal)->format('d F Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Tabel Kegiatan Logbook Mingguan</div>
    <table class="kegiatan">
        <thead>
            <tr>
                <th class="td-no">No</th>
                <th class="td-hari">Hari</th>
                <th class="td-tgl">Tanggal</th>
                <th class="td-jam">Jam</th>
                <th class="td-act">Kegiatan / Aktivitas</th>
                <th class="td-kendala">Kendala</th>
                <th class="td-dok">Dok.</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMenit = 0; @endphp
            @foreach($harian as $i => $log)
            @php
                $durasi = '';
                $menitLog = 0;
                if ($log->jam_masuk && $log->jam_keluar) {
                    $masuk  = \Carbon\Carbon::parse($log->jam_masuk);
                    $keluar = \Carbon\Carbon::parse($log->jam_keluar);
                    $menitLog = $masuk->diffInMinutes($keluar);
                    $totalMenit += $menitLog;
                    $jam  = intdiv($menitLog, 60);
                    $mnt  = $menitLog % 60;
                    $durasi = ($jam > 0 ? $jam . ' jam ' : '') . ($mnt > 0 ? $mnt . ' mnt' : '');
                }
            @endphp
            <tr>
                <td class="td-no">{{ $i + 1 }}</td>
                <td class="td-hari">{{ strtoupper($log->hari) }}</td>
                <td class="td-tgl">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                <td class="td-jam">
                    @if($log->jam_masuk && $log->jam_keluar)
                        {{ \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') }}
                        -
                        {{ \Carbon\Carbon::parse($log->jam_keluar)->format('H:i') }}
                        <br>
                        <span style="font-size:8px; color:#666;">({{ $durasi }})</span>
                    @else
                        <span style="color:#9ca3af">-</span>
                    @endif
                </td>
                <td class="td-act">{{ $log->aktivitas }}</td>
                <td class="td-kendala">
                    @if($log->kendala)
                        {{ $log->kendala }}
                    @else
                        <span style="color:#9ca3af">-</span>
                    @endif
                </td>
                <td class="td-dok">
                    @if($log->dokumentasi)
                        Ada
                    @else
                        <span style="color:#9ca3af">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @php
                $totalJam = intdiv($totalMenit, 60);
                $totalMnt = $totalMenit % 60;
                $totalDurasi = ($totalJam > 0 ? $totalJam . ' jam ' : '') . ($totalMnt > 0 ? $totalMnt . ' mnt' : ($totalJam == 0 ? '0 mnt' : ''));
            @endphp
            <tr class="tr-total">
                <td colspan="3" style="text-align:right;">Total Durasi Kegiatan</td>
                <td>{{ $totalDurasi }}</td>
                <td colspan="3">{{ $harian->count() }} hari tercatat</td>
            </tr>
        </tbody>
    </table>

    <table class="identitas">
        <tr>
            <td style="width:25%">
                <div class="lbl-id">NIM</div>
                <div>{{ $mahasiswa->nim ?? '-' }}</div>
            </td>
            <td style="width:35%">
                <div class="lbl-id">Nama Mahasiswa</div>
                <div>{{ $mahasiswa->nama ?? ($mahasiswa->user->name ?? '-') }}</div>
            </td>
            <td style="width:40%">
                <div class="lbl-id">Judul Proyek</div>
                <div>{{ $proyek->judul_proyek ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="ket">
        <tr>
            <td style="width:28%">
                <div class="ket-title">Keterangan Nilai</div>
                A &nbsp;= 85 &ndash; 100<br>
                AB = 78 &ndash; 84<br>
                B &nbsp;= 70 &ndash; 77<br>
                BC = 63 &ndash; 69<br>
                C &nbsp;= 55 &ndash; 62<br>
                D &nbsp;= 41 &ndash; 54<br>
                E &nbsp;= 0 &ndash; 40
            </td>
            <td style="width:40%">
                <div class="ket-title">Status Laporan</div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Proyek</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $proyek->judul_proyek ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Minggu ke</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $mingguKe }}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Jumlah Hari</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $harian->count() }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Total Jam</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $totalDurasi }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:14%; text-align:center;">
                <div class="ket-title">Nilai</div>
                <div style="border: 2px solid #004d4d; height: 40px; margin-top:4px;"></div>
            </td>
            <td style="width:18%; text-align:center; vertical-align:bottom; padding:6px;">
                <div style="font-size:8px; color:#555; margin-bottom:2px;">Bandung, {{ now()->format('d F Y') }}</div>
                <div style="font-size:8px; color:#555;">Manager Proyek</div>
                <div style="border-top:1px solid #004d4d; margin: 36px 10px 4px;"></div>
                <div style="font-size:8.5px; font-weight:bold; color:#004d4d;">{{ $proyek->manager->name ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="ttd-wrap">
        <div class="ttd-col">
            <div class="ttd-place">Mahasiswa</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">{{ $mahasiswa->nama ?? ($mahasiswa->user->name ?? '-') }}</div>
            <div class="ttd-sub">NIM: {{ $mahasiswa->nim ?? '-' }}</div>
        </div>
        <div class="ttd-col">
            <div class="ttd-place">Manager Proyek</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">{{ $proyek->manager->name ?? '-' }}</div>
        </div>
    </div>

    @if($harian->isNotEmpty())
    <div class="narasi-section">
        <div class="section-title" style="margin-bottom:10px;">Dokumentasi Kegiatan &mdash; Narasi Harian</div>
        @foreach($harian as $log)
        <div class="narasi-day">
            <div class="narasi-day-title">
                {{ $log->hari }}, {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}
                @if($log->jam_masuk && $log->jam_keluar)
                    ({{ \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_keluar)->format('H:i') }})
                @endif
            </div>
            <p>{{ $log->aktivitas }}</p>
            @if($log->kendala)
            <p class="narasi-kendala">Kendala: {{ $log->kendala }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</body>
</html>