<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 24px 28px;
        }

        /* ── HEADER ───────────────────────────────────── */
        .header {
            text-align: center;
            border-bottom: 2.5px solid #1a1a1a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header .school-name {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header .school-sub {
            font-size: 9.5px;
            color: #555;
            margin-top: 2px;
        }
        .header .doc-title {
            margin-top: 8px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }

        /* ── STATUS BADGE ─────────────────────────────── */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        .badge-lunas    { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-cicilan  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-tagihan  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* ── NO. KUITANSI ─────────────────────────────── */
        .doc-meta {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #555;
            margin-bottom: 12px;
        }

        /* ── INFO SISWA ───────────────────────────────── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 8px;
            letter-spacing: 0.4px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 14px;
        }
        .info-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 110px;
            color: #555;
        }
        .info-table td.sep {
            width: 12px;
            color: #555;
        }
        .info-table td.value {
            font-weight: 600;
        }

        /* ── NOMINAL BOX ──────────────────────────────── */
        .nominal-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .nominal-box .label-nominal {
            font-size: 9.5px;
            color: #666;
            margin-bottom: 3px;
        }
        .nominal-box .value-nominal {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .nominal-box .terbilang {
            font-size: 9px;
            color: #777;
            margin-top: 2px;
            font-style: italic;
        }

        /* ── CICILAN TABLE ────────────────────────────── */
        table.detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }
        table.detail thead tr th {
            background: #f1f5f9;
            padding: 5px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
        }
        table.detail thead tr th.right { text-align: right; }
        table.detail thead tr th.center { text-align: center; }
        table.detail tbody tr td {
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.detail tbody tr td.right  { text-align: right; }
        table.detail tbody tr td.center { text-align: center; }
        table.detail tfoot tr td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            background: #f8fafc;
        }
        table.detail tfoot tr td.right { text-align: right; }

        /* ── SISA BOX ─────────────────────────────────── */
        .sisa-box {
            border: 1.5px solid #fca5a5;
            background: #fff5f5;
            border-radius: 5px;
            padding: 7px 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sisa-box .sisa-label { font-size: 10px; color: #b91c1c; font-weight: bold; }
        .sisa-box .sisa-value { font-size: 12px; color: #b91c1c; font-weight: bold; }

        /* ── LUNAS STAMP ──────────────────────────────── */
        .lunas-stamp {
            text-align: center;
            margin: 8px 0;
        }
        .lunas-stamp span {
            display: inline-block;
            border: 3px solid #059669;
            color: #059669;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 4px;
            padding: 4px 18px;
            border-radius: 4px;
            transform: rotate(-6deg);
            opacity: 0.85;
        }

        /* ── FOOTER ───────────────────────────────────── */
        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer .ttd {
            text-align: center;
            font-size: 10px;
        }
        .footer .ttd .ttd-space {
            height: 46px;
        }
        .footer .ttd .ttd-name {
            border-top: 1px solid #333;
            padding-top: 3px;
            min-width: 100px;
            font-weight: bold;
            font-size: 10px;
        }
        .footer .cetak-info {
            font-size: 8.5px;
            color: #999;
            text-align: right;
        }

        .mb-4 { margin-bottom: 14px; }
        .highlight-row { background: #fefce8; }
    </style>
</head>
<body>

    {{-- ── HEADER ────────────────────────────────────────── --}}
    <div class="header">
        <div class="school-name">sditPayment</div>
        <div class="school-sub">Sistem Informasi Pembayaran Sekolah</div>
        <div class="doc-title">
            @if ($isLunas)
                Kuitansi Pembayaran
            @elseif ($isCicilan)
                Bukti Cicilan Pembayaran
            @else
                Tagihan Pembayaran
            @endif
        </div>
        <div>
            @if ($isLunas)
                <span class="status-badge badge-lunas">LUNAS</span>
            @elseif ($isCicilan)
                <span class="status-badge badge-cicilan">CICILAN</span>
            @else
                <span class="status-badge badge-tagihan">BELUM LUNAS</span>
            @endif
        </div>
    </div>

    {{-- ── NO. DOKUMEN ─────────────────────────────────────── --}}
    <div class="doc-meta">
        <span>No. {{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
        <span>Tanggal cetak: {{ $cetakTanggal }}</span>
    </div>

    {{-- ── INFO SISWA ──────────────────────────────────────── --}}
    <div class="section-title">Data Siswa</div>
    <table class="info-table mb-4">
        <tr>
            <td class="label">NIS</td>
            <td class="sep">:</td>
            <td class="value">{{ $pembayaran->siswa->nis }}</td>
        </tr>
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="sep">:</td>
            <td class="value">{{ $pembayaran->siswa->nama }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td class="value">{{ $pembayaran->siswa->kelasSaatIni?->kelas ?? '—' }}</td>
        </tr>
    </table>

    {{-- ── INFO PEMBAYARAN ─────────────────────────────────── --}}
    <div class="section-title">Detail Pembayaran</div>
    <table class="info-table mb-4">
        <tr>
            <td class="label">Jenis Pembayaran</td>
            <td class="sep">:</td>
            <td class="value">{{ $pembayaran->jenisPembayaran->nama }}</td>
        </tr>
        @if ($pembayaran->bulan)
        <tr>
            <td class="label">Bulan</td>
            <td class="sep">:</td>
            <td class="value">{{ $bulanLabels[$pembayaran->bulan] ?? $pembayaran->bulan }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Tahun</td>
            <td class="sep">:</td>
            <td class="value">{{ $pembayaran->tahun }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td class="sep">:</td>
            <td class="value">
                {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }}
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════════
         SPP LUNAS → tampilan sederhana dengan nominal
    ══════════════════════════════════════════════════════ --}}
    @if ($isSpp && $isLunas)

        <div class="nominal-box">
            <div class="label-nominal">Nominal Dibayar</div>
            <div class="value-nominal">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</div>
        </div>

        <div class="lunas-stamp">
            <span>L U N A S</span>
        </div>

    {{-- ══════════════════════════════════════════════════════
         NON-SPP LUNAS → tampilkan history cicilan
    ══════════════════════════════════════════════════════ --}}
    @elseif (!$isSpp && $isLunas)

        <div class="nominal-box">
            <div class="label-nominal">Total Nominal Tagihan</div>
            <div class="value-nominal">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</div>
        </div>

        @if ($historiCicilan->count() > 1)
            <div class="section-title">Riwayat Pembayaran / Cicilan</div>
            <table class="detail mb-4">
                <thead>
                    <tr>
                        <th class="center">No</th>
                        <th>Tanggal</th>
                        <th class="right">Nominal Bayar</th>
                        <th class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historiCicilan as $i => $cicil)
                        <tr class="">
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($cicil->tanggal_bayar)->format('d M Y') }}</td>
                            <td class="right">Rp {{ number_format($cicil->nominal, 0, ',', '.') }}</td>
                            <td class="center">{{ strtoupper($cicil->status) }}</td>
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

        <div class="lunas-stamp">
            <span>L U N A S</span>
        </div>

    {{-- ══════════════════════════════════════════════════════
         CICILAN (belum lunas) → tagihan + history cicilan + sisa
    ══════════════════════════════════════════════════════ --}}
    @elseif ($isCicilan)

        <div class="nominal-box">
            <div class="label-nominal">Total Nominal Tagihan (Asli)</div>
            <div class="value-nominal">Rp {{ number_format($nominalAsli, 0, ',', '.') }}</div>
        </div>

        <div class="section-title">Riwayat Cicilan</div>
        <table class="detail mb-4">
            <thead>
                <tr>
                    <th class="center">No</th>
                    <th>Tanggal Bayar</th>
                    <th class="right">Nominal</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historiCicilan as $i => $cicil)
                    <tr class="">
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($cicil->tanggal_bayar)->format('d M Y') }}</td>
                        <td class="right">Rp {{ number_format($cicil->nominal, 0, ',', '.') }}</td>
                        <td class="center">{{ strtoupper($cicil->status) }}</td>
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

        <div class="sisa-box">
            <span class="sisa-label">Sisa Tagihan</span>
            <span class="sisa-value">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
        </div>

    @endif

    {{-- ── FOOTER / TTD ──────────────────────────────────────── --}}
    <div class="footer">
        <div class="ttd">
            <div>Mengetahui,</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">Bendahara</div>
        </div>
        <div class="ttd">
            <div>Penerima,</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">( ________________ )</div>
        </div>
        <div class="cetak-info">
            Dicetak: {{ $cetakTanggal }}<br>
            Oleh: {{ auth()->user()?->name ?? 'Sistem' }}<br>
            #{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}
        </div>
    </div>

</body>
</html>
