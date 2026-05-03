<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt text-purple-600 mr-2"></i>{{ __('My Grade Transcript') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ __('View and export your exam grade transcript') }}</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Course Filter -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <form method="GET" action="{{ route('siswa.reports.my-transcript') }}"
                    class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1 min-w-[250px]">
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book text-gray-400 mr-1"></i>Pilih Kursus
                        </label>
                        <select name="course_id" id="course_id"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-search"></i>{{ __('View Transcript') }}
                    </button>
                </form>
            </div>

            @if ($course)
                <!-- Course Info Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 border-l-4 border-purple-500 p-6">
                    <div class="flex items-start justify-between flex-wrap gap-4">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                <i class="fas fa-book-open text-purple-600 mr-2"></i>{{ $course->title }}
                            </h2>
                            <p class="text-gray-700 mb-4">{{ $course->description }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <span><i
                                        class="fas fa-user-tie text-purple-600 mr-1"></i>{{ $course->instructor->name }}</span>
                                <span><i class="fas fa-clipboard-list text-purple-600 mr-1"></i>{{ $exams->count() }}
                                    {{ __('Exams') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('siswa.reports.my-transcript-pdf', $course) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-file-pdf"></i>Export PDF
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

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div
                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center border-l-4 border-blue-500">
                            <div
                                class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalExams }}</div>
                            <div class="text-sm text-gray-600">{{ __('Total Exams') }}</div>
                        </div>
                        <div
                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center border-l-4 border-green-500">
                            <div
                                class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">{{ $completedExams }}</div>
                            <div class="text-sm text-gray-600">{{ __('Completed') }}</div>
                        </div>
                        <div
                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center border-l-4 border-yellow-500">
                            <div
                                class="bg-yellow-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">{{ $passedExams }}</div>
                            <div class="text-sm text-gray-600">{{ __('Passed') }}</div>
                        </div>
                        <div
                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center border-l-4 border-purple-500">
                            <div
                                class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-star text-purple-600 text-xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($avgScore, 1) }}%</div>
                            <div class="text-sm text-gray-600">{{ __('Average Score') }}</div>
                        </div>
                    </div>

                    <!-- Exams List -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-list text-indigo-600 mr-2"></i>{{ __('Exam & Grade List') }}
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            {{ __('Exam Name') }}</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            Duration</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            {{ __('Pass Score') }}</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            My Score</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            Points</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exams as $index => $exam)
                                        @php
                                            $attempt = $exam->attempts->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}</td>
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
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 bg-yellow-100 text-yellow-800 rounded-full font-semibold">
                                                        <i class="fas fa-clock"></i>Not Attempted
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
                                                        class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        <i
                                                            class="fas fa-{{ $attempt->passed ? 'check' : 'times' }}-circle"></i>
                                                        {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
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
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No Exams Yet') }}</h3>
                        <p class="text-gray-500">{{ __('This course has no exams yet.') }}</p>
                    </div>
                @endif
            @else
                <!-- Select Course Prompt -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Select a Course') }}</h3>
                    <p class="text-gray-500">{{ __('Please select a course above to view your grade transcript') }}
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
