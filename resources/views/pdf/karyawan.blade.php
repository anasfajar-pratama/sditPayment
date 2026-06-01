<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Karyawan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        /* ── Print button bar ─────────────────── */
        .print-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            background: #1f2937; padding: 0.6rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .print-bar span { color: #d1d5db; font-size: 12px; }
        .btn-group { display: flex; gap: 0.5rem; }
        .btn {
            padding: 0.4rem 1rem; border-radius: 0.375rem; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
        }
        .btn-cetak  { background: #2563eb; color: #fff; }
        .btn-tutup  { background: #6b7280; color: #fff; }

        /* ── Konten utama ─────────────────────── */
        .page { margin-top: 3rem; padding: 1.5rem 2rem 2rem; }

        /* ── Header ──────────────────────────── */
        .header { text-align: center; margin-bottom: 1.25rem; }
        .header h1 { font-size: 16px; font-weight: 700; text-transform: uppercase; }
        .header p  { font-size: 11px; color: #6b7280; margin-top: 0.25rem; }
        .divider   { border: none; border-top: 2px solid #1f2937; margin: 0.5rem 0 1rem; }

        /* ── Rekap chip ───────────────────────── */
        .rekap-row {
            display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;
        }
        .chip {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.2rem 0.6rem; border-radius: 1rem; font-size: 10px; font-weight: 700;
        }
        .chip-guru     { background: #dcfce7; color: #15803d; }
        .chip-admin    { background: #fef9c3; color: #a16207; }
        .chip-operator { background: #eff6ff; color: #1d4ed8; }
        .chip-penjaga  { background: #f3f4f6; color: #374151; }
        .chip-kantin   { background: #fef2f2; color: #dc2626; }
        .chip-total    { background: #1f2937; color: #fff; }

        /* ── Tabel ────────────────────────────── */
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        thead tr { background: #1f2937; color: #fff; }
        thead th {
            padding: 0.5rem 0.6rem; text-align: left;
            font-weight: 600; font-size: 9.5px; letter-spacing: 0.04em; text-transform: uppercase;
        }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 0.5rem 0.6rem; vertical-align: top; }
        tbody td.center { text-align: center; }

        .badge {
            display: inline-block; padding: 0.15rem 0.5rem;
            border-radius: 0.75rem; font-size: 9px; font-weight: 700;
        }
        .badge-aktif    { background: #dcfce7; color: #15803d; }
        .badge-cuti     { background: #fef9c3; color: #a16207; }
        .badge-nonaktif { background: #fef2f2; color: #dc2626; }

        .td-nama   { font-weight: 600; color: #111827; }
        .td-sub    { font-size: 9.5px; color: #9ca3af; margin-top: 2px; }
        .td-group  { font-size: 9px; color: #6b7280; }

        /* ── Footer ──────────────────────────── */
        .footer {
            margin-top: 1.5rem; display: flex; justify-content: space-between;
            font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 0.5rem;
        }

        /* ── Print override ───────────────────── */
        @media print {
            .print-bar { display: none !important; }
            .page { margin-top: 0; padding: 1rem 1.5rem; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

{{-- ══ Tombol print ════════════════════════════════════════════════════════ --}}
<div class="print-bar">
    <span>Data Karyawan — SDIT</span>
    <div class="btn-group">
        <button class="btn btn-cetak" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <button class="btn btn-tutup" onclick="window.close()">✕ Tutup</button>
    </div>
</div>

<div class="page">

    {{-- ══ Kop surat ══════════════════════════════════════════════════════ --}}
    <div class="header">
        <h1>Data Karyawan</h1>
        <p>
            @if ($filterLabel) {{ $filterLabel }} &nbsp;·&nbsp; @endif
            Dicetak: {{ now()->format('d F Y, H:i') }} &nbsp;·&nbsp; Total: {{ $karyawans->count() }} orang
        </p>
    </div>
    <hr class="divider">

    {{-- ══ Rekap per job ══════════════════════════════════════════════════ --}}
    <div class="rekap-row">
        <div class="chip chip-total">Total: {{ $karyawans->count() }}</div>
        @foreach ([
            'guru'     => ['label' => 'Guru',     'class' => 'chip-guru'],
            'admin'    => ['label' => 'Admin',    'class' => 'chip-admin'],
            'operator' => ['label' => 'Operator', 'class' => 'chip-operator'],
            'penjaga'  => ['label' => 'Penjaga',  'class' => 'chip-penjaga'],
            'kantin'   => ['label' => 'Kantin',   'class' => 'chip-kantin'],
        ] as $key => $cfg)
            @if (!empty($rekapJob[$key]))
                <div class="chip {{ $cfg['class'] }}">{{ $cfg['label'] }}: {{ $rekapJob[$key] }}</div>
            @endif
        @endforeach
    </div>

    {{-- ══ Tabel ══════════════════════════════════════════════════════════ --}}
    @if ($karyawans->isEmpty())
        <p style="text-align:center;padding:2rem;color:#9ca3af;">Tidak ada data karyawan.</p>
    @else
        @php
            $currentJob = null;
            $jobLabels  = [
                'guru'     => 'GURU',
                'admin'    => 'ADMIN',
                'operator' => 'OPERATOR',
                'penjaga'  => 'PENJAGA SEKOLAH',
                'kantin'   => 'KANTIN',
            ];
            $no = 1;
        @endphp

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:2rem;">No</th>
                    <th style="width:20%;">Nama</th>
                    <th style="width:9%;">NIK</th>
                    <th style="width:11%;">Jabatan</th>
                    <th style="width:10%;">Status Kepeg.</th>
                    <th style="width:8%;">JK</th>
                    <th style="width:12%;">No. HP</th>
                    <th style="width:9%;">Gaji Pokok</th>
                    <th class="center" style="width:7%;">Status</th>
                    <th style="width:12%;">Tgl Masuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($karyawans as $k)
                    {{-- Pemisah grup per job --}}
                    @if ($k->job !== $currentJob)
                        @php $currentJob = $k->job; @endphp
                        <tr>
                            <td colspan="10"
                                style="background:#374151;color:#fff;font-weight:700;font-size:9px;
                                       padding:0.35rem 0.6rem;letter-spacing:0.1em;">
                                {{ $jobLabels[$k->job] ?? strtoupper($k->job) }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="center" style="color:#9ca3af;">{{ $no++ }}</td>

                        <td>
                            <div class="td-nama">{{ $k->nama }}</div>
                            @if ($k->mata_pelajaran)
                                <div class="td-sub">{{ $k->mata_pelajaran }}
                                    @if ($k->kelas_ajar) · Kls {{ $k->kelas_ajar }} @endif
                                </div>
                            @endif
                        </td>

                        <td style="color:#6b7280;">{{ $k->nik ?? '—' }}</td>

                        <td>{{ $k->jabatan ?? '—' }}</td>

                        <td>{{ $k->status_kepegawaian ?? '—' }}</td>

                        <td class="center">{{ $k->jenis_kelamin ?? '—' }}</td>

                        <td>{{ $k->no_hp ?? '—' }}</td>

                        <td style="font-weight:600;">
                            {{ $k->gaji_pokok ? 'Rp ' . number_format($k->gaji_pokok, 0, ',', '.') : '—' }}
                        </td>

                        <td class="center">
                            @php
                                $badgeClass = match($k->status) {
                                    'aktif'                  => 'badge-aktif',
                                    'cuti'                   => 'badge-cuti',
                                    'tidak_aktif', 'resign'  => 'badge-nonaktif',
                                    default                  => '',
                                };
                                $badgeLabel = [
                                    'aktif'       => 'Aktif',
                                    'cuti'        => 'Cuti',
                                    'tidak_aktif' => 'Non-aktif',
                                    'resign'      => 'Resign',
                                ][$k->status] ?? $k->status;
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        </td>

                        <td>{{ $k->tanggal_masuk?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ══ Footer ══════════════════════════════════════════════════════════ --}}
    <div class="footer">
        <span>SDIT · Data Karyawan</span>
        <span>Dicetak oleh: {{ auth()->user()?->name ?? 'System' }} · {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</div>
</body>
</html>
