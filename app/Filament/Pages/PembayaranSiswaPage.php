<?php

namespace App\Filament\Pages;

use App\Models\JenisPembayaran;
use App\Models\KasHarian;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use App\Models\Tagihan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class PembayaranSiswaPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'Pembayaran Siswa';
    protected static ?string $title           = 'Pembayaran Siswa';
    protected static ?int    $navigationSort  = 1;

    protected static string $view = 'filament.pages.pembayaran-siswa-page';

    // ─── Mode tampilan ────────────────────────────────────────────────────────

    public string $viewMode = 'inquiry'; // 'inquiry' | 'kelas'

    // ─── State inquiry siswa ──────────────────────────────────────────────────

    public string $searchQuery   = '';
    public bool   $showResults   = false;
    public ?int   $siswa_id      = null;
    public ?Siswa $selectedSiswa = null;

    // ─── State per kelas ──────────────────────────────────────────────────────

    public string $filterJenisSekolah = '';
    public string $filterKelas        = '';

    public function updatedViewMode(): void
    {
        unset($this->sppMatrixKelas, $this->kelasList);
    }

    public function updatedFilterJenisSekolah(): void
    {
        $this->filterKelas = '';
        unset($this->kelasList, $this->sppMatrixKelas);
    }

    public function updatedFilterKelas(): void
    {
        unset($this->sppMatrixKelas);
    }

    // ─── Search autocomplete ──────────────────────────────────────────────────

    #[Computed]
    public function searchResults(): \Illuminate\Support\Collection
    {
        if (strlen(trim($this->searchQuery)) < 2) return collect();

        return Siswa::where('nis', 'like', "%{$this->searchQuery}%")
            ->orWhere('nama', 'like', "%{$this->searchQuery}%")
            ->orderBy('nama')
            ->limit(5)
            ->get();
    }

    public function updatedSearchQuery(): void
    {
        $this->showResults = strlen(trim($this->searchQuery)) >= 2;
        unset($this->searchResults);
    }

    public function selectSiswa(int $id): void
    {
        $this->siswa_id      = $id;
        $this->selectedSiswa = Siswa::with('kelasSaatIni')->find($id);
        $this->searchQuery   = "{$this->selectedSiswa->nis} - {$this->selectedSiswa->nama}";
        $this->showResults   = false;
        unset($this->tagihans, $this->history, $this->riwayatPerTahun);
    }

    public function clearSiswa(): void
    {
        $this->siswa_id      = null;
        $this->selectedSiswa = null;
        $this->searchQuery   = '';
        $this->showResults   = false;
        unset($this->tagihans, $this->history, $this->riwayatPerTahun);
    }

    // ─── Computed: inquiry siswa ──────────────────────────────────────────────

    #[Computed]
    public function tagihans(): \Illuminate\Support\Collection
    {
        if (! $this->siswa_id) return collect();

        return Tagihan::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa_id)
            ->where('status', 'belum_bayar')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
    }

    #[Computed]
    public function history(): \Illuminate\Support\Collection
    {
        if (! $this->siswa_id) return collect();

        return Pembayaran::with(['jenisPembayaran', 'tagihan'])
            ->where('siswa_id', $this->siswa_id)
            ->orderByDesc('tanggal_bayar')
            ->limit(15)
            ->get()
            ->map(function ($p) {
                $p->share_token = \DB::table('pdf_links')
                    ->where('pdf_id', $p->id)
                    ->where('jenis', 'kuitansi')
                    ->where('expired_at', '>', now())
                    ->value('token');

                $semuaPembayaran = Pembayaran::where('tagihan_id', $p->tagihan_id)
                    ->orderBy('tanggal_bayar')->orderBy('id')
                    ->get(['id', 'nominal', 'tanggal_bayar', 'status']);

                $totalTerbayar = $semuaPembayaran->sum('nominal');
                $sisaTagihan   = ($p->tagihan && $p->tagihan->status !== 'lunas')
                    ? $p->tagihan->nominal_tagihan : 0;

                $p->total_tagihan  = $totalTerbayar + $sisaTagihan;
                $p->total_terbayar = $totalTerbayar;
                $p->sisa_tagihan   = $sisaTagihan;
                $p->cicilan_list   = $semuaPembayaran->values();
                $p->cicilan_ke     = $semuaPembayaran->search(fn($x) => $x->id === $p->id) + 1;

                return $p;
            });
    }

    /**
     * Riwayat pembayaran & kelas per tahun ajaran (untuk accordion history siswa).
     */
    #[Computed]
    public function riwayatPerTahun(): array
    {
        if (! $this->siswa_id) return [];

        $kelasMap = SiswaKelasHistory::where('siswa_id', $this->siswa_id)
            ->orderByDesc('tahun_mulai')
            ->get()
            ->keyBy('tahun_ajaran');

        $pembayaranAll = Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa_id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $grouped = [];
        foreach ($pembayaranAll as $p) {
            $bulan    = (int) $p->bulan;
            $tahun    = (int) $p->tahun;
            $taMulai  = $bulan >= 7 ? $tahun : $tahun - 1;
            $taLabel  = "{$taMulai}/" . ($taMulai + 1);

            if (! isset($grouped[$taLabel])) {
                $history              = $kelasMap->get($taLabel);
                $grouped[$taLabel] = [
                    'tahun_ajaran' => $taLabel,
                    'kelas'        => $history?->kelas ?? '—',
                    'jenis_sekolah'=> $history?->jenis_sekolah ?? '—',
                    'pembayaran'   => [],
                    'total'        => 0,
                ];
            }

            $grouped[$taLabel]['pembayaran'][] = [
                'jenis'      => $p->jenisPembayaran?->nama ?? '—',
                'bulan'      => $p->bulan ? $this->getBulanLabel($p->bulan) : '—',
                'tahun'      => $p->tahun,
                'nominal'    => (float) $p->nominal,
                'tanggal'    => Carbon::parse($p->tanggal_bayar)->format('d M Y'),
                'status'     => $p->status,
            ];
            $grouped[$taLabel]['total'] += (float) $p->nominal;
        }

        return array_values($grouped);
    }

    // ─── Computed: per kelas ──────────────────────────────────────────────────

    #[Computed]
    public function jenisSekolahList(): array
    {
        return \App\Models\SiswaKelasHistory::where('is_current', true)
            ->whereNotNull('jenis_sekolah')
            ->distinct()->orderBy('jenis_sekolah')
            ->pluck('jenis_sekolah')
            ->toArray();
    }

    #[Computed]
    public function kelasList(): array
    {
        if (! $this->filterJenisSekolah) return [];

        return \App\Models\SiswaKelasHistory::where('jenis_sekolah', $this->filterJenisSekolah)
            ->where('is_current', true)
            ->distinct()->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();
    }

    #[Computed]
    public function sppMatrixKelas(): array
    {
        if (! $this->filterJenisSekolah || ! $this->filterKelas) return [];

        $tahunMulai = $this->akademikTahunMulai();

        // 12 bulan: Juli s/d Juni
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $m       = (($i + 6) % 12) + 1;
            $t       = $m <= 6 ? $tahunMulai + 1 : $tahunMulai;
            $months[] = [
                'bulan' => str_pad($m, 2, '0', STR_PAD_LEFT),
                'tahun' => (string) $t,
            ];
        }

        $students = Siswa::where('status_aktif', true)
            ->whereHas('kelasSaatIni', fn($q) => $q
                ->where('jenis_sekolah', $this->filterJenisSekolah)
                ->where('kelas', $this->filterKelas))
            ->orderBy('nama')
            ->get();

        if ($students->isEmpty()) return ['months' => $months, 'rows' => [], 'summary' => []];

        $siswaIds   = $students->pluck('id');
        $jenisSppId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
        $tahunList  = array_unique(array_column($months, 'tahun'));

        $pembayaranAll = Pembayaran::where('jenis_pembayaran_id', $jenisSppId)
            ->whereIn('siswa_id', $siswaIds)
            ->whereIn('tahun', $tahunList)
            ->get()
            ->groupBy(fn ($p) => "{$p->siswa_id}_{$p->bulan}_{$p->tahun}");

        $tagihanAll = Tagihan::where('jenis_pembayaran_id', $jenisSppId)
            ->whereIn('siswa_id', $siswaIds)
            ->whereIn('tahun', $tahunList)
            ->get()
            ->groupBy(fn ($t) => "{$t->siswa_id}_{$t->bulan}_{$t->tahun}");

        $rows = [];
        foreach ($students as $no => $siswa) {
            $cells     = [];
            $tunggakan = 0;
            $lunas     = 0;

            foreach ($months as $m) {
                $key        = "{$siswa->id}_{$m['bulan']}_{$m['tahun']}";
                $bayarGroup = $pembayaranAll->get($key);
                $tagGroup   = $tagihanAll->get($key);

                if ($bayarGroup && $bayarGroup->isNotEmpty()) {
                    $p       = $bayarGroup->sortByDesc('tanggal_bayar')->first();
                    $status  = $p->status === 'lunas' ? 'lunas' : 'cicilan';
                    $cells[] = [
                        'status'      => $status,
                        'tanggal'     => Carbon::parse($p->tanggal_bayar)->format('d-M-y'),
                        'nominal'     => (float) $p->nominal,
                        'siswa_id'    => $siswa->id,
                        'tagihan_id'  => $p->tagihan_id,
                    ];
                    $lunas++;
                } elseif ($tagGroup && $tagGroup->isNotEmpty()) {
                    $t = $tagGroup->first();
                    $cells[] = [
                        'status'     => 'tunggakan',
                        'nominal'    => (float) $t->nominal_tagihan,
                        'siswa_id'   => $siswa->id,
                        'tagihan_id' => $t->id,
                    ];
                    $tunggakan++;
                } else {
                    $cells[] = ['status' => 'kosong', 'siswa_id' => $siswa->id, 'tagihan_id' => null];
                }
            }

            $rows[] = [
                'no'        => $no + 1,
                'nama'      => $siswa->nama,
                'kelas'     => $siswa->kelasSaatIni?->kelas ?? '-',
                'siswa_id'  => $siswa->id,
                'lunas'     => $lunas,
                'tunggakan' => $tunggakan,
                'cells'     => $cells,
            ];
        }

        $summary = [];
        foreach ($months as $idx => $m) {
            $l = $tk = 0;
            foreach ($rows as $row) {
                $s = $row['cells'][$idx]['status'];
                if ($s === 'lunas' || $s === 'cicilan') $l++;
                elseif ($s === 'tunggakan') $tk++;
            }
            $summary[] = ['lunas' => $l, 'tunggakan' => $tk];
        }

        return ['months' => $months, 'rows' => $rows, 'summary' => $summary];
    }

    // ─── Action: Bayar (modal dengan form lengkap) ────────────────────────────

    public function bayarAction(): Action
    {
        return Action::make('bayar')
            ->modalHeading('Proses Pembayaran')
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Simpan Pembayaran')
            ->fillForm(function (array $arguments): array {
                $tagihan = Tagihan::with('jenisPembayaran')->findOrFail($arguments['tagihan_id']);
                return [
                    '_tagihan_id'     => $tagihan->id,
                    '_nominal_tagihan'=> (float) $tagihan->nominal_tagihan,
                    '_jenis'          => $tagihan->jenisPembayaran?->nama ?? '—',
                    '_periode'        => ($tagihan->bulan
                        ? $this->getBulanLabel($tagihan->bulan) . ' ' . $tagihan->tahun
                        : (string) $tagihan->tahun),
                    '_is_spp'         => $this->isSppByJenis($tagihan->jenisPembayaran?->nama),
                    'potongan'              => 0,
                    'nominal_bayar'         => (float) $tagihan->nominal_tagihan,
                    'tgl_bayar_struk'       => now()->toDateTimeString(),
                    'no_ref'                => '',
                    'rekening_tujuan'       => 'Cash',
                    'nama_rekening_pengirim'=> '',
                ];
            })
            ->form([
                Placeholder::make('_info')
                    ->label('Tagihan')
                    ->content(fn (Get $get) => "{$get('_jenis')} — {$get('_periode')}"),

                Placeholder::make('_nominal_display')
                    ->label('Nominal Tagihan')
                    ->content(fn (Get $get) => 'Rp ' . number_format((float) $get('_nominal_tagihan'), 0, ',', '.')),

                Hidden::make('_tagihan_id'),
                Hidden::make('_nominal_tagihan'),
                Hidden::make('_jenis'),
                Hidden::make('_periode'),
                Hidden::make('_is_spp'),

                TextInput::make('potongan')
                    ->label('Potongan / Diskon (Rp)')
                    ->numeric()->prefix('Rp')->default(0)
                    ->lazy()
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $tagihan  = (float) $get('_nominal_tagihan');
                        $potongan = (float) ($get('potongan') ?? 0);
                        $set('nominal_bayar', max(0, $tagihan - $potongan));
                    })
                    ->helperText('Kosongkan atau isi 0 jika tidak ada potongan'),

                TextInput::make('nominal_bayar')
                    ->label('Nominal Bayar (Rp)')
                    ->numeric()->prefix('Rp')->required()
                    ->disabled(fn (Get $get) => $this->isSppByJenis($get('_jenis')))
                    ->dehydrated()
                    ->helperText(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                        ? 'SPP harus dibayar penuh (setelah potongan)'
                        : 'Bisa diisi sebagian untuk cicilan'),

                TextInput::make('no_ref')
                    ->label('No. Referensi / Transfer')
                    ->placeholder('Contoh: TRF2025001 — kosongkan jika tunai')
                    ->nullable(),

                Select::make('rekening_tujuan')
                    ->label('Rekening Tujuan')
                    ->options(fn () => \App\Models\MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label'))
                    ->default('Cash')
                    ->live()
                    ->required(),

                TextInput::make('nama_rekening_pengirim')
                    ->label('Nama Pengirim')
                    ->placeholder('Contoh: Sri Utami')
                    ->hidden(fn (Get $get) => $get('rekening_tujuan') === 'Cash')
                    ->required(fn (Get $get) => $get('rekening_tujuan') !== 'Cash'),

                DateTimePicker::make('tgl_bayar_struk')
                    ->label('Tanggal Bayar di Struk')
                    ->required()
                    ->default(now())
                    ->maxDate(now())
                    ->format('m/d/Y')
                    ->helperText('tidak boleh lebih dari hari ini'),

                FileUpload::make('bukti_bayar')
                    ->label('Bukti Bayar (Foto / Struk)')
                    ->image()
                    ->imagePreviewHeight('160')
                    ->directory('bukti-bayar')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                    ->helperText('Format: JPG, PNG, WebP. Maks 5 MB.')
                    ->maxSize(5120)
                    ->nullable(),
            ])
            ->action(function (array $data, array $arguments): void {
                $tagihanId = $data['_tagihan_id'];
                $tagihan   = Tagihan::with('jenisPembayaran')->findOrFail($tagihanId);
                $nominal   = (float) $data['nominal_bayar'];
                $potongan  = (float) ($data['potongan'] ?? 0);
                $isSpp     = $this->isSppByJenis($tagihan->jenisPembayaran?->nama);

                $nominalSetelahPotongan = (float) $tagihan->nominal_tagihan - $potongan;

                if ($isSpp && abs($nominal - $nominalSetelahPotongan) > 1) {
                    Notification::make()
                        ->title('SPP harus dibayar penuh')
                        ->body('Nominal setelah potongan: Rp ' . number_format($nominalSetelahPotongan, 0, ',', '.'))
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                if ($nominal <= 0) {
                    Notification::make()->title('Nominal tidak valid')->danger()->send();
                    $this->halt();
                    return;
                }

                if ($nominal > $tagihan->nominal_tagihan) {
                    Notification::make()
                        ->title('Nominal melebihi tagihan')
                        ->body('Maksimal Rp ' . number_format($tagihan->nominal_tagihan, 0, ',', '.'))
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                $lunas = $nominal >= ($tagihan->nominal_tagihan - $potongan - 1);

                // Siswa untuk relasi
                $siswaModel = $this->selectedSiswa
                    ?? Siswa::find($tagihan->siswa_id);

                $pembayaran = Pembayaran::create([
                    'siswa_id'            => $tagihan->siswa_id,
                    'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
                    'tagihan_id'          => $tagihan->id,
                    'bulan'               => $tagihan->bulan,
                    'tahun'               => $tagihan->tahun,
                    'nominal'             => $nominal,
                    'tanggal_bayar'       => $data['tgl_bayar_struk'] ?? now(),
                    'status'              => $lunas ? 'lunas' : 'cicilan',
                    'no_ref'              => $data['no_ref'] ?: null,
                    'tgl_bayar_struk'     => $data['tgl_bayar_struk'] ?? null,
                    'potongan'              => $potongan,
                    'bukti_bayar'           => $data['bukti_bayar'] ?? null,
                    'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
                    'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
                    'created_by'            => auth()->id(),
                ]);

                $pembayaran->setRelation('siswa', $siswaModel);
                $pembayaran->setRelation('jenisPembayaran', $tagihan->jenisPembayaran);
                KasHarian::postingDariPembayaran($pembayaran);

                \DB::table('pdf_links')->insert([
                    'token'        => \Str::random(16),
                    'pdf_id'       => $pembayaran->id,
                    'original_url' => "/kuitansi/{$pembayaran->id}/pdf",
                    'jenis'        => 'kuitansi',
                    'jumlah_view'  => 0,
                    'expired_at'   => now()->addDays(30),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                if ($lunas) {
                    $tagihan->update(['status' => 'lunas']);
                } else {
                    $tagihan->update(['nominal_tagihan' => $tagihan->nominal_tagihan - $nominal]);
                }

                $jenisNama  = $tagihan->jenisPembayaran->nama;
                $bulanLabel = $tagihan->bulan ? " ({$this->getBulanLabel($tagihan->bulan)})" : '';

                Notification::make()
                    ->title($lunas ? 'Pembayaran Lunas ✓' : 'Cicilan Berhasil Disimpan')
                    ->body('Rp ' . number_format($nominal, 0, ',', '.') . " — {$jenisNama}{$bulanLabel}")
                    ->success()->send();

                unset($this->tagihans, $this->history, $this->riwayatPerTahun, $this->sppMatrixKelas);
            });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function akademikTahunMulai(): int
    {
        $now = now();
        return $now->month >= 7 ? $now->year : $now->year - 1;
    }

    public function isSpp(Tagihan $tagihan): bool
    {
        return $this->isSppByJenis($tagihan->jenisPembayaran?->nama);
    }

    public function isSppByJenis(?string $nama): bool
    {
        return strtolower($nama ?? '') === 'spp';
    }

    public function getBulanLabel(string $bulan): string
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ][$bulan] ?? $bulan;
    }

    public function getBulanPendek(string $bulan): string
    {
        return [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
            '04' => 'Apr', '05' => 'Mei', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Agu', '09' => 'Sep',
            '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ][$bulan] ?? $bulan;
    }

    public static function formatCalonTingkat(?int $tingkat, ?string $jenjang): string
    {
        if ($tingkat === null) return '—';
        return match ($jenjang) {
            'sd'   => "Kelas {$tingkat}",
            'smp'  => "Kelas {$tingkat}",
            'dta'  => "Tingkat {$tingkat}",
            'paud' => $tingkat === 1 ? 'TK-A' : ($tingkat === 2 ? 'TK-B' : 'Kelompok Bermain'),
            'tk'   => $tingkat === 1 ? 'TK-A' : 'TK-B',
            'SD'   => "Kelas {$tingkat}",
            'SMP'  => "Kelas {$tingkat}",
            'DTA'  => "Tingkat {$tingkat}",
            'PAUD' => $tingkat === 1 ? 'TK-A' : ($tingkat === 2 ? 'TK-B' : 'Kelompok Bermain'),
            'TK'   => $tingkat === 1 ? 'TK-A' : 'TK-B',
            default => (string) $tingkat,
        };
    }

    public function getWhatsappUrl($bayar): string
    {
        $namaSiswa  = $this->selectedSiswa?->nama ?? '-';
        $nisSiswa   = $this->selectedSiswa?->nis  ?? '-';
        $jenis      = $bayar->jenisPembayaran?->nama ?? '-';
        $bulanLabel = $bayar->bulan ? $this->getBulanLabel($bayar->bulan) : '-';
        $tahun      = $bayar->tahun ?? '-';
        $linkUrl    = $bayar->share_token ? url('/k/' . $bayar->share_token) : '-';

        $barisCicilan = $bayar->cicilan_list->map(function ($c, $i) {
            $tgl = Carbon::parse($c->tanggal_bayar)->translatedFormat('d F Y');
            $nom = 'Rp ' . number_format($c->nominal, 0, ',', '.');
            return "  Cicilan " . ($i + 1) . "  : {$nom} ({$tgl})";
        })->implode("\n");

        $totalTagihan  = 'Rp ' . number_format($bayar->total_tagihan,  0, ',', '.');
        $totalTerbayar = 'Rp ' . number_format($bayar->total_terbayar, 0, ',', '.');
        $sisa          = 'Rp ' . number_format($bayar->sisa_tagihan,   0, ',', '.');

        if ($bayar->status === 'lunas') {
            $pesan = implode("\n", [
                'Assalamualaikum,', '',
                'Berikut kami sampaikan kuitansi pembayaran:', '',
                "Nama          : {$namaSiswa}",
                "NIS           : {$nisSiswa}",
                "Jenis         : {$jenis}",
                "Periode       : {$bulanLabel} {$tahun}", '',
                'Rincian Pembayaran:',
                $barisCicilan, '',
                "Total Tagihan : {$totalTagihan}",
                "Total Terbayar: {$totalTerbayar}",
                "Sisa Tagihan  : Rp 0",
                "Status        : *Lunas*", '',
                'Silakan lihat kuitansi di tautan berikut:',
                $linkUrl, '', 'Terima kasih.',
            ]);
        } else {
            $pesan = implode("\n", [
                'Assalamualaikum,', '',
                'Berikut kami sampaikan bukti cicilan pembayaran:', '',
                "Nama          : {$namaSiswa}",
                "NIS           : {$nisSiswa}",
                "Jenis         : {$jenis}",
                "Periode       : {$bulanLabel} {$tahun}", '',
                'Rincian Pembayaran:',
                $barisCicilan, '',
                "Total Tagihan : {$totalTagihan}",
                "Total Terbayar: {$totalTerbayar}",
                "Sisa Tagihan  : {$sisa}",
                "Status        : *Cicilan*", '',
                'Silakan lihat bukti cicilan di tautan berikut:',
                $linkUrl, '', 'Terima kasih.',
            ]);
        }

        $noHp = preg_replace('/\D/', '', $this->selectedSiswa?->no_hp_orang_tua ?? '');
        if (str_starts_with($noHp, '0'))     $noHp = '62' . substr($noHp, 1);
        elseif (str_starts_with($noHp, '8')) $noHp = '62' . $noHp;

        $teks = rawurlencode($pesan);
        return $noHp ? "https://wa.me/{$noHp}?text={$teks}" : "https://wa.me/?text={$teks}";
    }
}
