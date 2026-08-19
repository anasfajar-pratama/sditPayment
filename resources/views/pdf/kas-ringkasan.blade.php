<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Kas Harian</title>
    <style>
        @page { margin: 12mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1f2937; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 14pt; font-weight: 800; }
        .header p { margin: 3px 0; font-size: 8pt; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1f2937; color: #fff; padding: 5px 6px; font-size: 7.5pt; font-weight: 700; text-align: left; letter-spacing: 0.03em; }
        th.right { text-align: right; }
        th.center { text-align: center; }
        td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; }
        td.right { text-align: right; font-variant-numeric: tabular-nums; }
        td.center { text-align: center; }
        .total-row { background: #f3f4f6; font-weight: 700; }
        .total-row td { border-top: 2px solid #1f2937; }
        .grand-total { background: #1f2937; color: #fff; font-weight: 800; font-size: 9pt; }
        .grand-total td { border: none; }
        .section-title { font-size: 9pt; font-weight: 700; margin: 8px 0 4px; padding: 3px 6px; background: #ecfdf5; border-left: 3px solid #059669; }
        .footer { text-align: center; font-size: 7pt; color: #9ca3af; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .page-break { page-break-before: always; }
        .badge-in { color: #047857; font-weight: 700; }
        .badge-out { color: #b91c1c; font-weight: 700; }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN REKAP KAS HARIAN</h1>
    <p>{{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</p>
    <p style="font-size:7pt;color:#9ca3af;">Dicetak: {{ now()->format('d M Y H:i') }}</p>
</div>

{{-- ═══════════ TRANSFER ═══════════ --}}
<div class="section-title">1. TRANSFER</div>
<table>
    <thead>
        <tr>
            <th style="width:2.5rem;" class="center">NO</th>
            <th style="width:6rem;">TANGGAL</th>
            <th>URAIAN</th>
            <th>AKUN</th>
            <th style="width:7rem;">REKENING</th>
            <th style="width:8rem;" class="right">JUMLAH (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transfer as $row)
            <tr>
                <td class="center" style="color:#9ca3af;">{{ $row['no'] }}</td>
                <td style="color:#6b7280;">{{ $row['tanggal'] }}</td>
                <td>{{ $row['uraian'] }}</td>
                <td>{{ $row['akun'] }}</td>
                <td>{{ $row['rekening'] }}{{ $row['pengirim'] ? ' (' . $row['pengirim'] . ')' : '' }}</td>
                <td class="right" style="color:#047857;">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;">Tidak ada transaksi transfer</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="5" style="text-align:right;">Total Transfer</td>
            <td class="right" style="color:#047857;">{{ number_format($totalTransfer, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ═══════════ CASH ═══════════ --}}
<div class="section-title">2. CASH</div>
<table>
    <thead>
        <tr>
            <th style="width:2.5rem;" class="center">NO</th>
            <th style="width:6rem;">TANGGAL</th>
            <th>URAIAN</th>
            <th>AKUN</th>
            <th style="width:8rem;" class="right">JUMLAH (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($cash as $row)
            <tr>
                <td class="center" style="color:#9ca3af;">{{ $row['no'] }}</td>
                <td style="color:#6b7280;">{{ $row['tanggal'] }}</td>
                <td>{{ $row['uraian'] }}</td>
                <td>{{ $row['akun'] }}</td>
                <td class="right" style="color:#047857;">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Tidak ada transaksi cash</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" style="text-align:right;">Total Cash</td>
            <td class="right" style="color:#047857;">{{ number_format($totalCash, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ═══════════ PENGELUARAN ═══════════ --}}
<div class="section-title">3. PENGELUARAN</div>
<table>
    <thead>
        <tr>
            <th style="width:2.5rem;" class="center">NO</th>
            <th style="width:6rem;">TANGGAL</th>
            <th>URAIAN</th>
            <th>AKUN</th>
            <th style="width:8rem;" class="right">JUMLAH (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($kredit as $row)
            <tr>
                <td class="center" style="color:#9ca3af;">{{ $row['no'] }}</td>
                <td style="color:#6b7280;">{{ $row['tanggal'] }}</td>
                <td>{{ $row['uraian'] }}</td>
                <td>{{ $row['akun'] }}{{ $row['sub_kategori'] ? ' — ' . $row['sub_kategori'] : '' }}</td>
                <td class="right" style="color:#dc2626;">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Tidak ada pengeluaran</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" style="text-align:right;">Total Pengeluaran</td>
            <td class="right" style="color:#dc2626;">{{ number_format($totalKredit, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ═══════════ GABUNGAN (CASH + PENGELUARAN) ═══════════ --}}
<div class="page-break"></div>

<div class="section-title">4. GABUNGAN (CASH + PENGELUARAN)</div>
<table>
    <thead>
        <tr>
            <th style="width:2.5rem;" class="center">NO</th>
            <th style="width:6rem;">TANGGAL</th>
            <th>URAIAN</th>
            <th>AKUN</th>
            <th style="width:5.5rem;" class="center">TIPE</th>
            <th style="width:8rem;" class="right">JUMLAH (Rp)</th>
            <th style="width:8rem;" class="right">SALDO (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($gabungan as $row)
            <tr>
                <td class="center" style="color:#9ca3af;">{{ $row['no'] }}</td>
                <td style="color:#6b7280;">{{ $row['tanggal'] }}</td>
                <td>{{ $row['uraian'] }}</td>
                <td>{{ $row['akun'] }}{{ $row['sub_kategori'] ? ' — ' . $row['sub_kategori'] : '' }}</td>
                <td class="center">
                    <span class="{{ $row['tipe'] === 'Masuk' ? 'badge-in' : 'badge-out' }}">{{ $row['tipe'] }}</span>
                </td>
                <td class="right" style="color:{{ $row['tipe'] === 'Keluar' ? '#dc2626' : '#047857' }};">
                    {{ number_format($row['jumlah'], 0, ',', '.') }}
                </td>
                <td class="right">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#9ca3af;">Tidak ada transaksi cash atau pengeluaran</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    <table>
        <tr class="grand-total">
            <td style="text-align:right;padding:8px 10px;font-size:10pt;">
                KAS HARI INI (CASH − PENGELUARAN)
            </td>
            <td style="text-align:right;padding:8px 10px;font-size:11pt;width:8rem;">
                Rp {{ number_format($kasHariIni, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Laporan Rekap Kas Harian — {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}
    | Halaman dicetak otomatis
</div>

</body>
</html>