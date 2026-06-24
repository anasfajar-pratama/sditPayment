<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/ListCalonSiswa.php
// Perubahan: nilai calon_jenis pakai lowercase sesuai DB ('sd','smp','dta','paud')
//            + tambah 'dta' yang tadinya tidak ada di enum
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCalonSiswa extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getTableQuery(): Builder
    {
        return Siswa::query()->where('is_calon', 1);
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
            CreateAction::make()->label('Tambah Calon Siswa'),
        ];
    }

    // ─── Tab per jenjang — nilai DB lowercase ─────────────────────────────────

    public function getTabs(): array
    {
        // key  => [label display, nilai di DB, warna badge]
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
                $jenjangDb = $cfg['db']; // capture untuk closure
                $tab->modifyQueryUsing(
                    fn (Builder $query) => $query->where('calon_jenis', $jenjangDb)
                );
            }

            $tabs[$key] = $tab;
        }

        return $tabs;
    }
}
