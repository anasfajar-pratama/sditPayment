<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListSiswaByKelas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    public string $jenjang = '';
    public string $kelas   = '';
    public string $tahunAjaran = '';

    public function mount(): void
    {
        $this->jenjang    = strtoupper(request()->route('jenjang') ?? '');
        $this->kelas      = strtoupper(request()->route('kelas') ?? '');
        $this->tahunAjaran = request()->query('tahun_ajaran', '');
        parent::mount();
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(
                fn (Siswa $record): string =>
                    SiswaResource::getUrl('detail', ['record' => $record])
            );
    }

    protected function getTableQuery(): Builder
    {
        $query = Siswa::query()->where('is_calon', 0);

        if ($this->tahunAjaran) {
            $query->whereHas('kelasHistories', function ($q) {
                $q->where('jenis_sekolah', $this->jenjang)
                  ->where('kelas', $this->kelas)
                  ->where('tahun_ajaran', $this->tahunAjaran);
            });
        } else {
            $query->whereHas('kelasSaatIni', function ($q) {
                $q->where('jenis_sekolah', $this->jenjang)
                  ->where('kelas', $this->kelas);
            });
        }

        return $query;
    }

    public function getBreadcrumbs(): array
    {
        $jenjangUrl = SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang]);
        if ($this->tahunAjaran) {
            $jenjangUrl .= '?tahun_ajaran=' . urlencode($this->tahunAjaran);
        }

        $kelasLabel = $this->tahunAjaran
            ? "Kelas {$this->kelas} ({$this->tahunAjaran})"
            : "Kelas {$this->kelas}";

        return [
            $jenjangUrl => 'Siswa ' . $this->jenjang,
            '#' => $kelasLabel,
        ];
    }

    public function getTitle(): string
    {
        if ($this->tahunAjaran) {
            return "Kelas {$this->kelas} — {$this->jenjang} ({$this->tahunAjaran})";
        }
        return 'Kelas ' . $this->kelas . ' — ' . $this->jenjang;
    }

    protected function getHeaderActions(): array
    {
        $kembaliUrl = SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang]);
        if ($this->tahunAjaran) {
            $kembaliUrl .= '?tahun_ajaran=' . urlencode($this->tahunAjaran);
        }

        return [
            Action::make('kembali')
                ->label('← Kembali ke ' . $this->jenjang)
                ->color('gray')
                ->url($kembaliUrl),
            Action::make('tambah_siswa')
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus')
                ->url(SiswaResource::getUrl('create')),
        ];
    }
}
