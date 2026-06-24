<x-filament-panels::page>

    <div class="flex flex-wrap items-center gap-4 mb-2 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900 text-rose-500 dark:text-rose-300 shrink-0">
            <x-heroicon-o-user-circle class="h-8 w-8" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $donatur->nama }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $donatur->no_hp ?? '-' }}
                @if($donatur->email) · {{ $donatur->email }} @endif
            </p>
        </div>
        <div class="text-right shrink-0">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Donasi</p>
            <p class="text-xl font-bold text-rose-600">Rp {{ number_format($donatur->donasis->sum('nominal'), 0, ',', '.') }}</p>
        </div>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-banknotes class="h-5 w-5 text-rose-500" />
                <span>Input Donasi</span>
            </div>
        </x-slot>
        <form wire:submit="simpanDonasi">
            {{ $this->donasiForm }}
            <br>
            <div class="mt-8 flex justify-end">
                <x-filament::button type="submit" color="success" icon="heroicon-o-check" wire:loading.attr="disabled" wire:target="simpanDonasi">
                    <span wire:loading.remove wire:target="simpanDonasi">Simpan Donasi</span>
                    <span wire:loading wire:target="simpanDonasi">Menyimpan...</span>
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="h-5 w-5 text-blue-500" />
                <span>History Donasi</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                    {{ $this->historyDonasi->count() }} transaksi
                </span>
            </div>
        </x-slot>

        @if($this->historyDonasi->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400">
                <x-heroicon-o-inbox class="h-10 w-10 mb-2" />
                <p class="text-sm">Belum ada riwayat donasi</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-2 pr-4 font-semibold text-gray-600 dark:text-gray-300 w-36">Tgl Donasi</th>
                            <th class="pb-2 pr-10 font-semibold text-gray-600 dark:text-gray-300 text-right w-44">Nominal</th>
                            <th class="pb-2 pl-4 font-semibold text-gray-600 dark:text-gray-300 text-right">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($this->historyDonasi as $donasi)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $donasi->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="py-2.5 pr-10 font-medium text-right text-emerald-600 whitespace-nowrap">Rp {{ number_format($donasi->nominal, 0, ',', '.') }}</td>
                                <td class="py-2.5 pl-4 text-gray-500 text-right">{{ $donasi->note ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300">
                            <td class="pt-2.5 pr-4 font-semibold text-gray-700">Total</td>
                            <td class="pt-2.5 pr-10 font-bold text-right text-emerald-700 whitespace-nowrap">Rp {{ number_format($this->historyDonasi->sum('nominal'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>