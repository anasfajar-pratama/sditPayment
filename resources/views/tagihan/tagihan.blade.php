<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tagihan - SDIT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .tagihan { max-width: 800px; margin: auto; border: 2px solid #000; padding: 25px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 10px; text-align: left; }
        .footer { margin-top: 50px; text-align: right; }
        .status { color: #dc2626; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class="tagihan">
        <div class="header">
            <h2>SDIT - SEKOLAH DASAR ISLAM TERPADU</h2>
            <h3>TAGIHAN PEMBAYARAN</h3>
        </div>

        <table>
            <tr>
                <td width="35%">No. Tagihan</td>
                <td><strong>#{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Tagihan</td>
                <td>{{ now()->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>{{ $tagihan->siswa->nis }}</td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>{{ $tagihan->siswa->nama }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>{{ $tagihan->siswa->kelas }}</td>
            </tr>
            <tr>
                <td>Jenis Pembayaran</td>
                <td>{{ $tagihan->jenisPembayaran->nama }}
                    @if($tagihan->bulan) ({{ $tagihan->bulan }} {{ $tagihan->tahun }}) @endif
                </td>
            </tr>
            <tr>
                <td>Nominal Tagihan</td>
                <td><strong>Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Status</td>
                <td class="status">BELUM TERBAYAR</td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima Kasih</p>
            <p><strong>Admin SDIT</strong></p>
        </div>
    </div>
</body>
</html>