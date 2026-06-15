<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas Harian — {{ $judulPeriode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            padding: 32px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #1f2937;
        }
        .header-left h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
        }
        .header-left .subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
            font-size: 11px;
            color: #9ca3af;
        }
        .header-right .school-name {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
        }

        /* ── Summary cards ── */
        .summary {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            border-radius: 8px;
            padding: 12px 14px;
            border: 1px solid #e5e7eb;
        }
        .card-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        .card-value {
            font-size: 14px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }
        .card.debit  { background: #f0fdf4; border-color: #bbf7d0; }
        .card.kredit { background: #fef2f2; border-color: #fecaca; }
        .card.akhir  { background: #1f2937; border-color: #1f2937; }
        .card.debit  .card-value { color: #15803d; }
        .card.kredit .card-value { color: #dc2626; }
        .card.akhir  .card-label { color: #9ca3af; }
        .card.akhir  .card-value { color: #fde68a; }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        thead tr {
            background: #1f2937;
            color: #fff;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr.row-pembayaran  { background: #fefce8 !important; }

        tbody td {
            padding: 7px 10px;
            vertical-align: middle;
        }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; color: #9ca3af; font-size: 10.5px; }
        tbody td.tanggal { color: #6b7280; font-size: 10.5px; white-space: nowrap; }
        tbody td.akun    { color: #9ca3af; font-size: 10.5px; }
        tbody td.debit   { color: #15803d; font-weight: 600; font-variant-numeric: tabular-nums; }
        tbody td.kredit  { color: #dc2626; font-weight: 600; font-variant-numeric: tabular-nums; }
        tbody td.saldo   { color: #111827; font-weight: 700; font-variant-numeric: tabular-nums; }
        tbody td.muted   { color: #d1d5db; }

        .badge-siswa {
            display: inline-block;
            font-size: 9px;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 3px;
            padding: 1px 5px;
            font-weight: 600;
            margin-right: 4px;
            vertical-align: middle;
        }

        /* ── Footer total ── */
        tfoot tr {
            background: #1f2937;
            color: #fff;
        }
        tfoot td {
            padding: 9px 10px;
            font-size: 11px;
            font-weight: 700;
        }
        tfoot td.right { text-align: right; }
        tfoot td.debit-total  { color: #4ade80; font-variant-numeric: tabular-nums; }
        tfoot td.kredit-total { color: #f87171; font-variant-numeric: tabular-nums; }
        tfoot td.saldo-total  { color: #fde68a; font-variant-numeric: tabular-nums; font-size: 12px; }

        /* ── Legend ── */
        .legend {
            margin-top: 16px;
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: #9ca3af;
        }
        .legend-box {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            border: 1px solid;
            display: inline-block;
            flex-shrink: 0;
        }

        /* ── Print footer ── */
        .print-footer {
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }

        /* ── Print button (tersembunyi saat print) ── */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1f2937;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 999;
        }
        .print-btn:hover { background: #374151; }

        /* ── @media print ── */
        @media print {
            body { padding: 16px; }
            .print-btn { display: none !important; }
            @page {
                margin: 15mm 12mm;
                size: A4 landscape;
            }
        }
    </style>
</head>
<body>

    {{-- Tombol cetak --}}
    <button class="print-btn" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>Laporan Kas Harian</h1>
            <div class="subtitle">Periode: {{ $judulPeriode }}</div>
        </div>
        <div class="header-right">
            <div class="school-name">SDIT — Keuangan</div>
            <div>Dicetak: {{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="summary">
        <div class="card debit">
            <div class="card-label">Total Debit (Masuk)</div>
            <div class="card-value">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
        </div>
        <div class="card kredit">
            <div class="card-label">Total Kredit (Keluar)</div>
            <div class="card-value">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
        </div>
        <div class="card akhir">
            <div class="card-label">Saldo Akhir Periode</div>
            <div class="card-value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Tabel --}}
    @if (count($entries) === 0)
        <p style="text-align:center;color:#9ca3af;padding:40px 0;">
            Tidak ada transaksi untuk periode ini.
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th class="center" style="width:32px;">No</th>
                    <th style="width:80px;">Tanggal</th>
                    <th>Uraian</th>
                    <th style="width:130px;">Akun</th>
                    <th class="right" style="width:100px;">Debit</th>
                    <th class="right" style="width:100px;">Kredit</th>
                    <th class="right" style="width:115px;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $i => $e)
                    <tr class="{{ $e['source'] === 'pembayaran' ? 'row-pembayaran' : '' }}">
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="tanggal">{{ $e['tanggal'] }}</td>
                        <td>
                            @if ($e['source'] === 'pembayaran')
                                <span class="badge-siswa">Siswa</span>
                            @endif
                            {{ $e['uraian'] }}
                        </td>
                        <td class="akun">{{ $e['akun'] }}</td>
                        <td class="right {{ $e['debit'] ? 'debit' : 'muted' }}">
                            {{ $e['debit'] ? number_format($e['debit'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="right {{ $e['kredit'] ? 'kredit' : 'muted' }}">
                            {{ $e['kredit'] ? number_format($e['kredit'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="right saldo">{{ number_format($e['saldo'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="font-size:10px;color:#6b7280;">
                        {{ count($entries) }} transaksi
                    </td>
                    <td class="right debit-total">
                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                    </td>
                    <td class="right kredit-total">
                        Rp {{ number_format($totalKredit, 0, ',', '.') }}
                    </td>
                    <td class="right saldo-total">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Legend --}}
        <div class="legend">
            <div class="legend-item">
                <span class="legend-box" style="background:#fefce8;border-color:#fde68a;"></span>
                Otomatis dari pembayaran siswa
            </div>
            <div class="legend-item">
                <span class="legend-box" style="background:#fff;border-color:#e5e7eb;"></span>
                Input manual
            </div>
        </div>
    @endif

    {{-- Print footer --}}
    <div class="print-footer">
        <span>SDIT — Laporan Kas Harian | {{ $judulPeriode }}</span>
        <span>Dicetak {{ now()->format('d M Y H:i') }}</span>
    </div>

</body>
</html>
