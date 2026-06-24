<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji {{ $bulanLabel }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
            width: 190mm;
            padding: 6mm 10mm;
        }

        /* ── HEADER (sama dengan kuitansi) ── */
        .header {
            width: 100%;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header table td { vertical-align: middle; padding: 0; }
        .header td.logo-cell { width: 72px; text-align: center; }
        .header td.logo-cell img { width: 66px; height: 66px; object-fit: contain; }
        .header td.info-cell { text-align: center; padding: 0 6px; }
        .header td.info-cell h1 {
            font-size: 12px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .header td.info-cell .alamat {
            font-size: 7.5px; color: #444; margin-top: 3px; line-height: 1.5;
        }

        /* ── JUDUL DOKUMEN ── */
        .judul-wrap { text-align: center; margin-bottom: 6px; }
        .judul-wrap h2 {
            font-size: 11px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1.5px;
        }
        .judul-wrap .garis { border-bottom: 1px solid #aaa; margin-top: 2px; }

        /* ── META: bulan + status ── */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .meta-table td { vertical-align: middle; padding: 0; font-size: 8.5px; color: #555; }
        .badge {
            display: inline-block; padding: 1px 7px; border-radius: 20px;
            font-size: 7.5px; font-weight: bold;
        }
        .badge-sudah { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-belum { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

        /* ── INFO KARYAWAN ── */
        .section-title {
            font-size: 8px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.04em; color: #374151;
            border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; margin-bottom: 5px;
        }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .info-table td.lbl { width: 80px; font-size: 7.5px; color: #9ca3af; text-transform: uppercase; }
        .info-table td.val { font-size: 9px; font-weight: 600; color: #1f2937; }

        /* ── REKAP KEHADIRAN (4 kolom horizontal) ── */
        .rekap-table { width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 6px; }
        .rekap-table td {
            padding: 5px 8px; border-radius: 3px; vertical-align: top; width: 25%;
        }
        .rekap-hadir { background: #dcfce7; }
        .rekap-sakit { background: #fef9c3; }
        .rekap-izin  { background: #fce7f3; }
        .rekap-alpha { background: #fee2e2; }
        .rekap-lbl { font-size: 7.5px; font-weight: 600; }
        .rekap-lbl-hadir { color: #15803d; }
        .rekap-lbl-sakit { color: #a16207; }
        .rekap-lbl-izin  { color: #be185d; }
        .rekap-lbl-alpha { color: #b91c1c; }
        .rekap-val { font-size: 12px; font-weight: 800; margin-top: 1px; }
        .rekap-val-hadir { color: #14532d; }
        .rekap-val-sakit { color: #92400e; }
        .rekap-val-izin  { color: #9d174d; }
        .rekap-val-alpha { color: #991b1b; }

        /* ── DETAIL TANGGAL ABSEN ── */
        .absen-wrap { margin-bottom: 7px; line-height: 1.8; }
        .absen-item {
            display: inline-block; font-size: 7.5px; padding: 1px 5px;
            border-radius: 3px; border: 1px solid #e5e7eb; margin: 1px;
        }
        .absen-hadir { background: #dcfce7; border-color: #bbf7d0; color: #15803d; }
        .absen-dinas { background: #dbeafe; border-color: #bfdbfe; color: #1d4ed8; }
        .absen-sakit { background: #fef9c3; border-color: #fde68a; color: #a16207; }
        .absen-izin  { background: #fce7f3; border-color: #fbcfe8; color: #be185d; }
        .absen-alpha { background: #fee2e2; border-color: #fecaca; color: #b91c1c; }

        /* ── TABEL RINCIAN GAJI ── */
        table.gaji { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.gaji th {
            background: #1f2937; color: #fff; font-size: 7.5px; font-weight: 600;
            padding: 4px 7px; text-align: left;
            letter-spacing: 0.04em; text-transform: uppercase;
        }
        table.gaji th.right { text-align: right; }
        table.gaji td { padding: 4px 7px; border-bottom: 1px solid #f1f5f9; font-size: 8.5px; }
        table.gaji td.right { text-align: right; font-weight: 600; }
        table.gaji tfoot td {
            background: #f8fafc; font-weight: 700; font-size: 9px;
            border-top: 2px solid #e5e7eb; border-bottom: none; padding: 5px 7px;
        }
        table.gaji tfoot td.total-val {
            text-align: right; color: #1e3a8a; font-size: 10px;
        }

        /* ── TANDA TANGAN (sejajar, pakai table seperti kuitansi) ── */
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .ttd-table td { vertical-align: bottom; text-align: center; padding: 0 8px; }
        .ttd-space { height: 60px; }
        .ttd-img { max-height: 50px; max-width: 140px; margin-bottom: 2px; }
        .ttd-line { padding-top: 3px; font-size: 8.5px; font-weight: bold; }
        .ttd-line-inner { border-top: 1px solid #333; width: 130px; margin: 0 auto; padding-top: 3px; }
        .ttd-sub  { font-size: 8px; color: #555; font-weight: normal; margin-top: 1px; }

        /* ── PAGE BREAK ── */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@foreach ($gajiList as $gaji)
    @php
        $k         = $gaji->karyawan;
        $totalGaji = (int)$gaji->gaji_pokok + (int)$gaji->tunjangan
                   + (int)$gaji->transport   + (int)$gaji->thr;

        $hariHadir = $gaji->detail_absen->whereIn('status', ['hadir','dinas'])->count();
        $hariSakit = $gaji->detail_absen->where('status', 'sakit')->count();
        $hariIzin  = $gaji->detail_absen->where('status', 'izin')->count();
        $hariAlpha = $gaji->detail_absen->where('status', 'alpha')->count();
    @endphp

    <div class="{{ !$loop->last ? 'page-break' : '' }}">

        {{-- ══ HEADER ══ --}}
        <div class="header">
            <table>
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('images/logo-yayasan.png') }}" alt="Logo Yayasan">
                    </td>
                    <td class="info-cell">
                        <h1>Yayasan Fajar Nusantara<br>Sekolah Islam Terpadu Bunga Cempaka</h1>
                        <div class="alamat">
                            Jln. Cempaka Raya, Desa Maja Timur RT.20, Kelurahan Margasari, Kec. Karawang Timur<br>
                            Pinggir Jalan Baru (Alternatif) Karawang Timur. Telp: +6281313986542 &nbsp;|&nbsp; Web: https://bungacempakarw.sch.id
                        </div>
                    </td>
                    <td class="logo-cell">
                        <img src="{{ public_path('images/logo-sit.png') }}" alt="Logo SIT">
                    </td>
                </tr>
            </table>
        </div>

        {{-- ══ JUDUL ══ --}}
        <div class="judul-wrap">
            <h2>Slip Gaji Karyawan</h2>
            <div class="garis"></div>
        </div>

        {{-- ══ META ══ --}}
        <table class="meta-table">
            <tr>
                <td>Periode: <strong>{{ $bulanLabel }} {{ $tahun }}</strong></td>
                <td style="text-align:right;">
                    @if ($gaji->status_bayar === 'sudah')
                        <span class="badge badge-sudah">✓ SUDAH DIBAYAR</span>
                        @if ($gaji->tanggal_bayar)
                            &nbsp;<span style="font-size:7.5px;color:#9ca3af;">
                                {{ $gaji->tanggal_bayar->format('d/m/Y') }}
                            </span>
                        @endif
                    @else
                        <span class="badge badge-belum">BELUM DIBAYAR</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ══ INFO KARYAWAN ══ --}}
        <div class="section-title">Data Karyawan</div>
        <table class="info-table">
            <tr>
                <td class="lbl">Nama</td>
                <td style="width:6px;">:</td>
                <td class="val">{{ $k->nama ?? '-' }}</td>
                <td class="lbl">Jabatan</td>
                <td style="width:6px;">:</td>
                <td class="val">{{ $k->jabatan ?? '-' }}</td>
            </tr>
        </table>

        {{-- ══ REKAP KEHADIRAN (4 kolom horizontal) ══ --}}
        <div class="section-title">Rekap Kehadiran</div>
        <table class="rekap-table">
            <tr>
                <td class="rekap-hadir">
                    <div class="rekap-lbl rekap-lbl-hadir">Hadir / Dinas</div>
                    <div class="rekap-val rekap-val-hadir">{{ $hariHadir }} hari</div>
                </td>
                <td class="rekap-sakit">
                    <div class="rekap-lbl rekap-lbl-sakit">Sakit</div>
                    <div class="rekap-val rekap-val-sakit">{{ $hariSakit }} hari</div>
                </td>
                <td class="rekap-izin">
                    <div class="rekap-lbl rekap-lbl-izin">Izin</div>
                    <div class="rekap-val rekap-val-izin">{{ $hariIzin }} hari</div>
                </td>
                <td class="rekap-alpha">
                    <div class="rekap-lbl rekap-lbl-alpha">Alpha</div>
                    <div class="rekap-val rekap-val-alpha">{{ $hariAlpha }} hari</div>
                </td>
            </tr>
        </table>

        {{-- ══ DETAIL TANGGAL KEHADIRAN ══ --}}
        @if ($gaji->detail_absen->count() > 0)
            <div class="section-title">Detail Kehadiran</div>
            <div class="absen-wrap">
                @foreach ($gaji->detail_absen as $absen)
                    @php
                        $cls = match($absen->status) {
                            'hadir'  => 'absen-hadir',
                            'dinas'  => 'absen-dinas',
                            'sakit'  => 'absen-sakit',
                            'izin'   => 'absen-izin',
                            default  => 'absen-alpha',
                        };
                    @endphp
                    <span class="absen-item {{ $cls }}">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->format('d') }} — {{ ucfirst($absen->status) }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- ══ RINCIAN GAJI ══ --}}
        <div class="section-title">Rincian Gaji</div>
        <table class="gaji">
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th class="right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="right">{{ number_format((int)$gaji->gaji_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Tunjangan</td>
                    <td class="right">{{ number_format((int)$gaji->tunjangan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Transport</td>
                    <td class="right">{{ number_format((int)$gaji->transport, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>THR</td>
                    <td class="right">{{ number_format((int)$gaji->thr, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL GAJI</strong></td>
                    <td class="right total-val">
                        Rp {{ number_format($totalGaji, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        @if ($gaji->keterangan)
            <div style="font-size:7.5px;color:#6b7280;margin-bottom:5px;">
                <strong>Keterangan:</strong> {{ $gaji->keterangan }}
            </div>
        @endif

        {{-- ══ TANDA TANGAN (sejajar kiri-kanan) ══ --}}
        <table class="ttd-table">
            <tr>
                <td style="width:50%;">
                    <div style="font-size:8px;color:#555;margin-bottom:2px;">Penerima Gaji,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line-inner"></div>
                    <div class="ttd-line">
                        {{ $k->nama ?? '-' }}
                        <div class="ttd-sub">Karyawan</div>
                    </div>
                </td>
                <td style="width:50%;">
                    <div style="font-size:8px;color:#555;margin-bottom:2px;">Karawang, {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->endOfMonth()->format('d M Y') }}</div>
                    <div class="ttd-space">
                        @if (!empty($ttdKepsek))
                            <img class="ttd-img" src="{{ $ttdKepsek }}" alt="TTD Kepala Sekolah">
                        @endif
                    </div>
                    <div class="ttd-line-inner"></div>
                    <div class="ttd-line">
                        Hj. Suci Andari S. S., M. Hum
                        <div class="ttd-sub">Kepala Sekolah</div>
                    </div>
                </td>
            </tr>
        </table>

    </div>
@endforeach

</body>
</html>
