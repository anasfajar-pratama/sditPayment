<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.25rem;">

    {{-- ── FILTER BAR ─────────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">

        <div style="display:flex;align-items:center;gap:0.75rem;">
            <input type="date" wire:model.live="filterStart"
                style="border:1px solid #d1d5db;border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;min-width:150px;">

            <span style="color:#9ca3af;font-size:0.85rem;">s.d.</span>

            <input type="date" wire:model.live="filterEnd"
                style="border:1px solid #d1d5db;border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;min-width:150px;">

            <span style="font-size:0.875rem;color:#6b7280;font-weight:500;">
                {{ \Carbon\Carbon::parse($filterStart)->format('d M Y') }} — {{ \Carbon\Carbon::parse($filterEnd)->format('d M Y') }}
            </span>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem;">
            <a href="{{ route('kas-ringkasan.pdf', ['start' => $filterStart, 'end' => $filterEnd]) }}" target="_blank"
                style="display:inline-flex;align-items:center;gap:0.5rem;background:#ef4444;color:#fff;border-radius:0.5rem;padding:0.5rem 1rem;font-size:0.8rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;">
                <span style="font-size:1rem;">📄</span> Cetak PDF
            </a>
            <div style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:0.75rem;padding:0.6rem 1.25rem;text-align:right;min-width:180px;">
                <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;opacity:0.85;margin-bottom:0.15rem;">Kas Hari Ini</div>
                <div style="font-size:1.1rem;font-weight:800;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($this->kasHariIni, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── RINGKASAN GRID ──────────────────────────────────────────────── --}}
    @php
        $cards = [
            'transfer'    => ['label' => 'Transfer',     'value' => $this->totalTransfer],
            'cash'        => ['label' => 'Cash',         'value' => $this->totalCash],
            'pengeluaran' => ['label' => 'Pengeluaran',  'value' => $this->totalKredit],
            'gabungan'    => ['label' => 'Kas Hari Ini', 'value' => $this->kasHariIni],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;">
        @foreach($cards as $key => $card)
            @php
                $isActive = $activeTab === $key;
                $hasValue = $card['value'] > 0;
            @endphp
            <div wire:click="setTab('{{ $key }}')"
                style="
                    cursor:pointer;
                    border-radius:0.75rem;
                    padding:0.85rem 1rem;
                    border:2px solid {{ $isActive ? '#059669' : '#e5e7eb' }};
                    background:{{ $isActive ? '#ecfdf5' : '#fafafa' }};
                    transition:all 0.15s;
                ">
                <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:{{ $isActive ? '#047857' : '#9ca3af' }};margin-bottom:0.3rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $card['label'] }}
                </div>
                <div style="font-weight:700;font-size:0.9rem;color:{{ $isActive ? '#047857' : ($hasValue ? '#1f2937' : '#d1d5db') }};font-variant-numeric:tabular-nums;">
                    Rp {{ number_format($card['value'], 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TAB SECTION ─────────────────────────────────────────────────── --}}
    @php
        $tabs = [
            'transfer'    => 'Transfer',
            'cash'        => 'Cash',
            'pengeluaran' => 'Pengeluaran',
            'gabungan'    => 'Gabungan',
        ];
    @endphp

    <div style="background:#fff;border-radius:1rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;">

        {{-- Tab bar --}}
        <div style="display:flex;overflow-x:auto;border-bottom:1px solid #f3f4f6;background:#fafafa;scrollbar-width:thin;">
            @foreach($tabs as $key => $label)
                @php $isActive = $activeTab === $key; @endphp
                <button wire:click="setTab('{{ $key }}')"
                    style="
                        flex-shrink:0;
                        padding:0.7rem 1.1rem;
                        font-size:0.78rem;
                        font-weight:{{ $isActive ? '700' : '500' }};
                        white-space:nowrap;
                        border:none;
                        border-bottom:3px solid {{ $isActive ? '#059669' : 'transparent' }};
                        color:{{ $isActive ? '#047857' : '#9ca3af' }};
                        background:{{ $isActive ? '#ecfdf5' : 'transparent' }};
                        cursor:pointer;
                        letter-spacing:0.02em;
                    ">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Isi tabel --}}
        @php
            $rows = match ($activeTab) {
                'transfer'    => $this->entriesTransfer,
                'cash'        => $this->entriesCash,
                'pengeluaran' => $this->entriesKredit,
                'gabungan'    => $this->entriesGabungan,
                default       => [],
            };
            $tabLabel = $tabs[$activeTab] ?? 'Gabungan';
        @endphp

        @if(empty($rows))
            <div style="padding:3.5rem 1rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
                <div style="font-weight:600;color:#374151;margin-bottom:0.25rem;">
                    Belum ada data {{ $tabLabel }} pada rentang tanggal ini
                </div>
                <div style="font-size:0.8rem;color:#9ca3af;">
                    Transaksi masuk/keluar diinput otomatis dari pembayaran &amp; input jurnal di <strong>Kas Harian</strong>
                </div>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="k-grid-table" style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                    <thead>
                        <tr style="background:#1f2937;color:#fff;">
                            <th style="padding:0.65rem 1rem;text-align:center;width:2.5rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">NO</th>
                            <th style="padding:0.65rem 1rem;text-align:left;width:7rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">TANGGAL</th>
                            <th style="padding:0.65rem 1rem;text-align:left;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">KETERANGAN</th>
                            <th style="padding:0.65rem 1rem;text-align:left;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">AKUN</th>
                            @if($activeTab === 'transfer')
                                <th style="padding:0.65rem 1rem;text-align:left;width:9rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">REKENING TUJUAN</th>
                            @endif
                            @if($activeTab === 'gabungan')
                                <th style="padding:0.65rem 1rem;text-align:center;width:5.5rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">TIPE</th>
                            @endif
                            <th style="padding:0.65rem 1rem;text-align:right;width:10rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;">JUMLAH</th>
                            @if($activeTab === 'gabungan')
                                <th style="padding:0.65rem 1rem;text-align:right;width:10rem;font-size:0.7rem;font-weight:600;letter-spacing:0.05em;background:#374151;">SALDO</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr style="border-bottom:1px solid #f3f4f6;background:{{ $loop->index % 2 === 0 ? '#fff' : '#fafafa' }};">
                                <td style="padding:0.6rem 1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">{{ $row['no'] }}</td>
                                <td style="padding:0.6rem 1rem;color:#6b7280;font-size:0.8rem;white-space:nowrap;">{{ $row['tanggal'] }}</td>
                                <td style="padding:0.6rem 1rem;color:#1f2937;">{{ $row['uraian'] }}</td>
                                <td style="padding:0.6rem 1rem;color:#374151;font-size:0.8rem;">
                                    {{ $row['akun'] }}{{ $row['sub_kategori'] ? ' — ' . $row['sub_kategori'] : '' }}
                                </td>
                                @if($activeTab === 'transfer')
                                    <td style="padding:0.6rem 1rem;color:#374151;font-size:0.8rem;">
                                        {{ $row['rekening'] }}{{ $row['pengirim'] ? ' (' . $row['pengirim'] . ')' : '' }}
                                    </td>
                                @endif
                                @if($activeTab === 'gabungan')
                                    <td style="padding:0.6rem 1rem;text-align:center;font-size:0.75rem;font-weight:700;">
                                        <span style="display:inline-block;padding:0.15rem 0.6rem;border-radius:9999px;font-size:0.7rem;
                                            {{ $row['tipe'] === 'Masuk' ? 'background:#ecfdf5;color:#047857;' : 'background:#fef2f2;color:#b91c1c;' }}">
                                            {{ $row['tipe'] }}
                                        </span>
                                    </td>
                                @endif
                                <td style="padding:0.6rem 1rem;text-align:right;font-weight:600;color:{{ $row['tipe'] === 'Keluar' ? '#dc2626' : '#047857' }};font-variant-numeric:tabular-nums;">
                                    {{ number_format($row['jumlah'], 0, ',', '.') }}
                                </td>
                                @if($activeTab === 'gabungan')
                                    <td style="padding:0.6rem 1rem;text-align:right;font-weight:700;color:#1f2937;font-variant-numeric:tabular-nums;background:#f9fafb;">
                                        {{ number_format($row['saldo'], 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="background:#1f2937;color:#fff;padding:0.85rem 1.25rem;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#9ca3af;font-size:0.8rem;">
                    Total <strong style="color:#34d399;">{{ $tabLabel }}</strong>
                    — {{ \Carbon\Carbon::parse($filterStart)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($filterEnd)->format('d M Y') }}
                    &nbsp;({{ count($rows) }} transaksi)
                </span>
                <span style="font-weight:800;color:#34d399;font-size:1.05rem;font-variant-numeric:tabular-nums;">
                    Rp {{ number_format(match ($activeTab) {
                        'transfer'    => $this->totalTransfer,
                        'cash'        => $this->totalCash,
                        'pengeluaran' => $this->totalKredit,
                        default       => $this->kasHariIni,
                    }, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

</div>
</x-filament-panels::page>