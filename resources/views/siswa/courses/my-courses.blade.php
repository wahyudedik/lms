<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Courses') }}
            </h2>
            <a href="{{ route('siswa.courses.index') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-book text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">{{ __('Total Courses') }}</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $enrollments->total() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-play text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Sedang Berjalan</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $enrollments->where('status', 'active')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-check text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Selesai</p>
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
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
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                    @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $enrollment->status_display }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $enrollment->course->code }}</span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                {{ $enrollment->course->title }}</h3>

                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <i class="fas fa-user-tie mr-2"></i>
                                <span>{{ $enrollment->course->instructor->name }}</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600">Progress</span>
                                    <span class="text-xs font-bold text-gray-900">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-{{ $enrollment->progress_color }}-600 h-2 rounded-full"
                                        style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                            </div>

                            <div class="text-xs text-gray-500 mb-4">
                                <i class="fas fa-calendar mr-1"></i>
                                Bergabung: {{ $enrollment->enrolled_at->format('d M Y') }}
                            </div>

                            <a href="{{ route('siswa.courses.show', $enrollment->course) }}"
                                class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                <i class="fas fa-arrow-right mr-2"></i>{{ __('View Course') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-12 text-center">
                                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('No Courses Yet') }}</h3>
                                <p class="text-gray-500 mb-6">Anda belum terdaftar di kelas manapun</p>
                                <a href="{{ route('siswa.courses.index') }}"
                                    class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                    <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
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
