<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi #{{ $nomorKuitansi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            background: #fff;
            width: 190mm;
            padding: 8mm 10mm;
        }

        /* ── HEADER ── */
        .header {
            width: 100%;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header table td {
            vertical-align: middle;
            padding: 0;
        }
        .header table td.logo-cell {
            width: 85px;
            text-align: center;
        }
        .header table td.logo-cell img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .header table td.info-cell {
            text-align: center;
            padding: 0 8px;
        }
        .header table td.info-cell h1 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header table td.info-cell .alamat {
            font-size: 8px;
            color: #444;
            margin-top: 3px;
            line-height: 1.5;
        }

        /* ── JUDUL ── */
        .judul-wrap {
            text-align: center;
            margin-bottom: 8px;
        }
        .judul-wrap h2 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .judul-wrap .garis {
            border-bottom: 1px solid #aaa;
            margin-top: 3px;
        }

        /* ── NO & STATUS ── */
        .doc-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #555;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-lunas   { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-cicilan { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

        /* ── MAIN: 2 kolom ── */
        .main-layout {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .col-left  { flex: 1; }
        .col-right {
            width: 120px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* ── BODY KUITANSI ── */
        .body-kuitansi {
            border: 1.5px solid #777;
            border-radius: 3px;
            padding: 9px 11px;
            margin-bottom: 8px;
        }
        .row-k {
            display: flex;
            align-items: baseline;
            margin-bottom: 8px;
        }
        .row-k:last-child { margin-bottom: 0; }
        .row-k .lbl {
            min-width: 115px;
            color: #444;
            flex-shrink: 0;
            font-size: 10px;
        }
        .row-k .sep { margin: 0 5px; flex-shrink: 0; }
        .row-k .val {
            font-weight: 600;
            font-size: 10px;
            border-bottom: 1px dotted #aaa;
            flex: 1;
            padding-bottom: 1px;
        }

        /* ── JUMLAH ── */
        .jumlah-row {
            display: flex;
            align-items: center;
        }
        .jumlah-row .lbl {
            min-width: 115px;
            font-size: 10px;
            color: #444;
            flex-shrink: 0;
        }
        .jumlah-row .sep { margin: 0 5px; flex-shrink: 0; }
        .jumlah-row .box-rp {
            flex: 1;
            border: 1.5px solid #1a1a1a;
            border-radius: 2px;
            padding: 4px 8px;
            font-size: 13px;
            font-weight: bold;
            text-align: right;
        }

        /* ── TERBILANG ── */
        .terbilang-row {
            display: flex;
            align-items: baseline;
            margin-top: 5px;
        }
        .terbilang-row .lbl {
            min-width: 115px;
            color: #444;
            flex-shrink: 0;
            font-size: 9px;
        }
        .terbilang-row .sep { margin: 0 5px; flex-shrink: 0; font-size: 9px; }
        .terbilang-row .val {
            font-size: 9px;
            font-style: italic;
            color: #555;
            flex: 1;
        }

        /* ── BOTTOM TABLE ── */
        .bottom-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .bottom-table td { vertical-align: bottom; padding: 0 6px; }
        .bottom-table td.ttd-cell { width: 150px; text-align: center; }
        .bottom-table td.stamp-cell { text-align: center; }
        .bottom-table td.qr-cell { width: 125px; text-align: center; }
        .ttd-space { height: 46px; }
        .garis-ttd { border-top: 1px solid #333; padding-top: 3px; font-weight: bold; font-size: 9px; }
        .stamp {
            display: inline-block;
            border: 2.5px solid #059669;
            color: #059669;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 4px;
            padding: 4px 12px;
            border-radius: 3px;
            opacity: 0.85;
        }
        .qr-img { width: 110px; height: 110px; }
        .scan-label { font-size: 7px; color: #888; margin-top: 3px; line-height: 1.4; }
        .no-label { font-size: 6.5px; color: #bbb; margin-top: 1px; 
    }

    </style>
</head>
<body>

{{-- HEADER: logo kiri | info tengah | logo kanan --}}
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
                    Pinggir Jalan Baru (Alternatif) Karawang Timur. Telp:+6281313986542 Web: https://bungacempakarw.sch.id 
                </div>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo-sit.png') }}" alt="Logo SIT">
            </td>
        </tr>
    </table>
</div>

{{-- JUDUL --}}
<div class="judul-wrap">
    <h2>Kuitansi Pembayaran</h2>
    <div class="garis"></div>
</div>

{{-- NO & STATUS --}}
<div class="doc-meta">
    <span>
        No: <strong>{{ $nomorKuitansi }}</strong>
        &nbsp;
        @if($isLunas)
            <span class="badge badge-lunas">LUNAS</span>
        @else
            <span class="badge badge-cicilan">CICILAN</span>
        @endif
    </span>
    <span>Tanggal cetak: {{ $cetakTanggal }}</span>
</div>

{{-- MAIN LAYOUT --}}
<div class="main-layout">

    {{-- KOLOM KIRI --}}
    <div class="col-left" style="width:100%;">

        <div class="body-kuitansi">
            {{-- Sudah diterima dari --}}
            <div class="row-k">
                <span class="lbl">Sudah diterima dari</span>
                <span class="sep">:</span>
                <span class="val">
                    {{ $pembayaran->siswa->nis }} – {{ $pembayaran->siswa->nama }}
                    (Kelas {{ $pembayaran->siswa->kelas }})
                </span>
            </div>

            {{-- Banyaknya uang --}}
            <div class="row-k">
                <span class="lbl">Banyaknya uang</span>
                <span class="sep">:</span>
                <span class="val">{{ $terbilang }}</span>
            </div>

            {{-- Untuk pembayaran --}}
            <div class="row-k">
                <span class="lbl">Untuk pembayaran</span>
                <span class="sep">:</span>
                <span class="val">
                    {{ $pembayaran->jenisPembayaran->nama }}
                    @if($pembayaran->bulan) {{ $bulanLabels[$pembayaran->bulan] ?? $pembayaran->bulan }} @endif
                    @if($pembayaran->tahun) {{ $pembayaran->tahun }} @endif
                </span>
            </div>

            {{-- Jumlah Rp --}}
            <div class="jumlah-row">
                <span class="lbl">Jumlah Rp</span>
                <span class="sep">:</span>
                <div class="box-rp">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</div>
            </div>

            {{-- Terbilang --}}
            <!-- <div class="terbilang-row">
                <span class="lbl">Terbilang</span>
                <span class="sep">:</span>
                <span class="val">{{ $terbilang }}</span>
            </div> -->
        </div>

        {{-- BOTTOM: TTD | STAMP | QR sejajar --}}
        <table class="bottom-table">
            <tr>
                {{-- TTD Bendahara --}}
                <td class="ttd-cell">
                    <div class="ttd-space"></div>
                    <div style="font-size:9px; margin-bottom:2px;">Karawang, 
                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }}
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="garis-ttd">Rita Erninda S.M<br>Bendahara</div>
                </td>

                {{-- Kepsek --}}
                <td class="ttd-cell">
                    <div class="ttd-space"></div>
                    <div style="font-size:9px; margin-bottom:2px;"></div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="garis-ttd">Hj. Suci Andari S. S. , M. Hum<br>
                    Kepala Sekolah</div>
                </td>
<td class="stamp-cell"><td>
                {{-- QR Code --}}
                <td class="qr-cell">
                    <img class="qr-img"
                        src="data:image/svg+xml;base64,{{ base64_encode(\QrCode::format('svg')->size(110)->generate($urlKuitansi)) }}"
                        alt="QR Kuitansi">
                    <div class="scan-label">Scan untuk verifikasi<br>kuitansi secara online</div>
                    <div class="no-label">{{ $nomorKuitansi }}</div>
                </td>
            </tr>
        </table>

</div>

</body>
</html>
