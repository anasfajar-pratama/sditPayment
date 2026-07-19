<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ═══ TAB NAVIGATION ═══════════════════════════════════════════════ --}}
        <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #e5e7eb;
                    overflow:hidden;background:#f9fafb;width:fit-content;">
            <button wire:click="$set('tab','transaksi')"
                style="padding:0.5rem 1.25rem;font-size:0.85rem;border:none;cursor:pointer;white-space:nowrap;
                       font-weight:{{ $tab==='transaksi'?'700':'500' }};
                       background:{{ $tab==='transaksi'?'#1f2937':'transparent' }};
                       color:{{ $tab==='transaksi'?'#fff':'#6b7280' }};
                       border-right:1px solid #e5e7eb;">
                Daftar Transaksi
            </button>
            <button wire:click="$set('tab','log')"
                style="padding:0.5rem 1.25rem;font-size:0.85rem;border:none;cursor:pointer;white-space:nowrap;
                       font-weight:{{ $tab==='log'?'700':'500' }};
                       background:{{ $tab==='log'?'#1f2937':'transparent' }};
                       color:{{ $tab==='log'?'#fff':'#6b7280' }};">
                Log Perubahan
            </button>
        </div>

        @if ($tab === 'transaksi')

        {{-- ═══ FILTERS ═════════════════════════════════════════════════════ --}}
        <x-filament::section heading="Pencarian">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari no ref, pengirim, rekening tujuan, uraian..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterVerified"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Semua</option>
                        <option value="pending">Belum Verifikasi</option>
                        <option value="verified">Sudah Verifikasi</option>
                    </select>
                </div>
                <button wire:click="resetFilter"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition h-[38px]">
                    ↻ Reset
                </button>
            </div>
        </x-filament::section>

        {{-- ═══ TABEL TRANSAKSI ══════════════════════════════════════════ --}}
        <x-filament::section heading="Transaksi">
            @php $trans = $this->transaksiList; @endphp
            @if ($trans->isEmpty())
                <div class="py-6 text-center text-gray-400">Tidak ada transaksi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-3 text-center w-10">✓</th>
                                <th class="pb-3 pr-3 text-center w-10"></th>
                                <th class="pb-3 pr-3 text-left">Tanggal</th>
                                <th class="pb-3 pr-3 text-left">No. Ref</th>
                                <th class="pb-3 pr-3 text-left">Rek. Tujuan</th>
                                <th class="pb-3 pr-3 text-left">Pengirim</th>
                                <th class="pb-3 pr-3 text-left">Uraian</th>
                                <th class="pb-3 pr-3 text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($trans as $item)
                                @php
                                    $isVerified = !is_null($item->verified_at);
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $isVerified ? 'opacity-80' : '' }}">
                                    <td class="py-2.5 pr-3 text-center">
                                        <button type="button"
                                            wire:click.stop="toggleVerifikasi({{ $item->id }})"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded border-2 transition cursor-pointer"
                                            style="border-color: {{ $isVerified ? '#3b82f6' : '#d1d5db' }}; background: {{ $isVerified ? '#3b82f6' : 'transparent' }};">
                                            @if ($isVerified)
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="py-2.5 pr-3 text-center">
                                        <button wire:click.stop="openEdit({{ $item->id }})"
                                            class="text-gray-400 hover:text-primary-600 transition"
                                            title="Edit">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                    </td>
                                    <td class="py-2.5 pr-3 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-3 font-mono text-xs">{{ $item->no_ref ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-xs">{{ \App\Models\MasterRekeningTujuan::where('label', $item->rekening_tujuan)->value('bank') ?: ($item->rekening_tujuan ?: '—') }}</td>
                                    <td class="py-2.5 pr-3">{{ $item->nama_rekening_pengirim ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 max-w-xs truncate" title="{{ $item->uraian }}">
                                        {{ $item->uraian }} — Rp {{ number_format($item->debit, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 pr-3 text-center">
                                        @if ($item->source_bukti_url)
                                            <a href="{{ $item->source_bukti_url }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 transition">
                                                <x-heroicon-o-photo class="w-4 h-4" />
                                                Lihat
                                            </a>
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
                    {{ $trans->links() }}
                </div>
            @endif
        </x-filament::section>

        {{-- ═══ MODAL EDIT ══════════════════════════════════════════════════ --}}
        @if ($editId)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                wire:click.self="closeEdit"
                wire:key="edit-modal-{{ $editId }}">
                <div class="w-full max-w-lg rounded-xl shadow-2xl p-6 space-y-4"
                    style="background:#fff7ed;border:2px solid #f97316;"
                    wire:click.stop>
                    <h3 class="text-lg font-bold text-gray-800">Edit Data Transaksi</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">No. Referensi</label>
                        <input type="text" wire:model="editNoRef"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rekening Tujuan</label>
                        <select wire:model="editRekeningTujuan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">— Pilih —</option>
                            @foreach ($this->rekeningOptions as $label => $v)
                                <option value="{{ $label }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pengirim</label>
                        <input type="text" wire:model="editNamaPengirim"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Struk</label>
                        <input type="date" wire:model="editTanggal"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div x-data="{
                        nominalAwal: {{ (int) $editNominalAwal }},
                        potongan: '{{ $editPotongan }}',
                        get nominalBayar() {
                            let p = parseInt(this.potongan) || 0;
                            return Math.max(0, this.nominalAwal - p);
                        },
                        potonganInput(e) {
                            let raw = e.target.value.replace(/[^0-9]/g, '');
                            this.potongan = raw;
                            let p = parseInt(raw) || 0;
                            let nb = Math.max(0, this.nominalAwal - p);
                            $wire.set('editPotongan', String(p));
                            $wire.set('editNominal', String(nb));
                            e.target.value = raw;
                        }
                    }">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Tagihan (Rp)</label>
                            <input type="text" :value="'Rp ' + nominalAwal.toLocaleString('id-ID')" readonly
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-gray-100 text-gray-500">
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Potongan / Diskon (Rp)</label>
                            <input type="text" x-bind:value="potongan"
                                x-on:input="potonganInput($event)"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nominal Bayar (Rp)</label>
                            <input type="text" :value="'Rp ' + nominalBayar.toLocaleString('id-ID')" readonly
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-gray-100 text-green-700 font-semibold">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Konfirmasi Password</label>
                        <input type="password" wire:model="editPassword"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @if ($editError)
                            <p class="mt-1 text-xs" style="color:#dc2626;">{{ $editError }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button wire:click="closeEdit"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button wire:click="saveEdit"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm text-white hover:bg-primary-700 transition inline-flex items-center gap-2"
                            wire:loading.attr="disabled"
                            wire:target="saveEdit">
                            <span wire:loading.remove wire:target="saveEdit">Simpan Perubahan</span>
                            <span wire:loading wire:target="saveEdit" class="inline-flex items-center gap-1">
                                &#x21bb; Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══ MODAL KONFIRMASI VERIFIKASI ════════════════════════════════ --}}
        @if ($showVerifModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            wire:click.self="closeVerifModal"
            wire:key="verif-modal-{{ $verifToggleId }}">
            <div class="w-full max-w-md rounded-xl shadow-2xl p-6 space-y-4"
                style="background:#fff7ed;border:2px solid #f97316;">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ $verifToggleTo ? 'Verifikasi Transaksi' : 'Batalkan Verifikasi' }}
                </h3>
                <p class="text-sm text-gray-600">
                    {{ $verifToggleTo ? 'Transaksi ini akan ditandai sebagai terverifikasi.' : 'Status verifikasi transaksi ini akan dibatalkan.' }}
                    Masukkan password untuk melanjutkan.
                </p>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Password</label>
                    <input type="password" wire:model="verifPassword"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        wire:keydown.enter="submitVerifikasi">
                    @if ($verifError)
                        <p class="mt-1 text-xs" style="color:#dc2626;">{{ $verifError }}</p>
                    @endif
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button wire:click="closeVerifModal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button wire:click="submitVerifikasi"
                        class="rounded-lg px-4 py-2 text-sm text-white transition inline-flex items-center gap-2"
                        style="background:{{ $verifToggleTo ? '#2563eb' : '#ea580c' }};"
                        wire:loading.attr="disabled"
                        wire:target="submitVerifikasi">
                        <span wire:loading.remove wire:target="submitVerifikasi">
                            {{ $verifToggleTo ? 'Verifikasi' : 'Batalkan Verifikasi' }}
                        </span>
                        <span wire:loading wire:target="submitVerifikasi">&#x21bb; Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        @else {{-- tab log --}}

        {{-- ═══ LOG VIEWER ══════════════════════════════════════════════════ --}}
        <x-filament::section heading="Log Perubahan Data">
            <div class="mb-3 max-w-sm">
                <input type="text" wire:model.live.debounce.500ms="logSearch" placeholder="Cari dalam log..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>

            @php $logs = $this->logList; @endphp
            @if ($logs->isEmpty())
                <div class="py-6 text-center text-gray-400">Belum ada log perubahan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-3 text-left">Waktu</th>
                                <th class="pb-3 pr-3 text-center">Aksi</th>
                                <th class="pb-3 pr-3 text-left">User</th>
                                <th class="pb-3 pr-3 text-left">Uraian</th>
                                <th class="pb-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-2.5 pr-3 whitespace-nowrap text-xs">
                                        {{ $log->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="py-2.5 pr-3 text-center">
                                        @php
                                            $actColor = match($log->action) {
                                                'create'   => 'bg-success-100 text-success-700',
                                                'update'   => 'bg-blue-100 text-blue-700',
                                                'unverify' => 'bg-danger-100 text-danger-700',
                                                default    => 'bg-gray-100 text-gray-600',
                                            };
                                            $actLabel = match($log->action) {
                                                'create'   => 'CREATE',
                                                'update'   => 'UPDATE',
                                                'unverify' => 'UNVERIFY',
                                                default    => $log->action,
                                            };
                                        @endphp
                                        <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $actColor }}">
                                            {{ $actLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 pr-3 text-xs text-gray-500">{{ $log->createdBy?->name ?: '—' }}</td>
                                    <td class="py-2.5 pr-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $log->uraian ?? '—' }}">
                                        {{ $log->uraian ?? '—' }}
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <button wire:click.stop="openLogDetail({{ $log->id }})"
                                            class="text-primary-600 hover:text-primary-800 transition inline-flex items-center gap-1 text-xs font-medium">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </x-filament::section>

        {{-- ═══ MODAL DETAIL LOG ═══════════════════════════════════════════ --}}
        @if ($logDetailId)
            @php $detail = $this->logDetail; @endphp
            @if ($detail)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                wire:click.self="closeLogDetail"
                wire:key="log-detail-{{ $logDetailId }}">
                <div class="w-full max-w-3xl rounded-xl shadow-2xl p-6 space-y-4"
                    style="background:#fff7ed;border:2px solid #f97316;">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">
                            Detail Perubahan
                            <span class="ml-2 text-xs font-normal text-gray-500">
                                {{ $detail->created_at->format('d M Y H:i') }} — {{ $detail->createdBy?->name ?? '—' }}
                            </span>
                        </h3>
                        <button wire:click="closeLogDetail"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 border-b pb-1" style="border-color:#f97316;">Data Lama</h4>
                            @if ($detail->data_lama && count($detail->data_lama) > 0)
                                <div class="text-xs space-y-1.5">
                                    @foreach ($detail->data_lama as $key => $val)
                                        @php
                                            $baruVal = $detail->data_baru[$key] ?? null;
                                            $isDiff = $detail->action === 'update' && (string) $val !== (string) $baruVal;
                                        @endphp
                                        <div class="flex gap-2 {{ $isDiff ? 'rounded px-1 py-0.5' : '' }}" style="{{ $isDiff ? 'background:#fef2f2;' : '' }}">
                                            <span class="text-gray-500 w-20 shrink-0 capitalize">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            <span class="font-medium break-all" style="color:{{ $isDiff ? '#dc2626' : '#4b5563' }};text-decoration:{{ $isDiff ? 'line-through' : 'none' }}">{{ is_null($val) ? '—' : $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">—</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 border-b pb-1" style="border-color:#f97316;">Data Baru</h4>
                            @if ($detail->data_baru && count($detail->data_baru) > 0)
                                <div class="text-xs space-y-1.5">
                                    @foreach ($detail->data_baru as $key => $val)
                                        @php
                                            $lamaVal = $detail->data_lama[$key] ?? null;
                                            $isDiff = $detail->action === 'update' && (string) $val !== (string) $lamaVal;
                                        @endphp
                                        <div class="flex gap-2 {{ $isDiff ? 'rounded px-1 py-0.5' : '' }}" style="{{ $isDiff ? 'background:#f0fdf4;' : '' }}">
                                            <span class="text-gray-500 w-20 shrink-0 capitalize">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            <span class="font-medium break-words" style="color:{{ $isDiff ? '#16a34a' : '#374151' }}; font-weight:{{ $isDiff ? '700' : '500' }}">{{ is_null($val) ? '—' : $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">—</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button wire:click="closeLogDetail"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @endif
        
        @endif
    </div>
</x-filament-panels::page>