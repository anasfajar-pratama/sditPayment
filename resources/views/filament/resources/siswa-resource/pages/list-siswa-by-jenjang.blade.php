{{--
    File: resources/views/filament/resources/siswa-resource/pages/list-siswa-by-jenjang.blade.php
    Buat folder jika belum ada:
    resources/views/filament/resources/siswa-resource/pages/
--}}

<x-filament-panels::page>

    {{-- ── Header info jenjang ──────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold {{ $this->getJenjangColor() }}">
            {{ $jenjang }}
        </span>
        <span class="text-gray-500 text-sm">
            {{ count($kelasData) }} kelas tersedia ·
            {{ collect($kelasData)->sum('jumlah') }} siswa{{ $filterTahunAjaran ? '' : ' aktif' }}
        </span>

        {{-- ── Filter tahun ajaran ──────────────────────────────────────────── --}}
        @php
            $taList = $this->tahunAjaranList();
        @endphp
        @if (count($taList) > 0)
            <div class="ml-auto flex items-center gap-2">
                <label for="filter_ta" class="text-xs text-gray-500 font-medium">Tahun Ajaran</label>
                <select id="filter_ta" wire:model.live="filterTahunAjaran"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700
                           shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500
                           dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($taList as $ta)
                        <option value="{{ $ta }}" @selected($filterTahunAjaran === $ta)>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- ── Grid kartu kelas ─────────────────────────────────────────────────── --}}
    @if (count($kelasData) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach ($kelasData as $item)
                <a
                    href="{{ $item['url'] }}"
                    wire:navigate
                    class="group relative flex flex-col items-center justify-center gap-2
                           rounded-2xl border border-gray-200 dark:border-gray-700
                           bg-white dark:bg-gray-800
                           p-5 shadow-sm
                           transition-all duration-200
                           hover:shadow-md hover:border-primary-400 hover:scale-[1.03]
                           cursor-pointer"
                >
                    {{-- Icon buku --}}
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl
                                {{ match($jenjang) {
                                    'SD'   => 'bg-green-100 text-green-600 group-hover:bg-green-200',
                                    'SMP'  => 'bg-blue-100 text-blue-600 group-hover:bg-blue-200',
                                    'DTA'  => 'bg-yellow-100 text-yellow-700 group-hover:bg-yellow-200',
                                    'PAUD' => 'bg-red-100 text-red-600 group-hover:bg-red-200',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}
                                transition-colors duration-200">
                        <x-heroicon-o-academic-cap class="h-6 w-6" />
                    </div>

                    {{-- Nama kelas --}}
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                            Kelas {{ $item['kelas'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $item['jumlah'] }} siswa
                        </p>
                    </div>

                    {{-- Badge jumlah siswa di pojok --}}
                    <span class="absolute top-2 right-2 inline-flex items-center justify-center
                                 w-6 h-6 rounded-full text-[10px] font-bold
                                 bg-primary-500 text-white opacity-0 group-hover:opacity-100
                                 transition-opacity duration-200">
                        {{ $item['jumlah'] }}
                    </span>
                </a>
            @endforeach
        </div>
    @else
        {{-- ── Empty state ──────────────────────────────────────────────────── --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <x-heroicon-o-academic-cap class="h-8 w-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                Belum ada data kelas untuk {{ $jenjang }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm">
                Tambahkan siswa terlebih dahulu dengan memilih jenjang <strong>{{ $jenjang }}</strong>
                agar kartu kelas muncul di sini.
            </p>
            <a
                href="{{ \App\Filament\Resources\SiswaResource::getUrl('create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white
                       text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors"
            >
                <x-heroicon-o-plus class="h-4 w-4" />
                Tambah Siswa {{ $jenjang }}
            </a>
        </div>
    @endif

</x-filament-panels::page>
