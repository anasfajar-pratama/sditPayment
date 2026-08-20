<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Filament\Actions\Action;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
    protected static ?int    $navigationSort  = 70;

    protected static string $view = 'filament.pages.kenaikan-kelas-page';

    public string $filterJenisSekolah = '';
    public string $targetTahunAjaran  = '';

    public array $kelasMapping = [];

    public bool $canUndo = false;
    public ?array $lastRunData = null;

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

        $classes = SiswaKelasHistory::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('tahun_ajaran', $this->getTahunAjaranBerjalan())
            ->where('is_current', true)
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
        return SiswaKelasHistory::where('tahun_ajaran', $this->getTahunAjaranBerjalan())
            ->where('is_current', true)
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
                'A'      => 'B',
                default  => '',
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

        $rows = SiswaKelasHistory::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('tahun_ajaran', $this->getTahunAjaranBerjalan())
            ->where('is_current', true)
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

        return ['' => '— Pilih —', 'TINGGAL' => '— Tinggal Kelas —'] + $options;
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

        $siswaIds = SiswaKelasHistory::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('tahun_ajaran', $this->getTahunAjaranBerjalan())
            ->where('is_current', true)
            ->pluck('siswa_id');

        $duplicates = SiswaKelasHistory::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran', $this->targetTahunAjaran)
            ->with('siswa:id,nama')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $names = $duplicates->take(3)->pluck('siswa.nama')->implode(', ');
            $more  = $duplicates->count() > 3 ? ' dan ' . ($duplicates->count() - 3) . ' lainnya' : '';
            Notification::make()
                ->title($duplicates->count() . ' siswa sudah memiliki data untuk ' . $this->targetTahunAjaran)
                ->body("Contoh: {$names}{$more}. Record yang sudah ada akan diperbarui.")
                ->warning()->send();
        }

        $taMulai       = $this->getTahunMulai();
        $count         = 0;
        $newIds        = [];
        $graduatedIds  = [];
        $skipped       = [];

        try {
            DB::transaction(function () use ($taMulai, &$count, &$newIds, &$graduatedIds, &$skipped) {
                $kelasData = $this->kelasData;

                foreach ($kelasData as $item) {
                    $targetKelas = $this->kelasMapping[$item['kelas']] ?? '';
                    if (! $targetKelas) {
                        $skipped[] = $item['kelas'];
                        continue;
                    }

                    $histories = SiswaKelasHistory::where('jenis_sekolah', $this->filterJenisSekolah)
                        ->where('tahun_ajaran', $this->getTahunAjaranBerjalan())
                        ->where('kelas', $item['kelas'])
                        ->where('is_current', true)
                        ->with('siswa')
                        ->get();

                    foreach ($histories as $history) {
                        $siswa = $history->siswa;
                        if (! $siswa || ! $siswa->status_aktif) continue;

                        $history->update(['is_current' => false]);

                        if ($this->isGraduationValue($targetKelas)) {
                            $siswa->update(['status_aktif' => false]);
                            $graduatedIds[] = $siswa->id;

                            $new = SiswaKelasHistory::updateOrCreate(
                                ['siswa_id' => $siswa->id, 'tahun_ajaran' => $this->targetTahunAjaran],
                                [
                                    'kelas'         => $targetKelas,
                                    'tingkat'       => null,
                                    'jenis_sekolah' => $this->filterJenisSekolah,
                                    'tahun_mulai'   => $taMulai,
                                    'mutasi'        => 'lulus',
                                    'is_current'    => false,
                                    'created_by'    => auth()->id(),
                                    'catatan'       => "Lulus T.A. {$this->targetTahunAjaran}",
                                ]
                            );
                            $newIds[] = $new->id;
                        } elseif ($targetKelas === 'TINGGAL') {
                            $new = SiswaKelasHistory::updateOrCreate(
                                ['siswa_id' => $siswa->id, 'tahun_ajaran' => $this->targetTahunAjaran],
                                [
                                    'kelas'         => $history->kelas,
                                    'tingkat'       => $history->tingkat,
                                    'jenis_sekolah' => $this->filterJenisSekolah,
                                    'tahun_mulai'   => $taMulai,
                                    'mutasi'        => 'tinggal',
                                    'is_current'    => true,
                                    'created_by'    => auth()->id(),
                                    'catatan'       => "Tinggal kelas T.A. {$this->targetTahunAjaran}",
                                ]
                            );
                            $newIds[] = $new->id;
                        } else {
                            $tingkatBaru = (int) filter_var($targetKelas, FILTER_SANITIZE_NUMBER_INT);

                            $new = SiswaKelasHistory::updateOrCreate(
                                ['siswa_id' => $siswa->id, 'tahun_ajaran' => $this->targetTahunAjaran],
                                [
                                    'kelas'         => $targetKelas,
                                    'tingkat'       => $tingkatBaru,
                                    'jenis_sekolah' => $this->filterJenisSekolah,
                                    'tahun_mulai'   => $taMulai,
                                    'mutasi'        => 'naik',
                                    'is_current'    => true,
                                    'created_by'    => auth()->id(),
                                    'catatan'       => "Kenaikan kelas T.A. {$this->targetTahunAjaran}",
                                ]
                            );
                            $newIds[] = $new->id;
                        }

                        $count++;
                    }
                }
            });

            $this->canUndo = true;
            $this->lastRunData = [
                'new_history_ids' => $newIds,
                'graduated_ids'   => $graduatedIds,
                'target_ta'       => $this->targetTahunAjaran,
                'jenis_sekolah'   => $this->filterJenisSekolah,
            ];

            $this->dispatch('$refresh');

            $body = "{$count} siswa diproses ke tahun ajaran {$this->targetTahunAjaran}. Anda bisa undo kenaikan ini.";
            if ($skipped) {
                $body .= ' Kelas dilewati (target tidak diisi): ' . implode(', ', $skipped) . '.';
            }

            Notification::make()
                ->title('Kenaikan kelas selesai diproses')
                ->body($body)
                ->success()->send();
        } catch (QueryException $e) {
            Notification::make()
                ->title('Gagal memproses kenaikan kelas')
                ->body("Terjadi kesalahan database: {$e->getMessage()}")
                ->danger()->send();
        }
    }

    public function undoKenaikan(): void
    {
        if (! $this->canUndo || ! $this->lastRunData) {
            Notification::make()->title('Tidak ada data kenaikan yang bisa di-undo')->warning()->send();
            return;
        }

        $count = 0;

        foreach ($this->lastRunData['new_history_ids'] as $id) {
            $newRecord = SiswaKelasHistory::find($id);
            if (! $newRecord) continue;

            $siswaId = $newRecord->siswa_id;

            $previous = SiswaKelasHistory::where('siswa_id', $siswaId)
                ->where('id', '!=', $id)
                ->orderByDesc('tahun_mulai')
                ->first();

            if ($previous) {
                $previous->update(['is_current' => true]);
            }

            $newRecord->delete();
            $count++;
        }

        if (! empty($this->lastRunData['graduated_ids'])) {
            Siswa::whereIn('id', $this->lastRunData['graduated_ids'])->update(['status_aktif' => true]);
        }

        $this->canUndo = false;
        $this->lastRunData = null;

        $this->dispatch('$refresh');

        Notification::make()
            ->title('Kenaikan kelas berhasil di-undo')
            ->body("{$count} siswa dikembalikan ke keadaan sebelumnya")
            ->success()->send();
    }

    protected function getHeaderActions(): array
    {
        $actions = [$this->prosesKenaikanAction()];

        if ($this->canUndo) {
            $actions[] = Action::make('undoKenaikan')
                ->label('↩ Undo Kenaikan Terakhir')
                ->color('warning')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Undo Kenaikan Kelas')
                ->modalDescription('Tindakan ini akan membatalkan kenaikan kelas terakhir. History baru akan dihapus dan siswa dikembalikan ke kelas sebelumnya.')
                ->modalSubmitActionLabel('Ya, Undo')
                ->action(fn () => $this->undoKenaikan());
        }

        return $actions;
    }
}
