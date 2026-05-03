<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-clipboard-list mr-2"></i>{{ __('Available Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter by Course -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('siswa.exams.index') }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Filter by Kursus
                            </label>
                            <select name="course_id"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
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
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            @if ($exams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($exams as $exam)
                        <div
                            class="bg-white overflow-hidden shadow-md rounded-lg hover:shadow-xl transition-all duration-200">
                            <div class="p-6">
                                <!-- Course Badge -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-book mr-1"></i>
                                        {{ $exam->course->title }}
                                    </span>
                                </div>

                                <!-- Exam Title -->
                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    {{ $exam->title }}
                                </h3>

                                <!-- Description -->
                                @if ($exam->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $exam->description }}
                                    </p>
                                @endif

                                <!-- Exam Info -->
                                <div class="space-y-2 mb-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-blue-500"></i>
                                        <span>{{ $exam->duration_minutes }} menit</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clipboard-question text-purple-500"></i>
                                        <span>{{ $exam->total_questions }} soal</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-trophy text-yellow-500"></i>
                                        <span>{{ __('Pass score: :score%', ['score' => $exam->pass_score]) }}</span>
                                    </div>

                                    @if ($exam->end_time)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar-xmark text-red-500"></i>
                                            <span>Deadline: {{ $exam->end_time->format('d M Y, H:i') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- User's Attempts -->
                                @php
                                    $userAttempts = $exam->attempts->where('user_id', auth()->id());
                                    $attemptCount = $userAttempts->count();
                                    $latestAttempt = $userAttempts->sortByDesc('created_at')->first();
                                @endphp

                                @if ($attemptCount > 0)
                                    <div
                                        class="mb-4 p-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                                        <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-redo text-gray-500"></i>
                                            Percobaan Anda: {{ $attemptCount }}/{{ $exam->max_attempts }}
                                        </div>

                                        @if ($latestAttempt && $latestAttempt->status === 'completed')
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600">{{ __('Last Score:') }}</span>
                                                <span
                                                    class="text-lg font-bold {{ $latestAttempt->score >= $exam->pass_score ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($latestAttempt->score, 2) }}%
                                                </span>
                                            </div>
                                        @elseif ($latestAttempt && $latestAttempt->status === 'in_progress')
                                            <div class="text-sm text-yellow-600 font-semibold flex items-center gap-2">
                                                <i class="fas fa-spinner"></i>Sedang dikerjakan
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('siswa.exams.show', $exam) }}"
                                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                                        <i class="fas fa-eye"></i>
                                        {{ __('View Details') }}
                                    </a>

                                    @if ($attemptCount > 0 && $latestAttempt && $latestAttempt->status === 'in_progress')
                                        <a href="{{ route('siswa.exams.take', $latestAttempt) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                                            <i class="fas fa-play"></i>
                                            Lanjutkan
                                        </a>
                                    @elseif ($attemptCount < $exam->max_attempts)
                                        <a href="{{ route('siswa.exams.show', $exam) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                                            <i class="fas fa-pencil"></i>
                                            Mulai
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $exams->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">{{ __('No exams available at the moment.') }}</p>
                        <p class="text-gray-400 text-sm">
                            {{ __('Exams will appear here when published by the teacher.') }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
