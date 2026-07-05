<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ══ MODE TABS ══════════════════════════════════════════════════════ --}}
        <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #e5e7eb;
                    overflow:hidden;background:#f9fafb;width:fit-content;">
            <button wire:click="$set('viewMode','inquiry')"
                style="padding:0.5rem 1.25rem;font-size:0.85rem;border:none;cursor:pointer;white-space:nowrap;
                       font-weight:{{ $viewMode==='inquiry'?'700':'500' }};
                       background:{{ $viewMode==='inquiry'?'#1f2937':'transparent' }};
                       color:{{ $viewMode==='inquiry'?'#fff':'#6b7280' }};
                       border-right:1px solid #e5e7eb;">
                🔍 Inquiry Siswa
            </button>
            <button wire:click="$set('viewMode','kelas')"
                style="padding:0.5rem 1.25rem;font-size:0.85rem;border:none;cursor:pointer;white-space:nowrap;
                       font-weight:{{ $viewMode==='kelas'?'700':'500' }};
                       background:{{ $viewMode==='kelas'?'#1f2937':'transparent' }};
                       color:{{ $viewMode==='kelas'?'#fff':'#6b7280' }};">
                📋 Per Kelas
            </button>
        </div>

        {{-- ══ INQUIRY SISWA ══════════════════════════════════════════════════ --}}
        @if ($viewMode === 'inquiry')

        <x-filament::section heading="Inquiry Siswa">
            <div class="max-w-lg">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cari Siswa (NIS atau Nama)
                </label>
                <div class="relative">
                    <div class="flex gap-2">
                        <input type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            wire:focus="$set('showResults', true)"
                            placeholder="Ketik NIS atau nama siswa..."
                            autocomplete="off"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-800 px-3 py-2 text-sm shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        @if ($siswa_id)
                            <button wire:click="clearSiswa" type="button"
                                class="rounded-lg border border-gray-300 dark:border-gray-600
                                       px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 transition">✕</button>
                        @endif
                    </div>

                    @if ($showResults && strlen(trim($searchQuery)) >= 2)
                        <div class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200
                                    dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
                            @forelse ($this->searchResults as $siswa)
                                <button type="button" wire:click="selectSiswa({{ $siswa->id }})"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-primary-50
                                           dark:hover:bg-primary-900/30 transition flex items-center gap-3
                                           border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700
                                                 rounded px-1.5 py-0.5 text-gray-600 dark:text-gray-300">
                                        {{ $siswa->nis }}
                                    </span>
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $siswa->nama }}</span>
                                    <span class="ml-auto text-xs text-gray-400">
                                        @if ($siswa->is_calon)
                                            {{ strtoupper($siswa->calon_jenis ?? '') }} - {{ \App\Filament\Pages\PembayaranSiswaPage::formatCalonTingkat($siswa->calon_tingkat, $siswa->calon_jenis) }}
                                        @else
                                            Kelas {{ $siswa->kelasSaatIni?->kelas ?? '-' }}
                                        @endif
                                    </span>
                                </button>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-400 text-center">Siswa tidak ditemukan.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-400">Ketik minimal 2 karakter untuk melihat rekomendasi.</p>
            </div>
        </x-filament::section>

        @if ($selectedSiswa)

        {{-- INFO SISWA --}}
        <x-filament::section>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-1">
                <div class="space-y-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 pb-1
                            border-b border-gray-100 dark:border-gray-700">Data Siswa</h3>
                                        <dl class="space-y-2 text-sm">
                        @php
                            $current = $selectedSiswa->kelasSaatIni;
                            $isCalon = $selectedSiswa->is_calon;
                        @endphp
                        @foreach ([
                            'NIS'           => $selectedSiswa->nis,
                            'Nama'          => $selectedSiswa->nama,
                            'Kelas'         => $current?->kelas,
                            'Tingkat'       => $isCalon
                                ? \App\Filament\Pages\PembayaranSiswaPage::formatCalonTingkat($selectedSiswa->calon_tingkat, $selectedSiswa->calon_jenis)
                                : $current?->tingkat,
                            'Tahun Ajaran'  => $current?->tahun_ajaran,
                        ] as $label => $val)
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">{{ $label }}</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $val ?: '—' }}</dd>
                            </div>
                        @endforeach
                        <div class="flex gap-2">
                            <dt class="w-32 shrink-0 text-gray-500">Jenis Sekolah</dt>
                            <dd>
                                @if ($isCalon)
                                    <span class="rounded-full bg-primary-100 text-primary-700 text-xs font-semibold px-2.5 py-0.5">
                                        {{ strtoupper($selectedSiswa->calon_jenis ?? '') }}
                                    </span>
                                @elseif ($current?->jenis_sekolah)
                                    <span class="rounded-full bg-primary-100 text-primary-700 text-xs font-semibold px-2.5 py-0.5">
                                        {{ $current->jenis_sekolah }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-32 shrink-0 text-gray-500">Status</dt>
                            <dd>
                                <span class="rounded-full text-xs font-semibold px-2.5 py-0.5
                                    {{ $selectedSiswa->status_aktif ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">
                                    {{ $selectedSiswa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="space-y-3 mt-6 sm:mt-0">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 pb-1
                            border-b border-gray-100 dark:border-gray-700">Data Orang Tua / Wali</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex gap-2">
                            <dt class="w-32 shrink-0 text-gray-500">Nama</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $selectedSiswa->nama_orang_tua ?: '—' }}</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-32 shrink-0 text-gray-500">No. HP / WA</dt>
                            <dd>
                                @if ($selectedSiswa->no_hp_orang_tua)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', str_starts_with(ltrim($selectedSiswa->no_hp_orang_tua, '0'), '8') ? '62'.ltrim($selectedSiswa->no_hp_orang_tua,'0') : $selectedSiswa->no_hp_orang_tua) }}"
                                       target="_blank" class="text-[#128C7E] hover:underline">
                                        {{ $selectedSiswa->no_hp_orang_tua }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-32 shrink-0 text-gray-500">Email</dt>
                            <dd>
                                @if ($selectedSiswa->email_orang_tua)
                                    <a href="mailto:{{ $selectedSiswa->email_orang_tua }}" class="text-primary-600 hover:underline">
                                        {{ $selectedSiswa->email_orang_tua }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-filament::section>

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
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-4 text-left">Jenis Pembayaran</th>
                                <th class="pb-3 pr-4 text-left">Bulan</th>
                                <th class="pb-3 pr-4 text-left">Tahun</th>
                                <th class="pb-3 pr-4 text-right">Nominal Tagihan</th>
                                <th class="pb-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($this->tagihans as $tagihan)
                                <tr>
                                    <td class="py-3 pr-4 font-medium">
                                        {{ $tagihan->jenisPembayaran->nama }}
                                        @if ($this->isSpp($tagihan))
                                            <span class="ml-1 rounded-full bg-danger-100 text-danger-700 text-xs px-2 py-0.5">Wajib Lunas</span>
                                        @else
                                            <span class="ml-1 rounded-full bg-blue-100 text-blue-700 text-xs px-2 py-0.5">Bisa Cicil</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4">{{ $tagihan->bulan ? $this->getBulanLabel($tagihan->bulan) : '-' }}</td>
                                    <td class="py-3 pr-4">{{ $tagihan->tahun }}</td>
                                    <td class="py-3 pr-4 text-right font-semibold whitespace-nowrap">
                                        Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <x-filament::button
                                            wire:click="mountAction('bayar', { tagihan_id: {{ $tagihan->id }} })"
                                            wire:loading.attr="disabled"
                                            wire:target="mountAction"
                                            size="sm"
                                            color="primary">
                                            Bayar
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>

        {{-- HISTORY PEMBAYARAN --}}
        @if ($this->history->isNotEmpty())
            <x-filament::section heading="History Pembayaran Terbaru">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-semibold uppercase text-gray-500">
                                <th class="pb-3 pr-4 text-left">Jenis</th>
                                <th class="pb-3 pr-4 text-left">Bulan</th>
                                <th class="pb-3 pr-4 text-left">Tahun</th>
                                <th class="pb-3 pr-4 text-right">Nominal</th>
                                <th class="pb-3 pr-4 text-right">Potongan</th>
                                <th class="pb-3 pr-4 text-center">Tgl Bayar</th>
                                <th class="pb-3 pr-4 text-center">No. Ref</th>
                                <th class="pb-3 pr-4 text-center">Status</th>
                                <th class="pb-3 text-center">Kuitansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($this->history as $bayar)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="py-2.5 pr-4 font-medium">{{ $bayar->jenisPembayaran->nama }}</td>
                                    <td class="py-2.5 pr-4">{{ $bayar->bulan ? $this->getBulanLabel($bayar->bulan) : '-' }}</td>
                                    <td class="py-2.5 pr-4">{{ $bayar->tahun }}</td>
                                    <td class="py-2.5 pr-4 text-right font-medium whitespace-nowrap">
                                        Rp {{ number_format($bayar->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-right text-gray-500">
                                        @if (($bayar->potongan ?? 0) > 0)
                                            <span class="text-orange-600">−Rp {{ number_format($bayar->potongan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 pr-4 text-center text-gray-500 text-xs">
                                        {{ \Carbon\Carbon::parse($bayar->tgl_bayar_struk ?? $bayar->tanggal_bayar)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-center text-xs font-mono text-gray-500">
                                        {{ $bayar->no_ref ?: '—' }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-center">
                                        @if ($bayar->status === 'lunas')
                                            <span class="rounded-full bg-success-100 text-success-700 text-xs px-2 py-0.5 font-semibold">Lunas</span>
                                        @elseif ($bayar->status === 'cicilan')
                                            <span class="rounded-full bg-warning-100 text-warning-700 text-xs px-2 py-0.5 font-semibold">Cicilan</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 text-gray-600 text-xs px-2 py-0.5 font-semibold">{{ $bayar->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if ($bayar->share_token)
                                                <a href="{{ url('/k/' . $bayar->share_token) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium transition
                                                        {{ $bayar->status === 'lunas' ? 'bg-success-50 text-success-700 hover:bg-success-100 border border-success-200'
                                                                                      : 'bg-warning-50 text-warning-700 hover:bg-warning-100 border border-warning-200' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                    </svg>
                                                    Kuitansi
                                                </a>
                                                <a href="{{ $this->getWhatsappUrl($bayar) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium transition
                                                           bg-[#25D366]/10 text-[#128C7E] hover:bg-[#25D366]/20 border border-[#25D366]/30">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                                                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.531 5.845L.057 23.428a.5.5 0 00.609.61l5.703-1.498A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.034-1.388l-.36-.214-3.733.98.999-3.645-.235-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                                                    </svg>
                                                    WA
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">—</span>
                                            @endif
                                            @if ($bayar->bukti_bayar)
                                                <button type="button"
                                                    x-data
                                                    x-on:click="
                                                        $nextTick(() => {
                                                            $dispatch('open-bukti-bayar', { url: '{{ Storage::url($bayar->bukti_bayar) }}' });
                                                        });
                                                    "
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium
                                                           bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition cursor-pointer"
                                                    title="Lihat Bukti Bayar">
                                                    <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                                    Bukti
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        {{-- RIWAYAT KELAS PER TAHUN AJARAN ─── --}}
        @php $riwayat = $this->riwayatPerTahun; @endphp
        @if (count($riwayat) > 0)
            <x-filament::section heading="Riwayat Pembayaran per Tahun Ajaran">
                <div class="space-y-3">
                    @foreach ($riwayat as $r)
                        <details class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <summary style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1.25rem;
                                            cursor:pointer;background:#f9fafb;list-style:none;user-select:none;"
                                class="hover:bg-gray-100 transition">
                                <span style="font-weight:700;font-size:0.875rem;color:#1f2937;">
                                    T.A. {{ $r['tahun_ajaran'] }}
                                </span>
                                @if ($r['kelas'] !== '—')
                                    <span style="background:#e0e7ff;color:#3730a3;border-radius:0.3rem;
                                                 padding:0.1rem 0.5rem;font-size:0.72rem;font-weight:600;">
                                        {{ $r['jenis_sekolah'] }} — Kelas {{ $r['kelas'] }}
                                    </span>
                                @endif
                                <span style="margin-left:auto;font-size:0.8rem;color:#6b7280;font-weight:600;">
                                    {{ count($r['pembayaran']) }} transaksi
                                    · Rp {{ number_format($r['total'], 0, ',', '.') }}
                                </span>
                                <svg style="width:1rem;height:1rem;color:#9ca3af;flex-shrink:0;"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>

                            <div style="padding:0.75rem 1.25rem;">
                                <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                                    <thead>
                                        <tr style="border-bottom:1px solid #f1f5f9;">
                                            <th style="padding:0.4rem 0.75rem 0.4rem 0;text-align:left;color:#9ca3af;font-size:0.68rem;font-weight:600;text-transform:uppercase;">Jenis</th>
                                            <th style="padding:0.4rem 0.5rem;text-align:left;color:#9ca3af;font-size:0.68rem;font-weight:600;text-transform:uppercase;">Bulan</th>
                                            <th style="padding:0.4rem 0.5rem;text-align:right;color:#9ca3af;font-size:0.68rem;font-weight:600;text-transform:uppercase;">Nominal</th>
                                            <th style="padding:0.4rem 0.5rem;text-align:center;color:#9ca3af;font-size:0.68rem;font-weight:600;text-transform:uppercase;">Tanggal</th>
                                            <th style="padding:0.4rem 0 0.4rem 0.5rem;text-align:center;color:#9ca3af;font-size:0.68rem;font-weight:600;text-transform:uppercase;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($r['pembayaran'] as $p)
                                            <tr style="border-bottom:1px solid #f8fafc;">
                                                <td style="padding:0.5rem 0.75rem 0.5rem 0;font-weight:600;color:#374151;">{{ $p['jenis'] }}</td>
                                                <td style="padding:0.5rem 0.5rem;color:#6b7280;">{{ $p['bulan'] }} {{ $p['tahun'] }}</td>
                                                <td style="padding:0.5rem 0.5rem;text-align:right;font-weight:600;color:#1f2937;font-variant-numeric:tabular-nums;">
                                                    Rp {{ number_format($p['nominal'], 0, ',', '.') }}
                                                </td>
                                                <td style="padding:0.5rem 0.5rem;text-align:center;color:#9ca3af;font-size:0.75rem;">{{ $p['tanggal'] }}</td>
                                                <td style="padding:0.5rem 0 0.5rem 0.5rem;text-align:center;">
                                                    <span style="padding:0.15rem 0.5rem;border-radius:1rem;font-size:0.68rem;font-weight:600;
                                                                 background:{{ $p['status']==='lunas'?'#dcfce7':($p['status']==='cicilan'?'#fef9c3':'#f3f4f6') }};
                                                                 color:{{ $p['status']==='lunas'?'#15803d':($p['status']==='cicilan'?'#78350f':'#374151') }};">
                                                        {{ ucfirst($p['status']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        @endif {{-- end selectedSiswa --}}
        @endif {{-- end viewMode = inquiry --}}

        {{-- ══ PER KELAS ═══════════════════════════════════════════════════════ --}}
        @if ($viewMode === 'kelas')

        {{-- Filter Bar --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.75rem;">
            <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                        overflow:hidden;background:#f9fafb;flex-shrink:0;">
                @foreach ($this->jenisSekolahList as $js)
                    <button wire:click="$set('filterJenisSekolah', '{{ $js }}')"
                        style="padding:0.45rem 0.9rem;font-size:0.8rem;border:none;cursor:pointer;white-space:nowrap;
                               font-weight:{{ $filterJenisSekolah===$js?'700':'500' }};
                               background:{{ $filterJenisSekolah===$js?'#1f2937':'transparent' }};
                               color:{{ $filterJenisSekolah===$js?'#fff':'#6b7280' }};
                               border-right:1px solid #e5e7eb;">
                        {{ $js }}
                    </button>
                @endforeach
            </div>

            @if ($filterJenisSekolah && count($this->kelasList) > 0)
                <div style="display:flex;align-items:center;border-radius:0.5rem;border:1px solid #d1d5db;
                            overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.05);">
                    <span style="padding:0.5rem 0.75rem;font-size:0.8rem;color:#6b7280;border-right:1px solid #e5e7eb;">Kelas</span>
                    <select wire:model.live="filterKelas"
                        style="border:0;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;
                               color:#374151;outline:none;cursor:pointer;min-width:6rem;">
                        <option value="">— Pilih —</option>
                        @foreach ($this->kelasList as $k)
                            <option value="{{ $k }}" @selected($filterKelas===$k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($filterJenisSekolah && $filterKelas)
                @php
                    $tahunMulai  = $this->akademikTahunMulai();
                    $tahunAkhir  = $tahunMulai + 1;
                @endphp
                <div style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;
                            border-radius:0.5rem;background:#eff6ff;border:1px solid #bfdbfe;
                            font-size:0.8rem;color:#1d4ed8;font-weight:600;">
                    <x-heroicon-o-calendar style="width:0.9rem;height:0.9rem;" />
                    T.A. {{ $tahunMulai }}/{{ $tahunAkhir }}
                </div>
            @endif
        </div>

        @if (! $filterJenisSekolah || ! $filterKelas)
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                        padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <x-heroicon-o-academic-cap style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.4;" />
                <p style="font-size:0.875rem;">Pilih kategori pendidikan dan kelas untuk melihat laporan SPP.</p>
            </div>
        @else

        @php $matrix = $this->sppMatrixKelas; @endphp
        @if (empty($matrix['rows']))
            <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <p style="font-size:0.875rem;">Tidak ada siswa aktif di kelas ini.</p>
            </div>
        @else

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:#1f2937;margin:0;">
                    Laporan SPP — {{ $filterJenisSekolah }} Kelas {{ $filterKelas }}
                </h2>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0.2rem 0 0;">
                    T.A. {{ $this->akademikTahunMulai() }}/{{ $this->akademikTahunMulai() + 1 }}
                    · {{ count($matrix['rows']) }} siswa
                </p>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;">
                <span style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.72rem;color:#374151;">
                    <span style="width:0.9rem;height:0.9rem;border-radius:0.2rem;background:#dcfce7;border:1px solid #86efac;display:inline-block;"></span>Lunas
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.72rem;color:#374151;">
                    <span style="width:0.9rem;height:0.9rem;border-radius:0.2rem;background:#fee2e2;border:1px solid #fca5a5;display:inline-block;"></span>Tunggakan
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.72rem;color:#374151;">
                    <span style="width:0.9rem;height:0.9rem;border-radius:0.2rem;background:#f3f4f6;border:1px solid #e5e7eb;display:inline-block;"></span>Belum ada tagihan
                </span>
            </div>
        </div>

        <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                    box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.78rem;min-width:900px;">
                    <thead>
                        @php
                            $months     = $matrix['months'];
                            $yearGroups = [];
                            foreach ($months as $m) {
                                $t = $m['tahun'];
                                $yearGroups[$t] = ($yearGroups[$t] ?? 0) + 1;
                            }
                        @endphp
                        <tr style="background:#1f2937;color:#fff;">
                            <th colspan="3" style="padding:0.6rem 0.75rem;text-align:center;font-size:0.7rem;
                                                   font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                                   border-right:1px solid #374151;">Siswa</th>
                            @foreach ($yearGroups as $tahun => $span)
                                <th colspan="{{ $span }}"
                                    style="padding:0.5rem 0.75rem;text-align:center;font-weight:700;font-size:0.8rem;
                                           letter-spacing:0.05em;border-right:1px solid #374151;">
                                    TAHUN {{ $tahun }}
                                </th>
                            @endforeach
                            <th style="padding:0.5rem 0.75rem;text-align:center;font-size:0.7rem;
                                       font-weight:600;border-left:1px solid #374151;white-space:nowrap;">Rekap</th>
                        </tr>
                        <tr style="background:#374151;color:#d1d5db;">
                            <th style="padding:0.5rem 0.6rem;text-align:center;font-size:0.68rem;font-weight:600;width:2rem;border-right:1px solid #4b5563;">No</th>
                            <th style="padding:0.5rem 0.75rem;text-align:left;font-size:0.68rem;font-weight:600;border-right:1px solid #4b5563;">Nama</th>
                            <th style="padding:0.5rem 0.6rem;text-align:center;font-size:0.68rem;font-weight:600;width:3rem;border-right:1px solid #4b5563;">Kls</th>
                            @foreach ($months as $m)
                                <th style="padding:0.5rem 0.4rem;text-align:center;font-size:0.68rem;font-weight:600;
                                           white-space:nowrap;min-width:5.5rem;border-right:1px solid #4b5563;">
                                    {{ $this->getBulanPendek($m['bulan']) }}
                                </th>
                            @endforeach
                            <th style="padding:0.5rem 0.5rem;text-align:center;font-size:0.68rem;font-weight:600;
                                       white-space:nowrap;border-left:1px solid #4b5563;min-width:5rem;">✓ / ✗</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matrix['rows'] as $row)
                            <tr style="border-bottom:1px solid #f1f5f9;"
                                onmouseover="this.style.background='#f8fafc'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding:0.5rem 0.6rem;text-align:center;color:#9ca3af;font-size:0.72rem;border-right:1px solid #f1f5f9;">{{ $row['no'] }}</td>
                                <td style="padding:0.5rem 0.75rem;font-weight:600;color:#1f2937;border-right:1px solid #f1f5f9;">{{ $row['nama'] }}</td>
                                <td style="padding:0.5rem 0.6rem;text-align:center;color:#6b7280;font-size:0.72rem;font-weight:600;border-right:1px solid #f1f5f9;">{{ $row['kelas'] }}</td>
                                @foreach ($row['cells'] as $cell)
                                    @php
                                        $bg = match($cell['status']) {
                                            'lunas'    => '#f0fdf4', 'cicilan'  => '#fffbeb',
                                            'tunggakan'=> '#fff1f2', default    => '#fafafa',
                                        };
                                        $bd = match($cell['status']) {
                                            'lunas'    => '#bbf7d0', 'cicilan'  => '#fde68a',
                                            'tunggakan'=> '#fecaca', default    => '#f1f5f9',
                                        };
                                    @endphp
                                    <td style="padding:0.3rem 0.4rem;text-align:center;background:{{ $bg }};
                                               border:1px solid {{ $bd }};vertical-align:middle;">
                                        @if ($cell['status'] === 'lunas' || $cell['status'] === 'cicilan')
                                            <div style="font-size:0.65rem;color:{{ $cell['status']==='lunas'?'#15803d':'#92400e' }};
                                                        font-weight:600;line-height:1.3;white-space:nowrap;">
                                                {{ $cell['tanggal'] }}
                                            </div>
                                            <div style="font-size:0.68rem;color:{{ $cell['status']==='lunas'?'#166534':'#78350f' }};
                                                        font-variant-numeric:tabular-nums;font-weight:700;">
                                                {{ number_format($cell['nominal'], 0, ',', '.') }}
                                            </div>
                                        @elseif ($cell['status'] === 'tunggakan')
                                            <div style="font-size:0.65rem;color:#b91c1c;font-weight:700;">Tunggakan</div>
                                            <div style="font-size:0.68rem;color:#dc2626;font-variant-numeric:tabular-nums;">
                                                {{ number_format($cell['nominal'], 0, ',', '.') }}
                                            </div>
                                            {{-- Tombol bayar langsung dari matrix --}}
                                            @if ($cell['tagihan_id'])
                                                <button
                                                    wire:click="mountAction('bayar', { tagihan_id: {{ $cell['tagihan_id'] }} })"
                                                    style="margin-top:0.25rem;font-size:0.6rem;background:#dc2626;color:#fff;
                                                           border:none;border-radius:0.25rem;padding:0.1rem 0.35rem;cursor:pointer;"
                                                    onmouseover="this.style.background='#b91c1c'"
                                                    onmouseout="this.style.background='#dc2626'">
                                                    Bayar
                                                </button>
                                            @endif
                                        @else
                                            <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="padding:0.5rem 0.5rem;text-align:center;border-left:1px solid #e5e7eb;">
                                    @if ($row['tunggakan'] > 0)
                                        <div style="font-size:0.68rem;color:#dc2626;font-weight:700;white-space:nowrap;">{{ $row['tunggakan'] }}✗</div>
                                    @endif
                                    @if ($row['lunas'] > 0)
                                        <div style="font-size:0.68rem;color:#16a34a;font-weight:600;white-space:nowrap;">{{ $row['lunas'] }}✓</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr style="background:#f8fafc;border-top:2px solid #e5e7eb;">
                            <td colspan="3" style="padding:0.6rem 0.75rem;font-size:0.72rem;font-weight:700;color:#374151;border-right:1px solid #e5e7eb;">Rekap Bulan</td>
                            @foreach ($matrix['summary'] as $s)
                                <td style="padding:0.4rem 0.3rem;text-align:center;border:1px solid #e5e7eb;">
                                    @if ($s['lunas'] > 0)<div style="font-size:0.65rem;color:#15803d;font-weight:700;">{{ $s['lunas'] }}✓</div>@endif
                                    @if ($s['tunggakan'] > 0)<div style="font-size:0.65rem;color:#dc2626;font-weight:700;">{{ $s['tunggakan'] }}✗</div>@endif
                                </td>
                            @endforeach
                            <td style="border-left:1px solid #e5e7eb;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @endif {{-- empty rows --}}
        @endif {{-- filter check --}}
        @endif {{-- viewMode kelas --}}

    </div>

{{-- Popup preview bukti bayar --}}
<div
    x-data="{ open: false, url: '' }"
    x-on:open-bukti-bayar.window="url = $event.detail.url; open = true"
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
            <img :src="url" alt="Bukti Bayar" class="max-w-full h-auto rounded">
        </div>
    </div>
</div>
</x-filament-panels::page>
