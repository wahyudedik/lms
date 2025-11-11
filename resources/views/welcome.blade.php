@php
    // Detect school from domain or use default
    $currentSchool = null;
    if (isset($school)) {
        // Preview mode from controller
        $currentSchool = $school;
    } else {
        // Try to detect from authenticated user's school or default to first active school
    if (auth()->check() && auth()->user()->school_id) {
        $currentSchool = \App\Models\School::find(auth()->user()->school_id);
    } else {
        // You can implement domain detection here
        $currentSchool = \App\Models\School::where('is_active', true)->first();
        }
    }

    // Check if we should show custom landing page
    $showCustomLanding = $currentSchool && $currentSchool->show_landing_page;
@endphp

@if ($showCustomLanding)
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $currentSchool->meta_title ?? ($currentSchool->name ?? config('app.name')) }}</title>

        <!-- SEO Meta Tags -->
        @if ($currentSchool->meta_description)
            <meta name="description" content="{{ $currentSchool->meta_description }}">
        @endif
        @if ($currentSchool->meta_keywords)
            <meta name="keywords" content="{{ $currentSchool->meta_keywords }}">
        @endif

        <!-- Favicon -->
        @if ($currentSchool->favicon)
            <link rel="icon" href="{{ asset('storage/' . $currentSchool->favicon) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dynamic Theme CSS -->
        @if ($currentSchool->theme)
            <style>
                {!! $currentSchool->theme->generateCSS() !!}
            </style>
        @endif
    </head>

    <body class="antialiased bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white/95 backdrop-blur-sm shadow-sm sticky top-0 z-50 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center space-x-3">
                            @if ($currentSchool->logo)
                                <img src="{{ asset('storage/' . $currentSchool->logo) }}"
                                    alt="{{ $currentSchool->name }}" class="h-10 w-auto">
                            @else
                                <span class="text-xl font-bold text-gray-900">{{ $currentSchool->name }}</span>
                            @endif
                        </a>
                    </div>

                    <!-- Auth Links -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('forum.index') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-comments mr-1"></i>Forum
                        </a>
                        <a href="{{ route('guest.exams.index') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-clipboard-list mr-1"></i>Guest Exam
                        </a>
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-th-large mr-1"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-24 md:py-32 overflow-hidden">
            @if ($currentSchool->hero_image)
                <div class="absolute inset-0">
                    <img src="{{ asset('storage/' . $currentSchool->hero_image) }}" alt="Hero"
                        class="w-full h-full object-cover opacity-20">
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 to-indigo-800/90"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        {{ $currentSchool->hero_title ?? 'Welcome to ' . $currentSchool->name }}
                    </h1>
                    @if ($currentSchool->hero_subtitle)
                        <p class="text-xl md:text-2xl mb-6 text-blue-100 font-medium">
                            {{ $currentSchool->hero_subtitle }}
                        </p>
                    @endif
                    @if ($currentSchool->hero_description)
                        <p class="text-lg md:text-xl mb-8 text-blue-50 leading-relaxed">
                            {{ $currentSchool->hero_description }}
                        </p>
                    @endif
                    @if ($currentSchool->hero_cta_link)
                        <a href="{{ $currentSchool->hero_cta_link }}"
                            class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold text-lg rounded-lg hover:bg-gray-50 transition-all transform hover:scale-105 shadow-xl hover:shadow-2xl">
                            {{ $currentSchool->hero_cta_text ?? 'Get Started' }}
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        @if ($currentSchool->statistics && count($currentSchool->statistics) > 0)
            <section class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                        @foreach ($currentSchool->statistics as $stat)
                            <div
                                class="text-center p-6 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 hover:shadow-lg transition-shadow">
                                <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">
                                    {{ $stat['value'] }}
                                </div>
                                <div class="text-sm md:text-base text-gray-700 font-medium">
                                    {{ $stat['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Features Section -->
        @if ($currentSchool->features && count($currentSchool->features) > 0)
            <section class="py-20 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Why Choose Us?</h2>
                        <p class="text-lg text-gray-600">Discover what makes us special</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($currentSchool->features as $feature)
                            <div
                                class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200">
                                <div class="text-4xl text-blue-600 mb-4">
                                    <i class="fas {{ $feature['icon'] ?? 'fa-star' }}"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $feature['title'] }}
                                </h3>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ $feature['description'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- About Section -->
        @if ($currentSchool->about_title || $currentSchool->about_content)
            <section class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        @if ($currentSchool->about_image)
                            <div class="order-2 lg:order-1">
                                <img src="{{ asset('storage/' . $currentSchool->about_image) }}" alt="About us"
                                    class="rounded-2xl shadow-xl w-full h-auto object-cover">
                            </div>
                        @endif
                        <div class="order-1 lg:order-2 {{ $currentSchool->about_image ? '' : 'lg:col-span-2' }}">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                                {{ $currentSchool->about_title ?? 'About Us' }}
                            </h2>
                            <div class="text-base md:text-lg text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $currentSchool->about_content }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Contact Section -->
        @if ($currentSchool->contact_email || $currentSchool->contact_phone || $currentSchool->contact_address)
            <section class="py-20 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Get in Touch</h2>
                        <p class="text-lg text-gray-600">We'd love to hear from you</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                        @if ($currentSchool->contact_email)
                            <div
                                class="text-center bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                                <div class="text-3xl text-blue-600 mb-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                                <a href="mailto:{{ $currentSchool->contact_email }}"
                                    class="text-sm text-gray-600 hover:text-blue-600 transition-colors break-all">
                                    {{ $currentSchool->contact_email }}
                                </a>
                            </div>
                        @endif

                        @if ($currentSchool->contact_phone)
                            <div
                                class="text-center bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                                <div class="text-3xl text-blue-600 mb-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Phone</h3>
                                <a href="tel:{{ $currentSchool->contact_phone }}"
                                    class="text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                    {{ $currentSchool->contact_phone }}
                                </a>
                            </div>
                        @endif

                        @if ($currentSchool->contact_address)
                            <div
                                class="text-center bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                                <div class="text-3xl text-blue-600 mb-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Address</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ $currentSchool->contact_address }}
                                </p>
                            </div>
                        @endif

                        @if ($currentSchool->contact_whatsapp)
                            <div
                                class="text-center bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                                <div class="text-3xl text-green-600 mb-3">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $currentSchool->contact_whatsapp) }}"
                                    target="_blank"
                                    class="text-sm text-gray-600 hover:text-green-600 transition-colors">
                                    {{ $currentSchool->contact_whatsapp }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- School Info -->
                    <div>
                        @if ($currentSchool->logo)
                            <img src="{{ asset('storage/' . $currentSchool->logo) }}"
                                alt="{{ $currentSchool->name }}" class="h-10 mb-4">
                        @else
                            <h3 class="text-xl font-bold mb-4">{{ $currentSchool->name }}</h3>
                        @endif
                        @if ($currentSchool->about_content)
                            <p class="text-sm text-gray-400 leading-relaxed">
                                {{ Str::limit($currentSchool->about_content, 120) }}
                            </p>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-base font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('login') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guest.exams.index') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-clipboard-list mr-2"></i>Guest Exam
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('forum.index') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-comments mr-2"></i>Forum
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Social Media -->
                    @if (
                        $currentSchool->social_facebook ||
                            $currentSchool->social_instagram ||
                            $currentSchool->social_twitter ||
                            $currentSchool->social_youtube)
                        <div>
                            <h4 class="text-base font-semibold mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                @if ($currentSchool->social_facebook)
                                    <a href="{{ $currentSchool->social_facebook }}" target="_blank"
                                        class="text-xl text-gray-400 hover:text-blue-500 transition-colors">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_instagram)
                                    <a href="{{ $currentSchool->social_instagram }}" target="_blank"
                                        class="text-xl text-gray-400 hover:text-pink-500 transition-colors">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_twitter)
                                    <a href="{{ $currentSchool->social_twitter }}" target="_blank"
                                        class="text-xl text-gray-400 hover:text-blue-400 transition-colors">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_youtube)
                                    <a href="{{ $currentSchool->social_youtube }}" target="_blank"
                                        class="text-xl text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-800 pt-8 text-center">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} {{ $currentSchool->name }}. All rights reserved.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">Powered by {{ config('app.name') }}</p>
                </div>
            </div>
        </footer>
    </body>

    </html>
@else
    {{-- Default Laravel Welcome Page (when landing page is disabled) --}}
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body
        class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row text-center">
                <div
                    class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
                    <h1 class="mb-1 font-medium text-2xl">Welcome to {{ config('app.name') }}</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">A modern Learning Management System built with
                        Laravel</p>
                    <p class="text-sm text-gray-500 mt-4">
                        @if (auth()->check() && auth()->user()->isAdmin())
                            <a href="{{ route('admin.schools.index') }}" class="text-blue-600 hover:underline">
                                Configure landing page in School Settings
                            </a>
                        @endif
                    </p>
                </div>
            </main>
        </div>
    </body>

    </html>
@endif
