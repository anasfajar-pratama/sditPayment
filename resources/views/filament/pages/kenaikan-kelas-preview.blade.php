<div style="font-size:0.85rem;color:#374151;">
    <p style="margin:0 0 0.75rem;padding:0.5rem 0.75rem;background:#f0fdf4;border-radius:0.5rem;border:1px solid #bbf7d0;">
        <strong>Tahun Ajaran Sumber:</strong> {{ $taSumber }}
        &nbsp;&middot;&nbsp;
        <strong>Tahun Ajaran Target:</strong> {{ $targetTa }}
    </p>

    @php
        $totalSiswa = collect($kelasData)->sum('jumlah');
        $mapped = collect($kelasData)->filter(fn ($k) => ! empty($k['target']));
        $belum  = collect($kelasData)->filter(fn ($k) => empty($k['target']));
    @endphp

    @if ($belum->isNotEmpty())
        <div style="margin-bottom:0.75rem;padding:0.5rem 0.75rem;background:#fffbeb;border-radius:0.5rem;border:1px solid #fde68a;">
            <strong style="color:#d97706;">⚠ Perhatian:</strong>
            {{ $belum->count() }} kelas belum diisi targetnya.
            Silakan tutup modal dan isi kelas tujuan yang kosong.
        </div>
    @endif

    <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
        <thead>
            <tr style="background:#f3f4f6;border-bottom:2px solid #e5e7eb;">
                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;">Kelas Asal</th>
                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;">Jml Siswa</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;">Kelas Tujuan</th>
                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;">Tipe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kelasData as $item)
                @if (empty($item['target'])) @continue @endif
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:0.4rem 0.75rem;font-weight:600;">{{ $item['kelas'] }}</td>
                    <td style="padding:0.4rem 0.75rem;text-align:center;">{{ $item['jumlah'] }}</td>
                    <td style="padding:0.4rem 0.75rem;">
                        @php
                            $isLulus   = in_array($item['target'], ['Lulus SD', 'Lulus SMP']);
                            $isTinggal = $item['target'] === 'TINGGAL';
                        @endphp
                        @if ($isLulus)
                            <span style="color:#b91c1c;font-weight:600;">{{ $item['target'] }}</span>
                        @elseif ($isTinggal)
                            <span style="color:#d97706;font-weight:600;">Tinggal Kelas</span>
                        @else
                            <span style="color:#1d4ed8;font-weight:600;">{{ $item['target'] }}</span>
                        @endif
                    </td>
                    <td style="padding:0.4rem 0.75rem;text-align:center;">
                        @if ($isLulus)
                            <span style="display:inline-flex;align-items:center;gap:0.2rem;background:#fef2f2;
                                         color:#b91c1c;border-radius:0.25rem;padding:0.15rem 0.4rem;font-size:0.7rem;font-weight:600;">
                                <x-heroicon-o-academic-cap style="width:0.7rem;height:0.7rem;" />
                                Lulus
                            </span>
                        @elseif ($isTinggal)
                            <span style="display:inline-flex;align-items:center;gap:0.2rem;background:#fffbeb;
                                         color:#d97706;border-radius:0.25rem;padding:0.15rem 0.4rem;font-size:0.7rem;font-weight:600;">
                                <x-heroicon-o-arrow-uturn-left style="width:0.7rem;height:0.7rem;" />
                                Tinggal
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:0.2rem;background:#eff6ff;
                                         color:#1d4ed8;border-radius:0.25rem;padding:0.15rem 0.4rem;font-size:0.7rem;font-weight:600;">
                                <x-heroicon-o-arrow-up style="width:0.7rem;height:0.7rem;" />
                                Naik
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                <td style="padding:0.5rem 0.75rem;font-weight:700;">Total</td>
                <td style="padding:0.5rem 0.75rem;text-align:center;font-weight:700;">{{ $mapped->sum('jumlah') }}</td>
                <td style="padding:0.5rem 0.75rem;" colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
