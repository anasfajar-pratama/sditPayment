<?php

namespace App\Filament\Pages;
use App\Models\KasHarian;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class PembayaranSiswaPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'Pembayaran Siswa';
    protected static ?string $title           = 'Pembayaran Siswa';
    protected static ?int    $navigationSort  = 1;

    protected static string $view = 'filament.pages.pembayaran-siswa-page';

    // State pencarian
    public string $searchQuery   = '';
    public bool   $showResults   = false;

    // State siswa terpilih
    public ?int   $siswa_id      = null;
    public ?Siswa $selectedSiswa = null;

    // Nominal bayar per tagihan
    public array $nominals     = [];
    public array $percentages  = [];

    // ─── Search autocomplete ───────────────────────────────────────────────────

    #[Computed]
    public function searchResults(): \Illuminate\Support\Collection
    {
        if (strlen(trim($this->searchQuery)) < 2) {
            return collect();
        }

        return Siswa::where('nis', 'like', "%{$this->searchQuery}%")
            ->orWhere('nama', 'like', "%{$this->searchQuery}%")
            ->orderBy('nama')
            ->limit(5)
            ->get();
    }

    public function updatedSearchQuery(): void
    {
        $this->showResults = strlen(trim($this->searchQuery)) >= 2;
        unset($this->searchResults);
    }

    public function selectSiswa(int $id): void
    {
        $this->siswa_id    = $id;
        $this->selectedSiswa = Siswa::find($id);
        $this->searchQuery = "{$this->selectedSiswa->nis} - {$this->selectedSiswa->nama}";
        $this->showResults = false;

        // Reset nominal
        $this->nominals   = [];
        $this->percentages = [];

        unset($this->tagihans, $this->history);

        foreach ($this->tagihans as $tagihan) {
            $isSpp = $this->isSpp($tagihan);
            $this->nominals[$tagihan->id]    = $isSpp ? $tagihan->nominal_tagihan : null;
            $this->percentages[$tagihan->id] = $isSpp ? 100 : 0;
        }
    }

    public function clearSiswa(): void
    {
        $this->siswa_id      = null;
        $this->selectedSiswa = null;
        $this->searchQuery   = '';
        $this->showResults   = false;
        $this->nominals      = [];
        $this->percentages   = [];
        unset($this->tagihans, $this->history);
    }

    // ─── Computed: tagihan & history ──────────────────────────────────────────

    #[Computed]
    public function tagihans(): \Illuminate\Support\Collection
    {
        if (! $this->siswa_id) {
            return collect();
        }

        return Tagihan::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa_id)
            ->where('status', 'belum_bayar')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
    }

    #[Computed]
    public function history(): \Illuminate\Support\Collection
    {
        if (! $this->siswa_id) {
            return collect();
        }

        return Pembayaran::with(['jenisPembayaran', 'tagihan'])
            ->where('siswa_id', $this->siswa_id)
            ->orderByDesc('tanggal_bayar')
            ->limit(15)
            ->get()
            ->map(function ($p) {
                $p->share_token = \DB::table('pdf_links')
                    ->where('pdf_id', $p->id)
                    ->where('jenis', 'kuitansi')
                    ->where('expired_at', '>', now())
                    ->value('token');

                // Semua pembayaran untuk tagihan yang sama, urut tanggal
                $semuaPembayaran = Pembayaran::where('tagihan_id', $p->tagihan_id)
                    ->orderBy('tanggal_bayar')
                    ->orderBy('id')
                    ->get(['id', 'nominal', 'tanggal_bayar', 'status']);

                $totalTerbayar = $semuaPembayaran->sum('nominal');

                $sisaTagihan = ($p->tagihan && $p->tagihan->status !== 'lunas')
                    ? $p->tagihan->nominal_tagihan
                    : 0;

                $p->total_tagihan  = $totalTerbayar + $sisaTagihan;
                $p->total_terbayar = $totalTerbayar;
                $p->sisa_tagihan   = $sisaTagihan;

                // Nomor urut cicilan untuk pembayaran ini
                $p->cicilan_list = $semuaPembayaran->values(); // index 0,1,2,...
                $p->cicilan_ke   = $semuaPembayaran->search(fn($x) => $x->id === $p->id) + 1;

                return $p;
            });
    }

    // ─── Reactive: hitung persentase saat nominal diketik ─────────────────────

    public function updatedNominals(mixed $value, string $key): void
    {
        $tagihanId = (int) $key;
        $tagihan   = Tagihan::find($tagihanId);

        if ($tagihan && $tagihan->nominal_tagihan > 0) {
            $nominal = (float) ($value ?? 0);
            $this->percentages[$tagihanId] = round(($nominal / $tagihan->nominal_tagihan) * 100, 1);
        }
    }

    // ─── Bayar ────────────────────────────────────────────────────────────────

    public function isSpp(Tagihan $tagihan): bool
    {
        return strtolower($tagihan->jenisPembayaran?->nama ?? '') === 'spp';
    }

    public function bayar(int $tagihanId): void
    {
        $tagihan = Tagihan::with('jenisPembayaran')->findOrFail($tagihanId);
        $nominal = (float) ($this->nominals[$tagihanId] ?? 0);

        // Validasi SPP harus penuh
        if ($this->isSpp($tagihan) && $nominal < $tagihan->nominal_tagihan) {
            Notification::make()
                ->title('SPP harus dibayar penuh')
                ->body('Nominal SPP: Rp ' . number_format($tagihan->nominal_tagihan, 0, ',', '.'))
                ->danger()
                ->send();
            return;
        }

        if ($nominal <= 0) {
            Notification::make()
                ->title('Nominal tidak valid')
                ->body('Masukkan nominal yang akan dibayar.')
                ->danger()
                ->send();
            return;
        }

        if ($nominal > $tagihan->nominal_tagihan) {
            Notification::make()
                ->title('Nominal melebihi tagihan')
                ->body('Maksimal Rp ' . number_format($tagihan->nominal_tagihan, 0, ',', '.'))
                ->danger()
                ->send();
            return;
        }

        $lunas = $nominal >= $tagihan->nominal_tagihan;

        // Simpan pembayaran
        $pembayaran = Pembayaran::create([
            'siswa_id'            => $this->siswa_id,
            'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
            'tagihan_id'          => $tagihan->id,
            'bulan'               => $tagihan->bulan,
            'tahun'               => $tagihan->tahun,
            'nominal'             => $nominal,
            'tanggal_bayar'       => now(),
            'status'              => $lunas ? 'lunas' : 'cicilan',
            'created_by'          => auth()->id(),
        ]);

        // Auto-posting ke kas harian
        $pembayaran->setRelation('siswa', $this->selectedSiswa);
        $pembayaran->setRelation('jenisPembayaran', $tagihan->jenisPembayaran);
        KasHarian::postingDariPembayaran($pembayaran);
        // Auto-posting ke kas harian
        // try {
        //     $pembayaran->setRelation('siswa', $this->selectedSiswa);
        //     $pembayaran->setRelation('jenisPembayaran', $tagihan->jenisPembayaran);
        //     KasHarian::postingDariPembayaran($pembayaran);
        //     \Log::info('KasHarian posting OK', ['pembayaran_id' => $pembayaran->id]);
        // } catch (\Throwable $e) {
        //     \Log::error('KasHarian posting GAGAL', [
        //         'message' => $e->getMessage(),
        //         'file'    => $e->getFile(),
        //         'line'    => $e->getLine(),
        //     ]);
        // }

        // ===== GENERATE LINK KUITANSI =====
        \DB::table('pdf_links')->insert([
            'token'        => \Str::random(16),
            'pdf_id'       => $pembayaran->id,
            'original_url' => "/kuitansi/{$pembayaran->id}/pdf",
            'jenis'        => 'kuitansi',
            'jumlah_view'  => 0,
            'expired_at'   => now()->addDays(30),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        // ==================================

        if ($lunas) {
            // Lunas: update status tagihan
            $tagihan->update(['status' => 'lunas']);
        } else {
            // Cicilan: kurangi sisa tagihan
            $sisaNominal = $tagihan->nominal_tagihan - $nominal;
            $tagihan->update(['nominal_tagihan' => $sisaNominal]);
        }

        $jenisNama = $tagihan->jenisPembayaran->nama;
        $bulanLabel = $tagihan->bulan ? " ({$this->getBulanLabel($tagihan->bulan)})" : '';

        Notification::make()
            ->title($lunas ? 'Pembayaran Lunas' : 'Cicilan Berhasil Disimpan')
            ->body(
                'Rp ' . number_format($nominal, 0, ',', '.') .
                " — {$jenisNama}{$bulanLabel}" .
                (! $lunas ? '. Sisa: Rp ' . number_format($sisaNominal, 0, ',', '.') : '')
            )
            ->success()
            ->send();

        // Refresh data
        unset($this->tagihans, $this->history);
        $this->nominals[$tagihanId]    = null;
        $this->percentages[$tagihanId] = 0;
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    public function getBulanLabel(string $bulan): string
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ][$bulan] ?? $bulan;
    }

    /**
     * Generate URL WhatsApp dengan pesan berisi info pembayaran & link kuitansi.
     */
    public function getWhatsappUrl($bayar): string
    {
        $namaSiswa  = $this->selectedSiswa?->nama ?? '-';
        $nisSiswa   = $this->selectedSiswa?->nis  ?? '-';
        $jenis      = $bayar->jenisPembayaran?->nama ?? '-';
        $bulanLabel = $bayar->bulan ? $this->getBulanLabel($bayar->bulan) : '-';
        $tahun      = $bayar->tahun ?? '-';
        $nominal    = 'Rp ' . number_format($bayar->nominal, 0, ',', '.');
        $tanggal    = \Carbon\Carbon::parse($bayar->tanggal_bayar)->translatedFormat('d F Y');
        $linkUrl    = $bayar->share_token ? url('/k/' . $bayar->share_token) : '-';

        // Baris detail setiap cicilan
        $barisCicilan = $bayar->cicilan_list->map(function ($c, $i) {
            $tgl = \Carbon\Carbon::parse($c->tanggal_bayar)->translatedFormat('d F Y');
            $nom = 'Rp ' . number_format($c->nominal, 0, ',', '.');
            $no  = $i + 1;
            return "  Cicilan {$no}  : {$nom} ({$tgl})";
        })->implode("\n");

        $totalTagihan  = 'Rp ' . number_format($bayar->total_tagihan,  0, ',', '.');
        $totalTerbayar = 'Rp ' . number_format($bayar->total_terbayar, 0, ',', '.');
        $sisa          = 'Rp ' . number_format($bayar->sisa_tagihan,   0, ',', '.');

        if ($bayar->status === 'lunas') {
            $pesan = implode("\n", [
                'Assalamualaikum,',
                '',
                'Berikut kami sampaikan kuitansi pembayaran:',
                '',
                "Nama          : {$namaSiswa}",
                "NIS           : {$nisSiswa}",
                "Jenis         : {$jenis}",
                "Periode       : {$bulanLabel} {$tahun}",
                '',
                'Rincian Pembayaran:',
                $barisCicilan,
                '',
                "Total Tagihan : {$totalTagihan}",
                "Total Terbayar: {$totalTerbayar}",
                "Sisa Tagihan  : Rp 0",
                "Status        : *Lunas*",
                '',
                'Silakan lihat kuitansi di tautan berikut:',
                $linkUrl,
                '',
                'Terima kasih.',
            ]);
        } else {
            $pesan = implode("\n", [
                'Assalamualaikum,',
                '',
                'Berikut kami sampaikan bukti cicilan pembayaran:',
                '',
                "Nama          : {$namaSiswa}",
                "NIS           : {$nisSiswa}",
                "Jenis         : {$jenis}",
                "Periode       : {$bulanLabel} {$tahun}",
                '',
                'Rincian Pembayaran:',
                $barisCicilan,
                '',
                "Total Tagihan : {$totalTagihan}",
                "Total Terbayar: {$totalTerbayar}",
                "Sisa Tagihan  : {$sisa}",
                "Status        : *Cicilan*",
                '',
                'Silakan lihat bukti cicilan di tautan berikut:',
                $linkUrl,
                '',
                'Terima kasih.',
            ]);
        }

        // Normalisasi no_hp ke format internasional Indonesia (628xxx)
        $noHp = $this->selectedSiswa?->no_hp_orang_tua ?? '';
        $noHp = preg_replace('/\D/', '', $noHp); // hapus non-angka
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        } elseif (str_starts_with($noHp, '8')) {
            $noHp = '62' . $noHp;
        }

        $teks = rawurlencode($pesan);

        // Kalau no_hp terisi → langsung ke nomor; kalau kosong → pilih kontak manual
        return $noHp
            ? "https://wa.me/{$noHp}?text={$teks}"
            : "https://wa.me/?text={$teks}";
    }
}
