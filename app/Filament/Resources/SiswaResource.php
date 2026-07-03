<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource.php
// Versi 3 — tambah route 'detail' untuk halaman detail siswa
// Diff dari v2: hanya bagian getPages() yang berubah
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
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Siswa';
    protected static ?string $navigationGroup = 'Siswa';
    protected static ?int    $navigationSort  = 1;

    // ─── Sub-menu sidebar ─────────────────────────────────────────────────────

    public static function getNavigationItems(): array
    {
        $jenjangConfig = [
            'SD'   => 'heroicon-o-book-open',
            'SMP'  => 'heroicon-o-building-library',
            'DTA'  => 'heroicon-o-star',
            'PAUD' => 'heroicon-o-face-smile',
        ];

        $items = [];
        $sort  = 1;

        foreach ($jenjangConfig as $jenjang => $icon) {
            $items[] = NavigationItem::make($jenjang)
                ->group('Siswa')
                ->sort($sort++)
                ->icon($icon)
                ->badge(Siswa::where('jenis_sekolah', $jenjang)->where('is_calon', 0)->count() ?: null)
                ->url(static::getUrl('jenjang', ['jenjang' => $jenjang]))
                ->isActiveWhen(fn () =>
                    request()->routeIs('filament.admin.resources.siswas.jenjang')
                    && request()->route('jenjang') === $jenjang
                );
        }

        $items[] = NavigationItem::make('Calon Siswa')
            ->group('Siswa')
            ->sort($sort)
            ->icon('heroicon-o-user-plus')
            ->badge(Siswa::where('is_calon', 1)->count() ?: null)
            ->url(static::getUrl('calon'))
            ->isActiveWhen(fn () =>
                request()->routeIs('filament.admin.resources.siswas.calon')
            );

        return $items;
    }

    // ─── Opsi kelas ───────────────────────────────────────────────────────────

    public static function getKelasOptions(string $jenjang = 'SD'): array
    {
        return match (strtoupper($jenjang)) {
            'SD'   => self::buildKelas(6, ['A', 'B', 'C', 'D']),
            'SMP'  => self::buildKelas(3, ['A', 'B', 'C']),
            'DTA'  => self::buildKelas(4, ['A', 'B']),
            'PAUD' => ['TK-A' => 'TK-A', 'TK-B' => 'TK-B', 'Kelompok Bermain' => 'Kelompok Bermain'],
            default => [],
        };
    }

    private static function buildKelas(int $maxTingkat, array $huruf): array
    {
        $options = [];
        for ($i = 1; $i <= $maxTingkat; $i++) {
            foreach ($huruf as $h) {
                $label           = "{$i}{$h}";
                $options[$label] = $label;
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
                Section::make()
                    ->schema([
                        Toggle::make('is_calon')
                            ->label(fn (Get $get) => $get('is_calon') ? 'Form Input Tambah Calon Siswa' : 'Form Input Tambah Siswa')
                            ->helperText(fn (Get $get) => $get('is_calon') ? 'Aktif: mengisi data calon siswa baru' : 'Nonaktif: mengisi data siswa yang sudah terdaftar')
                            ->default(false)
                            ->live()
                            ->onColor('warning'),
                    ])
                    ->compact()
                    ->extraAttributes(fn (Get $get) => [
                        'style' => $get('is_calon')
                            ? 'background:#fffbeb;border:1px solid #fde68a;border-radius:0.5rem;'
                            : 'background:#f0fdf4;border:1px solid #bbf7d0;border-radius:0.5rem;',
                    ]),

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

                            Select::make('jenis_sekolah')
                                ->label('Jenjang Sekolah')
                                ->options([
                                    'SD'   => 'SD',
                                    'SMP'  => 'SMP',
                                    'DTA'  => 'DTA',
                                    'PAUD' => 'PAUD',
                                ])
                                ->native(false)
                                ->required()
                                ->live()
                                ->placeholder('Pilih jenjang'),

                            Select::make('kelas')
                                ->label('Kelas')
                                ->options(fn (Get $get) => static::getKelasOptions($get('jenis_sekolah') ?? 'SD'))
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
                                    'paud' => 'PAUD',
                                    'tk'   => 'TK',
                                    'sd'   => 'SD',
                                    'smp'  => 'SMP',
                                    'dta'  => 'DTA',
                                ])
                                ->native(false)
                                ->required()
                                ->placeholder('Pilih jenjang'),

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
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                // TextColumn::make('jenis_sekolah')
                //     ->label('Jenjang')
                //     ->badge()
                //     ->color(fn ($state) => match ($state) {
                //         'SD'   => 'success',
                //         'SMP'  => 'info',
                //         'DTA'  => 'warning',
                //         'PAUD' => 'danger',
                //         default => 'gray',
                //     })
                //     ->placeholder('-'),

                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->placeholder('-'),

                // TextColumn::make('calon_jenis')
                //     ->label('Calon Jenjang')
                //     ->badge()
                //     ->formatStateUsing(fn ($state) => strtoupper($state ?? ''))
                //     ->color(fn ($state) => match (strtolower($state ?? '')) {
                //         'paud' => 'danger',
                //         'tk'   => 'gray',
                //         'sd'   => 'success',
                //         'smp'  => 'info',
                //         'dta'  => 'warning',
                //         default => 'gray',
                //     })
                //     ->placeholder('-'),

                // TextColumn::make('nama_orang_tua')
                //     ->label('Orang Tua')
                //     ->searchable()
                //     ->placeholder('-'),

                // TextColumn::make('no_hp_orang_tua')
                //     ->label('No HP')
                //     ->searchable()
                //     ->placeholder('-'),

                IconColumn::make('status_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('nama', 'asc');
    }

    // ─── Pages ────────────────────────────────────────────────────────────────
    // PERUBAHAN dari v2: tambah route 'detail'

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListSiswas::route('/'),
            'jenjang' => Pages\ListSiswaByJenjang::route('/jenjang/{jenjang}'),
            'kelas'   => Pages\ListSiswaByKelas::route('/jenjang/{jenjang}/kelas/{kelas}'),
            'calon'   => Pages\ListCalonSiswa::route('/calon'),
            'create'  => Pages\CreateSiswa::route('/create'),
            'edit'    => Pages\EditSiswa::route('/{record}/edit'),
            'detail'  => Pages\DetailSiswa::route('/{record}/detail'),  // ← BARU
        ];
    }
}
