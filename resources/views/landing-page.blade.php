<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silobur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family='Red Hat Text':wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/landing-page.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('') }}assets/images/login-images/logo-silobur.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



    <style>
        html {
            scroll-behavior: smooth;
        }

        #tps {
            scroll-margin-top: 100px;
        }

        #jadwal h2,
        #jadwal h6 {
            font-family: 'Red Hat Text', sans-serif;
            font-weight: 700;
        }

        #jadwal p,
        #jadwal small {
            font-family: 'Red Hat Text', sans-serif;
        }

        #jadwal .card {
            border-radius: 12px;
            border: none;
            transition: transform 0.3s ease;
        }

        #jadwal .card:hover {
            transform: translateY(-5px);
        }

        #jadwal::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }

        .auth-buttons {
            display: none !important;
        }

        /* Sidebar Styles */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1060;
            /* lebih tinggi dari header */
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: -60%;
            width: 60%;
            height: 100%;
            background-color: #ffffff;
            z-index: 1061;
            /* lebih tinggi lagi dari overlay */
            transition: right 0.3s ease-in-out;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .stat-card:hover {
            transform: scale(1.03);
            transition: transform 0.3s ease;
        }


        .sidebar.open {
            right: 0;
        }

        .sidebar .close-btn {
            font-size: 24px;
            font-weight: bold;
            align-self: flex-end;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .sidebar .nav-link {
            padding: 10px 0;
            font-weight: 600;
            color: #333;
            text-decoration: none;
        }

        .sidebar .btn {
            margin-top: 10px;
        }

        @media (min-width: 992px) {
            .auth-buttons {
                display: flex !important;
            }

            .sidebar-overlay,
            .sidebar {
                display: none !important;
            }
        }
    </style>

</head>

<body>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebarMenu">
        <div class="close-btn" onclick="closeSidebar()">&times;</div>
        <a class="nav-link" href="#">Home</a>
        <a class="nav-link" href="#tentang-kami">Tentang Kami</a>
        <a class="nav-link" href="#Fitur">Fitur</a>
        {{-- <a class="nav-link" href="#tps">Galeri</a> --}}
        <a class="nav-link" href="#jadwal">Jadwal</a>
        <a class="nav-link" href="#berita">Berita</a>
        <hr>
        <a href="/login" class="btn btn-outline-primary w-100 fw-semibold">Sign In</a>
        <a href="/register" class="btn btn-primary w-100 fw-semibold mt-2">Sign Up</a>
    </div>

    <!-- Header/Navbar -->
    <header class="position-relative" style="z-index: 1051;">
        <div class="d-flex justify-content-center">
            <nav class="navbar navbar-expand-lg bg-white shadow rounded-pill px-4 py-2 mt-3 position-absolute"
                style="backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); max-width: 1100px; width: 100%;">
                <div class="container-fluid px-0">
                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center me-4" href="/">
                        <img src="{{ asset('assets/images/Group 1.png') }}" alt="Logo Silobur" style="height: 30px;">
                    </a>

                    <!-- Toggler (custom) -->
                    <button class="navbar-toggler" type="button" onclick="openSidebar()">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Desktop menu -->
                    <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex" id="navbarNav">
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#">Home</a></li>
                            <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#tentang-kami">Tentang
                                    Kami</a></li>
                            <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#Fitur">Fitur</a></li>
                            {{-- <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#tps">Galeri</a></li> --}}
                            <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#jadwal">Jadwal</a></li>
                            <li class="nav-item mx-2"><a class="nav-link fw-semibold" href="#berita">Berita</a></li>
                        </ul>
                    </div>

                    <!-- Sign In/Up for desktop only -->
                    <div class="auth-buttons d-none d-lg-flex align-items-center ms-auto">
                        <a href="/login" class="btn btn-outline-primary me-2 fw-semibold">Sign In</a>
                        <a href="/register" class="btn btn-primary fw-semibold">Sign Up</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>


    <!-- JS -->
    <script>
        function openSidebar() {
            document.getElementById("sidebarMenu").classList.add("open");
            document.getElementById("sidebarOverlay").style.display = "block";
        }

        function closeSidebar() {
            document.getElementById("sidebarMenu").classList.remove("open");
            document.getElementById("sidebarOverlay").style.display = "none";
        }
        // Tutup sidebar saat klik salah satu link di dalamnya (khusus mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('#sidebarMenu .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Tunggu sedikit agar animasi klik terasa, lalu tutup
                    setTimeout(closeSidebar, 100);
                });
            });
        });
    </script>

    <!-- Hero Section -->
    {{-- <section class="hero position-relative pt-5" style="background: linear-gradient(135deg, #af58ba, #6c72cb); color: white; overflow: hidden; min-height: 600px;"> --}}
    <section class="hero position-relative pt-5"
        style="background: #3846C3; color: white; overflow: hidden; min-height: 600px;">
        <div class="position-absolute start-0 top-50 translate-middle-y opacity-7" style="z-index: 1;">
            <img src="{{ asset('assets/images/Group.png') }}" alt="Siluet Logo" class="img-fluid"
                style="max-width: 300px;">
        </div>
        <div class="container py-5 pt-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-2 fw-light text-uppercase">Silobur</h6>
                    <h1 class="fw-bold display-5">Lomba Burung <br /> Jadi Mudah & Praktis</h1>
                    <p class="mb-4">Penilaian jujur, transparan, <br> terbuka dan cepat</p>
                    <a href="/register" class="btn btn-outline-light px-4">Daftar Sekarang</a>
                </div>
                <div class="col-md-6 text-end">
                    <img src="{{ asset('assets/images/kacer.png') }}" alt="kacer" class="img-fluid"
                        style="max-width: 400px;">
                </div>
            </div>
        </div>

        <!-- Wave bottom border -->
        <div class="position-absolute bottom-0 start-0 w-100"
            style="height: 80px; background: white; border-radius: 50% 50% 0 0;"></div>

        <!-- Navigation Arrows -->
        {{-- <button class="btn position-absolute top-50 start-0 translate-middle-y btn-light rounded-circle shadow">
    &larr;
  </button>
  <button class="btn position-absolute top-50 end-0 translate-middle-y btn-light rounded-circle shadow">
    &rarr;
  </button> --}}
    </section>

    <!-- Tentang Kami Section -->
    <section class="py-5" id="tentang-kami">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ asset('assets/images/animasi burung.png') }}" alt="animasi burung"
                        class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-4" style="font-weight: 650; font-family: 'Red Hat Text', sans-serif;">
                        Sistem Pendaftaran dan Penilaian Lomba Burung yang Mudah dan Terpercaya
                    </h2>

                    <p class="font-redhat">Silobur membantu penyelenggara lomba burung dan juri untuk
                        melakukan pendaftaran, penilaian, dan rekap hasil secara cepat dan terpercaya.
                        Dengan sistem ini, transparansi dan keadilan lomba bisa terjaga lebih baik.</p>

                    <div class="row text-center mt-4">
                        <div class="row text-center mt-4">
                            <!-- Jumlah Lomba -->
                            <div class="col-md-4 mb-3">
                                <div class="p-4" style="background-color: #3846C3; border-radius: 1rem;">
                                    <div class="mb-2">
                                        <i class="bi bi-flag-fill fs-2 text-white"></i>
                                    </div>
                                    <div class="fs-3 fw-bold text-white"
                                        style="font-family: 'Red Hat Text', sans-serif;">10+</div>
                                    <div class="text-white">Jumlah Lomba</div>
                                </div>
                            </div>

                            <!-- Juri Terdaftar -->
                            <div class="col-md-4 mb-3">
                                <div class="p-4" style="background-color: #3846C3; border-radius: 1rem;">
                                    <div class="mb-2">
                                        <i class="bi bi-person-check-fill fs-2 text-white"></i>
                                    </div>
                                    <div class="fs-3 fw-bold text-white"
                                        style="font-family: 'Red Hat Text', sans-serif;">30+</div>
                                    <div class="text-white">Juri Terdaftar</div>
                                </div>
                            </div>

                            <!-- Peserta Lomba -->
                            <div class="col-md-4 mb-3">
                                <div class="p-4" style="background-color: #3846C3; border-radius: 1rem;">
                                    <div class="mb-2">
                                        <i class="bi bi-people-fill fs-2 text-white"></i>
                                    </div>
                                    <div class="fs-3 fw-bold text-white"
                                        style="font-family: 'Red Hat Text', sans-serif;">500+</div>
                                    <div class="text-white">Peserta Lomba</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Fitur Section -->
    <!-- Wave top border for Fitur Section -->
    <div class="position-relative" style="height: 80px; margin-top: -1px;">
        <div class="position-absolute top-0 start-0 w-100"
            style="height: 100%; background: white; border-radius: 0 0 50% 50%;"></div>
    </div>

    <section class="py-5 mb-5 bg-white font-redhat" id="Fitur">
        <div class="container">
            <h2 class="fw-semibold mb-2">Fitur</h2>
            <p class="mb-5">Lihat fitur utama yang tersedia di sistem kami untuk kelancaran lomba burung</p>

            <div class="row g-5">
                <!-- Pendaftaran Peserta -->
                <div class="col-md-5 text-center">
                    <h3 class="fw-semibold mb-4">Pendaftaran Peserta</h3>
                    <div class="service-image mb-4">
                        <img src="{{ asset('assets/images/pendaftaran.png') }}" alt="Pendaftaran peserta"
                            class="img-fluid" style="max-height: 180px;">
                        <img src="{{ asset('assets/images/pendaftaran2.png') }}" alt="Pendaftaran peserta"
                            class="img-fluid" style="max-height: 180px;">
                    </div>
                    <p class="text-center px-3">
                        Memudahkan peserta mendaftar lomba dengan pemilihan jenis burung,
                        kelas, dan nomor gantangan secara online.
                    </p>
                </div>

                <!-- Garis Pemisah -->
                <div class="col-md-2 d-none d-md-flex justify-content-center">
                    <div class="vertical-divider"></div>
                </div>

                <!-- Penilaian oleh Juri -->
                <div class="col-md-5 text-center">
                    <h3 class="fw-semibold mb-4">Penilaian oleh Juri</h3>
                    <div class="service-image mb-4">
                        <img src="{{ asset('assets/images/penilaian.png') }}" alt="Penilaian juri" class="img-fluid"
                            style="max-height: 135px;">
                        <img src="{{ asset('assets/images/penilaian2.png') }}" alt="Penilaian juri" class="img-fluid"
                            style="max-height: 180px;">
                    </div>
                    <p class="text-center px-3">
                        Sistem penilaian dua tahap yang transparan,
                        memudahkan juri dalam memberikan nilai secara akurat dan realtime.
                    </p>
                </div>

                <!-- Bisa ditambahkan fitur ketiga -->
                <div class="col-md-5 text-center mt-5 mx-auto">
                    <h3 class="fw-semibold mb-4">Rekap & Laporan</h3>
                    <div class="service-image mb-4">
                        <img src="{{ asset('assets/images/rekap.png') }}" alt="Rekap & Laporan" class="img-fluid"
                            style="max-height: 180px;">
                        <img src="{{ asset('assets/images/rekap2.png') }}" alt="Rekap & Laporan" class="img-fluid"
                            style="max-height: 180px;">
                    </div>
                    <p class="text-center px-3">
                        Menyediakan laporan hasil lomba secara lengkap dan mudah diakses oleh penyelenggara dan peserta.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- TPS Section -->
    <!--<section id="tps" class="position-relative overflow-hidden">-->
    <!--    <div class="tps-wrapper text-white position-relative z-1">-->
    <!-- Header -->
    <!--        <div class="tps-header text-start">-->
    <!--            <h2 class="mb-2" style="font-family: 'Red Hat Text', sans-serif; font-weight: 600;">TPS</h2>-->
    <!--            <p class="mb-4" style="font-family: 'Red Hat Text', sans-serif;">Temukan Lokasi TPS Terdekat</p>-->
    <!--        </div>-->

    <!-- TPS Cards -->
    <!--        <div class="row g-4">-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS Tulus Harapan</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS Ketileng Atas</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS Ketileng Bawah</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS PSIS</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS Wanamukti</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 col-sm-6">-->
    <!--                <div class="tps-card">-->
    <!--                    <img src="{{ asset('assets/images/sampahtps.png') }}" alt="TPS Icon" class="tps-icon">-->
    <!--                    <p class="tps-title">TPS Perumahan Intan</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->

    <!-- Lihat Semua -->
    <!--        <a href="#" class="lihat-semua">Lihat Semua →</a>-->
    <!--    </div>-->

    <!-- Jadwal Section -->
    <section class="py-5 position-relative" id="jadwal">
        <div class="container text-center">
            <h2 class="fw-semibold mb-2" style="font-family: 'Red Hat Text', sans-serif;">Jadwal</h2>
            <p class="mb-4" style="font-family: 'Red Hat Text', sans-serif;">Lihat dan Pantau Lomba Burung yang
                Tersedia</p>

            <div class="d-flex justify-content-center">
                <img src="{{ asset('assets/images/jadwal.jpg') }}" alt="jadwal lomba" class="img-fluid"
                    style="max-width: 400px;">
            </div>
        </div>
    </section>



    <!-- Berita Section -->
    <section class="py-5" id="berita">
        <div class="container">
            <h2 class="fw-semibold mb-3 lexend-font">Berita</h2>
            <p class="mb-4 redhat-font fw-light">
                Simak kabar terbaru dari dunia lomba burung versi SILOBUR, mulai dari event seru,
                sistem baru, hingga pengalaman para peserta.
            </p>

            <div class="row">
                <!-- Kolom kiri: Berita 1 & 3 -->
                <div class="col-md-6">
                    <!-- Artikel 1 -->
                    <div class="mb-4">
                        <a href="https://burungnews.com/kembalinya-silobur-baru-1-mengapa-disambut-gegap-gempita-berita-27658/"
                            target="_blank" class="text-decoration-none text-dark">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="row g-0">
                                    <div class="col-md-5 order-md-1">
                                        <img src="assets/images/berita1.png" class="img-fluid rounded-start h-100"
                                            alt="Kembalinya Silobur Baru 1" style="object-fit: cover;">
                                    </div>
                                    <div class="col-md-7 order-md-2">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                <small class="text-muted redhat-font">Rabu, 23 April 2025</small>
                                            </div>
                                            <h5 class="card-title lexend-font">Kembalinya Silobur Baru 1 Disambut Gegap
                                                Gempita</h5>
                                            <p class="card-text redhat-font small">Silobur Baru 1 kembali hadir dengan
                                                suasana lebih semarak. Kembalinya event ini menjadi magnet bagi
                                                kicaumania karena sistem penilaian yang fair dan atmosfer kompetisi yang
                                                sportif.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Artikel 3 -->
                    <div class="mb-4">
                        <a href="https://burungnews.com/anniversary-silobur-pati-jadi-ajang-reuni-banyak-pendatang-baru-penasaran-silobur-updated-berita-27727/"
                            target="_blank" class="text-decoration-none text-dark">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="row g-0">
                                    <div class="col-md-5 order-md-1">
                                        <img src="assets/images/berita3.png" class="img-fluid rounded-start h-100"
                                            alt="Anniversary Silobur Pati" style="object-fit: cover;">
                                    </div>
                                    <div class="col-md-7 order-md-2">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                <small class="text-muted redhat-font">Senin, 5 Mei 2025</small>
                                            </div>
                                            <h5 class="card-title lexend-font">ANNIVERSARY SILOBUR PATI: Jadi Ajang
                                                Reuni,
                                                Banyak Pendatang Baru Penasaran</h5>
                                            <p class="card-text redhat-font small">Gelaran perdana Anniversary Silobur
                                                Pati
                                                pada 4 Mei 2025 menjadi momen reuni sekaligus ajang perkenalan sistem
                                                Silobur yang telah diperbarui, menarik banyak peserta lama maupun
                                                pendatang
                                                baru.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Kolom kanan: Artikel 2 -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <a href="https://burungnews.com/melihat-dari-dekat-event-silobur-setelah-up-date-apanya-yang-baru-berita-27831/"
                            target="_blank" class="text-decoration-none text-dark">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="row g-0">
                                    <div class="col-md-5 order-md-1">
                                        <img src="assets/images/berita2.png" class="img-fluid rounded-start h-100"
                                            alt="Update Silobur" style="object-fit: cover;">
                                    </div>
                                    <div class="col-md-7 order-md-2">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                <small class="text-muted redhat-font">Rabu, 21 Mei 2025</small>
                                            </div>
                                            <h5 class="card-title lexend-font">Melihat dari Dekat Event Silobur Setelah
                                                Up Date: Apa yang Baru?</h5>
                                            <p class="card-text redhat-font small">Silobur mengaktifkan kembali
                                                tiketing
                                                online dan software penjurian. Perbaikannya ditangani oleh tim IT
                                                Silobur,
                                                Dimas putra dari om Masturi KM sebagai upaya menyempurnakan sistem
                                                manual
                                                penilaian berbasis digital.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- Call to Action --}}
    {{--
<section class="">
    <div class="">
        <div class="row align-items-center">
            <div class="col-12 col-sm-6 col-md-4 text-center text-sm-start mb-4 mb-sm-0">
                <img src="{{ asset('assets/images/kurakura.png') }}" alt="Turtle" class="img-fluid"
                    style="max-width: 360px;">
            </div>
            <div class="col-12 col-sm-6 col-md-8 px-4">
                <h2 class="fw-bold text-success mb-3 display-4">
                    Langkah Kecilmu, <span style="color: #3846C3;">Dampak Besar</span> Untuk <span
                        style="color: #3846C3;">Bumi Bersih</span>
                </h2>
            </div>
        </div>
    </div>
</section>
--}}

    <!-- Footer Section -->
    <footer class="bg-dark text-white py-5 footer-custom" style="background-color: #3846C3;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <img src="{{ asset('assets/images/Group 6.png') }}" alt="Silobur Logo" height="60"
                        class="mb-3">
                    <ul class="list-unstyled d-flex">
                        <li class="me-3"><a href="#" class="text-white"><i
                                    class="fab fa-facebook-f fa-2x"></i></a>
                        </li>
                        <li class="me-3"><a href="#" class="text-white"><i
                                    class="fab fa-instagram fa-2x"></i></a>
                        </li>
                        <li class="me-3"><a href="#" class="text-white"><i
                                    class="fab fa-twitter fa-2x"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="mb-3 fw-bold">Silobur</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="#" class="text-white text-decoration-none fw-light">Tentang
                                Kami</a></li>
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Fitur</a>
                        </li>
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Galeri</a></li>
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Jadwal</a>
                        </li>
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Berita</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="mb-3 fw-semibold">Fitur</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Pendaftaran Peserta</a>
                        </li>
                        <li class="mb-3"><a href="#"
                                class="text-white text-decoration-none fw-light">Penilaian oleh Juri</a>
                        </li>
                        <li class="mb-3"><a href="#" class="text-white text-decoration-none fw-light">Rekap &
                                Laporan</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3 fw-bold">Contact</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> <span
                                class="fw-light">Semarang,
                                Indonesia</span>
                        </li>
                        <li class="mb-3"><i class="fas fa-envelope me-2 fw-light"></i> <span
                                class="fw-light">Silobur@gmail.com</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
