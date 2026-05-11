<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Siswas';
    protected static ?int    $navigationSort  = 1;

    // ─── Opsi Kelas ───────────────────────────────────────────────────────────

    private static function getKelasOptions(): array
    {
        $options = [];
        $huruf   = ['A', 'B', 'C'];

        for ($i = 1; $i <= 6; $i++) {
            foreach ($huruf as $h) {
                $label            = "{$i}{$h}";
                $options[$label]  = $label;
            }
        }

        return $options;
    }

    private static function isTagihanLunas(?\Illuminate\Database\Eloquent\Model $record): bool
    {
        if (! $record) {
            return false;
        }
        return Tagihan::where('siswa_id', $record->id)
            ->where('jenis_pembayaran_id', 1)
            ->where('status', 'lunas')
            ->exists();
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ── Toggle is_calon — selalu di atas ──────────────────────────
                Section::make()
                    ->schema([
                        Toggle::make('is_calon')
                            ->label('Calon Siswa')
                            ->helperText('Aktifkan jika ini adalah calon siswa (pendaftar baru)')
                            ->default(false)
                            ->live()
                            ->onColor('warning')
                            ->offColor('primary'),
                    ])
                    ->compact(),

                // ══════════════════════════════════════════════════════════════
                // FORM SISWA — hanya muncul saat is_calon = false
                // ══════════════════════════════════════════════════════════════
                Section::make('Data Siswa')
                    ->description('Isi data siswa yang sudah terdaftar')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nis')
                                ->label('NIS')
                                ->maxLength(20)
                                ->placeholder('Contoh: 2025001'),

                            TextInput::make('nama')
                                ->label('Nama Siswa')
                                ->required()
                                ->maxLength(100),

                            Select::make('kelas')
                                ->label('Kelas')
                                ->options(static::getKelasOptions())
                                ->searchable()
                                ->native(false)
                                ->placeholder('Pilih kelas'),

                            TextInput::make('tahun_ajaran')
                                ->label('Tahun Ajaran')
                                ->placeholder('2025-2026')
                                ->maxLength(20),
                            
                            TextInput::make('nama_orang_tua')
                                ->label('Nama Orang Tua / Wali')
                                ->maxLength(100),

                            TextInput::make('no_hp_orang_tua')
                                ->label('No HP Orang Tua')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('email_orang_tua')
                                ->label('Email Orang Tua')
                                ->email()
                                ->maxLength(100),

                            Toggle::make('status_aktif')
                                ->label('Status Aktif')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->hidden(fn (Get $get) => (bool) $get('is_calon')),

                // ══════════════════════════════════════════════════════════════
                // FORM CALON SISWA — hanya muncul saat is_calon = true
                // ══════════════════════════════════════════════════════════════
                Section::make('Data Calon Siswa')
                    ->description('Isi data pendaftar / calon siswa baru')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Calon Siswa')
                                ->required()
                                ->maxLength(100),

                            TextInput::make('nama_orang_tua')
                                ->label('Nama Orang Tua / Wali')
                                ->maxLength(100),

                            TextInput::make('no_hp_orang_tua')
                                ->label('No HP Orang Tua')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('email_orang_tua')
                                ->label('Email Orang Tua')
                                ->email()
                                ->maxLength(100),

                            Select::make('calon_jenis')
                                ->label('Jenjang Pendidikan yang Dituju')
                                ->options([
                                    'PAUD' => 'PAUD',
                                    'TK'   => 'TK',
                                    'SD'   => 'SD',
                                    'SMP'  => 'SMP',
                                    'SMA'  => 'SMA',
                                ])
                                ->native(false)
                                ->required()
                                ->placeholder('Pilih jenjang'),

                            // ── Field biaya pendaftaran (tidak disimpan ke tabel siswa) ──
                            TextInput::make('nominal_biaya_pendaftaran')
                                ->label('Nominal Biaya Pendaftaran')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0)
                                ->placeholder('Contoh: 500000')
                                ->required(fn ($record) => ! static::isTagihanLunas($record))
                                ->disabled(fn ($record) => static::isTagihanLunas($record))
                                ->helperText(fn ($record) => static::isTagihanLunas($record)
                                    ? '⚠ Tagihan sudah lunas, nominal tidak dapat diubah.'
                                    : null),
                                // ->dehydrated(false), // tidak ikut disimpan ke model Siswa

                            Toggle::make('status_aktif')
                                ->label('Status Aktif')
                                ->default(true),
                        ]),
                    ])
                    ->hidden(fn (Get $get) => ! (bool) $get('is_calon')),

            ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tab Siswa
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'calon'),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'calon'),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'calon'),

                // Tab Calon Siswa
                TextColumn::make('nama_orang_tua')
                    ->label('Nama Orang Tua')
                    ->searchable()
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'siswa'),

                TextColumn::make('no_hp_orang_tua')
                    ->label('Nomor Handphone Orang Tua')
                    ->searchable()
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'siswa'),

                TextColumn::make('calon_jenis')
                    ->label('Jenjang')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'PAUD' => 'gray',
                        'TK'   => 'info',
                        'SD'   => 'success',
                        'SMP'  => 'warning',
                        'SMA'  => 'danger',
                        default => 'gray',
                    })
                    ->hidden(fn (Pages\ListSiswas $livewire) => $livewire->activeTab === 'siswa'),

                // Kolom bersama
                IconColumn::make('status_aktif')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('nama', 'asc');
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit'   => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
