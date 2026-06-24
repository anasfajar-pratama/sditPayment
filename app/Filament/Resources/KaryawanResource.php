<?php
// File: app/Filament/Resources/KaryawanResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Karyawan';
    protected static ?string $navigationLabel = 'Data Karyawan';
    protected static ?string $modelLabel      = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Data Karyawan';
    protected static ?int    $navigationSort  = 10;   // muncul paling atas di group Karyawan

    // ─── FORM ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Identitas ───────────────────────────────────────────────────
            Forms\Components\Section::make('Identitas Karyawan')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('nik')
                        ->label('NIK Karyawan')
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),

                    Forms\Components\Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->native(false),

                    Forms\Components\TextInput::make('no_ktp')
                        ->label('No. KTP')
                        ->maxLength(16),

                    Forms\Components\TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir'),

                    Forms\Components\DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Forms\Components\TextInput::make('no_hp')
                        ->label('No. HP / WA')
                        ->tel(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),

                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            // ── Kepegawaian ─────────────────────────────────────────────────
            Forms\Components\Section::make('Data Kepegawaian')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('job')
                        ->label('Kategori / Job')
                        ->options([
                            'guru'     => 'Guru',
                            'admin'    => 'Admin',
                            'operator' => 'Operator',
                            'penjaga'  => 'Penjaga Sekolah',
                            'kantin'   => 'Kantin',
                        ])
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('mata_pelajaran', null)),

                    Forms\Components\TextInput::make('jabatan')
                        ->label('Jabatan')
                        ->placeholder('Wali Kelas 1A, Kepala Sekolah, Guru Mapel, dll')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('mata_pelajaran')
                        ->label('Mata Pelajaran')
                        ->placeholder('Matematika, IPA, PAI, dll')
                        ->visible(fn (Forms\Get $get) => $get('job') === 'guru'),

                    Forms\Components\TextInput::make('kelas_ajar')
                        ->label('Kelas Ajar')
                        ->placeholder('1A, 2B, Semua, dll')
                        ->visible(fn (Forms\Get $get) => $get('job') === 'guru'),

                    Forms\Components\Select::make('status_kepegawaian')
                        ->label('Status Kepegawaian')
                        ->options([
                            'GTY'   => 'GTY – Guru Tetap Yayasan',
                            'GTT'   => 'GTT – Guru Tidak Tetap',
                            'Honor' => 'Honor',
                            'PNS'   => 'PNS',
                            'PPPK'  => 'PPPK',
                        ])
                        ->native(false),

                    Forms\Components\Select::make('status')
                        ->label('Status Aktif')
                        ->options([
                            'aktif'       => 'Aktif',
                            'tidak_aktif' => 'Tidak Aktif',
                            'cuti'        => 'Cuti',
                            'resign'      => 'Resign',
                        ])
                        ->default('aktif')
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('tanggal_masuk')
                        ->label('Tanggal Masuk')
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Forms\Components\DatePicker::make('tanggal_keluar')
                        ->label('Tanggal Keluar')
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                ]),

            // ── Penggajian & Rekening ────────────────────────────────────────
            Forms\Components\Section::make('Penggajian & Rekening')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('gaji_pokok')
                        ->label('Gaji Pokok (Rp)')
                        ->numeric()
                        ->prefix('Rp'),

                    Forms\Components\TextInput::make('nama_bank')
                        ->label('Nama Bank'),

                    Forms\Components\TextInput::make('no_rekening')
                        ->label('No. Rekening'),

                    Forms\Components\TextInput::make('atas_nama_rekening')
                        ->label('Atas Nama Rekening'),
                ]),
        ]);
    }

    // ─── TABLE ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('3rem'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Karyawan $r) => $r->mata_pelajaran
                        ? $r->mata_pelajaran . ($r->kelas_ajar ? ' · Kls ' . $r->kelas_ajar : '')
                        : null),

                Tables\Columns\BadgeColumn::make('job')
                    ->label('Job')
                    ->colors([
                        'success' => 'guru',
                        'warning' => 'admin',
                        'info'    => 'operator',
                        'gray'    => 'penjaga',
                        'danger'  => 'kantin',
                    ])
                    ->formatStateUsing(fn ($state) => [
                        'guru'     => 'Guru',
                        'admin'    => 'Admin',
                        'operator' => 'Operator',
                        'penjaga'  => 'Penjaga',
                        'kantin'   => 'Kantin',
                    ][$state] ?? $state),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_kepegawaian')
                    ->label('Status Kepeg.')
                    ->colors([
                        'success' => fn ($state) => in_array($state, ['GTY', 'PNS', 'PPPK']),
                        'warning' => 'GTT',
                        'gray'    => 'Honor',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Aktif?')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'cuti',
                        'danger'  => ['tidak_aktif', 'resign'],
                    ])
                    ->formatStateUsing(fn ($state) => [
                        'aktif'       => 'Aktif',
                        'tidak_aktif' => 'Non-aktif',
                        'cuti'        => 'Cuti',
                        'resign'      => 'Resign',
                    ][$state] ?? $state),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->default('—'),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tgl Masuk')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('nama')
            ->searchPlaceholder('Cari nama / jabatan...')

            ->filters([
                SelectFilter::make('job')
                    ->label('Filter Job')
                    ->options([
                        'guru'     => 'Guru',
                        'admin'    => 'Admin',
                        'operator' => 'Operator',
                        'penjaga'  => 'Penjaga Sekolah',
                        'kantin'   => 'Kantin',
                    ])
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'aktif'       => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'cuti'        => 'Cuti',
                        'resign'      => 'Resign',
                    ])
                    ->native(false),

                SelectFilter::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->options([
                        'GTY'   => 'GTY',
                        'GTT'   => 'GTT',
                        'Honor' => 'Honor',
                        'PNS'   => 'PNS',
                        'PPPK'  => 'PPPK',
                    ])
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)

            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-m-pencil-square'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-m-trash'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->headerActions([
                Tables\Actions\Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($livewire) {
                        // Ambil filter yang aktif
                        $filters  = $livewire->tableFilters ?? [];
                        $job      = $filters['job']['value']                ?? '';
                        $status   = $filters['status']['value']             ?? '';
                        $kepeg    = $filters['status_kepegawaian']['value'] ?? '';
                        $search   = $livewire->tableSearch               ?? '';

                        $url = route('karyawan.pdf', compact('job', 'status', 'kepeg', 'search'));
                        $livewire->js("window.open('{$url}', '_blank')");
                    }),
            ]);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit'   => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
