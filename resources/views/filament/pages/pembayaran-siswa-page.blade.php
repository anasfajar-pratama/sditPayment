<x-filament-panels::page>
    <div class="space-y-6">

        {{-- INQUIRY: Pencarian Siswa --}}
        <x-filament::section heading="Inquiry Siswa">
            <div class="max-w-lg">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cari Siswa (NIS atau Nama)
                </label>
                <div class="relative">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            wire:focus="$set('showResults', true)"
                            placeholder="Ketik NIS atau nama siswa..."
                            autocomplete="off"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-800 px-3 py-2 text-sm shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary-500"
                        />
                        @if ($siswa_id)
                            <button
                                wire:click="clearSiswa"
                                type="button"
                                class="rounded-lg border border-gray-300 dark:border-gray-600
                                       px-3 py-2 text-sm text-gray-500 hover:bg-gray-100
                                       dark:hover:bg-gray-700 transition"
                                title="Ganti siswa"
                            >
                                ✕
                            </button>
                        @endif
                    </div>

                    {{-- Dropdown Hasil Pencarian --}}
                    @if ($showResults && strlen(trim($searchQuery)) >= 2)
                        <div
                            class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200
                                   dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden"
                        >
                            @forelse ($this->searchResults as $siswa)
                                <button
                                    type="button"
                                    wire:click="selectSiswa({{ $siswa->id }})"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-primary-50
                                           dark:hover:bg-primary-900/30 transition flex items-center gap-3
                                           border-b border-gray-100 dark:border-gray-700 last:border-0"
                                >
                                    <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700
                                                 rounded px-1.5 py-0.5 text-gray-600 dark:text-gray-300">
                                        {{ $siswa->nis }}
                                    </span>
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $siswa->nama }}
                                    </span>
                                    <span class="ml-auto text-xs text-gray-400">
                                        Kelas {{ $siswa->kelas }}
                                    </span>
                                </button>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-400 text-center">
                                    Siswa tidak ditemukan.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-400">Ketik minimal 2 karakter untuk melihat rekomendasi.</p>
            </div>
        </x-filament::section>

        @if ($selectedSiswa)

            {{-- INFO SISWA --}}
            <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20
                        border border-primary-200 dark:border-primary-700
                        px-5 py-3 flex flex-wrap gap-6 text-sm">
                <div>
                    <span class="font-semibold text-primary-700 dark:text-primary-300">NIS:</span>
                    {{ $selectedSiswa->nis }}
                </div>
                <div>
                    <span class="font-semibold text-primary-700 dark:text-primary-300">Nama:</span>
                    {{ $selectedSiswa->nama }}
                </div>
                <div>
                    <span class="font-semibold text-primary-700 dark:text-primary-300">Kelas:</span>
                    {{ $selectedSiswa->kelas }}
                </div>
            </div>

            {{-- TAGIHAN BELUM BAYAR --}}
            <x-filament::section heading="Tagihan Belum Bayar">
                @if ($this->tagihans->isEmpty())
                    <div class="py-8 text-center text-gray-400">
                        <x-heroicon-o-check-circle class="mx-auto h-10 w-10 mb-2 text-success-400" />
                        Tidak ada tagihan yang belum dibayar.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700
                                           text-xs font-semibold uppercase text-gray-500">
                                    <th class="pb-3 pr-4 text-left">Jenis Pembayaran</th>
                                    <th class="pb-3 pr-4 text-left">Bulan</th>
                                    <th class="pb-3 pr-4 text-left">Tahun</th>
                                    <th class="pb-3 pr-4 text-right">Nominal Tagihan</th>
                                    <th class="pb-3 pr-4 text-center">Nominal Bayar</th>
                                    <th class="pb-3 pr-4 text-center">%</th>
                                    <th class="pb-3 text-center">Tanggal Bayar</th>
                                    <th class="pb-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($this->tagihans as $tagihan)
                                    @php $isSpp = $this->isSpp($tagihan); @endphp
                                    <tr>
                                        <td class="py-3 pr-4 font-medium">
                                            {{ $tagihan->jenisPembayaran->nama }}
                                            @if ($isSpp)
                                                <span class="ml-1 rounded-full bg-danger-100 text-danger-700
                                                             text-xs px-2 py-0.5">
                                                    Wajib Lunas
                                                </span>
                                            @else
                                                <span class="ml-1 rounded-full bg-blue-100 text-blue-700
                                                             text-xs px-2 py-0.5">
                                                    Bisa Cicil
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            {{ $tagihan->bulan ? $this->getBulanLabel($tagihan->bulan) : '-' }}
                                        </td>
                                        <td class="py-3 pr-4">{{ $tagihan->tahun }}</td>
                                        <td class="py-3 pr-4 text-right font-semibold whitespace-nowrap">
                                            Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                                        </td>

                                        {{-- Nominal Bayar — center --}}
                                        <td class="py-3 pr-4 text-center">
                                            <div class="inline-flex items-center gap-1 justify-center">
                                                <span class="text-gray-400 text-xs">Rp</span>
                                                <input
                                                    type="number"
                                                    wire:model.live.debounce.400ms="nominals.{{ $tagihan->id }}"
                                                    @if ($isSpp) readonly @endif
                                                    min="0"
                                                    max="{{ $tagihan->nominal_tagihan }}"
                                                    class="w-32 rounded-lg border px-2 py-1.5 text-sm text-center
                                                        {{ $isSpp
                                                            ? 'bg-gray-100 dark:bg-gray-700 border-gray-200
                                                               dark:border-gray-600 cursor-not-allowed'
                                                            : 'border-gray-300 dark:border-gray-600
                                                               bg-white dark:bg-gray-800
                                                               focus:ring-2 focus:ring-primary-500 focus:outline-none' }}"
                                                />
                                            </div>
                                        </td>

                                        {{-- Persentase — center --}}
                                        <td class="py-3 pr-4 text-center">
                                            @php $pct = $percentages[$tagihan->id] ?? 0; @endphp
                                            <span class="font-semibold tabular-nums
                                                {{ $pct >= 100 ? 'text-success-600'
                                                    : ($pct > 0 ? 'text-warning-600'
                                                    : 'text-gray-400') }}">
                                                {{ $pct }}%
                                            </span>
                                        </td>

                                        {{-- Tanggal Bayar — center --}}
                                        <td class="py-3 pr-4 text-center text-gray-500">
                                            {{ now()->format('d M Y') }}
                                        </td>

                                        {{-- Tombol Bayar — center --}}
                                        <td class="py-3 text-center">
                                            <x-filament::button
                                                wire:click="bayar({{ $tagihan->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="bayar({{ $tagihan->id }})"
                                                size="sm"
                                                color="primary"
                                            >
                                                <span wire:loading.remove
                                                      wire:target="bayar({{ $tagihan->id }})">
                                                    Bayar
                                                </span>
                                                <span wire:loading
                                                      wire:target="bayar({{ $tagihan->id }})">
                                                    Proses...
                                                </span>
                                            </x-filament::button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>

            {{-- HISTORY PEMBAYARAN (semua status) --}}
            @if ($this->history->isNotEmpty())
                <x-filament::section heading="History Pembayaran">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700
                                        text-xs font-semibold uppercase text-gray-500">
                                    <th class="pb-3 pr-4 text-left">Jenis</th>
                                    <th class="pb-3 pr-4 text-left">Bulan</th>
                                    <th class="pb-3 pr-4 text-left">Tahun</th>
                                    <th class="pb-3 pr-4 text-right">Nominal</th>
                                    <th class="pb-3 pr-4 text-center">Tanggal Bayar</th>
                                    <th class="pb-3 pr-4 text-center">Status</th>
                                    <th class="pb-3 text-center">Kuitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($this->history as $bayar)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                        <td class="py-2.5 pr-4 font-medium">
                                            {{ $bayar->jenisPembayaran->nama }}
                                        </td>
                                        <td class="py-2.5 pr-4">
                                            {{ $bayar->bulan ? $this->getBulanLabel($bayar->bulan) : '-' }}
                                        </td>
                                        <td class="py-2.5 pr-4">{{ $bayar->tahun }}</td>
                                        <td class="py-2.5 pr-4 text-right font-medium whitespace-nowrap">
                                            Rp {{ number_format($bayar->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 pr-4 text-center text-gray-500">
                                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                        </td>
                                        <td class="py-2.5 pr-4 text-center">
                                            @if ($bayar->status === 'lunas')
                                                <span class="rounded-full bg-success-100 text-success-700
                                                            text-xs px-2 py-0.5 font-semibold">
                                                    Lunas
                                                </span>
                                            @elseif ($bayar->status === 'cicilan')
                                                <span class="rounded-full bg-warning-100 text-warning-700
                                                            text-xs px-2 py-0.5 font-semibold">
                                                    Cicilan
                                                </span>
                                            @else
                                                <span class="rounded-full bg-gray-100 text-gray-600
                                                            text-xs px-2 py-0.5 font-semibold">
                                                    {{ $bayar->status }}
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Tombol Cetak Kuitansi --}}
                                        <td class="py-2.5 text-center">
                                            <a
                                                href="{{ route('kuitansi.pdf', $bayar) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5
                                                    text-xs font-medium transition
                                                    {{ $bayar->status === 'lunas'
                                                        ? 'bg-success-50 text-success-700 hover:bg-success-100 border border-success-200'
                                                        : 'bg-warning-50 text-warning-700 hover:bg-warning-100 border border-warning-200' }}"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                    </path>
                                                </svg>
                                                @if ($bayar->status === 'lunas')
                                                    Kuitansi
                                                @else
                                                    Bukti Cicilan
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif

        @endif

    </div>
</x-filament-panels::page>
