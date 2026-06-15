<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- ══════════════════════════════════════════════════════════
             FILTER TANGGAL + TOMBOL HADIR SEMUA
        ══════════════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;">

            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                {{-- Navigasi hari --}}
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <button
                        wire:click="$set('filterTanggal', '{{ \Carbon\Carbon::parse($filterTanggal)->subDay()->toDateString() }}')"
                        style="padding:0.5rem 0.75rem;border:none;background:transparent;cursor:pointer;
                               color:#6b7280;font-size:1rem;border-right:1px solid #e5e7eb;"
                        title="Hari sebelumnya">‹</button>
                    <input type="date" wire:model.live="filterTanggal"
                        value="{{ $filterTanggal }}"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;">
                    <button
                        wire:click="$set('filterTanggal', '{{ \Carbon\Carbon::parse($filterTanggal)->addDay()->toDateString() }}')"
                        style="padding:0.5rem 0.75rem;border:none;background:transparent;cursor:pointer;
                               color:#6b7280;font-size:1rem;border-left:1px solid #e5e7eb;"
                        title="Hari berikutnya">›</button>
                </div>

                {{-- Judul hari --}}
                <span style="font-size:0.9rem;font-weight:600;color:#374151;">
                    {{ $this->judulHari() }}
                </span>

                @if ($filterTanggal !== now()->toDateString())
                    <button wire:click="$set('filterTanggal', '{{ now()->toDateString() }}')"
                        style="padding:0.35rem 0.75rem;font-size:0.75rem;border-radius:0.375rem;
                               border:1px solid #d1d5db;background:#fff;cursor:pointer;color:#6b7280;">
                        Hari Ini
                    </button>
                @endif
            </div>

            {{-- Tombol hadir semua --}}
            <button wire:click="hadirSemua"
                wire:confirm="Tandai semua guru yang belum diabsen sebagai Hadir?"
                style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;
                       border-radius:0.5rem;border:none;background:#15803d;color:#fff;
                       font-size:0.8rem;font-weight:600;cursor:pointer;">
                <x-heroicon-o-check-circle style="width:1rem;height:1rem;" />
                Hadir Semua
            </button>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             REKAP SINGKAT
        ══════════════════════════════════════════════════════════════ --}}
        @php $rekap = $this->rekapHari; @endphp
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
            @foreach([
                'hadir' => ['label'=>'Hadir',  'bg'=>'#dcfce7','text'=>'#15803d','border'=>'#bbf7d0'],
                'izin'  => ['label'=>'Izin',   'bg'=>'#fefce8','text'=>'#a16207','border'=>'#fde68a'],
                'sakit' => ['label'=>'Sakit',  'bg'=>'#fff7ed','text'=>'#c2410c','border'=>'#fed7aa'],
                'alpha' => ['label'=>'Alpha',  'bg'=>'#fef2f2','text'=>'#dc2626','border'=>'#fecaca'],
                'dinas' => ['label'=>'Dinas',  'bg'=>'#f5f3ff','text'=>'#7c3aed','border'=>'#ddd6fe'],
                'belum' => ['label'=>'Belum',  'bg'=>'#f9fafb','text'=>'#9ca3af','border'=>'#e5e7eb'],
            ] as $key => $cfg)
                <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.45rem 0.875rem;
                            border-radius:0.5rem;border:1px solid {{ $cfg['border'] }};
                            background:{{ $cfg['bg'] }};">
                    <span style="font-size:1rem;font-weight:800;color:{{ $cfg['text'] }};">
                        {{ $rekap[$key] ?? 0 }}
                    </span>
                    <span style="font-size:0.75rem;font-weight:500;color:{{ $cfg['text'] }};">
                        {{ $cfg['label'] }}
                    </span>
                </div>
            @endforeach
            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.45rem 0.875rem;
                        border-radius:0.5rem;border:1px solid #e5e7eb;background:#f9fafb;">
                <span style="font-size:1rem;font-weight:800;color:#374151;">
                    {{ $this->guru->count() }}
                </span>
                <span style="font-size:0.75rem;font-weight:500;color:#6b7280;">Total Guru</span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TABEL ABSEN
        ══════════════════════════════════════════════════════════════ --}}
        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

            <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
                <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                    Daftar Hadir Guru
                </h2>
            </div>

            @if ($this->guru->isEmpty())
                <div style="padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                    <x-heroicon-o-users style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.5;" />
                    <p style="font-size:0.875rem;">Belum ada data guru aktif.</p>
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
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                    Nama Guru
                                </th>
                                <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:10rem;">Jabatan</th>
                                <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                           font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                           width:26rem;">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->guru as $i => $k)
                                @php
                                    $statusSekarang = $this->absenHari[$k->id] ?? null;
                                    $warna = $statusSekarang
                                        ? $this->statusConfig($statusSekarang)
                                        : ['bg' => '#fff', 'text' => '#9ca3af', 'border' => '#e5e7eb'];
                                @endphp
                                <tr style="border-bottom:1px solid #f8fafc;
                                           background:{{ $statusSekarang ? $warna['bg'] : '#fff' }};"
                                    onmouseover="this.style.background='#f9fafb'"
                                    onmouseout="this.style.background='{{ $statusSekarang ? $warna['bg'] : '#fff' }}'">

                                    <td style="padding:0.75rem 1rem;text-align:center;
                                               color:#9ca3af;font-size:0.75rem;">{{ $i + 1 }}</td>

                                    <td style="padding:0.75rem 1rem;">
                                        <div style="font-weight:600;color:#1f2937;">{{ $k->nama }}</div>
                                        @if ($k->mata_pelajaran)
                                            <div style="font-size:0.7rem;color:#9ca3af;margin-top:0.15rem;">
                                                {{ $k->mata_pelajaran }}
                                                @if ($k->kelas_ajar) · Kelas {{ $k->kelas_ajar }} @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td style="padding:0.75rem 1rem;color:#6b7280;font-size:0.75rem;">
                                        {{ $k->jabatan ?? '—' }}
                                    </td>

                                    <td style="padding:0.75rem 1rem;">
                                        <div style="display:flex;gap:0.375rem;justify-content:center;flex-wrap:wrap;">
                                            @foreach ($this->statusList() as $s)
                                                @php
                                                    $cfg   = $this->statusConfig($s);
                                                    $aktif = $statusSekarang === $s;
                                                @endphp
                                                <button
                                                    wire:click="setStatus({{ $k->id }}, '{{ $s }}')"
                                                    style="padding:0.3rem 0.65rem;font-size:0.7rem;font-weight:{{ $aktif ? '700' : '500' }};
                                                           border-radius:0.375rem;cursor:pointer;
                                                           border:1.5px solid {{ $aktif ? $cfg['border'] : '#e5e7eb' }};
                                                           background:{{ $aktif ? $cfg['bg'] : 'transparent' }};
                                                           color:{{ $aktif ? $cfg['text'] : '#9ca3af' }};"
                                                    onmouseover="this.style.background='{{ $cfg['bg'] }}';this.style.color='{{ $cfg['text'] }}';this.style.borderColor='{{ $cfg['border'] }}';"
                                                    onmouseout="this.style.background='{{ $aktif ? $cfg['bg'] : 'transparent' }}';this.style.color='{{ $aktif ? $cfg['text'] : '#9ca3af' }}';this.style.borderColor='{{ $aktif ? $cfg['border'] : '#e5e7eb' }}';">
                                                    {{ $this->statusLabel($s) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
