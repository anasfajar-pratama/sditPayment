<?php
// File: app/Filament/Resources/DonaturResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\DonaturResource\Pages;
use App\Models\Donatur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DonaturResource extends Resource
{
    protected static ?string $model           = Donatur::class;
    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Orang Tua Asuh';
    protected static ?string $navigationGroup = 'Orang Tua Asuh';
    protected static ?string $pluralLabel     = 'Orang Tua Asuh';
    protected static ?int    $navigationSort  = 40;

    // ─── Form (dipakai halaman Create & Edit) ─────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Orang Tua Asuh')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Orang Tua Asuh')
                                ->required()
                                ->maxLength(100),

                            Forms\Components\TextInput::make('no_hp')
                                ->label('No HP')
                                ->tel()
                                ->maxLength(20),

                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(100),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),

                            Forms\Components\Textarea::make('alamat')
                                ->label('Alamat')
                                ->rows(2)
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Orang Tua Asuh')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('donasis_sum_nominal')
                    ->label('Total Donasi')
                    ->sum('donasis', 'nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->placeholder('Rp 0'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('nama', 'asc')
            ->recordUrl(fn (Donatur $record) => static::getUrl('detail', ['record' => $record]));
            // ->actions([
            //     Tables\Actions\Action::make('detail')
            //         ->label('Detail')
            //         ->icon('heroicon-o-eye')
            //         ->url(fn (Donatur $record) => static::getUrl('detail', ['record' => $record])),

            //     // Tables\Actions\EditAction::make(),
            //     // Tables\Actions\DeleteAction::make(),
            // ]);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDonatur::route('/'),
            'create' => Pages\CreateDonatur::route('/create'),
            'edit'   => Pages\EditDonatur::route('/{record}/edit'),
            'detail' => Pages\DetailDonatur::route('/{record}/detail'),
        ];
    }
}
