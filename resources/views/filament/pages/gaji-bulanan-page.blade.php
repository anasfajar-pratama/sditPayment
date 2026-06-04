<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- ══════════════════════════════════════════════════════════
         FILTER BULAN & TAHUN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;">

        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            {{-- Pilih Bulan --}}
            <div>
                <label style="display:block;font-size:0.7rem;font-weight:600;color:#6b7280;
                              letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.25rem;">Bulan</label>
                <select wire:model.live="filterBulan"
                    style="padding:0.5rem 0.875rem;border-radius:0.5rem;border:1px solid #d1d5db;
                           background:#fff;font-size:0.8rem;color:#374151;outline:none;
                           box-shadow:0 1px 2px rgba(0,0,0,0.04);cursor:pointer;">
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                              '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                              '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']
                              as $v => $l)
                        <option value="{{ $v }}" @selected($filterBulan === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Tahun --}}
            <div>
                <label style="display:block;font-size:0.7rem;font-weight:600;color:#6b7280;
                              letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.25rem;">Tahun</label>
                <select wire:model.live="filterTahun"
                    style="padding:0.5rem 0.875rem;border-radius:0.5rem;border:1px solid #d1d5db;
                           background:#fff;font-size:0.8rem;color:#374151;outline:none;
                           box-shadow:0 1px 2px rgba(0,0,0,0.04);cursor:pointer;">
                    @for ($y = now()->year + 1; $y >= 2022; $y--)
                        <option value="{{ $y }}" @selected((int)$filterTahun === $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Badge status --}}
            @if ($this->sudahAda)
                <div style="margin-top:1.35rem;padding:0.35rem 0.875rem;border-radius:1rem;
                            background:#dcfce7;border:1px solid #bbf7d0;
                            font-size:0.72rem;font-weight:600;color:#15803d;">
                    ✓ Data sudah ada — mode edit
                </div>
            @else
                <div style="margin-top:1.35rem;padding:0.35rem 0.875rem;border-radius:1rem;
                            background:#fef9c3;border:1px solid #fde68a;
                            font-size:0.72rem;font-weight:600;color:#a16207;">
                    ＋ Data baru
                </div>
            @endif
        </div>

        {{-- Tombol kanan: Cetak Slip + Tandai Sudah Bayar --}}
        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
            @if ($this->sudahAda)
                <a href="{{ $this->urlSlipGaji() }}" target="_blank"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;
                           border-radius:0.5rem;border:1px solid #bfdbfe;background:#eff6ff;
                           color:#1d4ed8;font-size:0.8rem;font-weight:600;cursor:pointer;
                           text-decoration:none;">
                    <x-heroicon-o-printer style="width:1rem;height:1rem;" />
                    Cetak Slip Gaji
                </a>

                <button wire:click="tandaiSudahBayar"
                    wire:confirm="Tandai semua gaji bulan {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }} sebagai sudah dibayar?"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;
                           border-radius:0.5rem;border:1px solid #bbf7d0;background:#f0fdf4;
                           color:#15803d;font-size:0.8rem;font-weight:600;cursor:pointer;">
                    <x-heroicon-o-check-badge style="width:1rem;height:1rem;" />
                    Tandai Sudah Dibayar
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RINGKASAN TOTAL
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
        <div style="flex:1;min-width:10rem;padding:1rem 1.5rem;background:#f0fdf4;
                    border:1px solid #bbf7d0;border-radius:0.75rem;">
            <div style="font-size:0.7rem;font-weight:600;color:#15803d;letter-spacing:0.05em;
                         text-transform:uppercase;margin-bottom:0.25rem;">Total Guru</div>
            <div style="font-size:1.75rem;font-weight:800;color:#14532d;">
                {{ count($gajiForm) }}
            </div>
        </div>
        <div style="flex:2;min-width:14rem;padding:1rem 1.5rem;background:#eff6ff;
                    border:1px solid #bfdbfe;border-radius:0.75rem;">
            <div style="font-size:0.7rem;font-weight:600;color:#1d4ed8;letter-spacing:0.05em;
                         text-transform:uppercase;margin-bottom:0.25rem;">Total Gaji Bulan Ini</div>
            <div style="font-size:1.75rem;font-weight:800;color:#1e3a8a;">
                Rp {{ number_format($this->totalGaji(), 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TABEL GAJI
    ══════════════════════════════════════════════════════════════ --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

        <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;
                    display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                Daftar Gaji — {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
            </h2>
            <span style="font-size:0.75rem;color:#9ca3af;">
                Hari masuk dihitung dari absen harian
            </span>
        </div>

        @if (empty($gajiForm))
            <div style="padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <x-heroicon-o-users style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.5;" />
                <p style="font-size:0.875rem;">Belum ada data guru aktif.</p>
            </div>
        @else
            <form wire:submit.prevent="simpanGaji">
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
                        <thead>
                            <tr style="background:#1f2937;color:#fff;">
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:2.5rem;">No</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                    Nama Guru</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:9rem;">Jabatan</th>
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:6.5rem;">Hari Masuk</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:14rem;">Nominal Gaji (Rp)</th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:11rem;">Keterangan</th>
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:7rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gajiForm as $karyawanId => $data)
                                @php
                                    $i = array_search($karyawanId, array_keys($gajiForm)) + 1;
                                    $statusBayar = $data['status_bayar'] ?? 'belum';
                                    $nominal = $this->hitungNominal($karyawanId);
                                @endphp
                                <tr style="border-bottom:1px solid #f8fafc;background:#fff;"
                                    onmouseover="this.style.background='#f9fafb'"
                                    onmouseout="this.style.background='#fff'">

                                    <td style="padding:0.75rem 1rem;text-align:center;
                                               color:#9ca3af;font-size:0.75rem;">{{ $i }}</td>

                                    <td style="padding:0.75rem 1rem;">
                                        <div style="font-weight:600;color:#1f2937;">{{ $data['nama'] }}</div>
                                    </td>

                                    <td style="padding:0.75rem 1rem;color:#6b7280;font-size:0.75rem;">
                                        {{ $data['jabatan'] ?? '—' }}
                                    </td>

                                    {{-- Hari masuk — read-only --}}
                                    <td style="padding:0.75rem 1rem;text-align:center;">
                                        <span style="display:inline-block;padding:0.3rem 0.75rem;
                                                     border-radius:0.75rem;font-size:0.8rem;font-weight:700;
                                                     background:{{ $data['hari_masuk'] > 0 ? '#dcfce7' : '#f3f4f6' }};
                                                     color:{{ $data['hari_masuk'] > 0 ? '#15803d' : '#9ca3af' }};">
                                            {{ $data['hari_masuk'] }} hari
                                        </span>
                                    </td>

                                    {{-- Nominal (akumulasi) + tombol rinci --}}
                                    <td style="padding:0.5rem 1rem;">
                                        <div style="display:flex;align-items:center;gap:0.5rem;">
                                            <div style="flex:1;display:flex;align-items:center;
                                                        border:1.5px solid #e5e7eb;border-radius:0.5rem;
                                                        overflow:hidden;background:#f9fafb;">
                                                <span style="padding:0 0.5rem;font-size:0.75rem;
                                                             color:#9ca3af;border-right:1px solid #e5e7eb;
                                                             background:#f1f5f9;white-space:nowrap;">Rp</span>
                                                <span style="flex:1;padding:0.45rem 0.625rem;
                                                             font-size:0.8rem;color:#1f2937;font-weight:600;">
                                                    {{ $nominal > 0 ? number_format($nominal, 0, ',', '.') : '0' }}
                                                </span>
                                            </div>
                                            {{-- Tombol buka modal rinci --}}
                                            <button type="button"
                                                wire:click="bukaModal({{ $karyawanId }})"
                                                title="Rinci komponen gaji"
                                                style="padding:0.4rem;border-radius:0.4rem;border:1px solid #e5e7eb;
                                                       background:#fff;cursor:pointer;color:#6b7280;
                                                       display:inline-flex;align-items:center;">
                                                <x-heroicon-o-pencil-square style="width:0.9rem;height:0.9rem;" />
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Keterangan --}}
                                    <td style="padding:0.5rem 1rem;">
                                        <input type="text"
                                            wire:model.defer="gajiForm.{{ $karyawanId }}.keterangan"
                                            placeholder="(opsional)"
                                            style="width:100%;padding:0.45rem 0.625rem;border-radius:0.5rem;
                                                   border:1.5px solid #e5e7eb;font-size:0.78rem;
                                                   color:#374151;outline:none;background:#fff;">
                                    </td>

                                    {{-- Status bayar --}}
                                    <td style="padding:0.5rem 1rem;text-align:center;">
                                        @if ($statusBayar === 'sudah')
                                            <span style="display:inline-block;padding:0.3rem 0.65rem;
                                                         border-radius:1rem;font-size:0.7rem;font-weight:600;
                                                         background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">
                                                ✓ Dibayar
                                            </span>
                                        @else
                                            <span style="display:inline-block;padding:0.3rem 0.65rem;
                                                         border-radius:1rem;font-size:0.7rem;font-weight:600;
                                                         background:#fef9c3;color:#a16207;border:1px solid #fde68a;">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        {{-- Footer total --}}
                        <tfoot>
                            <tr style="background:#f8fafc;border-top:2px solid #e5e7eb;">
                                <td colspan="4"
                                    style="padding:0.875rem 1rem;text-align:right;
                                           font-size:0.8rem;font-weight:600;color:#6b7280;">
                                    Total
                                </td>
                                <td style="padding:0.875rem 1rem;font-size:0.875rem;font-weight:800;color:#1e3a8a;">
                                    Rp {{ number_format($this->totalGaji(), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tombol submit --}}
                <div style="padding:1.25rem 1.5rem;border-top:1px solid #f1f5f9;
                            display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;">
                    <span style="font-size:0.75rem;color:#9ca3af;">
                        Bulan: <strong style="color:#374151;">
                            {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                        </strong>
                    </span>
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:0.5rem;
                               padding:0.625rem 1.75rem;border-radius:0.5rem;border:none;
                               background:#1f2937;color:#fff;font-size:0.875rem;font-weight:700;
                               cursor:pointer;box-shadow:0 2px 4px rgba(0,0,0,0.15);"
                        onmouseover="this.style.background='#111827'"
                        onmouseout="this.style.background='#1f2937'">
                        <x-heroicon-o-check style="width:1rem;height:1rem;" />
                        {{ $this->sudahAda ? 'Simpan Perubahan' : 'Simpan Gaji Bulan Ini' }}
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         PETUNJUK
    ══════════════════════════════════════════════════════════════ --}}
    <div style="padding:1rem 1.25rem;border-radius:0.75rem;background:#eff6ff;border:1px solid #bfdbfe;">
        <p style="font-size:0.75rem;color:#1d4ed8;margin:0 0 0.25rem;font-weight:600;">💡 Catatan</p>
        <ul style="font-size:0.75rem;color:#1e40af;margin:0;padding-left:1.1rem;
                   list-style-type:disc;display:flex;flex-direction:column;gap:0.25rem;">
            <li>Jumlah <strong>Hari Masuk</strong> dihitung otomatis dari data absen harian (status Hadir + Dinas).</li>
            <li>Klik ikon <strong>pensil</strong> di kolom Nominal untuk mengisi komponen gaji (Gaji Pokok, Tunjangan, Transport, THR).</li>
            <li>Kolom <strong>Nominal Gaji</strong> terisi otomatis dari total komponen.</li>
            <li>Klik <strong>"Cetak Slip Gaji"</strong> untuk mencetak semua slip dalam satu PDF.</li>
        </ul>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL KOMPONEN GAJI
══════════════════════════════════════════════════════════════════ --}}
@if ($showModal && $modalKaryawanId && isset($gajiForm[$modalKaryawanId]))
    @php $md = $gajiForm[$modalKaryawanId]; @endphp

    {{-- Overlay --}}
    <div wire:click="tutupModal"
        style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;"></div>

    {{-- Modal panel --}}
    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
                z-index:51;width:100%;max-width:28rem;background:#fff;
                border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;">

        {{-- Header --}}
        <div style="padding:1.25rem 1.5rem;background:#1f2937;color:#fff;
                    display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:1rem;font-weight:700;">Komponen Gaji</div>
                <div style="font-size:0.78rem;color:#9ca3af;margin-top:0.15rem;">
                    {{ $md['nama'] }} — {{ $this->getBulanLabel($filterBulan) }} {{ $filterTahun }}
                </div>
            </div>
            <button type="button" wire:click="tutupModal"
                style="background:transparent;border:none;color:#9ca3af;cursor:pointer;
                       padding:0.25rem;border-radius:0.375rem;">
                <x-heroicon-o-x-mark style="width:1.25rem;height:1.25rem;" />
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">

            @foreach ([
                'gaji_pokok' => 'Gaji Pokok',
                'tunjangan'  => 'Tunjangan',
                'transport'  => 'Uang Transport',
                'thr'        => 'THR',
            ] as $field => $label)
                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:#374151;
                                  margin-bottom:0.35rem;">{{ $label }}</label>
                    <div style="display:flex;align-items:center;border:1.5px solid #d1d5db;
                                border-radius:0.5rem;overflow:hidden;background:#fff;">
                        <span style="padding:0 0.625rem;font-size:0.78rem;color:#9ca3af;
                                     border-right:1px solid #e5e7eb;background:#f9fafb;
                                     white-space:nowrap;align-self:stretch;display:flex;
                                     align-items:center;">Rp</span>
                        <input type="text" inputmode="numeric"
                            wire:model.lazy="gajiForm.{{ $modalKaryawanId }}.{{ $field }}"
                            placeholder="0"
                            onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"
                            style="flex:1;border:none;padding:0.55rem 0.75rem;font-size:0.875rem;
                                   color:#1f2937;outline:none;background:transparent;">
                    </div>
                </div>
            @endforeach

            {{-- Subtotal preview --}}
            <div style="padding:0.875rem 1rem;background:#f0fdf4;border-radius:0.5rem;
                        border:1px solid #bbf7d0;display:flex;justify-content:space-between;
                        align-items:center;">
                <span style="font-size:0.8rem;font-weight:600;color:#15803d;">Total Gaji</span>
                <span style="font-size:1rem;font-weight:800;color:#14532d;">
                    Rp {{ number_format($this->hitungNominal($modalKaryawanId), 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:1rem 1.5rem;border-top:1px solid #f1f5f9;
                    display:flex;justify-content:flex-end;gap:0.5rem;">
            <button type="button" wire:click="tutupModal"
                style="padding:0.5rem 1.25rem;border-radius:0.5rem;border:1px solid #e5e7eb;
                       background:#fff;font-size:0.875rem;font-weight:600;color:#374151;cursor:pointer;">
                Tutup
            </button>
        </div>
    </div>
@endif

</x-filament-panels::page>
