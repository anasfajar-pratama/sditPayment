<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Pages/KasHarianPage.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Pages;

use App\Models\Akun;
use App\Models\KasHarian;
use App\Models\SaldoAwalBulan;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class KasHarianPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Kas Harian';
    protected static ?string $title           = 'Kas Harian';
    protected static ?int    $navigationSort  = 10;

    protected static string $view = 'filament.pages.kas-harian-page';

    #[Url]
    public string $filterBulan;

    #[Url]
    public string $filterTahun;

    public function mount(): void
    {
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->format('Y');
    }

    // ─── Computed: semua entri + running saldo ───────────────────────────────

    #[Computed]
    public function entries(): array
    {
        $rows = KasHarian::with('akun')
            ->where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $saldo   = $this->saldoAwal;
        $result  = [];

        foreach ($rows as $row) {
            $saldo += (float) ($row->debit ?? 0);
            $saldo -= (float) ($row->kredit ?? 0);

            $result[] = [
                'id'         => $row->id,
                'tanggal'    => $row->tanggal->format('d M y'),
                'uraian'     => $row->uraian,
                'akun'       => $row->akun?->nama_akun,
                'debit'      => $row->debit,
                'kredit'     => $row->kredit,
                'saldo'      => $saldo,
                'source'     => $row->source,
            ];
        }

        return $result;
    }

    #[Computed]
    public function saldoAwal(): float
    {
        return SaldoAwalBulan::getSaldo($this->filterBulan, $this->filterTahun);
    }

    #[Computed]
    public function totalDebit(): float
    {
        return KasHarian::where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
            ->sum('debit');
    }

    #[Computed]
    public function totalKredit(): float
    {
        return KasHarian::where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
            ->sum('kredit');
    }

    #[Computed]
    public function saldoAkhir(): float
    {
        return $this->saldoAwal + $this->totalDebit - $this->totalKredit;
    }

    #[Computed]
    public function hasSaldoAwal(): bool
    {
        return SaldoAwalBulan::where('bulan', $this->filterBulan)
            ->where('tahun', $this->filterTahun)
            ->exists();
    }

    public function updatedFilterBulan(): void
    {
        unset($this->entries, $this->saldoAwal, $this->totalDebit, $this->totalKredit);
    }

    public function updatedFilterTahun(): void
    {
        unset($this->entries, $this->saldoAwal, $this->totalDebit, $this->totalKredit);
    }

    // ─── Header Actions ──────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Set Saldo Awal
            Action::make('setSaldoAwal')
                ->label('Set Saldo Awal')
                ->icon('heroicon-o-currency-dollar')
                ->color('info')
                ->modalHeading('Set Saldo Awal Bulan')
                ->modalWidth('sm')
                ->form([
                    TextInput::make('saldo_awal')
                        ->label('Saldo Awal (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->default(fn () =>
                            SaldoAwalBulan::getSaldo($this->filterBulan, $this->filterTahun) ?: null
                        ),
                    TextInput::make('keterangan')
                        ->label('Keterangan (opsional)')
                        ->placeholder('Contoh: Saldo dari bulan sebelumnya'),
                ])
                ->action(function (array $data): void {
                    SaldoAwalBulan::updateOrCreate(
                        ['bulan' => $this->filterBulan, 'tahun' => $this->filterTahun],
                        [
                            'saldo_awal'  => $data['saldo_awal'],
                            'keterangan'  => $data['keterangan'] ?? null,
                            'created_by'  => auth()->id(),
                        ]
                    );

                    unset($this->saldoAwal, $this->saldoAkhir, $this->entries);

                    Notification::make()
                        ->title('Saldo awal berhasil disimpan')
                        ->success()
                        ->send();
                }),

            // Tombol Input Jurnal
            Action::make('inputJurnal')
                ->label('Input Jurnal')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->modalHeading('Input Jurnal Kas Manual')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Simpan Jurnal')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->required()
                        ->default(now()),

                    Select::make('akun_id')
                        ->label('Akun')
                        ->options(function () {
                            return Akun::where('is_active', true)
                                ->whereNotIn('kelompok', ['Aset']) // exclude aset (kas/bank)
                                ->orderBy('kode_akun')
                                ->get()
                                ->groupBy('kelompok')
                                ->map(fn ($group) =>
                                    $group->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} — {$a->nama_akun}"])
                                )
                                ->toArray();
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                            if (!$state) return;
                            $akun = Akun::find($state);
                            // Pendapatan → DEBIT (uang masuk), Beban → KREDIT (uang keluar)
                            $set('tipe', $akun?->kelompok === 'Pendapatan' ? 'debit' : 'kredit');
                        })
                        ->required()
                        ->searchable()
                        ->preload(),

                    Textarea::make('uraian')
                        ->label('Uraian / Keterangan')
                        ->required()
                        ->rows(2)
                        ->placeholder('Contoh: Gaji Pak Asep + Minum Desember 2025'),

                    Radio::make('tipe')
                        ->label('Tipe Transaksi')
                        ->options([
                            'debit'  => 'DEBIT — Uang Masuk',
                            'kredit' => 'KREDIT — Uang Keluar',
                        ])
                        ->default('kredit')
                        ->inline()
                        ->required(),

                    TextInput::make('nominal')
                        ->label('Nominal')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tanggal = \Carbon\Carbon::parse($data['tanggal']);

                    KasHarian::create([
                        'tanggal'    => $data['tanggal'],
                        'uraian'     => $data['uraian'],
                        'akun_id'    => $data['akun_id'],
                        'debit'      => $data['tipe'] === 'debit' ? $data['nominal'] : null,
                        'kredit'     => $data['tipe'] === 'kredit' ? $data['nominal'] : null,
                        'source'     => 'manual',
                        'bulan'      => $tanggal->format('m'),
                        'tahun'      => $tanggal->format('Y'),
                        'created_by' => auth()->id(),
                    ]);

                    unset($this->entries, $this->totalDebit, $this->totalKredit, $this->saldoAkhir);

                    Notification::make()
                        ->title('Jurnal berhasil disimpan')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function deleteEntry(int $id): void
    {
        $entry = KasHarian::findOrFail($id);

        if ($entry->source === 'pembayaran') {
            Notification::make()
                ->title('Tidak bisa dihapus')
                ->body('Entri otomatis dari pembayaran tidak bisa dihapus langsung. Hapus data pembayaran terkait.')
                ->danger()
                ->send();
            return;
        }

        $entry->delete();
        unset($this->entries, $this->totalDebit, $this->totalKredit, $this->saldoAkhir);

        Notification::make()->title('Jurnal dihapus')->success()->send();
    }

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
