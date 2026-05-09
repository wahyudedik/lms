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
                            <li class="main_nav_item"><a href="#">home</a></li>
                            <li class="main_nav_item"><a href="#">about us</a></li>
                            <li class="main_nav_item"><a href="{{ route('login') }}">courses</a></li>
                            <li class="main_nav_item"><a href="#">elements</a></li>
                            <li class="main_nav_item"><a href="#">news</a></li>
                            <li class="main_nav_item"><a href="#contact">contact</a></li>
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
                <span>{{ $school->contact_phone ?? '+43 4566 7788 2457' }}</span>
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
                        <li class="menu_item menu_mm"><a href="#">Home</a></li>
                        <li class="menu_item menu_mm"><a href="#">About us</a></li>
                        <li class="menu_item menu_mm"><a href="{{ route('login') }}">Courses</a></li>
                        <li class="menu_item menu_mm"><a href="#">Elements</a></li>
                        <li class="menu_item menu_mm"><a href="#">News</a></li>
                        <li class="menu_item menu_mm"><a href="#contact">Contact</a></li>
                        @auth
                            <li class="menu_item menu_mm"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @else
                            <li class="menu_item menu_mm"><a href="{{ route('login') }}">Login</a></li>
                        @endauth
                    </ul>

                    <!-- Menu Social -->

                    <div class="menu_social_container menu_mm">
                        <ul class="menu_social menu_mm">
                            <li class="menu_social_item menu_mm"><a href="#"><i class="fab fa-pinterest"></i></a>
                            </li>
                            <li class="menu_social_item menu_mm"><a href="#"><i
                                        class="fab fa-linkedin-in"></i></a></li>
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

        <!-- Popular -->

        <div class="popular page_section">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="section_title text-center">
                            <h1>Popular Courses</h1>
                        </div>
                    </div>
                </div>

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
                            <p>Belum ada kursus tersedia.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Register -->

        <div class="register">

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
                                <form id="search_form" class="search_form" action="post">
                                    <input id="search_form_name" class="input_field search_form_name" type="text"
                                        placeholder="Course Name" required="required"
                                        data-error="Course name is required.">
                                    <input id="search_form_category" class="input_field search_form_category"
                                        type="text" placeholder="Category">
                                    <input id="search_form_degree" class="input_field search_form_degree"
                                        type="text" placeholder="Degree">
                                    <button id="search_submit_button" type="submit"
                                        class="search_submit_button trans_200" value="Submit">search course</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

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

        <!-- Testimonials -->

        <div class="testimonials page_section">
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
        </div>

        <!-- Events -->

        <div class="events page_section">
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
                                        <div class="event_name"><a class="trans_200" href="#">Student
                                                Festival</a></div>
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
                                        <div class="event_name"><a class="trans_200" href="#">Open day in the
                                                Univesrsity campus</a></div>
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
                                        <div class="event_name"><a class="trans_200" href="#">Student
                                                Graduation Ceremony</a></div>
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
        </div>

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
                                    <li class="footer_list_item"><a href="#">Home</a></li>
                                    <li class="footer_list_item"><a href="#">About Us</a></li>
                                    <li class="footer_list_item"><a href="{{ route('login') }}">Courses</a></li>
                                    <li class="footer_list_item"><a href="#">News</a></li>
                                    <li class="footer_list_item"><a href="#contact">Contact</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Column - Usefull Links -->

                        <div class="col-lg-3 footer_col">
                            <div class="footer_column_title">Usefull Links</div>
                            <div class="footer_column_content">
                                <ul>
                                    <li class="footer_list_item"><a href="#">Testimonials</a></li>
                                    <li class="footer_list_item"><a href="#">FAQ</a></li>
                                    <li class="footer_list_item"><a href="#">Community</a></li>
                                    <li class="footer_list_item"><a href="#">Campus Pictures</a></li>
                                    <li class="footer_list_item"><a href="#">Tuitions</a></li>
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
                            Copyright &copy;{{ date('Y') }} All rights reserved <i class="fa fa-heart"
                                aria-hidden="true"></i> by <a href="#" target="_blank">Noteds Technology</a>
                        </span>
                    </div>
                    <div class="footer_social ml-sm-auto">
                        <ul class="menu_social">
                            <li class="menu_social_item"><a href="#"><i class="fab fa-pinterest"></i></a></li>
                            <li class="menu_social_item"><a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </li>
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

</body>

</html>
