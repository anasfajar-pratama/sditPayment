<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->searchable(['nis', 'nama'])
                    ->preload()
                    ->required()
                    ->label('Siswa (NIS - Nama)'),

                Forms\Components\Select::make('jenis_pembayaran_id')
                    ->relationship('jenisPembayaran', 'nama')
                    ->required()
                    ->label('Jenis Pembayaran')
                    ->live(),   // ini penting agar bulan bisa reactive

                Forms\Components\Select::make('bulan')
                    ->options([
                        'Januari'   => 'Januari',
                        'Februari'  => 'Februari',
                        'Maret'     => 'Maret',
                        'April'     => 'April',
                        'Mei'       => 'Mei',
                        'Juni'      => 'Juni',
                        'Juli'      => 'Juli',
                        'Agustus'   => 'Agustus',
                        'September' => 'September',
                        'Oktober'   => 'Oktober',
                        'November'  => 'November',
                        'Desember'  => 'Desember',
                    ])
                    ->required(fn (Forms\Get $get) => 
                        $get('jenis_pembayaran_id') && 
                        \App\Models\JenisPembayaran::find($get('jenis_pembayaran_id'))?->is_periodik
                    )
                    ->visible(fn (Forms\Get $get) => 
                        $get('jenis_pembayaran_id') && 
                        \App\Models\JenisPembayaran::find($get('jenis_pembayaran_id'))?->is_periodik
                    )
                    ->label('Bulan'),

                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->default(now()->year)
                    ->label('Tahun'),

                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->label('Nominal Bayar'),

                Forms\Components\DatePicker::make('tanggal_bayar')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'lunas'   => 'Lunas',
                    ])
                    ->default('lunas')
                    ->required(),

                Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    // Mutate Data
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
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
                Tables\Columns\TextColumn::make('nominal')->money('IDR'),
                Tables\Columns\TextColumn::make('tanggal_bayar')->date('d M Y'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'lunas',
                    ]),
            ])->actions([
            Action::make('cetak_kuitansi')
                ->label('Cetak Kuitansi')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (Pembayaran $record) => route('kuitansi.pdf', $record))
                ->openUrlInNewTab(),
            ])->defaultSort('tanggal_bayar', 'desc')
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }

        // Redirect setelah Create langsung ke Index (List)
    protected static function getRedirectAfterCreate(): string
    {
        return static::getUrl('index');
    }

    // Redirect setelah Edit juga ke Index (opsional)
    protected static function getRedirectAfterSave(): string
    {
        return static::getUrl('index');
    }
}