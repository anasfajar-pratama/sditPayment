<x-filament-panels::page>
<div class="space-y-6">

    {{-- Periode Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Ringkasan</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $this->getBulanLabel() }} · Diperbarui {{ now()->format('H:i') }}</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 1 — 4 QUICK STATS
    ══════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem;">

        {{-- Total Siswa Aktif --}}
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px 18px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:600; text-transform:uppercase;
                             letter-spacing:.5px; color:#3b82f6;">Siswa Aktif</span>
                <div style="background:#dbeafe; border-radius:8px; padding:5px;">
                    <x-heroicon-o-academic-cap style="width:16px;height:16px;color:#2563eb;"/>
                </div>
            </div>
            <div style="font-size:28px; font-weight:700; color:#1e40af; line-height:1;">
                {{ number_format($this->totalSiswaAktif) }}
            </div>
            <div style="font-size:11px; color:#93c5fd; margin-top:4px;">
                Calon: {{ $this->totalCalonSiswa }} siswa
            </div>
        </div>

        {{-- Pemasukan Hari Ini --}}
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 18px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:600; text-transform:uppercase;
                             letter-spacing:.5px; color:#22c55e;">Bayar Hari Ini</span>
                <div style="background:#dcfce7; border-radius:8px; padding:5px;">
                    <x-heroicon-o-arrow-trending-up style="width:16px;height:16px;color:#16a34a;"/>
                </div>
            </div>
            <div style="font-size:18px; font-weight:700; color:#15803d; line-height:1.2;">
                Rp {{ number_format($this->pemasukanHariIni, 0, ',', '.') }}
            </div>
            <div style="font-size:11px; color:#86efac; margin-top:4px;">
                Bulan ini: Rp {{ number_format($this->pemasukanBulanIni, 0, ',', '.') }}
            </div>
        </div>

        {{-- Siswa Belum Bayar (dari Master Biaya) --}}
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px 18px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:600; text-transform:uppercase;
                             letter-spacing:.5px; color:#f59e0b;">Belum Bayar</span>
                <div style="background:#fef3c7; border-radius:8px; padding:5px;">
                    <x-heroicon-o-exclamation-circle style="width:16px;height:16px;color:#d97706;"/>
                </div>
            </div>
            @php $bb = $this->belumBayarBulanIni; @endphp
            <div style="font-size:28px; font-weight:700; color:#b45309; line-height:1;">
                {{ $bb['spp_unpaid'] + $bb['du_unpaid'] }}
            </div>
            <div style="font-size:11px; color:#fcd34d; margin-top:4px;">
                SPP: {{ $bb['spp_unpaid'] }} siswa &middot; DU: {{ $bb['du_unpaid'] }} siswa
            </div>
        </div>

        {{-- Karyawan Aktif --}}
        <div style="background:#fdf4ff; border:1px solid #e9d5ff; border-radius:12px; padding:16px 18px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:600; text-transform:uppercase;
                             letter-spacing:.5px; color:#a855f7;">Karyawan</span>
                <div style="background:#f3e8ff; border-radius:8px; padding:5px;">
                    <x-heroicon-o-users style="width:16px;height:16px;color:#9333ea;"/>
                </div>
            </div>
            <div style="font-size:28px; font-weight:700; color:#7e22ce; line-height:1;">
                {{ number_format($this->totalKaryawanAktif) }}
            </div>
            <div style="font-size:11px; color:#d8b4fe; margin-top:4px;">
                Orang aktif
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 2 — KAS BULAN INI (3 kartu)
    ══════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem;">

        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                         letter-spacing:.5px; color:#6b7280; margin-bottom:10px;">
                Pemasukan Bulan Ini
            </div>
            <div style="font-size:24px; font-weight:700; color:#15803d;">
                Rp {{ number_format($this->pemasukanBulanIni, 0, ',', '.') }}
            </div>
            <div style="margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6;
                         font-size:11px; color:#9ca3af;">
                Donasi: Rp {{ number_format($this->donasiBulanIni, 0, ',', '.') }}
            </div>
        </div>

        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                         letter-spacing:.5px; color:#6b7280; margin-bottom:10px;">
                Pengeluaran Bulan Ini
            </div>
            <div style="font-size:24px; font-weight:700; color:#dc2626;">
                Rp {{ number_format($this->pengeluaranBulanIni, 0, ',', '.') }}
            </div>
            <div style="margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6;
                         font-size:11px; color:#9ca3af;">
                Termasuk gaji & operasional
            </div>
        </div>

        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px 24px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                         letter-spacing:.5px; color:#6b7280; margin-bottom:10px;">
                Saldo Kas Bulan Ini
            </div>
            <div style="font-size:24px; font-weight:700;
                         color:{{ $this->saldoBulanIni >= 0 ? '#2563eb' : '#dc2626' }};">
                @if($this->saldoBulanIni < 0)–@endif
                Rp {{ number_format(abs($this->saldoBulanIni), 0, ',', '.') }}
            </div>
            <div style="margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6;
                         font-size:11px; color:#9ca3af;">
                Pemasukan – Pengeluaran
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 3 — SISWA PER JENJANG + GRAFIK 6 BULAN
    ══════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:1.25rem;">

        {{-- Siswa Per Jenjang --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase;
                         letter-spacing:.5px; color:#374151; margin-bottom:14px;">
                Siswa per Jenjang
            </div>

            @php
                $jenjangConfig = [
                    'SD'   => ['color' => '#3b82f6', 'bg' => '#eff6ff', 'text' => '#1d4ed8'],
                    'SMP'  => ['color' => '#8b5cf6', 'bg' => '#f5f3ff', 'text' => '#6d28d9'],
                    'DTA'  => ['color' => '#f59e0b', 'bg' => '#fffbeb', 'text' => '#b45309'],
                    'PAUD' => ['color' => '#ec4899', 'bg' => '#fdf2f8', 'text' => '#be185d'],
                ];
                $maxSiswa = max(array_values($this->siswaPerJenjang) ?: [1]);
            @endphp

            <div style="space-y:10px;">
                @foreach($this->siswaPerJenjang as $jenjang => $jumlah)
                @php $cfg = $jenjangConfig[$jenjang] ?? ['color'=>'#6b7280','bg'=>'#f9fafb','text'=>'#374151']; @endphp
                <div style="margin-bottom:12px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:5px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:10px; height:10px; border-radius:50%;
                                         background:{{ $cfg['color'] }};"></div>
                            <span style="font-size:13px; font-weight:600; color:#374151;">{{ $jenjang }}</span>
                        </div>
                        <span style="font-size:12px; font-weight:700; background:{{ $cfg['bg'] }};
                                      color:{{ $cfg['text'] }}; padding:2px 10px;
                                      border-radius:20px;">{{ $jumlah }}</span>
                    </div>
                    <div style="background:#f3f4f6; border-radius:6px; height:8px; overflow:hidden;">
                        <div style="height:8px; border-radius:6px; background:{{ $cfg['color'] }};
                                     width:{{ $maxSiswa > 0 ? round(($jumlah/$maxSiswa)*100) : 0 }}%;
                                     transition:width .3s;"></div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($this->totalCalonSiswa > 0)
            <div style="margin-top:12px; padding-top:12px; border-top:1px solid #f3f4f6;">
                <div style="font-size:11px; color:#9ca3af; margin-bottom:6px;">Calon Siswa</div>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($this->calonPerJenjang as $jenis => $jml)
                    <span style="background:#f5f3ff; color:#7c3aed; font-size:11px;
                                  font-weight:600; padding:2px 10px; border-radius:20px;
                                  border:1px solid #ddd6fe;">
                        {{ $jenis ?: '—' }}: {{ $jml }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Grafik Kas 6 Bulan --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <div style="font-size:12px; font-weight:700; text-transform:uppercase;
                             letter-spacing:.5px; color:#374151;">
                    Tren Kas 6 Bulan Terakhir
                </div>
                <div style="display:flex; align-items:center; gap:12px; font-size:11px; color:#6b7280;">
                    <span style="display:flex;align-items:center;gap:4px;">
                        <span style="width:10px;height:10px;border-radius:2px;background:#34d399;display:inline-block;"></span>
                        Masuk
                    </span>
                    <span style="display:flex;align-items:center;gap:4px;">
                        <span style="width:10px;height:10px;border-radius:2px;background:#f87171;display:inline-block;"></span>
                        Keluar
                    </span>
                </div>
            </div>

            @php
                $chartData = $this->kasEnamBulan;
                $chartMax  = max(array_merge([1], array_column($chartData, 'masuk'), array_column($chartData, 'keluar')));
            @endphp

            <div style="display:flex; align-items:flex-end; gap:10px; height:120px; padding-bottom:4px;">
                @foreach($chartData as $row)
                @php
                    $pctMasuk  = $chartMax > 0 ? round(($row['masuk']  / $chartMax) * 100) : 0;
                    $pctKeluar = $chartMax > 0 ? round(($row['keluar'] / $chartMax) * 100) : 0;
                @endphp
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:2px; height:100%;">
                    <div style="flex:1; width:100%; display:flex; align-items:flex-end; gap:2px;">
                        <div style="flex:1; border-radius:4px 4px 0 0; background:#34d399;
                                     height:{{ max($pctMasuk, 2) }}%;"
                             title="Masuk: Rp {{ number_format($row['masuk'], 0, ',', '.') }}">
                        </div>
                        <div style="flex:1; border-radius:4px 4px 0 0; background:#f87171;
                                     height:{{ max($pctKeluar, 2) }}%;"
                             title="Keluar: Rp {{ number_format($row['keluar'], 0, ',', '.') }}">
                        </div>
                    </div>
                    <div style="font-size:10px; color:#9ca3af; text-align:center; margin-top:4px;">
                        {{ $row['label'] }}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Nilai bulan ini --}}
            @php $last = end($chartData); @endphp
            <div style="margin-top:12px; padding-top:12px; border-top:1px solid #f3f4f6;
                         display:flex; gap:16px; font-size:11px;">
                <span style="color:#059669; font-weight:600;">
                    ↑ Rp {{ number_format($last['masuk'], 0, ',', '.') }}
                </span>
                <span style="color:#dc2626; font-weight:600;">
                    ↓ Rp {{ number_format($last['keluar'], 0, ',', '.') }}
                </span>
                <span style="color:#6b7280;">bulan ini</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 4 — TAGIHAN BELUM BAYAR (standalone)
    ══════════════════════════════════════════════════ --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="background:#fef3c7; border-radius:8px; padding:6px;">
                    <x-heroicon-o-exclamation-circle style="width:18px;height:18px;color:#d97706;"/>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#374151;">Tagihan Belum Bayar</div>
                    <div style="font-size:11px; color:#9ca3af;">Perlu segera ditindaklanjuti</div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
                         padding:14px 16px; text-align:center;">
                <div style="font-size:32px; font-weight:800; color:#b45309; line-height:1;">
                    {{ number_format($this->totalTagihanBelumBayar) }}
                </div>
                <div style="font-size:11px; color:#92400e; margin-top:4px;">Jumlah Tagihan</div>
            </div>
            <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px;
                         padding:14px 16px; text-align:center;">
                <div style="font-size:20px; font-weight:800; color:#c2410c; line-height:1.2;">
                    Rp {{ number_format($this->nominalTagihanBelumBayar, 0, ',', '.') }}
                </div>
                <div style="font-size:11px; color:#9a3412; margin-top:4px;">Total Nominal</div>
            </div>
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;
                         padding:14px 16px; text-align:center; display:flex;
                         flex-direction:column; align-items:center; justify-content:center;">
                <a href="/admin/tagihans"
                   style="font-size:12px; font-weight:600; color:#15803d;
                           text-decoration:none; display:flex; align-items:center; gap:4px;">
                    <x-heroicon-o-arrow-right-circle style="width:16px;height:16px;"/>
                    Kelola Tagihan
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 5 — KARYAWAN & ABSENSI (standalone)
    ══════════════════════════════════════════════════ --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px 24px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="background:#f3e8ff; border-radius:8px; padding:7px;">
                    <x-heroicon-o-users style="width:18px;height:18px;color:#9333ea;"/>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#374151;">Karyawan & Absensi</div>
                    <div style="font-size:11px; color:#9ca3af;">Kehadiran hari ini, {{ now()->translatedFormat('d F Y') }}</div>
                </div>
            </div>
            <div style="background:#fdf4ff; border:1px solid #e9d5ff; border-radius:10px;
                         padding:10px 20px; text-align:center;">
                <div style="font-size:28px; font-weight:800; color:#7e22ce; line-height:1;">
                    {{ $this->totalKaryawanAktif }}
                </div>
                <div style="font-size:11px; color:#a855f7; margin-top:3px;">Karyawan Aktif</div>
            </div>
        </div>

        @php
            $absen = $this->absensiHariIni;
            $absenCfg = [
                'hadir' => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#15803d','label'=>'Hadir'],
                'izin'  => ['bg'=>'#fffbeb','border'=>'#fde68a','text'=>'#b45309','label'=>'Izin'],
                'sakit' => ['bg'=>'#fff7ed','border'=>'#fed7aa','text'=>'#c2410c','label'=>'Sakit'],
                'alpha' => ['bg'=>'#fef2f2','border'=>'#fecaca','text'=>'#dc2626','label'=>'Alpha'],
            ];
        @endphp

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px;">
            @foreach($absenCfg as $key => $cfg)
            <div style="background:{{ $cfg['bg'] }}; border:1px solid {{ $cfg['border'] }};
                         border-radius:12px; padding:16px 12px; text-align:center;">
                <div style="font-size:32px; font-weight:800; color:{{ $cfg['text'] }}; line-height:1;">
                    {{ $absen[$key] ?? 0 }}
                </div>
                <div style="font-size:12px; color:{{ $cfg['text'] }}; opacity:.8;
                             margin-top:6px; font-weight:600;">
                    {{ $cfg['label'] }}
                </div>
            </div>
            @endforeach
        </div>

        @if(array_sum($absen) === 0)
        <div style="text-align:center; color:#9ca3af; font-size:12px;
                     font-style:italic; padding:10px 0 0;">
            Belum ada data absensi hari ini
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         BARIS 6 — PEMBAYARAN TERBARU
    ══════════════════════════════════════════════════ --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="background:#dcfce7; border-radius:8px; padding:6px;">
                    <x-heroicon-o-banknotes style="width:18px;height:18px;color:#16a34a;"/>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#374151;">Pembayaran Terbaru</div>
                    <div style="font-size:11px; color:#9ca3af;">8 transaksi terakhir</div>
                </div>
            </div>
        </div>

        @if($this->pembayaranTerbaru->isEmpty())
            <div style="text-align:center; color:#9ca3af; font-size:13px;
                         font-style:italic; padding:24px 0;">
                Belum ada data pembayaran.
            </div>
        @else
        <div class="overflow-x-auto">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <th style="padding:6px 12px 8px 0; text-align:left; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Siswa</th>
                        <th style="padding:6px 12px 8px 0; text-align:left; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Jenis</th>
                        <th style="padding:6px 12px 8px 0; text-align:left; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Periode</th>
                        <th style="padding:6px 12px 8px 0; text-align:right; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Nominal</th>
                        <th style="padding:6px 12px 8px 0; text-align:center; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Tanggal</th>
                        <th style="padding:6px 0 8px 0; text-align:center; font-size:10px;
                                   font-weight:700; text-transform:uppercase; letter-spacing:.5px;
                                   color:#9ca3af;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->pembayaranTerbaru as $p)
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td style="padding:10px 12px 10px 0;">
                            <div style="font-weight:600; color:#111827;">
                                {{ $p->siswa?->nama ?? '—' }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">
                                {{ $p->siswa?->nis }}
                            </div>
                        </td>
                        <td style="padding:10px 12px 10px 0; color:#4b5563;">
                            {{ $p->jenisPembayaran?->nama ?? '—' }}
                        </td>
                        <td style="padding:10px 12px 10px 0; color:#9ca3af; font-size:12px;">
                            {{ $p->bulan ? $this->getBulanShort($p->bulan) : '' }}
                            {{ $p->tahun }}
                        </td>
                        <td style="padding:10px 12px 10px 0; text-align:right;
                                   font-weight:700; color:#111827; white-space:nowrap;">
                            Rp {{ number_format($p->nominal, 0, ',', '.') }}
                        </td>
                        <td style="padding:10px 12px 10px 0; text-align:center;
                                   color:#9ca3af; font-size:12px; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}
                        </td>
                        <td style="padding:10px 0 10px 0; text-align:center;">
                            @if($p->status === 'lunas')
                                <span style="background:#dcfce7; color:#15803d; font-size:11px;
                                              font-weight:600; padding:3px 10px; border-radius:20px;">
                                    Lunas
                                </span>
                            @elseif($p->status === 'cicilan')
                                <span style="background:#fef3c7; color:#b45309; font-size:11px;
                                              font-weight:600; padding:3px 10px; border-radius:20px;">
                                    Cicilan
                                </span>
                            @else
                                <span style="background:#f3f4f6; color:#6b7280; font-size:11px;
                                              font-weight:600; padding:3px 10px; border-radius:20px;">
                                    {{ $p->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
</x-filament-panels::page>
