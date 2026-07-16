<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tagihan</title>
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
        .header-left h1 {
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
        }
        .header-left .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
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
        td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tr:nth-child(even) td {
            background: #f9fafb;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-lunas {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 9px;
        }
        .badge-belum {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 9px;
        }
        .no-wrap { white-space: nowrap; }
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
            <h1>Laporan Tagihan</h1>
            <div class="subtitle">{{ $bagianFilter ?: 'Semua Data' }}</div>
        </div>
        <div class="header-right">
            <div>{{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jenis Pembayaran</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th class="text-right">Nominal</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tagihans as $t)
                @php
                    $siswa = $t->siswa;
                    if ($siswa && $siswa->is_calon) {
                        $namaSiswa = ($siswa->nama ?? '-') . ' / ' . (optional($siswa->kelasSaatIni)->jenis_sekolah ?? $siswa->calon_jenis ?? '-');
                    } elseif ($siswa) {
                        $namaSiswa = ($siswa->nama ?? '-') . ' / ' . (optional($siswa->kelasSaatIni)->kelas ?? '-') . ' / ' . (optional($siswa->kelasSaatIni)->jenis_sekolah ?? '-');
                    } else {
                        $namaSiswa = '-';
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="no-wrap">{{ $t->siswa->nis ?? '-' }}</td>
                    <td>{{ $namaSiswa }}</td>
                    <td>{{ $t->jenisPembayaran->nama ?? '-' }}</td>
                    <td>{{ $bulanLabels[$t->bulan] ?? $t->bulan }}</td>
                    <td>{{ $t->tahun }}</td>
                    <td class="text-right">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="{{ $t->status === 'lunas' ? 'badge-lunas' : 'badge-belum' }}">
                            {{ $t->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:24px;color:#9ca3af;">Tidak ada data tagihan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $totalNominal = $tagihans->sum('nominal_tagihan');
        $countLunas = $tagihans->where('status', 'lunas')->count();
        $countBelum = $tagihans->where('status', 'belum_bayar')->count();
    @endphp
    <div style="margin-top:16px;display:flex;gap:24px;font-size:11px;">
        <div><strong>Total Tagihan:</strong> Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
        <div><strong>Lunas:</strong> {{ $countLunas }}</div>
        <div><strong>Belum Bayar:</strong> {{ $countBelum }}</div>
        <div><strong>Jumlah:</strong> {{ $tagihans->count() }} tagihan</div>
    </div>

    <div class="footer">
        Dicetak dari Sistem {{ config('app.name') }} &mdash; {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
