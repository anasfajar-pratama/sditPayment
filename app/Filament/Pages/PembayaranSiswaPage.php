<?php

namespace App\Filament\Pages;

use App\Models\Akun;
use App\Models\JenisPembayaran;
use App\Models\KasHarian;
use App\Models\LogDanaMasuk;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use App\Models\Tagihan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;

class PembayaranSiswaPage extends Page
{
protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'Pembayaran Siswa';
    protected static ?int    $navigationSort  = 30;

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

        $jenisSppId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
        $jenisDuId  = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
        $jenisBpId  = 1; // Daftar Masuk (Biaya Pendaftaran)

        $isNewEntry = $this->isNewEntryMatrix();
        $isDta      = strtoupper($this->filterJenisSekolah) === 'DTA';

        // DTA: 12 bulan Juli s/d Juni (tanpa DU, Juli sebagai SPP biasa)
        // Lainnya: 11 bulan Agustus s/d Juni (Juli digabung dgn BP/DU)
        $bulanMulai  = $isDta ? 7 : 8;
        $jumlahBulan = $isDta ? 12 : 11;
        $months = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $m       = (($bulanMulai - 1 + $i) % 12) + 1;
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

        $siswaIds  = $students->pluck('id');
        $tahunList = array_unique(array_column($months, 'tahun'));

        // ── Batch load SPP pembayaran & tagihan ──
        $pembayaranAll = Pembayaran::where('jenis_pembayaran_id', $jenisSppId)
            ->whereIn('siswa_id', $siswaIds)
            ->whereIn('tahun', $tahunList)
            ->get()
            ->groupBy(fn ($p) => "{$p->siswa_id}_{$p->bulan}_{$p->tahun}");

        $tagihanSppAll = collect();
        $tagihanSemua = Tagihan::whereIn('siswa_id', $siswaIds)
            ->whereIn('tahun', $tahunList)
            ->where('status', 'belum_bayar')
            ->get();
        foreach ($tagihanSemua as $t) {
            if ($t->detail && count($t->detail) > 0) {
                foreach ($t->detail as $item) {
                    if (($item['jenis'] ?? '') === 'SPP' && !empty($item['bulan'])) {
                        $v = (object) [
                            'id' => $t->id,
                            'siswa_id' => $t->siswa_id,
                            'bulan' => $item['bulan'],
                            'tahun' => $item['tahun'],
                            'nominal_tagihan' => $item['nominal'] ?? 0,
                            'status' => $t->status,
                        ];
                        $tagihanSppAll->push($v);
                    }
                }
            } elseif ($t->jenis_pembayaran_id == $jenisSppId && $t->bulan) {
                $tagihanSppAll->push($t);
            }
        }
        $tagihanSppAll = $tagihanSppAll->groupBy(fn ($t) => "{$t->siswa_id}_{$t->bulan}_{$t->tahun}");

        // ── Batch load DU pembayaran & tagihan ──
        $duPembayaran = Pembayaran::where('jenis_pembayaran_id', $jenisDuId)
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun', (string) $tahunMulai)
            ->get()
            ->groupBy('siswa_id');

        $duTagihanSemua = Tagihan::where('jenis_pembayaran_id', $jenisDuId)
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun', (string) $tahunMulai)
            ->get()
            ->keyBy('siswa_id');

        // ── Siswa "baru masuk" (kelas entry ATAU punya tagihan BP) → kolom pertama BP ──
        $baruIds = Tagihan::where('jenis_pembayaran_id', $jenisBpId)
            ->whereIn('siswa_id', $siswaIds)
            ->pluck('siswa_id')
            ->flip();

        // ── Batch load BP (Biaya Pendaftaran) tagihan & pembayaran ──
        $bpTagihan = collect();
        $bpPembayaran = collect();
        if ($isNewEntry || $baruIds->isNotEmpty()) {
            $bpPembayaran = Pembayaran::where('jenis_pembayaran_id', $jenisBpId)
                ->whereIn('siswa_id', $siswaIds)
                ->get()
                ->groupBy('siswa_id');
            $bpTagihan = Tagihan::where('jenis_pembayaran_id', $jenisBpId)
                ->whereIn('siswa_id', $siswaIds)
                ->get()
                ->keyBy('siswa_id');
        }

        // ── Latest tagihan (for aksi column) ──
        $latestTagihan = Tagihan::whereIn('siswa_id', $siswaIds)
            ->whereIn('tahun', $tahunList)
            ->where('status', 'belum_bayar')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('siswa_id')
            ->map(fn ($g) => $g->first());

        // ── Also find old multi-item tagihan via detail JSON ──
        $multiTagihan = Tagihan::whereNull('jenis_pembayaran_id')
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun', (string) $tahunMulai)
            ->where('status', 'belum_bayar')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('siswa_id')
            ->map(fn ($g) => $g->first());

        // Merge: prefer latest overall
        foreach ($multiTagihan as $sid => $t) {
            if (!isset($latestTagihan[$sid]) || $t->created_at > $latestTagihan[$sid]->created_at) {
                $latestTagihan[$sid] = $t;
            }
        }

        $rows = [];
        foreach ($students as $no => $siswa) {
            $cells          = [];
            $tunggakan      = 0;
            $lunas          = 0;
            $belumDibayar  = 0;
            $adaBelumBayar = false;
            $isBaruSiswa   = $isNewEntry || isset($baruIds[$siswa->id]);

            // ── Cell BP / Daftar Ulang + Juli ── (DTA tanpa DU/BP)
            if ($isDta) {
                $firstCell = null;
            } elseif ($isBaruSiswa) {
                $bpBayarList = $bpPembayaran->get($siswa->id);
                $bpTagih = $bpTagihan->get($siswa->id);

                if ($bpBayarList && $bpBayarList->isNotEmpty()) {
                    $totalNominal = (float) $bpBayarList->sum('nominal');
                    $latestBayar = $bpBayarList->sortByDesc('tanggal_bayar')->first();
                    $allLunas = $bpTagih
                        ? $bpTagih->status === 'lunas'
                        : $bpBayarList->every(fn ($p) => $p->status === 'lunas');

                    $firstCell = [
                        'status'  => $allLunas ? 'lunas' : 'cicilan',
                        'tanggal' => Carbon::parse($latestBayar->tanggal_bayar)->format('d-M-y'),
                        'nominal' => $totalNominal,
                        'tipe'    => 'bp',
                    ];
                    if (!$allLunas && $bpTagih) {
                        $firstCell['sisa'] = (float) ($bpTagih->nominal_tagihan ?? 0);
                        $firstCell['tagihan_id'] = $bpTagih->id;
                    }
                    if ($allLunas) {
                        $lunas++;
                    } else {
                        $tunggakan++;
                        $adaBelumBayar = true;
                    }
                } elseif ($bpTagih) {
                    $firstCell = [
                        'status'     => 'tunggakan',
                        'nominal'    => (float) $bpTagih->nominal_tagihan,
                        'tagihan_id' => $bpTagih->id,
                        'tipe'       => 'bp',
                    ];
                    $tunggakan++;
                    $adaBelumBayar = true;
                } else {
                    $firstCell = [
                        'status' => 'belum_dibayar',
                        'tipe'   => 'bp',
                    ];
                    $belumDibayar++;
                    $adaBelumBayar = true;
                }
            } else {
                $duBayarList = $duPembayaran->get($siswa->id);
                $duTagih = $duTagihanSemua->get($siswa->id);

                if ($duBayarList && $duBayarList->isNotEmpty()) {
                    $totalNominal = (float) $duBayarList->sum('nominal');
                    $latestBayar = $duBayarList->sortByDesc('tanggal_bayar')->first();
                    $allLunas = $duTagih
                        ? $duTagih->status === 'lunas'
                        : $duBayarList->every(fn ($p) => $p->status === 'lunas');

                    $firstCell = [
                        'status'  => $allLunas ? 'lunas' : 'cicilan',
                        'tanggal' => Carbon::parse($latestBayar->tanggal_bayar)->format('d-M-y'),
                        'nominal' => $totalNominal,
                    ];
                    if (!$allLunas && $duTagih) {
                        $firstCell['sisa'] = (float) ($duTagih->nominal_tagihan ?? 0);
                        $firstCell['tagihan_id'] = $duTagih->id;
                    }
                    if ($allLunas) {
                        $lunas++;
                    } else {
                        $tunggakan++;
                        $adaBelumBayar = true;
                    }
                } elseif ($duTagih) {
                    $firstCell = [
                        'status'     => 'tunggakan',
                        'nominal'    => (float) $duTagih->nominal_tagihan,
                        'tagihan_id' => $duTagih->id,
                    ];
                    $tunggakan++;
                    $adaBelumBayar = true;
                } else {
                    $firstCell = [
                        'status' => 'belum_dibayar',
                        'tipe'   => 'daftar_ulang',
                    ];
                    $belumDibayar++;
                    $adaBelumBayar = true;
                }
            }

            // ── Cell bulan (Jul–Jun untuk new entry, Agu–Jun untuk standard) ──
            foreach ($months as $m) {
                $key       = "{$siswa->id}_{$m['bulan']}_{$m['tahun']}";
                $bayarGrp  = $pembayaranAll->get($key);
                $tagGrp    = $tagihanSppAll->get($key);

                if ($bayarGrp && $bayarGrp->isNotEmpty()) {
                    $p = $bayarGrp->sortByDesc('tanggal_bayar')->first();
                    $totalNominal = (float) $bayarGrp->sum('nominal');
                    $sisaTagihan = $p->status === 'cicilan' && $tagGrp && $tagGrp->isNotEmpty()
                        ? $tagGrp->first()
                        : null;
                    $cells[] = [
                        'status'      => $p->status === 'lunas' ? 'lunas' : 'cicilan',
                        'tanggal'     => Carbon::parse($p->tanggal_bayar)->format('d-M-y'),
                        'nominal'     => $totalNominal,
                        'sisa'        => $sisaTagihan ? (float) $sisaTagihan->nominal_tagihan : 0,
                        'siswa_id'    => $siswa->id,
                        'tagihan_id'  => $sisaTagihan?->id ?? $p->tagihan_id,
                        'bulan'       => $m['bulan'],
                        'tahun'       => $m['tahun'],
                    ];
                    if ($sisaTagihan) {
                        $tunggakan++;
                        $adaBelumBayar = true;
                    } else {
                        $lunas++;
                    }
                } elseif ($tagGrp && $tagGrp->isNotEmpty()) {
                    $t = $tagGrp->first();
                    $cells[] = [
                        'status'     => 'tunggakan',
                        'nominal'    => (float) ($t->nominal_tagihan ?? 0),
                        'siswa_id'   => $siswa->id,
                        'tagihan_id' => $t->id,
                        'bulan'      => $m['bulan'],
                        'tahun'      => $m['tahun'],
                    ];
                    $tunggakan++;
                    $adaBelumBayar = true;
                } else {
                    $cells[] = [
                        'status'   => 'belum_dibayar',
                        'siswa_id' => $siswa->id,
                        'bulan'    => $m['bulan'],
                        'tahun'    => $m['tahun'],
                    ];
                    $belumDibayar++;
                    $adaBelumBayar = true;
                }
            }

            $lTagihan = $latestTagihan->get($siswa->id);

            $rows[] = [
                'no'                   => $no + 1,
                'nama'                 => $siswa->nama,
                'kelas'                => $siswa->kelasSaatIni?->kelas ?? '-',
                'siswa_id'             => $siswa->id,
                'is_new_entry'         => $isBaruSiswa,
                'first_cell'           => $firstCell,
                'cells'                => $cells,
                'lunas_count'          => $lunas,
                'tunggakan_count'      => $tunggakan,
                'belum_dibayar_count'  => $belumDibayar,
                'has_unpaid'           => $adaBelumBayar,
                'latest_tagihan_id'    => $lTagihan?->id,
                'latest_tagihan_status'=> $lTagihan?->status,
            ];
        }

        // ── Summary per bulan ──
        $summary = [];
        foreach ($months as $idx => $m) {
            $l = $tk = $bd = 0;
            foreach ($rows as $row) {
                $s = $row['cells'][$idx]['status'] ?? 'belum_dibayar';
                if ($s === 'lunas' || $s === 'cicilan') $l++;
                elseif ($s === 'tunggakan') $tk++;
                else $bd++;
            }
            $summary[] = ['lunas' => $l, 'tunggakan' => $tk, 'belum_dibayar' => $bd];
        }

        return ['months' => $months, 'rows' => $rows, 'summary' => $summary, 'is_new_entry' => $isNewEntry, 'is_dta' => $isDta];
    }

    // ─── Action: Bayar (modal dengan form lengkap) ────────────────────────────

    public function bayarAction(): Action
    {
        return Action::make('bayar')
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->modalHeading('Proses Pembayaran')
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Simpan Pembayaran')
            ->fillForm(function (array $arguments): array {
                if (isset($arguments['tagihan_id'])) {
                    $tagihan = Tagihan::with('jenisPembayaran')->findOrFail($arguments['tagihan_id']);
                    return [
                        '_mode'           => 'tagihan',
                        '_tagihan_id'     => $tagihan->id,
                        '_nominal_tagihan'=> (float) $tagihan->nominal_tagihan,
                        '_jenis'          => $tagihan->jenisPembayaran?->nama ?? '—',
                        '_periode'        => ($tagihan->bulan
                            ? $this->getBulanLabel($tagihan->bulan) . ' ' . $tagihan->tahun
                            : (string) $tagihan->tahun),
                        '_is_spp'         => $this->isSppByJenis($tagihan->jenisPembayaran?->nama),
                        '_cara_bayar'     => 'lunasi',
                        'pakai_potongan'        => false,
                        'potongan'              => 0,
                        'nominal_bayar'         => (float) $tagihan->nominal_tagihan,
                        'tgl_bayar_struk'       => now()->toDateTimeString(),
                        'no_ref'                => '',
                        'rekening_tujuan'       => 'Cash',
                        'nama_rekening_pengirim'=> '',
                    ];
                }

                $jenis = $arguments['jenis'] ?? 'spp';
                $bulanDari = $arguments['bulan'] ?? '08';
                $tahunMulai = $this->akademikTahunMulai();
                $tahun = $arguments['tahun'] ?? (string) $tahunMulai;

                return [
                    '_mode'           => 'direct',
                    '_siswa_id'       => $arguments['siswa_id'],
                    '_bulan_dari'     => $bulanDari,
                    '_tahun'          => $tahun,
                    '_jenis'          => $jenis,
                    'sampai_bulan'    => $bulanDari,
                    'nominal_spp_per_bulan' => null,
                    'nominal_awal'    => null,
                    'pakai_potongan'  => false,
                    'potongan'              => 0,
                    'nominal_bayar'         => 0,
                    'tgl_bayar_struk'       => now()->toDateTimeString(),
                    'no_ref'                => '',
                    'rekening_tujuan'       => 'Cash',
                    'nama_rekening_pengirim'=> '',
                ];
            })
            ->form(function (array $arguments): array {
                $isDirect = !isset($arguments['tagihan_id']);

                if (!$isDirect) {
                    // ── Mode: ada tagihan existing ──
                    return [
                        Placeholder::make('_info')
                            ->label('Tagihan')
                            ->content(fn (Get $get) => "{$get('_jenis')} — {$get('_periode')}"),

                        Placeholder::make('_nominal_display')
                            ->label('Nominal Tagihan')
                            ->content(fn (Get $get) => 'Rp ' . number_format((float) $get('_nominal_tagihan'), 0, ',', '.')),

                        Hidden::make('_mode'),
                        Hidden::make('_tagihan_id'),
                        Hidden::make('_nominal_tagihan'),
                        Hidden::make('_jenis'),
                        Hidden::make('_periode'),
                        Hidden::make('_is_spp'),

                        ToggleButtons::make('_cara_bayar')
                            ->label('Cara Bayar')
                            ->options([
                                'lunasi' => 'Lunasi',
                                'cicil'  => 'Cicil',
                            ])
                            ->default('lunasi')
                            ->grouped()
                            ->live()
                            ->hidden(fn (Get $get) => $this->isSppByJenis($get('_jenis')))
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                if ($get('_cara_bayar') === 'lunasi') {
                                    $tagihan  = (float) $get('_nominal_tagihan');
                                    $potongan = (float) ($get('potongan') ?? 0);
                                    $set('nominal_bayar', max(0, $tagihan - $potongan));
                                } else {
                                    $set('nominal_bayar', 0);
                                    $set('pakai_potongan', false);
                                    $set('potongan', 0);
                                }
                            }),

                        TextInput::make('nominal_bayar')
                            ->label('Nominal Bayar (Rp)')
                            ->numeric()->prefix('Rp')->required()
                            ->disabled(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                                || $get('_cara_bayar') === 'lunasi')
                            ->dehydrated()
                            ->helperText(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                                ? 'SPP harus dibayar penuh (setelah potongan)'
                                : ($get('_cara_bayar') === 'lunasi'
                                    ? 'Lunasi: nominal = tagihan - potongan'
                                    : 'Isi nominal cicilan')),

                        Checkbox::make('pakai_potongan')
                            ->label('Potongan / Diskon')
                            ->default(false)
                            ->live()
                            ->hidden(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                                || $get('_cara_bayar') !== 'lunasi'),

                        TextInput::make('potongan')
                            ->label(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                                ? 'Potongan / Diskon (Rp)' : 'Potongan (Rp)')
                            ->numeric()->prefix('Rp')->default(0)
                            ->lazy()
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                if ($this->isSppByJenis($get('_jenis'))
                                    || $get('_cara_bayar') === 'lunasi') {
                                    $tagihan  = (float) $get('_nominal_tagihan');
                                    $potongan = (float) ($get('potongan') ?? 0);
                                    $set('nominal_bayar', max(0, $tagihan - $potongan));
                                }
                            })
                            ->hidden(fn (Get $get) => !$this->isSppByJenis($get('_jenis'))
                                && !$get('pakai_potongan'))
                            ->helperText(fn (Get $get) => $this->isSppByJenis($get('_jenis'))
                                ? 'Kosongkan atau isi 0 jika tidak ada potongan'
                                : 'Isi nominal potongan jika ada'),

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

                        DatePicker::make('tgl_bayar_struk')
                            ->label('Tanggal Bayar di Struk')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->helperText('Tidak boleh lebih dari hari ini'),

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
                    ];
                }

                // ── Mode: Direct Pay (tanpa tagihan) ──
                $isDu = fn (Get $get) => in_array($get('_jenis'), ['daftar_ulang', 'daftar_masuk']);

                $bulanOptions = [];
                $bulanDari = (int) ($arguments['bulan'] ?? 8);
                $tahunAwal = $this->akademikTahunMulai();

                $startJan = 1;
                if ($bulanDari >= 7) {
                    for ($b = $bulanDari; $b <= 12; $b++) {
                        $label = $this->getBulanLabel(str_pad($b, 2, '0', STR_PAD_LEFT)) . ' ' . $tahunAwal;
                        $bulanOptions[str_pad($b, 2, '0', STR_PAD_LEFT)] = $label;
                    }
                } else {
                    $startJan = $bulanDari;
                }

                for ($b = $startJan; $b <= 6; $b++) {
                    $label = $this->getBulanLabel(str_pad($b, 2, '0', STR_PAD_LEFT)) . ' ' . ($tahunAwal + 1);
                    $bulanOptions[str_pad($b, 2, '0', STR_PAD_LEFT)] = $label;
                }

                $kalkulasiNominal = function (Get $get, Set $set) {
                    $jenis = $get('_jenis');
                    $potongan = (float) ($get('potongan') ?? 0);

                    if (in_array($jenis, ['daftar_ulang', 'daftar_masuk'])) {
                        $nominalAwal = (float) ($get('nominal_awal') ?? 0);
                        $pakaiPotongan = (bool) ($get('pakai_potongan') ?? false);
                        $potonganDu = $pakaiPotongan ? (float) ($get('potongan_du') ?? 0) : 0;
                        $set('nominal_bayar', max(0, $nominalAwal - $potonganDu));
                    } else {
                        $bulanDari   = (int) $get('_bulan_dari');
                        $bulanSampai = (int) ($get('sampai_bulan') ?? $bulanDari);
                        $sppPerBulan = (float) ($get('nominal_spp_per_bulan') ?? 0);

                        if ($bulanDari <= $bulanSampai) {
                            $jmlBulan = $bulanSampai - $bulanDari + 1;
                        } else {
                            $jmlBulan = (12 - $bulanDari + 1) + $bulanSampai;
                        }

                        $total = ($sppPerBulan - $potongan) * $jmlBulan;
                        $set('nominal_bayar', max(0, $total));
                    }
                };

                return [
                    Hidden::make('_mode'),
                    Hidden::make('_siswa_id'),
                    Hidden::make('_bulan_dari'),
                    Hidden::make('_tahun'),
                    Hidden::make('_jenis'),

                    Placeholder::make('_info_direct')
                        ->label('Pembayaran')
                        ->content(fn (Get $get) => match ($get('_jenis')) {
                            'daftar_masuk' => 'Biaya Pendaftaran — ' . $get('_tahun'),
                            'daftar_ulang' => 'Daftar Ulang + Juli ' . $get('_tahun'),
                            default        => 'SPP — ' . $this->getBulanLabel($get('_bulan_dari')) . ' ' . $get('_tahun'),
                        }),

                    // ── SPP: pilihan sampai bulan ──
                    Select::make('sampai_bulan')
                        ->label('Sampai Bulan')
                        ->hidden($isDu)
                        ->options($bulanOptions)
                        ->default($arguments['bulan'] ?? '08')
                        ->live()
                        ->afterStateUpdated($kalkulasiNominal),

                    // ── SPP: nominal per bulan ──
                    TextInput::make('nominal_spp_per_bulan')
                        ->label('Nominal SPP per Bulan (Rp)')
                        ->hidden($isDu)
                        ->numeric()->prefix('Rp')->default(0)
                        ->lazy()
                        ->afterStateUpdated($kalkulasiNominal)
                        ->helperText('Isi nominal SPP untuk satu bulan'),

                    // ── DU/BP: nominal awal ──
                    TextInput::make('nominal_awal')
                        ->label(fn (Get $get) => $get('_jenis') === 'daftar_masuk'
                            ? 'Nominal Biaya Pendaftaran (Rp)'
                            : 'Nominal Daftar Ulang (Rp)')
                        ->visible($isDu)
                        ->numeric()->prefix('Rp')->default(0)
                        ->lazy()
                        ->afterStateUpdated($kalkulasiNominal)
                        ->helperText(fn (Get $get) => $get('_jenis') === 'daftar_masuk'
                            ? 'Nominal Biaya Pendaftaran'
                            : 'Nominal Daftar Ulang sudah termasuk SPP Juli'),

                    // ── Potongan (SPP) — selalu tampil ──
                    TextInput::make('potongan')
                        ->label('Potongan per Bulan (Rp)')
                        ->numeric()->prefix('Rp')->default(0)
                        ->lazy()
                        ->hidden($isDu)
                        ->afterStateUpdated($kalkulasiNominal)
                        ->helperText('Potongan per bulan, dikalikan jumlah bulan'),

                    // ── Nominal Bayar (disabled, hasil kalkulasi) ──
                    TextInput::make('nominal_bayar')
                        ->label('Total Nominal Bayar (Rp)')
                        ->numeric()->prefix('Rp')->required()
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Hasil kalkulasi otomatis'),

                    // ── DU: checkbox + potongan (setelah nominal_bayar) ──
                    Checkbox::make('pakai_potongan')
                        ->label('Potongan / Diskon')
                        ->default(false)
                        ->live()
                        ->visible($isDu)
                        ->afterStateUpdated($kalkulasiNominal),

                    TextInput::make('potongan_du')
                        ->label('Potongan (Rp)')
                        ->numeric()->prefix('Rp')->default(0)
                        ->lazy()
                        ->visible(fn (Get $get) => $isDu($get) && $get('pakai_potongan'))
                        ->afterStateUpdated($kalkulasiNominal)
                        ->helperText('Isi nominal potongan jika ada'),

                    // ── Fields umum ──
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

                    DatePicker::make('tgl_bayar_struk')
                        ->label('Tanggal Bayar di Struk')
                        ->required()
                        ->default(now())
                        ->maxDate(now())
                        ->helperText('Tidak boleh lebih dari hari ini'),

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
                ];
            })
            ->action(function (array $data): void {
                $mode = $data['_mode'] ?? 'tagihan';

                if ($mode === 'direct') {
                    $this->prosesBayarDirect($data);
                } else {
                    $this->prosesBayarTagihan($data);
                }

                unset($this->tagihans, $this->history, $this->riwayatPerTahun, $this->sppMatrixKelas);
            });
    }

    // ─── Action: Ubah nominal SPP per bulan (khusus SPP) ─────────────────────

    public function editNominalAction(): Action
    {
        return Action::make('editNominal')
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->modalHeading('Ubah Nominal Tagihan')
            ->modalWidth('sm')
            ->modalSubmitActionLabel('Simpan Perubahan')
            ->fillForm(function (array $arguments): array {
                $tagihan = Tagihan::with('jenisPembayaran')->findOrFail($arguments['tagihan_id']);

                $detail = $tagihan->detail ?? [];
                $jumlahBulan = 1;
                if (count($detail) > 0) {
                    $jumlahBulan = collect($detail)
                        ->filter(fn ($item) => strtoupper($item['jenis'] ?? '') === 'SPP')
                        ->count();
                    if ($jumlahBulan < 1) $jumlahBulan = 1;
                }

                $tipe = $this->isDaftarUlang($tagihan) ? 'du' : ($this->isDaftarMasuk($tagihan) ? 'bp' : 'spp');

                return [
                    '_tagihan_id'       => $tagihan->id,
                    '_tipe'             => $tipe,
                    '_jumlah_bulan'     => $jumlahBulan,
                    '_bulan_label'      => $tagihan->bulan
                        ? $this->getBulanLabel($tagihan->bulan) . ' ' . $tagihan->tahun
                        : (string) $tagihan->tahun,
                    'nominal_per_bulan' => (float) ($tagihan->nominal_tagihan / $jumlahBulan),
                ];
            })
            ->form([
                Hidden::make('_tagihan_id'),
                Hidden::make('_tipe'),
                Hidden::make('_jumlah_bulan'),
                Hidden::make('_bulan_label'),

                Placeholder::make('_info')
                    ->label('Tagihan')
                    ->content(fn (Get $get) => match ($get('_tipe')) {
                        'du' => 'Daftar Ulang — ' . $get('_bulan_label'),
                        'bp' => 'Biaya Pendaftaran — ' . $get('_bulan_label'),
                        default => 'SPP — ' . $get('_bulan_label')
                            . ((int) $get('_jumlah_bulan') > 1 ? ' (× ' . $get('_jumlah_bulan') . ' bulan)' : ''),
                    }),

                TextInput::make('nominal_per_bulan')
                    ->label(fn (Get $get) => match ($get('_tipe')) {
                        'du' => 'Nominal Daftar Ulang (Rp)',
                        'bp' => 'Nominal Biaya Pendaftaran (Rp)',
                        default => 'Nominal SPP per Bulan (Rp)',
                    })
                    ->numeric()->prefix('Rp')->required()
                    ->live()
                    ->helperText(fn (Get $get) => match ($get('_tipe')) {
                        'du' => 'Total Daftar Ulang (sudah termasuk SPP Juli)',
                        'bp' => 'Total Biaya Pendaftaran',
                        default => ((int) $get('_jumlah_bulan') > 1
                            ? 'Akan dikalikan ' . $get('_jumlah_bulan') . ' bulan'
                            : 'Nominal untuk 1 bulan SPP'),
                    }),

                Placeholder::make('_total')
                    ->label('Total Tagihan (di tabel)')
                    ->content(function (Get $get) {
                        $perBulan = (float) ($get('nominal_per_bulan') ?? 0);
                        $total    = $perBulan * (int) ($get('_jumlah_bulan') ?? 1);
                        return 'Rp ' . number_format($total, 0, ',', '.');
                    }),
            ])
            ->action(function (array $data): void {
                $tagihan = Tagihan::with('jenisPembayaran')->findOrFail($data['_tagihan_id']);

                if (! $this->canEditTagihan($tagihan)) {
                    Notification::make()
                        ->title('Tagihan tidak dapat diedit')
                        ->body('Hanya tagihan SPP, atau Daftar Ulang / Biaya Pendaftaran yang belum pernah dibayar/cicil, yang bisa diubah nominalnya.')
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                $sebelumEdit = $tagihan->getOriginal();

                $jumlahBulan = max(1, (int) ($data['_jumlah_bulan'] ?? 1));
                $perBulan    = (float) ($data['nominal_per_bulan'] ?? 0);
                $tipe        = (string) ($data['_tipe'] ?? 'spp');
                $isOneTime   = in_array($tipe, ['du', 'bp']);

                if ($perBulan <= 0) {
                    Notification::make()
                        ->title('Nominal tidak valid')
                        ->body('Nominal harus lebih dari 0.')
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                $total = $isOneTime ? $perBulan : ($perBulan * $jumlahBulan);

                $detail = $tagihan->detail ?? [];
                if (count($detail) > 0) {
                    $targetJenis = match ($tipe) {
                        'du' => ['DAFTAR ULANG'],
                        'bp' => ['BIAYA PENDAFTARAN', 'DAFTAR MASUK'],
                        default => ['SPP'],
                    };
                    foreach ($detail as &$item) {
                        if (in_array(strtoupper($item['jenis'] ?? ''), $targetJenis)) {
                            $item['nominal'] = $perBulan;
                        }
                    }
                    unset($item);
                    $tagihan->detail = $detail;
                }

                $tagihan->fill(['nominal_tagihan' => $total])->save();

                $sesudahEdit = $tagihan->getAttributes();
                \App\Models\TagihanLog::catat(
                    aksi: 'edit',
                    tagihanId: $tagihan->id,
                    sebelum: $sebelumEdit,
                    sesudah: $sesudahEdit,
                    keterangan: $this->tagihanLogKeterangan($tagihan, 'Edit tagihan'),
                );

                unset($this->tagihans, $this->history, $this->riwayatPerTahun, $this->sppMatrixKelas);

                $body = match ($tipe) {
                    'du' => 'Daftar Ulang: Rp ' . number_format($total, 0, ',', '.'),
                    'bp' => 'Biaya Pendaftaran: Rp ' . number_format($total, 0, ',', '.'),
                    default => 'SPP: Rp ' . number_format($perBulan, 0, ',', '.')
                        . ($jumlahBulan > 1 ? ' × ' . $jumlahBulan . ' bulan' : '')
                        . ' — Total: Rp ' . number_format($total, 0, ',', '.'),
                };

                Notification::make()
                    ->title('Nominal tagihan diperbarui ✓')
                    ->body($body)
                    ->success()->send();
            });
    }

    // ─── Action: Hapus tagihan (verifikasi sandi + belum pernah dibayar) ─────

    public function hapusTagihanAction(): Action
    {
        return Action::make('hapusTagihan')
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->modalHeading('Hapus Tagihan')
            ->modalDescription('Tindakan ini tidak dapat dibatalkan. Hanya bisa dihapus jika tagihan belum dibayar sama sekali.')
            ->modalSubmitActionLabel('Ya, Hapus')
            ->color('danger')
            ->modalWidth('sm')
            ->fillForm(function (array $arguments): array {
                $tagihan = Tagihan::with('jenisPembayaran', 'siswa')->findOrFail($arguments['tagihan_id']);
                return [
                    '_tagihan_id' => $tagihan->id,
                    '_info'       => $this->tagihanLogKeterangan($tagihan, 'Tagihan'),
                    'sandi'       => '',
                ];
            })
            ->form([
                Hidden::make('_tagihan_id'),

                Placeholder::make('_info')
                    ->label('Tagihan yang akan dihapus')
                    ->content(fn (Get $get) => $get('_info')),

                TextInput::make('sandi')
                    ->label('Konfirmasi Sandi')
                    ->password()
                    ->required()
                    ->helperText('Masukkan sandi akun Anda untuk mengonfirmasi'),
            ])
            ->action(function (array $data): void {
                $tagihan = Tagihan::findOrFail($data['_tagihan_id']);

                if (! Hash::check((string) ($data['sandi'] ?? ''), auth()->user()->password)) {
                    Notification::make()->title('Sandi salah')->danger()->send();
                    $this->halt();
                    return;
                }

                if (Pembayaran::where('tagihan_id', $tagihan->id)->exists()) {
                    Notification::make()
                        ->title('Tagihan tidak bisa dihapus')
                        ->body('Tagihan ini sudah memiliki pembayaran/cicilan.')
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                $keterangan = $this->tagihanLogKeterangan($tagihan, 'Hapus tagihan');
                $tagihan->delete();

                unset($this->tagihans, $this->history, $this->riwayatPerTahun, $this->sppMatrixKelas);

                Notification::make()
                    ->title('Tagihan dihapus ✓')
                    ->body($keterangan . ' — tercatat di log tagihan.')
                    ->success()->send();
            });
    }

    protected function prosesBayarTagihan(array $data): void
    {
        $tagihan  = Tagihan::with('jenisPembayaran')->findOrFail($data['_tagihan_id']);
        $nominal  = (float) $data['nominal_bayar'];
        $potongan = (float) ($data['potongan'] ?? 0);
        $isSpp    = $this->isSppByJenis($tagihan->jenisPembayaran?->nama);

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

        if (!$isSpp && $potongan > 0 && abs(($nominal + $potongan) - $tagihan->nominal_tagihan) > 1) {
            Notification::make()
                ->title('Potongan hanya untuk pelunasan penuh')
                ->body('Nominal bayar (Rp ' . number_format($nominal, 0, ',', '.') . ') + potongan (Rp ' . number_format($potongan, 0, ',', '.') . ') harus sama dengan nominal tagihan (Rp ' . number_format($tagihan->nominal_tagihan, 0, ',', '.') . ').')
                ->danger()->send();
            $this->halt();
            return;
        }

        if ($potongan > $tagihan->nominal_tagihan) {
            Notification::make()
                ->title('Potongan melebihi tagihan')
                ->body('Potongan Rp ' . number_format($potongan, 0, ',', '.') . ' melebihi nominal tagihan Rp ' . number_format($tagihan->nominal_tagihan, 0, ',', '.') . '.')
                ->danger()->send();
            $this->halt();
            return;
        }

        $lunas = $nominal >= ($tagihan->nominal_tagihan - $potongan - 1);

        $siswaModel = $this->selectedSiswa ?? Siswa::find($tagihan->siswa_id);

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
        $this->buatPdfLink($pembayaran->id);

        if ($lunas) {
            $tagihan->update([
                'status'          => 'lunas',
                'nominal_tagihan' => 0,
            ]);
        } else {
            $tagihan->update(['nominal_tagihan' => $tagihan->nominal_tagihan - $nominal]);
        }

        $jenisNama  = $tagihan->jenisPembayaran?->nama ?? '—';
        $bulanLabel = $tagihan->bulan ? " ({$this->getBulanLabel($tagihan->bulan)})" : '';

        $body = 'Rp ' . number_format($nominal, 0, ',', '.') . " — {$jenisNama}{$bulanLabel}";
        if ($potongan > 0 && $lunas) {
            $body .= ' (potongan Rp ' . number_format($potongan, 0, ',', '.') . ')';
        }

        Notification::make()
            ->title($lunas ? 'Pembayaran Lunas ✓' : 'Cicilan Berhasil Disimpan')
            ->body($body)
            ->success()->send();
    }

    protected function prosesBayarDirect(array $data): void
    {
        $siswaId   = (int) $data['_siswa_id'];
        $bulanDari = $data['_bulan_dari'];
        $tahun     = $data['_tahun'];
        $jenis     = $data['_jenis'];
        $isBiayaSekali = in_array($jenis, ['daftar_ulang', 'daftar_masuk']);
        $potongan  = (float) ($data[$isBiayaSekali ? 'potongan_du' : 'potongan'] ?? 0);

        if ($isBiayaSekali) {
            $nominalBayar = (float) ($data['nominal_bayar'] ?? 0);
            if ($nominalBayar <= 0) {
                Notification::make()->title('Nominal tidak valid')->danger()->send();
                $this->halt();
                return;
            }

            $isDaftarMasuk = $jenis === 'daftar_masuk';
            $jpId = $isDaftarMasuk ? 1 : JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
            $siswa = Siswa::find($siswaId);

            $pembayaran = Pembayaran::create([
                'siswa_id'            => $siswaId,
                'jenis_pembayaran_id' => $jpId,
                'tagihan_id'          => null,
                'bulan'               => null,
                'tahun'               => $tahun,
                'nominal'             => $nominalBayar,
                'tanggal_bayar'       => $data['tgl_bayar_struk'] ?? now(),
                'status'              => 'lunas',
                'no_ref'              => $data['no_ref'] ?: null,
                'tgl_bayar_struk'     => $data['tgl_bayar_struk'] ?? null,
                'potongan'              => $potongan,
                'bukti_bayar'           => $data['bukti_bayar'] ?? null,
                'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
                'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
                'created_by'            => auth()->id(),
            ]);

            if ($siswa) {
                $pembayaran->setRelation('siswa', $siswa);
                $pembayaran->setRelation('jenisPembayaran', JenisPembayaran::find($jpId));
            }
            KasHarian::postingDariPembayaran($pembayaran);
            $this->buatPdfLink($pembayaran->id);

            $title = $isDaftarMasuk ? 'Pembayaran Biaya Pendaftaran Berhasil' : 'Pembayaran Daftar Ulang Lunas ✓';
            $body = 'Rp ' . number_format($nominalBayar, 0, ',', '.');
            if ($potongan > 0) {
                $body .= ' (potongan Rp ' . number_format($potongan, 0, ',', '.') . ')';
            }
            $body .= $isDaftarMasuk ? '' : ' — sudah termasuk SPP Juli';
            Notification::make()->title($title)->body($body)->success()->send();
            return;
        }

        // ── SPP Direct Pay ──
        $nominalBayar    = (float) ($data['nominal_bayar'] ?? 0);
        $sppPerBulan     = (float) ($data['nominal_spp_per_bulan'] ?? 0);
        $bulanSampai     = $data['sampai_bulan'] ?? $bulanDari;

        if ($nominalBayar <= 0 || $sppPerBulan <= 0) {
            Notification::make()->title('Nominal tidak valid')->danger()->send();
            $this->halt();
            return;
        }

        $tahunMulai = (int) $bulanDari <= 6 ? (int) $tahun - 1 : (int) $tahun;
        $bulanRange = $this->rangeBulan($bulanDari, $bulanSampai, $tahunMulai);
        $jmlBulan   = count($bulanRange);
        $nominalPerBulan = $sppPerBulan - $potongan;
        $potonganPerBulan = $potongan;

        $jenisSppId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
        $siswa      = Siswa::find($siswaId);
        $totalNominal = 0;
        $firstPembayaran = null;
        $batchUuid = (string) \Illuminate\Support\Str::uuid();

        foreach ($bulanRange as $i => $m) {
            $pembayaran = Pembayaran::create([
                'siswa_id'            => $siswaId,
                'jenis_pembayaran_id' => $jenisSppId,
                'tagihan_id'          => null,
                'bulan'               => $m['bulan'],
                'tahun'               => $m['tahun'],
                'nominal'             => round($nominalPerBulan, 2),
                'tanggal_bayar'       => $data['tgl_bayar_struk'] ?? now(),
                'status'              => 'lunas',
                'no_ref'              => $data['no_ref'] ?: null,
                'tgl_bayar_struk'     => $data['tgl_bayar_struk'] ?? null,
                'potongan'              => round($potonganPerBulan, 2),
                'bukti_bayar'           => $data['bukti_bayar'] ?? null,
                'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
                'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
                'batch_uuid'            => $batchUuid,
                'created_by'            => auth()->id(),
            ]);

            if ($siswa) {
                $pembayaran->setRelation('siswa', $siswa);
                $pembayaran->setRelation('jenisPembayaran', JenisPembayaran::find($jenisSppId));
            }
            if (!$firstPembayaran) $firstPembayaran = $pembayaran;
            $this->buatPdfLink($pembayaran->id);
            $totalNominal += round($nominalPerBulan, 2);
        }

        $bulanAwalLabel  = $this->getBulanLabel($bulanDari);
        $bulanAkhirLabel = $this->getBulanLabel($bulanSampai);

        // KasHarian: 1 row untuk total multi-month
        $akunSpp = Akun::where('kode_akun', '4101')->first();
        $kelasLabel = $siswa->kelasSaatIni?->kelas ? ' Kls ' . $siswa->kelasSaatIni->kelas : '';
        $kasHarian = KasHarian::create([
            'tanggal'               => $data['tgl_bayar_struk'] ?? now(),
            'uraian'                => $siswa->nama . $kelasLabel . ' — SPP ' . $bulanAwalLabel . '–' . $bulanAkhirLabel . ' (' . $jmlBulan . ' bln)',
            'akun_id'               => $akunSpp?->id,
            'debit'                 => $totalNominal,
            'kredit'                => null,
            'source'                => 'pembayaran',
            'source_id'             => $firstPembayaran?->id,
            'no_ref'                => $data['no_ref'] ?: null,
            'rekening_tujuan'       => $data['rekening_tujuan'] ?? null,
            'nama_rekening_pengirim'=> $data['nama_rekening_pengirim'] ?? null,
            'bulan'                 => $bulanDari,
            'tahun'                 => (string) ((int) $bulanDari <= 6 ? $tahunMulai + 1 : $tahunMulai),
            'created_by'            => auth()->id(),
        ]);

        LogDanaMasuk::create([
            'kas_harian_id' => $kasHarian->id,
            'action'        => 'create',
            'uraian'        => $kasHarian->uraian,
            'data_lama'     => null,
            'data_baru'     => $kasHarian->only(['no_ref', 'rekening_tujuan', 'nama_rekening_pengirim', 'debit']),
            'created_by'    => auth()->id(),
        ]);

        Notification::make()
            ->title('Pembayaran SPP Berhasil ✓')
            ->body($jmlBulan . ' bulan (' . $bulanAwalLabel . '–' . $bulanAkhirLabel . ') — Rp ' . number_format($totalNominal, 0, ',', '.'))
            ->success()->send();
    }

    public function openBayarDirect(int $siswaId, string $bulan, string $tahun): void
    {
        $message = $this->checkPreviousUnpaid($siswaId, $bulan, $tahun);
        if ($message) {
            Notification::make()->title($message)->warning()->send();
            return;
        }
        $this->mountAction('bayar', [
            'siswa_id' => $siswaId,
            'bulan'    => $bulan,
            'tahun'    => $tahun,
            'jenis'    => 'spp',
        ]);
    }

    protected function checkPreviousUnpaid(int $siswaId, string $bulan, string $tahun): ?string
    {
        $tahunMulai = (int) $bulan <= 6 ? (int) $tahun - 1 : (int) $tahun;
        $isDta      = strtoupper($this->filterJenisSekolah) === 'DTA';
        $allMonths  = $isDta
            ? ['07','08','09','10','11','12','01','02','03','04','05','06']
            : ['08','09','10','11','12','01','02','03','04','05','06'];
        $jenisSppId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
        $jenisDuId  = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
        $unpaid     = [];

        $isNewEntry = $this->isNewEntryMatrix()
            || Tagihan::where('siswa_id', $siswaId)->where('jenis_pembayaran_id', 1)->exists();

        if (!$isDta) {
            if ($isNewEntry) {
                $bpPaid = Pembayaran::where('siswa_id', $siswaId)
                    ->where('jenis_pembayaran_id', 1)
                    ->whereIn('status', ['lunas', 'cicilan'])
                    ->exists();
                if (!$bpPaid) {
                    $unpaid[] = 'Biaya Pendaftaran';
                }
            } else {
                $duPaid = Pembayaran::where('siswa_id', $siswaId)
                    ->where('jenis_pembayaran_id', $jenisDuId)
                    ->where('tahun', (string) $tahunMulai)
                    ->exists();
                if (!$duPaid) {
                    $unpaid[] = 'Daftar Ulang ' . $tahunMulai . '/' . ($tahunMulai + 1);
                }
            }
        }

        foreach ($allMonths as $m) {
            if ($m === $bulan) break;
            $mt = (int) $m <= 6 ? $tahunMulai + 1 : $tahunMulai;
            $paid = Pembayaran::where('siswa_id', $siswaId)
                ->where('jenis_pembayaran_id', $jenisSppId)
                ->where('bulan', $m)
                ->where('tahun', (string) $mt)
                ->whereIn('status', ['lunas', 'cicilan'])
                ->exists();
            if (!$paid) {
                $unpaid[] = $this->getBulanLabel($m) . ' ' . $mt;
            }
        }

        if (count($unpaid) > 0) {
            return 'Lunasi tagihan bulan sebelumnya: ' . implode(', ', $unpaid);
        }
        return null;
    }

    protected function buatPdfLink(int $pembayaranId): void
    {
        \DB::table('pdf_links')->insert([
            'token'        => \Str::random(16),
            'pdf_id'       => $pembayaranId,
            'original_url' => "/kuitansi/{$pembayaranId}/pdf",
            'jenis'        => 'kuitansi',
            'jumlah_view'  => 0,
            'expired_at'   => now()->addDays(30),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function buatTagihanAction(): Action
    {
        $namaBulan = fn ($b) => $this->getBulanLabel($b);
        $rekap = function (Get $get, Set $set) {
            $nominalDu    = $get('du_checked') ? (float) ($get('nominal_du') ?? 0) : 0;
            $sppPerBulan  = (float) ($get('nominal_spp_per_bulan') ?? 0);
            $checkedCount = 0;
            foreach (['08','09','10','11','12','01','02','03','04','05','06'] as $b) {
                if ($get('bulan_' . $b)) $checkedCount++;
            }
            $total = ($nominalDu) + ($sppPerBulan * $checkedCount);
            $set('_total_tagihan', $total);
        };

        return Action::make('buatTagihan')
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->modalHeading('Buat Tagihan')
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Simpan Tagihan')
            ->fillForm(function (array $arguments): array {
                $siswa = Siswa::with('kelasSaatIni')->findOrFail($arguments['siswa_id']);
                $tahunMulai = $this->akademikTahunMulai();

                $kelas   = $siswa->kelasSaatIni?->kelas ?? '';
                $jenjang = $siswa->kelasSaatIni?->jenis_sekolah ?? $siswa->calon_jenis ?? '';
                $isNewEntry = $siswa->is_calon
                    || $jenjang === 'PAUD'
                    || ($jenjang === 'SD'  && str_starts_with($kelas, '1'))
                    || ($jenjang === 'SMP' && str_starts_with($kelas, '7'))
                    || Tagihan::where('siswa_id', $siswa->id)->where('jenis_pembayaran_id', 1)->exists();

                $jenisDuId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
                $bpPaid   = Pembayaran::where('siswa_id', $siswa->id)->where('jenis_pembayaran_id', 1)->whereIn('status', ['lunas', 'cicilan'])->exists();
                $duPaid   = Pembayaran::where('siswa_id', $siswa->id)->where('jenis_pembayaran_id', $jenisDuId)->where('tahun', $tahunMulai)->whereIn('status', ['lunas', 'cicilan'])->exists();
                $duBpPaid = $isNewEntry ? $bpPaid : $duPaid;

                $jenisSppId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
                $sppPaid = [];
                foreach (['08','09','10','11','12','01','02','03','04','05','06'] as $b) {
                    $t = (int) $b <= 6 ? $tahunMulai + 1 : $tahunMulai;
                    $sppPaid['bulan_' . $b] = Pembayaran::where('siswa_id', $siswa->id)
                        ->where('jenis_pembayaran_id', $jenisSppId)
                        ->where('bulan', $b)
                        ->where('tahun', (string) $t)
                        ->whereIn('status', ['lunas', 'cicilan'])
                        ->exists();
                }

                $data = [
                    '_siswa_id' => $siswa->id,
                    '_tahun'    => (string) $tahunMulai,
                    '_total_tagihan' => 0,
                    '_is_new_entry' => $isNewEntry,
                    '_du_bp_paid' => $duBpPaid,
                    'nominal_du' => null,
                    'nominal_spp_per_bulan' => null,
                ];
                foreach (['08','09','10','11','12','01','02','03','04','05','06'] as $b) {
                    $data['bulan_' . $b] = false;
                    $data['_spp_paid_' . $b] = $sppPaid['bulan_' . $b];
                }
                $data['du_checked'] = false;
                return $data;
            })
            ->form([
                Hidden::make('_siswa_id'),
                Hidden::make('_tahun'),
                Hidden::make('_total_tagihan'),
                Hidden::make('_is_new_entry'),
                Hidden::make('_du_bp_paid'),
                Hidden::make('_spp_paid_08'),
                Hidden::make('_spp_paid_09'),
                Hidden::make('_spp_paid_10'),
                Hidden::make('_spp_paid_11'),
                Hidden::make('_spp_paid_12'),
                Hidden::make('_spp_paid_01'),
                Hidden::make('_spp_paid_02'),
                Hidden::make('_spp_paid_03'),
                Hidden::make('_spp_paid_04'),
                Hidden::make('_spp_paid_05'),
                Hidden::make('_spp_paid_06'),

                Placeholder::make('_info_siswa')
                    ->label('Siswa')
                    ->content(fn (Get $get) => Siswa::find($get('_siswa_id'))?->nama . ' — ' . $this->filterKelas),

                \Filament\Forms\Components\Section::make('Pilih Tagihan')
                    ->schema([
                        \Filament\Forms\Components\Checkbox::make('du_checked')
                            ->label(fn (Get $get) => $get('_is_new_entry') ? 'Biaya Pendaftaran' : 'Daftar Ulang + Juli')
                            ->live()
                            ->afterStateUpdated($rekap)
                            ->visible(fn (Get $get) => !$get('_du_bp_paid')),

                        TextInput::make('nominal_du')
                            ->label(fn (Get $get) => $get('_is_new_entry') ? 'Nominal Biaya Pendaftaran (Rp)' : 'Nominal Daftar Ulang (Rp)')
                            ->numeric()->prefix('Rp')->default(null)
                            ->visible(fn (Get $get) => $get('du_checked') && !$get('_du_bp_paid'))
                            ->lazy()
                            ->afterStateUpdated($rekap)
                            ->helperText(fn (Get $get) => $get('_is_new_entry') ? '' : 'Sudah termasuk SPP Juli'),

                        \Filament\Forms\Components\Grid::make(4)
                            ->schema([
                                \Filament\Forms\Components\Checkbox::make('bulan_08')->label('Agu')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_08')),
                                \Filament\Forms\Components\Checkbox::make('bulan_09')->label('Sep')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_09')),
                                \Filament\Forms\Components\Checkbox::make('bulan_10')->label('Okt')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_10')),
                                \Filament\Forms\Components\Checkbox::make('bulan_11')->label('Nov')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_11')),
                                \Filament\Forms\Components\Checkbox::make('bulan_12')->label('Des')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_12')),
                                \Filament\Forms\Components\Checkbox::make('bulan_01')->label('Jan')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_01')),
                                \Filament\Forms\Components\Checkbox::make('bulan_02')->label('Feb')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_02')),
                                \Filament\Forms\Components\Checkbox::make('bulan_03')->label('Mar')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_03')),
                                \Filament\Forms\Components\Checkbox::make('bulan_04')->label('Apr')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_04')),
                                \Filament\Forms\Components\Checkbox::make('bulan_05')->label('Mei')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_05')),
                                \Filament\Forms\Components\Checkbox::make('bulan_06')->label('Jun')->live()->afterStateUpdated($rekap)->visible(fn (Get $get) => !$get('_spp_paid_06')),
                            ]),

                        TextInput::make('nominal_spp_per_bulan')
                            ->label('Nominal SPP per Bulan (Rp)')
                            ->numeric()->prefix('Rp')->default(null)
                            ->lazy()
                            ->afterStateUpdated($rekap)
                            ->helperText('Akan dikali jumlah bulan yang dicentang'),
                    ]),

                \Filament\Forms\Components\Section::make('Ringkasan')
                    ->schema([
                        Placeholder::make('_ringkasan')
                            ->label('Total Tagihan')
                            ->content(function (Get $get) {
                                $total = (float) ($get('_total_tagihan') ?? 0);
                                $jmlBulan = 0;
                                foreach (['08','09','10','11','12','01','02','03','04','05','06'] as $b) {
                                    if ($get('bulan_' . $b)) $jmlBulan++;
                                }
                                $parts = [];
                                if ($get('du_checked')) {
                                    $parts[] = $get('_is_new_entry') ? 'BP' : 'DU';
                                }
                                if ($jmlBulan > 0) $parts[] = $jmlBulan . ' bln SPP';
                                $label = count($parts) > 0 ? implode(' + ', $parts) : '(belum ada pilihan)';
                                return 'Rp ' . number_format($total, 0, ',', '.') . ' — ' . $label;
                            }),
                    ]),
            ])
            ->action(function (array $data): void {
                $siswaId   = (int) $data['_siswa_id'];
                $tahun     = $data['_tahun'];
                $total     = (float) ($data['_total_tagihan'] ?? 0);
                $isNewEntry = $data['_is_new_entry'] ?? false;

                if ($total <= 0) {
                    Notification::make()->title('Pilih item dan isi nominal terlebih dahulu')->danger()->send();
                    $this->halt();
                    return;
                }

                $namaDuBp = $isNewEntry ? 'Biaya Pendaftaran' : 'Daftar Ulang';

                $duplicated = [];
                if ($data['du_checked'] ?? false) {
                    $jpIdDuCheck = $isNewEntry ? 1 : JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
                    $exists = Tagihan::where('siswa_id', $siswaId)
                        ->where('jenis_pembayaran_id', $jpIdDuCheck)
                        ->where('tahun', $tahun)
                        ->where('status', 'belum_bayar')
                        ->exists();
                    if ($exists) $duplicated[] = $namaDuBp;
                }

                $checkedMonths = [];
                $tahunMulaiInt = (int) $tahun;
                foreach (['08','09','10','11','12','01','02','03','04','05','06'] as $b) {
                    if ($data['bulan_' . $b] ?? false) {
                        $checkedMonths[] = $b;
                        $bulanTahun = (int) $b <= 6 ? (string) ($tahunMulaiInt + 1) : $tahun;
                        $exists = Tagihan::where('siswa_id', $siswaId)
                            ->where('jenis_pembayaran_id', JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id'))
                            ->where('bulan', $b)
                            ->where('tahun', $bulanTahun)
                            ->where('status', 'belum_bayar')
                            ->exists();
                        if ($exists) $duplicated[] = $this->getBulanLabel($b) . ' ' . $bulanTahun;
                    }
                }

                if (count($duplicated) > 0) {
                    Notification::make()
                        ->title('Tagihan sudah ada')
                        ->body('Item berikut sudah memiliki tagihan aktif: ' . implode(', ', $duplicated) . '. Hapus atau lunasi tagihan lama terlebih dahulu.')
                        ->danger()->send();
                    $this->halt();
                    return;
                }

                $detail = [];

                if ($data['du_checked'] ?? false) {
                    $nominalDu = (float) ($data['nominal_du'] ?? 0);
                    $detail[] = [
                        'jenis'   => $namaDuBp,
                        'bulan'   => null,
                        'tahun'   => $tahun,
                        'nominal' => $nominalDu,
                    ];
                }

                $sppPerBulan = (float) ($data['nominal_spp_per_bulan'] ?? 0);
                $tahunMulai  = (int) $tahun;
                foreach ($checkedMonths as $b) {
                    $t = (int) $b <= 6 ? $tahunMulai + 1 : $tahunMulai;
                    $detail[] = [
                        'jenis'   => 'SPP',
                        'bulan'   => $b,
                        'tahun'   => (string) $t,
                        'nominal' => $sppPerBulan,
                    ];
                }

                $hasSpp = collect($detail)->contains(fn ($item) => strtoupper($item['jenis'] ?? '') === 'SPP');

                if ($hasSpp) {
                    $jenisPembayaranId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['spp'])->value('id');
                } elseif ($isNewEntry) {
                    $jenisPembayaranId = 1; // Daftar Masuk (Biaya Pendaftaran)
                } else {
                    $jenisPembayaranId = JenisPembayaran::whereRaw('LOWER(nama) = ?', ['daftar ulang'])->value('id');
                }

                $bulanTagihan = null;
                foreach ($detail as $item) {
                    if (($item['jenis'] ?? '') === 'SPP' && !empty($item['bulan'])) {
                        $bulanTagihan = $item['bulan'];
                        break;
                    }
                }

                $tagihan = Tagihan::create([
                    'siswa_id'            => $siswaId,
                    'jenis_pembayaran_id' => $jenisPembayaranId,
                    'bulan'               => $bulanTagihan,
                    'tahun'               => $tahun,
                    'nominal_tagihan'     => $total,
                    'status'              => 'belum_bayar',
                    'detail'              => $detail,
                ]);

                $encId = urlencode(\App\Http\Controllers\TagihanPublicController::encryptId($tagihan->id));
                $pdfUrl   = url("/tagihan/{$tagihan->id}/pdf");
                $shareUrl = url("/tagihan/share/{$encId}");
                $waUrl    = 'https://wa.me/?text=' . urlencode(
                    "Yth. Orang Tua/Wali " . ($tagihan->siswa?->nama ?? 'Siswa') . "\n\n"
                    . "Berikut tagihan sekolah:\n"
                    . "Total: Rp " . number_format($total, 0, ',', '.') . "\n"
                    . count($detail) . " item\n\n"
                    . "Lihat detail: {$shareUrl}\n\nTerima kasih."
                );

                Notification::make()
                    ->title('Tagihan berhasil dibuat ✓')
                    ->body('Total: Rp ' . number_format($total, 0, ',', '.') . ' (' . count($detail) . ' item)')
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('cetak_pdf')
                            ->label('Cetak PDF')
                            ->url($pdfUrl, shouldOpenInNewTab: true)
                            ->color('warning'),
                        \Filament\Notifications\Actions\Action::make('share_wa')
                            ->label('Share WA')
                            ->url($waUrl, shouldOpenInNewTab: true)
                            ->color('success'),
                        \Filament\Notifications\Actions\Action::make('salin_link')
                            ->label('Salin Link')
                            ->extraAttributes([
                                'x-on:click' => \App\Filament\Resources\TagihanResource::clipboardJs($shareUrl),
                            ])
                            ->color('gray'),
                    ])
                    ->send();

                unset($this->sppMatrixKelas);
            });
    }

    // ─── Action: Buat Tagihan per bulan dari baris rekap ───────────────────
public function buatTagihanBulanAction(): Action
    {
        return Action::make('buatTagihanBulan')
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->modalHeading('Buat Tagihan Bulanan')
            ->modalWidth('sm')
            ->modalSubmitActionLabel('Buat & Cetak PDF')
            ->fillForm(function (array $arguments): array {
                return [
                    'bulan'   => $arguments['bulan'],
                    'tahun'   => $arguments['tahun'],
                    'nominal' => 0,
                ];
            })
            ->form([
                \Filament\Forms\Components\Hidden::make('bulan'),
                \Filament\Forms\Components\Hidden::make('tahun'),

                Placeholder::make('_info')
                    ->label('Jenis')
                    ->content(fn (Get $get) => $get('bulan')
                        ? $this->getBulanLabel($get('bulan')) . ' ' . $get('tahun')
                        : ($this->isNewEntryMatrix() ? 'Biaya Pendaftaran ' . $get('tahun') : 'Daftar Ulang ' . $get('tahun')))
                    ->extraAttributes(['class' => '!font-semibold']),

                Placeholder::make('_kelas')
                    ->label('Kelas')
                    ->content(fn () => $this->filterJenisSekolah . ' — ' . $this->filterKelas),

                TextInput::make('nominal')
                    ->label(fn (Get $get) => $get('bulan')
                        ? 'Nominal SPP (Rp)'
                        : ($this->isNewEntryMatrix() ? 'Nominal Biaya Pendaftaran (Rp)' : 'Nominal Daftar Ulang (Rp)'))
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default(0),
            ])
            ->action(function (array $data): void {
                $bulan   = $data['bulan'];
                $tahun   = $data['tahun'];
                $nominal = (float) $data['nominal'];
                $isDu    = empty($bulan);

                if ($nominal <= 0) {
                    Notification::make()->title('Nominal tidak valid')->danger()->send();
                    $this->halt();
                    return;
                }

                $isNewEntry = $this->isNewEntryMatrix();

                $jenisNama = $isDu ? ($isNewEntry ? 'daftar masuk' : 'daftar ulang') : 'spp';
                $jenisId   = JenisPembayaran::whereRaw('LOWER(nama) = ?', [$jenisNama])->value('id');

                $siswaIds = Siswa::where('status_aktif', true)
                    ->whereHas('kelasSaatIni', fn($q) => $q
                        ->where('jenis_sekolah', $this->filterJenisSekolah)
                        ->where('kelas', $this->filterKelas))
                    ->pluck('id');

                if ($isDu && !$isNewEntry) {
                    $baruIds = Tagihan::where('jenis_pembayaran_id', 1)
                        ->whereIn('siswa_id', $siswaIds)
                        ->pluck('siswa_id');
                    $siswaIds = $siswaIds->diff($baruIds);
                }

                $queryPaid = Pembayaran::whereIn('siswa_id', $siswaIds)
                    ->where('jenis_pembayaran_id', $jenisId);
                if ($isDu) {
                    $queryPaid->where('tahun', $tahun);
                } else {
                    $queryPaid->where('bulan', $bulan)->where('tahun', $tahun);
                }
                $paidIds = $queryPaid->whereIn('status', ['lunas', 'cicilan'])
                    ->pluck('siswa_id')
                    ->unique();

                $queryExisting = Tagihan::whereIn('siswa_id', $siswaIds)
                    ->where('jenis_pembayaran_id', $jenisId)
                    ->where('status', 'belum_bayar');
                if ($isDu) {
                    $queryExisting->where('tahun', $tahun);
                } else {
                    $queryExisting->where('bulan', $bulan)->where('tahun', $tahun);
                }
                $existingTagihanIds = $queryExisting->pluck('siswa_id');

                $needTagihanIds = $siswaIds->diff($paidIds)->diff($existingTagihanIds);

                if ($needTagihanIds->isEmpty()) {
                    Notification::make()
                        ->title('Semua siswa sudah memiliki tagihan')
                        ->warning()
                        ->send();
                    return;
                }

                foreach ($needTagihanIds as $siswaId) {
                    Tagihan::create([
                        'siswa_id'            => $siswaId,
                        'jenis_pembayaran_id' => $jenisId,
                        'bulan'               => $isDu ? null : $bulan,
                        'tahun'               => $tahun,
                        'nominal_tagihan'     => $nominal,
                        'status'              => 'belum_bayar',
                    ]);
                }

                unset($this->sppMatrixKelas);

                $label   = $isDu
                    ? ($isNewEntry ? 'Biaya Pendaftaran ' . $tahun : 'Daftar Ulang ' . $tahun)
                    : $this->getBulanLabel($bulan) . ' ' . $tahun;
                $jumlah  = count($needTagihanIds);

                Notification::make()
                    ->title($jumlah . ' tagihan berhasil dibuat ✓')
                    ->body($label . ' — Rp ' . number_format($nominal, 0, ',', '.') . ' x ' . $jumlah . ' siswa')
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('cetak_pdf')
                            ->label('Cetak PDF')
                            ->url(route('tagihan.matrix.pdf', [
                                'jenis_sekolah' => $this->filterJenisSekolah,
                                'kelas'         => $this->filterKelas,
                                'bulan'         => $bulan,
                                'tahun'         => $tahun,
                            ]), shouldOpenInNewTab: true)
                            ->color('warning'),
                        \Filament\Notifications\Actions\Action::make('cetak_csv')
                            ->label('Cetak CSV')
                            ->url(route('tagihan.matrix.csv', [
                                'jenis_sekolah' => $this->filterJenisSekolah,
                                'kelas'         => $this->filterKelas,
                                'bulan'         => $bulan,
                                'tahun'         => $tahun,
                            ]), shouldOpenInNewTab: true)
                            ->color('success'),
                    ])
                    ->send();
            });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function akademikTahunMulai(): int
    {
        $now = now();
        return $now->month >= 7 ? $now->year : $now->year - 1;
    }

    protected function isNewEntryMatrix(): bool
    {
        return $this->filterJenisSekolah === 'PAUD'
            || ($this->filterJenisSekolah === 'SD'  && str_starts_with($this->filterKelas, '1'))
            || ($this->filterJenisSekolah === 'SMP' && str_starts_with($this->filterKelas, '7'));
    }

    public function isSpp(Tagihan $tagihan): bool
    {
        return $this->isSppByJenis($tagihan->jenisPembayaran?->nama);
    }

    public function isSppByJenis(?string $nama): bool
    {
        return strtolower($nama ?? '') === 'spp';
    }

    public function isPureSpp(Tagihan $tagihan): bool
    {
        $detail = $tagihan->detail ?? [];

        if (count($detail) === 0) {
            return $this->isSppByJenis($tagihan->jenisPembayaran?->nama);
        }

        return collect($detail)
            ->every(fn ($item) => strtoupper($item['jenis'] ?? '') === 'SPP');
    }

    public function isDaftarUlangByJenis(?string $nama): bool
    {
        return strtolower($nama ?? '') === 'daftar ulang';
    }

    public function isDaftarUlang(Tagihan $tagihan): bool
    {
        return $this->isDaftarUlangByJenis($tagihan->jenisPembayaran?->nama);
    }

    public function isDaftarMasukByJenis(?string $nama): bool
    {
        return strtolower($nama ?? '') === 'daftar masuk';
    }

    public function isDaftarMasuk(Tagihan $tagihan): bool
    {
        return $this->isDaftarMasukByJenis($tagihan->jenisPembayaran?->nama);
    }

    public function canEditTagihan(Tagihan $tagihan): bool
    {
        if ($this->isPureSpp($tagihan)) {
            return true;
        }

        $oneTime = $this->isDaftarUlang($tagihan) || $this->isDaftarMasuk($tagihan);

        if ($oneTime) {
            return ! Pembayaran::where('tagihan_id', $tagihan->id)->exists();
        }

        return false;
    }

    public function canHapusTagihan(Tagihan $tagihan): bool
    {
        if ($tagihan->status !== 'belum_bayar') {
            return false;
        }

        return ! Pembayaran::where('tagihan_id', $tagihan->id)->exists();
    }

    protected function tagihanLogKeterangan(Tagihan $tagihan, string $prefix): string
    {
        $detail = $tagihan->detail ?? [];
        $jenis  = count($detail) > 0
            ? 'Multi (' . count($detail) . ' item)'
            : ($tagihan->jenisPembayaran?->nama ?? '—');

        $periode = $tagihan->bulan
            ? $this->getBulanLabel($tagihan->bulan) . ' ' . $tagihan->tahun
            : (string) $tagihan->tahun;

        return "{$prefix}: {$jenis} — {$periode} — {$tagihan->siswa?->nama} ({$tagihan->siswa?->nis})";
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

    protected function rangeBulan(string $dari, string $sampai, int $tahunMulai): array
    {
        $start  = (int) $dari;
        $end    = (int) $sampai;
        $result = [];

        if ($start <= $end) {
            for ($m = $start; $m <= $end; $m++) {
                $t = $m <= 6 ? $tahunMulai + 1 : $tahunMulai;
                $result[] = [
                    'bulan' => str_pad($m, 2, '0', STR_PAD_LEFT),
                    'tahun' => (string) $t,
                ];
            }
        } else {
            for ($m = $start; $m <= 12; $m++) {
                $result[] = [
                    'bulan' => str_pad($m, 2, '0', STR_PAD_LEFT),
                    'tahun' => (string) $tahunMulai,
                ];
            }
            for ($m = 1; $m <= $end; $m++) {
                $result[] = [
                    'bulan' => str_pad($m, 2, '0', STR_PAD_LEFT),
                    'tahun' => (string) ($tahunMulai + 1),
                ];
            }
        }

        return $result;
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
            'paud' => $tingkat === 1 ? 'A' : 'B',
            'tk'   => $tingkat === 1 ? 'A' : 'B',
            'SD'   => "Kelas {$tingkat}",
            'SMP'  => "Kelas {$tingkat}",
            'DTA'  => "Tingkat {$tingkat}",
            'PAUD' => $tingkat === 1 ? 'A' : 'B',
            'TK'   => $tingkat === 1 ? 'A' : 'B',
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
