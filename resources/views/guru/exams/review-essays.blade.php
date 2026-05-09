<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-pen-fancy mr-2"></i>Review Jawaban Essay - {{ $exam->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Exam Info -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-blue-900 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi Ujian
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <i class="fas fa-align-left text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Soal Essay</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $essayQuestions->count() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <i class="fas fa-users text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Attempts</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $attemptsWithEssays->count() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Perlu Review</p>
                                    <p class="text-2xl font-bold text-red-600">
                                        {{ $attemptsWithEssays->sum(function ($attempt) {
                                            return $attempt->answers->filter(function ($answer) {
                                                    return $answer->question->type === 'essay' &&
                                                        ($answer->question->needsManualGrading() || !$answer->is_correct);
                                                })->count();
                                        }) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($attemptsWithEssays->isEmpty())
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Jawaban</h3>
                            <p class="text-gray-500 text-sm">Belum ada jawaban essay yang perlu direview</p>
                        </div>
                    @else
                        <!-- Essay Questions Tabs -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-4 overflow-x-auto" id="essay-tabs">
                                    @foreach ($essayQuestions as $index => $question)
                                        <button type="button"
                                            class="essay-tab py-3 px-4 border-b-2 font-semibold text-sm whitespace-nowrap transition-all duration-200 {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                            data-question-id="{{ $question->id }}">
                                            <i class="fas fa-align-left mr-2"></i>
                                            Soal {{ $index + 1 }}
                                            <span
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                <i class="fas fa-star text-yellow-500 mr-1"></i>
                                                {{ $question->points }}
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
                                <div
                                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-l-4 border-indigo-500 rounded-lg p-6 mb-6 shadow-sm">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="text-lg font-bold text-gray-900">
                                            <i class="fas fa-question-circle text-indigo-600 mr-2"></i>Soal
                                            {{ $qIndex + 1 }}
                                        </h4>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star text-yellow-600 mr-1"></i>
                                            {{ $question->points }} poin
                                        </span>
                                    </div>
                                    <p class="text-gray-800 mb-4 leading-relaxed">{{ $question->question_text }}</p>

                                    <div class="flex items-center gap-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-sm text-gray-700">
                                            <i class="fas fa-robot text-blue-500 mr-2"></i>
                                            {{ $question->essay_grading_mode_display }}
                                        </span>
                                    </div>

                                    @if ($question->essay_grading_mode === 'keyword')
                                        <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                                            <p class="text-sm font-bold text-yellow-900 mb-3">
                                                <i class="fas fa-key text-yellow-600 mr-2"></i>Kata Kunci:
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($question->essay_keywords as $index => $keyword)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-800 text-sm font-semibold border border-yellow-200">
                                                        <i class="fas fa-tag text-yellow-600 mr-2"></i>
                                                        {{ $keyword }}
                                                        <span
                                                            class="ml-2 px-2 py-0.5 bg-yellow-200 rounded-full text-xs">
                                                            {{ $question->essay_keyword_points[$index] ?? 0 }} poin
                                                        </span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($question->essay_grading_mode === 'similarity')
                                        <div class="mt-4 bg-green-50 border-l-4 border-green-400 rounded-lg p-4">
                                            <p class="text-sm font-bold text-green-900 mb-2">
                                                <i class="fas fa-check-double text-green-600 mr-2"></i>Jawaban Model:
                                            </p>
                                            <p
                                                class="text-sm text-green-800 bg-white rounded-lg p-3 border border-green-200 mb-3">
                                                {{ $question->essay_model_answer }}
                                            </p>
                                            <p class="text-xs text-green-700 flex items-center gap-2">
                                                <i class="fas fa-percentage text-green-600"></i>
                                                Minimal Similarity: <span
                                                    class="font-bold">{{ $question->essay_min_similarity }}%</span>
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
                                        class="border-2 rounded-lg p-6 mb-4 shadow-sm transition-all duration-200 hover:shadow-md {{ $needsGrading ? 'border-red-300 bg-red-50' : 'border-green-300 bg-white' }}">
                                        <!-- Student Info -->
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                                    <i class="fas fa-user-circle text-gray-400"></i>
                                                    {{ $attempt->user->name }}
                                                </h5>
                                                <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                                    <i class="fas fa-clock text-gray-400"></i>
                                                    Attempt #{{ $attempt->id }} •
                                                    {{ $attempt->submitted_at?->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if ($needsGrading)
                                                <span
                                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-red-500 text-white shadow-sm">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Perlu Review
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                                                    <i class="fas fa-check-circle mr-1"></i>Sudah Dinilai
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Student Answer -->
                                        <div class="bg-white border-l-4 border-blue-400 rounded-lg p-4 mb-4 shadow-sm">
                                            <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                                <i class="fas fa-comment-dots text-blue-500"></i>
                                                Jawaban Mahasiswa:
                                            </p>
                                            <p class="text-gray-900 leading-relaxed">
                                                {{ is_array($answer->answer) ? json_encode($answer->answer) : $answer->answer }}
                                            </p>
                                        </div>

                                        <!-- Auto-Grading Result (if any) -->
                                        @if ($question->hasAutoGrading() && $answer->points_earned !== null)
                                            <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4 mb-4">
                                                <p class="text-sm font-bold text-blue-900 mb-2 flex items-center gap-2">
                                                    <i class="fas fa-robot text-blue-600"></i>
                                                    Hasil Auto-Grading:
                                                </p>
                                                <p class="text-blue-800 text-lg mb-2">
                                                    <span class="font-bold">{{ $answer->points_earned }}</span> /
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
                                                        <span class="text-sm font-normal">(Similarity:
                                                            {{ round($similarity, 1) }}%)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-blue-700 flex items-center gap-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    Anda bisa override nilai ini dengan form di bawah
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Grading Form -->
                                        <form action="{{ route(auth()->user()->getRolePrefix() . '.', [$exam, $answer]) }}"
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
                                        <!-- Grading Form -->
                                        <form action="{{ route(auth()->user()->getRolePrefix() . '.', [$exam, $answer]) }}"
                                            method="POST"
                                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-2 border-gray-300 rounded-lg p-5">
                                            @csrf
                                            <h6 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                                <i class="fas fa-edit text-indigo-600"></i>
                                                Form Penilaian
                                            </h6>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-star text-gray-400 mr-1"></i>Nilai (0 -
                                                        {{ $question->points }})
                                                    </label>
                                                    <input type="number" name="points_earned"
                                                        value="{{ old('points_earned', $answer->points_earned) }}"
                                                        min="0" max="{{ $question->points }}" step="0.1"
                                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-150"
                                                        required>
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-comment text-gray-400 mr-1"></i>Feedback
                                                        (Opsional)
                                                    </label>
                                                    <textarea name="feedback" rows="2"
                                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-150"
                                                        placeholder="Berikan feedback untuk mahasiswa...">{{ old('feedback', $answer->feedback) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                    <i class="fas fa-save"></i>
                                                    Simpan Nilai
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach

                                @if ($answersForQuestion->isEmpty())
                                    <div
                                        class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Jawaban</h3>
                                        <p class="text-gray-500 text-sm">Belum ada jawaban untuk soal ini</p>
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
