<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use App\Filament\Resources\TagihanResource;
use App\Models\JenisPembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListTagihans extends ListRecords
{
    protected static string $resource = TagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // ── Export CSV ────────────────────────────────────────────────────
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->modalHeading('Export Tagihan ke CSV')
                ->modalDescription('Pilih filter data yang ingin di-export. Kosongkan untuk export semua data.')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Download CSV')
                ->form([
                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
                            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
                            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
                        ])
                        ->placeholder('Semua Bulan')
                        ->native(false),

                    Select::make('tahun')
                        ->label('Tahun')
                        ->options(fn (): array => Tagihan::query()
                            ->distinct()
                            ->orderByDesc('tahun')
                            ->pluck('tahun', 'tahun')
                            ->toArray()
                        )
                        ->placeholder('Semua Tahun')
                        ->native(false),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'lunas'       => 'Lunas',
                            'belum_bayar' => 'Belum Bayar',
                        ])
                        ->placeholder('Semua Status')
                        ->native(false),
                ])
                ->action(function (array $data, \Livewire\Component $livewire): void {
                    // Kirim ke route export dengan query params filter
                    $params = http_build_query(array_filter($data, fn ($v) => filled($v)));
                    $livewire->redirect(url('/tagihan/export' . ($params ? '?' . $params : '')));
                }),

            // ── Generate Tagihan ─────────────────────────────────────────────
            Action::make('generateTagihan')
                ->label('Generate Tagihan')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->modalHeading('Generate Tagihan')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Select::make('jenis_pembayaran_id')
                        ->label('Jenis Pembayaran')
                        ->options(JenisPembayaran::pluck('nama', 'id'))
                        ->required()
                        ->placeholder('Pilih Jenis Pembayaran'),

                    Radio::make('filter_kelas')
                        ->label('Kelas')
                        ->options([
                            'semua' => 'Semua',
                            'pilih' => 'Pilih Kelas',
                        ])
                        ->default('semua')
                        ->live()
                        ->inline(false),

                    Select::make('kelas')
                        ->label('Kelas')
                        ->options(
                            Siswa::query()
                                ->whereNotNull('kelas')
                                ->distinct()
                                ->orderBy('kelas')
                                ->pluck('kelas', 'kelas')
                        )
                        ->placeholder('Pilih Kelas')
                        ->visible(fn (Get $get) => $get('filter_kelas') === 'pilih')
                        ->required(fn (Get $get) => $get('filter_kelas') === 'pilih'),

                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
                            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
                            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
                        ])
                        ->default(Carbon::now()->format('m'))
                        ->required(),

                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->default(Carbon::now()->year)
                        ->minValue(2000)
                        ->maxValue(2100)
                        ->required(),

                    TextInput::make('nominal_default')
                        ->label('Nominal Default')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(500000)
                        ->required(),

                    Checkbox::make('skip_existing')
                        ->label('Skip jika sudah ada')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $query = Siswa::query();

                    if ($data['filter_kelas'] === 'pilih' && filled($data['kelas'] ?? null)) {
                        $query->where('kelas', $data['kelas']);
                    }

                    $siswaList = $query->get();
                    $generated = 0;
                    $skipped   = 0;

                    foreach ($siswaList as $siswa) {
                        $exists = Tagihan::where('siswa_id', $siswa->id)
                            ->where('jenis_pembayaran_id', $data['jenis_pembayaran_id'])
                            ->where('bulan', $data['bulan'])
                            ->where('tahun', $data['tahun'])
                            ->exists();

                        if ($exists && $data['skip_existing']) {
                            $skipped++;
                            continue;
                        }

                        Tagihan::create([
                            'siswa_id'            => $siswa->id,
                            'jenis_pembayaran_id' => $data['jenis_pembayaran_id'],
                            'bulan'               => $data['bulan'],
                            'tahun'               => $data['tahun'],
                            'nominal_tagihan'     => $data['nominal_default'],
                            'status'              => 'belum_bayar',
                        ]);

                        $generated++;
                    }

                    Notification::make()
                        ->title('Generate Selesai')
                        ->body("Berhasil generate {$generated} tagihan." . ($skipped > 0 ? " {$skipped} dilewati karena sudah ada." : ''))
                        ->success()
                        ->send();
                }),
        ];
    }
}
