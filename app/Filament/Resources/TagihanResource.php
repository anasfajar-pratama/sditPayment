<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $pluralLabel = 'Daftar Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // kosong karena tidak perlu create manual
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisPembayaran.nama')
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('bulan'),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nominal_tagihan')
                    ->money('IDR')
                    ->label('Nominal'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'belum_bayar',
                        'success' => 'lunas',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
            ])
            // Hilangkan tombol New Tagihan
            ->headerActions([])
            ->actions([
                Action::make('cetak_tagihan')
                    ->label('Cetak Tagihan')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn (Tagihan $record) => route('tagihan.pdf', $record))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }
}