<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $school->meta_title ?? $school->name }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $school->meta_description ?? '' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('course/styles/bootstrap4/bootstrap.min.css') }}">
    <link href="{{ asset('course/plugins/fontawesome-free-5.0.1/css/fontawesome-all.css') }}" rel="stylesheet"
        type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('course/plugins/OwlCarousel2-2.2.1/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('course/plugins/OwlCarousel2-2.2.1/owl.theme.default.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('course/plugins/OwlCarousel2-2.2.1/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('course/styles/main_styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('course/styles/responsive.css') }}">
    @if ($school && $school->theme)
        <style>
            {!! $school->theme->generateCSS() !!}
        </style>
    @endif
    <style>
        html {
            scroll-behavior: smooth;
        }
        /* Auto-resize logo image to fit header & footer cleanly */
        .logo img {
            max-height: 40px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            vertical-align: middle;
        }
        .footer_content .logo img {
            max-height: 50px;
            max-width: 150px;
        }
        /* Enforce uniform course card image dimensions */
        .course_box .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .footer_copyright, .footer_copyright span {
            color: #a5a5a5 !important;
        }
        .footer_copyright i {
            color: #ef4444 !important;
        }
        .footer_copyright a {
            color: #ffb606 !important;
            transition: color 200ms ease;
        }
        .footer_copyright a:hover {
            color: #ffffff !important;
        }
    </style>
</head>

<body>

    <div class="super_container">

        <!-- Header -->

        <header class="header d-flex flex-row">
            <div class="header_content d-flex flex-row align-items-center">
                <!-- Logo -->
                <div class="logo_container">
                    <div class="logo">
                        <img src="{{ $school->logo_url }}" alt="">
                        <span>{{ $school->name }}</span>
                    </div>
                </div>

                <!-- Main Navigation -->
                <nav class="main_nav_container">
                    <div class="main_nav">
                        <ul class="main_nav_list">
                            <li class="main_nav_item"><a href="{{ route('landing') }}">home</a></li>
                            <li class="main_nav_item"><a href="{{ route('landing') }}#about">about us</a></li>
                            <li class="main_nav_item"><a href="{{ route('landing') }}#courses">courses</a></li>
                            <li class="main_nav_item"><a href="{{ route('guest.exams.index') }}">ujian tamu</a></li>
                            <li class="main_nav_item"><a href="{{ route('landing') }}#contact">contact</a></li>
                            @auth
                                <li class="main_nav_item"><a href="{{ route('dashboard') }}">dashboard</a></li>
                            @else
                                <li class="main_nav_item"><a href="{{ route('login') }}">login</a></li>
                            @endauth
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="header_side d-flex flex-row justify-content-center align-items-center">
                <img src="{{ asset('course/images/phone-call.svg') }}" alt="">
                <span>{{ $school->contact_whatsapp ?? $school->contact_phone ?? '+43 4566 7788 2457' }}</span>
            </div>

            <!-- Hamburger -->
            <div class="hamburger_container">
                <i class="fas fa-bars trans_200"></i>
            </div>

        </header>

        <!-- Menu -->
        <div class="menu_container menu_mm">

            <!-- Menu Close Button -->
            <div class="menu_close_container">
                <div class="menu_close"></div>
            </div>

            <!-- Menu Items -->
            <div class="menu_inner menu_mm">
                <div class="menu menu_mm">
                    <ul class="menu_list menu_mm">
                        <li class="menu_item menu_mm"><a href="{{ route('landing') }}">Home</a></li>
                        <li class="menu_item menu_mm"><a href="{{ route('landing') }}#about">About us</a></li>
                        <li class="menu_item menu_mm"><a href="{{ route('landing') }}#courses">Courses</a></li>
                        <li class="menu_item menu_mm"><a href="{{ route('guest.exams.index') }}">Ujian Tamu</a></li>
                        <li class="menu_item menu_mm"><a href="{{ route('landing') }}#contact">Contact</a></li>
                        @auth
                            <li class="menu_item menu_mm"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @else
                            <li class="menu_item menu_mm"><a href="{{ route('login') }}">Login</a></li>
                        @endauth
                    </ul>

                    <!-- Menu Social -->

                    <div class="menu_social_container menu_mm">
                        <ul class="menu_social menu_mm">
                            @if($school->contact_whatsapp)
                                <li class="menu_social_item menu_mm"><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->contact_whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                            @endif
                            @if($school->social_youtube)
                                <li class="menu_social_item menu_mm"><a href="{{ $school->social_youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            @endif
                            <li class="menu_social_item menu_mm"><a href="{{ $school->social_instagram ?? '#' }}"><i
                                        class="fab fa-instagram"></i></a></li>
                            <li class="menu_social_item menu_mm"><a href="{{ $school->social_facebook ?? '#' }}"><i
                                        class="fab fa-facebook-f"></i></a></li>
                            <li class="menu_social_item menu_mm"><a href="{{ $school->social_twitter ?? '#' }}"><i
                                        class="fab fa-twitter"></i></a></li>
                        </ul>
                    </div>

                    <div class="menu_copyright menu_mm">Colorlib All rights reserved</div>
                </div>

            </div>

        </div>

        <!-- Home -->

        <div class="home">

            <!-- Hero Slider -->
            <div class="hero_slider_container">
                <div class="hero_slider owl-carousel">

                    <!-- Hero Slide -->
                    <div class="hero_slide">
                        <div class="hero_slide_background"
                            style="background-image:url({{ $school->hero_image_url }})"></div>
                        <div class="hero_slide_container d-flex flex-column align-items-center justify-content-center">
                            <div class="hero_slide_content text-center">
                                <h1 data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                    {!! $school->hero_title ?? 'Get your <span>Education</span> today!' !!}</h1>
                                @if($school->hero_subtitle)
                                    <h2 class="text-white mt-3" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 24px; font-weight: 300; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_subtitle }}
                                    </h2>
                                @endif
                                @if($school->hero_description)
                                    <p class="text-white mt-3 mb-4 mx-auto" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 16px; max-width: 700px; opacity: 0.9; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_description }}
                                    </p>
                                @endif
                                @if($school->hero_cta_text)
                                    <div class="button button_1 mx-auto mt-4" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                        <a href="{{ $school->hero_cta_link ?? route('login') }}">{{ $school->hero_cta_text }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Hero Slide -->
                    <div class="hero_slide">
                        <div class="hero_slide_background"
                            style="background-image:url({{ $school->hero_image_url }})"></div>
                        <div class="hero_slide_container d-flex flex-column align-items-center justify-content-center">
                            <div class="hero_slide_content text-center">
                                <h1 data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                    {!! $school->hero_title ?? 'Get your <span>Education</span> today!' !!}</h1>
                                @if($school->hero_subtitle)
                                    <h2 class="text-white mt-3" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 24px; font-weight: 300; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_subtitle }}
                                    </h2>
                                @endif
                                @if($school->hero_description)
                                    <p class="text-white mt-3 mb-4 mx-auto" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 16px; max-width: 700px; opacity: 0.9; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_description }}
                                    </p>
                                @endif
                                @if($school->hero_cta_text)
                                    <div class="button button_1 mx-auto mt-4" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                        <a href="{{ $school->hero_cta_link ?? route('login') }}">{{ $school->hero_cta_text }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Hero Slide -->
                    <div class="hero_slide">
                        <div class="hero_slide_background"
                            style="background-image:url({{ $school->hero_image_url }})"></div>
                        <div class="hero_slide_container d-flex flex-column align-items-center justify-content-center">
                            <div class="hero_slide_content text-center">
                                <h1 data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                    {!! $school->hero_title ?? 'Get your <span>Education</span> today!' !!}</h1>
                                @if($school->hero_subtitle)
                                    <h2 class="text-white mt-3" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 24px; font-weight: 300; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_subtitle }}
                                    </h2>
                                @endif
                                @if($school->hero_description)
                                    <p class="text-white mt-3 mb-4 mx-auto" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut" style="font-size: 16px; max-width: 700px; opacity: 0.9; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                                        {{ $school->hero_description }}
                                    </p>
                                @endif
                                @if($school->hero_cta_text)
                                    <div class="button button_1 mx-auto mt-4" data-animation-in="fadeInUp" data-animation-out="animate-out fadeOut">
                                        <a href="{{ $school->hero_cta_link ?? route('login') }}">{{ $school->hero_cta_text }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="hero_slider_left hero_slider_nav trans_200"><span class="trans_200">prev</span></div>
                <div class="hero_slider_right hero_slider_nav trans_200"><span class="trans_200">next</span></div>
            </div>

        </div>

        <div class="hero_boxes">
            <div class="hero_boxes_inner">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-4 hero_box_col">
                            <div class="hero_box d-flex flex-row align-items-center justify-content-start">
                                <img src="{{ asset('course/images/earth-globe.svg') }}" class="svg"
                                    alt="">
                                <div class="hero_box_content">
                                    <h2 class="hero_box_title">Online Courses</h2>
                                    <a href="{{ route('login') }}" class="hero_box_link">view more</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 hero_box_col">
                            <div class="hero_box d-flex flex-row align-items-center justify-content-start">
                                <img src="{{ asset('course/images/books.svg') }}" class="svg" alt="">
                                <div class="hero_box_content">
                                    <h2 class="hero_box_title">Our Library</h2>
                                    <a href="{{ route('login') }}" class="hero_box_link">view more</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 hero_box_col">
                            <div class="hero_box d-flex flex-row align-items-center justify-content-start">
                                <img src="{{ asset('course/images/professor.svg') }}" class="svg" alt="">
                                <div class="hero_box_content">
                                    <h2 class="hero_box_title">Our Teachers</h2>
                                    <a href="{{ route('login') }}" class="hero_box_link">view more</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- About Us -->
        @if ($school->about_title || $school->about_content)
        <div id="about" class="about_section page_section bg-light py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="section_title text-left mb-4">
                            <h1>{{ $school->about_title ?? 'About Us' }}</h1>
                        </div>
                        <p class="about_text" style="font-size: 16px; line-height: 1.8; color: #555;">
                            {!! nl2br(e($school->about_content)) !!}
                        </p>
                    </div>
                    @if ($school->about_image_url)
                    <div class="col-lg-6 text-center mt-4 mt-lg-0">
                        <img src="{{ $school->about_image_url }}" alt="{{ $school->about_title }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: cover;">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Popular -->

        <div id="courses" class="popular page_section">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="section_title text-center">
                            <h1>Popular Courses</h1>
                        </div>
                    </div>
                </div>

                @if(request()->filled('name') || request()->filled('category') || request()->filled('degree'))
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5 class="text-muted">
                                Hasil Pencarian:
                                @if(request('name')) nama "{{ request('name') }}" @endif
                                @if(request('category')) kategori "{{ request('category') }}" @endif
                                @if(request('degree')) jenjang "{{ request('degree') }}" @endif
                            </h5>
                            <a href="{{ route('landing') }}#courses" class="badge badge-warning p-2 text-white mt-1">Reset Pencarian</a>
                        </div>
                    </div>
                @endif

                <div class="row course_boxes">

                    @if ($school->courses && $school->courses->count() > 0)
                        @foreach ($school->courses as $course)
                            <div class="col-lg-4 course_box">
                                <div class="card">
                                    <img class="card-img-top"
                                        src="{{ $course->cover_image ? asset('storage/' . $course->cover_image) : asset('course/images/course_1.jpg') }}"
                                        alt="{{ $course->title }}">
                                    <div class="card-body text-center">
                                        <div class="card-title"><a
                                                href="{{ route('login') }}">{{ $course->title }}</a></div>
                                        <div class="card-text">{{ Str::limit($course->description, 60) }}</div>
                                    </div>
                                    <div class="price_box d-flex flex-row align-items-center">
                                        <div class="course_author_image">
                                            <img src="{{ asset('course/images/author.jpg') }}" alt="">
                                        </div>
                                        <div class="course_author_name">
                                            {{ $course->instructor->name ?? 'Instructor' }}, <span>Author</span></div>
                                        <div
                                            class="course_price d-flex flex-column align-items-center justify-content-center">
                                            <span>{{ $course->price ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Free' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-5">
                            @if(request()->filled('name') || request()->filled('category') || request()->filled('degree'))
                                <p class="text-muted">Belum ada kursus yang sesuai dengan kriteria pencarian Anda.</p>
                                <a href="{{ route('landing') }}#courses" class="btn btn-warning mt-2 text-white">Reset Pencarian</a>
                            @else
                                <p>Belum ada kursus tersedia.</p>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Register -->

        {{-- <div class="register">

            <div class="container-fluid">

                <div class="row row-eq-height">
                    <div class="col-lg-6 nopadding">

                        <!-- Register -->

                        <div class="register_section d-flex flex-column align-items-center justify-content-center">
                            <div class="register_content text-center">
                                <h1 class="register_title">Register now and get a discount <span>50%</span> discount
                                    until 1 January</h1>
                                <p class="register_text">In aliquam, augue a gravida rutrum, ante nisl fermentum nulla,
                                    vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor
                                    fermentum. Aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempo.
                                </p>
                                <div class="button button_1 register_button mx-auto trans_200"><a
                                        href="{{ route('register') }}">register now</a></div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6 nopadding">

                        <!-- Search -->

                        <div class="search_section d-flex flex-column align-items-center justify-content-center">
                            <div class="search_background"
                                style="background-image:url({{ asset('course/images/search_background.jpg') }});">
                            </div>
                            <div class="search_content text-center">
                                <h1 class="search_title">Search for your course</h1>
                                <form id="search_form" class="search_form" action="{{ route('landing') }}#courses" method="GET">
                                    <input id="search_form_name" name="name" class="input_field search_form_name" type="text"
                                        placeholder="Course Name" value="{{ request('name') }}">
                                    <input id="search_form_category" name="category" class="input_field search_form_category"
                                        type="text" placeholder="Category" value="{{ request('category') }}">
                                    <input id="search_form_degree" name="degree" class="input_field search_form_degree"
                                        type="text" placeholder="Degree" value="{{ request('degree') }}">
                                    <button id="search_submit_button" type="submit"
                                        class="search_submit_button trans_200" value="Submit">search course</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Services -->

        <div class="services page_section">

            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="section_title text-center">
                            <h1>Our Services</h1>
                        </div>
                    </div>
                </div>

                <div class="row services_row">

                    @foreach ($school->features as $feature)
                        <div
                            class="col-lg-4 service_item text-left d-flex flex-column align-items-start justify-content-start">
                            <div class="icon_container d-flex flex-column justify-content-end">
                                <i class="fas {{ $feature['icon'] }}" style="font-size:40px;color:#f5a425;"></i>
                            </div>
                            <h3>{{ $feature['title'] }}</h3>
                            <p>{{ $feature['description'] }}</p>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- Milestones (Statistics) -->
        @if ($school->statistics && count($school->statistics) > 0)
        @php
            $getIconForLabel = function($label) {
                $label = strtolower($label);
                if (str_contains($label, 'siswa') || str_contains($label, 'student') || str_contains($label, 'murid')) {
                    return 'fa-user-graduate';
                }
                if (str_contains($label, 'guru') || str_contains($label, 'teacher') || str_contains($label, 'dosen') || str_contains($label, 'instructor')) {
                    return 'fa-chalkboard-teacher';
                }
                if (str_contains($label, 'kursus') || str_contains($label, 'course') || str_contains($label, 'materi')) {
                    return 'fa-book-open';
                }
                if (str_contains($label, 'kelas') || str_contains($label, 'class')) {
                    return 'fa-school';
                }
                return 'fa-chart-line';
            };
        @endphp
        <div class="milestones page_section bg-dark text-white py-5" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
            <div class="container">
                <div class="row">
                    @foreach ($school->statistics as $stat)
                        <div class="col-lg-3 col-md-6 milestone_col text-center my-3">
                            <div class="milestone_icon mb-3">
                                <i class="fas {{ $getIconForLabel($stat['label']) }} text-warning" style="font-size: 32px; color: #ffb606 !important;"></i>
                            </div>
                            <div class="milestone_counter font-weight-bold display-4 text-warning mb-2" style="font-size: 42px; color: #ffb606 !important;">
                                {{ $stat['value'] }}
                            </div>
                            <div class="milestone_text text-uppercase tracking-wider text-muted font-weight-bold" style="font-size: 14px; color: #cbd5e0 !important;">
                                {{ $stat['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Testimonials -->

        {{-- <div id="testimonials" class="testimonials page_section">
            <!-- <div class="testimonials_background" style="background-image:url(images/testimonials_background.jpg)"></div> -->
            <div class="testimonials_background_container prlx_parent">
                <div class="testimonials_background prlx"
                    style="background-image:url({{ asset('course/images/testimonials_background.jpg') }})"></div>
            </div>
            <div class="container">

                <div class="row">
                    <div class="col">
                        <div class="section_title text-center">
                            <h1>What our students say</h1>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10 offset-lg-1">

                        <div class="testimonials_slider_container">

                            <!-- Testimonials Slider -->
                            <div class="owl-carousel owl-theme testimonials_slider">

                                <!-- Testimonials Item -->
                                <div class="owl-item">
                                    <div class="testimonials_item text-center">
                                        <div class="quote">"</div>
                                        <p class="testimonials_text">In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.</p>
                                        <div class="testimonial_user">
                                            <div class="testimonial_image mx-auto">
                                                <img src="{{ asset('course/images/testimonials_user.jpg') }}"
                                                    alt="">
                                            </div>
                                            <div class="testimonial_name">james cooper</div>
                                            <div class="testimonial_title">Graduate Student</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testimonials Item -->
                                <div class="owl-item">
                                    <div class="testimonials_item text-center">
                                        <div class="quote">"</div>
                                        <p class="testimonials_text">In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.</p>
                                        <div class="testimonial_user">
                                            <div class="testimonial_image mx-auto">
                                                <img src="{{ asset('course/images/testimonials_user.jpg') }}"
                                                    alt="">
                                            </div>
                                            <div class="testimonial_name">james cooper</div>
                                            <div class="testimonial_title">Graduate Student</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testimonials Item -->
                                <div class="owl-item">
                                    <div class="testimonials_item text-center">
                                        <div class="quote">"</div>
                                        <p class="testimonials_text">In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.In aliquam, augue a gravida rutrum, ante nisl
                                            fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada,
                                            finibus tortor fermentum.</p>
                                        <div class="testimonial_user">
                                            <div class="testimonial_image mx-auto">
                                                <img src="{{ asset('course/images/testimonials_user.jpg') }}"
                                                    alt="">
                                            </div>
                                            <div class="testimonial_name">james cooper</div>
                                            <div class="testimonial_title">Graduate Student</div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div> --}}

        <!-- Events -->

        {{-- <div class="events page_section">
            <div class="container">

                <div class="row">
                    <div class="col">
                        <div class="section_title text-center">
                            <h1>Upcoming Events</h1>
                        </div>
                    </div>
                </div>

                <div class="event_items">

                    <!-- Event Item -->
                    <div class="row event_item">
                        <div class="col">
                            <div class="row d-flex flex-row align-items-end">

                                <div class="col-lg-2 order-lg-1 order-2">
                                    <div
                                        class="event_date d-flex flex-column align-items-center justify-content-center">
                                        <div class="event_day">07</div>
                                        <div class="event_month">January</div>
                                    </div>
                                </div>

                                <div class="col-lg-6 order-lg-2 order-3">
                                    <div class="event_content">
                                        <div class="event_name">
                                            <a class="trans_200 event-detail-link" href="#"
                                               data-event-title="Student Festival"
                                               data-event-date="07 January"
                                               data-event-location="Grand Central Park"
                                               data-event-desc="Mari bergabung bersama kami dalam perayaan festival pelajar tahunan. Nikmati berbagai pertunjukan seni, pameran inovasi proyek siswa, bazar kuliner, dan sesi networking interaktif antar siswa.">
                                                Student Festival
                                            </a>
                                        </div>
                                        <div class="event_location">Grand Central Park</div>
                                        <p>In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor
                                            nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor.</p>
                                    </div>
                                </div>

                                <div class="col-lg-4 order-lg-3 order-1">
                                    <div class="event_image">
                                        <img src="{{ asset('course/images/event_1.jpg') }}"
                                            alt="https://unsplash.com/@theunsteady5">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Event Item -->
                    <div class="row event_item">
                        <div class="col">
                            <div class="row d-flex flex-row align-items-end">

                                <div class="col-lg-2 order-lg-1 order-2">
                                    <div
                                        class="event_date d-flex flex-column align-items-center justify-content-center">
                                        <div class="event_day">07</div>
                                        <div class="event_month">January</div>
                                    </div>
                                </div>

                                <div class="col-lg-6 order-lg-2 order-3">
                                    <div class="event_content">
                                        <div class="event_name">
                                            <a class="trans_200 event-detail-link" href="#"
                                               data-event-title="Open day in the University campus"
                                               data-event-date="07 January"
                                               data-event-location="Grand Central Park"
                                               data-event-desc="Kesempatan terbaik bagi para calon siswa/mahasiswa dan orang tua untuk melihat secara langsung fasilitas sekolah/kampus kami, berinteraksi dengan guru/dosen, serta berkonsultasi mengenai program studi dan beasiswa yang tersedia.">
                                                Open day in the University campus
                                            </a>
                                        </div>
                                        <div class="event_location">Grand Central Park</div>
                                        <p>In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor
                                            nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor.</p>
                                    </div>
                                </div>

                                <div class="col-lg-4 order-lg-3 order-1">
                                    <div class="event_image">
                                        <img src="{{ asset('course/images/event_2.jpg') }}"
                                            alt="https://unsplash.com/@claybanks1989">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Event Item -->
                    <div class="row event_item">
                        <div class="col">
                            <div class="row d-flex flex-row align-items-end">

                                <div class="col-lg-2 order-lg-1 order-2">
                                    <div
                                        class="event_date d-flex flex-column align-items-center justify-content-center">
                                        <div class="event_day">07</div>
                                        <div class="event_month">January</div>
                                    </div>
                                </div>

                                <div class="col-lg-6 order-lg-2 order-3">
                                    <div class="event_content">
                                        <div class="event_name">
                                            <a class="trans_200 event-detail-link" href="#"
                                               data-event-title="Student Graduation Ceremony"
                                               data-event-date="07 January"
                                               data-event-location="Grand Central Park"
                                               data-event-desc="Upacara kelulusan resmi siswa/mahasiswa berprestasi tahun akademik ini. Rayakan pencapaian akademik bersama keluarga, teman-teman, dan civitas akademika dalam suasana khidmat dan meriah.">
                                                Student Graduation Ceremony
                                            </a>
                                        </div>
                                        <div class="event_location">Grand Central Park</div>
                                        <p>In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor
                                            nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor.</p>
                                    </div>
                                </div>

                                <div class="col-lg-4 order-lg-3 order-1">
                                    <div class="event_image">
                                        <img src="{{ asset('course/images/event_3.jpg') }}"
                                            alt="https://unsplash.com/@juanmramosjr">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div> --}}

        <!-- Footer -->

        <footer class="footer" id="contact">
            <div class="container">

                <!-- Newsletter -->

                <div class="newsletter">
                    <div class="row">
                        <div class="col">
                            <div class="section_title text-center">
                                <h1>Subscribe to newsletter</h1>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-center">
                            <div class="newsletter_form_container mx-auto">
                                <form action="post">
                                    <div
                                        class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-center">
                                        <input id="newsletter_email" class="newsletter_email" type="email"
                                            placeholder="Email Address" required="required"
                                            data-error="Valid email is required.">
                                        <button id="newsletter_submit" type="submit"
                                            class="newsletter_submit_btn trans_300" value="Submit">Subscribe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Content -->

                <div class="footer_content">
                    <div class="row">

                        <!-- Footer Column - About -->
                        <div class="col-lg-3 footer_col">

                            <!-- Logo -->
                            <div class="logo_container">
                                <div class="logo">
                                    <img src="{{ $school->logo_url }}" alt="">
                                    <span>{{ $school->name }}</span>
                                </div>
                            </div>

                            <p class="footer_about_text">
                                {{ $school->about_content ? Str::limit($school->about_content, 150) : 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum, tempor lacus.' }}
                            </p>

                        </div>

                        <!-- Footer Column - Menu -->

                        <div class="col-lg-3 footer_col">
                            <div class="footer_column_title">Menu</div>
                            <div class="footer_column_content">
                                <ul>
                                    <li class="footer_list_item"><a href="{{ route('landing') }}">Home</a></li>
                                    <li class="footer_list_item"><a href="{{ route('landing') }}#about">About Us</a></li>
                                    <li class="footer_list_item"><a href="{{ route('landing') }}#courses">Courses</a></li>
                                    <li class="footer_list_item"><a href="{{ route('guest.exams.index') }}">Ujian Tamu</a></li>
                                    <li class="footer_list_item"><a href="{{ route('landing') }}#contact">Contact</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Column - Usefull Links -->

                        <div class="col-lg-3 footer_col">
                            <div class="footer_column_title">Usefull Links</div>
                            <div class="footer_column_content">
                                <ul>
                                    <li class="footer_list_item"><a href="{{ route('landing') }}#testimonials">Testimonials</a></li>
                                    <li class="footer_list_item"><a href="#" class="placeholder-link" data-title="FAQ (Pertanyaan Umum)">FAQ</a></li>
                                    <li class="footer_list_item"><a href="#" class="placeholder-link" data-title="Komunitas Siswa & Alumni">Community</a></li>
                                    <li class="footer_list_item"><a href="#" class="placeholder-link" data-title="Galeri & Foto Kampus">Campus Pictures</a></li>
                                    <li class="footer_list_item"><a href="#" class="placeholder-link" data-title="Informasi Biaya Pendidikan (Tuitions)">Tuitions</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Column - Contact -->

                        <div class="col-lg-3 footer_col">
                            <div class="footer_column_title">Contact</div>
                            <div class="footer_column_content">
                                <ul>
                                    <li class="footer_contact_item">
                                        <div class="footer_contact_icon">
                                            <img src="{{ asset('course/images/placeholder.svg') }}"
                                                alt="https://www.flaticon.com/authors/lucy-g">
                                        </div>
                                        {{ $school->contact_address ?? 'Blvd Libertad, 34 m05200 Arévalo' }}
                                    </li>
                                    <li class="footer_contact_item">
                                        <div class="footer_contact_icon">
                                            <img src="{{ asset('course/images/smartphone.svg') }}"
                                                alt="https://www.flaticon.com/authors/lucy-g">
                                        </div>
                                        {{ $school->contact_phone ?? '0034 37483 2445 322' }}
                                    </li>
                                    <li class="footer_contact_item">
                                        <div class="footer_contact_icon">
                                            <img src="{{ asset('course/images/envelope.svg') }}"
                                                alt="https://www.flaticon.com/authors/lucy-g">
                                        </div>{{ $school->contact_email ?? 'hello@company.com' }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Copyright -->

                <div class="footer_bar d-flex flex-column flex-sm-row align-items-center">
                    <div class="footer_copyright">
                        <span>
                            Copyright &copy;{{ date('Y') }} All rights reserved, created by <a href="https://noteds.com" target="_blank">Noteds Technology</a>
                        </span>
                    </div>
                    <div class="footer_social ml-sm-auto">
                        <ul class="menu_social">
                            @if($school->contact_whatsapp)
                                <li class="menu_social_item"><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->contact_whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                            @endif
                            @if($school->social_youtube)
                                <li class="menu_social_item"><a href="{{ $school->social_youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            @endif
                            <li class="menu_social_item"><a href="{{ $school->social_instagram ?? '#' }}"><i
                                        class="fab fa-instagram"></i></a></li>
                            <li class="menu_social_item"><a href="{{ $school->social_facebook ?? '#' }}"><i
                                        class="fab fa-facebook-f"></i></a></li>
                            <li class="menu_social_item"><a href="{{ $school->social_twitter ?? '#' }}"><i
                                        class="fab fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </footer>

    </div>

    <script src="{{ asset('course/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('course/styles/bootstrap4/popper.js') }}"></script>
    <script src="{{ asset('course/styles/bootstrap4/bootstrap.min.js') }}"></script>
    <script src="{{ asset('course/plugins/greensock/TweenMax.min.js') }}"></script>
    <script src="{{ asset('course/plugins/greensock/TimelineMax.min.js') }}"></script>
    <script src="{{ asset('course/plugins/scrollmagic/ScrollMagic.min.js') }}"></script>
    <script src="{{ asset('course/plugins/greensock/animation.gsap.min.js') }}"></script>
    <script src="{{ asset('course/plugins/greensock/ScrollToPlugin.min.js') }}"></script>
    <script src="{{ asset('course/plugins/OwlCarousel2-2.2.1/owl.carousel.js') }}"></script>
    <script src="{{ asset('course/plugins/scrollTo/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('course/plugins/easing/easing.js') }}"></script>
    <script src="{{ asset('course/js/custom.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Newsletter form submit handler
            $('.newsletter_form_container form').on('submit', function(e) {
                e.preventDefault();
                var emailInput = $('#newsletter_email');
                var email = emailInput.val().trim();

                if (email) {
                    Swal.fire({
                        title: 'Langganan Berhasil!',
                        text: 'Terima kasih! Email Anda (' + email + ') telah terdaftar untuk menerima berita terbaru dari kami.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb606'
                    });
                    emailInput.val('');
                }
            });

            // Event detail click handler
            $('.event-detail-link').on('click', function(e) {
                e.preventDefault();
                var title = $(this).data('event-title');
                var date = $(this).data('event-date');
                var location = $(this).data('event-location');
                var desc = $(this).data('event-desc');

                Swal.fire({
                    title: title,
                    html: `
                        <div class="text-left" style="font-size: 15px; line-height: 1.6;">
                            <p class="mb-2"><strong><i class="far fa-calendar-alt text-warning mr-1"></i> Tanggal:</strong> ${date}</p>
                            <p class="mb-3"><strong><i class="fas fa-map-marker-alt text-warning mr-1"></i> Lokasi:</strong> ${location}</p>
                            <hr>
                            <p class="text-muted">${desc}</p>
                            <div class="mt-4 text-center">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->contact_whatsapp ?? '') }}" target="_blank" class="btn btn-success text-white px-4 py-2" style="border-radius: 30px; font-weight: 500;">
                                    <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
                                </a>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            });

            // Placeholder link click handler
            $('.placeholder-link').on('click', function(e) {
                e.preventDefault();
                var title = $(this).data('title');

                Swal.fire({
                    title: title,
                    html: `
                        <div class="text-center" style="font-size: 15px; line-height: 1.6;">
                            <p class="mb-3 text-muted">Halaman/informasi ini sedang dipersiapkan dan akan segera tersedia. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                            <div class="mt-4">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->contact_whatsapp ?? '') }}" target="_blank" class="btn btn-success text-white px-4 py-2" style="border-radius: 30px; font-weight: 500;">
                                    <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
                                </a>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            });
        });
    </script>

</body>

</html>
