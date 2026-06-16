<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class KenaikanKelasPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $navigationLabel = 'Kenaikan Kelas';
    protected static ?string $title           = 'Kenaikan Kelas';
    protected static ?int    $navigationSort  = 10;

    protected static string $view = 'filament.pages.kenaikan-kelas-page';

    // ─── Filter state ─────────────────────────────────────────────────────────

    public string $filterJenisSekolah = '';
    public string $filterKelas        = '';

    public function updatedFilterJenisSekolah(): void
    {
        $this->filterKelas = '';
        unset($this->kelasList, $this->siswaDiKelas);
    }

    public function updatedFilterKelas(): void
    {
        unset($this->siswaDiKelas);
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    #[Computed]
    public function jenisSekolahList(): array
    {
        return Siswa::where('status_aktif', true)
            ->whereNotNull('jenis_sekolah')
            ->distinct()->orderBy('jenis_sekolah')
            ->pluck('jenis_sekolah')
            ->toArray();
    }

    #[Computed]
    public function kelasList(): array
    {
        if (! $this->filterJenisSekolah) return [];

        return Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('status_aktif', true)
            ->whereNotNull('kelas')
            ->distinct()->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();
    }

    #[Computed]
    public function siswaDiKelas(): array
    {
        if (! $this->filterJenisSekolah || ! $this->filterKelas) return [];

        $students = Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('kelas', $this->filterKelas)
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();

        $kelasHistories = SiswaKelasHistory::whereIn('siswa_id', $students->pluck('id'))
            ->orderBy('tahun_mulai')
            ->get()
            ->groupBy('siswa_id');

        return $students->map(function ($siswa) use ($kelasHistories) {
            return [
                'id'           => $siswa->id,
                'nis'          => $siswa->nis,
                'nama'         => $siswa->nama,
                'kelas'        => $siswa->kelas,
                'jenis_sekolah'=> $siswa->jenis_sekolah,
                'history'      => ($kelasHistories->get($siswa->id) ?? collect())
                    ->map(fn ($h) => [
                        'tahun_ajaran'  => $h->tahun_ajaran,
                        'kelas'         => $h->kelas,
                        'jenis_sekolah' => $h->jenis_sekolah,
                    ])
                    ->toArray(),
            ];
        })->toArray();
    }

    // ─── Info tahun ajaran berjalan ────────────────────────────────────────────

    public function getTahunAjaranBerjalan(): string
    {
        $now   = now();
        $start = $now->month >= 7 ? $now->year : $now->year - 1;
        return "{$start}/" . ($start + 1);
    }

    public function getTahunMulai(): int
    {
        $now = now();
        return $now->month >= 7 ? $now->year : $now->year - 1;
    }

    // ─── Action: Naik kelas individual ────────────────────────────────────────

    public function naikKelasAction(): Action
    {
        return Action::make('naikKelas')
            ->modalHeading('Naik Kelas Siswa')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Simpan Kenaikan Kelas')
            ->fillForm(function (array $arguments): array {
                $siswa = Siswa::findOrFail($arguments['siswa_id']);
                return [
                    'nama_siswa'     => $siswa->nama,
                    'kelas_lama'     => $siswa->kelas,
                    'jenis_lama'     => $siswa->jenis_sekolah,
                    'kelas_baru'     => '',
                    'jenis_baru'     => $siswa->jenis_sekolah,
                    'catatan'        => '',
                ];
            })
            ->form([
                \Filament\Forms\Components\Placeholder::make('info_siswa')
                    ->label('Siswa')
                    ->content(fn (\Filament\Forms\Get $get) => "{$get('nama_siswa')} — saat ini Kelas {$get('kelas_lama')} ({$get('jenis_lama')})"),

                \Filament\Forms\Components\Hidden::make('nama_siswa'),
                \Filament\Forms\Components\Hidden::make('kelas_lama'),
                \Filament\Forms\Components\Hidden::make('jenis_lama'),

                Select::make('jenis_baru')
                    ->label('Jenis Sekolah Baru')
                    ->options(fn () => collect($this->jenisSekolahList)->mapWithKeys(fn ($v) => [$v => $v])->toArray())
                    ->required(),

                TextInput::make('kelas_baru')
                    ->label('Kelas Baru')
                    ->placeholder('Contoh: 2A, 3B, IV')
                    ->required(),

                Textarea::make('catatan')
                    ->label('Catatan (opsional)')
                    ->rows(2)
                    ->placeholder('Contoh: Naik kelas reguler T.A. 2025/2026'),
            ])
            ->action(function (array $data, array $arguments): void {
                $siswa = Siswa::findOrFail($arguments['siswa_id']);

                // Simpan history kelas lama
                SiswaKelasHistory::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $this->getTahunAjaranBerjalan()],
                    [
                        'kelas'         => $data['kelas_lama'],
                        'jenis_sekolah' => $data['jenis_lama'],
                        'tahun_mulai'   => $this->getTahunMulai(),
                        'catatan'       => $data['catatan'] ?: null,
                        'created_by'    => auth()->id(),
                    ]
                );

                // Update siswa ke kelas baru
                $siswa->update([
                    'kelas'         => $data['kelas_baru'],
                    'jenis_sekolah' => $data['jenis_baru'],
                ]);

                unset($this->siswaDiKelas);

                Notification::make()
                    ->title("Kenaikan kelas disimpan")
                    ->body("{$siswa->nama}: {$data['kelas_lama']} → {$data['kelas_baru']}")
                    ->success()->send();
            });
    }

    // ─── Action: Proses kenaikan kelas semua siswa di kelas ini ───────────────

    public function prosesBatchNaikKelasAction(): Action
    {
        return Action::make('prosesBatchNaikKelas')
            ->label('Proses Kenaikan Kelas (Semua)')
            ->icon('heroicon-o-arrow-up-circle')
            ->color('warning')
            ->modalHeading("Kenaikan Kelas — {$this->filterJenisSekolah} Kelas {$this->filterKelas}")
            ->modalDescription('Semua siswa aktif di kelas ini akan dipindah ke kelas baru. Kelas lama akan tersimpan sebagai riwayat.')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Proses Kenaikan')
            ->form([
                TextInput::make('kelas_baru')
                    ->label('Kelas Baru')
                    ->placeholder('Contoh: 2A, 3B, IV')
                    ->required(),

                Select::make('jenis_baru')
                    ->label('Jenis Sekolah Baru')
                    ->options(fn () => collect($this->jenisSekolahList)->mapWithKeys(fn ($v) => [$v => $v])->toArray())
                    ->default($this->filterJenisSekolah)
                    ->required(),

                Textarea::make('catatan')
                    ->label('Catatan (opsional)')
                    ->rows(2)
                    ->placeholder("Naik kelas dari {$this->filterKelas} ke kelas baru"),
            ])
            ->action(function (array $data): void {
                if (! $this->filterJenisSekolah || ! $this->filterKelas) {
                    Notification::make()->title('Pilih jenis sekolah dan kelas terlebih dahulu')->warning()->send();
                    return;
                }

                $students = Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
                    ->where('kelas', $this->filterKelas)
                    ->where('status_aktif', true)
                    ->get();

                if ($students->isEmpty()) {
                    Notification::make()->title('Tidak ada siswa aktif di kelas ini')->warning()->send();
                    return;
                }

                $ta       = $this->getTahunAjaranBerjalan();
                $taMulai  = $this->getTahunMulai();
                $catatan  = $data['catatan'] ?: null;
                $count    = 0;

                foreach ($students as $siswa) {
                    SiswaKelasHistory::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran' => $ta],
                        [
                            'kelas'         => $siswa->kelas,
                            'jenis_sekolah' => $siswa->jenis_sekolah,
                            'tahun_mulai'   => $taMulai,
                            'catatan'       => $catatan,
                            'created_by'    => auth()->id(),
                        ]
                    );

                    $siswa->update([
                        'kelas'         => $data['kelas_baru'],
                        'jenis_sekolah' => $data['jenis_baru'],
                    ]);
                    $count++;
                }

                unset($this->siswaDiKelas);
                $this->filterKelas = $data['kelas_baru'];
                if ($this->filterJenisSekolah !== $data['jenis_baru']) {
                    $this->filterJenisSekolah = $data['jenis_baru'];
                }
                unset($this->kelasList);

                Notification::make()
                    ->title("Kenaikan kelas selesai")
                    ->body("{$count} siswa dipindah dari Kelas {$this->filterKelas} ke {$data['kelas_baru']}")
                    ->success()->send();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->prosesBatchNaikKelasAction(),
        ];
    }
}
