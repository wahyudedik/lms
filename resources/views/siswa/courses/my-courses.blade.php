<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book-open mr-2"></i>{{ __('My Courses') }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-search"></i>
                {{ __('Browse Courses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-blue-600">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-book text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">{{ __('Total Courses') }}</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $enrollments->total() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-green-600">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-play text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Sedang Berjalan</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $enrollments->where('status', 'active')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-purple-600">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i class="fas fa-check text-purple-600 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Selesai</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $enrollments->where('status', 'completed')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($enrollments as $enrollment)
                    <div
                        class="bg-white overflow-hidden shadow-md rounded-lg hover:shadow-xl transition-all duration-200">
                        @if ($enrollment->course->cover_image)
                            <img src="{{ Storage::url($enrollment->course->cover_image) }}" alt="Cover"
                                class="w-full h-48 object-cover">
                        @else
                            <div
                                class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-6xl"></i>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                    @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ $enrollment->status_display }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    <i class="fas fa-code mr-1"></i>
                                    {{ $enrollment->course->code }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                {{ $enrollment->course->title }}</h3>

                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                <span>{{ $enrollment->course->instructor->name }}</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-gray-600">Progress</span>
                                    <span class="text-xs font-bold text-gray-900">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-{{ $enrollment->progress_color }}-600 h-2.5 rounded-full transition-all duration-300"
                                        style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                            </div>

                            <div class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                                <i class="fas fa-calendar text-gray-400"></i>
                                Bergabung: {{ $enrollment->enrolled_at->format('d M Y') }}
                            </div>

                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $enrollment->course) }}"
                                class="block w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                                <i class="fas fa-arrow-right"></i>
                                {{ __('View Course') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white overflow-hidden shadow-md rounded-lg">
                            <div class="p-12 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No Courses Yet') }}</h3>
                                <p class="text-gray-500 text-sm mb-6">Anda belum terdaftar di kelas manapun</p>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-search"></i>
                                    {{ __('Browse Courses') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
