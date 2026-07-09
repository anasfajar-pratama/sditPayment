<?php
// File: app/Filament/Resources/TokenListrikResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenListrikResource\Pages;
use App\Models\TokenListrik;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TokenListrikResource extends Resource
{
    protected static ?string $model           = TokenListrik::class;
    protected static ?string $navigationIcon  = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Token Listrik';
    protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $pluralLabel     = 'Token Listrik';
    protected static ?int    $navigationSort  = 50;

    // ─── Form (Create & Edit) ─────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Token Listrik')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nama_ruangan')
                                ->label('Nama Ruangan')
                                ->required()
                                ->maxLength(100)
                                ->placeholder('Contoh: Kelas 1A, Kantor, Masjid'),

                            Forms\Components\TextInput::make('nomor_meter')
                                ->label('Nomor Meter (ID Pelanggan)')
                                ->required()
                                ->maxLength(50)
                                ->unique(
                                    table: 'token_listrik',
                                    column: 'nomor_meter',
                                    ignoreRecord: true,
                                )
                                ->validationMessages([
                                    'unique' => 'Nomor meter ini sudah terdaftar.',
                                ])
                                ->placeholder('Contoh: 1234567890'),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->required()
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        $namaBulan = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];

        $tahunTersedia = array_combine(
            range(now()->year - 3, now()->year),
            range(now()->year - 3, now()->year),
        );

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_ruangan')
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_meter')
                    ->label('Nomor Meter')
                    ->searchable()
                    ->placeholder('-'),

                // ── Total sesuai filter periode ──────────────────────────────
                Tables\Columns\TextColumn::make('total_bulan')
                    ->label('Total Bulan Ini')
                    ->getStateUsing(function (TokenListrik $record, $livewire): string {
                        $filters = $livewire->tableFilters ?? [];
                        $bulan   = $filters['periode']['bulan'] ?? now()->month;
                        $tahun   = $filters['periode']['tahun'] ?? now()->year;

                        $total = $record->pembelians()
                            ->whereMonth('tanggal', (int) $bulan)
                            ->whereYear('tanggal', (int) $tahun)
                            ->sum('nominal');

                        return 'Rp ' . number_format((float) $total, 0, ',', '.');
                    })
                    ->color('primary'),

                // ── Jumlah transaksi bulan yang dipilih ──────────────────────
                Tables\Columns\TextColumn::make('jml_transaksi_bulan')
                    ->label('Transaksi Bulan Ini')
                    ->getStateUsing(function (TokenListrik $record, $livewire): string {
                        $filters = $livewire->tableFilters ?? [];
                        $bulan   = $filters['periode']['bulan'] ?? now()->month;
                        $tahun   = $filters['periode']['tahun'] ?? now()->year;

                        $count = $record->pembelians()
                            ->whereMonth('tanggal', (int) $bulan)
                            ->whereYear('tanggal', (int) $tahun)
                            ->count();

                        return $count . 'x';
                    }),

                // ── Total semua waktu (bisa disembunyikan via toggle kolom) ──
                Tables\Columns\TextColumn::make('pembelians_sum_nominal')
                    ->label('Total Semua Waktu')
                    ->sum('pembelians', 'nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->placeholder('Rp 0')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // ─── Filter Periode ───────────────────────────────────────────────
            ->filters([
                Tables\Filters\Filter::make('periode')
                    ->label('Periode Pembelian')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('bulan')
                                ->label('Bulan')
                                ->options($namaBulan)
                                ->default(now()->month)
                                ->placeholder('Pilih bulan'),

                            Forms\Components\Select::make('tahun')
                                ->label('Tahun')
                                ->options($tahunTersedia)
                                ->default(now()->year)
                                ->placeholder('Pilih tahun'),
                        ]),
                    ])
                    // Filter ini hanya mengubah tampilan kolom, tidak menyembunyikan baris
                    ->query(fn (Builder $query, array $data) => $query)
                    ->indicateUsing(function (array $data) use ($namaBulan): ?string {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;

                        if ($bulan && $tahun) {
                            return 'Periode: ' . ($namaBulan[(int) $bulan] ?? $bulan) . ' ' . $tahun;
                        }

                        return null;
                    }),
            ])
            ->filtersFormColumns(1)
            ->defaultSort('nama_ruangan', 'asc')
            ->recordUrl(fn (TokenListrik $record) => static::getUrl('detail', ['record' => $record]));
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTokenListrik::route('/'),
            'create' => Pages\CreateTokenListrik::route('/create'),
            'edit'   => Pages\EditTokenListrik::route('/{record}/edit'),
            'detail' => Pages\DetailTokenListrik::route('/{record}/detail'),
        ];
    }
}