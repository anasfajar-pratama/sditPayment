<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan Gabungan - {{ $siswa->nama }}</title>
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

        /* ── HEADER ──────────────────────────────────────── */
        .header {
            width: 100%;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header table td { vertical-align: middle; padding: 0; }
        .header table td.logo-cell { width: 85px; text-align: center; }
        .header table td.logo-cell img { width: 80px; height: 80px; object-fit: contain; }
        .header table td.info-cell { text-align: center; padding: 0 8px; }
        .header table td.info-cell h1 {
            font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .header table td.info-cell .alamat {
            font-size: 8px; color: #444; margin-top: 3px; line-height: 1.5;
        }

        /* ── JUDUL ───────────────────────────────────────── */
        .judul-wrap { text-align: center; margin-bottom: 6px; }
        .judul-wrap h2 {
            font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;
        }
        .judul-wrap .garis { border-bottom: 1px solid #aaa; margin-top: 3px; }

        /* ── DOC META ────────────────────────────────────── */
        .doc-meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .doc-meta td { font-size: 9px; color: #555; padding: 0; }
        .doc-meta td.right { text-align: right; }

        /* ── SECTION TITLE ───────────────────────────────── */
        .section-title {
            font-size: 9.5px; font-weight: bold; text-transform: uppercase; color: #444;
            border-bottom: 1px solid #ddd; padding-bottom: 2px; margin-bottom: 7px; letter-spacing: 0.4px;
        }

        /* ── INFO TABLE ──────────────────────────────────── */
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td.label { width: 120px; color: #555; }
        .info-table td.sep   { width: 10px; color: #555; }
        .info-table td.value { font-weight: 600; }

        /* ── DETAIL TABLE ─────────────────────────────────── */
        table.detail { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9.5px; }
        table.detail thead tr th {
            background: #f1f5f9; padding: 4px 7px; text-align: left; font-weight: bold;
            border: 1px solid #e2e8f0; font-size: 9px;
        }
        table.detail thead tr th.right  { text-align: right; }
        table.detail thead tr th.center { text-align: center; }
        table.detail tbody tr td { padding: 4px 7px; border: 1px solid #e2e8f0; vertical-align: middle; }
        table.detail tbody tr td.right  { text-align: right; }
        table.detail tbody tr td.center { text-align: center; }
        table.detail tfoot tr td {
            padding: 4px 7px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;
        }
        table.detail tfoot tr td.right { text-align: right; }

        /* ── GRAND TOTAL BOX ─────────────────────────────── */
        .nominal-box {
            background: #fff5f5; border: 1.5px solid #fca5a5; border-radius: 4px;
            padding: 8px 12px; margin-bottom: 10px;
        }
        .nominal-row { width: 100%; border-collapse: collapse; }
        .nominal-row td { vertical-align: middle; }
        .nominal-row td.right { text-align: right; }
        .nominal-label { font-size: 8.5px; color: #b91c1c; font-weight: bold; }
        .nominal-value { font-size: 14px; font-weight: bold; color: #b91c1c; }

        .catatan-box {
            background: #fffbeb; border: 1px solid #fde68a; border-radius: 3px;
            padding: 5px 10px; font-size: 8.5px; color: #78350f; margin-bottom: 10px;
        }

        /* ── BOTTOM TABLE (TTD + cetak info) ─────────────── */
        .bottom-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .bottom-table td { vertical-align: bottom; padding: 0 6px; }
        .bottom-table td.ttd-cell { width: 320px; text-align: center; }
        .bottom-table td.info-cell { text-align: center; vertical-align: bottom; }
        .ttd-space { height: 96px; display: flex; align-items: flex-end; justify-content: center; }
        .ttd-img { max-height: 94px; max-width: 247px; }
        .garis-ttd {
            border-top: 1px solid #333; padding: 1px 20px 0; font-weight: bold; font-size: 9px;
            display: inline-block;
        }
        .cetak-info { font-size: 8px; color: #999; line-height: 1.6; }

        .mb-4 { margin-bottom: 10px; }
    </style>
</head>
<body>

{{-- ── HEADER ───────────────────────────────────────────────── --}}
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
                <img src="{{ public_path('images/logo-sit.png') }}" alt="Logo Yayasan">
            </td>
        </tr>
    </table>
</div>

{{-- ── JUDUL ──────────────────────────────────────────────────── --}}
<div class="judul-wrap">
    <h2>Tagihan Gabungan</h2>
    <div class="garis"></div>
</div>

{{-- ── META ───────────────────────────────────────────────────── --}}
<table class="doc-meta">
    <tr>
        <td>NIS : <strong>{{ $siswa->nis ?: '—' }}</strong></td>
        <td class="right">Tanggal cetak : {{ $cetakTanggal }}</td>
    </tr>
</table>

{{-- ── DATA SISWA ─────────────────────────────────────────────── --}}
<div class="section-title">Data Siswa</div>
<table class="info-table mb-4">
    <tr>
        <td class="label">NIS</td>
        <td class="sep">:</td>
        <td class="value">{{ $siswa->nis ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Nama Siswa</td>
        <td class="sep">:</td>
        <td class="value">
            @if ($siswa->is_calon)
                {{ $siswa->nama }} / {{ $siswa->kelasSaatIni?->jenis_sekolah ?? $siswa->calon_jenis ?? '—' }}
            @else
                {{ $siswa->nama }} / {{ $siswa->kelasSaatIni?->kelas ?? '—' }} / {{ $siswa->kelasSaatIni?->jenis_sekolah ?? '—' }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Nama Orang Tua</td>
        <td class="sep">:</td>
        <td class="value">{{ $siswa->nama_orang_tua ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">No. HP</td>
        <td class="sep">:</td>
        <td class="value">{{ $siswa->no_hp_orang_tua ?: '—' }}</td>
    </tr>
</table>

{{-- ── RINCIAN TAGIHAN ────────────────────────────────────────── --}}
<div class="section-title">Rincian Tagihan Belum Bayar</div>
<table class="detail mb-4">
    <thead>
        <tr>
            <th class="center" style="width:4%;">No</th>
            <th>Jenis Tagihan</th>
            <th>Bulan &amp; Tahun</th>
            <th class="right">Total Tagihan</th>
            <th class="right">Terbayar</th>
            <th class="right">Nominal Tagihan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $i => $r)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $r['jenis'] }}</td>
                <td>{{ $r['periode'] }}</td>
                <td class="right">Rp {{ number_format($r['total'], 0, ',', '.') }}</td>
                <td class="right">
                    {{ $r['terbayar'] > 0 ? 'Rp ' . number_format($r['terbayar'], 0, ',', '.') : '—' }}
                </td>
                <td class="right">Rp {{ number_format($r['nominal'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center" style="padding:14px;color:#9ca3af;">
                    Tidak ada tagihan belum bayar.
                </td>
            </tr>
        @endforelse
    </tbody>
    @if (count($rows) > 0)
        <tfoot>
            <tr>
                <td colspan="3">TOTAL ({{ count($rows) }} tagihan)</td>
                <td class="right">Rp {{ number_format($totalAsli, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    @endif
</table>

@if (count($rows) > 0)
    <div class="nominal-box">
        <table class="nominal-row">
            <tr>
                <td>
                    <div class="nominal-label">SISA TAGIHAN YANG HARUS DIBAYAR</div>
                </td>
                <td class="right">
                    <span class="nominal-value">Rp {{ number_format($totalSisa, 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="catatan-box">
        <strong>Catatan:</strong> Daftar di atas adalah gabungan seluruh tagihan yang belum lunas
        (termasuk yang sedang dicicil). Harap segera melunasi total sisa sebesar
        <strong>Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong> ke bendahara sekolah.
    </div>
@endif

{{-- ── BOTTOM: TTD Bendahara | Cetak Info ─────────────────────── --}}
<table class="bottom-table">
    <tr>
        <td class="ttd-cell">
            <div style="font-size:9px;">Karawang, {{ now()->format('d M Y') }}</div>
            <div class="ttd-space">
                @if ($ttdBendahara)
                    <img class="ttd-img" src="{{ $ttdBendahara }}" alt="TTD Bendahara">
                @endif
            </div>
            <div class="garis-ttd">Rita Erninda S.M<br>Bendahara</div>
        </td>

        <td class="info-cell">
            <div class="cetak-info">
                Dicetak : {{ $cetakTanggal }}<br>
                Oleh &nbsp;&nbsp;: {{ auth()->user()?->name ?? 'Sistem' }}<br>
                Siswa &nbsp;: {{ $siswa->nama }}
            </div>
        </td>
    </tr>
</table>

</body>
</html>
