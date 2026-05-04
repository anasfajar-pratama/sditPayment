<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:1.75rem;">

        {{-- TOP BAR: Judul kiri, Filter + Export kanan --}}
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.375rem;font-size:0.8rem;color:#9ca3af;margin-bottom:0.35rem;">
                    <span>Kas Harian</span>
                    <span>›</span>
                    <span>Rekap Bulanan</span>
                </div>
                <h1 style="font-size:1.6rem;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.2;margin:0;">
                    Rekap Kas &mdash; {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                </h1>
            </div>

            <div style="display:flex;align-items:center;gap:0.75rem;flex-shrink:0;">
                {{-- Filter bulan + tahun --}}
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            box-shadow:0 1px 2px rgba(0,0,0,0.05);overflow:hidden;background:#fff;">
                    <select wire:model.live="filterBulan"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;min-width:7rem;">
                        @foreach([
                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                        ] as $val => $lbl)
                            <option value="{{ $val }}" @selected($filterBulan === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <div style="width:1px;height:1.25rem;background:#e5e7eb;flex-shrink:0;"></div>
                    <select wire:model.live="filterTahun"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;">
                        @foreach(range(now()->year, 2023) as $y)
                            <option value="{{ $y }}" @selected($filterTahun == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Export PDF --}}
                <button
                    onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:0.45rem;background:#1f2937;color:#fff;
                           padding:0.55rem 1.1rem;border-radius:0.5rem;font-size:0.875rem;font-weight:600;
                           border:none;cursor:pointer;white-space:nowrap;box-shadow:0 1px 2px rgba(0,0,0,0.15);"
                    onmouseover="this.style.background='#374151'"
                    onmouseout="this.style.background='#1f2937'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 16v-8m0 8l-3-3m3 3l3-3M6 20h12a2 2 0 002-2V8l-6-6H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        {{-- 4 RINGKASAN KOTAK --}}
        @php $r = $this->ringkasan; @endphp
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">

            <div style="background-color:#3b82f6;color:#fff;border-radius:1rem;padding:1.25rem 1.5rem;">
                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.85;margin-bottom:0.5rem;font-weight:700;">Saldo Awal</div>
                <div style="font-size:1.05rem;font-weight:800;line-height:1.3;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($r['saldo_awal'], 0, ',', '.') }}
                </div>
            </div>

            <div style="background-color:#22c55e;color:#fff;border-radius:1rem;padding:1.25rem 1.5rem;">
                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.85;margin-bottom:0.5rem;font-weight:700;">Total Debit</div>
                <div style="font-size:1.05rem;font-weight:800;line-height:1.3;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($r['total_debit'], 0, ',', '.') }}
                </div>
            </div>

            <div style="background-color:#ef4444;color:#fff;border-radius:1rem;padding:1.25rem 1.5rem;">
                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.85;margin-bottom:0.5rem;font-weight:700;">Total Kredit</div>
                <div style="font-size:1.05rem;font-weight:800;line-height:1.3;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($r['total_kredit'], 0, ',', '.') }}
                </div>
            </div>

            <div style="background-color:#1f2937;color:#fff;border-radius:1rem;padding:1.25rem 1.5rem;">
                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.65;margin-bottom:0.5rem;font-weight:700;">Saldo Akhir</div>
                <div style="font-size:1.05rem;font-weight:800;line-height:1.3;color:#fde68a;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($r['saldo_akhir'], 0, ',', '.') }}
                </div>
            </div>

        </div>

        {{-- REKAP PER KELOMPOK AKUN --}}
        @if ($this->rekapPerKelompok->isEmpty())
            <x-filament::section>
                <div class="py-10 text-center text-gray-400">
                    <x-heroicon-o-chart-bar class="mx-auto w-10 h-10 mb-2" />
                    <p>Belum ada data transaksi untuk periode ini.</p>
                </div>
            </x-filament::section>
        @else
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @foreach ($this->rekapPerKelompok as $grp)
                    @php
                        $isPendapatan = $grp['kelompok'] === 'Pendapatan';
                        $isBeban      = $grp['kelompok'] === 'Beban';
                        $nominal      = $isPendapatan ? $grp['total_debit'] : $grp['total_kredit'];
                        $totalKelompok = max($nominal, 1);

                        if ($isPendapatan) {
                            $badgeBg    = '#dcfce7'; $badgeColor = '#15803d'; $badgeBorder = '#86efac';
                            $headerColor= '#15803d'; $barColor   = '#22c55e';
                            $badgeLabel = 'DEBIT';
                            $dividerColor = '#f0fdf4';
                        } elseif ($isBeban) {
                            $badgeBg    = '#fee2e2'; $badgeColor = '#b91c1c'; $badgeBorder = '#fca5a5';
                            $headerColor= '#b91c1c'; $barColor   = '#ef4444';
                            $badgeLabel = 'KREDIT';
                            $dividerColor = '#fff5f5';
                        } else {
                            $badgeBg    = '#dbeafe'; $badgeColor = '#1d4ed8'; $badgeBorder = '#93c5fd';
                            $headerColor= '#1d4ed8'; $barColor   = '#3b82f6';
                            $badgeLabel = strtoupper(substr($grp['kelompok'], 0, 6));
                            $dividerColor = '#eff6ff';
                        }
                    @endphp

                    <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                                box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

                        {{-- Header kelompok --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex;align-items:center;gap:0.625rem;">
                                <span style="font-size:0.65rem;font-weight:800;padding:0.25rem 0.65rem;
                                             border-radius:0.3rem;border:1px solid {{ $badgeBorder }};
                                             background:{{ $badgeBg }};color:{{ $badgeColor }};
                                             letter-spacing:0.06em;">
                                    {{ $badgeLabel }}
                                </span>
                                <span style="font-weight:700;color:#1f2937;font-size:1rem;">
                                    {{ $grp['kelompok'] }}
                                </span>
                            </div>
                            <span style="font-weight:700;color:{{ $headerColor }};
                                         font-variant-numeric:tabular-nums;font-size:0.95rem;">
                                Rp {{ number_format($nominal, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Daftar item --}}
                        <div>
                            @foreach ($grp['items'] as $item)
                                @php
                                    $itemNominal = $isPendapatan ? $item['total_debit'] : $item['total_kredit'];
                                    $pct = $totalKelompok > 0 ? round($itemNominal / $totalKelompok * 100) : 0;
                                    $barWidth = max(4, min($pct, 100));
                                    $isLast = $loop->last;
                                @endphp
                                <div style="display:flex;align-items:center;justify-content:space-between;
                                            padding:0.875rem 1.5rem;
                                            {{ !$isLast ? 'border-bottom:1px solid #f8fafc;' : '' }}
                                            transition:background 0.15s;"
                                     onmouseover="this.style.background='#fafafa'"
                                     onmouseout="this.style.background='transparent'">

                                    <span style="font-size:0.875rem;color:#374151;">
                                        {{ $item['nama_akun'] }}
                                    </span>

                                    <div style="display:flex;align-items:center;gap:0.875rem;flex-shrink:0;">
                                        <span style="font-size:0.875rem;font-weight:600;
                                                     color:{{ $headerColor }};font-variant-numeric:tabular-nums;
                                                     white-space:nowrap;">
                                            Rp {{ number_format($itemNominal, 0, ',', '.') }}
                                        </span>
                                        <div style="width:5rem;background:#f1f5f9;border-radius:9999px;
                                                    height:5px;overflow:hidden;flex-shrink:0;">
                                            <div style="width:{{ $barWidth }}%;height:5px;border-radius:9999px;
                                                        background:{{ $barColor }};"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

        {{-- RINGKASAN AKHIR BULAN --}}
        <div style="background-color:#1f2937;border-radius:1rem;padding:1.5rem 1.75rem;color:#fff;">
            <div style="font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;
                        letter-spacing:0.08em;margin-bottom:1rem;">
                Rekap Akhir {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;font-size:0.875rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#9ca3af;">Saldo Awal Bulan</span>
                    <span style="font-weight:600;color:#93c5fd;font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($r['saldo_awal'], 0, ',', '.') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#9ca3af;">+ Total Pemasukan (Debit)</span>
                    <span style="font-weight:600;color:#4ade80;font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($r['total_debit'], 0, ',', '.') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#9ca3af;">− Total Pengeluaran (Kredit)</span>
                    <span style="font-weight:600;color:#f87171;font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($r['total_kredit'], 0, ',', '.') }}
                    </span>
                </div>
                <div style="border-top:1px solid #374151;margin-top:0.25rem;padding-top:1rem;
                            display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-weight:700;font-size:0.95rem;">
                        Saldo Akhir {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                    </span>
                    <span style="font-weight:800;color:#fde68a;font-size:1.15rem;font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($r['saldo_akhir'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>