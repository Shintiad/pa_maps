<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css') <!-- Tailwind CSS -->
</head>
</head>

<body class="hold-transition sidebar-mini layout-fixed font-josefin">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('images/logo_l.png') }}" alt="L - EndeMap Logo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item ms-3 me-3">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <!-- <li class="nav-item me-3 d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item me-3 d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li> -->
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item me-3 dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="ms-2">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

                <!-- Navbar Search -->
                <!-- <li class="nav-item me-3">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li> -->

                <li class="nav-item me-3">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li> -->
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/dashboard" class="brand-link">
                <img src="{{ asset('images/logo_l.png') }}" alt="L - EndeMap Logo" class="brand-image" style="opacity: .8">
                <span class="brand-text font-semibold hover:text-teal-500">L - EndeMap</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image flex-shrink-0">
                        <img src="{{ asset('images/no-profile.jpg') }}" class="img-circle elevation-3" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="{{ route('profile.edit') }}" class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="/dashboard" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/info-penyakit" class="nav-link">
                                <i class="nav-icon fas fa-square-virus"></i>
                                <p>
                                    Informasi Penyakit
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/tahun" class="nav-link">
                                <i class="nav-icon fas fa-calendar-days"></i>
                                <p>
                                    List Tahun
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/kecamatan" class="nav-link">
                                <i class="nav-icon fas fa-building-circle-arrow-right"></i>
                                <p>
                                    List Kecamatan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/desa" class="nav-link">
                                <i class="nav-icon fas fa-house-chimney-window"></i>
                                <p>
                                    List Desa
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/penduduk" class="nav-link">
                                <i class="nav-icon fas fa-person-arrow-up-from-line"></i>
                                <p>
                                    Jumlah Penduduk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/penyakit" class="nav-link">
                                <i class="nav-icon fas fa-virus"></i>
                                <p>
                                    List Penyakit
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/kasus" class="nav-link">
                                <i class="nav-icon fas fa-viruses"></i>
                                <p>
                                    Kasus Penyakit
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/detail-kasus" class="nav-link">
                                <i class="nav-icon fas fa-viruses"></i>
                                <p>
                                    Detail Kasus Penyakit
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/maps-penduduk" class="nav-link">
                                <i class="nav-icon fas fa-map-location-dot"></i>
                                <p>
                                    Sebaran Penduduk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/maps-penyakit" class="nav-link">
                                <i class="nav-icon fas fa-map-location-dot"></i>
                                <p>
                                    Sebaran Penyakit
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/unduh-data" class="nav-link">
                                <i class="nav-icon fas fa-download"></i>
                                <p>
                                    Unduh Data
                                </p>
                            </a>
                        </li>
                        @if(auth()->check() && auth()->user()->role == 'superadmin')
                        <li class="nav-item">
                            <a href="/admin" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    Admin
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')
                        <li class="nav-item">
                            <a href="/user" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    User
                                </p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="/about" class="nav-link">
                                <i class="nav-icon fas fa-circle-info"></i>
                                <p>
                                    About
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header pt-4 ps-3">
                @yield('header')
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="bg-light text-grey-300 py-3 border-t border-t-grey-300">
            <div class="container mx-auto flex justify-center items-center">
                <div class="flex text-sm text-center items-center">
                    <span class="mr-1">Created By <span class="text-teal-500">Shintia</span></span>
                    <strong class="flex items-center">
                        &copy; 2025
                        <a href="https://wa.me/{{ $about['phone'] }}?text=Assalamu'alaikum" class="text-teal-500 hover:text-teal-700 mx-2 text-lg"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="{{ $about['instagram'] }}" class="text-teal-500 hover:text-teal-700 mx-2 text-lg"><i class="fa-brands fa-instagram"></i></a>
                        <a href="{{ $about['tiktok'] }}" class="text-teal-500 hover:text-teal-700 mx-2 text-lg"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="{{ $about['facebook'] }}" class="text-teal-500 hover:text-teal-700 mx-2 text-lg"><i class="fa-brands fa-facebook"></i></a>
                        <a href="{{ $about['linkedin'] }}" class="text-teal-500 hover:text-teal-700 mx-2 text-lg"><i class="fa-brands fa-linkedin"></i></a>
                    </strong>
                </div>
            </div>
        </footer>

    </div>
    <!-- ./wrapper -->

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
    <script src="{{ asset('lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('lte/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="{{ asset('lte/dist/js/demo.js') }}"></script> -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('lte/dist/js/pages/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const sidebarLinks = document.querySelectorAll('.nav-sidebar .nav-link');

            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                let isActive = false;

                // Check for exact match first
                if (href === currentPath) {
                    isActive = true;
                }
                // Special cases for related pages
                else if (href === '/user' && (
                        currentPath === '/user' ||
                        currentPath.startsWith('/user/') ||
                        currentPath.includes('/user-search')
                    )) {
                    isActive = true;
                }
                // Add special case for Sebaran Penyakit and Detail Maps Penyakit
                else if (href === '/maps-penyakit' && (
                        currentPath === '/maps-penyakit' ||
                        currentPath === '/detail-maps-penyakit'
                    )) {
                    isActive = true;
                }

                // Apply or remove active styles
                if (isActive) {
                    link.classList.add('bg-teal-500', 'text-white', 'hover:!bg-teal-700');
                } else {
                    link.classList.remove('bg-teal-500', 'text-white', 'hover:!bg-teal-700');
                }
            });
        });
    </script>
</body>

</html>