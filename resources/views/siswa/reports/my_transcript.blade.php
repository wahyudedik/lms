@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-alt text-purple-600 mr-2"></i>{{ __('My Grade Transcript') }}
            </h1>
            <p class="text-gray-600">{{ __('View and export your exam grade transcript') }}</p>
        </div>

        <!-- Course Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('siswa.reports.my-transcript') }}" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-book mr-1"></i>Pilih Kursus
                    </label>
                    <select name="course_id" id="course_id"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required>
                        <option value="">-- Pilih Kursus --</option>
                        @foreach ($enrolledCourses as $enrolledCourse)
                            <option value="{{ $enrolledCourse->id }}"
                                {{ request('course_id') == $enrolledCourse->id ? 'selected' : '' }}>
                                {{ $enrolledCourse->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search mr-2"></i>{{ __('View Transcript') }}
                </button>
            </form>
        </div>

        @if ($course)
            <!-- Course Info Card -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">{{ $course->title }}</h2>
                        <p class="opacity-90 mb-4">{{ $course->description }}</p>
                        <div class="flex items-center gap-4 text-sm">
                            <span><i class="fas fa-user-tie mr-1"></i>{{ $course->instructor->name }}</span>
                            <span><i class="fas fa-clipboard-list mr-1"></i>{{ $exams->count() }} {{ __('Exams') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('siswa.reports.my-transcript-pdf', $course) }}"
                        class="px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition shadow-md">
                        <i class="fas fa-download mr-2"></i>Export PDF
                    </a>
                </div>
            </div>

            @if ($exams->count() > 0)
                <!-- Summary Statistics -->
                @php
                    $totalExams = $exams->count();
                    $completedExams = $exams->filter(fn($exam) => $exam->attempts->count() > 0)->count();
                    $passedExams = $exams->filter(fn($exam) => $exam->attempts->first()?->passed ?? false)->count();
                    $totalScore = $exams
                        ->filter(fn($exam) => $exam->attempts->count() > 0)
                        ->sum(fn($exam) => $exam->attempts->first()->score ?? 0);
                    $avgScore = $completedExams > 0 ? $totalScore / $completedExams : 0;
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <i class="fas fa-clipboard-list text-blue-500 text-3xl mb-2"></i>
                        <div class="text-2xl font-bold text-gray-800">{{ $totalExams }}</div>
                        <div class="text-sm text-gray-600">{{ __('Total Exams') }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <div class="text-2xl font-bold text-gray-800">{{ $completedExams }}</div>
                        <div class="text-sm text-gray-600">{{ __('Completed') }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <i class="fas fa-trophy text-yellow-500 text-3xl mb-2"></i>
                        <div class="text-2xl font-bold text-gray-800">{{ $passedExams }}</div>
                        <div class="text-sm text-gray-600">{{ __('Passed') }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <i class="fas fa-star text-purple-500 text-3xl mb-2"></i>
                        <div class="text-2xl font-bold text-gray-800">{{ number_format($avgScore, 1) }}%</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                    </div>
                </div>

                <!-- Exams List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-list text-indigo-600 mr-2"></i>{{ __('Exam & Grade List') }}
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Exam Name') }}</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Duration</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Pass Score') }}</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        My Score</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Points</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($exams as $index => $exam)
                                    @php
                                        $attempt = $exam->attempts->first();
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-semibold">{{ $exam->title }}</div>
                                            @if ($exam->description)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($exam->description, 60) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                            {{ $exam->duration_minutes }} minutes
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                            {{ $exam->pass_score }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($attempt)
                                                <span
                                                    class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($attempt->score, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                                                    Not Attempted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                            @if ($attempt)
                                                {{ number_format($attempt->total_points_earned, 2) }} /
                                                {{ number_format($attempt->total_points_possible, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($attempt)
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                            @if ($attempt && $attempt->submitted_at)
                                                {{ $attempt->submitted_at->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            @if ($attempt)
                                                <a href="{{ route('siswa.exams.review-attempt', $attempt) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                                    <i class="fas fa-eye mr-1"></i>Review
                                                </a>
                                            @else
                                                <a href="{{ route('siswa.exams.show', $exam) }}"
                                                    class="text-green-600 hover:text-green-800 font-medium">
                                                    <i class="fas fa-play mr-1"></i>Start
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- No Exams -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No Exams Yet') }}</h3>
                    <p class="text-gray-500">{{ __('This course has no exams yet.') }}</p>
                </div>
            @endif
        @else
            <!-- Select Course Prompt -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Select a Course') }}</h3>
                <p class="text-gray-500">{{ __('Please select a course above to view your grade transcript') }}</p>
            </div>
        @endif
    </div>
@endsection
