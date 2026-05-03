<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-book-reader text-indigo-600 mr-2"></i>Materi Pembelajaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <form method="GET" action="{{ route('siswa.materials.index') }}" class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book text-gray-400 mr-1"></i>Filter Kursus
                        </label>
                        <select name="course_id" id="course_id"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Kursus</option>
                            @foreach ($enrolledCourses as $course)
                                <option value="{{ $course->id }}"
                                    {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-filter text-gray-400 mr-1"></i>Tipe Materi
                        </label>
                        <select name="type" id="type"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Tipe</option>
                            <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="youtube" {{ request('type') == 'youtube' ? 'selected' : '' }}>YouTube
                            </option>
                            <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Link</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-search"></i>Filter
                    </button>
                    @if (request()->hasAny(['course_id', 'type']))
                        <a href="{{ route('siswa.materials.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-times"></i>Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Total Materi</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $materials->total() }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <i class="fas fa-book-reader text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">PDF</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pdf'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Video</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['video'] ?? 0 }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-video text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Link</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['link'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-link text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials List -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-list text-indigo-600 mr-2"></i>Daftar Materi
                    </h3>
                </div>

                @if ($materials->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach ($materials as $material)
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="text-lg font-bold text-gray-900">
                                                {{ $material->title }}
                                            </h4>
                                            @if ($material->type == 'pdf')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    <i class="fas fa-file-pdf"></i>PDF
                                                </span>
                                            @elseif($material->type == 'video')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fas fa-video"></i>Video
                                                </span>
                                            @elseif($material->type == 'youtube')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fab fa-youtube"></i>YouTube
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-link"></i>Link
                                                </span>
                                            @endif
                                            @if ($material->is_published)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle"></i>Published
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3">
                                            <i class="fas fa-book text-indigo-600 mr-1"></i>
                                            <span class="font-semibold">{{ $material->course->title }}</span>
                                        </p>

                                        @if ($material->description)
                                            <p class="text-sm text-gray-700 mb-3">
                                                {{ Str::limit($material->description, 150) }}</p>
                                        @endif

                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>
                                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                                {{ $material->course->instructor->name }}
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                                {{ $material->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('siswa.materials.show', $material) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition">
                                            <i class="fas fa-eye"></i>Lihat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t">
                        {{ $materials->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Materi</h3>
                        <p class="text-gray-500">Materi pembelajaran akan muncul di sini.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
