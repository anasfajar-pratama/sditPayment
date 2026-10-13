<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
            padding: 24px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #1f2937;
        }
        .header-left h1 { font-size: 18px; font-weight: 800; color: #1f2937; }
        .header-left .subtitle { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .header-right { text-align: right; font-size: 10px; color: #9ca3af; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th {
            background: #1f2937;
            color: #fff;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            text-align: left;
        }
        td { padding: 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .no-wrap { white-space: nowrap; }
        .item-line { font-size: 9.5px; color: #374151; }
        tfoot td { background: #fef3c7 !important; font-weight: 800; border-top: 2px solid #f59e0b; }
        .empty { text-align: center; padding: 24px; color: #9ca3af; }
        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];
    @endphp

    <div class="header">
        <div class="header-left">
            <h1>{{ $judul }}</h1>
            <div class="subtitle">Kelas {{ $kelas }} &mdash; {{ $jenisSekolah }}</div>
        </div>
        <div class="header-right">
            <div>{{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:2.5rem;" class="text-center">No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Bulan Menunggak</th>
                <th class="text-right">Total Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="no-wrap">{{ $r['nis'] ?? '-' }}</td>
                    <td>{{ $r['nama'] ?? '-' }}</td>
                    <td>
                        @foreach ($r['items'] as $it)
                            <div class="item-line">
                                {{ $it['bulan'] ? (($bulanLabels[$it['bulan']] ?? $it['bulan']) . ' ' . $it['tahun']) : 'Daftar Ulang' }}
                                &mdash; Rp {{ number_format($it['nominal'], 0, ',', '.') }}
                            </div>
                        @endforeach
                    </td>
                    <td class="text-right">Rp {{ number_format($r['total'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada tunggakan s/d {{ $periodeLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
        @if (count($rows) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">TOTAL ({{ count($rows) }} siswa)</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak dari Sistem {{ config('app.name') }} &mdash; {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
