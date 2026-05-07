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

        <div style="background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-radius:0.75rem;padding:0.6rem 1.25rem;text-align:right;min-width:180px;">
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;opacity:0.85;margin-bottom:0.15rem;">Total Operasional</div>
            <div style="font-size:1.1rem;font-weight:800;font-variant-numeric:tabular-nums;">
                Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ── RINGKASAN GRID ──────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;">
        @foreach(\App\Filament\Pages\PengeluaranOperasionalPage::KATEGORI as $kat)
            @php
                $isActive  = $activeTab === $kat;
                $hasValue  = ($this->ringkasan[$kat] ?? 0) > 0;
            @endphp
            <div wire:click="setTab('{{ $kat }}')"
                style="
                    cursor:pointer;
                    border-radius:0.75rem;
                    padding:0.85rem 1rem;
                    border:2px solid {{ $isActive ? '#f97316' : '#e5e7eb' }};
                    background:{{ $isActive ? '#fff7ed' : '#fafafa' }};
                    transition:all 0.15s;
                ">
                <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:{{ $isActive ? '#c2410c' : '#9ca3af' }};margin-bottom:0.3rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $kat }}
                </div>
                <div style="font-weight:700;font-size:0.9rem;color:{{ $isActive ? '#ea580c' : ($hasValue ? '#1f2937' : '#d1d5db') }};font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($this->ringkasan[$kat] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TAB SECTION ─────────────────────────────────────────────────── --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;">

        {{-- Tab bar --}}
        <div style="display:flex;overflow-x:auto;border-bottom:1px solid #f3f4f6;background:#fafafa;scrollbar-width:thin;">
            @foreach(\App\Filament\Pages\PengeluaranOperasionalPage::KATEGORI as $kat)
                @php $isActive = $activeTab === $kat; @endphp
                <button wire:click="setTab('{{ $kat }}')"
                    style="
                        flex-shrink:0;
                        padding:0.7rem 1.1rem;
                        font-size:0.78rem;
                        font-weight:{{ $isActive ? '700' : '500' }};
                        white-space:nowrap;
                        border:none;
                        border-bottom:3px solid {{ $isActive ? '#f97316' : 'transparent' }};
                        color:{{ $isActive ? '#ea580c' : '#9ca3af' }};
                        background:{{ $isActive ? '#fff7ed' : 'transparent' }};
                        cursor:pointer;
                        letter-spacing:0.02em;
                    ">
                    {{ $kat }}
                </button>
            @endforeach
        </div>

        {{-- Isi tabel --}}
        @php $rows = $this->entriesPerTab[$activeTab] ?? []; @endphp

        @if(empty($rows))
            <div style="padding:3.5rem 1rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
                <div style="font-weight:600;color:#374151;margin-bottom:0.25rem;">
                    Belum ada pengeluaran {{ $activeTab }} bulan ini
                </div>
                <div style="font-size:0.8rem;color:#9ca3af;">
                    Input via <strong>Kas Harian → Input Jurnal</strong>, pilih Sub Kategori <em>"{{ $activeTab }}"</em>
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
                            <th style="padding:0.65rem 1rem;text-align:right;width:10rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">KREDIT PENGELUARAN</th>
                            <th style="padding:0.65rem 1rem;text-align:right;width:10rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;background:#374151;">JUMLAH TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            <tr style="border-bottom:1px solid #f3f4f6;background:{{ $i % 2 === 0 ? '#fff' : '#fafafa' }};">
                                <td style="padding:0.6rem 1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">{{ $row['no'] }}</td>
                                <td style="padding:0.6rem 1rem;color:#6b7280;font-size:0.8rem;white-space:nowrap;">{{ $row['tanggal'] }}</td>
                                <td style="padding:0.6rem 1rem;color:#1f2937;">{{ $row['uraian'] }}</td>
                                <td style="padding:0.6rem 1rem;text-align:right;font-weight:600;color:#dc2626;font-variant-numeric:tabular-nums;">
                                    {{ number_format($row['jumlah'], 0, ',', '.') }}
                                </td>
                                <td style="padding:0.6rem 1rem;text-align:right;font-weight:700;color:#1f2937;font-variant-numeric:tabular-nums;background:#f9fafb;">
                                    {{ number_format($row['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="background:#1f2937;color:#fff;padding:0.85rem 1.25rem;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#9ca3af;font-size:0.8rem;">
                    Total <strong style="color:#fdba74;">{{ $activeTab }}</strong> — {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                    &nbsp;({{ count($rows) }} transaksi)
                </span>
                <span style="font-weight:800;color:#fb923c;font-size:1.05rem;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($this->ringkasan[$activeTab] ?? 0, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

</div>
</x-filament-panels::page>
