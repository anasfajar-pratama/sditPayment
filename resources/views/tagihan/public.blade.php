{{-- ════════════════════════════════════════════════════════════ --}}
{{-- File: resources/views/tagihan/public.blade.php              --}}
{{-- Halaman publik tagihan — dapat diakses tanpa login           --}}
{{-- ════════════════════════════════════════════════════════════ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Pembayaran — {{ $tagihan->siswa->nama ?? '-' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,.1); overflow: hidden; }
        .header { background: #1e3a5f; color: #fff; padding: 24px 28px; }
        .header h1 { font-size: 20px; font-weight: 700; }
        .header p { font-size: 13px; margin-top: 4px; opacity: .8; }
        .body { padding: 28px; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .row:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 600; text-align: right; }
        .status-lunas { color: #16a34a; }
        .status-belum { color: #dc2626; }
        .nominal { font-size: 22px; font-weight: 700; color: #1e3a5f; text-align: center; margin: 20px 0 8px; }
        .nominal-label { font-size: 12px; color: #999; text-align: center; margin-bottom: 20px; }
        .footer { background: #f9f9f9; padding: 16px 28px; text-align: center; font-size: 12px; color: #999; }
        .print-btn { display: block; margin: 0 auto 20px; padding: 10px 24px; background: #1e3a5f; color: #fff; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        @media print { .print-btn { display: none; } body { background: #fff; } .container { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tagihan Pembayaran</h1>
            <p>Informasi tagihan resmi dari sekolah</p>
        </div>

        <div class="body">
            <div class="row">
                <span class="label">Nama Siswa</span>
                <span class="value">{{ $tagihan->siswa->nama ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">NIS</span>
                <span class="value">{{ $tagihan->siswa->nis ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">Jenis Pembayaran</span>
                <span class="value">{{ $tagihan->jenisPembayaran->nama ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">Periode</span>
                <span class="value">{{ $namaBulan }} {{ $tagihan->tahun }}</span>
            </div>
            <div class="row">
                <span class="label">Status</span>
                <span class="value {{ $tagihan->status === 'lunas' ? 'status-lunas' : 'status-belum' }}">
                    {{ $tagihan->status === 'lunas' ? '✓ Lunas' : '⚠ Belum Bayar' }}
                </span>
            </div>

            <div class="nominal">
                Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
            </div>
            <div class="nominal-label">Total Tagihan</div>

            <button class="print-btn" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
        </div>

        <div class="footer">
            Halaman ini dapat dibagikan ke wali murid. Diakses pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
        </div>
    </div>
</body>
</html>
