<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="h-12 w-auto">
                <span class="text-xl font-bold text-gray-800 hidden sm:block">{{ $school->name }}</span>
            </div>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-6">
                <a href="#home" class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Beranda</a>
                <a href="#courses" class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Kursus</a>
                <a href="#services" class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Layanan</a>
                <a href="#contact" class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Kontak</a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Login
                    </a>
                @endauth
            </nav>

            {{-- Contact Info (Desktop) --}}
            @if ($school->contact_phone)
                <div class="hidden md:flex items-center gap-2 text-gray-700">
                    <i class="fas fa-phone text-blue-600"></i>
                    <span class="font-medium">{{ $school->contact_phone }}</span>
                </div>
            @endif

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-button" class="lg:hidden text-gray-700 hover:text-blue-600 text-2xl">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        {{-- Mobile Navigation --}}
        <nav id="mobile-menu" class="hidden lg:hidden mt-4 pb-4 border-t border-gray-200 pt-4">
            <div class="flex flex-col gap-3">
                <a href="#home"
                    class="text-gray-700 hover:text-blue-600 transition-colors font-medium py-2">Beranda</a>
                <a href="#courses"
                    class="text-gray-700 hover:text-blue-600 transition-colors font-medium py-2">Kursus</a>
                <a href="#services"
                    class="text-gray-700 hover:text-blue-600 transition-colors font-medium py-2">Layanan</a>
                <a href="#contact"
                    class="text-gray-700 hover:text-blue-600 transition-colors font-medium py-2">Kontak</a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-center">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-center">
                        Login
                    </a>
                @endauth
            </div>
        </nav>
    </div>
</header>

@push('scripts')
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
@endpush
