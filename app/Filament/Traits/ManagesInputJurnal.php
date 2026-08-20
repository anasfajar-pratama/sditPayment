<?php

namespace App\Filament\Traits;

use App\Models\Akun;
use App\Models\KasHarian;
use App\Models\KasHarianLog;
use App\Models\MasterRekeningTujuan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

trait ManagesInputJurnal
{
    use ConvertsToWebp;

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

    private function subKategoriOptions(?int $akunId): array
    {
        if (! $akunId) return [];
        $akun = Akun::find($akunId);
        if (! $akun || $akun->kelompok !== 'Beban') return [];

        $subKelompok = $akun->sub_kelompok ?? 'Operasional';

        $opts = static::SUB_KATEGORI[$subKelompok] ?? [];

        $used = KasHarian::whereNotNull('sub_kategori')
            ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', $subKelompok))
            ->distinct()->orderBy('sub_kategori')
            ->pluck('sub_kategori')
            ->toArray();

        $opts = array_values(array_unique(array_merge($opts, $used)));

        return array_combine($opts, $opts);
    }

    public function inputJurnalAction(): Action
    {
        return Action::make('inputJurnal')
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
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Sub Kategori Baru')
                            ->required()
                            ->placeholder('Contoh: KEGIATAN TAHUNAN, OPERASIONAL LAIN'),
                    ])
                    ->createOptionUsing(fn (array $data) => data_get($data, 'nama'))
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

                $this->clearKasCache();
                Notification::make()->title('Jurnal berhasil disimpan')->success()->send();
            });
    }

    protected function clearKasCache(): void
    {
        unset(
            $this->entries,
            $this->saldoAwal,
            $this->totalDebit,
            $this->totalKredit,
            $this->totalDebitTransfer,
            $this->totalDebitCash,
            $this->saldoAkhir,
            $this->kasHariIni,
            $this->hasSaldoAwal,
            $this->judulPeriode,
            $this->logEntries,
            $this->rows,
            $this->totalTransfer,
            $this->totalCash,
            $this->entriesTransfer,
            $this->entriesCash,
            $this->entriesKredit,
            $this->entriesGabungan,
            $this->entriesKasHariIni,
            $this->totalGabungan,
        );
    }
}