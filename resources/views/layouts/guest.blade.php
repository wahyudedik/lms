<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 px-4">
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center space-y-2">
                    {{-- <x-application-logo class="w-16 h-16 fill-current text-blue-600" /> --}}
                    <span class="text-3xl font-bold text-gray-900">Koneksi</span> 
                    <span class="text-sm font-medium text-gray-600">Kolaborasi Online Edukasi dan Komunikasi Siswa</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="px-6 py-8 sm:px-8">
                    {{ $slot }}
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </body>
</html>
