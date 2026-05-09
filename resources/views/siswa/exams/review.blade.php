<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-eye text-blue-600 mr-2"></i>Review Hasil: {{ $exam->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.show', $exam) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                <i class="fas fa-arrow-left"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Score Summary -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Score -->
                        <div
                            class="text-center p-4 bg-gray-50 rounded-lg border-l-4 {{ $attempt->passed ? 'border-green-500' : 'border-red-500' }}">
                            <div class="flex items-center justify-center mb-2">
                                <i
                                    class="fas fa-{{ $attempt->passed ? 'check-circle text-green-600' : 'times-circle text-red-600' }} text-3xl"></i>
                            </div>
                            <div class="text-4xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($attempt->score, 2) }}%
                            </div>
                            <div class="text-sm text-gray-600 mt-1">{{ __('Your Score') }}</div>
                            <div class="mt-2">
                                @if ($attempt->passed)
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i>Lulus
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle"></i>Tidak Lulus
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Time Spent -->
                        <div class="text-center p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-clock text-blue-600 text-3xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ floor($attempt->time_spent / 60) }}:{{ str_pad($attempt->time_spent % 60, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">{{ __('Time Spent') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('from :duration minutes', ['duration' => $exam->duration_minutes]) }}
                            </div>
                        </div>

                        <!-- Points -->
                        <div class="text-center p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-star text-purple-600 text-3xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ number_format($attempt->total_points_earned, 2) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">{{ __('Points Earned') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('from :total_points possible', ['total_points' => number_format($attempt->total_points_possible, 2)]) }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="text-center p-4 bg-gray-50 rounded-lg border-l-4 border-indigo-500">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-check-double text-indigo-600 text-3xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $attempt->answers->where('is_correct', true)->count() }}/{{ $attempt->answers->count() }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">{{ __('Correct Answers') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('Pass score: :score%', ['score' => $exam->pass_score]) }}
                            </div>
                        </div>
                    </div>

                    @if ($attempt->violations && count($attempt->violations) > 0)
                        <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">{{ __('Violations Detected') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($attempt->violations as $violation)
                                                <li>
                                                    {{ ucfirst(str_replace('_', ' ', $violation['type'])) }} at
                                                    {{ \Carbon\Carbon::parse($violation['timestamp'])->format('H:i:s') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Questions Review -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">
                        <i class="fas fa-list-check text-indigo-600 mr-2"></i>{{ __('Review Answers') }}
                    </h3>

                    @foreach ($attempt->answers as $index => $answer)
                        @php
                            $question = $answer->question;
                        @endphp
                        <div class="mb-8 pb-8 {{ $loop->last ? '' : 'border-b' }}">
                            <!-- Question Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span
                                            class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-lg">
                                            <i
                                                class="fas fa-hashtag"></i>{{ __('Question :number', ['number' => $index + 1]) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-lg">
                                            <i class="{{ $question->type_icon }}"></i>{{ $question->type_display }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-sm font-semibold px-3 py-1 rounded-lg">
                                            <i class="fas fa-star"></i>{{ $question->points }} points
                                        </span>
                                    </div>
                                    <h4 class="text-base font-semibold text-gray-900">
                                        {{ $question->question_text }}
                                    </h4>
                                </div>
                                <div class="ml-4">
                                    @if ($answer->is_correct)
                                        <div
                                            class="flex items-center text-green-600 bg-green-50 px-4 py-2 rounded-lg border-l-4 border-green-500">
                                            <i class="fas fa-check-circle text-xl mr-2"></i>
                                            <div>
                                                <div class="font-bold">{{ __('Correct') }}</div>
                                                <div class="text-sm">
                                                    {{ __('+:points points', ['points' => number_format($answer->points_earned, 2)]) }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="flex items-center text-red-600 bg-red-50 px-4 py-2 rounded-lg border-l-4 border-red-500">
                                            <i class="fas fa-times-circle text-xl mr-2"></i>
                                            <div>
                                                <div class="font-bold">{{ __('Incorrect') }}</div>
                                                <div class="text-sm">{{ number_format($answer->points_earned, 2) }}
                                                    points</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($question->question_image)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($question->question_image) }}" alt="Question Image"
                                        class="max-w-md rounded-lg border border-gray-200">
                                </div>
                            @endif

                            <!-- User Answer -->
                            <div class="bg-gray-50 rounded-lg border-l-4 border-gray-400 p-4 mb-3">
                                <div class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1"></i>{{ __('Your Answer:') }}
                                </div>
                                <div class="text-gray-900">
                                    @if ($question->type === 'essay')
                                        <div class="whitespace-pre-wrap">
                                            {{ $answer->answer ?: __('Not answered') }}</div>
                                    @elseif ($question->type === 'matching')
                                        @php
                                            $userMatches = is_array($answer->answer)
                                                ? $answer->answer
                                                : json_decode($answer->answer, true) ?? [];
                                            $pairs = $question->pairs ?? [];
                                        @endphp
                                        <div class="space-y-2">
                                            @foreach ($pairs as $pairIndex => $pair)
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium">{{ $pair['left'] ?? '' }}</span>
                                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                                    <span>{{ $userMatches[$pairIndex] ?? __('Not answered') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($question->type === 'mcq_multiple')
                                        @php
                                            $userAnswers = is_array($answer->answer)
                                                ? $answer->answer
                                                : json_decode($answer->answer, true) ?? [];
                                        @endphp
                                        @if (empty($userAnswers))
                                            {{ __('Not answered') }}
                                        @else
                                            <ul class="list-disc list-inside">
                                                @foreach ($userAnswers as $ans)
                                                    <li>{{ $ans }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @else
                                        {{ $answer->answer ?: __('Not answered') }}
                                    @endif
                                </div>
                            </div>

                            <!-- Correct Answer (if allowed) -->
                            @if ($exam->show_correct_answers)
                                <div class="bg-green-50 rounded-lg border-l-4 border-green-500 p-4 mb-3">
                                    <div class="text-sm font-semibold text-green-700 mb-2">
                                        <i class="fas fa-check mr-1"></i>{{ __('Correct Answer:') }}
                                    </div>
                                    <div class="text-gray-900">
                                        @if ($question->type === 'essay')
                                            <div class="text-sm text-gray-600">
                                                <em>{{ __('Essay will be graded by the teacher') }}</em>
                                                @if ($question->essay_grading_mode !== 'manual')
                                                    <br>{{ __('Grading mode:') }}
                                                    {{ $question->essay_grading_mode_display }}
                                                @endif
                                            </div>
                                        @elseif ($question->type === 'matching')
                                            <div class="space-y-2">
                                                @foreach ($question->pairs ?? [] as $pair)
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">{{ $pair['left'] ?? '' }}</span>
                                                        <i class="fas fa-arrow-right text-gray-400"></i>
                                                        <span
                                                            class="text-green-700 font-medium">{{ $pair['right'] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($question->type === 'mcq_multiple')
                                            @php
                                                $correctAnswers = $question->correct_answer_multiple ?? [];
                                            @endphp
                                            @if (!empty($correctAnswers))
                                                <ul class="list-disc list-inside">
                                                    @foreach ($correctAnswers as $ans)
                                                        <li class="text-green-700 font-medium">{{ $ans }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span
                                                    class="text-gray-500 italic">{{ __('No correct answers provided.') }}
                                                    {{ __('determined') }}</span>
                                            @endif
                                        @else
                                            <span
                                                class="text-green-700 font-medium">{{ $question->correct_answer_single ?? __('Not determined') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Explanation -->
                            @if ($question->explanation)
                                <div class="bg-blue-50 rounded-lg border-l-4 border-blue-500 p-4">
                                    <div class="text-sm font-semibold text-blue-700 mb-2">
                                        <i class="fas fa-lightbulb mr-1"></i>{{ __('Explanation') }}:
                                    </div>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ $question->explanation }}</div>
                                </div>
                            @endif

                            <!-- Feedback (for essay) -->
                            @if ($answer->feedback)
                                <div class="bg-purple-50 rounded-lg border-l-4 border-purple-500 p-4 mt-3">
                                    <div class="text-sm font-semibold text-purple-700 mb-2">
                                        <i class="fas fa-comment mr-1"></i>{{ __('Feedback from Teacher') }}:
                                    </div>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ $answer->feedback }}</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex gap-4 justify-center flex-wrap">
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.show', $exam) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-arrow-left"></i>{{ __('Back to Exam Details') }}
                        </a>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-list"></i>{{ __('Exam List') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
