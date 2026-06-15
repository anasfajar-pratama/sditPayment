<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ══ INFO TAHUN AJARAN ════════════════════════════════════════════════ --}}
        @php
            $now     = now();
            $taMulai = $now->month >= 7 ? $now->year : $now->year - 1;
            $ta      = $taMulai . '/' . ($taMulai + 1);
        @endphp
        <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;
                    border-radius:0.5rem;background:#eff6ff;border:1px solid #bfdbfe;font-size:0.85rem;">
            <x-heroicon-o-calendar-days style="width:1rem;height:1rem;color:#2563eb;" />
            <span style="color:#1d4ed8;font-weight:600;">Tahun Ajaran Berjalan: {{ $ta }}</span>
            <span style="color:#93c5fd;font-size:0.75rem;">
                — Kelas lama akan disimpan sebagai riwayat setelah proses kenaikan
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

            @if ($filterJenisSekolah && count($this->kelasList) > 0)
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.05);">
                    <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;border-right:1px solid #e5e7eb;">Kelas</span>
                    <select wire:model.live="filterKelas"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;min-width:6rem;">
                        <option value="">— Pilih —</option>
                        @foreach ($this->kelasList as $k)
                            <option value="{{ $k }}" @selected($filterKelas===$k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

        </div>

        {{-- ══ PLACEHOLDER ══════════════════════════════════════════════════════ --}}
        @if (! $filterJenisSekolah || ! $filterKelas)
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                        padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <x-heroicon-o-academic-cap style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.4;" />
                <p style="font-size:0.875rem;">Pilih jenis sekolah dan kelas untuk melihat daftar siswa.</p>
            </div>

        @elseif (empty($this->siswaDiKelas))
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                        padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <p style="font-size:0.875rem;">Tidak ada siswa aktif di kelas ini.</p>
            </div>

        @else

        {{-- ══ HEADER INFO ══════════════════════════════════════════════════════ --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                    {{ $filterJenisSekolah }} — Kelas {{ $filterKelas }}
                </h2>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0.2rem 0 0;">
                    {{ count($this->siswaDiKelas) }} siswa aktif
                </p>
            </div>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                <span style="font-size:0.75rem;color:#9ca3af;">
                    Tombol "Proses Kenaikan Kelas" ada di pojok kanan atas
                </span>
            </div>
        </div>

        {{-- ══ TABEL SISWA ══════════════════════════════════════════════════════ --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:0.825rem;">
                <thead>
                    <tr style="background:#1f2937;color:#fff;">
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:2.5rem;">No</th>
                        <th style="padding:0.7rem 1rem;text-align:left;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;">Nama Siswa</th>
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:5rem;">NIS</th>
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:5rem;">Kelas Saat Ini</th>
                        <th style="padding:0.7rem 1rem;text-align:left;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;">Riwayat Kelas</th>
                        <th style="padding:0.7rem 1rem;text-align:center;font-size:0.68rem;font-weight:600;
                                   letter-spacing:0.05em;text-transform:uppercase;width:7rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->siswaDiKelas as $i => $siswa)
                        <tr style="border-bottom:1px solid #f8fafc;"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='transparent'">

                            <td style="padding:0.75rem 1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">
                                {{ $i + 1 }}
                            </td>

                            <td style="padding:0.75rem 1rem;font-weight:600;color:#1f2937;">
                                {{ $siswa['nama'] }}
                            </td>

                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <span style="font-family:monospace;font-size:0.75rem;background:#f3f4f6;
                                             color:#374151;border-radius:0.25rem;padding:0.15rem 0.4rem;">
                                    {{ $siswa['nis'] ?: '—' }}
                                </span>
                            </td>

                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <span style="background:#dbeafe;color:#1d4ed8;border-radius:0.3rem;
                                             padding:0.2rem 0.5rem;font-size:0.75rem;font-weight:700;">
                                    {{ $siswa['kelas'] }}
                                </span>
                            </td>

                            <td style="padding:0.75rem 1rem;">
                                @if (empty($siswa['history']))
                                    <span style="color:#d1d5db;font-size:0.75rem;">Belum ada riwayat</span>
                                @else
                                    <div style="display:flex;flex-wrap:wrap;gap:0.3rem;">
                                        @foreach ($siswa['history'] as $h)
                                            <span style="display:inline-flex;align-items:center;gap:0.25rem;
                                                         background:#f3f4f6;border-radius:0.3rem;
                                                         padding:0.15rem 0.5rem;font-size:0.7rem;color:#374151;
                                                         border:1px solid #e5e7eb;">
                                                <span style="font-weight:600;">{{ $h['tahun_ajaran'] }}</span>
                                                <span style="color:#9ca3af;">→</span>
                                                <span style="color:#1d4ed8;font-weight:600;">{{ $h['kelas'] }}</span>
                                                <span style="color:#9ca3af;font-size:0.65rem;">({{ $h['jenis_sekolah'] }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <button
                                    wire:click="mountAction('naikKelas', { siswa_id: {{ $siswa['id'] }} })"
                                    style="display:inline-flex;align-items:center;gap:0.3rem;
                                           background:#f0fdf4;color:#15803d;border:1px solid #86efac;
                                           border-radius:0.4rem;padding:0.35rem 0.7rem;font-size:0.75rem;
                                           font-weight:600;cursor:pointer;"
                                    onmouseover="this.style.background='#dcfce7'"
                                    onmouseout="this.style.background='#f0fdf4'">
                                    <x-heroicon-o-arrow-up-circle style="width:0.9rem;height:0.9rem;" />
                                    Naik Kelas
                                </button>
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
                    <li>Klik <strong>Naik Kelas</strong> per-siswa untuk memindah satu siswa ke kelas baru</li>
                    <li>Klik <strong>Proses Kenaikan Kelas (Semua)</strong> di pojok kanan atas untuk memindah semua siswa di kelas ini sekaligus</li>
                    <li>Kelas lama otomatis tersimpan sebagai <strong>riwayat</strong> dengan tahun ajaran berjalan ({{ $ta }})</li>
                    <li>Riwayat kelas juga tampil di <strong>Inquiry Siswa → Riwayat Pembayaran per Tahun Ajaran</strong></li>
                </ul>
            </div>
        </div>

        @endif

    </div>
</x-filament-panels::page>
