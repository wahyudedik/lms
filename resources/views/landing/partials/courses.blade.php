<section id="courses" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Kursus Populer</h2>
            <p class="text-gray-600 text-lg">Jelajahi kursus-kursus terbaik kami</p>
        </div>

        @if ($school->courses && $school->courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($school->courses as $course)
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
                        {{-- Course Cover Image --}}
                        <div class="relative h-48 bg-gray-200 overflow-hidden">
                            @if ($course->cover_image)
                                <img src="{{ asset('storage/' . $course->cover_image) }}" alt="{{ $course->title }}"
                                    class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-500">
                                    <i class="fas fa-book text-white text-6xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Course Content --}}
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                                {{ $course->title }}
                            </h3>

                            @if ($course->description)
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ Str::limit($course->description, 120) }}
                                </p>
                            @endif

                            {{-- Instructor --}}
                            @if ($course->instructor)
                                <div class="flex items-center gap-2 mb-4 text-sm text-gray-500">
                                    <i class="fas fa-user-tie"></i>
                                    <span>{{ $course->instructor->name }}</span>
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="text-lg font-bold text-blue-600">
                                    @if ($course->price && $course->price > 0)
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    @else
                                        <span class="text-green-600">Gratis</span>
                                    @endif
                                </div>
                                @auth
                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                        Daftar
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All Courses Button --}}
            @auth
                <div class="text-center mt-12">
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                        class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Lihat Semua Kursus
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @endauth
        @else
            <div class="text-center py-12">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada kursus tersedia</h3>
                <p class="text-gray-500">Kursus akan segera hadir. Silakan cek kembali nanti!</p>
            </div>
        @endif
    </div>
</section>
