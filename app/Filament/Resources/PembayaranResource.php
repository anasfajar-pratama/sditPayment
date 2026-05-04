<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\JenisPembayaran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $pluralLabel = 'Pembayaran Siswa';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->searchable(['nis', 'nama'])
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => [
                        $set('tagihan_id', null),
                        $set('nominal', null),
                    ])
                    ->label('Siswa (NIS - Nama)'),

                Forms\Components\Select::make('jenis_pembayaran_id')
                    ->relationship('jenisPembayaran', 'nama')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => [
                        $set('tagihan_id', null),
                        $set('nominal', null),
                        $set('bulan', null),
                    ])
                    ->label('Jenis Pembayaran'),

                Forms\Components\Select::make('bulan')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
                        '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::lookupTagihan($get, $set);
                    })
                    ->visible(fn (Get $get) =>
                        $get('jenis_pembayaran_id') &&
                        JenisPembayaran::find($get('jenis_pembayaran_id'))?->is_periodik
                    )
                    ->required(fn (Get $get) =>
                        $get('jenis_pembayaran_id') &&
                        JenisPembayaran::find($get('jenis_pembayaran_id'))?->is_periodik
                    )
                    ->label('Bulan'),

                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->default(now()->year)
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::lookupTagihan($get, $set);
                    })
                    ->label('Tahun'),

                // Hidden — menyimpan tagihan_id yang ditemukan
                Forms\Components\Hidden::make('tagihan_id'),

                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->label('Nominal Bayar')
                    ->helperText(fn (Get $get) =>
                        $get('tagihan_id')
                            ? '✓ Nominal diambil dari tagihan'
                            : ($get('siswa_id') && $get('jenis_pembayaran_id')
                                ? '⚠ Tagihan belum ditemukan, isi manual'
                                : null)
                    ),

                Forms\Components\DatePicker::make('tanggal_bayar')
                    ->required()
                    ->default(now())
                    ->label('Tanggal Bayar'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'lunas'   => 'Lunas',
                    ])
                    ->default('lunas')
                    ->required()
                    ->label('Status'),

                Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    // Helper: cari tagihan berdasarkan kombinasi siswa + jenis + bulan + tahun
    protected static function lookupTagihan(Get $get, Set $set): void
    {
        $siswaId           = $get('siswa_id');
        $jenisPembayaranId = $get('jenis_pembayaran_id');
        $bulan             = $get('bulan');
        $tahun             = $get('tahun');

        if (! $siswaId || ! $jenisPembayaranId || ! $tahun) {
            return;
        }

        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->where('jenis_pembayaran_id', $jenisPembayaranId)
            ->where('tahun', $tahun)
            ->when($bulan, fn ($q) => $q->where('bulan', $bulan))
            ->where('status', 'belum_bayar')
            ->first();

        if ($tagihan) {
            $set('tagihan_id', $tagihan->id);
            $set('nominal', $tagihan->nominal_tagihan);
        } else {
            $set('tagihan_id', null);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nis')->label('NIS')->searchable(),
                Tables\Columns\TextColumn::make('siswa.nama')->label('Nama Siswa')->searchable(),
                Tables\Columns\TextColumn::make('jenisPembayaran.nama')->label('Jenis'),
                Tables\Columns\TextColumn::make('bulan'),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nominal')->money('IDR')->label('Nominal'),
                Tables\Columns\TextColumn::make('tanggal_bayar')->date('d M Y')->label('Tanggal Bayar'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'lunas',
                    ]),
            ])
            ->actions([
                Action::make('cetak_kuitansi')
                    ->label('Cetak Kuitansi')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Pembayaran $record) => route('kuitansi.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('tanggal_bayar', 'desc')
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit'   => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}