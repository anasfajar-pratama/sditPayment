<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Pembayaran - SDIT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .kuitansi { max-width: 800px; margin: auto; border: 2px solid #000; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .footer { margin-top: 40px; text-align: right; }
        .title { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="kuitansi">
        <div class="header">
            <h2>SDIT - SEKOLAH DASAR ISLAM TERPADU</h2>
            <p>Kuitansi Pembayaran</p>
        </div>

        <table>
            <tr>
                <td width="30%">No. Kuitansi</td>
                <td><strong>#{{ $pembayaran->id ?? '00000' }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>{{ $pembayaran->tanggal_bayar->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>{{ $pembayaran->siswa->nis }}</td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>{{ $pembayaran->siswa->nama }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>{{ $pembayaran->siswa->kelas }}</td>
            </tr>
            <tr>
                <td>Jenis Pembayaran</td>
                <td>{{ $pembayaran->jenisPembayaran->nama }}
                    @if($pembayaran->bulan) ({{ $pembayaran->bulan }} {{ $pembayaran->tahun }}) @endif
                </td>
            </tr>
            <tr>
                <td>Nominal</td>
                <td><strong>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Status Pembayaran</td>
                <td>
                    <strong style="color: {{ $pembayaran->status === 'lunas' ? 'green' : 'orange' }}">
                        {{ ucfirst($pembayaran->status) }}
                    </strong>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima Kasih</p>
            <p><strong>Admin SDIT</strong></p>
            <small>{{ now()->format('d M Y H:i') }}</small>
        </div>
    </div>
</body>
</html>