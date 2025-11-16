<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 {{ __('Available Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter by Course -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('siswa.exams.index') }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Kursus</label>
                            <select name="course_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </form>
                </div>
            </div>

            @if ($exams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($exams as $exam)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                            <div class="p-6">
                                <!-- Course Badge -->
                                <div class="mb-3">
                                    <span
                                        class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        {{ $exam->course->title }}
                                    </span>
                                </div>

                                <!-- Exam Title -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
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
                                    <div class="flex items-center">
                                        <i class="fas fa-clock w-5 text-gray-400"></i>
                                        <span>{{ $exam->duration_minutes }} menit</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clipboard-question w-5 text-gray-400"></i>
                                        <span>{{ $exam->total_questions }} soal</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-trophy w-5 text-gray-400"></i>
                                        <span>{{ __('Pass score: :score%', ['score' => $exam->pass_score]) }}</span>
                                    </div>

                                    @if ($exam->end_time)
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-xmark w-5 text-gray-400"></i>
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
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                        <div class="text-sm font-medium text-gray-700 mb-1">
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
                                            <div class="text-sm text-yellow-600 font-medium">
                                                <i class="fas fa-spinner mr-1"></i>Sedang dikerjakan
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('siswa.exams.show', $exam) }}"
                                        class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                        <i class="fas fa-eye mr-1"></i>{{ __('View Details') }}
                                    </a>

                                    @if ($attemptCount > 0 && $latestAttempt && $latestAttempt->status === 'in_progress')
                                        <a href="{{ route('siswa.exams.take', $latestAttempt) }}"
                                            class="flex-1 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                                            <i class="fas fa-play mr-1"></i>Lanjutkan
                                        </a>
                                    @elseif ($attemptCount < $exam->max_attempts)
                                        <a href="{{ route('siswa.exams.show', $exam) }}"
                                            class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                            <i class="fas fa-pencil mr-1"></i>Mulai
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
                        <p class="text-gray-400 text-sm">{{ __('Exams will appear here when published by the teacher.') }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
