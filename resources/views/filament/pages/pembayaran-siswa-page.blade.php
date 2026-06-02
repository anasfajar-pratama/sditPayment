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
            <x-filament::section>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-1">

                    {{-- Kolom Kiri: Data Siswa --}}
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 pb-1
                                border-b border-gray-100 dark:border-gray-700">
                            Data Siswa
                        </h3>

                        <dl class="space-y-2 text-sm">
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">NIS</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $selectedSiswa->nis ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Nama</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">
                                    {{ $selectedSiswa->nama ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Kelas</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $selectedSiswa->kelas ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Tingkat</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $selectedSiswa->tingkat ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Jenis Sekolah</dt>
                                <dd>
                                    @if ($selectedSiswa->jenis_sekolah)
                                        <span class="rounded-full bg-primary-100 text-primary-700
                                                    dark:bg-primary-900/40 dark:text-primary-300
                                                    text-xs font-semibold px-2.5 py-0.5">
                                            {{ $selectedSiswa->jenis_sekolah }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Tahun Ajaran</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $selectedSiswa->tahun_ajaran ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Status</dt>
                                <dd>
                                    @if ($selectedSiswa->status_aktif)
                                        <span class="rounded-full bg-success-100 text-success-700
                                                    text-xs font-semibold px-2.5 py-0.5">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="rounded-full bg-danger-100 text-danger-700
                                                    text-xs font-semibold px-2.5 py-0.5">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Kolom Kanan: Data Orang Tua --}}
                    <div class="space-y-3 mt-6 sm:mt-0">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 pb-1
                                border-b border-gray-100 dark:border-gray-700">
                            Data Orang Tua / Wali
                        </h3>

                        <dl class="space-y-2 text-sm">
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Nama</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $selectedSiswa->nama_orang_tua ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">No. HP / WA</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    @if ($selectedSiswa->no_hp_orang_tua)
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', str_starts_with(ltrim($selectedSiswa->no_hp_orang_tua, '0'), '8') ? '62' . ltrim($selectedSiswa->no_hp_orang_tua, '0') : $selectedSiswa->no_hp_orang_tua) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[#128C7E] hover:underline">
                                            {{ $selectedSiswa->no_hp_orang_tua }}
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.531 5.845L.057 23.428a.5.5 0 00.609.61l5.703-1.498A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.034-1.388l-.36-.214-3.733.98.999-3.645-.235-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-32 shrink-0 text-gray-500">Email</dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-100">
                                    @if ($selectedSiswa->email_orang_tua)
                                        <a href="mailto:{{ $selectedSiswa->email_orang_tua }}"
                                        class="text-primary-600 hover:underline">
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

                                        {{-- Tombol Kuitansi + WhatsApp --}}
                                        <td class="py-2.5 text-center">
                                            <div class="inline-flex items-center gap-1.5">

                                                {{-- Tombol Kuitansi / Bukti Cicilan --}}
                                                @if ($bayar->share_token)
                                                    <a
                                                        href="{{ url('/k/' . $bayar->share_token) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5
                                                            text-xs font-medium transition
                                                            {{ $bayar->status === 'lunas'
                                                                ? 'bg-success-50 text-success-700 hover:bg-success-100 border border-success-200'
                                                                : 'bg-warning-50 text-warning-700 hover:bg-warning-100 border border-warning-200' }}"
                                                        title="{{ $bayar->status === 'lunas' ? 'Lihat Kuitansi' : 'Lihat Bukti Cicilan' }}"
                                                    >
                                                        {{-- Ikon printer --}}
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                            </path>
                                                        </svg>
                                                        {{ $bayar->status === 'lunas' ? 'Kuitansi' : 'Bukti Cicilan' }}
                                                    </a>

                                                    {{-- Tombol Bagikan via WhatsApp --}}
                                                    <a
                                                        href="{{ $this->getWhatsappUrl($bayar) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5
                                                               text-xs font-medium transition
                                                               bg-[#25D366]/10 text-[#128C7E] hover:bg-[#25D366]/20
                                                               border border-[#25D366]/30"
                                                        title="Bagikan via WhatsApp"
                                                    >
                                                        {{-- Ikon WhatsApp --}}
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.531 5.845L.057 23.428a.5.5 0 00.609.61l5.703-1.498A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.034-1.388l-.36-.214-3.733.98.999-3.645-.235-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                                                        </svg>
                                                        WA
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Token tidak tersedia</span>
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

        @endif

    </div>
</x-filament-panels::page>
