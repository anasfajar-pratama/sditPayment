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
    protected static ?int    $navigationSort  = 31;
    protected static ?string $pluralLabel = 'Daftar Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ─── Helper: enkripsi ID untuk URL publik ─────────────────────────────────

    public static function encryptTagihanId(int $id): string
    {
        return TagihanPublicController::encryptId($id);
    }

    // ─── Helper: build public share URL ──────────────────────────────────────

    public static function publicShareUrl(Tagihan $record): string
    {
        return url('/tagihan/share/' . static::encryptTagihanId($record->id));
    }

    // ─── Helper: nama bulan dari angka ───────────────────────────────────────

    protected static function namaBulan(?string $bulan): string
    {
        return match ($bulan) {
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
            default => $bulan ?? '—',
        };
    }

    // ─── Helper: build URL WhatsApp dengan pesan otomatis ────────────────────

    public static function whatsappUrl(Tagihan $record): string
    {
        $nama    = $record->siswa?->nama ?? 'Siswa';
        $nominal = 'Rp ' . number_format($record->nominal_tagihan, 0, ',', '.');
        $status  = $record->status === 'lunas' ? 'LUNAS ✅' : 'BELUM DIBAYAR ⚠️';
        $link    = static::publicShareUrl($record);

        if ($record->detail && count($record->detail) > 0) {
            $jml = count($record->detail);
            $pesan = implode("\n", [
                "Yth. Orang Tua/Wali Murid *{$nama}*",
                '',
                'Berikut informasi tagihan sekolah:',
                "• {$jml} item tagihan",
                "• Total   : {$nominal}",
                "• Status  : {$status}",
                '',
                'Lihat detail tagihan:',
                $link,
                '',
                'Terima kasih. 🙏',
            ]);
        } else {
            $jenis   = $record->jenisPembayaran?->nama ?? '-';
            $bulan   = static::namaBulan($record->bulan);
            $tahun   = $record->tahun;
            $pesan = implode("\n", [
                "Yth. Orang Tua/Wali Murid *{$nama}*",
                '',
                'Berikut informasi tagihan sekolah:',
                "• Jenis    : {$jenis}",
                "• Periode  : {$bulan} {$tahun}",
                "• Nominal  : {$nominal}",
                "• Status   : {$status}",
                '',
                'Lihat detail tagihan:',
                $link,
                '',
                'Terima kasih. 🙏',
            ]);
        }

        return 'https://wa.me/?text=' . urlencode($pesan);
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
                    ->label('Jenis')
                    ->formatStateUsing(fn ($record) => $record->detail && count($record->detail) > 0
                        ? 'Multi (' . count($record->detail) . ' item)'
                        : ($record->jenisPembayaran?->nama ?? '—')),
                Tables\Columns\TextColumn::make('bulan')
                    ->formatStateUsing(fn ($record) => $record->detail && count($record->detail) > 0
                        ? 'Multi'
                        : static::namaBulan($record->bulan ?? '')),
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

                // ── Cetak: buka PDF di tab baru ───────────────────────────────
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

                // ── Salin Link publik (tanpa login) ───────────────────────────
                //
                // x-on:click  → copy ke clipboard (sync, satu baris JS — WAJIB satu baris,
                //               newline di atribut HTML akan memotong ekspresi)
                // wire:click  → Livewire action → notifikasi sukses
                Action::make('salin_link')
                    ->tooltip('Salin Link Tagihan (tanpa login)')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->iconButton()
                    ->extraAttributes(fn (Tagihan $record): array => [
                        'x-on:click' => self::clipboardJs(static::publicShareUrl($record)),
                    ])
                    ->action(fn () => Notification::make()
                        ->title('Link berhasil disalin!')
                        ->body('Bagikan ke wali murid — dapat dibuka tanpa login.')
                        ->success()
                        ->send()),

                // ── Kirim via WhatsApp ────────────────────────────────────────
                //
                // Membuka wa.me tanpa nomor — pengguna tinggal pilih/ketik kontak
                // di WhatsApp. Pesan sudah terisi otomatis: nama siswa, jenis,
                // periode, nominal, status, dan link tagihan publik.
                Action::make('kirim_wa')
                    ->tooltip('Kirim Tagihan via WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->iconButton()
                    ->url(fn (Tagihan $record): string => static::whatsappUrl($record))
                    ->openUrlInNewTab(),

            ]);
    }

    /**
     * Hasilkan JS SATU BARIS untuk menyalin $url ke clipboard.
     *
     * ── Mengapa harus satu baris? ────────────────────────────────────────────
     * Nilai atribut HTML (x-on:click="...") diparse oleh browser sebagai
     * satu token — newline di dalamnya menyebabkan ekspresi terpotong dan
     * operasi clipboard gagal total tanpa error di console.
     * Heredoc PHP menghasilkan baris jamak dengan indentasi, sehingga TIDAK
     * boleh dipakai di sini. Gunakan string biasa yang flat.
     *
     * ── Mengapa variabel __u dan __fb? ──────────────────────────────────────
     * Nama pendek dan diawali __ menghindari tabrakan dengan variabel Alpine
     * atau variabel lain yang mungkin sudah ada di scope yang sama.
     *
     * ── Dukungan browser ────────────────────────────────────────────────────
     * - HTTPS (isSecureContext = true)  → navigator.clipboard.writeText()
     * - HTTP / browser lama             → execCommand('copy') via textarea
     */
    public static function clipboardJs(string $url): string
    {
        // addslashes: escape karakter ' dan \ agar aman di dalam JS single-quoted string
        $safe = addslashes($url);

        // Satu baris flat — tidak boleh ada newline sama sekali
        return "var __u='{$safe}';function __fb(t){var ta=document.createElement('textarea');ta.value=t;ta.style.cssText='position:fixed;top:-9999px;left:-9999px;opacity:0;';document.body.appendChild(ta);ta.focus();ta.select();try{document.execCommand('copy')}catch(e){}document.body.removeChild(ta);}if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(__u).catch(function(){__fb(__u)});}else{__fb(__u);}";
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }
}
