<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ══ INFO TAHUN AJARAN ════════════════════════════════════════════════ --}}
        @php
            $taBerjalan = $this->getTahunAjaranBerjalan();
        @endphp
        <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;
                    border-radius:0.5rem;background:#eff6ff;border:1px solid #bfdbfe;font-size:0.85rem;">
            <x-heroicon-o-calendar-days style="width:1rem;height:1rem;color:#2563eb;" />
            <span style="color:#1d4ed8;font-weight:600;">
                {{ $taBerjalan }}
                <x-heroicon-o-arrow-right style="display:inline;width:0.85rem;height:0.85rem;margin:0 0.25rem;" />
                {{ $targetTahunAjaran }}
            </span>
            <span style="color:#93c5fd;font-size:0.75rem;">
                — Kenaikan kelas ke tahun ajaran baru
            </span>
        </div>

        {{-- ══ FILTER BAR ══════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.75rem;">

            {{-- Tabs: Jenis Sekolah --}}
            <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                        overflow:hidden;background:#f9fafb;flex-shrink:0;">
                @foreach ($this->jenisSekolahList as $js)
                    <button wire:click="$set('filterJenisSekolah', '{{ $js }}')"
                        style="padding:0.45rem 0.9rem;font-size:0.8rem;border:none;cursor:pointer;white-space:nowrap;
                               font-weight:{{ $filterJenisSekolah===$js?'700':'500' }};
                               background:{{ $filterJenisSekolah===$js?'#1f2937':'transparent' }};
                               color:{{ $filterJenisSekolah===$js?'#fff':'#6b7280' }};
                               border-right:1px solid #e5e7eb;">
                        {{ $js }}
                    </button>
                @endforeach
            </div>

            {{-- Target Tahun Ajaran --}}
            @if ($filterJenisSekolah)
                @php
                    $taBerjalanStart = (int) explode('/', $taBerjalan)[0];
                @endphp
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.05);">
                    <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;border-right:1px solid #e5e7eb;">Target T.A.</span>
                    <select wire:model.live="targetTahunAjaran"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;min-width:8rem;">
                        @for ($i = 0; $i <= 3; $i++)
                            @php
                                $start = $taBerjalanStart + $i;
                                $ta    = "{$start}/" . ($start + 1);
                            @endphp
                            <option value="{{ $ta }}" @selected($targetTahunAjaran === $ta)>{{ $ta }}</option>
                        @endfor
                    </select>
                </div>
            @endif

        </div>

        {{-- ══ PLACEHOLDER ══════════════════════════════════════════════════════ --}}
        @if (! $filterJenisSekolah)
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                        padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <x-heroicon-o-academic-cap style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.4;" />
                <p style="font-size:0.875rem;">Pilih jenis sekolah untuk memulai simulasi kenaikan kelas.</p>
            </div>

        @elseif (empty($this->kelasData))
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                        padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <p style="font-size:0.875rem;">Tidak ada kelas dengan siswa aktif untuk {{ $filterJenisSekolah }}.</p>
            </div>

        @else

        {{-- ══ HEADER INFO ══════════════════════════════════════════════════════ --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                    Mapping Kenaikan Kelas — {{ $filterJenisSekolah }}
                </h2>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0.2rem 0 0;">
                    {{ collect($this->kelasData)->sum('jumlah') }} siswa aktif &middot;
                    {{ count($this->kelasData) }} kelas
                </p>
            </div>
        </div>

        {{-- ══ TABEL MAPPING ════════════════════════════════════════════════════ --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:0.825rem;">
                <thead>
                    <tr style="background:#1f2937;color:#fff;">
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:2.5rem;">No</th>
                        <th style="padding:0.7rem 1rem;text-align:left;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;">Kelas Asal</th>
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:4rem;">Jml</th>
                        <th style="padding:0.7rem 1rem;text-align:left;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;">Kelas Tujuan</th>
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->kelasData as $i => $item)
                        <tr style="border-bottom:1px solid #f8fafc;"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='transparent'">

                            <td style="padding:0.75rem 1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">
                                {{ $i + 1 }}
                            </td>

                            <td style="padding:0.75rem 1rem;font-weight:600;color:#1f2937;">
                                {{ $item['kelas'] }}
                            </td>

                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <span style="font-family:monospace;font-size:0.75rem;background:#f3f4f6;
                                             color:#374151;border-radius:0.25rem;padding:0.15rem 0.4rem;">
                                    {{ $item['jumlah'] }}
                                </span>
                            </td>

                            <td style="padding:0.75rem 1rem;">
                                <select wire:model.live="kelasMapping.{{ $item['kelas'] }}"
                                    style="border:1px solid #d1d5db;border-radius:0.375rem;padding:0.35rem 0.6rem;
                                           font-size:0.8rem;color:#374151;background:#fff;outline:none;
                                           cursor:pointer;min-width:8rem;
                                           {{ empty($item['target']) ? 'border-color:#f59e0b;' : '' }}">
                                    @foreach ($this->getTargetOptions() as $val => $label)
                                        <option value="{{ $val }}" @selected(($this->kelasMapping[$item['kelas']] ?? '') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td style="padding:0.75rem 1rem;text-align:center;">
                                @php
                                    $target = $this->kelasMapping[$item['kelas']] ?? '';
                                @endphp
                                @if ($target)
                                    <span style="display:inline-flex;align-items:center;gap:0.25rem;
                                                 background:#f0fdf4;color:#15803d;border-radius:0.3rem;
                                                 padding:0.2rem 0.5rem;font-size:0.75rem;font-weight:600;">
                                        <x-heroicon-o-check-circle style="width:0.75rem;height:0.75rem;" />
                                        Siap
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:0.25rem;
                                                 background:#fffbeb;color:#d97706;border-radius:0.3rem;
                                                 padding:0.2rem 0.5rem;font-size:0.75rem;font-weight:600;">
                                        <x-heroicon-o-exclamation-triangle style="width:0.75rem;height:0.75rem;" />
                                        Isi
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ══ PETUNJUK ═════════════════════════════════════════════════════════ --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:0.75rem;
                    padding:1rem 1.25rem;display:flex;gap:0.75rem;align-items:flex-start;">
            <x-heroicon-o-light-bulb style="width:1.25rem;height:1.25rem;color:#d97706;flex-shrink:0;margin-top:0.1rem;" />
            <div style="font-size:0.8rem;color:#78350f;">
                <strong>Cara penggunaan:</strong>
                <ul style="margin:0.4rem 0 0;padding-left:1rem;line-height:1.8;">
                    <li>Pilih jenis sekolah dan atur kelas tujuan per baris (terisi otomatis, bisa diubah)</li>
                    <li>Klik <strong>Jalankan Simulasi</strong> di pojok kanan atas untuk melihat pratinjau</li>
                    <li>Setelah yakin, klik <strong>Proses Semua Kenaikan</strong> untuk menjalankan</li>
                    <li>Kelas lama otomatis tersimpan sebagai <strong>riwayat</strong> dengan tahun ajaran {{ $targetTahunAjaran }}</li>
                    <li>Siswa lulus akan dinonaktifkan (<em>status_aktif = false</em>)</li>
                    <li>Siswa naik kelas akan diperbarui <strong>kelas</strong> dan <strong>tahun_ajaran</strong>-nya</li>
                </ul>
            </div>
        </div>

        @endif

    </div>
</x-filament-panels::page>
