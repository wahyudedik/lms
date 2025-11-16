<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Browse Courses') }}
            </h2>
            <a href="{{ route('siswa.courses.my-courses') }}"
                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-book mr-2"></i>{{ __('My Courses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enroll by Code -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Enroll with Course Code') }}</h3>
                    <form method="POST" action="{{ route('siswa.courses.enroll-by-code') }}" class="flex gap-4">
                        @csrf
                        <input type="text" name="code" placeholder="Masukkan kode kelas (contoh: MTK001)" required
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Enroll') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('siswa.courses.index') }}">
                        <div class="flex gap-4">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kelas berdasarkan nama, deskripsi, atau pengajar..."
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-search mr-2"></i>Cari
                            </button>
                            @if (request('search'))
                                <a href="{{ route('siswa.courses.index') }}"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($courses as $course)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                        @if ($course->cover_image)
                            <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                class="w-full h-48 object-cover">
                        @else
                            <div
                                class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-6xl"></i>
                            </div>
                        @endif

                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>

                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <i class="fas fa-user-tie mr-2"></i>
                                <span>{{ $course->instructor->name }}</span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $course->description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $course->activeEnrollmentsCount() }} siswa
                                </span>
                                <span class="text-xs font-semibold px-2 py-1 rounded bg-blue-100 text-blue-800">
                                    {{ $course->code }}
                                </span>
                            </div>

                            <a href="{{ route('siswa.courses.show', $course) }}"
                                class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                <i class="fas fa-arrow-right mr-2"></i>{{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-12 text-center">
                                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-lg">{{ __('No courses found') }}</p>
                                @if (request('search'))
                                    <a href="{{ route('siswa.courses.index') }}"
                                        class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                        Tampilkan semua kelas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
