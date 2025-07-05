<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L - EndeMap</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css" type="text/css">

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

        .map-container {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .feature-card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }

        .feature-card:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .disease-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .statistic-card,
        .definition-card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }

        .statistic-card,
        .definition-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
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

        .blob-shape {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }

        .gradient-text {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
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
    </style>
</head>

<body class="flex flex-col min-h-screen font-open">
    <!-- Modern Navbar -->
    <nav class="w-full bg-white/80 backdrop-blur-md shadow-sm fixed top-0 left-0 z-50 py-3 ps-3 pe-3">
        <div class="container mx-auto px-6 flex items-center justify-between">
            <!-- Logo and brand -->
            <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo_l.png') }}" alt="Logo" class="h-10 w-auto">
                <div class="flex flex-col">
                    <span class="text-lg font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-teal-300 to-teal-600">
                        {{ $about['brand'] }}
                    </span>
                </div>
            </a>

            <!-- Middle Nav Links - visible on desktop, hidden on mobile -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#peta" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Peta</a>
                <a href="#statistik" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Statistik</a>
                <a href="#fitur" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Fitur</a>
                <a href="/landing-about" class="relative text-gray-700 hover:text-teal-600 transition-colors after:absolute after:w-0 after:h-0.5 after:bg-teal-600 after:bottom-[-4px] after:left-0 hover:after:w-full after:transition-all">Tentang</a>
            </div>

            <!-- Right Auth Buttons -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-teal-600 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-teal-300 to-teal-600 text-white font-medium py-2 px-5 rounded-full hover:shadow-lg hover:shadow-emerald-200 transition-all">Daftar</a>

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
                <a href="#peta" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Peta</a>
                <a href="#statistik" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Statistik</a>
                <a href="#fitur" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors border-b border-gray-100">Fitur</a>
                <a href="/landing-about" class="block py-2 text-gray-700 hover:text-teal-600 transition-colors">Tentang</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-1 pt-12">
        <!-- Modern Hero Section -->
        <section class="relative min-h-screen bg-gradient-to-b from-white to-teal-50 overflow-hidden ps-6 pe-6">
            <!-- Background decorations -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-100 rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute top-1/3 -left-24 w-80 h-80 bg-teal-100 rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-blue-100 rounded-full opacity-40 blur-3xl"></div>

            <div class="container mx-auto px-6 py-20 relative z-10">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <!-- Left content - Text and CTA -->
                    <div class="w-full md:w-1/2 space-y-8 text-center md:text-left">
                        <div>
                            <span class="inline-block py-1 px-3 rounded-full bg-teal-100 text-teal-800 text-sm font-medium mb-4">Kabupaten Lamongan, Jawa Timur</span>
                            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight">
                                Pemetaan <span class="gradient-text">Spasial</span> Penyakit Endemik
                            </h1>
                            <p class="mt-6 text-gray-600 leading-relaxed">
                                {{ $about['description'] }}
                            </p>
                        </div>

                        <div class="flex flex-wrap justify-center md:justify-start gap-4">
                            <a href="{{ route('login') }}" class="hero-btn bg-gradient-to-r from-teal-300 to-teal-600 text-white font-medium py-3 px-8 rounded-full hover:shadow-lg hover:shadow-emerald-200 transition-all">
                                Mulai Eksplorasi
                            </a>
                            <a href="#peta" class="hero-btn bg-white text-gray-800 font-medium py-3 px-8 rounded-full shadow-sm border border-gray-200 hover:shadow-md transition-all">
                                Lihat Peta <i class="fas fa-arrow-down ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Right content - Image -->
                    <div class="w-full md:w-1/2 mt-12 md:mt-0 relative">
                        <div class="relative p-6">
                            <!-- Main image with blob shape mask -->
                            <div class="overflow-hidden blob-shape shadow-2xl border-8 border-white animate-float">
                                <img src="{{ asset('images/maps.jpg') }}" alt="Endemic Disease Map" class="w-full h-full object-cover">
                            </div>

                            <!-- Floating element - Stats card -->
                            <div class="absolute -top-6 -left-6 bg-white rounded-xl shadow-xl p-4 animate-float" style="animation-delay: 0.5s;">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-virus text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Kasus Aktif Tahun {{ $currentYear->tahun }}</p>
                                        <p class="text-lg font-bold text-gray-800">{{ $totalKasusAktif }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating element - Location card -->
                            <div class="absolute -bottom-6 -right-6 bg-white rounded-xl shadow-xl p-4 animate-float" style="animation-delay: 1s;">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Area Terpantau</p>
                                        <p class="text-lg font-bold text-gray-800">{{ $totalKecamatan }} Kecamatan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trust indicators / Partners -->
                <!-- <div class="mt-20">
                    <p class="text-sm text-gray-500 text-center mb-6">Didukung dan bekerja sama dengan</p>
                    <div class="flex flex-wrap justify-center items-center gap-8 opacity-70">
                        <img src="{{ asset('images/partner-logo.png') }}" alt="Partner" class="h-8 grayscale hover:grayscale-0 transition-all">
                        <img src="{{ asset('images/partner-logo.png') }}" alt="Partner" class="h-8 grayscale hover:grayscale-0 transition-all">
                        <img src="{{ asset('images/partner-logo.png') }}" alt="Partner" class="h-8 grayscale hover:grayscale-0 transition-all">
                        <img src="{{ asset('images/partner-logo.png') }}" alt="Partner" class="h-8 grayscale hover:grayscale-0 transition-all">
                    </div>
                </div> -->
            </div>

            <!-- Wave separator -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#f9fafb" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,128C672,107,768,85,864,90.7C960,96,1056,128,1152,133.3C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </section>

        <!-- Definition of Endemic Disease -->
        <section id="definition" class="py-16 bg-gradient-to-b from-gray-50 via-teal-50 via-50% to-gray-50 bg-gray-50 ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <div class="definition-card bg-white rounded-xl shadow-lg p-6 border-r-4 border-teal-500 p-10">
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 md:mb-4">Penyakit Endemik</h3>
                        <p class="text-gray-500 mb-4">{{ $about['definition'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Preview Section -->
        <section id="peta" class="py-10 md:py-16 bg-gray-50 px-4 md:px-6">
            <div class="container mx-auto px-2 md:px-6">
                <div class="text-center mb-6 md:mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 md:mb-4">Peta Penyakit Endemik Lamongan Tahun {{ $currentYear->tahun ?? '' }}</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Visualisasi data penyakit endemik di Kabupaten Lamongan dengan pemetaan spasial menggunakan teknologi OpenMap</p>
                </div>

                <div class="flex flex-col h-[75vh] md:h-[80vh] ps-6 pe-6" id="map">
                    <div class="flex flex-wrap justify-center items-center -mt-2 -ml-2 mb-2 md:mb-4"
                        id="disease-buttons"
                        role="tablist"
                        data-year-id="{{ $currentYearId }}"
                        data-initial-disease="{{ $penyakit->first()->id ?? '' }}">
                        @foreach($penyakit as $index => $penyakitList)
                        <a href="#"
                            class="btn text-white md:text-sm p-1.5 md:p-2 mt-2 ml-2 transition-colors duration-200 ease-in-out @if ($index === 0) bg-teal-700 @else bg-teal-500 @endif hover:bg-teal-600"
                            data-disease-id="{{ $penyakitList->id }}"
                            role="tab"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="map-iframe"
                            id="disease-tab-{{ $penyakitList->id }}">
                            {{ $penyakitList->nama_penyakit }}
                        </a>
                        @endforeach
                    </div>

                    <div class="loading-indicator text-center py-2 md:py-4" role="status" aria-live="polite">
                        <div class="spinner-border text-teal-500">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-1 md:mt-2 text-sm">Memuat data peta...</p>
                    </div>

                    <iframe id="map-iframe"
                        src=""
                        frameborder="0"
                        allowtransparency
                        allowfullscreen
                        mozallowfullscreen="true"
                        webkitallowfullscreen="true"
                        class="w-full h-full flex-1 hidden"
                        title="Peta sebaran penyakit"></iframe>

                    <div class="flex flex-col items-center justify-center mt-2">
                        <p id="no-map-message" class="text-center text-gray-500 pb-2 pt-4 hidden" aria-live="polite"></p>
                        <a id="create-map-btn" href="#" class="btn btn-info mt-2 text-white text-center hidden w-auto px-3 py-1.5 md:px-4 md:py-2 rounded"
                            data-user-role="{{ auth()->check() ? auth()->user()->role : 0 }}">Buat Peta Sebaran</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section id="statistik" class="py-16 bg-white ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold font-open text-gray-800 mb-4">Statistik Penyakit</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">
                        Data statistik kasus penyakit endemik di Kabupaten Lamongan
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                    $hasMetabaseLink = false;
                    @endphp

                    @foreach ($trend_penyakit as $penyakit)
                    @if($penyakit->link_metabase)
                    @php $hasMetabaseLink = true; @endphp
                    <div class="statistic-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-teal-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $penyakit->nama_penyakit }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Tren kasus terkonfirmasi</p>
                        <iframe
                            src="{{ $penyakit->link_metabase }}"
                            title="Tren {{ $penyakit->nama_penyakit }}"
                            frameborder="0"
                            allowtransparency
                            style="width: 100%; height: 400px;"
                            class="rounded-lg border"></iframe>
                    </div>
                    @endif
                    @endforeach

                    @if(!$hasMetabaseLink)
                    <div class="col-span-1 md:col-span-2">
                        <div class="bg-red-100 text-center rounded-xl shadow p-6">
                            <p class="text-red-700 font-semibold">Belum ada tren penyakit yang dibuat.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="fitur" class="py-16 bg-gray-50 ps-6 pe-6">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold font-open text-gray-800 mb-4">Fitur Utama</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">{{ $about['brand'] }} menyediakan berbagai fitur untuk membantu penelitian dan pemantauan penyakit endemik di Kabupaten Lamongan</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="feature-card bg-white rounded-xl shadow-md p-6">
                        <div class="w-14 h-14 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-map-marked-alt text-2xl text-teal-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Pemetaan Spasial</h3>
                        <p class="text-gray-600">Visualisasi sebaran penyakit endemik dengan peta interaktif dan filter berdasarkan jenis penyakit dan periode waktu.</p>
                    </div>

                    <div class="feature-card bg-white rounded-xl shadow-md p-6">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Analisis Temporal</h3>
                        <p class="text-gray-600">Analisis pola dan tren penyakit endemik berdasarkan waktu dengan visualisasi grafik dan statistik interaktif.</p>
                    </div>

                    <div class="feature-card bg-white rounded-xl shadow-md p-6">
                        <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-file-download text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Laporan & Unduhan</h3>
                        <p class="text-gray-600">Akses dan unduh laporan penyakit endemik dalam format PDF, Excel, atau CSV untuk kebutuhan penelitian dan pelaporan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-teal-600 ps-6 pe-6">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold font-open text-white mb-6">Mulai Gunakan {{ $about['brand'] }} Sekarang</h2>
                <p class="text-white mb-8 max-w-2xl mx-auto">Daftar dan akses informasi lengkap tentang pemetaan spasial penyakit endemik di Kabupaten Lamongan</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="bg-gray-100 text-teal-600 font-semibold py-3 px-8 rounded-xl hover:bg-teal-600 hover:text-gray-100 hover:border-2 hover:border-white transition-colors">Daftar</a>
                    <a href="{{ route('login') }}" class="bg-teal-600 text-gray-100 border-2 border-white font-semibold py-3 px-8 rounded-xl hover:bg-gray-100 hover:text-teal-600 transition-colors">Masuk</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-4 ps-6 pe-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Brand Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">{{ $about['brand'] }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ $about['title'] }}</p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <!-- Email -->
                        <li>
                            <a href="mailto:{{ $about['email'] }}" class="flex items-center hover:text-teal-400">
                                <i class="fas fa-envelope text-base mr-3 w-5 text-gray-400"></i>
                                <span>{{ $about['email'] }}</span>
                            </a>
                        </li>

                        <!-- WhatsApp -->
                        <li>
                            <a href="https://wa.me/{{ $about['phone'] }}?text=Assalamu'alaikum" class="flex items-center hover:text-teal-400">
                                <i class="fa-brands fa-whatsapp text-base mr-3 w-5 text-gray-400"></i>
                                <span>{{ $about['phone'] }}</span>
                            </a>
                        </li>

                        <!-- Alamat -->
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-base mr-3 w-5 pt-1 text-gray-400"></i>
                            <span class="leading-snug">{{ $about['address'] }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sosial Media</h3>
                    <div class="flex space-x-4 text-gray-400 text-lg">
                        <a href="{{ $about['instagram'] }}" class="hover:text-teal-400"><i class="fa-brands fa-instagram"></i></a>
                        <a href="{{ $about['tiktok'] }}" class="hover:text-teal-400"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="{{ $about['linkedin'] }}" class="hover:text-teal-400"><i class="fa-brands fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <!-- Bottom Line -->
            <div class="border-t border-gray-700 mt-6 pt-3 text-center">
                <!-- <p class="text-sm text-gray-400">&copy; 2025 L-EndeMap | Dinas Kesehatan Kabupaten Lamongan</p> -->
                <p class="text-sm text-gray-400">&copy; 2025 {{ $about['brand'] }} | Shintia</p>
            </div>
        </div>
    </footer>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements to improve performance
            const elements = {
                mobileMenuButton: document.getElementById('mobile-menu-button'),
                mobileMenu: document.getElementById('mobile-menu'),
                iframe: document.getElementById('map-iframe'),
                noMapMessage: document.getElementById('no-map-message'),
                createMapBtn: document.getElementById('create-map-btn'),
                loadingIndicator: document.querySelector('.loading-indicator'),
                diseaseButtons: document.getElementById('disease-buttons')
            };

            // Get initial values from data attributes
            const currentYearId = elements.diseaseButtons.getAttribute('data-year-id') || '{{ $currentYearId }}';
            let currentDiseaseId = elements.diseaseButtons.getAttribute('data-initial-disease') || '{{ $penyakit->first()->id }}';
            let diseaseAvailability = {};

            // Mobile menu toggle
            elements.mobileMenuButton.addEventListener('click', function() {
                elements.mobileMenu.classList.toggle('hidden');
                const isExpanded = elements.mobileMenu.classList.contains('hidden') ? 'false' : 'true';
                elements.mobileMenuButton.setAttribute('aria-expanded', isExpanded);
            });

            // Use event delegation for mobile menu items
            elements.mobileMenu.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    elements.mobileMenu.classList.add('hidden');
                    elements.mobileMenuButton.setAttribute('aria-expanded', 'false');
                }
            });

            // Helper functions
            function updateElementState(element, isEnabled) {
                if (isEnabled) {
                    element.classList.remove('disabled');
                    element.style.opacity = '1';
                    element.style.cursor = 'pointer';
                    element.style.pointerEvents = 'auto';
                    element.setAttribute('aria-disabled', 'false');
                } else {
                    element.classList.add('disabled');
                    element.style.opacity = '0.5';
                    element.style.cursor = 'not-allowed';
                    element.style.pointerEvents = 'none';
                    element.setAttribute('aria-disabled', 'true');
                }
            }

            function updateDiseaseButtonsState() {
                // Get all disease buttons and update their state
                const buttons = elements.diseaseButtons.querySelectorAll('.btn');

                buttons.forEach(button => {
                    const diseaseId = button.getAttribute('data-disease-id');
                    const availability = diseaseAvailability[diseaseId] || {
                        has_data: false,
                        is_complete: false
                    };

                    const isEnabled = availability.has_data && availability.is_complete;
                    updateElementState(button, isEnabled);

                    // Update button appearance
                    button.classList.remove('bg-teal-500', 'bg-teal-700', 'bg-gray-400', 'hover:bg-teal-600');

                    if (!isEnabled) {
                        button.classList.add('bg-gray-400');
                        button.setAttribute('title', 'Data tidak lengkap untuk penyakit ini');
                    } else {
                        button.classList.add(diseaseId === currentDiseaseId ? 'bg-teal-700' : 'bg-teal-500');
                        button.classList.add('hover:bg-teal-600');
                        button.setAttribute('title', 'Lihat peta untuk penyakit ini');
                    }
                });
            }

            function showError(message) {
                elements.loadingIndicator.classList.add('hidden');
                elements.iframe.classList.add('hidden');
                elements.noMapMessage.textContent = message || 'Terjadi kesalahan saat memuat data.';
                elements.noMapMessage.classList.remove('hidden');

                // Only show create map button for admin users (without regenerate-disease link)
                const userRole = elements.createMapBtn.getAttribute('data-user-role');
                if (userRole == 1) {
                    elements.createMapBtn.classList.remove('hidden');
                }
            }

            function updateMapDisplay() {
                // Hide all elements and show loading
                elements.iframe.classList.add('hidden');
                elements.noMapMessage.classList.add('hidden');
                elements.createMapBtn.classList.add('hidden');
                elements.loadingIndicator.classList.remove('hidden');

                console.log(`Fetching map data for year: ${currentYearId}, disease: ${currentDiseaseId}`);

                // Fetch map data
                fetch(`${window.location.origin}/get-map-link?tahun_id=${currentYearId}&penyakit_id=${currentDiseaseId}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response body:', text);
                                throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Received data:', data);
                        elements.loadingIndicator.classList.add('hidden');

                        // Update disease availability and button states
                        diseaseAvailability = data.disease_availability || {};
                        updateDiseaseButtonsState();

                        // Handle different statuses
                        switch (data.status) {
                            case 'has_map':
                                elements.iframe.src = data.link_metabase;
                                elements.iframe.classList.remove('hidden');
                                break;
                            case 'no_data':
                                showError('Belum ada data untuk penyakit ini pada tahun terbaru.');
                                break;
                            case 'incomplete_data':
                                showError('Data belum lengkap untuk semua kecamatan pada tahun terbaru untuk penyakit ini.');
                                break;
                            case 'no_map':
                                showError('Data telah lengkap namun peta belum tersedia.');
                                break;
                            default:
                                showError('Status peta tidak dikenali.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching the map link:', error);
                        showError('Terjadi kesalahan saat memuat data: ' + error.message);
                    });
            }

            // Use event delegation for disease buttons
            elements.diseaseButtons.addEventListener('click', function(e) {
                const button = e.target.closest('.btn');
                if (button && !button.classList.contains('disabled')) {
                    e.preventDefault();
                    currentDiseaseId = button.getAttribute('data-disease-id');

                    // Update selected button appearance
                    elements.diseaseButtons.querySelectorAll('.btn').forEach(btn => {
                        btn.classList.remove('bg-teal-700');
                        btn.classList.add('bg-teal-500');
                        btn.setAttribute('aria-selected', 'false');
                    });

                    button.classList.remove('bg-teal-500');
                    button.classList.add('bg-teal-700');
                    button.setAttribute('aria-selected', 'true');

                    updateMapDisplay();
                }
            });

            // Initialize
            updateMapDisplay();
        });
    </script>

    <!-- jQuery -->
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('lte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('lte/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('lte/plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('lte/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('lte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/daterangepicker/daterangepicker