<?php
// File: app/Filament/Resources/DonaturResource/Pages/DetailDonatur.php

namespace App\Filament\Resources\DonaturResource\Pages;

use App\Filament\Resources\DonaturResource;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Filament\Traits\ConvertsToWebp;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class DetailDonatur extends Page implements HasForms
{
    use InteractsWithForms, ConvertsToWebp;

    protected static string $resource = DonaturResource::class;
    protected static string $view     = 'filament.resources.donatur-resource.pages.detail-donatur';

    // ── Record ────────────────────────────────────────────────────────────────
    public Donatur $donatur;

    // ── State form donasi ─────────────────────────────────────────────────────
    public array $donasiData = [];

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(int|string $record): void
    {
        $this->donatur = Donatur::findOrFail($record);

        $this->donasiForm->fill([
            'tanggal' => now()->toDateString(),
            'nominal' => null,
            'note'    => null,
        ]);
    }

    // ─── Daftar forms (Filament v4) ───────────────────────────────────────────

    protected function getForms(): array
    {
        return ['donasiForm'];
    }

    // ─── Form: Input Donasi ───────────────────────────────────────────────────

    public function donasiForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Donasi')
                        ->required()
                        ->default(now()->toDateString())
                        ->native(false),

                    TextInput::make('nominal')
                        ->label('Nominal (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(1)
                        ->required()
                        ->placeholder('Contoh: 500000'),

                    FileUpload::make('bukti_transfer')
                        ->label('Bukti Transfer')
                        ->image()
                        ->directory('bukti-transfer')
                        ->maxSize(2048)
                        ->columnSpanFull(),

                    Textarea::make('note')
                        ->label('Catatan / Keterangan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Opsional'),
                ]),
            ])
            ->statePath('donasiData');
    }

    // ─── Action: Simpan Donasi ────────────────────────────────────────────────

    public function simpanDonasi(): void
    {
        $data    = $this->donasiForm->getState();
        $tanggal = \Illuminate\Support\Carbon::parse($data['tanggal']);

        Donasi::create([
            'donatur_id'     => $this->donatur->id,
            'tanggal'        => $tanggal->toDateString(),
            'nominal'        => $data['nominal'],
            'bukti_transfer' => $this->convertToWebp($data['bukti_transfer'] ?? null),
            'note'           => $data['note'] ?? null,
            'bulan'          => $tanggal->format('m'),
            'tahun'          => $tanggal->format('Y'),
            'created_by'     => auth()->id(),
        ]);

        $this->donatur->refresh();

        $this->donasiForm->fill([
            'tanggal'        => now()->toDateString(),
            'nominal'        => null,
            'bukti_transfer' => null,
            'note'           => null,
        ]);

        Notification::make()
            ->title('Donasi berhasil disimpan & dicatat di Kas Harian')
            ->success()
            ->send();
    }

    // ─── History donasi ───────────────────────────────────────────────────────

    public function getHistoryDonasiProperty(): Collection
    {
        return $this->donatur
            ->donasis()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    // ─── Header actions ───────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_donatur')
                ->label('Edit Data Orang Tua Asuh')
                ->icon('heroicon-o-pencil-square')
                ->url(DonaturResource::getUrl('edit', ['record' => $this->donatur])),

            Action::make('kembali')
                ->label('← Daftar Orang Tua Asuh')
                ->color('gray')
                ->url(DonaturResource::getUrl('index')),
        ];
    }

    // ─── Title & breadcrumb ───────────────────────────────────────────────────

    public function getTitle(): string
    {
        return $this->donatur->nama;
    }

    public function getBreadcrumbs(): array
    {
        return [
            DonaturResource::getUrl('index') => 'Orang Tua Asuh',
            '#'                              => $this->donatur->nama,
        ];
    }
}
