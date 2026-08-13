<!-- resources/views/layouts/app.blade.php -->
<html>
    <head>
        @vite(['resources/sass/app.scss','resources/css/legacy-shell.css','resources/js/app.js'])

        <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

        <!-- Vendor CSS Files -->
        <link href="/assets/vendor/aos/aos.css" rel="stylesheet">
        <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
        <link href="/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
        <link href="/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

        <!-- Template Main CSS File -->
        <link href="/assets/css/style.css" rel="stylesheet">
        <title>@yield('title', 'Meu Site')</title>
    </head>
    <body>
        <!-- ======= Header ======= -->
        <header class="sca-navbar">
            <div class="sca-navbar__inner">
                <a href="{{ route('dashboard.index') }}" class="sca-navbar__brand">
                    <img src="/img/logo_sca.jpg" alt="" class="sca-navbar__logo">
                    <span>SCA</span>
                </a>

                <button class="sca-navbar__toggle" type="button" data-bs-toggle="collapse" data-bs-target="#scaNavCollapse" aria-controls="scaNavCollapse" aria-expanded="false" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>

                <div class="collapse sca-navbar__collapse" id="scaNavCollapse">
                    <nav class="sca-navbar__nav">
                        <a class="sca-navbar__link {{ request()->is('dashboard*') ? 'is-active' : '' }}" href="{{ route('dashboard.index') }}">Dashboard</a>
                        <a class="sca-navbar__link {{ request()->is('cra*') ? 'is-active' : '' }}" href="/cra">CRA</a>
                        <a class="sca-navbar__link {{ request()->is('laboratorio*') ? 'is-active' : '' }}" href="/laboratorio">Laboratório</a>
                        <a class="sca-navbar__link {{ request()->is('resptec*') ? 'is-active' : '' }}" href="/resptec">Resp Tec</a>
                        <a class="sca-navbar__link {{ request()->is('solicitacao*') || request()->is('solicitante*') ? 'is-active' : '' }}" href="/solicitacao">Solicitante</a>
                    </nav>

                    @auth
                        <div class="sca-navbar__user dropdown">
                            <button class="sca-navbar__user-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="sca-avatar"><i class="bi bi-person-fill"></i></span>
                                <span class="sca-navbar__user-name">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down sca-navbar__chevron"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end sca-dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/logout">
                                        <i class="bi bi-box-arrow-right"></i> Sair
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="sca-navbar__link" href="{{ route('login') }}">Entrar</a>
                    @endauth
                </div>
            </div>
        </header><!-- End Header -->

        <main>
            <div class="container">
                @yield('content')
            </div>

        </main>

        <!-- ======= Footer ======= -->
        <footer class="sca-footer">
            <div class="sca-footer__inner">
                <span>&copy; {{ date('Y') }} SCA. Todos os direitos reservados.</span>
                <span>Desenvolvido pela Embrapa Agroindústria de Alimentos</span>
            </div>
        </footer><!-- End Footer -->

        <div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/assets/vendor/aos/aos.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="/assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="/assets/vendor/waypoints/noframework.waypoints.js"></script>
<script src="/assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="/assets/js/main.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    </body>
</html>

