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
                        @foreach([
                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                        ] as $v => $l)
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

            {{-- Tandai Sudah Bayar --}}
            @if ($this->sudahAda)
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

        {{-- ══════════════════════════════════════════════════════════
             RINGKASAN TOTAL
        ══════════════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-wrap:wrap;gap:1rem;">
            <div style="flex:1;min-width:8rem;padding:1rem 1.25rem;background:#f0fdf4;
                        border:1px solid #bbf7d0;border-radius:0.75rem;">
                <div style="font-size:0.7rem;font-weight:600;color:#15803d;letter-spacing:0.05em;
                             text-transform:uppercase;margin-bottom:0.25rem;">Total Guru</div>
                <div style="font-size:1.75rem;font-weight:800;color:#14532d;">
                    {{ count($gajiForm) }}
                </div>
            </div>
            <div style="flex:2;min-width:10rem;padding:1rem 1.25rem;background:#eff6ff;
                        border:1px solid #bfdbfe;border-radius:0.75rem;">
                <div style="font-size:0.7rem;font-weight:600;color:#1d4ed8;letter-spacing:0.05em;
                             text-transform:uppercase;margin-bottom:0.25rem;">Total Gaji Kotor</div>
                <div style="font-size:1.5rem;font-weight:800;color:#1e3a8a;">
                    Rp {{ number_format($this->totalNominal(), 0, ',', '.') }}
                </div>
            </div>
            <div style="flex:1;min-width:8rem;padding:1rem 1.25rem;background:#fef2f2;
                        border:1px solid #fecaca;border-radius:0.75rem;">
                <div style="font-size:0.7rem;font-weight:600;color:#dc2626;letter-spacing:0.05em;
                             text-transform:uppercase;margin-bottom:0.25rem;">Total Potongan</div>
                <div style="font-size:1.5rem;font-weight:800;color:#991b1b;">
                    Rp {{ number_format($this->totalPotongan(), 0, ',', '.') }}
                </div>
            </div>
            <div style="flex:2;min-width:10rem;padding:1rem 1.25rem;background:#f0fdf4;
                        border:1px solid #86efac;border-radius:0.75rem;">
                <div style="font-size:0.7rem;font-weight:600;color:#15803d;letter-spacing:0.05em;
                             text-transform:uppercase;margin-bottom:0.25rem;">Total Bersih Diterima</div>
                <div style="font-size:1.75rem;font-weight:800;color:#14532d;">
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
                                                width:10rem;">Nominal Gaji (Rp)</th>
                                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                                font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                                width:9rem;">Potongan (Rp)</th>
                                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                                font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                                width:10rem;">Keterangan</th>
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

                                        {{-- Hari masuk — read-only dari absen --}}
                                        <td style="padding:0.75rem 1rem;text-align:center;">
                                            <span style="display:inline-block;padding:0.3rem 0.75rem;
                                                         border-radius:0.75rem;font-size:0.8rem;font-weight:700;
                                                         background:{{ $data['hari_masuk'] > 0 ? '#dcfce7' : '#f3f4f6' }};
                                                         color:{{ $data['hari_masuk'] > 0 ? '#15803d' : '#9ca3af' }};">
                                                {{ $data['hari_masuk'] }} hari
                                            </span>
                                        </td>

                                        {{-- Input nominal gaji --}}
                                        <td style="padding:0.5rem 1rem;">
                                            <div style="display:flex;align-items:center;
                                                        border:1.5px solid #e5e7eb;border-radius:0.5rem;
                                                        overflow:hidden;background:#fff;">
                                                <span style="padding:0 0.5rem;font-size:0.75rem;
                                                             color:#9ca3af;border-right:1px solid #e5e7eb;
                                                             background:#f9fafb;white-space:nowrap;">Rp</span>
                                                <input type="text" inputmode="numeric"
                                                    wire:model.defer="gajiForm.{{ $karyawanId }}.nominal"
                                                    placeholder="0"
                                                    onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"
                                                    style="flex:1;border:none;padding:0.45rem 0.625rem;
                                                           font-size:0.8rem;color:#1f2937;outline:none;
                                                           background:transparent;">
                                            </div>
                                        </td>

                                        {{-- Input potongan --}}
                                        <td style="padding:0.5rem 1rem;">
                                            <div style="display:flex;align-items:center;
                                                        border:1.5px solid #fecaca;border-radius:0.5rem;
                                                        overflow:hidden;background:#fff;">
                                                <span style="padding:0 0.5rem;font-size:0.75rem;
                                                             color:#dc2626;border-right:1px solid #fecaca;
                                                             background:#fef2f2;white-space:nowrap;">Rp</span>
                                                <input type="text" inputmode="numeric"
                                                    wire:model.defer="gajiForm.{{ $karyawanId }}.potongan"
                                                    placeholder="0"
                                                    onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"
                                                    style="flex:1;border:none;padding:0.45rem 0.625rem;
                                                           font-size:0.8rem;color:#dc2626;outline:none;
                                                           background:transparent;">
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
                                        style="padding:0.625rem 1rem;text-align:right;
                                               font-size:0.8rem;font-weight:600;color:#6b7280;">
                                        Total Gaji
                                    </td>
                                    <td style="padding:0.625rem 1rem;font-size:0.875rem;font-weight:800;color:#1e3a8a;">
                                        Rp {{ number_format($this->totalNominal(), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr style="background:#fef2f2;border-top:1px solid #fecaca;">
                                    <td colspan="4"
                                        style="padding:0.625rem 1rem;text-align:right;
                                               font-size:0.8rem;font-weight:600;color:#dc2626;">
                                        Total Potongan
                                    </td>
                                    <td style="padding:0.625rem 1rem;font-size:0.875rem;font-weight:800;color:#dc2626;">
                                        Rp {{ number_format($this->totalPotongan(), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr style="background:#f0fdf4;border-top:2px solid #86efac;">
                                    <td colspan="4"
                                        style="padding:0.875rem 1rem;text-align:right;
                                               font-size:0.875rem;font-weight:700;color:#15803d;">
                                        Total Bersih
                                    </td>
                                    <td style="padding:0.875rem 1rem;font-size:1rem;font-weight:800;color:#15803d;">
                                        Rp {{ number_format($this->totalGaji(), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- ══════════════════════════════════════════
                         TOMBOL SUBMIT — 1 BUTTON
                    ══════════════════════════════════════════════ --}}
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
        <div style="padding:1rem 1.25rem;border-radius:0.75rem;
                    background:#eff6ff;border:1px solid #bfdbfe;">
            <p style="font-size:0.75rem;color:#1d4ed8;margin:0 0 0.25rem;font-weight:600;">
                💡 Catatan
            </p>
            <ul style="font-size:0.75rem;color:#1e40af;margin:0;padding-left:1.1rem;
                       list-style-type:disc;display:flex;flex-direction:column;gap:0.25rem;">
                <li>Jumlah <strong>Hari Masuk</strong> dihitung otomatis dari data absen harian (status Hadir + Dinas).</li>
                <li>Isi <strong>Potongan</strong> jika ada potongan gaji (alfa, terlambat, dll).</li>
                <li>Pastikan absen bulan ini sudah lengkap sebelum input gaji.</li>
                <li>Klik <strong>"Tandai Sudah Dibayar"</strong> setelah transfer/pembayaran dilakukan.</li>
                <li>Edit gaji cukup ubah nominal lalu klik <strong>"Simpan Perubahan"</strong>.</li>
            </ul>
        </div>

    </div>
</x-filament-panels::page>
