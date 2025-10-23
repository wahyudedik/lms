<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $exam->title }} - Ujian</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div id="exam-container" class="min-h-screen">
        <!-- Header -->
        <div class="bg-blue-600 text-white py-4 px-6 shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold">{{ $exam->title }}</h1>
                    <p class="text-sm text-blue-100">{{ $exam->course->title }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold" id="timer">
                        <i class="fas fa-clock mr-2"></i>
                        <span id="timer-display">{{ $exam->duration_minutes }}:00</span>
                    </div>
                    <p class="text-xs text-blue-100">Waktu tersisa</p>
                </div>
            </div>
        </div>

        <!-- Warning Bar (if anti-cheat active) -->
        @if ($exam->detect_tab_switch || $exam->require_fullscreen)
            <div id="warning-bar" class="hidden bg-yellow-500 text-white py-2 px-6 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="warning-message"></span>
            </div>
        @endif

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Questions Panel -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        @foreach ($questions as $index => $question)
                            <div class="question-container {{ $index === 0 ? '' : 'hidden' }}"
                                data-question-id="{{ $question->id }}" data-question-index="{{ $index }}">

                                <!-- Question Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <span class="text-sm text-gray-500">Soal {{ $index + 1 }} dari
                                            {{ $questions->count() }}</span>
                                        <h3 class="text-lg font-semibold text-gray-900 mt-1">
                                            {{ $question->question_text }}
                                        </h3>
                                    </div>
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                        {{ $question->points }} poin
                                    </span>
                                </div>

                                <!-- Question Image -->
                                @if ($question->question_image)
                                    <div class="mb-4">
                                        <img src="{{ Storage::url($question->question_image) }}"
                                            alt="Question Image" class="max-w-full h-auto rounded-lg">
                                    </div>
                                @endif

                                <!-- Answer Section -->
                                <div class="mt-6">
                                    @php
                                        $savedAnswer = $attempt->answers->firstWhere('question_id', $question->id);
                                    @endphp

                                    @if ($question->type === 'mcq_single')
                                        <!-- MCQ Single Answer -->
                                        <div class="space-y-3">
                                            @php
                                                $options = $question->options ?? [];
                                                if ($exam->shuffle_options) {
                                                    shuffle($options);
                                                }
                                            @endphp
                                            @foreach ($options as $optIndex => $option)
                                                @php
                                                    // Handle both string and array format
                                                    if (is_array($option)) {
                                                        $optionValue = $option['text'] ?? $option['id'] ?? json_encode($option);
                                                        $optionId = $option['id'] ?? $optionValue;
                                                    } else {
                                                        $optionValue = $option;
                                                        $optionId = $option;
                                                    }
                                                @endphp
                                                <label
                                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition {{ $savedAnswer && $savedAnswer->answer == $optionId ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                                    <input type="radio" name="question_{{ $question->id }}"
                                                        value="{{ $optionId }}"
                                                        {{ $savedAnswer && $savedAnswer->answer == $optionId ? 'checked' : '' }}
                                                        class="mt-1 mr-3" onchange="saveAnswer({{ $question->id }})">
                                                    <span class="text-gray-900">{{ chr(65 + $optIndex) }}.
                                                        {{ $optionValue }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($question->type === 'mcq_multiple')
                                        <!-- MCQ Multiple Answers -->
                                        <div class="space-y-3">
                                            @php
                                                $savedAnswers = $savedAnswer ? (is_array($savedAnswer->answer) ? $savedAnswer->answer : json_decode($savedAnswer->answer, true)) : [];
                                                $savedAnswers = $savedAnswers ?? [];
                                                $options = $question->options ?? [];
                                                if ($exam->shuffle_options) {
                                                    shuffle($options);
                                                }
                                            @endphp
                                            @foreach ($options as $optIndex => $option)
                                                @php
                                                    // Handle both string and array format
                                                    if (is_array($option)) {
                                                        $optionValue = $option['text'] ?? $option['id'] ?? json_encode($option);
                                                        $optionId = $option['id'] ?? $optionValue;
                                                    } else {
                                                        $optionValue = $option;
                                                        $optionId = $option;
                                                    }
                                                @endphp
                                                <label
                                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition {{ in_array($optionId, $savedAnswers ?? []) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                                    <input type="checkbox" name="question_{{ $question->id }}[]"
                                                        value="{{ $optionId }}"
                                                        {{ in_array($optionId, $savedAnswers ?? []) ? 'checked' : '' }}
                                                        class="mt-1 mr-3" onchange="saveAnswer({{ $question->id }})">
                                                    <span class="text-gray-900">{{ chr(65 + $optIndex) }}.
                                                        {{ $optionValue }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($question->type === 'matching')
                                        <!-- Matching Question -->
                                        <div class="space-y-3">
                                            @php
                                                $savedMatches = $savedAnswer ? (is_array($savedAnswer->answer) ? $savedAnswer->answer : json_decode($savedAnswer->answer, true)) : [];
                                                $savedMatches = $savedMatches ?? [];
                                                $pairs = $question->pairs ?? [];
                                                $rightOptions = collect($pairs)->pluck('right')->shuffle()->values();
                                            @endphp
                                            @foreach ($pairs as $pairIndex => $pair)
                                                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                                    <div class="flex-1">
                                                        <span
                                                            class="font-medium text-gray-900">{{ $pair['left'] }}</span>
                                                    </div>
                                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                                    <div class="flex-1">
                                                        <select name="question_{{ $question->id }}[{{ $pairIndex }}]"
                                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            onchange="saveAnswer({{ $question->id }})">
                                                            <option value="">Pilih jawaban...</option>
                                                            @foreach ($rightOptions as $rightOpt)
                                                                <option value="{{ $rightOpt }}"
                                                                    {{ isset($savedMatches[$pairIndex]) && $savedMatches[$pairIndex] == $rightOpt ? 'selected' : '' }}>
                                                                    {{ $rightOpt }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($question->type === 'essay')
                                        <!-- Essay Question -->
                                        <div>
                                            <textarea name="question_{{ $question->id }}" rows="8"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Tuliskan jawaban Anda di sini..." onchange="saveAnswer({{ $question->id }})">{{ $savedAnswer ? $savedAnswer->answer : '' }}</textarea>
                                        </div>
                                    @endif
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="flex justify-between mt-8 pt-6 border-t">
                                    <button type="button" onclick="previousQuestion()"
                                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 {{ $index === 0 ? 'invisible' : '' }}">
                                        <i class="fas fa-arrow-left mr-2"></i>Sebelumnya
                                    </button>

                                    @if ($index < $questions->count() - 1)
                                        <button type="button" onclick="nextQuestion()"
                                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                            Selanjutnya<i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    @else
                                        <button type="button" onclick="confirmSubmit()"
                                            class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                            <i class="fas fa-check mr-2"></i>Selesai & Kumpulkan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Question Navigation Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-4 sticky top-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Navigasi Soal</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach ($questions as $index => $question)
                                @php
                                    $isAnswered = $attempt->answers->where('question_id', $question->id)->first();
                                @endphp
                                <button type="button" onclick="goToQuestion({{ $index }})"
                                    class="nav-btn aspect-square rounded-lg font-semibold transition {{ $isAnswered ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}"
                                    data-question-index="{{ $index }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Terjawab:</span>
                                <span class="font-semibold text-green-600" id="answered-count">
                                    {{ $attempt->answers->count() }}/{{ $questions->count() }}
                                </span>
                            </div>
                        </div>

                        <button type="button" onclick="confirmSubmit()"
                            class="w-full mt-6 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold">
                            <i class="fas fa-check mr-2"></i>Kumpulkan Ujian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Submission -->
    <form id="submit-form" action="{{ route('siswa.exams.submit', $attempt) }}" method="POST" class="hidden">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Configuration
        const attemptId = {{ $attempt->id }};
        const examDuration = {{ $exam->duration_minutes }};
        const requireFullscreen = {{ $exam->require_fullscreen ? 'true' : 'false' }};
        const detectTabSwitch = {{ $exam->detect_tab_switch ? 'true' : 'false' }};
        const maxTabSwitches = {{ $exam->max_tab_switches ?? 999 }};
        let tabSwitchCount = {{ $attempt->tab_switches }};
        let currentQuestionIndex = 0;
        let timeRemaining = 0;
        let timerInterval;

        // Initialize timer
        function initTimer() {
            fetch(`/siswa/attempts/${attemptId}/time-remaining`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.seconds_remaining !== undefined) {
                        timeRemaining = data.seconds_remaining;
                        startTimer();
                    } else {
                        console.error('Invalid time data:', data);
                        // Fallback: calculate from exam duration
                        timeRemaining = examDuration * 60;
                        startTimer();
                    }
                })
                .catch(error => {
                    console.error('Timer fetch error:', error);
                    // Fallback: use exam duration
                    timeRemaining = examDuration * 60;
                    startTimer();
                });
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    autoSubmit('Waktu habis!');
                    return;
                }

                timeRemaining--;
                updateTimerDisplay();
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            document.getElementById('timer-display').textContent =
                `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Warning when time is running out
            if (timeRemaining <= 60) {
                document.getElementById('timer').classList.add('text-red-400');
            } else if (timeRemaining <= 300) {
                document.getElementById('timer').classList.add('text-yellow-300');
            }
        }

        // Question navigation
        function goToQuestion(index) {
            document.querySelectorAll('.question-container').forEach(q => q.classList.add('hidden'));
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('ring-2', 'ring-blue-500'));

            document.querySelector(`[data-question-index="${index}"]`).classList.remove('hidden');
            document.querySelector(`.nav-btn[data-question-index="${index}"]`).classList.add('ring-2',
                'ring-blue-500');
            currentQuestionIndex = index;

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function nextQuestion() {
            if (currentQuestionIndex < {{ $questions->count() - 1 }}) {
                goToQuestion(currentQuestionIndex + 1);
            }
        }

        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                goToQuestion(currentQuestionIndex - 1);
            }
        }

        // Save answer
        function saveAnswer(questionId) {
            const container = document.querySelector(`[data-question-id="${questionId}"]`);
            let answerData;

            // Get answer based on question type
            const radioInput = container.querySelector(`input[name="question_${questionId}"]:checked`);
            const checkboxes = container.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
            const selects = container.querySelectorAll(`select[name^="question_${questionId}"]`);
            const textarea = container.querySelector(`textarea[name="question_${questionId}"]`);

            if (radioInput) {
                answerData = radioInput.value;
            } else if (checkboxes.length > 0) {
                answerData = Array.from(checkboxes).map(cb => cb.value);
            } else if (selects.length > 0) {
                answerData = {};
                selects.forEach((select, index) => {
                    if (select.value) answerData[index] = select.value;
                });
            } else if (textarea) {
                answerData = textarea.value;
            }

            // Send to server
                fetch(`/siswa/attempts/${attemptId}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: answerData
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update navigation button
                        const navBtn = document.querySelector(
                            `.nav-btn[data-question-index="${currentQuestionIndex}"]`);
                        navBtn.classList.remove('bg-gray-200', 'text-gray-700');
                        navBtn.classList.add('bg-green-500', 'text-white');

                        // Update answered count
                        document.getElementById('answered-count').textContent =
                            `${data.answered_count}/${{{ $questions->count() }}}`;
                    }
                });
        }

        // Submit exam
        function confirmSubmit() {
            const answeredCount = {{ $attempt->answers->count() }};
            const totalQuestions = {{ $questions->count() }};
            const unanswered = totalQuestions - answeredCount;

            let message = 'Yakin ingin mengumpulkan ujian?';
            if (unanswered > 0) {
                message += ` Masih ada ${unanswered} soal yang belum dijawab.`;
            }

            Swal.fire({
                title: 'Kumpulkan Ujian?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kumpulkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitExam();
                }
            });
        }

        function submitExam() {
            clearInterval(timerInterval);
            document.getElementById('submit-form').submit();
        }

        function autoSubmit(reason) {
            Swal.fire({
                title: reason,
                text: 'Ujian akan otomatis dikumpulkan.',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                submitExam();
            });
        }

        // Anti-cheat: Fullscreen
        if (requireFullscreen) {
            function requestFullscreen() {
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            }

            requestFullscreen();

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    showWarning('Mode layar penuh dimatikan! Harap aktifkan kembali.');
                    fetch(`/siswa/attempts/${attemptId}/track-fullscreen-exit`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    requestFullscreen();
                }
            });
        }

        // Anti-cheat: Tab switching
        if (detectTabSwitch) {
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    tabSwitchCount++;

                    fetch(`/siswa/attempts/${attemptId}/track-tab-switch`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (tabSwitchCount >= maxTabSwitches) {
                        autoSubmit('Batas perpindahan tab tercapai!');
                    } else {
                        showWarning(
                            `Peringatan! Jangan keluar dari halaman ujian. (${tabSwitchCount}/${maxTabSwitches})`);
                    }
                }
            });
        }

        function showWarning(message) {
            const warningBar = document.getElementById('warning-bar');
            const warningMessage = document.getElementById('warning-message');
            if (warningBar && warningMessage) {
                warningMessage.textContent = message;
                warningBar.classList.remove('hidden');
                setTimeout(() => {
                    warningBar.classList.add('hidden');
                }, 5000);
            }
        }

        // Prevent right-click and copy
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());

        // Initialize
        initTimer();
    </script>
</body>

</html>

