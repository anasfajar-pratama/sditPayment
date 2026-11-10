<?php

// File: app/Filament/Resources/TokenListrikResource/Pages/ListTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Exports\TokenListrikExport;
use App\Filament\Resources\TokenListrikResource;
use App\Models\TokenListrik;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListTokenListrik extends ListRecords
{
    protected static string $resource = TokenListrikResource::class;

    public function getTitle(): string
    {
        return 'Daftar Token Listrik';
    }

    protected function getHeaderActions(): array
    {
        $namaBulan = [
            1 => 'Januari',   2 => 'Februari', 3 => 'Maret',
            4 => 'April',     5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',      8 => 'Agustus',   9 => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];

        $tahunTersedia = array_combine(
            range(now()->year - 3, now()->year),
            range(now()->year - 3, now()->year),
        );

        $bulanTerpilih = (int) ($this->tableFilters['periode']['bulan'] ?? now()->month);
        $tahunTerpilih = (int) ($this->tableFilters['periode']['tahun'] ?? now()->year);

        return [
            CreateAction::make()->label('Tambah Token Listrik'),

            Action::make('cetak_excel')
                ->label('Cetak Excel')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Laporan Token Listrik')
                ->modalDescription('Pilih jenis laporan, bulan, dan tahun. Laporan akan diunduh dalam format Excel.')
                ->modalSubmitActionLabel('Download Excel')
                ->modalWidth('md')
                ->form([
                    Radio::make('jenis')
                        ->label('Jenis Cetak')
                        ->options([
                            'per_ruangan' => 'Per ruangan - per bulan',
                            'semua' => 'Seluruh ruangan - per bulan',
                        ])
                        ->default('per_ruangan')
                        ->required()
                        ->live(),

                    Select::make('token_listrik_id')
                        ->label('Ruangan')
                        ->options(fn (): array => TokenListrik::query()
                            ->orderBy('nama_ruangan')
                            ->pluck('nama_ruangan', 'id')
                            ->toArray()
                        )
                        ->searchable()
                        ->required(fn (Get $get): bool => $get('jenis') === 'per_ruangan')
                        ->visible(fn (Get $get): bool => $get('jenis') === 'per_ruangan'),

                    Grid::make(2)->schema([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options($namaBulan)
                            ->default($bulanTerpilih)
                            ->required()
                            ->native(false),

                        Select::make('tahun')
                            ->label('Tahun')
                            ->options($tahunTersedia)
                            ->default($tahunTerpilih)
                            ->required()
                            ->native(false),
                    ]),
                ])
                ->action(function (array $data) use ($namaBulan) {
                    $tokenId = $data['jenis'] === 'per_ruangan'
                        ? (int) $data['token_listrik_id']
                        : null;

                    $namaRuangan = $tokenId ? TokenListrik::find($tokenId)?->nama_ruangan : null;
                    $slug = $namaRuangan ? Str::slug($namaRuangan) : 'seluruh-ruangan';

                    $namaFile = 'laporan-token-listrik-'.$slug.'-'
                        .strtolower($namaBulan[(int) $data['bulan']] ?? $data['bulan']).'-'
                        .$data['tahun'].'.xlsx';

                    return Excel::download(
                        new TokenListrikExport(
                            mode: $data['jenis'],
                            bulan: (int) $data['bulan'],
                            tahun: (int) $data['tahun'],
                            tokenListrikId: $tokenId,
                        ),
                        $namaFile,
                    );
                }),
        ];
    }
}
