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

        return Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa_id)
            ->orderByDesc('tanggal_bayar')
            ->limit(15)
            ->get();
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
}
