<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- ══════════════════════════════════════════════════════════
             FILTER BAR
        ══════════════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.75rem;">

            <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                        overflow:hidden;background:#f9fafb;flex-shrink:0;">
                @foreach([
                    'bulanan' => 'Bulanan',
                    'harian'  => 'Harian',
                    '7hari'   => '7 Hari',
                    'range'   => 'Rentang',
                ] as $mode => $label)
                    <button
                        wire:click="$set('filterMode', '{{ $mode }}')"
                        style="padding:0.45rem 0.9rem;font-size:0.8rem;border:none;cursor:pointer;
                               white-space:nowrap;font-weight:{{ $filterMode === $mode ? '700' : '500' }};
                               background:{{ $filterMode === $mode ? '#1f2937' : 'transparent' }};
                               color:{{ $filterMode === $mode ? '#fff' : '#6b7280' }};
                               {{ !($loop->last) ? 'border-right:1px solid #e5e7eb;' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if ($filterMode === 'bulanan')
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

            @elseif ($filterMode === 'harian')
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            box-shadow:0 1px 2px rgba(0,0,0,0.05);overflow:hidden;background:#fff;">
                    <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;
                                 border-right:1px solid #e5e7eb;white-space:nowrap;">Tanggal</span>
                    <input type="date" wire:model.live="filterTanggal"
                        value="{{ $filterTanggal }}"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;">
                </div>

            @elseif ($filterMode === '7hari')
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                                box-shadow:0 1px 2px rgba(0,0,0,0.05);overflow:hidden;background:#fff;">
                        <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;
                                     border-right:1px solid #e5e7eb;white-space:nowrap;">Mulai</span>
                        <input type="date" wire:model.live="filterTanggal"
                            value="{{ $filterTanggal }}"
                            style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                                   color:#374151;outline:none;cursor:pointer;">
                    </div>
                    @if ($filterTanggal)
                        <div style="display:inline-flex;align-items:center;gap:0.3rem;
                                    font-size:0.8rem;color:#9ca3af;padding:0.5rem 0;">
                            s/d
                            <span style="color:#374151;font-weight:600;">
                                {{ \Carbon\Carbon::parse($filterTanggal)->addDays(6)->format('d M Y') }}
                            </span>
                        </div>
                    @endif
                </div>

            @elseif ($filterMode === 'range')
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                                box-shadow:0 1px 2px rgba(0,0,0,0.05);overflow:hidden;background:#fff;">
                        <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;
                                     border-right:1px solid #e5e7eb;white-space:nowrap;">Dari</span>
                        <input type="date" wire:model.live="filterDari"
                            value="{{ $filterDari }}"
                            style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                                   color:#374151;outline:none;cursor:pointer;">
                    </div>
                    <span style="color:#d1d5db;font-size:1rem;">—</span>
                    <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                                box-shadow:0 1px 2px rgba(0,0,0,0.05);overflow:hidden;background:#fff;">
                        <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;
                                     border-right:1px solid #e5e7eb;white-space:nowrap;">Sampai</span>
                        <input type="date" wire:model.live="filterSampai"
                            value="{{ $filterSampai }}"
                            style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                                   color:#374151;outline:none;cursor:pointer;">
                    </div>
                </div>
            @endif

            @if ($filterMode === 'bulanan')
                @if ($this->hasSaldoAwal)
                    <div style="display:inline-flex;align-items:center;gap:0.5rem;border-radius:0.5rem;
                                background:#eff6ff;border:1px solid #bfdbfe;padding:0.45rem 0.875rem;font-size:0.875rem;">
                        <span style="color:#2563eb;font-weight:500;">Saldo Awal:</span>
                        <span style="font-weight:700;color:#1d40af;font-variant-numeric:tabular-nums;">
                            Rp {{ number_format($this->saldoAwal, 0, ',', '.') }}
                        </span>
                    </div>
                @else
                    <div style="display:inline-flex;align-items:center;gap:0.5rem;border-radius:0.5rem;
                                background:#fffbeb;border:1px solid #fde68a;padding:0.45rem 0.875rem;font-size:0.875rem;">
                        <x-heroicon-o-exclamation-triangle style="width:1rem;height:1rem;color:#d97706;flex-shrink:0;" />
                        <span style="color:#92400e;">Saldo awal belum diset untuk bulan ini.</span>
                    </div>
                @endif
            @endif

        </div>

        {{-- ══════════════════════════════════════════════════════════
             SUMMARY KAS HARI INI
        ══════════════════════════════════════════════════════════════ --}}
        @php
            $kasHariIni = $this->kasHariIni;
            $kasPositif = $kasHariIni >= 0;
        @endphp
        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;
                    border-radius:0.75rem;border:1px solid {{ $kasPositif ? '#bbf7d0' : '#fecaca' }};
                    background:{{ $kasPositif ? '#f0fdf4' : '#fff1f2' }};">
            <div style="display:flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;
                        border-radius:0.5rem;background:{{ $kasPositif ? '#dcfce7' : '#fee2e2' }};flex-shrink:0;">
                <x-heroicon-o-banknotes style="width:1.1rem;height:1.1rem;color:{{ $kasPositif ? '#16a34a' : '#dc2626' }};" />
            </div>
            <div>
                <div style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;
                            color:{{ $kasPositif ? '#15803d' : '#b91c1c' }};margin-bottom:0.15rem;">
                    Kas Hari Ini
                </div>
                <div style="font-size:1rem;font-weight:800;font-variant-numeric:tabular-nums;
                            color:{{ $kasPositif ? '#15803d' : '#b91c1c' }};">
                    {{ $kasPositif ? '' : '−' }}Rp {{ number_format(abs($kasHariIni), 0, ',', '.') }}
                </div>
            </div>
            <div style="margin-left:auto;font-size:0.75rem;color:{{ $kasPositif ? '#4ade80' : '#f87171' }};
                        font-weight:500;text-align:right;white-space:nowrap;">
                {{ now()->translatedFormat('d F Y') }}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TABEL KAS HARIAN
        ══════════════════════════════════════════════════════════════ --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

            <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;
                        display:flex;align-items:center;justify-content:space-between;gap:0.5rem;flex-wrap:wrap;">
                <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                    Kas Harian &mdash; {{ $this->judulPeriode }}
                </h2>
                <span style="font-size:0.75rem;color:#9ca3af;">
                    {{ count($this->entries) }} transaksi
                </span>
            </div>

            @if (count($this->entries) === 0)
                <div style="padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                    <x-heroicon-o-document-text style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.5;" />
                    <p style="font-size:0.875rem;">Belum ada transaksi untuk periode ini.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
                        <thead>
                            <tr style="background:#1f2937;color:#fff;">
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:2.5rem;">No</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:6rem;white-space:nowrap;">Tanggal</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">Uraian</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:10rem;">Akun</th>
                                <th style="padding:0.75rem 1rem;text-align:right;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:8rem;">Debit</th>
                                <th style="padding:0.75rem 1rem;text-align:right;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:8rem;">Kredit</th>
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:3rem;">Bukti</th>
                                <th style="padding:0.75rem 1rem;text-align:right;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:9rem;">Saldo</th>
                                <th style="padding:0.75rem 1rem;width:4rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->entries as $i => $e)
                                @php $isPembayaran = $e['source'] === 'pembayaran'; @endphp
                                <tr style="border-bottom:1px solid #f8fafc;
                                           {{ $isPembayaran ? 'background:#fefce8;' : 'background:#fff;' }}"
                                    onmouseover="this.style.background='#f9fafb'"
                                    onmouseout="this.style.background='{{ $isPembayaran ? '#fefce8' : '#fff' }}'">

                                    <td style="padding:0.75rem 1rem;text-align:center;
                                               color:#9ca3af;font-size:0.75rem;">
                                        {{ $i + 1 }}
                                    </td>
                                    <td style="padding:0.75rem 1rem;color:#6b7280;
                                               font-size:0.75rem;white-space:nowrap;">
                                        {{ $e['tanggal'] }}
                                    </td>
                                    <td style="padding:0.75rem 1rem;color:#1f2937;">
                                        <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap;">
                                            @if ($isPembayaran)
                                                <span style="font-size:0.7rem;background:#dbeafe;color:#1d4ed8;
                                                             border-radius:0.25rem;padding:0.15rem 0.4rem;
                                                             font-weight:600;white-space:nowrap;flex-shrink:0;">
                                                    Siswa
                                                </span>
                                            @endif
                                            <span>{{ $e['uraian'] }}</span>
                                        </div>
                                    </td>
                                    <td style="padding:0.75rem 1rem;color:#9ca3af;font-size:0.75rem;">
                                        {{ $e['akun'] ?? '—' }}
                                    </td>
                                    <td style="padding:0.75rem 1rem;text-align:right;font-weight:600;
                                               color:#15803d;font-variant-numeric:tabular-nums;">
                                        @if ($e['debit'])
                                            {{ number_format($e['debit'], 0, ',', '.') }}
                                        @else
                                            <span style="color:#d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:0.75rem 1rem;text-align:right;font-weight:600;
                                               color:#dc2626;font-variant-numeric:tabular-nums;">
                                        @if ($e['kredit'])
                                            {{ number_format($e['kredit'], 0, ',', '.') }}
                                        @else
                                            <span style="color:#d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:0.75rem 1rem;text-align:center;">
                                        @if ($e['bukti'])
                                            <button type="button"
                                                x-data
                                                x-on:click="
                                                    $nextTick(() => {
                                                        $dispatch('open-bukti-kas', { url: '{{ $e['bukti_url'] }}' });
                                                    });
                                                "
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition cursor-pointer">
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </button>
                                        @else
                                            <span style="color:#d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:0.75rem 1rem;text-align:right;font-weight:700;
                                               color:#111827;font-variant-numeric:tabular-nums;">
                                        {{ number_format($e['saldo'], 0, ',', '.') }}
                                    </td>
                                    <td style="padding:0.75rem 0.5rem;text-align:center;">
                                        @if ($e['source'] === 'manual')
                                            <div style="display:flex;align-items:center;gap:0.25rem;justify-content:center;">
                                                {{-- Tombol Edit --}}
                                                <button
                                                    wire:click="mountAction('editKas', { id: {{ $e['id'] }} })"
                                                    style="background:none;border:none;cursor:pointer;
                                                           color:#d1d5db;padding:0.25rem;border-radius:0.25rem;"
                                                    onmouseover="this.style.color='#2563eb';this.style.background='#eff6ff'"
                                                    onmouseout="this.style.color='#d1d5db';this.style.background='none'"
                                                    title="Edit">
                                                    <x-heroicon-o-pencil-square style="width:1rem;height:1rem;" />
                                                </button>
                                                {{-- Tombol Hapus --}}
                                                <button
                                                    wire:click="mountAction('deleteKas', { id: {{ $e['id'] }} })"
                                                    style="background:none;border:none;cursor:pointer;
                                                           color:#d1d5db;padding:0.25rem;border-radius:0.25rem;"
                                                    onmouseover="this.style.color='#ef4444';this.style.background='#fff1f2'"
                                                    onmouseout="this.style.color='#d1d5db';this.style.background='none'"
                                                    title="Hapus">
                                                    <x-heroicon-o-trash style="width:1rem;height:1rem;" />
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer Total --}}
                <div style="margin:0;padding:1rem 1.5rem;background:#1f2937;
                            display:flex;flex-wrap:wrap;justify-content:flex-end;gap:2rem;
                            align-items:center;">
                    <div style="text-align:right;">
                        <div style="font-size:0.68rem;color:#6b7280;text-transform:uppercase;
                                    letter-spacing:0.07em;font-weight:600;margin-bottom:0.3rem;">
                            Total Debit
                        </div>
                        <div style="font-weight:700;color:#4ade80;font-variant-numeric:tabular-nums;
                                    font-size:0.9rem;">
                            Rp {{ number_format($this->totalDebit, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.68rem;color:#6b7280;text-transform:uppercase;
                                    letter-spacing:0.07em;font-weight:600;margin-bottom:0.3rem;">
                            Total Kredit
                        </div>
                        <div style="font-weight:700;color:#f87171;font-variant-numeric:tabular-nums;
                                    font-size:0.9rem;">
                            Rp {{ number_format($this->totalKredit, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="text-align:right;border-left:1px solid #374151;padding-left:2rem;">
                        <div style="font-size:0.68rem;color:#6b7280;text-transform:uppercase;
                                    letter-spacing:0.07em;font-weight:600;margin-bottom:0.3rem;">
                            Saldo Akhir
                        </div>
                        <div style="font-weight:800;color:#fde68a;font-variant-numeric:tabular-nums;
                                    font-size:1.05rem;">
                            Rp {{ number_format($this->saldoAkhir, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Legend --}}
                <div style="padding:0.75rem 1.5rem;background:#f9fafb;border-top:1px solid #f1f5f9;
                            display:flex;gap:1.25rem;">
                    <span style="display:inline-flex;align-items:center;gap:0.4rem;
                                 font-size:0.75rem;color:#9ca3af;">
                        <span style="width:0.75rem;height:0.75rem;border-radius:0.2rem;
                                     background:#fefce8;border:1px solid #fde68a;display:inline-block;
                                     flex-shrink:0;"></span>
                        Otomatis dari pembayaran siswa
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:0.4rem;
                                 font-size:0.75rem;color:#9ca3af;">
                        <span style="width:0.75rem;height:0.75rem;border-radius:0.2rem;
                                     background:#fff;border:1px solid #e5e7eb;display:inline-block;
                                     flex-shrink:0;"></span>
                        Input manual
                    </span>
                </div>

            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════
             LOG AKTIVITAS
        ══════════════════════════════════════════════════════════════ --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

            <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;
                        display:flex;align-items:center;justify-content:space-between;">
                <h2 style="font-size:0.9rem;font-weight:700;color:#1f2937;margin:0;
                            display:flex;align-items:center;gap:0.5rem;">
                    <x-heroicon-o-clipboard-document-list style="width:1rem;height:1rem;color:#6b7280;" />
                    Log Aktivitas
                </h2>
                <span style="font-size:0.75rem;color:#9ca3af;">50 entri terakhir</span>
            </div>

            @if (count($this->logEntries) === 0)
                <div style="padding:2rem 1.5rem;text-align:center;color:#9ca3af;font-size:0.875rem;">
                    Belum ada aktivitas tercatat.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:0.6rem 1rem;text-align:left;font-size:0.68rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           color:#6b7280;white-space:nowrap;">Waktu</th>
                                <th style="padding:0.6rem 1rem;text-align:left;font-size:0.68rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           color:#6b7280;width:5rem;">Aksi</th>
                                <th style="padding:0.6rem 1rem;text-align:left;font-size:0.68rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           color:#6b7280;">Keterangan</th>
                                <th style="padding:0.6rem 1rem;text-align:left;font-size:0.68rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           color:#6b7280;width:8rem;">Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->logEntries as $log)
                                @php
                                    $aksiConfig = match($log['aksi']) {
                                        'buat'  => ['label' => 'Buat',  'bg' => '#dcfce7', 'color' => '#15803d'],
                                        'edit'  => ['label' => 'Edit',  'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                        'hapus' => ['label' => 'Hapus', 'bg' => '#fee2e2', 'color' => '#b91c1c'],
                                        default => ['label' => $log['aksi'], 'bg' => '#f3f4f6', 'color' => '#374151'],
                                    };
                                @endphp
                                <tr style="border-bottom:1px solid #f8fafc;"
                                    onmouseover="this.style.background='#fafafa'"
                                    onmouseout="this.style.background='transparent'">
                                    <td style="padding:0.65rem 1rem;color:#9ca3af;white-space:nowrap;font-size:0.75rem;">
                                        {{ $log['waktu'] }}
                                    </td>
                                    <td style="padding:0.65rem 1rem;">
                                        <span style="display:inline-block;padding:0.15rem 0.5rem;border-radius:0.3rem;
                                                     font-size:0.7rem;font-weight:700;
                                                     background:{{ $aksiConfig['bg'] }};color:{{ $aksiConfig['color'] }};">
                                            {{ $aksiConfig['label'] }}
                                        </span>
                                    </td>
                                    <td style="padding:0.65rem 1rem;color:#374151;">
                                        {{ $log['keterangan'] ?? '—' }}
                                    </td>
                                    <td style="padding:0.65rem 1rem;color:#6b7280;font-size:0.75rem;">
                                        {{ $log['admin'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

{{-- Popup preview bukti transaksi --}}
<div
    x-data="{ open: false, url: '' }"
    x-on:open-bukti-kas.window="url = $event.detail.url; open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 p-4"
    x-on:click.self="open = false"
>
    <div class="relative w-full max-w-2xl max-h-[80vh] bg-white rounded-xl shadow-2xl overflow-auto">
        <button type="button"
            x-on:click="open = false"
            class="sticky top-2 z-10 ml-auto mr-2 block w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
        <div class="p-4 pt-0 flex items-start justify-center">
            <img :src="url" alt="Bukti Transaksi" class="max-w-full h-auto rounded">
        </div>
    </div>
</div>
</x-filament-panels::page>
