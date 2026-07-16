<?php

namespace App\Filament\Pages;

use App\Models\MasterBiaya;
use App\Models\MasterRekeningTujuan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PengaturanKeuangan extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon    = 'heroicon-o-cog';
    protected static ?string $navigationLabel   = 'Biaya & Rekening';
    protected static ?string $navigationGroup   = 'Master Data';
    protected static ?int    $navigationSort    = 100;

    protected static string $view = 'filament.pages.pengaturan-keuangan';

    public ?array $data = [];

    public function mount(): void
    {
        $tahun = now()->year;
        $biaya = MasterBiaya::firstOrNew(['tahun' => $tahun]);
        $this->form->fill([
            'tahun' => $tahun,
            'nominal_spp' => (float) ($biaya->nominal_spp ?? 0),
            'nominal_daftar_ulang' => (float) ($biaya->nominal_daftar_ulang ?? 0),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tahun')
                    ->label('Tahun Ajaran')
                    ->options(fn () => collect(range(now()->year, now()->year - 2))
                        ->mapWithKeys(fn ($y) => [$y => $y . '/' . ($y + 1)]))
                    ->live()
                    ->afterStateUpdated(function (string $state): void {
                        $biaya = MasterBiaya::firstOrNew(['tahun' => $state]);
                        $this->data['nominal_spp'] = (float) ($biaya->nominal_spp ?? 0);
                        $this->data['nominal_daftar_ulang'] = (float) ($biaya->nominal_daftar_ulang ?? 0);
                    })
                    ->required(),
                TextInput::make('nominal_spp')
                    ->label('Nominal SPP (Rp)')
                    ->numeric()->prefix('Rp')->required(),
                TextInput::make('nominal_daftar_ulang')
                    ->label('Nominal Daftar Ulang (Rp)')
                    ->numeric()->prefix('Rp')->required(),
            ])
            ->statePath('data')
            ->model(MasterBiaya::class);
    }

    public function saveBiaya(): void
    {
        $data = $this->form->getState();
        MasterBiaya::updateOrCreate(
            ['tahun' => $data['tahun']],
            $data
        );
        Notification::make()->title('Biaya berhasil disimpan')->success()->send();
    }

    public function table(Table $table): Table
    {
        $rekForm = fn () => [
            TextInput::make('label')->label('Label')->required(),
            TextInput::make('bank')->label('Bank')->required(),
            TextInput::make('no_rekening')->label('No. Rekening')->required(),
            TextInput::make('atas_nama')->label('Atas Nama')->required(),
            Toggle::make('is_cash')->label('Cash (Tunai)')->default(false),
            TextInput::make('urutan')->label('Urutan')->numeric()->default(0),
        ];

        return $table
            ->query(MasterRekeningTujuan::query())
            ->columns([
                TextColumn::make('label')->label('Label')->searchable(),
                TextColumn::make('bank')->label('Bank'),
                TextColumn::make('no_rekening')->label('No. Rekening'),
                TextColumn::make('atas_nama')->label('Atas Nama'),
                IconColumn::make('is_cash')->label('Cash')->boolean(),
                TextColumn::make('urutan')->label('Urutan')->sortable(),
            ])
            ->headerActions([
                CreateAction::make('tambahRekening')
                    ->label('Tambah Rekening')
                    ->model(MasterRekeningTujuan::class)
                    ->form($rekForm)
                    ->modalHeading('Tambah Rekening Tujuan')
                    ->modalWidth('md'),
            ])
            ->actions([
                EditAction::make('editRekening')
                    ->modalHeading('Edit Rekening Tujuan')
                    ->modalWidth('md')
                    ->form($rekForm),
                DeleteAction::make('hapusRekening'),
            ]);
    }
}
