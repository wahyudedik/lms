<section id="home"
    class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-700 text-white overflow-hidden">
    {{-- Background Image --}}
    @if ($school->hero_image)
        <div class="absolute inset-0 z-0">
            <img src="{{ $school->hero_image_url }}" alt="Hero Background" class="w-full h-full object-cover opacity-20">
        </div>
    @endif

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/50 to-indigo-900/50 z-0"></div>

    {{-- Content --}}
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
                {{ $school->hero_title ?? 'Selamat Datang di Learning Management System' }}
            </h1>

            @if ($school->hero_subtitle)
                <p class="text-xl md:text-2xl mb-6 text-blue-100 animate-fade-in-up animation-delay-200">
                    {{ $school->hero_subtitle }}
                </p>
            @endif

            @if ($school->hero_description)
                <p class="text-lg mb-8 text-blue-50 max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
                    {{ $school->hero_description }}
                </p>
            @endif

            @if ($school->hero_cta_link)
                <a href="{{ $school->hero_cta_link }}"
                    class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-all text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 animate-fade-in-up animation-delay-600">
                    {{ $school->hero_cta_text ?? 'Mulai Sekarang' }}
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Scroll Down Indicator --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10 animate-bounce">
        <a href="#courses" class="text-white text-3xl">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
    }

    .animation-delay-600 {
        animation-delay: 0.6s;
    }
</style>
