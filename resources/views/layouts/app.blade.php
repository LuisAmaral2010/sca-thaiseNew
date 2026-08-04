<!-- resources/views/layouts/app.blade.php -->
<html>
    <head>
        @vite(['resources/sass/app.scss','resources/js/app.js'])
        
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
        <header id="header" class="d-flex align-items-center">
            <div class="container d-flex align-items-center justify-content-between">

            <h1 class="logo"><a href="index.html" class="logo"><img src="/img/logo_sca.jpg" alt=""></a></h1>
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="index.html" class="logo"><img src="assets/img/logo.png" alt=""></a>-->

            <nav id="navbar" class="navbar">
                <ul>
                <li><a class="nav-link scrollto active" href="/">Home</a></li>
                <li><a class="nav-link scrollto" href="/cra">CRA</a></li>
                <li><a class="nav-link scrollto" href="/laboratorio">Laboratório</a></li>
                <li><a class="nav-link scrollto" href="/resptec">Resp Tec</a></li>
                <li><a class="nav-link scrollto" href="/solicitacao">Solicitante</a></li>
                <!-- <li class="dropdown"><a href="/solicitacao"><span>Solicitante</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                    <li><a href="/solicitacao/criar">Criar solicitação</a></li>
                    <li><a href="/solicitacao/salvar">Consultar solicitação</a></li>
                    </ul>
                </li> -->
                <li class="dropdown"><i class="bi bi-person-fill"></i> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <div>
                            <strong><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{auth()->user()->name}}</a></strong>
                            <a href="/logout">Logout</a></li>
                        </div>

                        
                    </ul>
         <!--
                <li class="nav-item dropdown"> -->
                <!-- <ul class="dropdown-menu">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{--auth()->user()->name--}}</a>
           
                    <li class="nav-item">
                      <form action="/logout" method="POST">
                        @csrf
                        <a href="/welcome"
                          class="nav-link"
                          onclick="event.preventDefault();
                          this.closest('form').submit();">
                          Sair
                        </a>
                      </form>
                    </li>
                </ul> -->
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

            </div>
        </header><!-- End Header -->

        <main>
            <div class="container">
                @yield('content')
            </div>

        </main>

        <!-- ======= Footer ======= -->
        <footer id="footer">
            <div class="container py-4">
                <div class="copyright">
                    &copy; Copyright <strong><span></span></strong>. All Rights Reserved
                </div>
                <div class="credits">
                    Designed by Embrapa Agroindústria de Alimentos
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

