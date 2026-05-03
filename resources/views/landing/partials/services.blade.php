<section id="services" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Layanan Kami</h2>
            <p class="text-gray-600 text-lg">Fitur-fitur unggulan yang kami tawarkan</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($school->features as $feature)
                <div
                    class="bg-white rounded-lg shadow-md p-8 hover:shadow-xl transition-shadow duration-300 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas {{ $feature['icon'] }} text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Statistics Section --}}
        @if ($school->statistics && count($school->statistics) > 0)
            <div class="mt-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 md:p-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                    @foreach ($school->statistics as $stat)
                        <div>
                            <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stat['value'] }}</div>
                            <div class="text-blue-100 text-sm md:text-base">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
