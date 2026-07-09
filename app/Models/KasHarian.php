<?php
// ════════════════════════════════════════════════════════════
// File: app/Models/KasHarian.php
// Perubahan: tambah postingDariDonasi() dan hapusPostingDonasi()
// ════════════════════════════════════════════════════════════

namespace App\Models;

use App\Models\LogDanaMasuk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarian extends Model
{
    protected $table = 'kas_harians';
    protected $fillable = [
        'tanggal', 'uraian', 'sub_kategori', 'akun_id',
        'debit', 'kredit', 'bukti',
        'source', 'source_id',
        'no_ref', 'rekening_tujuan', 'nama_rekening_pengirim',
        'verified_at', 'verified_by',
        'bulan', 'tahun',
        'created_by',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'debit'        => 'decimal:2',
        'kredit'       => 'decimal:2',
        'verified_at'  => 'datetime',
    ];

    protected $appends = [
        'source_bukti_url',
    ];

    public function getSourceBuktiUrlAttribute(): ?string
    {
        if ($this->source === 'pembayaran') {
            $pembayaran = \App\Models\Pembayaran::find($this->source_id);
            return $pembayaran?->bukti_url;
        }
        if ($this->source === 'donasi') {
            $donasi = \App\Models\Donasi::find($this->source_id);
            if ($donasi?->bukti_transfer) {
                return \Illuminate\Support\Facades\Storage::url($donasi->bukti_transfer);
            }
        }
        return null;
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ─── Posting dari pembayaran siswa ────────────────────────────────────────

    public static function postingDariPembayaran(Pembayaran $pembayaran): void
    {
        if (static::where('source', 'pembayaran')->where('source_id', $pembayaran->id)->exists()) {
            return;
        }

        // if (static::where('source', 'pembayaran')->where('source_id', $pembayaran->id)->exists()) {
        //     \Log::warning('KasHarian: SKIP duplikat', ['source_id' => $pembayaran->id]);
        //     return;  // ← ini kemungkinan besar penyebabnya
        // }
        $namaJenis = strtolower($pembayaran->jenisPembayaran?->nama ?? '');
        $kodeAkun  = match(true) {
            str_contains($namaJenis, 'spp')          => '4101',
            str_contains($namaJenis, 'daftar ulang')  => '4102',
            str_contains($namaJenis, 'daftar masuk')  => '4103',
            str_contains($namaJenis, 'donasi')         => '4104',
            default                                    => '4101',
        };

        $akun = Akun::where('kode_akun', $kodeAkun)->first();

        $bulanLabels = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Ags',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        $siswa      = $pembayaran->siswa;
        $bulanLabel = $bulanLabels[$pembayaran->bulan] ?? '';
        $kelas = $pembayaran->jenisPembayaran->nama === 'Daftar Masuk'
            ? ''
            : ' Kls ' . ($siswa->kelasSaatIni?->kelas ?? '-');

        $uraian = "{$siswa->nama}{$kelas}"
            . " — {$pembayaran->jenisPembayaran->nama}"
            . ($bulanLabel ? " {$bulanLabel}" : '')
            . " {$pembayaran->tahun}";

        $record = static::create([
            'tanggal'               => $pembayaran->tanggal_bayar,
            'uraian'                => $uraian,
            'akun_id'               => $akun?->id,
            'debit'                 => $pembayaran->nominal,
            'kredit'                => null,
            'source'                => 'pembayaran',
            'source_id'             => $pembayaran->id,
            'no_ref'                => $pembayaran->no_ref,
            'rekening_tujuan'       => $pembayaran->rekening_tujuan,
            'nama_rekening_pengirim'=> $pembayaran->nama_rekening_pengirim,
            'bulan'                 => $pembayaran->bulan,
            'tahun'                 => $pembayaran->tahun,
            'created_by'            => $pembayaran->created_by,
        ]);

        LogDanaMasuk::create([
            'kas_harian_id' => $record->id,
            'action'         => 'create',
            'uraian'         => $record->uraian,
            'data_lama'      => null,
            'data_baru'      => $record->only(['no_ref', 'rekening_tujuan', 'nama_rekening_pengirim', 'debit']),
            'created_by'     => $pembayaran->created_by,
        ]);
    }

    public static function hapusPostingPembayaran(int $pembayaranId): void
    {
        static::where('source', 'pembayaran')
              ->where('source_id', $pembayaranId)
              ->delete();
    }

    // ─── Posting dari donasi donatur ─────────────────────────────────────────
    // Akun 7 = Pendapatan Donasi (4104)

    public static function postingDariDonasi(Donasi $donasi): void
    {
        // Cegah duplikasi
        if (static::where('source', 'donasi')->where('source_id', $donasi->id)->exists()) {
            return;
        }

        $donatur = $donasi->donatur;
        $uraian  = "Donasi — {$donatur->nama}"
                 . ($donasi->note ? " ({$donasi->note})" : '');

        $buktiTransfer  = $donasi->bukti_transfer ?? null;
        $rekeningTujuan = $donasi->rekening_tujuan ?? null;
        $namaPengirim   = $donasi->nama_rekening_pengirim ?? $donasi->donatur?->nama ?? null;

        $record = static::create([
            'tanggal'               => $donasi->tanggal,
            'uraian'                => $uraian,
            'akun_id'               => 7,
            'debit'                 => $donasi->nominal,
            'kredit'                => null,
            'source'                => 'donasi',
            'source_id'             => $donasi->id,
            'no_ref'                => $donasi->no_ref ?? null,
            'rekening_tujuan'       => $rekeningTujuan,
            'nama_rekening_pengirim'=> $namaPengirim,
            'bukti'                 => $buktiTransfer,
            'bulan'                 => $donasi->bulan,
            'tahun'                 => $donasi->tahun,
            'created_by'            => $donasi->created_by,
        ]);

        LogDanaMasuk::create([
            'kas_harian_id' => $record->id,
            'action'         => 'create',
            'uraian'         => $record->uraian,
            'data_lama'      => null,
            'data_baru'      => $record->only(['no_ref', 'rekening_tujuan', 'nama_rekening_pengirim', 'debit']),
            'created_by'     => $donasi->created_by,
        ]);
    }

    public static function hapusPostingDonasi(int $donasiId): void
    {
        static::where('source', 'donasi')
              ->where('source_id', $donasiId)
              ->delete();
    }

    // ─── Posting dari pembelian token listrik ─────────────────────────────────
    // akun_id = 9  |  sub_kategori = "TOKEN & PULSA"  |  sisi KREDIT (pengeluaran)

    public static function postingDariToken(TokenPembelian $pembelian): void
    {
        // Cegah duplikasi
        if (static::where('source', 'token')->where('source_id', $pembelian->id)->exists()) {
            return;
        }

        $ruangan = $pembelian->tokenListrik?->nama_ruangan ?? 'Token Listrik';
        $nomorToken = $pembelian->nomor_token ?? '';
        $uraian  = 'TOKEN ' . strtoupper($ruangan)
                 . ($nomorToken ? " ({$nomorToken})" : '');

        static::create([
            'tanggal'      => $pembelian->tanggal,
            'uraian'       => $uraian,
            'sub_kategori' => 'TOKEN & PULSA',
            'akun_id'      => 9,
            'debit'        => null,
            'kredit'       => $pembelian->nominal,
            'source'       => 'token',
            'source_id'    => $pembelian->id,
            'bulan'        => $pembelian->bulan,
            'tahun'        => $pembelian->tahun,
            'created_by'   => $pembelian->created_by,
        ]);
    }

    public static function hapusPostingToken(int $pembelianId): void
    {
        static::where('source', 'token')
              ->where('source_id', $pembelianId)
              ->delete();
    }
}
