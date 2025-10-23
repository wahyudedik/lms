<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Jawaban Essay - {{ $exam->title }}
            </h2>
            <a href="{{ route('guru.exams.show', $exam) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Exam Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Ujian
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Soal Essay:</span>
                                <span class="font-semibold ml-2">{{ $essayQuestions->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Attempts:</span>
                                <span class="font-semibold ml-2">{{ $attemptsWithEssays->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Perlu Review:</span>
                                <span class="font-semibold ml-2 text-red-600">
                                    {{ $attemptsWithEssays->sum(function ($attempt) {
                                        return $attempt->answers->filter(function ($answer) {
                                                return $answer->question->type === 'essay' &&
                                                    ($answer->question->needsManualGrading() || !$answer->is_correct);
                                            })->count();
                                    }) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($attemptsWithEssays->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum ada jawaban essay yang perlu direview</p>
                        </div>
                    @else
                        <!-- Essay Questions Tabs -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-4" id="essay-tabs">
                                    @foreach ($essayQuestions as $index => $question)
                                        <button type="button"
                                            class="essay-tab py-2 px-4 border-b-2 font-medium text-sm {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                            data-question-id="{{ $question->id }}">
                                            <i class="fas fa-align-left mr-1"></i>
                                            Soal {{ $index + 1 }}
                                            <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                                                {{ $question->points }} poin
                                            </span>
                                        </button>
                                    @endforeach
                                </nav>
                            </div>
                        </div>

                        <!-- Essay Answers by Question -->
                        @foreach ($essayQuestions as $qIndex => $question)
                            <div class="question-answers {{ $qIndex !== 0 ? 'hidden' : '' }}"
                                data-question-id="{{ $question->id }}">
                                <!-- Question Card -->
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                    <h4 class="font-semibold text-gray-900 mb-2">Soal {{ $qIndex + 1 }}</h4>
                                    <p class="text-gray-700 mb-2">{{ $question->question_text }}</p>

                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                            {{ $question->points }} poin
                                        </span>
                                        <span>
                                            <i class="fas fa-robot mr-1"></i>
                                            {{ $question->essay_grading_mode_display }}
                                        </span>
                                    </div>

                                    @if ($question->essay_grading_mode === 'keyword')
                                        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded p-3">
                                            <p class="text-sm font-medium text-yellow-900 mb-1">Kata Kunci:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($question->essay_keywords as $index => $keyword)
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                                        {{ $keyword }}
                                                        <span
                                                            class="font-semibold">({{ $question->essay_keyword_points[$index] ?? 0 }}
                                                            poin)</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($question->essay_grading_mode === 'similarity')
                                        <div class="mt-3 bg-green-50 border border-green-200 rounded p-3">
                                            <p class="text-sm font-medium text-green-900 mb-1">Jawaban Model:</p>
                                            <p class="text-sm text-green-800">{{ $question->essay_model_answer }}</p>
                                            <p class="text-xs text-green-700 mt-2">
                                                Minimal Similarity: {{ $question->essay_min_similarity }}%
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Student Answers -->
                                @php
                                    $answersForQuestion = collect();
                                    foreach ($attemptsWithEssays as $attempt) {
                                        $answer = $attempt->answers->firstWhere('question_id', $question->id);
                                        if ($answer) {
                                            $answersForQuestion->push([
                                                'attempt' => $attempt,
                                                'answer' => $answer,
                                            ]);
                                        }
                                    }
                                @endphp

                                @foreach ($answersForQuestion as $item)
                                    @php
                                        $attempt = $item['attempt'];
                                        $answer = $item['answer'];
                                        $needsGrading =
                                            $question->needsManualGrading() ||
                                            $answer->points_earned === null ||
                                            $answer->points_earned === 0;
                                    @endphp
                                    <div
                                        class="border rounded-lg p-4 mb-4 {{ $needsGrading ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white' }}">
                                        <!-- Student Info -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-semibold text-gray-900">
                                                    {{ $attempt->user->name }}
                                                </h5>
                                                <p class="text-sm text-gray-600">
                                                    Attempt #{{ $attempt->id }} •
                                                    {{ $attempt->submitted_at?->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if ($needsGrading)
                                                <span
                                                    class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Perlu Review
                                                </span>
                                            @else
                                                <span
                                                    class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-check-circle mr-1"></i>Sudah Dinilai
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Student Answer -->
                                        <div class="bg-white border border-gray-200 rounded p-3 mb-3">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Jawaban Siswa:</p>
                                            <p class="text-gray-900">
                                                {{ is_array($answer->answer) ? json_encode($answer->answer) : $answer->answer }}
                                            </p>
                                        </div>

                                        <!-- Auto-Grading Result (if any) -->
                                        @if ($question->hasAutoGrading() && $answer->points_earned !== null)
                                            <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
                                                <p class="text-sm font-medium text-blue-900 mb-1">
                                                    <i class="fas fa-robot mr-1"></i>Hasil Auto-Grading:
                                                </p>
                                                <p class="text-blue-800">
                                                    <span class="font-semibold">{{ $answer->points_earned }}</span> /
                                                    {{ $question->points }} poin
                                                    @if ($question->essay_grading_mode === 'similarity')
                                                        @php
                                                            $answerText = is_array($answer->answer)
                                                                ? json_encode($answer->answer)
                                                                : $answer->answer;
                                                            similar_text(
                                                                strtolower($question->essay_model_answer),
                                                                strtolower($answerText),
                                                                $similarity,
                                                            );
                                                        @endphp
                                                        <span class="text-sm">(Similarity:
                                                            {{ round($similarity, 1) }}%)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-blue-700 mt-1">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Anda bisa override nilai ini dengan form di bawah
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Grading Form -->
                                        <form action="{{ route('guru.exams.grade-essay', [$exam, $answer]) }}"
                                            method="POST" class="bg-gray-50 border border-gray-300 rounded p-3">
                                            @csrf
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Nilai (0 - {{ $question->points }})
                                                    </label>
                                                    <input type="number" name="points_earned"
                                                        value="{{ old('points_earned', $answer->points_earned) }}"
                                                        min="0" max="{{ $question->points }}" step="0.1"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        required>
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Feedback (Opsional)
                                                    </label>
                                                    <textarea name="feedback" rows="2"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        placeholder="Berikan feedback untuk siswa...">{{ old('feedback', $answer->feedback) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex justify-end">
                                                <button type="submit"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                    <i class="fas fa-save mr-2"></i>Simpan Nilai
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach

                                @if ($answersForQuestion->isEmpty())
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-gray-500">Belum ada jawaban untuk soal ini</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tab switching
            document.querySelectorAll('.essay-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const questionId = this.dataset.questionId;

                    // Update tab styles
                    document.querySelectorAll('.essay-tab').forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.add('border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500');

                    // Show/hide question answers
                    document.querySelectorAll('.question-answers').forEach(section => {
                        if (section.dataset.questionId === questionId) {
                            section.classList.remove('hidden');
                        } else {
                            section.classList.add('hidden');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
