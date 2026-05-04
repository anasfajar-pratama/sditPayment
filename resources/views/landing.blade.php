<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SDIT Bunga Cempaka — Mendidik Generasi Cerdas & Berakhlak</title>
    <meta name="description" content="SDIT Bunga Cempaka — Sekolah Dasar Islam Terpadu unggulan di Bekasi. Mendidik generasi cerdas, berakhlak mulia, dan berprestasi dengan program tahfidz, pembinaan rohani, dan pembelajaran aktif." />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fredoka', 'sans-serif'],
                        sans: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        primary: { DEFAULT: '#f97316', light: '#fdba74', dark: '#c2410c' },
                        secondary: { DEFAULT: '#0d9488', light: '#5eead4', dark: '#0f766e' },
                        accent: { DEFAULT: '#facc15' },
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; scroll-behavior: smooth; }
        body { font-family: 'Nunito', sans-serif; overflow-x: hidden; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Fredoka', sans-serif; }

        /* Rainbow gradient hero */
        .bg-rainbow {
            background: linear-gradient(135deg, #fce7f3 0%, #fef9c3 30%, #d1fae5 65%, #dbeafe 100%);
        }

        /* Text gradient */
        .text-gradient {
            background: linear-gradient(90deg, #f97316, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Butterfly animation */
        @keyframes flutter {
            0%, 100% { transform: translateY(0) rotate(-5deg) scaleX(1); }
            25% { transform: translateY(-12px) rotate(5deg) scaleX(-1); }
            50% { transform: translateY(-6px) rotate(-3deg) scaleX(1); }
            75% { transform: translateY(-18px) rotate(8deg) scaleX(-1); }
        }
        @keyframes fly-across {
            0% { transform: translateX(-80px) translateY(30px); opacity: 0; }
            5% { opacity: 1; }
            95% { opacity: 1; }
            100% { transform: translateX(110vw) translateY(-60px); opacity: 0; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes bloom {
            from { opacity: 0; transform: scale(0.5) rotate(-20deg); }
            to { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        @keyframes rainbow-slide {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .butterfly { animation: flutter 2.5s ease-in-out infinite; }
        .fly-1 { animation: fly-across 18s linear infinite; }
        .fly-2 { animation: fly-across 24s linear infinite 6s; }
        .fly-3 { animation: fly-across 20s linear infinite 12s; }
        .float { animation: float 5s ease-in-out infinite; }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .scale-on-scroll {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .scale-on-scroll.visible {
            opacity: 1;
            transform: scale(1);
        }

        /* Wave separator */
        .wave-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        /* Navbar scroll effect */
        .navbar-scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* Image hover zoom */
        .img-zoom { overflow: hidden; }
        .img-zoom img { transition: transform 0.5s ease; }
        .img-zoom:hover img { transform: scale(1.07); }

        /* Card hover lift */
        .card-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-lift:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }

        /* Gradient border */
        .gradient-border {
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #f97316, #ec4899, #0d9488) border-box;
            border: 3px solid transparent;
        }
    </style>
</head>
<body class="bg-white text-gray-800">

    {{-- ==================== FLOATING BUTTERFLIES ==================== --}}
    <div class="fixed inset-0 pointer-events-none z-30 overflow-hidden">
        {{-- Butterfly SVG 1 --}}
        <div class="fly-1 absolute top-1/4 text-pink-400" style="opacity:0.5;">
            <svg width="32" height="28" viewBox="0 0 32 28" fill="none">
                <ellipse cx="10" cy="10" rx="9" ry="6" fill="#f472b6" transform="rotate(-20 10 10)" opacity="0.8"/>
                <ellipse cx="10" cy="18" rx="7" ry="4" fill="#fb7185" transform="rotate(20 10 18)" opacity="0.8"/>
                <ellipse cx="22" cy="10" rx="9" ry="6" fill="#f472b6" transform="rotate(20 22 10)" opacity="0.8"/>
                <ellipse cx="22" cy="18" rx="7" ry="4" fill="#fb7185" transform="rotate(-20 22 18)" opacity="0.8"/>
                <line x1="16" y1="4" x2="16" y2="24" stroke="#4b5563" stroke-width="1.5"/>
            </svg>
        </div>
        {{-- Butterfly SVG 2 --}}
        <div class="fly-2 absolute top-1/2 text-yellow-400" style="opacity:0.5;">
            <svg width="28" height="24" viewBox="0 0 32 28" fill="none">
                <ellipse cx="10" cy="10" rx="9" ry="6" fill="#facc15" transform="rotate(-20 10 10)" opacity="0.85"/>
                <ellipse cx="10" cy="18" rx="7" ry="4" fill="#fbbf24" transform="rotate(20 10 18)" opacity="0.85"/>
                <ellipse cx="22" cy="10" rx="9" ry="6" fill="#facc15" transform="rotate(20 22 10)" opacity="0.85"/>
                <ellipse cx="22" cy="18" rx="7" ry="4" fill="#fbbf24" transform="rotate(-20 22 18)" opacity="0.85"/>
                <line x1="16" y1="4" x2="16" y2="24" stroke="#4b5563" stroke-width="1.5"/>
            </svg>
        </div>
        {{-- Butterfly SVG 3 --}}
        <div class="fly-3 absolute top-3/4 text-teal-400" style="opacity:0.45;">
            <svg width="24" height="20" viewBox="0 0 32 28" fill="none">
                <ellipse cx="10" cy="10" rx="9" ry="6" fill="#2dd4bf" transform="rotate(-20 10 10)" opacity="0.8"/>
                <ellipse cx="10" cy="18" rx="7" ry="4" fill="#14b8a6" transform="rotate(20 10 18)" opacity="0.8"/>
                <ellipse cx="22" cy="10" rx="9" ry="6" fill="#2dd4bf" transform="rotate(20 22 10)" opacity="0.8"/>
                <ellipse cx="22" cy="18" rx="7" ry="4" fill="#14b8a6" transform="rotate(-20 22 18)" opacity="0.8"/>
                <line x1="16" y1="4" x2="16" y2="24" stroke="#4b5563" stroke-width="1.5"/>
            </svg>
        </div>
    </div>

    {{-- ==================== NAVBAR ==================== --}}
    <header id="navbar" class="sticky top-0 z-50 w-full bg-white/85 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between" style="height:72px;">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-primary flex items-center justify-center text-white font-display font-bold text-lg shadow-md">BC</div>
                <div>
                    <div class="font-display font-bold text-lg text-primary leading-tight">SDIT Bunga Cempaka</div>
                    <div class="text-xs text-gray-400 font-semibold">Cerdas & Berakhlak</div>
                </div>
            </div>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-7 font-bold text-sm text-gray-600">
                <a href="#tentang" class="hover:text-primary transition-colors">Tentang</a>
                <a href="#program" class="hover:text-primary transition-colors">Program</a>
                <a href="#guru" class="hover:text-primary transition-colors">Guru</a>
                <a href="#fasilitas" class="hover:text-primary transition-colors">Fasilitas</a>
                <a href="#testimoni" class="hover:text-primary transition-colors">Testimoni</a>
                <a href="#kontak" class="hover:text-primary transition-colors">Kontak</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="#ppdb" class="hidden sm:block bg-primary hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-full shadow-md hover:shadow-lg transition-all text-sm">
                    Daftar Sekarang
                </a>
                {{-- Mobile hamburger --}}
                <button id="menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
            <nav class="flex flex-col px-4 py-4 gap-3 font-semibold text-gray-700">
                <a href="#tentang" class="hover:text-primary py-1">Tentang</a>
                <a href="#program" class="hover:text-primary py-1">Program</a>
                <a href="#guru" class="hover:text-primary py-1">Guru</a>
                <a href="#fasilitas" class="hover:text-primary py-1">Fasilitas</a>
                <a href="#testimoni" class="hover:text-primary py-1">Testimoni</a>
                <a href="#kontak" class="hover:text-primary py-1">Kontak</a>
                <a href="#ppdb" class="bg-primary text-white text-center py-2.5 rounded-full font-bold">Daftar Sekarang</a>
            </nav>
        </div>
    </header>

    {{-- ==================== HERO ==================== --}}
    <section class="relative pt-24 pb-36 overflow-hidden bg-rainbow" id="beranda">
        {{-- Floating flowers decoration --}}
        <div class="float absolute bottom-16 left-8 opacity-40" style="animation-delay:0s;">
            <svg width="80" height="80" viewBox="0 0 80 80"><circle cx="40" cy="40" r="12" fill="#0d9488"/><ellipse cx="40" cy="20" rx="9" ry="14" fill="#5eead4"/><ellipse cx="40" cy="60" rx="9" ry="14" fill="#5eead4"/><ellipse cx="20" cy="40" rx="14" ry="9" fill="#5eead4"/><ellipse cx="60" cy="40" rx="14" ry="9" fill="#5eead4"/></svg>
        </div>
        <div class="float absolute top-20 right-12 opacity-40" style="animation-delay:1.5s;">
            <svg width="100" height="100" viewBox="0 0 80 80"><circle cx="40" cy="40" r="12" fill="#f97316"/><ellipse cx="40" cy="20" rx="9" ry="14" fill="#fdba74"/><ellipse cx="40" cy="60" rx="9" ry="14" fill="#fdba74"/><ellipse cx="20" cy="40" rx="14" ry="9" fill="#fdba74"/><ellipse cx="60" cy="40" rx="14" ry="9" fill="#fdba74"/></svg>
        </div>
        <div class="float absolute top-1/2 left-1/4 opacity-25" style="animation-delay:3s;">
            <svg width="60" height="60" viewBox="0 0 80 80"><circle cx="40" cy="40" r="12" fill="#ec4899"/><ellipse cx="40" cy="20" rx="9" ry="14" fill="#f9a8d4"/><ellipse cx="40" cy="60" rx="9" ry="14" fill="#f9a8d4"/><ellipse cx="20" cy="40" rx="14" ry="9" fill="#f9a8d4"/><ellipse cx="60" cy="40" rx="14" ry="9" fill="#f9a8d4"/></svg>
        </div>

        {{-- Rainbow arch --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-2xl opacity-10 pointer-events-none">
            <svg viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 200 Q200 -50 390 200" stroke="#ef4444" stroke-width="12" fill="none"/>
                <path d="M25 200 Q200 -20 375 200" stroke="#f97316" stroke-width="12" fill="none"/>
                <path d="M40 200 Q200 10 360 200" stroke="#facc15" stroke-width="12" fill="none"/>
                <path d="M55 200 Q200 30 345 200" stroke="#22c55e" stroke-width="12" fill="none"/>
                <path d="M70 200 Q200 50 330 200" stroke="#3b82f6" stroke-width="12" fill="none"/>
                <path d="M85 200 Q200 65 315 200" stroke="#8b5cf6" stroke-width="12" fill="none"/>
            </svg>
        </div>

        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/70 backdrop-blur-sm border border-white/60 shadow-sm text-sm font-bold text-primary mb-8">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Akreditasi A — Tahun Ajaran 2025/2026
            </div>

            <h1 class="font-display text-5xl md:text-7xl font-bold text-gray-800 leading-tight mb-6">
                Taman Bermain & Belajar<br>
                <span class="text-gradient">Generasi Berakhlak Mulia</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Mendidik Generasi Cerdas, Berakhlak, dan Berprestasi.<br>
                Lingkungan sekolah yang asri, nyaman, dan Islami untuk buah hati Anda.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#ppdb" class="flex items-center gap-2 bg-primary hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all text-lg">
                    Informasi Pendaftaran
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="#program" class="bg-white/70 hover:bg-white border-2 border-primary/20 text-primary font-bold px-8 py-4 rounded-full transition-all text-lg backdrop-blur-sm">
                    Lihat Program Kami
                </a>
            </div>
        </div>

        {{-- Wave bottom --}}
        <div class="wave-bottom">
            <svg viewBox="0 0 1200 80" preserveAspectRatio="none" class="w-full" style="height:60px;display:block;">
                <path d="M0,40 C300,80 900,0 1200,40 L1200,80 L0,80 Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- ==================== STATS ==================== --}}
    <section class="py-12 bg-white relative z-10" id="tentang">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach([
                    ['icon' => 'users', 'value' => '400+', 'label' => 'Siswa Aktif', 'color' => 'bg-blue-100', 'text' => 'text-blue-500'],
                    ['icon' => 'hat', 'value' => '20+', 'label' => 'Guru Profesional', 'color' => 'bg-orange-100', 'text' => 'text-primary'],
                    ['icon' => 'calendar', 'value' => '18+', 'label' => 'Tahun Berpengalaman', 'color' => 'bg-teal-100', 'text' => 'text-secondary'],
                    ['icon' => 'trophy', 'value' => '50+', 'label' => 'Prestasi Diraih', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-500'],
                ] as $stat)
                <div class="animate-on-scroll bg-white rounded-3xl p-6 shadow border border-gray-100 text-center card-lift">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4 {{ $stat['color'] }}">
                        @if($stat['icon'] === 'users')
                            <svg class="w-8 h-8 {{ $stat['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @elseif($stat['icon'] === 'hat')
                            <svg class="w-8 h-8 {{ $stat['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        @elseif($stat['icon'] === 'calendar')
                            <svg class="w-8 h-8 {{ $stat['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @else
                            <svg class="w-8 h-8 {{ $stat['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        @endif
                    </div>
                    <div class="font-display text-3xl font-bold text-gray-800 mb-1">{{ $stat['value'] }}</div>
                    <div class="text-sm font-semibold text-gray-500">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== ABOUT ==================== --}}
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-14">
                <div class="lg:w-1/2 animate-on-scroll">
                    <div class="relative rounded-3xl img-zoom shadow-2xl overflow-hidden aspect-[4/3]">
                        <img src="{{ asset('images/sekolah/halaman-sekolah.jpeg') }}" alt="Halaman Sekolah SDIT Bunga Cempaka" class="w-full h-full object-cover"/>
                    </div>
                    <div class="absolute -z-10" style="width:200px;height:200px;background:radial-gradient(circle,#fdba7455,transparent);border-radius:50%;bottom:-40px;right:-40px;"></div>
                </div>
                <div class="lg:w-1/2 animate-on-scroll space-y-6">
                    <h2 class="font-display text-4xl md:text-5xl font-bold text-gray-800 leading-tight">
                        Tumbuh Bagaikan <span style="color:#f97316">Bunga Cempaka</span>
                    </h2>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Nama "Bunga Cempaka" mencerminkan harapan kami agar setiap anak mekar menjadi pribadi yang harum akhlaknya dan indah pekertinya. Di SDIT Bunga Cempaka, kami menyediakan taman belajar yang hangat, Islami, dan penuh warna untuk mendukung perkembangan potensi terbaik setiap anak.
                    </p>
                    <ul class="space-y-4">
                        @foreach([
                            'Pendekatan pembelajaran aktif, menyenangkan, dan kondusif',
                            'Pembinaan akhlak mulia dan sholat berjamaah setiap hari',
                            'Lingkungan sekolah yang aman, asri, dan peduli anak',
                            'Program tahfidz Al-Qur\'an dengan pengajar berpengalaman',
                        ] as $item)
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-secondary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-gray-700 font-medium">{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== PROGRAMS & GALLERY ==================== --}}
    <section class="py-24 bg-orange-50/40" id="program">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-14 animate-on-scroll">
                <h2 class="font-display text-4xl md:text-5xl font-bold text-gray-800 mb-4">Program & Kegiatan Unggulan</h2>
                <p class="text-lg text-gray-500">Beragam aktivitas dirancang untuk mengembangkan kecerdasan intelektual, emosional, dan spiritual anak.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach([
                    ['img' => 'kegiatan-sholat.jpeg', 'title' => 'Pembinaan Rohani', 'desc' => 'Pembiasaan sholat Dhuha, Dzuhur berjamaah, dan kegiatan tahfidz harian.', 'tag' => 'Spiritual', 'tag_color' => 'bg-orange-100 text-orange-700'],
                    ['img' => 'siswa-belajar.jpeg', 'title' => 'Pembelajaran Aktif', 'desc' => 'Proses belajar di kelas yang interaktif, kondusif, dan menyenangkan.', 'tag' => 'Akademik', 'tag_color' => 'bg-blue-100 text-blue-700'],
                    ['img' => 'siswi-berhijab.jpeg', 'title' => 'Pembentukan Akhlak', 'desc' => 'Menanamkan adab, kesopanan, dan karakter Islami dalam setiap interaksi.', 'tag' => 'Karakter', 'tag_color' => 'bg-teal-100 text-teal-700'],
                    ['img' => 'guru-foto-bersama.jpeg', 'title' => 'Kegiatan & Peringatan', 'desc' => 'Pramuka, seni tari, peringatan hari besar nasional, dan kegiatan keagamaan.', 'tag' => 'Ekstrakurikuler', 'tag_color' => 'bg-yellow-100 text-yellow-700'],
                    ['img' => 'gedung-sekolah.jpeg', 'title' => 'Fasilitas Sekolah', 'desc' => 'Gedung representatif dengan ruang kelas modern dan lingkungan hijau yang asri.', 'tag' => 'Fasilitas', 'tag_color' => 'bg-pink-100 text-pink-700'],
                    ['img' => 'halaman-sekolah.jpeg', 'title' => 'Lingkungan Nyaman', 'desc' => 'Halaman luas dan rindang untuk bermain, olahraga, dan upacara bendera.', 'tag' => 'Lingkungan', 'tag_color' => 'bg-green-100 text-green-700'],
                ] as $prog)
                <div class="scale-on-scroll rounded-3xl overflow-hidden shadow-sm border border-gray-100 bg-white card-lift">
                    <div class="aspect-[4/3] img-zoom">
                        <img src="{{ asset('images/sekolah/' . $prog['img']) }}" alt="{{ $prog['title'] }}" class="w-full h-full object-cover"/>
                    </div>
                    <div class="p-5">
                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3 {{ $prog['tag_color'] }}">{{ $prog['tag'] }}</span>
                        <h3 class="font-display text-xl font-bold text-gray-800 mb-2">{{ $prog['title'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $prog['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== EXTRA ACTIVITIES ==================== --}}
    <section class="py-20 bg-white" id="fasilitas">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="font-display text-4xl font-bold text-gray-800 mb-3">Ekstrakurikuler & Kegiatan Tambahan</h2>
                <p class="text-gray-500 text-lg">Mendukung bakat dan minat anak di luar jam pelajaran</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                @foreach([
                    ['icon' => '📖', 'name' => 'Tahfidz Qur\'an'],
                    ['icon' => '🏕️', 'name' => 'Pramuka'],
                    ['icon' => '⚽', 'name' => 'Futsal'],
                    ['icon' => '🎨', 'name' => 'Kaligrafi'],
                    ['icon' => '🌍', 'name' => 'Bahasa Arab'],
                    ['icon' => '💃', 'name' => 'Seni Tari'],
                ] as $ekskul)
                <div class="animate-on-scroll text-center p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:border-primary/30 hover:bg-orange-50 transition-all card-lift cursor-default">
                    <div class="text-4xl mb-2">{{ $ekskul['icon'] }}</div>
                    <div class="font-bold text-sm text-gray-700">{{ $ekskul['name'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== TEACHERS ==================== --}}
    <section class="py-24 bg-teal-50/30" id="guru">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-14 animate-on-scroll">
                <h2 class="font-display text-4xl md:text-5xl font-bold text-gray-800 mb-4">Pendidik Berdedikasi</h2>
                <p class="text-lg text-gray-500">Dewan guru kami adalah pendidik profesional yang ramah, sholeh, dan berkomitmen membimbing anak-anak dengan penuh kasih sayang.</p>
            </div>

            {{-- Teacher group photo --}}
            <div class="animate-on-scroll rounded-3xl overflow-hidden shadow-xl mb-10 img-zoom max-w-3xl mx-auto">
                <img src="{{ asset('images/sekolah/guru-foto-bersama.jpeg') }}" alt="Dewan Guru SDIT Bunga Cempaka" class="w-full h-72 object-cover object-top"/>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['name' => 'Ustdzh. Siti Rahayu, S.Pd.I', 'role' => 'Kepala Sekolah', 'desc' => '15 tahun pengalaman, S2 Pendidikan Islam', 'grad' => 'from-orange-100 to-pink-100'],
                    ['name' => 'Ust. Ahmad Fauzi, S.Pd', 'role' => 'Guru Tahfidz', 'desc' => 'Hafidz 30 Juz, pengajar Al-Qur\'an berpengalaman', 'grad' => 'from-teal-100 to-blue-100'],
                    ['name' => 'Ustdzh. Nurlaila Hidayah, S.Pd', 'role' => 'Guru Kelas 1', 'desc' => 'Sabar, kreatif, dan sangat menyenangkan', 'grad' => 'from-yellow-100 to-orange-100'],
                    ['name' => 'Ust. Ridwan Santoso, M.Pd', 'role' => 'Guru Matematika & Sains', 'desc' => 'Lulusan S2, menerapkan metode fun learning', 'grad' => 'from-blue-100 to-teal-100'],
                    ['name' => 'Ustdzh. Fitri Amalia, S.Pd', 'role' => 'Guru B. Indonesia & PAI', 'desc' => 'Terampil, berdedikasi, dan penuh semangat', 'grad' => 'from-pink-100 to-purple-100'],
                    ['name' => 'Ust. Hendra Permana, S.Pd', 'role' => 'Guru Olahraga & Pramuka', 'desc' => 'Energik, motivatif, dan aktif membimbing siswa', 'grad' => 'from-green-100 to-teal-100'],
                ] as $guru)
                <div class="animate-on-scroll bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 card-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br {{ $guru['grad'] }} flex items-center justify-center shrink-0 text-2xl">
                        🌸
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm leading-tight">{{ $guru['name'] }}</h4>
                        <div class="text-xs font-bold text-secondary mb-1 mt-0.5">{{ $guru['role'] }}</div>
                        <p class="text-xs text-gray-500">{{ $guru['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== TESTIMONIALS ==================== --}}
    <section class="py-24 bg-white" id="testimoni">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-14 animate-on-scroll">
                <h2 class="font-display text-4xl md:text-5xl font-bold text-gray-800 mb-4">Apa Kata Wali Murid?</h2>
                <p class="text-lg text-gray-500">Kepercayaan orang tua adalah motivasi terbesar kami</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                @foreach([
                    ['quote' => 'Anak saya jadi lebih rajin sholat dan sudah hafal beberapa juz sejak di SDIT Bunga Cempaka. Gurunya sabar dan perhatian sekali.', 'name' => 'Ibu Dewi Rahmawati', 'role' => 'Wali murid kelas 3'],
                    ['quote' => 'Sekolah yang luar biasa! Bukan hanya pintar secara akademik, tapi akhlak anak saya juga terbentuk dengan sangat baik.', 'name' => 'Bapak Rizky Pratama', 'role' => 'Wali murid kelas 5'],
                    ['quote' => 'Lingkungannya Islami, gurunya ramah, dan program tahfidznya bagus banget. Sangat direkomendasikan untuk orang tua Muslim!', 'name' => 'Ibu Sari Indah', 'role' => 'Wali murid kelas 1'],
                ] as $i => $testi)
                <div class="scale-on-scroll bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative card-lift gradient-border">
                    <div class="font-serif text-6xl text-primary/15 absolute top-4 left-5 select-none leading-none">"</div>
                    <div class="flex gap-1 mb-4">
                        @for($s = 0; $s < 5; $s++)
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 italic leading-relaxed mb-6 relative z-10">"{{ $testi['quote'] }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center font-display font-bold text-white text-lg shadow-sm"
                             style="background: linear-gradient(135deg, #f97316, #ec4899)">
                            {{ mb_substr($testi['name'], 4, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-sm text-gray-800">{{ $testi['name'] }}</div>
                            <div class="text-xs text-gray-400">{{ $testi['role'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== CTA / PPDB ==================== --}}
    <section class="py-24 relative overflow-hidden" id="ppdb">
        <div class="absolute inset-0 bg-rainbow opacity-60"></div>
        <div class="max-w-5xl mx-auto px-4 relative z-10">
            <div class="bg-white rounded-[3rem] p-10 md:p-16 shadow-2xl border border-white/80 text-center relative overflow-hidden">
                <div class="float absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-20" style="background:radial-gradient(circle,#f97316,transparent);"></div>
                <div class="float absolute -bottom-10 -left-10 w-48 h-48 rounded-full opacity-20" style="background:radial-gradient(circle,#0d9488,transparent);animation-delay:2s;"></div>

                <div class="inline-block bg-yellow-100 text-yellow-800 font-bold px-6 py-2 rounded-full text-sm mb-6">
                    🎉 Penerimaan Peserta Didik Baru (PPDB) 2025/2026
                </div>
                <h2 class="font-display text-4xl md:text-5xl font-bold text-gray-800 mb-5">Mari Bergabung Bersama Kami!</h2>
                <p class="text-xl text-gray-600 mb-4 max-w-2xl mx-auto leading-relaxed">
                    Daftarkan putra-putri Anda segera. Kuota terbatas untuk Kelas 1 dan siswa pindahan. Jangan lewatkan kesempatan terbaik ini!
                </p>
                <p class="text-sm text-gray-400 mb-10">⏰ Pendaftaran dibuka: Januari — Juni 2025</p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="#kontak" class="w-full sm:w-auto bg-primary hover:bg-orange-600 text-white font-bold px-10 py-4 rounded-full shadow-lg hover:shadow-xl transition-all text-lg">
                        Daftar Sekarang
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer"
                       class="w-full sm:w-auto flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 hover:bg-green-50 font-bold px-10 py-4 rounded-full transition-all text-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== CONTACT & FOOTER ==================== --}}
    <footer class="bg-gray-900 text-white pt-16 pb-8" id="kontak">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 bg-primary rounded-full flex items-center justify-center font-display font-bold text-white text-lg">BC</div>
                        <div class="font-display font-bold text-lg">SDIT Bunga Cempaka</div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">Mendidik Generasi Cerdas, Berakhlak, dan Berprestasi sejak tahun 2005.</p>
                    <div class="flex gap-3">
                        <a href="https://instagram.com/sditbungacempaka" target="_blank" class="w-9 h-9 rounded-full bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://facebook.com/sditbungacempaka" target="_blank" class="w-9 h-9 rounded-full bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://youtube.com/@sditbungacempaka" target="_blank" class="w-9 h-9 rounded-full bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="font-display font-bold text-lg mb-5 pb-2 border-b border-white/15">Kontak Kami</h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li class="flex gap-3">
                            <svg class="w-5 h-5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Jl. Bunga Cempaka No. 12, Kec. Cikampek, Karawang, Jawa Barat 17510</span>
                        </li>
                        <li class="flex gap-3 items-center">
                            <svg class="w-5 h-5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:+6281234567890" class="hover:text-white">+62 813-1898-8499</a>
                        </li>
                        <li class="flex gap-3 items-center">
                            <svg class="w-5 h-5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:info@sditbungacempaka.sch.id" class="hover:text-white">info@sditbungacempaka.sch.id</a>
                        </li>
                        <li class="flex gap-3 items-center">
                            <svg class="w-5 h-5 shrink-0 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <a href="https://wa.me/6281318988499" target="_blank" class="hover:text-white text-green-400 font-semibold">Chat WhatsApp</a>
                        </li>
                    </ul>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-display font-bold text-lg mb-5 pb-2 border-b border-white/15">Menu Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach(['Tentang Sekolah' => '#tentang', 'Program Unggulan' => '#program', 'Dewan Guru' => '#guru', 'Fasilitas' => '#fasilitas', 'Testimoni' => '#testimoni', 'Info Pendaftaran' => '#ppdb'] as $label => $href)
                        <li><a href="{{ $href }}" class="text-gray-400 hover:text-white transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Location placeholder --}}
                <div>
                    <h4 class="font-display font-bold text-lg mb-5 pb-2 border-b border-white/15">Lokasi</h4>
                    <a href="https://maps.google.com/?q=SDIT+Bunga+Cempaka+Bekasi" target="_blank"
                       class="block w-full h-36 bg-white/10 rounded-2xl flex items-center justify-center text-white/50 hover:bg-white/15 transition-colors group">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-sm font-semibold">Lihat di Google Maps</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="pt-8 border-t border-white/10 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SDIT Bunga Cempaka. Hak Cipta Dilindungi. 🌸</p>
            </div>
        </div>
    </footer>

    {{-- ==================== FLOATING WHATSAPP ==================== --}}
    <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center text-white shadow-2xl hover:scale-110 transition-all"
       title="Chat WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    {{-- ==================== JAVASCRIPT ==================== --}}
    <script>
        // Mobile menu toggle
        document.getElementById('menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        // Close mobile menu on link click
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => document.getElementById('mobile-menu').classList.add('hidden'));
        });

        // Navbar scroll shadow
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Scroll animations (IntersectionObserver)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), entry.target.dataset.delay || 0);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll, .scale-on-scroll').forEach((el, i) => {
            el.dataset.delay = (i % 4) * 80;
            observer.observe(el);
        });
    </script>
</body>
</html>
