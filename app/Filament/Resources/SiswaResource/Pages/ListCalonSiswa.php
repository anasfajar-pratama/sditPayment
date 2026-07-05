<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use App\Models\Tagihan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListCalonSiswa extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getTableQuery(): Builder
    {
        return Siswa::query()->where('is_calon', 1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('calon_jenis')
                    ->label('Jenjang Pendidikan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sd'   => 'success',
                        'smp'  => 'info',
                        'dta'  => 'warning',
                        'paud' => 'danger',
                        'tk'   => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '')),

                TextColumn::make('calon_tingkat')
                    ->label('Tingkat Tujuan')
                    ->formatStateUsing(fn ($state, $record) => static::formatTingkat($state, $record->calon_jenis)),

                TextColumn::make('no_hp_orang_tua')
                    ->label('No HP Orang Tua')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('tagihan_status')
                    ->label('Status Pembayaran Biaya Masuk')
                    ->badge()
                    ->state(fn (Siswa $record): string => $record->tagihanPendaftaran?->status ?? 'belum_bayar')
                    ->color(fn (string $state): string => match ($state) {
                        'lunas'      => 'success',
                        'belum_bayar'=> 'danger',
                        default      => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lunas'      => 'Lunas',
                        'belum_bayar'=> 'Belum Bayar',
                        default      => ucfirst($state),
                    }),
            ])
            ->defaultSort('nama', 'asc');
    }

    public static function formatTingkat(?int $tingkat, ?string $jenjang): string
    {
        if ($tingkat === null) return '-';
        return match ($jenjang) {
            'sd'   => "Kelas {$tingkat}",
            'smp'  => "Kelas {$tingkat}",
            'dta'  => "Tingkat {$tingkat}",
            'paud' => $tingkat === 1 ? 'TK-A' : ($tingkat === 2 ? 'TK-B' : 'Kelompok Bermain'),
            'tk'   => $tingkat === 1 ? 'TK-A' : 'TK-B',
            default => (string) $tingkat,
        };
    }

    public function getTitle(): string
    {
        return 'Calon Siswa';
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Calon Siswa',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('proses_masuk_kelas')
                ->label('Proses Masuk Kelas')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->modalHeading('Proses Masuk Kelas')
                ->modalDescription('Pilih jenjang, lalu tentukan kelas tujuan untuk setiap calon siswa yang sudah lunas.')
                ->modalWidth('3xl')
                ->form([
                    Select::make('calon_jenis')
                        ->label('Jenjang')
                        ->options(fn () => $this->getCalonJenjangOptions())
                        ->required()
                        ->live()
                        ->native(false)
                        ->placeholder('Pilih jenjang')
                        ->afterStateUpdated(function ($state, Set $set): void {
                            $items = [];
                            if ($state) {
                                $calonList = Siswa::where('is_calon', 1)
                                    ->where('calon_jenis', $state)
                                    ->whereHas('tagihanPendaftaran', fn ($q) => $q->where('status', 'lunas'))
                                    ->orderBy('nama')
                                    ->get(['id', 'nama', 'calon_tingkat']);
                                foreach ($calonList as $c) {
                                    $items[] = [
                                        'calon_id'      => $c->id,
                                        'nama'          => $c->nama,
                                        'calon_tingkat' => $c->calon_tingkat,
                                        'calon_jenis'   => $state,
                                        'kelas'         => '',
                                    ];
                                }
                            }
                            $set('calon_items', $items);
                        }),

                    Repeater::make('calon_items')
                        ->label('Calon Siswa yang sudah Lunas')
                        ->schema([
                            Hidden::make('calon_id'),
                            Hidden::make('calon_tingkat'),
                            Hidden::make('calon_jenis'),
                            TextInput::make('nama')
                                ->label('Nama')
                                ->disabled()
                                ->extraAttributes(['class' => 'bg-gray-50']),
                            Select::make('kelas')
                                ->label('Kelas Tujuan')
                                ->options(function (Get $get) {
                                    $jenjang = $get('calon_jenis');
                                    $tingkat = $get('calon_tingkat');
                                    if (! $jenjang || ! $tingkat) return [];
                                    return $this->getKelasForTingkat($jenjang, (int) $tingkat);
                                })
                                ->native(false)
                                ->required()
                                ->placeholder('Pilih kelas'),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false),
                ])
                ->action(function (array $data): void {
                    $this->prosesCalonItems($data['calon_items'] ?? []);
                }),

            CreateAction::make()->label('Tambah Calon Siswa'),
        ];
    }

    protected function getCalonJenjangOptions(): array
    {
        $list = Siswa::where('is_calon', 1)
            ->whereNotNull('calon_jenis')
            ->whereNotNull('calon_tingkat')
            ->distinct()->pluck('calon_jenis')->toArray();
        return array_combine($list, array_map('strtoupper', $list));
    }

    protected function getKelasForTingkat(string $jenjang, int $tingkat): array
    {
        $map = ['sd' => 'SD', 'smp' => 'SMP', 'dta' => 'DTA', 'paud' => 'PAUD', 'tk' => 'SD'];
        $normalized = $map[strtolower($jenjang)] ?? null;
        if (! $normalized) return [];
        $all = SiswaResource::getKelasOptions($normalized);
        $prefix = (string) $tingkat;
        return array_filter($all, fn ($key) => str_starts_with($key, $prefix), ARRAY_FILTER_USE_KEY);
    }

    protected function prosesCalonItems(array $items): void
    {
        if (empty($items)) {
            Notification::make()->title('Tidak ada data calon siswa')->danger()->send();
            return;
        }

        $tahunAjaran = $this->getTahunAjaranBerjalan();
        $parts       = explode('/', $tahunAjaran);
        $tahunMulai  = (int) ($parts[0] ?? now()->year);
        $jenisMap    = ['sd' => 'SD', 'smp' => 'SMP', 'dta' => 'DTA', 'paud' => 'PAUD', 'tk' => 'SD'];
        $sukses      = 0;

        foreach ($items as $item) {
            if (empty($item['kelas']) || empty($item['calon_id'])) continue;

            $siswa = Siswa::find($item['calon_id']);
            if (! $siswa || ! $siswa->is_calon) continue;

            $kelas        = $item['kelas'];
            $tingkat      = (int) $kelas;
            $jenisSekolah = $jenisMap[strtolower($siswa->calon_jenis)] ?? 'SD';

            $siswa->update(['is_calon' => false]);

            SiswaKelasHistory::create([
                'siswa_id'      => $siswa->id,
                'kelas'         => $kelas,
                'tingkat'       => $tingkat,
                'jenis_sekolah' => $jenisSekolah,
                'tahun_ajaran'  => $tahunAjaran,
                'tahun_mulai'   => $tahunMulai,
                'mutasi'        => 'naik',
                'is_current'    => true,
                'created_by'    => auth()->id() ?? 1,
            ]);

            $sukses++;
        }

        Notification::make()
            ->title("{$sukses} calon siswa berhasil diproses menjadi siswa kelas")
            ->success()->send();
    }

    protected function getTahunAjaranBerjalan(): string
    {
        $now   = now();
        $start = $now->month >= 7 ? $now->year : $now->year - 1;
        return "{$start}/" . ($start + 1);
    }

    // ─── Tab per jenjang ─────────────────────────────────────────────────────

    public function getTabs(): array
    {
        $jenjangList = [
            'semua' => ['label' => 'Semua',  'db' => null,   'color' => 'gray'],
            'sd'    => ['label' => 'SD',     'db' => 'sd',   'color' => 'success'],
            'smp'   => ['label' => 'SMP',    'db' => 'smp',  'color' => 'info'],
            'dta'   => ['label' => 'DTA',    'db' => 'dta',  'color' => 'warning'],
            'paud'  => ['label' => 'PAUD',   'db' => 'paud', 'color' => 'danger'],
            'tk'    => ['label' => 'TK',     'db' => 'tk',   'color' => 'gray'],
        ];

        $tabs = [];

        foreach ($jenjangList as $key => $cfg) {
            $count = $cfg['db'] === null
                ? Siswa::where('is_calon', 1)->count()
                : Siswa::where('is_calon', 1)->where('calon_jenis', $cfg['db'])->count();

            $tab = Tab::make($cfg['label'])
                ->badge($count)
                ->badgeColor($cfg['color']);

            if ($cfg['db'] !== null) {
                $jenjangDb = $cfg['db'];
                $tab->modifyQueryUsing(
                    fn (Builder $query) => $query->where('calon_jenis', $jenjangDb)
                );
            }

            $tabs[$key] = $tab;
        }

        return $tabs;
    }
}
