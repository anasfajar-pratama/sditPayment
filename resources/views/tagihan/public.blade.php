{{--
    File: resources/views/tagihan/public.blade.php
    Halaman publik tagihan — dapat diakses tanpa login.
    Letakkan di: resources/views/tagihan/public.blade.php
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan – {{ $tagihan->siswa->nama ?? 'Siswa' }}</title>
    <meta name="robots" content="noindex, nofollow">

    {{-- Tailwind CDN — hanya untuk halaman publik ini --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-start py-8 px-4">

    {{-- ── Kartu Utama ──────────────────────────────────────────────────────── --}}
    <div class="card w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header sekolah --}}
        <div class="bg-orange-500 px-6 py-5 text-white">
            <div class="flex items-center gap-3">
                {{-- Ikon sekolah --}}
                <div class="bg-white/20 rounded-full p-2.5">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 15.5c0 2.485-4.03 4.5-9 4.5s-9-2.015-9-4.5a12.083 12.083 0 012.84-4.922L12 14z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white/80 text-xs font-medium uppercase tracking-wide">Informasi Tagihan</p>
                    <h1 class="text-white font-bold text-lg leading-tight">{{ config('app.name', 'Sekolah') }}</h1>
                </div>
            </div>
        </div>

        {{-- ── Badge Status ────────────────────────────────────────────────── --}}
        <div class="px-6 pt-5 pb-2">
            @if ($tagihan->status === 'lunas')
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-semibold text-sm">Tagihan ini sudah <strong>LUNAS</strong></span>
                </div>
            @else
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 4a8 8 0 100 16A8 8 0 0012 4z"/>
                    </svg>
                    <span class="font-semibold text-sm">Tagihan ini <strong>BELUM DIBAYAR</strong></span>
                </div>
            @endif
        </div>

        {{-- ── Data Siswa ──────────────────────────────────────────────────── --}}
        <div class="px-6 py-4">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Data Siswa</h2>
            <div class="space-y-3">

                <div class="flex justify-between items-start gap-4">
                    <span class="text-sm text-gray-500 min-w-fit">Nama Siswa</span>
                    <span class="text-sm font-semibold text-gray-800 text-right">
                        {{ $tagihan->siswa->nama ?? '-' }}
                    </span>
                </div>

                @if (!empty($tagihan->siswa->nis))
                <div class="flex justify-between items-start gap-4">
                    <span class="text-sm text-gray-500 min-w-fit">NIS</span>
                    <span class="text-sm font-medium text-gray-700 text-right font-mono">
                        {{ $tagihan->siswa->nis }}
                    </span>
                </div>
                @endif

                @if (optional($tagihan->siswa->kelasSaatIni)->kelas)
                <div class="flex justify-between items-start gap-4">
                    <span class="text-sm text-gray-500 min-w-fit">Kelas</span>
                    <span class="text-sm font-medium text-gray-700 text-right">
                        {{ $tagihan->siswa->kelasSaatIni->kelas }}
                    </span>
                </div>
                @endif

            </div>
        </div>

        <div class="border-t border-gray-100 mx-6"></div>

        {{-- ── Detail Tagihan ──────────────────────────────────────────────── --}}
        <div class="px-6 py-4">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Detail Tagihan</h2>

            @if ($tagihan->detail && count($tagihan->detail) > 0)
                <div class="space-y-2">
                    @foreach ($tagihan->detail as $i => $item)
                        <div class="flex justify-between items-center gap-4 py-2 {{ $i > 0 ? 'border-t border-gray-100' : '' }}">
                            <div>
                                <span class="text-sm font-medium text-gray-800 block">{{ $item['jenis'] ?? '-' }}</span>
                                <span class="text-xs text-gray-400">
                                    @if (($item['jenis'] ?? '') === 'SPP')
                                        {{ \App\Http\Controllers\TagihanPublicController::$namaBulan[$item['bulan']] ?? $item['bulan'] }} {{ $item['tahun'] ?? '' }}
                                    @else
                                        {{ $item['tahun'] ?? '' }}
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                    <div class="flex justify-between items-center gap-4 pt-3 border-t-2 border-gray-200 mt-2">
                        <span class="text-sm font-bold text-gray-800">Total Tagihan</span>
                        <span class="text-base font-bold text-gray-900">
                            Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Jenis Pembayaran</span>
                        <span class="text-sm font-medium text-gray-700 text-right">
                            {{ $tagihan->jenisPembayaran->nama ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Periode</span>
                        <span class="text-sm font-medium text-gray-700 text-right">
                            {{ $namaBulan ?? '' }} {{ $tagihan->tahun }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Nominal Tagihan</span>
                        <span class="text-base font-bold text-gray-900">
                            Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endif

        </div>
        </div>

        {{-- ── Info Pembayaran (jika LUNAS) ────────────────────────────────── --}}
        @if ($tagihan->status === 'lunas' && $tagihan->pembayaran)
            <div class="border-t border-gray-100 mx-6"></div>
            <div class="px-6 py-4">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Rincian Pelunasan</h2>
                <div class="space-y-3">

                    @if (!empty($tagihan->pembayaran->tanggal_bayar))
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Tanggal Bayar</span>
                        <span class="text-sm font-medium text-gray-700 text-right">
                            {{ \Carbon\Carbon::parse($tagihan->pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    @endif

                    @if (!empty($tagihan->pembayaran->jumlah_bayar))
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Jumlah Dibayar</span>
                        <span class="text-base font-bold text-green-700">
                            Rp {{ number_format($tagihan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif

                    @if (!empty($tagihan->pembayaran->keterangan))
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-sm text-gray-500 min-w-fit">Keterangan</span>
                        <span class="text-sm text-gray-700 text-right">
                            {{ $tagihan->pembayaran->keterangan }}
                        </span>
                    </div>
                    @endif

                </div>
            </div>
        @endif

        {{-- ── Instruksi Pembayaran (jika BELUM BAYAR) ─────────────────────── --}}
        @if ($tagihan->status !== 'lunas')
            <div class="border-t border-gray-100 mx-6"></div>
            <div class="px-6 py-4">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-sm text-orange-800 font-medium mb-1">Cara Pembayaran</p>
                    <p class="text-sm text-orange-700">
                        Silakan lakukan pembayaran langsung ke bagian administrasi sekolah
                        dengan menunjukkan halaman ini atau menyebutkan nama siswa.
                    </p>
                </div>
            </div>
        @endif

        {{-- ── Tombol Cetak & Footer ───────────────────────────────────────── --}}
        <div class="border-t border-gray-100 px-6 py-4 no-print">

            {{-- Tombol cetak / simpan PDF --}}
            <button onclick="window.print()"
                    class="w-full flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-xl py-3 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-5H9v5a1 1 0 001 1zm1-9V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v4h6z"/>
                </svg>
                Cetak / Simpan sebagai PDF
            </button>
        </div>

        {{-- Footer kecil --}}
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-3 text-center">
            <p class="text-xs text-gray-400">
                Dokumen ini diterbitkan secara otomatis oleh sistem.
                <br>Hubungi pihak sekolah jika ada pertanyaan.
            </p>
        </div>

    </div>

    {{-- Tanggal akses --}}
    <p class="mt-4 text-xs text-gray-400 no-print">
        Diakses pada {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </p>

</body>
</html>
