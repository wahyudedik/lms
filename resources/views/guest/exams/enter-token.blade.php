<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Exam Access') }} - {{ config('app.name', 'Laravel LMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        {{-- <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-white drop-shadow-lg" />
            </a>
        </div> --}}

        <!-- Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl">
            <div class="text-center mb-6">
                <i class="fas fa-ticket-alt text-5xl text-indigo-600 mb-3"></i>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Exam Access via Token') }}</h2>
                <p class="text-gray-600 mt-2">{{ __('Enter the exam token you received') }}</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('guest.exams.verify-token') }}">
                @csrf

                <!-- Token -->
                <div class="mb-6">
                    <label for="token" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Exam Token') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="token" type="text" name="token" value="{{ old('token') }}" maxlength="8"
                        required autofocus placeholder="{{ __('Example: ABC123XY') }}"
                        class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-center text-2xl font-mono tracking-widest uppercase focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 @error('token') border-red-500 @enderror">
                    @error('token')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 text-center">
                        <i class="fas fa-info-circle"></i> {{ __('Token must be 8 characters (letters/numbers)') }}
                    </p>
                </div>

                <div class="flex items-center justify-center mt-6">
                    <button type="submit"
                        class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Access Exam') }}
                    </button>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-question-circle text-indigo-600"></i> {{ __("Don't have a token?") }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Contact your teacher/admin to get the exam token') }}
                </p>
                <div class="mt-4">
                    <a href="/" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-home mr-1"></i> {{ __('Back to Home') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="w-full sm:max-w-md mt-6 text-white text-center">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <i class="fas fa-clock text-2xl mb-2"></i>
                    <p class="text-xs">{{ __('Quick Access') }}</p>
                </div>
                <div>
                    <i class="fas fa-shield-alt text-2xl mb-2"></i>
                    <p class="text-xs">{{ __('Secure & Reliable') }}</p>
                </div>
                <div>
                    <i class="fas fa-mobile-alt text-2xl mb-2"></i>
                    <p class="text-xs">{{ __('Mobile Friendly') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-uppercase and format token input
        document.getElementById('token').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });

        // Global toast for session messages
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        @endif
    </script>
</body>

</html>
