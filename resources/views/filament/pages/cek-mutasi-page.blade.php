<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ═══ SUMMARY CARD PER REKENING (1 ROW 3 KOLOM) ═══════════════════ --}}
        @php
            $allCards = $this->summaryPerRekening;
            $nonCash  = array_filter($allCards, fn($k) => $k !== 'Cash', ARRAY_FILTER_USE_KEY);
            $cashCard = $allCards['Cash'] ?? null;
            $cards    = [];
            foreach ($nonCash as $label => $sum) {
                $cards[] = ['label' => $label, 'total_masuk' => $sum['total_masuk'], 'total_terverifikasi' => $sum['total_terverifikasi']];
            }
            if ($cashCard) {
                $cards[] = ['label' => 'Cash', 'total_masuk' => $cashCard['total_masuk'], 'total_terverifikasi' => $cashCard['total_terverifikasi']];
            }
            $colors = [
                ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1e40af', 'accent' => '#3b82f6'],
                ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'accent' => '#22c55e'],
                ['bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b', 'accent' => '#ef4444'],
                ['bg' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '#4c1d95', 'accent' => '#8b5cf6'],
                ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'accent' => '#f97316'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($cards as $i => $card)
                @php
                    $c = $colors[$i % count($colors)];
                    $selisih = $card['total_masuk'] - $card['total_terverifikasi'];
                @endphp
                <div class="rounded-xl p-4 shadow-sm" style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }};">
                    <div class="text-sm font-bold mb-2" style="color:{{ $c['text'] }}">{{ $card['label'] }}</div>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span style="color:{{ $c['text'] }};opacity:0.7;">Total Masuk</span>
                            <span class="font-semibold" style="color:{{ $c['text'] }}">Rp {{ number_format($card['total_masuk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color:{{ $c['text'] }};opacity:0.7;">Terverifikasi</span>
                            <span class="font-semibold" style="color:{{ $c['accent'] }}">Rp {{ number_format($card['total_terverifikasi'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-1" style="border-top:1px solid {{ $c['border'] }}">
                            <span style="color:{{ $c['text'] }};opacity:0.7;">Selisih</span>
                            <span class="font-semibold" style="color:{{ $selisih > 0 ? '#dc2626' : $c['text'] }}">
                                Rp {{ number_format($selisih, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ═══ FILTER ═══════════════════════════════════════════════════════ --}}
        <x-filament::section heading="Filter Pencarian">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Dari</label>
                    <input type="date" wire:model.live.debounce.500ms="tanggalDari"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Sampai</label>
                    <input type="date" wire:model.live.debounce.500ms="tanggalSampai"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari (No. Ref / Pengirim / Uraian)</label>
                    <input type="text" wire:model.live.debounce.500ms="searchGlobal" placeholder="Ketik kata kunci..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rekening Tujuan</label>
                    <select wire:model.live="filterRekening"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Semua Rekening</option>
                        @foreach ($rekeningFilters as $label => $v)
                            <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button wire:click="resetFilter"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition">
                    &#x21bb; Reset Pencarian
                </button>
            </div>
        </x-filament::section>
        
        {{-- ═══ TABEL TRANSAKSI PENDING (BLM VERIFIKASI) ═════════════════════ --}}
        @php $pending = $this->transaksiPending; @endphp
        <x-filament::section>
            <x-slot:heading>
                <div class="flex items-center justify-between w-full">
                    <span>Daftar Transaksi (Belum Verifikasi) <span class="text-xs text-gray-400">— {{ count($pending) }} transaksi</span></span>
                    <button wire:click="openVerifModal"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm text-white hover:bg-primary-700 transition inline-flex items-center gap-2"
                        wire:loading.attr="disabled">
                        <span>Verifikasi Terpilih</span>
                    </button>
                </div>
            </x-slot:heading>
            @if ($pending->isEmpty())
                <div class="py-6 text-center text-gray-400">Semua transaksi sudah terverifikasi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-3 text-center w-10">
                                    <input type="checkbox" class="rounded border-gray-300 text-primary-600"
                                        wire:click="$toggle('selectAll')"
                                        wire:key="select-all">
                                </th>
                                <th class="pb-3 pr-3 text-left">Tanggal</th>
                                <th class="pb-3 pr-3 text-left">No. Ref</th>
                                <th class="pb-3 pr-3 text-left">Rek. Tujuan</th>
                                <th class="pb-3 pr-3 text-left">Pengirim</th>
                                <th class="pb-3 pr-3 text-left">Uraian</th>
                                <th class="pb-3 pr-3 text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pending as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-2.5 pr-3 text-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary-600"
                                            value="{{ $item->id }}"
                                            wire:model.live="selectedIds">
                                    </td>
                                    <td class="py-2.5 pr-3 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-3 font-mono text-xs">{{ $item->no_ref ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-xs">{{ \App\Models\MasterRekeningTujuan::where('label', $item->rekening_tujuan)->value('bank') ?: ($item->rekening_tujuan ?: '—') }}</td>
                                    <td class="py-2.5 pr-3">{{ $item->nama_rekening_pengirim ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 max-w-xs truncate" title="{{ $item->uraian }} — Rp {{ number_format($item->debit, 0, ',', '.') }}">
                                        {{ $item->uraian }} — Rp {{ number_format($item->debit, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 pr-3 text-center">
                                        @if ($item->source_bukti_url)
                                            <button type="button"
                                                x-data
                                                x-on:click="
                                                    $nextTick(() => {
                                                        $dispatch('open-bukti', { url: '{{ $item->source_bukti_url }}' });
                                                    });
                                                "
                                                class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 transition cursor-pointer bg-transparent border-none">
                                                <x-heroicon-o-photo class="w-4 h-4" />
                                                Lihat
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $pending->links() }}
                </div>
            @endif
        </x-filament::section>

        {{-- ═══ TABEL TRANSAKSI TERVERIFIKASI ════════════════════════════════ --}}
        @php $verified = $this->transaksiTerverifikasi; @endphp
        <x-filament::section heading="Transaksi Terverifikasi">
            @if ($verified->isEmpty())
                <div class="py-6 text-center text-gray-400">Belum ada transaksi yang diverifikasi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-3 text-left">Tgl Verifikasi</th>
                                <th class="pb-3 pr-3 text-left">Tanggal Bayar</th>
                                <th class="pb-3 pr-3 text-left">No. Ref</th>
                                <th class="pb-3 pr-3 text-left">Rek. Tujuan</th>
                                <th class="pb-3 pr-3 text-left">Pengirim</th>
                                <th class="pb-3 pr-3 text-center">Bukti</th>
                                <th class="pb-3 pr-3 text-right">Nominal</th>
                                <th class="pb-3 text-center">Verifikator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($verified as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-2.5 pr-3 whitespace-nowrap text-xs">
                                        {{ \Carbon\Carbon::parse($item->verified_at)->format('d M Y H:i') }}
                                    </td>
                                    <td class="py-2.5 pr-3 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-3 font-mono text-xs">{{ $item->no_ref ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-xs">{{ \App\Models\MasterRekeningTujuan::where('label', $item->rekening_tujuan)->value('bank') ?? ($item->rekening_tujuan ?: '—') }}</td>
                                    <td class="py-2.5 pr-3">{{ $item->nama_rekening_pengirim ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-center">
                                        @if ($item->source_bukti_url)
                                            <button type="button"
                                                x-data
                                                x-on:click="
                                                    $nextTick(() => {
                                                        $dispatch('open-bukti', { url: '{{ $item->source_bukti_url }}' });
                                                    });
                                                "
                                                class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 transition cursor-pointer bg-transparent border-none">
                                                <x-heroicon-o-photo class="w-4 h-4" />
                                                Lihat
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 pr-3 text-right font-semibold whitespace-nowrap">
                                        Rp {{ number_format($item->debit, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 text-center text-xs text-gray-500">
                                        {{ $item->verifiedBy?->name ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $verified->links() }}
                </div>
            @endif
        </x-filament::section>

    </div>

{{-- Popup preview bukti transaksi --}}
<div
    x-data="{ open: false, url: '' }"
    x-on:open-bukti.window="url = $event.detail.url; open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 p-4"
    x-on:click.self="open = false"
>
    <div class="relative w-full max-w-2xl max-h-[80vh] bg-white rounded-xl shadow-2xl overflow-auto">
        <button type="button"
            x-on:click="open = false"
            class="sticky top-2 z-10 ml-auto mr-2 block w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
        <div class="p-4 pt-0 flex items-start justify-center">
            <img :src="url" alt="Bukti Transaksi" class="max-w-full h-auto rounded">
        </div>
    </div>
</div>

{{-- Modal verifikasi massal --}}
@if ($showVerifModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    wire:click.self="closeVerifModal">
    <div class="w-full max-w-md rounded-xl shadow-2xl p-6 space-y-4"
        style="background:#fff7ed;border:2px solid #f97316;">
        <h3 class="text-lg font-bold text-gray-800">Verifikasi Transaksi</h3>
        <p class="text-sm text-gray-600">
            {{ count($selectedIds) }} transaksi akan diverifikasi. Masukkan password untuk mengonfirmasi.
        </p>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Password</label>
            <input type="password" wire:model="verifPassword"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                wire:keydown.enter="submitVerifikasiMassal">
            @if ($verifError)
                <p class="mt-1 text-xs" style="color:#dc2626;">{{ $verifError }}</p>
            @endif
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button wire:click="closeVerifModal"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition">
                Batal
            </button>
            <button wire:click="submitVerifikasiMassal"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm text-white hover:bg-primary-700 transition inline-flex items-center gap-2"
                wire:loading.attr="disabled"
                wire:target="submitVerifikasiMassal">
                <span wire:loading.remove wire:target="submitVerifikasiMassal">Verifikasi</span>
                <span wire:loading wire:target="submitVerifikasiMassal">&#x21bb; Memproses...</span>
            </button>
        </div>
    </div>
</div>
@endif
</x-filament-panels::page>