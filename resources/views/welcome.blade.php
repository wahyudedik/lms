@php
    // Detect school from domain or use default
    $currentSchool = null;
    try {
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
    } catch (\Exception $e) {
        // Table might not exist - continue without school
        $currentSchool = null;
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
                            <i class="fas fa-comments mr-1"></i>@lang('Forum')
                        </a>
                        <a href="{{ route('guest.exams.index') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-clipboard-list mr-1"></i>@lang('Guest Exam')
                        </a>
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-th-large mr-1"></i>@lang('Dashboard')
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                <i class="fas fa-sign-in-alt mr-1"></i>@lang('Login')
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
                        {{ $currentSchool->hero_title ?? __('Welcome to :name', ['name' => 'Koneksi']) }}
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
                            {{ $currentSchool->hero_cta_text ?? __('Get Started') }}
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
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">@lang('Why Choose Us?')</h2>
                        <p class="text-lg text-gray-600">@lang('Discover what makes us special')</p>
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
                                {{ $currentSchool->about_title ?? __('About Us') }}
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
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">@lang('Get in Touch')</h2>
                        <p class="text-lg text-gray-600">@lang("We'd love to hear from you")</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                        @if ($currentSchool->contact_email)
                            <div
                                class="text-center bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                                <div class="text-3xl text-blue-600 mb-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">@lang('Email')</h3>
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">@lang('Phone')</h3>
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">@lang('Address')</h3>
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">@lang('WhatsApp')</h3>
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
        <footer class="bg-[#0F172A] text-slate-200 py-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    <!-- School Info -->
                    <div class="space-y-4">
                        @if ($currentSchool->logo)
                            <img src="{{ asset('storage/' . $currentSchool->logo) }}"
                                alt="{{ $currentSchool->name }}" class="h-10 mb-2">
                        @else
                            <h3 class="text-2xl font-bold text-white tracking-tight">{{ $currentSchool->name }}</h3>
                        @endif
                        @if ($currentSchool->about_content)
                            <p class="text-sm text-slate-300 leading-relaxed max-w-sm">
                                {{ Str::limit($currentSchool->about_content, 140) }}
                            </p>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-5 relative inline-block">
                            @lang('Quick Links')
                            <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-blue-500 rounded-full"></span>
                        </h4>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('login') }}"
                                    class="group flex items-center text-sm text-slate-300 hover:text-white transition-all duration-200">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center mr-3 group-hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-sign-in-alt text-xs"></i>
                                    </span>
                                    @lang('Login')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guest.exams.index') }}"
                                    class="group flex items-center text-sm text-slate-300 hover:text-white transition-all duration-200">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center mr-3 group-hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-clipboard-list text-xs"></i>
                                    </span>
                                    @lang('Guest Exam')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('forum.index') }}"
                                    class="group flex items-center text-sm text-slate-300 hover:text-white transition-all duration-200">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center mr-3 group-hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-comments text-xs"></i>
                                    </span>
                                    @lang('Forum')
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
                            <h4 class="text-lg font-semibold text-white mb-5 relative inline-block">
                                @lang('Follow Us')
                                <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-blue-500 rounded-full"></span>
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                @if ($currentSchool->social_facebook)
                                    <a href="{{ $currentSchool->social_facebook }}" target="_blank"
                                        class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-[#1877F2] hover:text-white transition-all duration-300 shadow-sm hover:shadow-blue-500/30"
                                        aria-label="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_instagram)
                                    <a href="{{ $currentSchool->social_instagram }}" target="_blank"
                                        class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-gradient-to-br hover:from-purple-500 hover:to-pink-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-pink-500/30"
                                        aria-label="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_twitter)
                                    <a href="{{ $currentSchool->social_twitter }}" target="_blank"
                                        class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-[#1DA1F2] hover:text-white transition-all duration-300 shadow-sm hover:shadow-blue-400/30"
                                        aria-label="Twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                @endif
                                @if ($currentSchool->social_youtube)
                                    <a href="{{ $currentSchool->social_youtube }}" target="_blank"
                                        class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-[#FF0000] hover:text-white transition-all duration-300 shadow-sm hover:shadow-red-500/30"
                                        aria-label="YouTube">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                @endif
                            </div>
                            <p class="mt-6 text-sm text-slate-400">
                                @lang('Stay connected with us for the latest updates and educational resources.')
                            </p>
                        </div>
                    @endif
                </div>

                <div
                    class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-slate-400 text-center md:text-left">
                        &copy; {{ date('Y') }} <span
                            class="text-slate-200 font-medium">{{ $currentSchool->name }}</span>. @lang('All rights reserved.')
                    </p>
                    <p class="text-xs text-slate-500 flex items-center gap-1">
                        <span>@lang('Powered by')</span>
                        <span class="font-semibold text-blue-500">{{ config('app.name') }}</span>
                    </p>
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

        <title>Koneksi (Kolaborasi Online Edukasi dan Komunikasi Siswa)</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="antialiased font-sans text-gray-900 bg-gray-50">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <div
                class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <div class="flex flex-col items-center">
                                <span class="text-4xl font-bold text-blue-600 mb-2">Koneksi</span>
                                <span class="text-sm font-medium text-gray-600 text-center">Kolaborasi Online Edukasi
                                    dan Komunikasi Siswa</span>
                            </div>
                        </div>
                        @if (Route::has('login'))
                            <nav class="flex items-center justify-end gap-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                        @lang('Dashboard')
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                        @lang('Log in')
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                            @lang('Register')
                                        </a>
                                    @endif
                                @endauth
                            </nav>
                        @endif
                    </header>
                    <div
                        class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
                        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row text-center">
                            <div
                                class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
                                <h1 class="mb-1 font-medium text-2xl">
                                    {{ __('Welcome to :name', ['name' => config('app.name')]) }}
                                </h1>
                                <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                                    @lang('A modern Learning Management System built with Laravel')
                                </p>
                                <p class="text-sm text-gray-500 mt-4">
                                    @if (auth()->check() && auth()->user()->isAdmin())
                                        <a href="{{ route('admin.schools.index') }}"
                                            class="text-blue-600 hover:underline">
                                            @lang('Configure landing page in School Settings')
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </main>
                    </div>
    </body>

    </html>
@endif
