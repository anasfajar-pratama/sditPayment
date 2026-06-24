<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan #{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</title>
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

        /* ── HEADER (identik kuitansi) ────────────────────── */
        .header {
            width: 100%;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header table td { vertical-align: middle; padding: 0; }
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

        /* ── JUDUL ───────────────────────────────────────── */
        .judul-wrap {
            text-align: center;
            margin-bottom: 6px;
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

        /* ── STATUS BADGE ────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 10px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-lunas   { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-cicilan { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-belum   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* ── DOC META ────────────────────────────────────── */
        .doc-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .doc-meta td { font-size: 9px; color: #555; padding: 0; }
        .doc-meta td.right { text-align: right; }

        /* ── SECTION TITLE ───────────────────────────────── */
        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            margin-bottom: 7px;
            letter-spacing: 0.4px;
        }

        /* ── INFO TABLE ──────────────────────────────────── */
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td.label { width: 120px; color: #555; }
        .info-table td.sep   { width: 10px; color: #555; }
        .info-table td.value { font-weight: 600; }

        /* ── NOMINAL BOX ──────────────────────────────────── */
        .nominal-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        .nominal-row { width: 100%; border-collapse: collapse; }
        .nominal-row td { padding: 0; vertical-align: middle; }
        .nominal-row td.right { text-align: right; }
        .nominal-label { font-size: 8.5px; color: #666; margin-bottom: 2px; }
        .nominal-value { font-size: 14px; font-weight: bold; color: #1a1a1a; }
        .nominal-value-green { font-size: 14px; font-weight: bold; color: #059669; }
        .nominal-value-amber { font-size: 14px; font-weight: bold; color: #d97706; }

        /* ── DETAIL TABLE ─────────────────────────────────── */
        table.detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9.5px;
        }
        table.detail thead tr th {
            background: #f1f5f9;
            padding: 4px 7px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table.detail thead tr th.right  { text-align: right; }
        table.detail thead tr th.center { text-align: center; }
        table.detail tbody tr td {
            padding: 4px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.detail tbody tr td.right  { text-align: right; }
        table.detail tbody tr td.center { text-align: center; }
        table.detail tfoot tr td {
            padding: 4px 7px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            background: #f8fafc;
        }
        table.detail tfoot tr td.right { text-align: right; }

        /* ── SISA BOX ─────────────────────────────────────── */
        .sisa-box {
            border: 1.5px solid #fca5a5;
            background: #fff5f5;
            border-radius: 1px;
            margin-bottom: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        
        .sisa-box td { padding: 6px 10px; vertical-align: middle; }
        .sisa-box td.right { text-align: right; padding: 6px 10px; }
        .sisa-label { font-size: 9.5px; color: #b91c1c; font-weight: bold; }
        .sisa-sub   { font-size: 8px; color: #dc2626; margin-top: 1px; }
        .sisa-value { font-size: 12px; color: #b91c1c; font-weight: bold; }

        /* ── LUNAS STAMP ──────────────────────────────────── */
        .lunas-stamp { text-align: center; margin: 8px 0 10px; }
        .lunas-stamp span {
            display: inline-block;
            border: 2.5px solid #059669;
            color: #059669;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 5px;
            padding: 4px 16px;
            border-radius: 3px;
            opacity: 0.85;
        }

        /* ── BELUM BAYAR BOX ──────────────────────────────── */
        .belum-box {
            border: 1.5px dashed #fca5a5;
            background: #fff5f5;
            border-radius: 4px;
            padding: 7px 12px;
            margin-bottom: 10px;
            text-align: center;
            color: #b91c1c;
            font-weight: bold;
            font-size: 10px;
        }
        .belum-sub { font-size: 8.5px; font-weight: normal; color: #dc2626; margin-top: 3px; }

        /* ── CATATAN ──────────────────────────────────────── */
        .catatan-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 3px;
            padding: 5px 10px;
            font-size: 8.5px;
            color: #78350f;
            margin-bottom: 10px;
        }

        /* ── BOTTOM TABLE (TTD + QR, identik kuitansi) ───── */
        .bottom-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .bottom-table td { vertical-align: bottom; padding: 0 6px; }
        .bottom-table td.ttd-cell { width: 150px; text-align: center; }
        .bottom-table td.qr-cell  { width: 125px; text-align: center; }
        .bottom-table td.info-cell { text-align: right; vertical-align: bottom; }
        .ttd-space { height: 46px; }
        .ttd-img { max-height: 55px; max-width: 140px; margin-bottom: 2px; }
        .garis-ttd {
            border-top: 1px solid #333;
            padding-top: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .qr-img { width: 110px; height: 110px; }
        .scan-label { font-size: 7px; color: #888; margin-top: 3px; line-height: 1.4; }
        .no-label   { font-size: 6.5px; color: #bbb; margin-top: 1px; }
        .cetak-info { font-size: 8px; color: #999; line-height: 1.6; }

        .mb-4 { margin-bottom: 10px; }
    </style>
</head>
<body>

{{-- ── HEADER: logo kiri | info tengah | logo kanan ────────── --}}
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

{{-- ── JUDUL + STATUS BADGE ─────────────────────────────────── --}}
<div class="judul-wrap">
    <h2>Tagihan Pembayaran</h2>
    <div style="margin-top:4px;">
        @if ($isLunas)
            <span class="badge badge-lunas">LUNAS</span>
        @elseif ($isCicilan)
            <span class="badge badge-cicilan">CICILAN – BELUM LUNAS</span>
        @else
            <span class="badge badge-belum">BELUM TERBAYAR</span>
        @endif
    </div>
    <div class="garis"></div>
</div>

{{-- ── NO. TAGIHAN + TANGGAL CETAK ─────────────────────────── --}}
<table class="doc-meta">
    <tr>
        <td>No. Tagihan : <strong>#{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
        <td class="right">Tanggal cetak : {{ $cetakTanggal }}</td>
    </tr>
</table>

{{-- ── DATA SISWA (full width, QR sudah di bawah) ─────────── --}}
<div class="section-title">Data Siswa</div>
<table class="info-table mb-4">
    <tr>
        <td class="label">NIS</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->siswa->nis ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Nama Siswa</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->siswa->nama }}</td>
    </tr>
    <tr>
        <td class="label">
            @if ($tagihan->siswa->is_calon) Jenis Calon @else Kelas @endif
        </td>
        <td class="sep">:</td>
        <td class="value">
            @if ($tagihan->siswa->is_calon)
                Calon Siswa {{ $tagihan->siswa->calon_jenis }}
            @else
                {{ $tagihan->siswa->kelas ?: '—' }}
            @endif
        </td>
    </tr>
    @if ($tagihan->siswa->jenis_sekolah && ! $tagihan->siswa->is_calon)
    <tr>
        <td class="label">Jenis Sekolah</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->siswa->jenis_sekolah }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Nama Orang Tua</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->siswa->nama_orang_tua ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">No. HP</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->siswa->no_hp_orang_tua ?: '—' }}</td>
    </tr>
</table>

{{-- ── DETAIL TAGIHAN ───────────────────────────────────────── --}}
<div class="section-title">Detail Tagihan</div>
<table class="info-table mb-4">
    <tr>
        <td class="label">Jenis Pembayaran</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->jenisPembayaran->nama }}</td>
    </tr>
    @if ($tagihan->bulan)
    <tr>
        <td class="label">Bulan</td>
        <td class="sep">:</td>
        <td class="value">{{ $bulanLabels[$tagihan->bulan] ?? $tagihan->bulan }}</td>
    </tr>
    @endif
    @if ($tagihan->tahun)
    <tr>
        <td class="label">Tahun</td>
        <td class="sep">:</td>
        <td class="value">{{ $tagihan->tahun }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Nominal Tagihan</td>
        <td class="sep">:</td>
        <td class="value">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════════════
     BELUM TERBAYAR
══════════════════════════════════════════════════════ --}}
@if ($isBelumBayar)

    <div class="nominal-box">
        <div class="nominal-label">Total yang Harus Dibayar</div>
        <div class="nominal-value">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</div>
    </div>

    <div class="belum-box">
        ⚠ TAGIHAN INI BELUM TERBAYAR
        <div class="belum-sub">Harap segera melakukan pembayaran ke bendahara sekolah.</div>
    </div>

{{-- ══════════════════════════════════════════════════════
     LUNAS
══════════════════════════════════════════════════════ --}}
@elseif ($isLunas)

    <div class="nominal-box">
        <table class="nominal-row">
            <tr>
                <td>
                    <div class="nominal-label">Total Nominal Tagihan</div>
                    <div class="nominal-value">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</div>
                </td>
                <td class="right">
                    <div class="nominal-label">Total Terbayar</div>
                    <div class="nominal-value-green">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if ($historiPembayaran->count() > 0)
        <div class="section-title">Riwayat Pembayaran</div>
        <table class="detail mb-4">
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Tanggal Bayar</th>
                    <th class="right">Nominal Dibayar</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historiPembayaran as $i => $bayar)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}</td>
                        <td class="right">Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                        <td class="center">{{ strtoupper($bayar->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Terbayar</td>
                    <td class="right">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                    <td class="center">—</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="lunas-stamp"><span>L U N A S</span></div>

{{-- ══════════════════════════════════════════════════════
     CICILAN
══════════════════════════════════════════════════════ --}}
@elseif ($isCicilan)

    <div class="nominal-box">
        <table class="nominal-row">
            <tr>
                <td>
                    <div class="nominal-label">Total Nominal Tagihan</div>
                    <div class="nominal-value">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</div>
                </td>
                <td class="right">
                    <div class="nominal-label">Sudah Terbayar</div>
                    <div class="nominal-value-amber">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Riwayat Cicilan</div>
    <table class="detail mb-4">
        <thead>
            <tr>
                <th class="center" style="width:28px;">No</th>
                <th>Tanggal Bayar</th>
                <th class="right">Nominal</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($historiPembayaran as $i => $bayar)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}</td>
                    <td class="right">Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                    <td class="center">{{ strtoupper($bayar->status) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total Terbayar</td>
                <td class="right">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table class="sisa-box">
        <tr>
            <td>
                <div class="sisa-label">Sisa Tagihan yang Belum Dibayar</div>
                <div class="sisa-sub">
                    Rp {{ number_format($nominalAsli, 0, ',', '.') }}
                    − Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
                </div>
            </td>
            <td class="right">
                <span class="sisa-value">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <div class="catatan-box">
        <strong>Catatan:</strong> Tagihan ini belum lunas. Harap segera melunasi sisa tagihan
        sebesar <strong>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong> ke bendahara sekolah.
    </div>

@endif

{{-- ── BOTTOM: TTD Kepsek | TTD Bendahara | QR Code ────────── --}}
<table class="bottom-table">
    <tr>
        {{-- Kepala Sekolah --}}
        <td class="ttd-cell">
            <div style="font-size:9px;">Mengetahui,</div>
            <div class="ttd-space">
                @if ($ttdKepsek)
                    <img class="ttd-img" src="{{ $ttdKepsek }}" alt="TTD Kepala Sekolah">
                @endif
            </div>
            <div class="garis-ttd">Hj. Suci Andari S. S., M.Hum<br>Kepala Sekolah</div>
        </td>

        {{-- Bendahara --}}
        <td class="ttd-cell">
            <div style="font-size:9px;">Karawang, {{ now()->format('d M Y') }}</div>
            <div class="ttd-space">
                @if ($ttdBendahara)
                    <img class="ttd-img" src="{{ $ttdBendahara }}" alt="TTD Bendahara">
                @endif
            </div>
            <div class="garis-ttd">Rita Erninda S.M<br>Bendahara</div>
        </td>

        {{-- Cetak info (tengah bawah) --}}
        <td class="info-cell" style="text-align:center; vertical-align:bottom; padding-bottom:4px;">
            <div class="cetak-info" style="text-align:center;">
                Dicetak : {{ $cetakTanggal }}<br>
                Oleh &nbsp;&nbsp;: {{ auth()->user()?->name ?? 'Sistem' }}<br>
                Dok &nbsp;&nbsp;&nbsp;: #{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </td>

        {{-- QR Code --}}
        <td class="qr-cell">
            <img class="qr-img"
                 src="data:image/svg+xml;base64,{{ base64_encode(\QrCode::format('svg')->size(110)->generate($urlTagihan)) }}"
                 alt="QR Tagihan">
            <div class="scan-label">Scan untuk verifikasi<br>tagihan secara online</div>
            <div class="no-label">#{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</div>
        </td>
    </tr>
</table>

</body>
</html>
