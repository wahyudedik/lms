<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>Detail Nilai - {{ $grade->course->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.grades.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                <i class="fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Course Header -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 border-l-4 border-purple-500 p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $grade->course->title }}</h1>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-user text-purple-600 mr-1"></i>
                                    <span class="font-semibold">Instruktur:</span>
                                </span>
                                <span class="text-gray-900">{{ $grade->course->instructor->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-calendar text-purple-600 mr-1"></i>
                                    <span class="font-semibold">Terdaftar:</span>
                                </span>
                                <span class="text-gray-900">{{ $grade->enrolled_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-clock text-purple-600 mr-1"></i>
                                    <span class="font-semibold">Terakhir Aktif:</span>
                                </span>
                                <span
                                    class="text-gray-900">{{ $grade->last_activity ? $grade->last_activity->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Grade Badge -->
                    <div class="text-center bg-purple-50 rounded-lg p-4 border-2 border-purple-500">
                        <div class="text-sm text-gray-600 mb-1">Nilai Akhir</div>
                        <div
                            class="text-4xl font-bold {{ $grade->final_grade >= 70 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($grade->final_grade, 1) }}
                        </div>
                        @if ($grade->final_grade >= 85)
                            <div class="text-xs text-green-600 font-semibold mt-1">Excellent</div>
                        @elseif($grade->final_grade >= 70)
                            <div class="text-xs text-blue-600 font-semibold mt-1">Good</div>
                        @elseif($grade->final_grade >= 60)
                            <div class="text-xs text-yellow-600 font-semibold mt-1">Fair</div>
                        @else
                            <div class="text-xs text-red-600 font-semibold mt-1">Needs Improvement</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Total Ujian</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $examGrades->count() }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Lulus</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $examGrades->where('passed', true)->count() }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Rata-rata</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($examGrades->avg('score'), 1) }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-star text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Progress</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($grade->progress ?? 0, 0) }}%</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <i class="fas fa-chart-line text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Grades Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-list-check text-purple-600 mr-2"></i>Rincian Nilai Ujian
                    </h3>
                </div>

                @if ($examGrades->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                        Nama Ujian</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                        Nilai</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                        Passing Grade</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($examGrades as $index => $examGrade)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-semibold">{{ $examGrade->exam->title }}</div>
                                            @if ($examGrade->exam->description)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($examGrade->exam->description, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                            {{ $examGrade->submitted_at ? $examGrade->submitted_at->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="text-lg font-bold {{ $examGrade->passed ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($examGrade->score, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                            {{ $examGrade->exam->pass_score }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($examGrade->passed)
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle"></i>Lulus
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle"></i>Tidak Lulus
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.review-attempt', $examGrade) }}"
                                                class="text-purple-600 hover:text-purple-900 font-medium">
                                                <i class="fas fa-eye mr-1"></i>Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada nilai ujian untuk kursus ini.</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $grade->course) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-book"></i>Lihat Kursus
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.reports.my-transcript', ['course_id' => $grade->course->id]) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-file-alt"></i>Lihat Transkrip
                    </a>
                    @if ($grade->certificate)
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.certificates.show', $grade->certificate) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-certificate"></i>Lihat Sertifikat
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
