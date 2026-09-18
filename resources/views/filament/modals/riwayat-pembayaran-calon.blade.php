@php
    $status = 'belum_bayar';
    if ($tagihan && $tagihan->status === 'lunas') {
        $status = 'lunas';
    } elseif ($pembayarans->isNotEmpty()) {
        $status = 'cicilan';
    }

    $statusColor = match ($status) {
        'lunas' => 'success',
        'cicilan' => 'warning',
        default => 'danger',
    };

    $statusLabel = match ($status) {
        'lunas' => 'Lunas',
        'cicilan' => 'Cicilan (Sebagian)',
        default => 'Belum Bayar',
    };
@endphp

<div class="space-y-4">

    <div class="rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 border-b border-gray-200 dark:border-gray-700">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-950 dark:text-white">{{ $siswa->nama }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Calon {{ $jenjangLabel }}
                    @if ($tingkatLabel !== '-')
                        &middot; {{ $tingkatLabel }}
                    @endif
                    @if ($siswa->nama_orang_tua)
                        &middot; {{ $siswa->nama_orang_tua }}
                    @endif
                </div>
            </div>
            <x-filament::badge :color="$statusColor">{{ $statusLabel }}</x-filament::badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3">
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 sm:border-b-0">
                <div class="text-xs text-gray-500 dark:text-gray-400">Total Tagihan</div>
                <div class="text-sm font-semibold text-gray-950 dark:text-white">
                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </div>
            </div>
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 sm:border-b-0">
                <div class="text-xs text-gray-500 dark:text-gray-400">Total Terbayar</div>
                <div class="text-sm font-semibold text-gray-950 dark:text-white">
                    Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
                </div>
            </div>
            <div class="px-3 py-2">
                <div class="text-xs text-gray-500 dark:text-gray-400">Sisa Tagihan</div>
                <div class="text-sm font-semibold text-gray-950 dark:text-white">
                    Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    @if ($pembayarans->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400 dark:text-gray-500">
            <x-heroicon-o-inbox class="h-10 w-10 mb-2" />
            <p class="text-sm">Belum ada pembayaran biaya masuk.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left k-grid-table">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2 pr-3 font-semibold text-gray-600 dark:text-gray-300">No</th>
                        <th class="pb-2 pr-3 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Tanggal Bayar</th>
                        <th class="pb-2 pr-3 font-semibold text-gray-600 dark:text-gray-300 text-right whitespace-nowrap">Nominal</th>
                        <th class="pb-2 pr-3 font-semibold text-gray-600 dark:text-gray-300 text-right whitespace-nowrap">Potongan</th>
                        <th class="pb-2 pr-3 text-center font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="pb-2 text-center font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach ($pembayarans as $bayar)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-2.5 pr-3 text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="py-2.5 pr-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($bayar->tgl_bayar_struk ?? $bayar->tanggal_bayar)->format('d M Y') }}
                            </td>
                            <td class="py-2.5 pr-3 text-right font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                Rp {{ number_format($bayar->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 pr-3 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                @if (($bayar->potongan ?? 0) > 0)
                                    &minus;Rp {{ number_format($bayar->potongan, 0, ',', '.') }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="py-2.5 pr-3 text-center">
                                @if ($bayar->status === 'lunas')
                                    <x-filament::badge color="success" size="sm">Lunas</x-filament::badge>
                                @elseif ($bayar->status === 'cicilan')
                                    <x-filament::badge color="warning" size="sm">Cicilan</x-filament::badge>
                                @else
                                    <x-filament::badge color="gray" size="sm">{{ $bayar->status }}</x-filament::badge>
                                @endif
                            </td>
                            <td class="py-2.5 text-center">
                                <x-filament::button
                                    tag="a"
                                    size="xs"
                                    color="success"
                                    icon="heroicon-m-printer"
                                    :href="route('kuitansi.cetak', $bayar)"
                                    target="_blank"
                                >
                                    Cetak Bukti
                                </x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-300 dark:border-gray-600">
                        <td class="pt-2.5 pr-3 font-semibold text-gray-700 dark:text-gray-200" colspan="2">Total Terbayar</td>
                        <td class="pt-2.5 pr-3 text-right font-bold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

</div>
