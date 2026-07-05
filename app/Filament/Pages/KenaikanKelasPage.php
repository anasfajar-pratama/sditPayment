<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
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

    public string $filterJenisSekolah = '';
    public string $targetTahunAjaran  = '';

    public array $kelasMapping = [];

    public function mount(): void
    {
        $this->targetTahunAjaran = $this->defaultTargetTahunAjaran();
    }

    public function updatedFilterJenisSekolah(): void
    {
        $this->initKelasMapping();
    }

    protected function initKelasMapping(): void
    {
        $this->kelasMapping = [];

        if (! $this->filterJenisSekolah) return;

        $classes = Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('status_aktif', true)
            ->whereNotNull('kelas')
            ->distinct()
            ->orderByRaw('LENGTH(kelas), kelas')
            ->pluck('kelas');

        foreach ($classes as $kelas) {
            $this->kelasMapping[$kelas] = $this->suggestTargetKelas($kelas, $this->filterJenisSekolah);
        }
    }

    #[Computed]
    public function jenisSekolahList(): array
    {
        return Siswa::where('status_aktif', true)
            ->whereNotNull('jenis_sekolah')
            ->distinct()->orderBy('jenis_sekolah')
            ->pluck('jenis_sekolah')
            ->toArray();
    }

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

    public function defaultTargetTahunAjaran(): string
    {
        $ta    = $this->getTahunAjaranBerjalan();
        $parts = explode('/', $ta);
        $start = (int) $parts[0] + 1;
        return "{$start}/" . ($start + 1);
    }

    public function getMaxLevel(string $jenisSekolah): ?int
    {
        return match ($jenisSekolah) {
            'SD'   => 6,
            'SMP'  => 3,
            'DTA'  => 4,
            default => null,
        };
    }

    public function getGraduationLabel(string $jenisSekolah): string
    {
        return match ($jenisSekolah) {
            'SD'   => 'Lulus SD',
            'SMP'  => 'Lulus SMP',
            default => '',
        };
    }

    public function isGraduationValue(string $value): bool
    {
        return in_array($value, ['Lulus SD', 'Lulus SMP']);
    }

    public function suggestTargetKelas(string $kelas, string $jenisSekolah): string
    {
        if ($jenisSekolah === 'PAUD') {
            return match ($kelas) {
                'Kelompok Bermain' => 'TK-A',
                'TK-A'             => 'TK-B',
                default            => '',
            };
        }

        if (preg_match('/^(\d+)([A-Z]?)$/', $kelas, $m)) {
            $num    = (int) $m[1];
            $letter = $m[2];
            $max    = $this->getMaxLevel($jenisSekolah);

            if ($max !== null && $num >= $max) {
                return $this->getGraduationLabel($jenisSekolah);
            }

            return ($num + 1) . $letter;
        }

        return '';
    }

    #[Computed]
    public function kelasData(): array
    {
        if (! $this->filterJenisSekolah) return [];

        $rows = Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('status_aktif', true)
            ->whereNotNull('kelas')
            ->selectRaw('kelas, COUNT(*) as jumlah')
            ->groupBy('kelas')
            ->orderByRaw('LENGTH(kelas), kelas')
            ->get();

        return $rows->map(fn ($row) => [
            'kelas'     => $row->kelas,
            'jumlah'    => $row->jumlah,
            'target'    => $this->kelasMapping[$row->kelas] ?? '',
            'status'    => ! empty($this->kelasMapping[$row->kelas]) ? 'siap' : 'perlu_diisi',
        ])->toArray();
    }

    public function getTargetOptions(): array
    {
        $jenis   = $this->filterJenisSekolah;
        $options = \App\Filament\Resources\SiswaResource::getKelasOptions($jenis);

        $lulus = $this->getGraduationLabel($jenis);
        if ($lulus) {
            $options[$lulus] = $lulus;
        }

        ksort($options);

        return ['' => '— Pilih —'] + $options;
    }

    public function prosesKenaikanAction(): Action
    {
        return Action::make('prosesKenaikan')
            ->label('Jalankan Simulasi')
            ->icon('heroicon-o-play')
            ->color('success')
            ->modalHeading('Simulasi Kenaikan Kelas')
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Proses Semua Kenaikan')
            ->modalSubmitAction('proses')
            ->form([
                Placeholder::make('preview')
                    ->label('')
                    ->content(fn () => view('filament.pages.kenaikan-kelas-preview', [
                        'kelasData' => $this->kelasData,
                        'targetTa'  => $this->targetTahunAjaran,
                        'taSumber'  => $this->getTahunAjaranBerjalan(),
                    ])),
            ])
            ->action(function (): void {
                $this->prosesSemuaKenaikan();
            });
    }

    protected function prosesSemuaKenaikan(): void
    {
        if (! $this->filterJenisSekolah || ! $this->targetTahunAjaran) {
            Notification::make()->title('Pilih jenis sekolah dan tahun ajaran target terlebih dahulu')->warning()->send();
            return;
        }

        $kelasData = $this->kelasData;
        $taMulai   = $this->getTahunMulai();
        $count     = 0;

        foreach ($kelasData as $item) {
            $targetKelas = $this->kelasMapping[$item['kelas']] ?? '';
            if (! $targetKelas) continue;

            $students = Siswa::where('jenis_sekolah', $this->filterJenisSekolah)
                ->where('kelas', $item['kelas'])
                ->where('status_aktif', true)
                ->get();

            foreach ($students as $siswa) {
                SiswaKelasHistory::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $this->targetTahunAjaran],
                    [
                        'kelas'         => $siswa->kelas,
                        'jenis_sekolah' => $siswa->jenis_sekolah,
                        'tahun_mulai'   => $taMulai,
                        'catatan'       => "Kenaikan kelas T.A. {$this->targetTahunAjaran}",
                        'created_by'    => auth()->id(),
                    ]
                );

                if ($this->isGraduationValue($targetKelas)) {
                    $siswa->update(['status_aktif' => false]);
                } else {
                    $siswa->update([
                        'kelas'        => $targetKelas,
                        'tahun_ajaran' => $this->targetTahunAjaran,
                    ]);
                }

                $count++;
            }
        }

        Notification::make()
            ->title('Kenaikan kelas selesai diproses')
            ->body("{$count} siswa diproses ke tahun ajaran {$this->targetTahunAjaran}")
            ->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->prosesKenaikanAction(),
        ];
    }
}
