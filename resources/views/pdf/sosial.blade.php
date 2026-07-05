<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sosial</title>
    <style>
        @page { margin: 12mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1f2937; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 14pt; font-weight: 800; }
        .header p { margin: 3px 0; font-size: 8pt; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1f2937; color: #fff; padding: 5px 6px; font-size: 7.5pt; font-weight: 700; text-align: left; letter-spacing: 0.03em; }
        th.right { text-align: right; }
        td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; }
        td.right { text-align: right; font-variant-numeric: tabular-nums; }
        .total-row { background: #f3f4f6; font-weight: 700; }
        .total-row td { border-top: 2px solid #1f2937; }
        .grand-total { background: #1f2937; color: #fff; font-weight: 800; font-size: 9pt; }
        .grand-total td { border: none; }
        .section-title { font-size: 9pt; font-weight: 700; margin: 8px 0 4px; padding: 3px 6px; background: #fdf2f8; border-left: 3px solid #ec4899; }
        .footer { text-align: center; font-size: 7pt; color: #9ca3af; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PENGELUARAN SOSIAL</h1>
    <p>{{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</p>
    <p style="font-size:7pt;color:#9ca3af;">Dicetak: {{ now()->format('d M Y H:i') }}</p>
</div>

@php $pageNo = 0; @endphp

@foreach ($kategori as $kat)
    @if (!empty($grouped[$kat]))
        @php $pageNo++; @endphp
        @if ($pageNo > 1) <div class="page-break"></div> @endif

        <div class="section-title">{{ $kat }}</div>

        <table>
            <thead>
                <tr>
                    <th style="width:2.5rem;text-align:center;">NO</th>
                    <th style="width:6rem;">TANGGAL</th>
                    <th>URAIAN</th>
                    <th style="width:8rem;" class="right">JUMLAH (Rp)</th>
                    <th style="width:8rem;" class="right">TOTAL (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grouped[$kat] as $row)
                    <tr>
                        <td style="text-align:center;color:#9ca3af;">{{ $row['no'] }}</td>
                        <td style="color:#6b7280;">{{ $row['tanggal'] }}</td>
                        <td>{{ $row['uraian'] }}</td>
                        <td class="right" style="color:#dc2626;">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total {{ $kat }}</td>
                    <td class="right" style="color:#dc2626;">{{ number_format($ringkasan[$kat], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endforeach

<div style="margin-top:12px;">
    <table>
        <tr class="grand-total">
            <td colspan="3" style="text-align:right;padding:8px 10px;font-size:10pt;">
                GRAND TOTAL PENGELUARAN SOSIAL
            </td>
            <td style="text-align:right;padding:8px 10px;font-size:11pt;">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </td>
            <td></td>
        </tr>
    </table>
</div>

<div class="footer">
    Laporan Pengeluaran Sosial — {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}
    | Halaman dicetak otomatis
</div>

</body>
</html>
