<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Http\Controllers\TagihanPublicController;
use App\Models\JenisPembayaran;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
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

    // ─── Helper: label kolom untuk tagihan multi-item (dari detail JSON) ──────

    public static function detailJenisLabel(Tagihan $record): string
    {
        $names = collect($record->detail)
            ->pluck('jenis')
            ->filter()
            ->map(fn ($j) => match (strtoupper(trim((string) $j))) {
                'SPP'                        => 'SPP',
                'DAFTAR ULANG'               => 'Daftar Ulang',
                'BIAYA PENDAFTARAN', 'DAFTAR MASUK' => 'Pendaftaran',
                default                      => (string) $j,
            })
            ->unique()
            ->values();

        return $names->isNotEmpty() ? 'Multi (' . $names->implode('/') . ')' : 'Multi';
    }

    public static function detailBulanLabel(Tagihan $record): string
    {
        $bulans = collect($record->detail)
            ->pluck('bulan')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->map(fn ($b) => static::namaBulan($b));

        return $bulans->isNotEmpty() ? $bulans->implode(', ') : '—';
    }

    // ─── Helper: tagihan belum bayar paling akhir (bulan terakhir) ────────────

    public static function latestUnpaidTagihan(int $siswaId): ?Tagihan
    {
        return Tagihan::where('siswa_id', $siswaId)
            ->where('status', 'belum_bayar')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id')
            ->first();
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

    // ─── Helper: URL WhatsApp untuk tagihan gabungan (semua belum bayar) ──────

    public static function whatsappGabunganUrl($siswa): string
    {
        $unpaid = Tagihan::where('siswa_id', $siswa->id)->where('status', 'belum_bayar')->get();
        $total  = (float) $unpaid->sum('nominal_tagihan');
        $link   = route('tagihan.gabungan.pdf', $siswa->id);

        $pesan = implode("\n", [
            "Yth. Orang Tua/Wali *{$siswa->nama}*",
            '',
            'Berikut informasi tagihan sekolah (gabungan):',
            '• Jumlah   : ' . $unpaid->count() . ' tagihan belum bayar',
            '• Total    : Rp ' . number_format($total, 0, ',', '.'),
            '',
            'Lihat semua tagihan:',
            $link,
            '',
            'Terima kasih. 🙏',
        ]);

        return 'https://wa.me/?text=' . urlencode($pesan);
    }

    /**
     * HTML tabel tagihan belum bayar milik siswa (dipakai modal cetak & kirim WA).
     * Return: ['html' => string, 'count' => int, 'total' => float]
     */
    public static function tagihanListModal(Tagihan $record): array
    {
        $unpaid = Tagihan::with('jenisPembayaran')
            ->where('siswa_id', $record->siswa_id)
            ->where('status', 'belum_bayar')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $latest = static::latestUnpaidTagihan($record->siswa_id);

        $count = $unpaid->count();
        $total = (float) $unpaid->sum('nominal_tagihan');

        $rowsHtml = '';
        foreach ($unpaid->take(10) as $t) {
            $isMulti = $t->detail && count($t->detail) > 0;
            $jenis   = $isMulti
                ? static::detailJenisLabel($t)
                : ($t->jenisPembayaran?->nama ?? 'Tagihan');
            $periode = $isMulti
                ? (static::detailBulanLabel($t) . ($t->tahun ? ' ' . $t->tahun : ''))
                : ($t->bulan ? (static::namaBulan($t->bulan) . ' ' . $t->tahun) : ($t->tahun ?: '—'));
            $nominal = 'Rp ' . number_format((float) $t->nominal_tagihan, 0, ',', '.');
            $bg      = ($latest && $t->id === $latest->id) ? 'background:#fef9c3;' : '';

            $rowsHtml .= '<tr style="' . $bg . '">'
                . '<td style="padding:2px 6px;">' . e($jenis) . '</td>'
                . '<td style="padding:2px 6px;">' . e($periode) . '</td>'
                . '<td style="padding:2px 6px;text-align:right;white-space:nowrap;">' . e($nominal) . '</td>'
                . '</tr>';
        }

        if ($count > 10) {
            $rowsHtml .= '<tr><td colspan="3" style="padding:2px 6px;color:#6b7280;">'
                . '… dan ' . ($count - 10) . ' tagihan lainnya</td></tr>';
        }

        $html = '<div style="font-size:0.8rem;">'
            . '<div style="margin-bottom:4px;">Siswa: <strong>' . e(static::formatNamaSiswa($record->siswa)) . '</strong></div>'
            . '<div style="margin-bottom:6px;color:#6b7280;">Tagihan belum bayar: <strong>' . $count . '</strong>'
            . ' &bull; Total: <strong>Rp ' . number_format($total, 0, ',', '.') . '</strong></div>'
            . '<table style="width:100%;border-collapse:collapse;">'
            . '<thead><tr style="border-bottom:1px solid #e5e7eb;color:#6b7280;">'
            . '<th style="padding:2px 6px;text-align:left;font-weight:600;">Jenis</th>'
            . '<th style="padding:2px 6px;text-align:left;font-weight:600;">Bulan &amp; Tahun</th>'
            . '<th style="padding:2px 6px;text-align:right;font-weight:600;">Nominal</th>'
            . '</tr></thead><tbody>' . $rowsHtml . '</tbody></table>'
            . '</div>';

        return ['html' => $html, 'count' => $count, 'total' => $total, 'latest' => $latest];
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex()
                    ->label('No'),
                Tables\Columns\TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => static::formatNamaSiswa($record->siswa)),
                Tables\Columns\TextColumn::make('jenisPembayaran.nama')
                    ->label('Jenis')
                    ->formatStateUsing(fn ($record) => ($record->detail && count($record->detail) > 0)
                        ? static::detailJenisLabel($record)
                        : ($record->jenisPembayaran?->nama ?? '—')),
                Tables\Columns\TextColumn::make('bulan')
                    ->formatStateUsing(fn ($record) => ($record->detail && count($record->detail) > 0)
                        ? static::detailBulanLabel($record)
                        : static::namaBulan($record->bulan ?? '')),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nominal_tagihan')
                    ->money('IDR')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($record) => $record->status === 'lunas'
                        ? (float) ($record->pembayarans_sum_nominal ?? 0) + (float) ($record->pembayarans_sum_potongan ?? 0)
                        : (float) $record->nominal_tagihan),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger'  => 'belum_bayar',
                        'success' => 'lunas',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->modifyQueryUsing(fn ($query) => $query->withSum('pembayarans', 'nominal')->withSum('pembayarans', 'potongan'))

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
                    ->default('belum_bayar')
                    ->native(false),

                Tables\Filters\SelectFilter::make('jenis_pembayaran_id')
                    ->label('Jenis Tagihan')
                    ->options(fn () => JenisPembayaran::pluck('nama', 'id')->toArray())
                    ->placeholder('Semua Jenis')
                    ->native(false),
            ])

            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->modalWidth('sm')
                    ->modalHeading('Export PDF')
                    ->modalSubmitActionLabel('Download')
                    ->form(fn ($livewire) => static::exportModalForm($livewire, 'pdf'))
                    ->action(function ($data, $livewire) {
                        return redirect()->route('tagihan.export.pdf', static::exportFilterParams($livewire));
                    }),

                Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-table-cells')
                    ->color('gray')
                    ->modalWidth('sm')
                    ->modalHeading('Export CSV')
                    ->modalSubmitActionLabel('Download')
                    ->form(fn ($livewire) => static::exportModalForm($livewire, 'csv'))
                    ->action(function ($data, $livewire) {
                        return redirect()->route('tagihan.export.csv', static::exportFilterParams($livewire));
                    }),
            ])
            ->actions([

                // ── Cetak Tagihan: modal + opsi gabungan bila > 1 tagihan ─────
                Action::make('cetak')
                    ->tooltip('Cetak Tagihan')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->iconButton()
                    ->visible(fn (Tagihan $record) => $record->status !== 'lunas')
                    ->modalHeading('Cetak Tagihan')
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Cetak')
                    ->form(function (Tagihan $record) {
                        $d = static::tagihanListModal($record);

                        return [
                            Placeholder::make('daftar_tagihan')
                                ->hiddenLabel()
                                ->content(new \Illuminate\Support\HtmlString($d['html'])),

                            Forms\Components\Radio::make('pilihan')
                                ->label('Pilih cetak')
                                ->options([
                                    'single'   => 'Cetak tagihan bulan terakhir saja',
                                    'gabungan' => 'Cetak Tagihan Gabungan (' . $d['count'] . ' tagihan belum bayar)',
                                ])
                                ->default('single')
                                ->visible($d['count'] > 1),
                        ];
                    })
                    ->action(function (array $data, Tagihan $record, $livewire) {
                        $latest = static::latestUnpaidTagihan($record->siswa_id) ?? $record;
                        $url = (($data['pilihan'] ?? 'single') === 'gabungan')
                            ? route('tagihan.gabungan.pdf', $record->siswa_id)
                            : route('tagihan.pdf', $latest);

                        $livewire->js("window.open('{$url}', '_blank')");
                    }),

                // ── Cetak Kuitansi (tagihan sudah lunas) ──────────────────────
                Action::make('cetak_kuitansi')
                    ->tooltip('Cetak Kuitansi')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->iconButton()
                    ->visible(fn (Tagihan $record) => $record->status === 'lunas')
                    ->url(function (Tagihan $record) {
                        $pembayaran = $record->pembayaran;

                        return $pembayaran
                            ? "/kuitansi/{$pembayaran->id}"
                            : route('tagihan.pdf', $record);
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
                    ->modalHeading('Kirim Tagihan via WhatsApp')
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Kirim via WhatsApp')
                    ->form(function (Tagihan $record) {
                        $d = static::tagihanListModal($record);

                        return [
                            Placeholder::make('daftar_tagihan')
                                ->hiddenLabel()
                                ->content(new \Illuminate\Support\HtmlString($d['html'])),

                            Forms\Components\Radio::make('pilihan')
                                ->label('Pilih yang dikirim')
                                ->options([
                                    'single'   => 'Kirim tagihan bulan terakhir saja',
                                    'gabungan' => 'Kirim Tagihan Gabungan (' . $d['count'] . ' tagihan belum bayar)',
                                ])
                                ->default('single')
                                ->visible($d['count'] > 1),
                        ];
                    })
                    ->action(function (array $data, Tagihan $record, $livewire) {
                        $latest = static::latestUnpaidTagihan($record->siswa_id) ?? $record;
                        $url = (($data['pilihan'] ?? 'single') === 'gabungan')
                            ? static::whatsappGabunganUrl($record->siswa)
                            : static::whatsappUrl($latest);

                        $livewire->js("window.open('" . addslashes($url) . "', '_blank')");
                    }),

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

    // ─── Helper: format nama siswa ──────────────────────────────────────────

    public static function formatNamaSiswa($siswa): string
    {
        if (!$siswa) return '—';

        $nama = $siswa->nama ?? '—';

        if ($siswa->is_calon) {
            return $nama . ' / ' . ($siswa->kelasSaatIni?->jenis_sekolah ?? $siswa->calon_jenis ?? '—');
        }

        return $nama . ' / ' . ($siswa->kelasSaatIni?->kelas ?? '—') . ' / ' . ($siswa->kelasSaatIni?->jenis_sekolah ?? '—');
    }

    // ─── Helper: filter key → label ─────────────────────────────────────────

    protected static function exportFilterLabel(string $key, string $value): string
    {
        return match ($key) {
            'bulan'               => static::namaBulan($value),
            'tahun'               => $value,
            'status'              => $value === 'lunas' ? 'Lunas' : 'Belum Bayar',
            'jenis_pembayaran_id' => JenisPembayaran::find($value)?->nama ?? $value,
            default               => $value,
        };
    }

    // ─── Helper: ekstrak filter params dari Livewire ────────────────────────

    protected static function exportFilterParams($livewire): array
    {
        $params = [];
        foreach (['bulan', 'tahun', 'status', 'jenis_pembayaran_id'] as $key) {
            $state = $livewire->getTableFilterState($key);
            $value = $state['value'] ?? null;
            if (filled($value)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    // ─── Helper: form schema untuk modal export ─────────────────────────────

    protected static function exportModalForm($livewire, string $jenis): array
    {
        $params = static::exportFilterParams($livewire);

        $filterLabels = [
            'bulan'               => 'Bulan',
            'tahun'               => 'Tahun',
            'status'              => 'Status',
            'jenis_pembayaran_id' => 'Jenis',
        ];

        $filterParts = [];
        foreach ($params as $key => $value) {
            $label = $filterLabels[$key] ?? $key;
            $filterParts[] = $label . ': ' . static::exportFilterLabel($key, $value);
        }

        $filterText = $filterParts ? implode(' | ', $filterParts) : 'Semua data';

        $query = Tagihan::query();
        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }
        $count = $query->count();

        return [
            Placeholder::make('ringkasan_filter')
                ->label('Filter Aktif')
                ->content($filterText),
            Placeholder::make('jumlah')
                ->label('Jumlah Tagihan')
                ->content($count . ' tagihan'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
