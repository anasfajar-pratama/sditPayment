<?php
// File: app/Filament/Resources/TokenListrikResource/Pages/DetailTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Filament\Resources\TokenListrikResource;
use App\Models\TokenListrik;
use App\Models\TokenPembelian;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class DetailTokenListrik extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TokenListrikResource::class;
    protected static string $view     = 'filament.resources.token-listrik-resource.pages.detail-token-listrik';

    // ── Record ────────────────────────────────────────────────────────────────
    public TokenListrik $token;

    // ── State form pembelian ──────────────────────────────────────────────────
    public array $pembelianData = [];

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(int|string $record): void
    {
        $this->token = TokenListrik::findOrFail($record);

        $this->pembelianForm->fill([
            'tanggal'      => now()->toDateString(),
            'nominal'      => null,
            'nomor_token'  => null,
            'kwh'          => null,
            'note'         => null,
        ]);
    }

    // ─── Daftar forms (Filament v4) ───────────────────────────────────────────

    protected function getForms(): array
    {
        return ['pembelianForm'];
    }

    // ─── Form: Input Pembelian Token ──────────────────────────────────────────

    public function pembelianForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Pembelian')
                        ->required()
                        ->default(now()->toDateString())
                        ->native(false),

                    TextInput::make('nominal')
                        ->label('Nominal (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(1)
                        ->required()
                        ->placeholder('Contoh: 100000'),

                    TextInput::make('nomor_token')
                        ->label('Nomor Token (pada struk)')
                        ->required()
                        ->maxLength(30)
                        ->placeholder('Contoh: 1234-5678-9012-3456-7890')
                        ->columnSpanFull(),

                    TextInput::make('kwh')
                        ->label('Jumlah KWH')
                        ->numeric()
                        ->required()
                        ->suffix('kWh')
                        ->placeholder('Contoh: 50.5'),

                    Textarea::make('note')
                        ->label('Catatan')
                        ->rows(2)
                        ->placeholder('Contoh: Pembelian rutin bulanan'),
                ]),
            ])
            ->statePath('pembelianData');
    }

    // ─── Action: Simpan Pembelian ─────────────────────────────────────────────

    public function simpanPembelian(): void
    {
        $data    = $this->pembelianForm->getState();
        $tanggal = \Illuminate\Support\Carbon::parse($data['tanggal']);

        TokenPembelian::create([
            'token_listrik_id' => $this->token->id,
            'tanggal'          => $tanggal->toDateString(),
            'nominal'          => $data['nominal'],
            'nomor_token'      => $data['nomor_token'] ?? null,
            'kwh'              => $data['kwh'] ?? null,
            'note'             => $data['note'] ?? null,
            'bulan'            => $tanggal->format('m'),
            'tahun'            => $tanggal->format('Y'),
            'created_by'       => auth()->id(),
        ]);

        $this->token->refresh();

        $this->pembelianForm->fill([
            'tanggal'     => now()->toDateString(),
            'nominal'     => null,
            'nomor_token' => null,
            'kwh'         => null,
            'note'        => null,
        ]);

        Notification::make()
            ->title('Pembelian token disimpan & dicatat di Kas Harian')
            ->success()
            ->send();
    }

    // ─── History pembelian ────────────────────────────────────────────────────

    public function getHistoryPembelianProperty(): Collection
    {
        return $this->token
            ->pembelians()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    // ─── Header actions ───────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_token')
                ->label('Edit Data Token')
                ->icon('heroicon-o-pencil-square')
                ->url(TokenListrikResource::getUrl('edit', ['record' => $this->token])),

            Action::make('kembali')
                ->label('← Daftar Token Listrik')
                ->color('gray')
                ->url(TokenListrikResource::getUrl('index')),
        ];
    }

    // ─── Title & breadcrumb ───────────────────────────────────────────────────

    public function getTitle(): string
    {
        return $this->token->nama_ruangan;
    }

    public function getBreadcrumbs(): array
    {
        return [
            TokenListrikResource::getUrl('index') => 'Token Listrik',
            '#'                                   => $this->token->nama_ruangan,
        ];
    }
}
