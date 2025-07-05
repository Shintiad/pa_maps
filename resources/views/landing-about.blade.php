<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - L-EndeMap</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    @vite('resources/css/app.css') <!-- Tailwind CSS -->

    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #0ea5e9;
            --accent-color: #8b5cf6;
            --text-color: #1f2937;
            --background-light: #f9fafb;
        }

        .font-open {
            font-family: 'Open Sans', sans-serif;
        }

        .gradient-text {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .feature-card,
        .team-card,
        .timeline-card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }

        .feature-card:hover,
        .team-card:hover,
        .timeline-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .blob-shape {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .hero-btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .hero-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
            z-index: -1;
        }

        .hero-btn:hover::before {
            left: 100%;
        }

        .timeline-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #10b981, #0ea5e9);
            transform: translateX(-50%);
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            width: 16px;
            height: 16px;
            background: #10b981;
            border: 3px solid white;
            border-radius: 50%;
            transform: translateX(-50%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .timeline-line {
                left: 24px;
            }

            .timeline-dot {
                left: 24px;
            }
        }
    </style>
</head>

<body class="flex flex-col min-h-screen font-open">
    <!-- Modern Navbar -->
    <nav class="w-full bg-white/80 backdrop-blur-md shadow-sm fixed top-0 left-0 z-50 py-3 ps-3 pe-3">
        <div class="container mx-auto px-6 flex items-center justify-between">
            <!-- Logo and brand -->
            <a href="{{ route('landing') }}" class="flex items-center space-x-2 py-1 hover:opacity-80 transition">
                <img src="{{ asset('images/logo_l.png') }}" alt="Logo" class="h-10 w-auto">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-teal-300 to-teal-600">
                        L - Endemap
                    </span>
                </div>
            </a>


            <!-- Middle Nav Links - visible on desktop, hidden on mobile -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/#peta" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Peta</a>
                <a href="/#statistik" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Statistik</a>
                <a href="/#fitur" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Fitur</a>
                <a href="/landing-about" class="relative text-teal-600 hover:text-teal-600 transition-colors after:absolute after:w-full after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 after:transition-all">Tentang</a>
            </div>

            <!-- Right Auth Buttons -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-teal-600 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-teal-300 to-teal-600 text-white font-medium py-2 px-12 rounded-full hover:shadow-lg hover:shadow-emerald-200 transition-all">Daftar</a>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu - hidden by default -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t mt-3 py-2 shadow-md">
            <div class="container mx-auto px-6 flex flex-col space-y-3">
                <a href="/#peta" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Peta</a>
                <a href="/#statistik" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Statistik</a>
                <a href="/#fitur" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Fitur</a>
                <a href="/landing-about" class="block py-2 text-teal-600 hover:text-teal-600 transition-colors">Tentang</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-1 pt-16">
        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-b from-white to-teal-50 overflow-hidden ps-6 pe-6">
            <!-- Background decorations -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-100 rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute top-1/3 -left-24 w-80 h-80 bg-teal-100 rounded-full opacity-50 blur-3xl"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center max-w-4xl mx-auto">
                    <span class="inline-block py-1 px-3 rounded-full bg-teal-100 text-teal-800 text-sm font-medium mb-4">Tentang Kami</span>
                    <h1 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                        Mengenal <span class="gradient-text">L - Endemap</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Sistem untuk pemetaan spasial penyakit endemik di Kabupaten Lamongan, Jawa Timur
                    </p>
                </div>
            </div>
        </section>

        <!-- About Overview -->
        <section class="py-16 bg-white ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Apa itu L - Endemap?</h2>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            L - Endemap (Lamongan Endemic Disease Mapping) adalah sistem yang dirancang khusus untuk memetakan dan menganalisis penyebaran penyakit endemik di Kabupaten Lamongan, Jawa Timur. Sistem ini menggabungkan teknologi pemetaan modern dengan data kesehatan untuk memberikan visualisasi yang komprehensif tentang pola penyebaran penyakit.
                        </p>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            Dengan menggunakan teknologi pemetaan spasial, L - Endemap membantu petugas kesehatan, peneliti, dan pengambil kebijakan dalam memahami distribusi geografis penyakit endemik, mengidentifikasi area berisiko tinggi, dan merencanakan strategi pencegahan yang lebih efektif.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-4 py-2 bg-teal-100 text-teal-800 rounded-full text-sm">Visualization</span>
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm">Data Analytics</span>
                            <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm">Health Mapping</span>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="overflow-hidden blob-shape shadow-2xl border-8 border-white animate-float">
                            <img src="{{ asset('images/maps.jpg') }}" alt="Endemic Disease Map" class="w-full h-full object-cover">
                        </div>
                        <!-- Floating stats -->
                        <!-- <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-xl p-4 animate-float">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                                    <i class="fas fa-map-marked-alt text-teal-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Area Terpantau</p>
                                    <p class="text-lg font-bold text-gray-800">27 Kecamatan</p>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="py-16 bg-gray-50 ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Visi & Misi</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Komitmen kami dalam mendukung program kesehatan masyarakat</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Vision -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-eye text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Visi</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Menjadi sistem informasi geografis terdepan dalam pemetaan penyakit endemik yang mendukung program kesehatan masyarakat dan pengambilan keputusan berbasis data di Kabupaten Lamongan.
                        </p>
                    </div>

                    <!-- Mission -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-bullseye text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Misi</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-teal-500 mt-1 mr-2 flex-shrink-0"></i>
                                <span>Menyediakan platform pemetaan spasial yang akurat dan real-time</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-teal-500 mt-1 mr-2 flex-shrink-0"></i>
                                <span>Mendukung analisis epidemiologi untuk pencegahan penyakit</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-teal-500 mt-1 mr-2 flex-shrink-0"></i>
                                <span>Memfasilitasi kolaborasi antar stakeholder kesehatan</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Features -->
        <section class="py-16 bg-white ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Teknologi dan fitur modern untuk membantu analisis penyakit endemik</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-teal-500">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-map-marked-alt text-teal-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Pemetaan Interaktif</h3>
                        <p class="text-gray-600 text-sm">Visualisasi data penyakit dengan peta interaktif yang memungkinkan eksplorasi wilayah.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Analisis Temporal</h3>
                        <p class="text-gray-600 text-sm">Analisis tren penyakit dari waktu ke waktu untuk memahami pola penyebaran dan musiman.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-database text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Database Terintegrasi</h3>
                        <p class="text-gray-600 text-sm">Sistem database yang terintegrasi antara sistem dengan platform visualisasi yang digunakan sehingga data secara realtime terbarui.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Multi-User Access</h3>
                        <p class="text-gray-600 text-sm">Akses multi-pengguna dengan tingkat otorisasi berbeda untuk berbagai stakeholder.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-file-export text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Export & Reporting</h3>
                        <p class="text-gray-600 text-sm">Fitur ekspor data dan pembuatan laporan dalam berbagai format untuk kebutuhan analisis lanjutan.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-mobile-alt text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Mobile Responsive</h3>
                        <p class="text-gray-600 text-sm">Desain responsif yang dapat diakses melalui berbagai perangkat mobile dan desktop.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technology Stack -->
        <section class="py-16 bg-gray-50 ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Teknologi yang Digunakan</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Stack teknologi modern untuk performa dan keandalan optimal</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-laravel text-3xl text-red-500"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Laravel</h4>
                        <p class="text-sm text-gray-600">PHP Framework</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center mx-auto mb-4">
                            <!-- <i class="fas fa-map text-2xl text-green-500"></i> -->
                            <img src="{{ asset('images/logo-metabase.png') }}" alt="Metabase Logo" class="p-4">
                        </div>
                        <h4 class="font-semibold text-gray-900">Metabase</h4>
                        <p class="text-sm text-gray-600">Visualization Platform</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-database text-3xl text-blue-500"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">MySQL</h4>
                        <p class="text-sm text-gray-600">Database</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center mx-auto mb-4">
                            <!-- <i class="fab fa-js-square text-3xl text-yellow-500"></i> -->
                            <img src="{{ asset('images/logo-tailwind.png') }}" alt="Tailwind Logo" class="p-4">
                        </div>
                        <h4 class="font-semibold text-gray-900">Tailwind</h4>
                        <p class="text-sm text-gray-600">Styling Frontend</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Development Timeline -->
        <section class="py-16 bg-gray-50 ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Timeline Pengembangan</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Perjalanan pengembangan L-EndeMap dari konsep hingga implementasi</p>
                </div>

                <div class="relative max-w-4xl mx-auto">
                    <div class="timeline-line"></div>

                    <!-- Timeline Item 1 -->
                    <div class="relative mb-12 md:mb-8">
                        <div class="timeline-dot top-6"></div>
                        <div class="md:flex items-center">
                            <div class="md:w-1/2 md:pr-8 mb-4 md:mb-0">
                                <div class="timeline-card bg-white rounded-xl shadow-lg p-6 md:ml-8">
                                    <span class="inline-block px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium mb-3">Q1 2024</span>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Konsep & Perencanaan</h3>
                                    <p class="text-gray-600 text-sm">Identifikasi kebutuhan dan analisis sistem untuk pemetaan penyakit endemik di Kabupaten Lamongan.</p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-8"></div>
                        </div>
                    </div>

                    <!-- Timeline Item 2 -->
                    <div class="relative mb-12 md:mb-8">
                        <div class="timeline-dot top-6"></div>
                        <div class="md:flex items-center">
                            <div class="md:w-1/2 md:pr-8"></div>
                            <div class="md:w-1/2 md:pl-8">
                                <div class="timeline-card bg-white rounded-xl shadow-lg p-6 md:mr-8">
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mb-3">Q2 2024</span>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pengembangan Sistem</h3>
                                    <p class="text-gray-600 text-sm">Development backend dan frontend dengan Laravel, integrasi Metabase, dan pengembangan fitur visualisasi pemetaan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Item 3 -->
                    <div class="relative mb-12 md:mb-8">
                        <div class="timeline-dot top-6"></div>
                        <div class="md:flex items-center">
                            <div class="md:w-1/2 md:pr-8 mb-4 md:mb-0">
                                <div class="timeline-card bg-white rounded-xl shadow-lg p-6 md:ml-8">
                                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium mb-3">Q3 2025</span>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Testing & Validasi</h3>
                                    <p class="text-gray-600 text-sm">Pengujian sistem, validasi data dengan stakeholder kesehatan, dan perbaikan fitur berdasarkan feedback.</p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-8"></div>
                        </div>
                    </div>

                    <!-- Timeline Item 4 -->
                    <div class="relative">
                        <div class="timeline-dot top-6"></div>
                        <div class="md:flex items-center">
                            <div class="md:w-1/2 md:pr-8"></div>
                            <div class="md:w-1/2 md:pl-8">
                                <div class="timeline-card bg-white rounded-xl shadow-lg p-6 md:mr-8">
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mb-3">Q4 2025</span>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Deployment & Launch</h3>
                                    <p class="text-gray-600 text-sm">Peluncuran sistem ke publik, pelatihan pengguna, dan implementasi di lapangan untuk Kabupaten Lamongan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact & Support -->
        <section class="py-16 bg-white ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Hubungi Kami</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Butuh bantuan? Jangan ragu untuk menghubungi kami</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Contact Info 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-2xl text-teal-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Email</h3>
                        <p class="text-gray-600">shintiadewi789@gmail.com</p>
                        <!-- <p class="text-gray-600">support@lendemap.com</p> -->
                    </div>

                    <!-- Contact Info 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Telepon</h3>
                        <p class="text-gray-600">+62 858 0681 9449</p>
                        <!-- <p class="text-gray-600">+62 812 3456 7890</p> -->
                    </div>

                    <!-- Contact Info 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Alamat</h3>
                        <p class="text-gray-600">Karanggeneng No. 37, Karanggeneng, Lamongan, Jawa Timur</p>
                        <!-- <p class="text-gray-600">Lamongan, Jawa Timur 62211</p> -->
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="mt-12 text-center">
                    <div class="bg-gradient-to-r from-teal-500 to-blue-600 rounded-2xl p-8 text-white">
                        <h3 class="text-2xl font-bold mb-4">Siap Menggunakan L-EndeMap?</h3>
                        <p class="text-teal-100 mb-6 max-w-2xl mx-auto">
                            Bergabunglah dengan kami menggunakan L-EndeMap untuk analisis penyakit endemik yang lebih baik.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/register" class="hero-btn bg-white text-teal-600 font-semibold py-3 px-8 rounded-full hover:bg-gray-50 transition-all inline-flex items-center justify-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Daftar Sekarang
                            </a>
                            <a href="/#peta" class="hero-btn border-2 border-white text-white font-semibold py-3 px-8 rounded-full hover:bg-white hover:text-teal-600 transition-all inline-flex items-center justify-center">
                                <i class="fas fa-map mr-2"></i>
                                Lihat Peta
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-5">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-center items-center text-center">
                <p class="text-gray-400 text-sm">
                    © 2025 L - Endemap. All rights reserved.
                </p>
                <!-- <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-teal-400 text-sm transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-teal-400 text-sm transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-teal-400 text-sm transition-colors">Support</a>
                </div> -->
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white/90');
                navbar.classList.remove('bg-white/80');
            } else {
                navbar.classList.add('bg-white/80');
                navbar.classList.remove('bg-white/90');
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        document.querySelectorAll('.feature-card, .team-card, .timeline-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>

</html>