<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- ══════════════════════════════════════════════════════════
         FILTER BULAN & TAHUN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;">

        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
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
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                    Nominal Gaji (Rp)</th>
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

                                    <td style="padding:0.75rem 1rem;text-align:center;">
                                        <span style="display:inline-block;padding:0.3rem 0.75rem;
                                                     border-radius:0.75rem;font-size:0.8rem;font-weight:700;
                                                     background:{{ $data['hari_masuk'] > 0 ? '#dcfce7' : '#f3f4f6' }};
                                                     color:{{ $data['hari_masuk'] > 0 ? '#15803d' : '#9ca3af' }};">
                                            {{ $data['hari_masuk'] }} hari
                                        </span>
                                    </td>

                                    {{-- Nominal (bersih) + tombol rinci --}}
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
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

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
            <li>Klik ikon <strong>pensil</strong> untuk mengisi komponen gaji, potongan, dan keterangan.</li>
            <li>Nominal Gaji = (Gaji Pokok + Tunjangan + Transport + THR) − Potongan.</li>
            <li>Klik <strong>"Cetak Slip Gaji"</strong> untuk mencetak semua slip dalam satu PDF.</li>
        </ul>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL KOMPONEN GAJI + POTONGAN + KETERANGAN
══════════════════════════════════════════════════════════════════ --}}
@if ($showModal && $modalKaryawanId && isset($gajiForm[$modalKaryawanId]))
    @php $md = $gajiForm[$modalKaryawanId]; @endphp

    <div wire:click="tutupModal"
        style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;"></div>

    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
                z-index:51;width:100%;max-width:28rem;background:#fff;
                border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;">

        <div style="padding:1.25rem 1.5rem;background:#1f2937;color:#fff;
                    display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:1rem;font-weight:700;">Rincian Gaji</div>
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

        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">

            <div style="font-size:0.75rem;font-weight:600;color:#059669;text-transform:uppercase;
                        letter-spacing:0.05em;margin-bottom:0.25rem;">Pemasukan</div>

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

            {{-- Potongan --}}
            <div style="border-top:1px solid #fecaca;padding-top:0.75rem;">
                <div style="font-size:0.75rem;font-weight:600;color:#dc2626;text-transform:uppercase;
                            letter-spacing:0.05em;margin-bottom:0.5rem;">Potongan</div>
                <div>
                    <div style="display:flex;align-items:center;border:1.5px solid #fecaca;
                                border-radius:0.5rem;overflow:hidden;background:#fff;">
                        <span style="padding:0 0.625rem;font-size:0.78rem;color:#dc2626;
                                     border-right:1px solid #fecaca;background:#fef2f2;
                                     white-space:nowrap;align-self:stretch;display:flex;
                                     align-items:center;">Rp</span>
                        <input type="text" inputmode="numeric"
                            wire:model.lazy="gajiForm.{{ $modalKaryawanId }}.potongan"
                            placeholder="0"
                            onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"
                            style="flex:1;border:none;padding:0.55rem 0.75rem;font-size:0.875rem;
                                   color:#dc2626;outline:none;background:transparent;">
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div style="border-top:1px solid #e5e7eb;padding-top:0.75rem;">
                <div style="font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;
                            letter-spacing:0.05em;margin-bottom:0.5rem;">Keterangan</div>
                <input type="text"
                    wire:model.defer="gajiForm.{{ $modalKaryawanId }}.keterangan"
                    placeholder="(opsional)"
                    style="width:100%;padding:0.55rem 0.75rem;border:1.5px solid #d1d5db;
                           border-radius:0.5rem;font-size:0.875rem;color:#374151;outline:none;
                           background:#fff;">
            </div>

            {{-- Ringkasan --}}
            @php
                $grossM = (int)preg_replace('/[^0-9]/', '', $md['gaji_pokok'] ?? '0')
                        + (int)preg_replace('/[^0-9]/', '', $md['tunjangan']  ?? '0')
                        + (int)preg_replace('/[^0-9]/', '', $md['transport']  ?? '0')
                        + (int)preg_replace('/[^0-9]/', '', $md['thr']        ?? '0');
                $potM   = (int)preg_replace('/[^0-9]/', '', $md['potongan'] ?? '0');
                $netM   = $grossM - $potM;
            @endphp

            <div style="padding:0.875rem 1rem;background:#f0fdf4;border-radius:0.5rem;
                        border:1px solid #bbf7d0;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <span style="font-size:0.8rem;font-weight:600;color:#15803d;">Total Pemasukan</span>
                    <span style="font-size:0.95rem;font-weight:800;color:#14532d;">
                        Rp {{ number_format($grossM, 0, ',', '.') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <span style="font-size:0.75rem;font-weight:600;color:#dc2626;">Potongan</span>
                    <span style="font-size:0.85rem;font-weight:700;color:#dc2626;">
                        Rp {{ number_format($potM, 0, ',', '.') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:4px;
                            border-top:1px solid #86efac;">
                    <span style="font-size:0.85rem;font-weight:700;color:#15803d;">Nominal Bersih</span>
                    <span style="font-size:1.1rem;font-weight:800;color:#14532d;">
                        Rp {{ number_format($netM, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

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
