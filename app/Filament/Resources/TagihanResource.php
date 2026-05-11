<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Http\Controllers\TagihanPublicController;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $pluralLabel = 'Daftar Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ─── Helper: enkripsi ID untuk URL publik ─────────────────────────────────

    public static function encryptTagihanId(int $id): string
    {
        // base64url: ganti +/ → -_ dan strip padding = agar URL bersih
        return TagihanPublicController::encryptId($id);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisPembayaran.nama')
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('bulan')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
                        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
                        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nominal_tagihan')
                    ->money('IDR')
                    ->label('Nominal'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger'  => 'belum_bayar',
                        'success' => 'lunas',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()

            // ── Filter di atas tabel ──────────────────────────────────────────
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
                        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
                        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
                    ])
                    ->placeholder('Semua Bulan')
                    ->native(false),

                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(fn (): array => Tagihan::query()
                        ->distinct()
                        ->orderByDesc('tahun')
                        ->pluck('tahun', 'tahun')
                        ->toArray()
                    )
                    ->placeholder('Semua Tahun')
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lunas'       => 'Lunas',
                        'belum_bayar' => 'Belum Bayar',
                    ])
                    ->placeholder('Semua Status')
                    ->native(false),
            ])

            ->headerActions([])
            ->actions([

                // ── Cetak: icon saja, tooltip menggantikan label ──────────────
                Action::make('cetak')
                    ->tooltip(fn (Tagihan $record) => $record->status === 'lunas'
                        ? 'Cetak Kuitansi'
                        : 'Cetak Tagihan')
                    ->icon('heroicon-o-printer')
                    ->color(fn (Tagihan $record) => $record->status === 'lunas' ? 'success' : 'warning')
                    ->iconButton()
                    ->url(function (Tagihan $record) {
                        if ($record->status === 'lunas') {
                            $pembayaran = $record->pembayaran;
                            if ($pembayaran) {
                                return "/kuitansi/{$pembayaran->id}";
                            }
                        }
                        return route('tagihan.pdf', $record);
                    })
                    ->openUrlInNewTab(),

                // ── Salin link: icon saja, clipboard via Alpine.js ────────────
                Action::make('salin_link')
                    ->tooltip('Salin Link Tagihan (tanpa login)')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->iconButton()
                    // Salin ke clipboard saat klik (harus client-side / user gesture)
                    ->extraAttributes(fn (Tagihan $record): array => [
                        'x-data' => json_encode([
                            'shareUrl' => url('/tagihan/share/' . static::encryptTagihanId($record->id)),
                        ]),
                        'x-on:click' => 'navigator.clipboard.writeText(shareUrl)',
                    ])
                    ->action(fn () => Notification::make()
                        ->title('Link berhasil disalin!')
                        ->body('Bagikan link ini ke wali murid. Dapat dibuka tanpa login.')
                        ->success()
                        ->send()),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }
}
