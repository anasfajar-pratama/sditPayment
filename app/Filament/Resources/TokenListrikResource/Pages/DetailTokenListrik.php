<?php
// File: app/Filament/Resources/TokenListrikResource/Pages/DetailTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Filament\Traits\ConvertsToWebp;
use App\Filament\Resources\TokenListrikResource;
use App\Models\KasHarian;
use App\Models\KasHarianLog;
use App\Models\TokenListrik;
use App\Models\TokenPembelian;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DetailTokenListrik extends Page implements HasForms
{
    use InteractsWithForms, ConvertsToWebp;

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

                    FileUpload::make('bukti')
                        ->label('Bukti Transaksi (foto struk)')
                        ->image()
                        ->directory('bukti-token')
                        ->maxSize(2048)
                        ->columnSpanFull(),

                    TextInput::make('nomor_token')
                        ->label('Nomor Token (pada struk)')
                        // ->required()
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
            'bukti'            => $this->convertToWebp($data['bukti'] ?? null),
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
            'bukti'       => null,
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

    // ─── Field sandi (verifikasi hapus) ───────────────────────────────────────

    private function sandiField(): TextInput
    {
        return TextInput::make('sandi')
            ->label('Konfirmasi Sandi')
            ->password()
            ->autocomplete('new-password')
            ->required()
            ->helperText('Masukkan sandi akun Anda untuk mengonfirmasi penghapusan');
    }

    // ─── Action: Hapus History Pembelian Token ────────────────────────────────

    public function hapusPembelianAction(): Action
    {
        return Action::make('hapusPembelian')
            ->modalHeading('Hapus History Pembelian Token')
            ->modalDescription('Tindakan ini tidak dapat dibatalkan. Baris Kas Harian terkait juga akan ikut terhapus.')
            ->modalSubmitActionLabel('Ya, Hapus')
            ->color('danger')
            ->fillForm(function (array $arguments): array {
                $pembelian = TokenPembelian::findOrFail($arguments['id']);

                return [
                    'preview_tanggal' => $pembelian->tanggal?->format('d M Y') ?? '—',
                    'preview_nominal' => 'Rp ' . number_format((int) $pembelian->nominal, 0, ',', '.'),
                    'preview_token'   => $pembelian->nomor_token ?: '—',
                ];
            })
            ->form([
                Placeholder::make('preview_tanggal')
                    ->label('Tanggal')
                    ->content(fn (Get $get) => $get('preview_tanggal')),

                Placeholder::make('preview_nominal')
                    ->label('Nominal')
                    ->content(fn (Get $get) => $get('preview_nominal')),

                Placeholder::make('preview_token')
                    ->label('Nomor Token')
                    ->content(fn (Get $get) => $get('preview_token')),

                $this->sandiField(),
            ])
            ->action(function (array $data, array $arguments): void {
                if (! Hash::check($data['sandi'], auth()->user()->password)) {
                    Notification::make()->title('Sandi salah')->danger()->send();
                    $this->halt();

                    return;
                }

                $pembelian = TokenPembelian::findOrFail($arguments['id']);

                $kasHarian = KasHarian::where('source', 'token')
                    ->where('source_id', $pembelian->id)
                    ->first();

                if ($kasHarian) {
                    KasHarianLog::catat(
                        aksi: 'hapus',
                        kasHarianId: $kasHarian->id,
                        sebelum: $kasHarian->toArray(),
                        sesudah: null,
                        keterangan: "Hapus token: {$kasHarian->uraian}",
                    );
                }

                // Menghapus pembelian otomatis menghapus baris Kas Harian terkait
                // melalui event TokenPembelian::deleted().
                $pembelian->delete();

                unset($this->historyPembelian);

                Notification::make()
                    ->title('History pembelian token dihapus')
                    ->body('Baris Kas Harian terkait juga telah dihapus.')
                    ->success()
                    ->send();
            });
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
