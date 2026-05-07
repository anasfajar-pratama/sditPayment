<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.25rem;">

    {{-- ── FILTER BAR ─────────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">

        <div style="display:flex;align-items:center;gap:0.75rem;">
            <select wire:model.live="filterBulan"
                style="border:1px solid #d1d5db;border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;min-width:130px;cursor:pointer;">
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                          '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $lbl)
                    <option value="{{ $val }}" @selected($filterBulan === $val)>{{ $lbl }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterTahun"
                style="border:1px solid #d1d5db;border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;min-width:90px;cursor:pointer;">
                @foreach(range(now()->year, 2023) as $y)
                    <option value="{{ $y }}" @selected($filterTahun == $y)>{{ $y }}</option>
                @endforeach
            </select>

            <span style="font-size:0.875rem;color:#6b7280;font-weight:500;">
                {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
            </span>
        </div>

        <div style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border-radius:0.75rem;padding:0.6rem 1.25rem;text-align:right;min-width:180px;">
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;opacity:0.85;margin-bottom:0.15rem;">Total Upah Bulan Ini</div>
            <div style="font-size:1.1rem;font-weight:800;font-variant-numeric:tabular-nums;">
                Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
            </div>
        </div>
    </div>

    @if(empty($this->penerimas))
        <div style="background:#fff;border-radius:1rem;border:1px solid #e5e7eb;padding:4rem 1rem;text-align:center;">
            <div style="font-size:2.5rem;margin-bottom:0.75rem;">👥</div>
            <div style="font-weight:600;color:#374151;font-size:1rem;margin-bottom:0.35rem;">Belum ada data upah</div>
            <div style="font-size:0.8rem;color:#9ca3af;max-width:400px;margin:0 auto;">
                Input via <strong>Kas Harian → Input Jurnal</strong>, pilih akun <em>Beban Gaji & Upah</em>,
                lalu isi nama penerima di kolom Sub Kategori.
            </div>
        </div>
    @else

        {{-- ── RINGKASAN PER PENERIMA ─────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:0.75rem;">
            @foreach($this->penerimas as $penerima)
                @php
                    $isActive = $activeTab === $penerima;
                    $totalBln = $this->ringkasan[$penerima] ?? 0;
                    $totalThn = $this->totalTahunPerPenerima[$penerima] ?? 0;
                @endphp
                <div wire:click="setTab('{{ $penerima }}')"
                    style="
                        cursor:pointer;
                        border-radius:0.75rem;
                        padding:0.85rem 1rem;
                        border:2px solid {{ $isActive ? '#6366f1' : '#e5e7eb' }};
                        background:{{ $isActive ? '#eef2ff' : '#fafafa' }};
                    ">
                    <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:0.35rem;">
                        <span style="width:28px;height:28px;border-radius:50%;background:{{ $isActive ? '#6366f1' : '#e5e7eb' }};display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:{{ $isActive ? '#fff' : '#6b7280' }};">
                            {{ strtoupper(substr($penerima, 0, 2)) }}
                        </span>
                        <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:{{ $isActive ? '#3730a3' : '#6b7280' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100px;">
                            {{ $penerima }}
                        </span>
                    </div>
                    <div style="font-weight:700;font-size:0.85rem;color:{{ $isActive ? '#4f46e5' : ($totalBln > 0 ? '#1f2937' : '#d1d5db') }};font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($totalBln, 0, ',', '.') }}
                    </div>
                    <div style="font-size:0.68rem;color:#9ca3af;margin-top:0.1rem;font-variant-numeric:tabular-nums;">
                        Tahun: Rp {{ number_format($totalThn, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── TAB SECTION ─────────────────────────────────────────────── --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;">

            {{-- Tab bar --}}
            <div style="display:flex;overflow-x:auto;border-bottom:1px solid #f3f4f6;background:#eef2ff;scrollbar-width:thin;">
                @foreach($this->penerimas as $penerima)
                    @php $isActive = $activeTab === $penerima; @endphp
                    <button wire:click="setTab('{{ $penerima }}')"
                        style="
                            flex-shrink:0;
                            padding:0.7rem 1.1rem;
                            font-size:0.78rem;
                            font-weight:{{ $isActive ? '700' : '500' }};
                            white-space:nowrap;
                            border:none;
                            border-bottom:3px solid {{ $isActive ? '#6366f1' : 'transparent' }};
                            color:{{ $isActive ? '#4f46e5' : '#9ca3af' }};
                            background:{{ $isActive ? '#fff' : 'transparent' }};
                            cursor:pointer;
                        ">
                        {{ $penerima }}
                        @if(($this->ringkasan[$penerima] ?? 0) > 0)
                            <span style="margin-left:0.3rem;font-size:0.65rem;opacity:0.7;">
                                ({{ number_format($this->ringkasan[$penerima], 0, ',', '.') }})
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            @php $rows = $this->entriesPerTab[$activeTab] ?? []; @endphp

            @if(empty($rows))
                <div style="padding:3.5rem 1rem;text-align:center;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
                    <div style="font-weight:600;color:#374151;margin-bottom:0.25rem;">
                        Belum ada upah untuk {{ $activeTab }} bulan ini
                    </div>
                    <div style="font-size:0.8rem;color:#9ca3af;">
                        Input via <strong>Kas Harian → Input Jurnal</strong> dengan Sub Kategori <em>"{{ $activeTab }}"</em>
                    </div>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                        <thead>
                            <tr style="background:#1f2937;color:#fff;">
                                <th style="padding:0.65rem 1rem;text-align:center;width:2.5rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">NO</th>
                                <th style="padding:0.65rem 1rem;text-align:left;width:7rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">TANGGAL</th>
                                <th style="padding:0.65rem 1rem;text-align:left;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">KETERANGAN</th>
                                <th style="padding:0.65rem 1rem;text-align:right;width:9rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">JUMLAH</th>
                                <th style="padding:0.65rem 1rem;text-align:right;width:9rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;background:#374151;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $i => $row)
                                <tr style="border-bottom:1px solid #f3f4f6;background:{{ $i % 2 === 0 ? '#fff' : '#f5f3ff' }};">
                                    <td style="padding:0.6rem 1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">{{ $row['no'] }}</td>
                                    <td style="padding:0.6rem 1rem;color:#6b7280;font-size:0.8rem;white-space:nowrap;">{{ $row['tanggal'] }}</td>
                                    <td style="padding:0.6rem 1rem;color:#1f2937;">{{ $row['uraian'] }}</td>
                                    <td style="padding:0.6rem 1rem;text-align:right;font-weight:600;color:#dc2626;font-variant-numeric:tabular-nums;">
                                        {{ number_format($row['jumlah'], 0, ',', '.') }}
                                    </td>
                                    <td style="padding:0.6rem 1rem;text-align:right;font-weight:700;color:#1f2937;font-variant-numeric:tabular-nums;background:#f5f3ff;">
                                        {{ number_format($row['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="background:#1f2937;color:#fff;padding:0.85rem 1.25rem;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div style="color:#9ca3af;font-size:0.8rem;">
                            Upah <strong style="color:#a5b4fc;">{{ $activeTab }}</strong> — {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                            &nbsp;({{ count($rows) }} pembayaran)
                        </div>
                        <div style="color:#6b7280;font-size:0.7rem;margin-top:0.1rem;">
                            Total tahun {{ $filterTahun }}: Rp {{ number_format($this->totalTahunPerPenerima[$activeTab] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <span style="font-weight:800;color:#818cf8;font-size:1.05rem;font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($this->ringkasan[$activeTab] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>
    @endif

</div>
</x-filament-panels::page>
