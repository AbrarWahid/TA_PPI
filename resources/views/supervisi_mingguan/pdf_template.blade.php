<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Supervisi Mingguan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            padding: 18px 24px;
        }
        .kop {
            width: 100%;
            border-bottom: 3px solid #004d4d;
            padding-bottom: 8px;
            margin-bottom: 0;
            display: table;
        }
        .kop-logo { display: table-cell; width: 70px; vertical-align: middle; text-align: center; }
        .kop-logo-box {
            width: 56px; height: 56px; border: 2px solid #004d4d; border-radius: 6px;
            display: inline-block; line-height: 56px; font-size: 7px; font-weight: bold;
            color: #004d4d; text-align: center;
        }
        .kop-tengah { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10px; }
        .kop-tengah .inst { font-size: 13px; font-weight: bold; color: #004d4d; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-tengah .prodi { font-size: 10px; font-weight: bold; color: #333; text-transform: uppercase; margin-top: 2px; }
        .kop-tengah .alamat { font-size: 8px; color: #666; margin-top: 3px; }
        .kop-kode { display: table-cell; width: 80px; vertical-align: middle; text-align: center; }
        .kop-kode table { border-collapse: collapse; width: 100%; }
        .kop-kode td { border: 1px solid #004d4d; padding: 4px 6px; font-size: 9px; font-weight: bold; color: #004d4d; text-align: center; }
        .cover {
            text-align: center; margin: 14px 0; padding: 12px;
            border: 1.5px solid #004d4d; border-radius: 4px; background: #f0fafa;
        }
        .cover .doc-type { font-size: 14px; font-weight: bold; color: #004d4d; text-transform: uppercase; letter-spacing: 1px; }
        .cover .doc-sub { font-size: 10px; font-weight: bold; color: #333; text-transform: uppercase; margin-top: 2px; }
        .cover .periode { font-size: 9px; color: #555; margin-top: 6px; }
        .cover .minggu-badge {
            display: inline-block; margin-top: 6px; padding: 3px 14px;
            background: #004d4d; color: white; font-size: 9px; font-weight: bold;
            border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .cover .nama { font-size: 12px; font-weight: bold; color: #004d4d; margin-top: 10px; }
        .cover .nim-kelas { font-size: 9px; color: #555; margin-top: 2px; }
        .info-section { margin-bottom: 12px; }
        .info-section table { width: 100%; border-collapse: collapse; }
        .info-section td { padding: 3px 6px 3px 0; font-size: 9.5px; vertical-align: top; }
        .info-section .lbl { width: 30%; font-weight: bold; color: #004d4d; text-transform: uppercase; font-size: 8.5px; }
        .info-section .sep { width: 3%; }
        .info-section .val { width: 67%; }
        .section-title {
            background: #004d4d; color: white; font-size: 9px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 10px; margin-bottom: 0;
        }
        table.kegiatan { width: 100%; border-collapse: collapse; margin-top: 0; margin-bottom: 12px; }
        table.kegiatan thead tr { background: #e6f4f1; }
        table.kegiatan th {
            padding: 6px 8px; text-align: center; font-size: 8.5px; font-weight: bold;
            text-transform: uppercase; color: #004d4d; border: 1px solid #b2d8d8; letter-spacing: 0.3px;
        }
        table.kegiatan td { padding: 5px 8px; border: 1px solid #d1e8e8; font-size: 9px; vertical-align: top; }
        table.kegiatan tr:nth-child(even) td { background: #f8fdfd; }
        table.kegiatan .td-no    { text-align: center; width: 4%; }
        table.kegiatan .td-hari  { text-align: center; width: 10%; font-weight: bold; }
        table.kegiatan .td-tgl   { text-align: center; width: 12%; }
        table.kegiatan .td-jam   { text-align: center; width: 13%; }
        table.kegiatan .td-act   { width: 40%; }
        table.kegiatan .td-kend  { width: 21%; color: #c0392b; font-style: italic; }
        table.kegiatan .tr-total td {
            background: #004d4d; color: white; font-weight: bold;
            font-size: 8.5px; padding: 4px 8px; border: 1px solid #003d3d;
        }
        table.identitas { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.identitas td { border: 1px solid #b2d8d8; padding: 4px 8px; font-size: 9px; vertical-align: middle; }
        table.identitas .lbl-id { font-weight: bold; color: #004d4d; font-size: 8px; text-transform: uppercase; }
        table.ket { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.ket td { border: 1px solid #b2d8d8; padding: 6px 10px; font-size: 8.5px; vertical-align: top; }
        .ket-nilai { width: 28%; }
        .ket-kode  { width: 40%; }
        .ket-nilai-box { width: 14%; text-align: center; }
        .ket-ttd   { width: 18%; text-align: center; vertical-align: bottom; }
        .ket-title { font-weight: bold; color: #004d4d; text-transform: uppercase; font-size: 8px; margin-bottom: 4px; }
        .ttd-wrap { display: table; width: 100%; margin-top: 10px; }
        .ttd-col  { display: table-cell; width: 50%; text-align: center; padding: 0 20px; }
        .ttd-place { font-size: 9px; color: #555; margin-bottom: 4px; }
        .ttd-line { border-top: 1px solid #004d4d; margin: 50px auto 6px; width: 80%; }
        .ttd-name { font-size: 9.5px; font-weight: bold; color: #004d4d; text-transform: uppercase; }
        .ttd-sub  { font-size: 8px; color: #888; margin-top: 2px; }
        .narasi-section { margin-top: 16px; page-break-before: always; }
        .narasi-day { margin-bottom: 10px; }
        .narasi-day-title {
            font-size: 10px; font-weight: bold; color: #004d4d;
            border-bottom: 1px solid #b2d8d8; padding-bottom: 3px; margin-bottom: 5px;
        }
        .narasi-day p { font-size: 9.5px; line-height: 1.6; color: #333; text-align: justify; }
        .cetak-info { font-size: 8px; color: #aaa; text-align: right; margin-top: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
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
                <tr><td>SUP-{{ str_pad($minggu_ke, 2, '0', STR_PAD_LEFT) }}</td></tr>
            </table>
        </div>
    </div>

    {{-- COVER / JUDUL --}}
    <div class="cover">
        <div class="doc-type">Laporan Supervisi Mingguan</div>
        <div class="doc-sub">Program Studi Automation Engineering &mdash; PBL</div>
        <div class="periode">
            Mata Kuliah: <strong>{{ $matkul->nama_matkul ?? '-' }}</strong>
            @if(!empty($matkul->kode_matkul))
            ({{ $matkul->kode_matkul }})
            @endif
        </div>
        <div><span class="minggu-badge">Minggu ke-{{ $minggu_ke }}</span></div>
        <div class="nama">{{ $mahasiswa->nama ?? '-' }}</div>
        <div class="nim-kelas">
            NIM: {{ $mahasiswa->nim ?? '-' }}
            @if(!empty($mahasiswa->kelas))
            &nbsp;|&nbsp; Kelas: {{ $mahasiswa->kelas }}
            @endif
        </div>
    </div>

    <div class="cetak-info">Dicetak pada: {{ now()->format('d F Y, H:i') }}</div>

    {{-- INFO IDENTITAS --}}
    <div class="info-section">
        <table>
            <tr>
                <td class="lbl">Nama Mahasiswa</td>
                <td class="sep">:</td>
                <td class="val">{{ $mahasiswa->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">NIM</td>
                <td class="sep">:</td>
                <td class="val">{{ $mahasiswa->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Mata Kuliah</td>
                <td class="sep">:</td>
                <td class="val">
                    {{ $matkul->nama_matkul ?? '-' }}
                    
@if(!empty($matkul->kode_matkul)) ({{ $matkul->kode_matkul }}) 
@endif
                </td>
            </tr>
            <tr>
                <td class="lbl">Dosen Pengampu</td>
                <td class="sep">:</td>
                <td class="val">
                    {{ $dosen->nama_dosen ?? '-' }}
                    
@if(!empty($dosen->nidn)) (NIDN: {{ $dosen->nidn }}) 
@endif
                </td>
            </tr>
            <tr>
                <td class="lbl">Minggu ke</td>
                <td class="sep">:</td>
                <td class="val">{{ $minggu_ke }}</td>
            </tr>
            <tr>
                <td class="lbl">Periode</td>
                <td class="sep">:</td>
                <td class="val">
                    @if($logs->isNotEmpty())
                        {{ \Carbon\Carbon::parse($logs->first()->tanggal)->format('d F Y') }}
                        s.d.
                        {{ \Carbon\Carbon::parse($logs->last()->tanggal)->format('d F Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL KEGIATAN --}}
    @php
        $totalMenit = 0;
        foreach ($logs as $h) {
            $m = \Carbon\Carbon::parse($h->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($h->jam_selesai));
            $totalMenit += $m;
        }
    @endphp

    <div class="section-title">Tabel Kegiatan Supervisi Mingguan</div>
    <table class="kegiatan">
        <thead>
            <tr>
                <th class="td-no">No</th>
                <th class="td-hari">Hari</th>
                <th class="td-tgl">Tanggal</th>
                <th class="td-jam">Jam</th>
                <th class="td-act">Kegiatan / Aktivitas</th>
                <th class="td-kend">Kendala</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $i => $h)
            @php
                $mulai   = \Carbon\Carbon::parse($h->jam_mulai);
                $selesai = \Carbon\Carbon::parse($h->jam_selesai);
                $menit   = $mulai->diffInMinutes($selesai);
                $durasi  = floor($menit / 60) . ' jam' . ($menit % 60 > 0 ? ' ' . ($menit % 60) . ' mnt' : '');
            @endphp
            <tr>
                <td class="td-no">{{ $i + 1 }}</td>
                <td class="td-hari">{{ strtoupper($h->hari) }}</td>
                <td class="td-tgl">{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                <td class="td-jam">
                    {{ $mulai->format('H:i') }} &ndash; {{ $selesai->format('H:i') }}<br>
                    <span style="font-size:8px; color:#666;">({{ $durasi }})</span>
                </td>
                <td class="td-act">{{ $h->aktivitas }}</td>
                <td class="td-kend">{{ $h->kendala ?? '&mdash;' }}</td>
            </tr>
            @endforeach
            <tr class="tr-total">
                <td colspan="3" style="text-align:right;">Total Durasi Kegiatan</td>
                <td style="text-align:center;">
                    {{ floor($totalMenit / 60) }} jam
                    
@if($totalMenit % 60 > 0){{ $totalMenit % 60 }} mnt
@endif
                </td>
                <td colspan="2">{{ $logs->count() }} sesi kegiatan tercatat</td>
            </tr>
        </tbody>
    </table>

    {{-- IDENTITAS BAWAH TABEL --}}
    <table class="identitas">
        <tr>
            <td style="width:25%">
                <div class="lbl-id">NIM</div>
                <div>{{ $mahasiswa->nim ?? '-' }}</div>
            </td>
            <td style="width:35%">
                <div class="lbl-id">Nama Mahasiswa</div>
                <div>{{ $mahasiswa->nama ?? '-' }}</div>
            </td>
            <td style="width:20%">
                <div class="lbl-id">Mata Kuliah</div>
                <div>{{ $matkul->nama_matkul ?? '-' }}</div>
            </td>
            <td style="width:20%">
                <div class="lbl-id">Dosen Pengampu</div>
                <div>{{ $dosen->nama_dosen ?? '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- KETERANGAN & NILAI --}}
    <table class="ket">
        <tr>
            <td class="ket-nilai">
                <div class="ket-title">Keterangan Nilai</div>
                A &nbsp;= 85 &ndash; 100<br>
                AB = 78 &ndash; 84<br>
                B &nbsp;= 70 &ndash; 77<br>
                BC = 63 &ndash; 69<br>
                C &nbsp;= 55 &ndash; 62<br>
                D &nbsp;= 41 &ndash; 54<br>
                E &nbsp;= 0 &ndash; 40
            </td>
            <td class="ket-kode">
                <div class="ket-title">Status Laporan</div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Mata Kuliah</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $matkul->nama_matkul ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Minggu ke</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $minggu_ke }}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Jumlah Sesi</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">{{ $logs->count() }} sesi</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">Total Jam</td>
                        <td style="padding:2px 4px; font-size:8.5px; border:1px solid #ccc;">
                            {{ floor($totalMenit / 60) }} jam
                            
@if($totalMenit % 60 > 0){{ $totalMenit % 60 }} mnt
@endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="ket-nilai-box">
                <div class="ket-title">Nilai</div>
                <div style="border: 2px solid #004d4d; height: 40px; margin-top:4px;"></div>
            </td>
            <td class="ket-ttd" style="text-align:center; padding:6px;">
                <div style="font-size:8px; color:#555; margin-bottom:2px;">
                    Bandung, {{ now()->format('d F Y') }}
                </div>
                <div style="font-size:8px; color:#555;">Dosen Pengampu</div>
                <div style="border-top:1px solid #004d4d; margin: 36px 10px 4px;"></div>
                <div style="font-size:8.5px; font-weight:bold; color:#004d4d;">{{ $dosen->nama_dosen ?? '-' }}</div>
                @if(!empty($dosen->nidn))
                <div style="font-size:7.5px; color:#888;">NIDN: {{ $dosen->nidn }}</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- TTD MAHASISWA & DOSEN --}}
    <div class="ttd-wrap">
        <div class="ttd-col">
            <div class="ttd-place">Mahasiswa</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">{{ $mahasiswa->nama ?? '-' }}</div>
            <div class="ttd-sub">NIM: {{ $mahasiswa->nim ?? '-' }}</div>
        </div>
        <div class="ttd-col">
            <div class="ttd-place">Dosen Pengampu</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">{{ $dosen->nama_dosen ?? '-' }}</div>
            <div class="ttd-sub">NIDN: {{ $dosen->nidn ?? '-' }}</div>
        </div>
    </div>

    {{-- NARASI KEGIATAN (halaman 2) --}}
    @if($logs->isNotEmpty())
    <div class="narasi-section">
        <div class="section-title" style="margin-bottom:10px;">Dokumentasi Kegiatan &mdash; Narasi Harian</div>
        @foreach($logs as $h)
        <div class="narasi-day">
            <div class="narasi-day-title">
                {{ $h->hari }}, {{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d F Y') }}
                &nbsp;|&nbsp;
                {{ \Carbon\Carbon::parse($h->jam_mulai)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($h->jam_selesai)->format('H:i') }}
            </div>
            <p>{{ $h->aktivitas }}</p>
            @if(!empty($h->kendala))
            <p style="margin-top:4px; color:#c0392b; font-style:italic;">
                <strong>Kendala:</strong> {{ $h->kendala }}
            </p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</body>
</html>
