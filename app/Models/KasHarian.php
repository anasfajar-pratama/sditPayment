<?php
// ════════════════════════════════════════════════════════════
// File: app/Models/KasHarian.php
// ════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarian extends Model
{
    protected $table = 'kas_harians';
    protected $fillable = [
        'tanggal', 'uraian', 'akun_id',
        'debit', 'kredit',
        'source', 'source_id',
        'bulan', 'tahun',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debit'   => 'decimal:2',
        'kredit'  => 'decimal:2',
    ];

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helper: posting otomatis dari pembayaran siswa ──────────────────────
    public static function postingDariPembayaran(Pembayaran $pembayaran): void
    {
        // Cek apakah sudah ada posting untuk pembayaran ini
        if (static::where('source', 'pembayaran')->where('source_id', $pembayaran->id)->exists()) {
            return;
        }

        // Tentukan akun berdasarkan nama jenis pembayaran
        $namaJenis = strtolower($pembayaran->jenisPembayaran?->nama ?? '');
        $kodeAkun  = match(true) {
            str_contains($namaJenis, 'spp')           => '4101',
            str_contains($namaJenis, 'daftar ulang')   => '4102',
            str_contains($namaJenis, 'daftar masuk')   => '4103',
            str_contains($namaJenis, 'donasi')          => '4104',
            default                                     => '4101',
        };

        $akun = Akun::where('kode_akun', $kodeAkun)->first();

        $bulanLabels = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Ags',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        $siswa   = $pembayaran->siswa;
        $bulanLabel = $bulanLabels[$pembayaran->bulan] ?? '';
        $uraian  = "{$siswa->nama} Kls {$siswa->kelas}"
                 . " — {$pembayaran->jenisPembayaran->nama}"
                 . ($bulanLabel ? " {$bulanLabel}" : '')
                 . " {$pembayaran->tahun}";

        static::create([
            'tanggal'    => $pembayaran->tanggal_bayar,
            'uraian'     => $uraian,
            'akun_id'    => $akun?->id,
            'debit'      => $pembayaran->nominal,
            'kredit'     => null,
            'source'     => 'pembayaran',
            'source_id'  => $pembayaran->id,
            'bulan'      => $pembayaran->bulan,
            'tahun'      => $pembayaran->tahun,
            'created_by' => $pembayaran->created_by,
        ]);
    }

    // ─── Hapus posting jika pembayaran dihapus ───────────────────────────────
    public static function hapusPostingPembayaran(int $pembayaranId): void
    {
        static::where('source', 'pembayaran')
              ->where('source_id', $pembayaranId)
              ->delete();
    }
}