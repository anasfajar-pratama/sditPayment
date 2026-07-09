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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
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
                    <label class="block text-xs font-medium text-gray-500 mb-1">No. Referensi</label>
                    <input type="text" wire:model.live.debounce.500ms="searchNoRef" placeholder="Cari no ref..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pengirim</label>
                    <input type="text" wire:model.live.debounce.500ms="searchPengirim" placeholder="Cari pengirim..."
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
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterVerified"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Semua</option>
                        <option value="pending">Belum Verifikasi</option>
                        <option value="verified">Sudah Verifikasi</option>
                    </select>
                </div>
            </div>
        </x-filament::section>
        
        {{-- ═══ TABEL TRANSAKSI PENDING (BLM VERIFIKASI) ═════════════════════ --}}
        @php $pending = $this->transaksiPending; @endphp
        <x-filament::section heading="Daftar Transaksi (Belum Verifikasi)">
            @if ($pending->isEmpty())
                <div class="py-6 text-center text-gray-400">Semua transaksi sudah terverifikasi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-3 text-center w-10">✓</th>
                                <th class="pb-3 pr-3 text-left">Tanggal</th>
                                <th class="pb-3 pr-3 text-left">No. Ref</th>
                                <th class="pb-3 pr-3 text-left">Rek. Tujuan</th>
                                <th class="pb-3 pr-3 text-left">Pengirim</th>
                                <th class="pb-3 pr-3 text-left">Uraian</th>
                                <th class="pb-3 pr-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pending as $item)
                                <tr class="hover:bg-gray-50 transition cursor-pointer"
                                    wire:click="toggleVerifikasi({{ $item->id }})">
                                    <td class="py-2.5 pr-3 text-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary-600"
                                            wire:click.stop="toggleVerifikasi({{ $item->id }})">
                                    </td>
                                    <td class="py-2.5 pr-3 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-3 font-mono text-xs">{{ $item->no_ref ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-xs">{{ \App\Models\MasterRekeningTujuan::where('label', $item->rekening_tujuan)->value('bank') ?: ($item->rekening_tujuan ?: '—') }}</td>
                                    <td class="py-2.5 pr-3">{{ $item->nama_rekening_pengirim ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 max-w-xs truncate" title="{{ $item->uraian }}">{{ $item->uraian }}</td>
                                    <td class="py-2.5 pr-3 text-right font-semibold whitespace-nowrap">
                                        Rp {{ number_format($item->debit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            @endif
        </x-filament::section>

    </div>
</x-filament-panels::page>