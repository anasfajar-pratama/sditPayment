<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Pages/KasHarianPage.php
// Update: edit + hapus dengan konfirmasi sandi + log audit
// ════════════════════════════════════════════════════════════

namespace App\Filament\Pages;

use App\Filament\Traits\ConvertsToWebp;
use App\Models\Akun;
use App\Models\KasHarian;
use App\Models\KasHarianLog;
use App\Models\MasterRekeningTujuan;
use App\Models\SaldoAwalBulan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class KasHarianPage extends Page
{
    use ConvertsToWebp;

    protected static ?string $navigationIcon  = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Kas Harian';
    protected static ?string $title           = 'Kas Harian';
    protected static ?int    $navigationSort  = 20;

    protected static string $view = 'filament.pages.kas-harian-page';

    // ─── Filter state ─────────────────────────────────────────────────────────

    #[Url] public string $filterBulan;
    #[Url] public string $filterTahun;

    /** bulanan | harian | 7hari | range */
    #[Url] public string $filterMode    = 'bulanan';
    #[Url] public string $filterTanggal = ''; // mode harian & 7hari
    #[Url] public string $filterDari    = ''; // mode range
    #[Url] public string $filterSampai  = ''; // mode range

    public function mount(): void
    {
        $this->filterBulan   = now()->format('m');
        $this->filterTahun   = now()->format('Y');
        $this->filterTanggal = now()->toDateString();
        $this->filterDari    = now()->startOfMonth()->toDateString();
        $this->filterSampai  = now()->toDateString();
    }

    // ─── Sub kategori per kelompok akun ──────────────────────────────────────

    public const SUB_KATEGORI = [
        'Operasional' => [
            'TOKEN & PULSA', 'PERLENGKAPAN', 'MAINTENANCE & FC',
            'TRANSPORT & DINAS', 'PEMBANGUNAN', 'BUKU PAKET', 'BANGKU & SERAGAM'
        ],
        'Sosial' => [
            'SOSIAL & OBAT', 'JAMUAN', 'KELUARGA', 'KASBON'
        ],
        'Upah' => [],
    ];

    // ─── Query helper ─────────────────────────────────────────────────────────

    private function buildQuery()
    {
        $q = KasHarian::with('akun');

        switch ($this->filterMode) {
            case 'harian':
                $q->whereDate('tanggal', $this->filterTanggal ?: now()->toDateString());
                break;

            case '7hari':
                $start = Carbon::parse($this->filterTanggal ?: now()->toDateString());
                $q->whereBetween('tanggal', [
                    $start->toDateString(),
                    $start->copy()->addDays(6)->toDateString(),
                ]);
                break;

            case 'range':
                $dari   = $this->filterDari   ?: now()->startOfMonth()->toDateString();
                $sampai = $this->filterSampai ?: now()->toDateString();
                $q->whereBetween('tanggal', [$dari, $sampai]);
                break;

            default: // bulanan
                $q->whereYear('tanggal', $this->filterTahun)
                  ->whereMonth('tanggal', $this->filterBulan);
                break;
        }

        return $q;
    }

    private function hitungSaldoAwalPeriode(): float
    {
        if ($this->filterMode === 'bulanan') {
            return SaldoAwalBulan::getSaldo($this->filterBulan, $this->filterTahun);
        }

        $startDate = match($this->filterMode) {
            'harian' => $this->filterTanggal ?: now()->toDateString(),
            '7hari'  => $this->filterTanggal ?: now()->toDateString(),
            'range'  => $this->filterDari    ?: now()->startOfMonth()->toDateString(),
            default  => now()->toDateString(),
        };

        $start = Carbon::parse($startDate);
        $bulan = $start->format('m');
        $tahun = $start->format('Y');
        $saldo = SaldoAwalBulan::getSaldo($bulan, $tahun);

        $before = KasHarian::whereYear('tanggal', $tahun)
                            ->whereMonth('tanggal', $bulan)
                            ->whereDate('tanggal', '<', $startDate);

        $saldo += (float) (clone $before)->sum('debit');
        $saldo -= (float) (clone $before)->sum('kredit');

        return $saldo;
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    #[Computed]
    public function entries(): array
    {
        $rows  = $this->buildQuery()->orderBy('tanggal')->orderBy('id')->get();
        $saldo = $this->saldoAwal;
        $result = [];

        foreach ($rows as $row) {
            $saldo += (float) ($row->debit  ?? 0);
            $saldo -= (float) ($row->kredit ?? 0);

            $result[] = [
                'id'               => $row->id,
                'tanggal'          => $row->tanggal->format('d M y'),
                'uraian'           => $row->uraian,
                'sub_kategori'     => $row->sub_kategori,
                'akun'             => $row->akun?->nama_akun,
                'debit'            => $row->debit,
                'kredit'           => $row->kredit,
                'saldo'            => $saldo,
                'source'           => $row->source,
                'bukti'            => $row->bukti,
                'bukti_url'        => $row->bukti_url,
                'source_bukti_url' => $row->source_bukti_url,
            ];
        }

        return $result;
    }

    #[Computed]
    public function saldoAwal(): float
    {
        return $this->hitungSaldoAwalPeriode();
    }

    #[Computed]
    public function totalDebit(): float
    {
        return (float) $this->buildQuery()->sum('debit');
    }

    #[Computed]
    public function totalKredit(): float
    {
        return (float) $this->buildQuery()->sum('kredit');
    }

    #[Computed]
    public function saldoAkhir(): float
    {
        return $this->saldoAwal + $this->totalDebit - $this->totalKredit;
    }

    #[Computed]
    public function kasHariIni(): float
    {
        $today  = now()->toDateString();
        $debit  = (float) KasHarian::whereDate('tanggal', $today)->sum('debit');
        $kredit = (float) KasHarian::whereDate('tanggal', $today)->sum('kredit');

        return $debit - $kredit;
    }

    #[Computed]
    public function hasSaldoAwal(): bool
    {
        if ($this->filterMode === 'bulanan') {
            return SaldoAwalBulan::where('bulan', $this->filterBulan)
                                 ->where('tahun', $this->filterTahun)
                                 ->exists();
        }

        $startDate = $this->filterTanggal ?: $this->filterDari ?: now()->toDateString();
        $start     = Carbon::parse($startDate);

        return SaldoAwalBulan::where('bulan', $start->format('m'))
                              ->where('tahun', $start->format('Y'))
                              ->exists();
    }

    #[Computed]
    public function judulPeriode(): string
    {
        return match($this->filterMode) {
            'harian' => Carbon::parse($this->filterTanggal)->translatedFormat('d F Y'),
            '7hari'  => Carbon::parse($this->filterTanggal)->format('d M y')
                        . ' – '
                        . Carbon::parse($this->filterTanggal)->addDays(6)->format('d M Y'),
            'range'  => Carbon::parse($this->filterDari)->format('d M y')
                        . ' – '
                        . Carbon::parse($this->filterSampai)->format('d M Y'),
            default  => $this->getBulanLabel($this->filterBulan) . ' ' . $this->filterTahun,
        };
    }

    #[Computed]
    public function logEntries(): array
    {
        return KasHarianLog::with('admin')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($log) => [
                'id'           => $log->id,
                'aksi'         => $log->aksi,
                'keterangan'   => $log->keterangan,
                'admin'        => $log->admin?->name ?? '—',
                'waktu'        => $log->created_at->format('d M y, H:i'),
                'data_sebelum' => $log->data_sebelum,
                'data_sesudah' => $log->data_sesudah,
            ])
            ->toArray();
    }

    // ─── Invalidate computed cache saat filter berubah ────────────────────────

    public function updatedFilterBulan(): void    { $this->clearCache(); }
    public function updatedFilterTahun(): void    { $this->clearCache(); }
    public function updatedFilterMode(): void     { $this->clearCache(); }
    public function updatedFilterTanggal(): void  { $this->clearCache(); }
    public function updatedFilterDari(): void     { $this->clearCache(); }
    public function updatedFilterSampai(): void   { $this->clearCache(); }

    private function clearCache(): void
    {
        unset(
            $this->entries,
            $this->saldoAwal,
            $this->totalDebit,
            $this->totalKredit,
            $this->saldoAkhir,
            $this->kasHariIni,
            $this->hasSaldoAwal,
            $this->judulPeriode,
            $this->logEntries,
        );
    }

    // ─── Form helper: sub_kategori options ───────────────────────────────────

    private function subKategoriOptions(?int $akunId): array
    {
        if (! $akunId) return [];
        $akun = Akun::find($akunId);
        if (! $akun || $akun->kelompok !== 'Beban') return [];

        $subKelompok = $akun->sub_kelompok ?? 'Operasional';

        if ($subKelompok === 'Upah') {
            return KasHarian::whereNotNull('sub_kategori')
                ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
                ->distinct()->orderBy('sub_kategori')
                ->pluck('sub_kategori')
                ->mapWithKeys(fn ($v) => [$v => $v])
                ->toArray();
        }

        $opts = static::SUB_KATEGORI[$subKelompok] ?? [];
        return array_combine($opts, $opts);
    }

    // ─── Sandi field (reused di edit & hapus) ─────────────────────────────────

    private function sandiField(): TextInput
    {
        return TextInput::make('sandi')
            ->label('Konfirmasi Sandi')
            ->password()
            ->required()
            ->helperText('Masukkan sandi akun Anda untuk mengonfirmasi');
    }

    private function verifySandi(string $sandi): bool
    {
        return Hash::check($sandi, auth()->user()->password);
    }

    // ─── Page-level actions (edit & hapus per baris) ─────────────────────────

    public function editKasAction(): Action
    {
        return Action::make('editKas')
            ->modalHeading('Edit Jurnal Kas')
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Simpan Perubahan')
            ->fillForm(function (array $arguments): array {
                $entry = KasHarian::findOrFail($arguments['id']);
                return [
                    'tanggal'               => $entry->tanggal->toDateString(),
                    'akun_id'               => $entry->akun_id,
                    'sub_kategori'          => $entry->sub_kategori,
                    'uraian'                => $entry->uraian,
                    'tipe'                  => $entry->debit ? 'debit' : 'kredit',
                    'nominal'               => (float) ($entry->debit ?? $entry->kredit),
                    'no_ref'                => $entry->no_ref,
                    'rekening_tujuan'       => $entry->rekening_tujuan ?? 'Cash',
                    'nama_rekening_pengirim'=> $entry->nama_rekening_pengirim,
                    'bukti'                 => $entry->bukti,
                ];
            })
            ->form([
                DatePicker::make('tanggal')
                    ->label('Tanggal')->required(),

                Select::make('akun_id')
                    ->label('Akun')
                    ->options(fn () => Akun::where('is_active', true)
                        ->whereNotIn('kelompok', ['Aset'])
                        ->whereNotIn('kode_akun', ['4103', '4104'])
                        ->orderByRaw("CASE WHEN kelompok = 'Beban' THEN 0 ELSE 1 END")
                        ->orderBy('kode_akun')
                        ->get()
                        ->groupBy('kelompok')
                        ->map(fn ($g) => $g->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} — {$a->nama_akun}"]))
                        ->toArray()
                    )
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('sub_kategori', null))
                    ->required()->searchable()->preload(),

                FileUpload::make('bukti')
                    ->label('Bukti Transaksi')
                    ->image()
                    ->directory('bukti-kas')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Select::make('sub_kategori')
                    ->label('Sub Kategori Pengeluaran')
                    ->helperText('Pilih dari daftar atau klik "+ Tambah baru" untuk membuat sub kategori baru')
                    ->options(fn (Get $get) => $this->subKategoriOptions($get('akun_id')))
                    ->visible(fn (Get $get) => Akun::find($get('akun_id'))?->kelompok === 'Beban')
                    ->live()
                    ->searchable()
                    ->createOptionForm(fn (Get $get) => Akun::find($get('akun_id'))?->sub_kelompok === 'Upah'
                        ? [TextInput::make('nama')->label('Nama Sub Kategori Baru')->required()->placeholder('Contoh: SD, SMP, PAUD')]
                        : []
                    )
                    ->createOptionUsing(fn (array $data) => $data['nama'])
                    ->nullable(),

                Textarea::make('uraian')
                    ->label('Uraian / Keterangan')->required()->rows(2),

                Radio::make('tipe')
                    ->label('Tipe')
                    ->options(['debit' => 'DEBIT — Uang Masuk', 'kredit' => 'KREDIT — Uang Keluar'])
                    ->inline()->required()
                    ->live(),

                TextInput::make('no_ref')
                    ->label('No. Referensi / Transfer')
                    ->placeholder('Contoh: TRF2025001')
                    ->visible(fn (Get $get) => $get('tipe') === 'debit')
                    ->columnSpanFull(),

                Select::make('rekening_tujuan')
                    ->label('Rekening Tujuan')
                    ->options(fn () => MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label'))
                    ->default('Cash')
                    ->live()
                    ->required(fn (Get $get) => $get('tipe') === 'debit')
                    ->visible(fn (Get $get) => $get('tipe') === 'debit'),

                TextInput::make('nama_rekening_pengirim')
                    ->label('Nama Pengirim')
                    ->placeholder('Contoh: Sri Utami')
                    ->visible(fn (Get $get) => $get('tipe') === 'debit' && $get('rekening_tujuan') !== 'Cash')
                    ->required(fn (Get $get) => $get('tipe') === 'debit' && $get('rekening_tujuan') !== 'Cash'),

                TextInput::make('nominal')
                    ->label('Nominal')->numeric()->prefix('Rp')->required(),

                $this->sandiField(),
            ])
            ->action(function (array $data, array $arguments): void {
                if (! $this->verifySandi($data['sandi'])) {
                    Notification::make()->title('Sandi salah')->danger()->send();
                    $this->halt();
                    return;
                }

                $entry   = KasHarian::findOrFail($arguments['id']);
                $sebelum = $entry->toArray();

                $tanggal = Carbon::parse($data['tanggal']);
                $buktibaru = $this->convertToWebp($data['bukti'] ?? null);

                $entry->update([
                    'tanggal'               => $data['tanggal'],
                    'uraian'                => $data['uraian'],
                    'akun_id'               => $data['akun_id'],
                    'sub_kategori'          => $data['sub_kategori'] ?? null,
                    'debit'                 => $data['tipe'] === 'debit'  ? $data['nominal'] : null,
                    'kredit'                => $data['tipe'] === 'kredit' ? $data['nominal'] : null,
                    'no_ref'                => $data['no_ref'] ?? null,
                    'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
                    'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
                    'bukti'                 => $buktibaru ?? $entry->bukti,
                    'bulan'                 => $tanggal->format('m'),
                    'tahun'                 => $tanggal->format('Y'),
                ]);

                $akunNamaEdit = $entry->akun?->nama_akun ?? 'Tanpa Akun';
                KasHarianLog::catat(
                    aksi: 'edit',
                    kasHarianId: $entry->id,
                    sebelum: $sebelum,
                    sesudah: $entry->fresh()->toArray(),
                    keterangan: "Edit: {$entry->uraian} ({$akunNamaEdit})",
                );

                unset($this->entries, $this->totalDebit, $this->totalKredit, $this->saldoAkhir, $this->kasHariIni, $this->logEntries);
                Notification::make()->title('Jurnal berhasil diperbarui')->success()->send();
            });
    }

    public function deleteKasAction(): Action
    {
        return Action::make('deleteKas')
            ->modalHeading('Hapus Jurnal Kas')
            ->modalDescription('Tindakan ini tidak dapat dibatalkan. Pastikan Anda yakin sebelum melanjutkan.')
            ->modalSubmitActionLabel('Ya, Hapus')
            ->color('danger')
            ->fillForm(function (array $arguments): array {
                $entry = KasHarian::findOrFail($arguments['id']);
                return ['uraian_preview' => $entry->uraian];
            })
            ->form([
                Placeholder::make('uraian_preview')
                    ->label('Jurnal yang akan dihapus')
                    ->content(fn (Get $get) => $get('uraian_preview')),

                $this->sandiField(),
            ])
            ->action(function (array $data, array $arguments): void {
                if (! $this->verifySandi($data['sandi'])) {
                    Notification::make()->title('Sandi salah')->danger()->send();
                    $this->halt();
                    return;
                }

                $entry = KasHarian::findOrFail($arguments['id']);

                if ($entry->source === 'pembayaran') {
                    Notification::make()
                        ->title('Tidak bisa dihapus')
                        ->body('Hapus data pembayaran terkait untuk menghapus entri ini.')
                        ->danger()->send();
                    return;
                }

                $snapshot = $entry->toArray();

                $akunNamaHapus = $entry->akun?->nama_akun ?? 'Tanpa Akun';
                KasHarianLog::catat(
                    aksi: 'hapus',
                    kasHarianId: $entry->id,
                    sebelum: $snapshot,
                    sesudah: null,
                    keterangan: "Hapus: {$entry->uraian} ({$akunNamaHapus})",
                );

                $entry->delete();

                unset($this->entries, $this->totalDebit, $this->totalKredit, $this->saldoAkhir, $this->kasHariIni, $this->logEntries);
                Notification::make()->title('Jurnal dihapus')->success()->send();
            });
    }

    // ─── Header Actions ───────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    $url = route('kas-harian.print', [
                        'mode'    => $this->filterMode,
                        'bulan'   => $this->filterBulan,
                        'tahun'   => $this->filterTahun,
                        'tanggal' => $this->filterTanggal,
                        'dari'    => $this->filterDari,
                        'sampai'  => $this->filterSampai,
                    ]);
                    $this->js("window.open('{$url}', '_blank')");
                }),

            Action::make('setSaldoAwal')
                ->label('Set Saldo Awal')
                ->icon('heroicon-o-currency-dollar')
                ->color('info')
                ->visible(fn () => $this->filterMode === 'bulanan')
                ->modalHeading('Set Saldo Awal Bulan')
                ->modalWidth('sm')
                ->form([
                    TextInput::make('saldo_awal')
                        ->label('Saldo Awal (Rp)')
                        ->numeric()->prefix('Rp')->required()
                        ->default(fn () => SaldoAwalBulan::getSaldo($this->filterBulan, $this->filterTahun) ?: null),
                    TextInput::make('keterangan')
                        ->label('Keterangan (opsional)')
                        ->placeholder('Contoh: Saldo dari bulan sebelumnya'),
                ])
                ->action(function (array $data): void {
                    SaldoAwalBulan::updateOrCreate(
                        ['bulan' => $this->filterBulan, 'tahun' => $this->filterTahun],
                        ['saldo_awal' => $data['saldo_awal'], 'keterangan' => $data['keterangan'] ?? null, 'created_by' => auth()->id()]
                    );
                    unset($this->saldoAwal, $this->saldoAkhir, $this->entries);
                    Notification::make()->title('Saldo awal berhasil disimpan')->success()->send();
                }),

            Action::make('inputJurnal')
                ->label('Input Jurnal')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->modalHeading('Input Jurnal Kas Manual')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Simpan Jurnal')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Tanggal')->required()->default(now())->maxDate(now()),

                    Select::make('akun_id')
                        ->label('Akun')
                        ->options(function () {
                            return Akun::where('is_active', true)
                                ->whereNotIn('kelompok', ['Aset'])
                                ->whereNotIn('kode_akun', ['4103', '4104'])
                                ->orderByRaw("CASE WHEN kelompok = 'Beban' THEN 0 ELSE 1 END")
                                ->orderBy('kode_akun')
                                ->get()
                                ->groupBy('kelompok')
                                ->map(fn ($g) => $g->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} — {$a->nama_akun}"]))
                                ->toArray();
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! $state) return;
                            $akun = Akun::find($state);
                            $set('tipe', $akun?->kelompok === 'Pendapatan' ? 'debit' : 'kredit');
                            $set('sub_kategori', null);
                        })
                        ->required()->searchable()->preload(),

                    FileUpload::make('bukti')
                        ->label('Bukti Transaksi')
                        ->image()
                        ->directory('bukti-kas')
                        ->maxSize(2048)
                        ->columnSpanFull(),

                    Select::make('sub_kategori')
                        ->label('Sub Kategori Pengeluaran')
                        ->helperText('Pilih dari daftar atau klik "+ Tambah baru" untuk membuat sub kategori baru')
                        ->options(fn (Get $get) => $this->subKategoriOptions($get('akun_id')))
                        ->visible(fn (Get $get) => Akun::find($get('akun_id'))?->kelompok === 'Beban')
                        ->live()
                        ->searchable()
                        ->createOptionForm(fn (Get $get) => Akun::find($get('akun_id'))?->sub_kelompok === 'Upah'
                            ? [TextInput::make('nama')->label('Nama Sub Kategori Baru')->required()->placeholder('Contoh: SD, SMP, PAUD')]
                            : []
                        )
                        ->createOptionUsing(fn (array $data) => $data['nama'])
                        ->nullable(),

                    Textarea::make('uraian')
                        ->label('Uraian / Keterangan')->required()->rows(2)
                        ->placeholder('Contoh: Gaji Pak Asep + Minum'),

                    Radio::make('tipe')
                        ->label('Tipe')
                        ->options(['debit' => 'DEBIT — Uang Masuk', 'kredit' => 'KREDIT — Uang Keluar'])
                        ->default('kredit')->inline()->required()
                        ->live(),

                    TextInput::make('no_ref')
                        ->label('No. Referensi / Transfer')
                        ->placeholder('Contoh: TRF2025001')
                        ->visible(fn (Get $get) => $get('tipe') === 'debit')
                        ->columnSpanFull(),

                    Select::make('rekening_tujuan')
                        ->label('Rekening Tujuan')
                        ->options(fn () => MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label'))
                        ->default('Cash')
                        ->live()
                        ->required(fn (Get $get) => $get('tipe') === 'debit')
                        ->visible(fn (Get $get) => $get('tipe') === 'debit'),

                    TextInput::make('nama_rekening_pengirim')
                        ->label('Nama Pengirim')
                        ->placeholder('Contoh: Sri Utami')
                        ->visible(fn (Get $get) => $get('tipe') === 'debit' && $get('rekening_tujuan') !== 'Cash')
                        ->required(fn (Get $get) => $get('tipe') === 'debit' && $get('rekening_tujuan') !== 'Cash'),

                    TextInput::make('nominal')
                        ->label('Nominal')->numeric()->prefix('Rp')->required(),
                ])
                ->action(function (array $data): void {
                    $tanggal = Carbon::parse($data['tanggal']);

                    $entry = KasHarian::create([
                        'tanggal'               => $data['tanggal'],
                        'uraian'                => $data['uraian'],
                        'akun_id'               => $data['akun_id'],
                        'sub_kategori'          => $data['sub_kategori'] ?? null,
                        'debit'                 => $data['tipe'] === 'debit'  ? $data['nominal'] : null,
                        'kredit'                => $data['tipe'] === 'kredit' ? $data['nominal'] : null,
                        'no_ref'                => $data['no_ref'] ?? null,
                        'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
                        'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
                        'bukti'                 => $this->convertToWebp($data['bukti'] ?? null),
                        'source'                => 'manual',
                        'bulan'                 => $tanggal->format('m'),
                        'tahun'                 => $tanggal->format('Y'),
                        'created_by'            => auth()->id(),
                    ]);

                    $akunNamaBuat = $entry->akun?->nama_akun ?? 'Tanpa Akun';
                    KasHarianLog::catat(
                        aksi: 'buat',
                        kasHarianId: $entry->id,
                        sebelum: null,
                        sesudah: $entry->toArray(),
                        keterangan: "Buat: {$entry->uraian} ({$akunNamaBuat})",
                    );

                    unset($this->entries, $this->totalDebit, $this->totalKredit, $this->saldoAkhir, $this->kasHariIni, $this->logEntries);
                    Notification::make()->title('Jurnal berhasil disimpan')->success()->send();
                }),
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getBulanLabel(string $bulan): string
    {
        return ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'][$bulan] ?? $bulan;
    }
}
