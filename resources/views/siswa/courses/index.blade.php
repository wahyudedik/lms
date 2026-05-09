<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.my-courses') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-book"></i>
                {{ __('My Courses') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enroll by Code -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-key text-indigo-600"></i>
                        {{ __('Enroll with Course Code') }}
                    </h3>
                    <form method="POST" action="{{ route(auth()->user()->getRolePrefix() . '.courses.enroll-by-code') }}" class="flex gap-4">
                        @csrf
                        <input type="text" name="code" placeholder="Masukkan kode kelas (contoh: MTK001)" required
                            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-150">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-sign-in-alt"></i>
                            {{ __('Enroll') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}">
                        <div class="flex gap-4">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kelas berdasarkan nama, deskripsi, atau pengajar..."
                                class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-search"></i>
                                Cari
                            </button>
                            @if (request('search'))
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-times"></i>
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
                    <div
                        class="bg-white overflow-hidden shadow-md rounded-lg hover:shadow-xl transition-all duration-200">
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
                                <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                <span>{{ $course->instructor->name }}</span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $course->description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-users text-purple-500"></i>
                                    {{ $course->activeEnrollmentsCount() }} mahasiswa
                                </span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-code mr-1"></i>
                                    {{ $course->code }}
                                </span>
                            </div>

                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                                class="block w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                                <i class="fas fa-arrow-right"></i>
                                {{ __('View Details') }}
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
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No courses found') }}</h3>
                                <p class="text-gray-500 text-sm mb-4">Tidak ada kelas yang ditemukan</p>
                                @if (request('search'))
                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-list"></i>
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
