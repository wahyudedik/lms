<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'LMS') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'spin-slow': 'spin 12s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-15px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(243, 244, 246, 1) 0%, rgba(229, 231, 235, 1) 90%);
        }
        .error-card {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
    </style>
</head>

<body class="antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 overflow-x-hidden relative bg-gray-50">
    <!-- Background Decorative Elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
    
    <div class="w-full max-w-xl text-center relative z-10">
        <!-- Error Container Card -->
        <div class="error-card rounded-3xl shadow-2xl p-8 sm:p-12 transition-all duration-300 hover:shadow-3xl">
            <!-- Icon Wrapper -->
            <div class="mb-8 flex justify-center">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr @yield('icon-bg', 'from-blue-500 to-indigo-600') text-white flex items-center justify-center text-4xl shadow-lg shadow-indigo-100 animate-float">
                    @yield('icon')
                </div>
            </div>

            <!-- Error Code & Header -->
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest @yield('badge-bg', 'bg-indigo-50 text-indigo-700') mb-4">
                Error @yield('code')
            </span>
            
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                @yield('message')
            </h1>

            <p class="text-gray-600 text-sm sm:text-base mb-8 max-w-md mx-auto leading-relaxed">
                @yield('description')
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @yield('action')
                <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl bg-white hover:bg-gray-50 hover:border-gray-400 shadow-sm transition-all duration-150 text-sm">
                    <i class="fas fa-home"></i>
                    Kembali Ke Dashboard
                </a>
            </div>
        </div>

        <!-- System Footer Info -->
        <p class="mt-8 text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Learning Management System') }} - Hak Cipta Dilindungi.
        </p>
    </div>
</body>

</html>
