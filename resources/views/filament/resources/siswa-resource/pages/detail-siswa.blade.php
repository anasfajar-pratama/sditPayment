{{--
    File: resources/views/filament/resources/siswa-resource/pages/detail-siswa.blade.php
    Buat folder jika belum ada:
    resources/views/filament/resources/siswa-resource/pages/
--}}

<x-filament-panels::page>

    {{-- ── Info singkat siswa ───────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-4 mb-2 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">

        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 shrink-0">
            <x-heroicon-o-academic-cap class="h-8 w-8" />
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $siswa->nama }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                NIS: {{ $siswa->nis ?? '-' }}
                @if($siswa->kelasSaatIni)
                    &middot; Kelas {{ $siswa->kelasSaatIni->kelas ?? '-' }}
                    &middot; {{ $siswa->kelasSaatIni->jenis_sekolah ?? '-' }}
                    &middot; {{ $siswa->kelasSaatIni->tahun_ajaran ?? '-' }}
                @endif
                @if($siswa->angkatan)
                    &middot; Angkatan {{ $siswa->angkatan }}
                @endif
            </p>
            @if($siswa->nama_orang_tua)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Orang Tua: {{ $siswa->nama_orang_tua }}
                    @if($siswa->no_hp_orang_tua)
                        &middot; {{ $siswa->no_hp_orang_tua }}
                    @endif
                </p>
            @endif
        </div>

        <div class="text-right shrink-0">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Total Pembayaran</p>
            {{-- FIX: gunakan field 'nominal' sesuai tabel pembayaran --}}
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                Rp {{ number_format($this->historyTagihan->sum('nominal'), 0, ',', '.') }}
            </p>
            @php
                $lunas = $this->historyTagihan->where('status', 'lunas')->count();
                $total = $this->historyTagihan->count();
                $belum = $total - $lunas;
            @endphp
            @if($total > 0)
                <p class="text-xs mt-0.5">
                    <span class="text-emerald-600 font-medium">{{ $lunas }} lunas</span>
                    @if($belum > 0)
                        &middot; <span class="text-amber-500 font-medium">{{ $belum }} belum</span>
                    @endif
                </p>
            @endif
        </div>
    </div>

    {{-- ── History Pembayaran ───────────────────────────────────────────────── --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="h-5 w-5 text-blue-500" />
                <span>History Pembayaran</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                    {{ $this->historyTagihan->count() }} transaksi
                </span>
            </div>
        </x-slot>

        @if($this->historyTagihan->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400 dark:text-gray-500">
                <x-heroicon-o-inbox class="h-10 w-10 mb-2" />
                <p class="text-sm">Belum ada data pembayaran</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-2 pr-4 font-semibold text-gray-600 dark:text-gray-300">Jenis Pembayaran</th>
                            <th class="pb-2 pr-4 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Periode</th>
                            <th class="pb-2 pr-4 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Tgl Bayar</th>
                            <th class="pb-2 pr-10 font-semibold text-gray-600 dark:text-gray-300 text-right whitespace-nowrap w-44">Nominal</th>
                            <th class="pb-2 pl-4 text-center font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($this->historyTagihan as $bayar)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                                {{-- Jenis Pembayaran --}}
                                <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-300">
                                    {{ $bayar->jenisPembayaran?->nama ?? '-' }}
                                </td>

                                {{-- Periode — handle bulan berupa angka ("05") atau nama ("Januari") --}}
                                <td class="py-2.5 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    @if($bayar->bulan && $bayar->tahun)
                                        @php
                                            // Jika bulan berupa angka, konversi ke nama bulan
                                            // Jika sudah berupa nama (misal "Januari"), tampilkan langsung
                                            if (is_numeric($bayar->bulan)) {
                                                $periode = \Carbon\Carbon::createFromDate($bayar->tahun, (int) $bayar->bulan, 1)
                                                    ->translatedFormat('M Y');
                                            } else {
                                                $periode = ucfirst($bayar->bulan) . ' ' . $bayar->tahun;
                                            }
                                        @endphp
                                        {{ $periode }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Tanggal Bayar --}}
                                <td class="py-2.5 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $bayar->tanggal_bayar ? \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') : '-' }}
                                </td>

                                {{-- FIX: field 'nominal' bukan 'nominal_tagihan' --}}
                                <td class="py-2.5 pr-10 font-medium text-right text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    Rp {{ number_format($bayar->nominal ?? 0, 0, ',', '.') }}
                                </td>

                                {{-- Status — lunas / cicilan / pending / belum lunas --}}
                                <td class="py-2.5 pl-4 text-center">
                                    @php
                                        $status = strtolower($bayar->status ?? '');
                                    @endphp
                                    @if($status === 'lunas')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            <x-heroicon-o-check-circle class="h-3.5 w-3.5" />
                                            Lunas
                                        </span>
                                    @elseif($status === 'cicilan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            <x-heroicon-o-minus-circle class="h-3.5 w-3.5" />
                                            Cicilan
                                        </span>
                                    @elseif($status === 'sebagian')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            <x-heroicon-o-minus-circle class="h-3.5 w-3.5" />
                                            Sebagian
                                        </span>
                                    @elseif($status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            <x-heroicon-o-clock class="h-3.5 w-3.5" />
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                            <x-heroicon-o-clock class="h-3.5 w-3.5" />
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                            <td class="pt-2.5 pr-4 font-semibold text-gray-700 dark:text-gray-200" colspan="3">Total</td>
                            {{-- FIX: sum('nominal') sesuai field di tabel pembayaran --}}
                            <td class="pt-2.5 pr-10 font-bold text-right text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                Rp {{ number_format($this->historyTagihan->sum('nominal'), 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>
