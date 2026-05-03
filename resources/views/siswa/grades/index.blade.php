<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>Nilai & Prestasi Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <form method="GET" action="{{ route('siswa.grades.index') }}" class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book text-gray-400 mr-1"></i>Filter Kursus
                        </label>
                        <select name="course_id" id="course_id"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Semua Kursus</option>
                            @foreach ($enrolledCourses as $course)
                                <option value="{{ $course->id }}"
                                    {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-search"></i>Filter
                    </button>
                    @if (request('course_id'))
                        <a href="{{ route('siswa.grades.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-times"></i>Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Total Kursus</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_courses'] ?? 0 }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-book text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Rata-rata Nilai</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['average_grade'] ?? 0, 1) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Nilai Tertinggi</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['highest_grade'] ?? 0, 1) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Nilai Terendah</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['lowest_grade'] ?? 0, 1) }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-arrow-down text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades by Course -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-list text-purple-600 mr-2"></i>Nilai per Kursus
                    </h3>
                </div>

                @if ($grades->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach ($grades as $grade)
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $grade->course->title }}</h4>
                                            @if ($grade->final_grade >= 85)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-star"></i>Excellent
                                                </span>
                                            @elseif($grade->final_grade >= 70)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    <i class="fas fa-check"></i>Good
                                                </span>
                                            @elseif($grade->final_grade >= 60)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-minus"></i>Fair
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fas fa-times"></i>Needs Improvement
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                                            <div class="text-sm">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-user text-purple-600 mr-1"></i>Instruktur:
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $grade->course->instructor->name }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-clipboard-list text-purple-600 mr-1"></i>Ujian:
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $grade->total_exams ?? 0 }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-check-circle text-purple-600 mr-1"></i>Selesai:
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $grade->completed_exams ?? 0 }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-calendar text-purple-600 mr-1"></i>Terakhir:
                                                </span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $grade->last_activity ? $grade->last_activity->format('d M Y') : '-' }}</span>
                                            </div>
                                        </div>

                                        <!-- Grade Progress Bar -->
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600 font-semibold">Nilai Akhir</span>
                                                <span
                                                    class="font-bold {{ $grade->final_grade >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($grade->final_grade, 2) }}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-3">
                                                <div class="h-3 rounded-full transition-all duration-300 {{ $grade->final_grade >= 85 ? 'bg-green-600' : ($grade->final_grade >= 70 ? 'bg-blue-600' : ($grade->final_grade >= 60 ? 'bg-yellow-600' : 'bg-red-600')) }}"
                                                    style="width: {{ min($grade->final_grade, 100) }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('siswa.grades.show', $grade) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
                                            <i class="fas fa-eye"></i>Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t">
                        {{ $grades->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Nilai</h3>
                        <p class="text-gray-500 mb-4">Ikuti kursus dan selesaikan ujian untuk melihat nilai Anda.</p>
                        <a href="{{ route('siswa.courses.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-book"></i>Lihat Kursus
                        </a>
                    </div>
                @endif
            </div>

            <!-- Grade Legend -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>Keterangan Nilai
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 rounded-lg p-3">
                            <i class="fas fa-star text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Excellent</div>
                            <div class="text-sm text-gray-600">85 - 100</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-check text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Good</div>
                            <div class="text-sm text-gray-600">70 - 84</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-100 rounded-lg p-3">
                            <i class="fas fa-minus text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Fair</div>
                            <div class="text-sm text-gray-600">60 - 69</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 rounded-lg p-3">
                            <i class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Needs Improvement</div>
                            <div class="text-sm text-gray-600">0 - 59</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
