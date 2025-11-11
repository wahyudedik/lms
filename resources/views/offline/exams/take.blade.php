<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $exam->title }}</h2>
                <p class="text-sm text-gray-600">Offline Mode Enabled</p>
            </div>

            <!-- Status Indicators -->
            <div class="flex items-center gap-4">
                <!-- Online/Offline Status -->
                <div id="connectionStatus"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 border border-green-200">
                    <span class="status-dot online"></span>
                    <span class="text-sm font-medium text-green-700">Online</span>
                </div>

                <!-- Auto-save Status -->
                <div id="saveStatus"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-200">
                    <i class="fas fa-check-circle text-gray-400"></i>
                    <span class="text-sm text-gray-600">All saved</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form id="examForm" data-exam-id="{{ $exam->id }}" data-attempt-id="{{ $activeAttempt->id }}">
                @csrf

                <!-- Exam Info Banner -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <i class="fas fa-question-circle text-indigo-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Questions</p>
                            <p class="text-xl font-bold text-gray-800">{{ $exam->questions->count() }}</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-clock text-indigo-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Time Remaining</p>
                            <p class="text-xl font-bold text-gray-800" id="timer">{{ $exam->duration }}:00</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Answered</p>
                            <p class="text-xl font-bold text-gray-800" id="answeredCount">0</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-circle text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Unanswered</p>
                            <p class="text-xl font-bold text-gray-800" id="unansweredCount">
                                {{ $exam->questions->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Offline Mode Warning -->
                <div id="offlineWarning" class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6"
                    style="display: none;">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-medium text-yellow-800">You are currently offline</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                Don't worry! Your answers are being saved locally and will be submitted when you're back
                                online.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="space-y-6">
                    @foreach ($exam->questions as $index => $question)
                        <div class="bg-white rounded-lg shadow-md p-6 question-card"
                            data-question-id="{{ $question->id }}" id="question{{ $question->id }}">

                            <!-- Question Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800">{{ $question->question_text }}
                                        </h3>
                                        @if ($question->points)
                                            <p class="text-sm text-gray-500 mt-1">{{ $question->points }} points</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="answer-status text-gray-400" id="status{{ $question->id }}">
                                    <i class="fas fa-circle text-xs"></i>
                                </span>
                            </div>

                            <!-- Question Content -->
                            <div class="ml-11 space-y-3">
                                @if ($question->type === 'multiple_choice')
                                    @php
                                        $options = is_array($question->options)
                                            ? $question->options
                                            : json_decode($question->options, true);
                                    @endphp
                                    @foreach ($options as $key => $option)
                                        <label
                                            class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors answer-option">
                                            <input type="radio" name="answer[{{ $question->id }}]"
                                                value="{{ $key }}"
                                                {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == $key ? 'checked' : '' }}
                                                class="w-4 h-4 text-indigo-600">
                                            <span class="text-gray-700">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                @elseif($question->type === 'true_false')
                                    <label
                                        class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="answer[{{ $question->id }}]" value="true"
                                            {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == 'true' ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600">
                                        <span class="text-gray-700">True</span>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="answer[{{ $question->id }}]" value="false"
                                            {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == 'false' ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600">
                                        <span class="text-gray-700">False</span>
                                    </label>
                                @elseif($question->type === 'short_answer')
                                    <input type="text" name="answer[{{ $question->id }}]"
                                        value="{{ $existingAnswers[$question->id] ?? '' }}"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Type your answer here...">
                                @elseif($question->type === 'essay')
                                    <textarea name="answer[{{ $question->id }}]" rows="6"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Type your essay here...">{{ $existingAnswers[$question->id] ?? '' }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-between items-center bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Make sure to review all your answers before submitting
                    </div>
                    <button type="button" id="submitExam" class="btn btn-primary px-8 py-3 text-lg font-bold">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Exam
                    </button>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/offline.js') }}"></script>
        <script>
            const examId = {{ $exam->id }};
            const attemptId = {{ $activeAttempt->id }};
            const duration = {{ $exam->duration }};
            let timeRemaining = duration * 60; // in seconds
            let timerInterval;
            let autoSaveInterval;
            let wasOffline = false;

            document.addEventListener('DOMContentLoaded', () => {
                initializeExam();
                startTimer();
                setupAutoSave();
                monitorConnection();
                trackAnswerChanges();
                updateAnswerStats();
            });

            /**
             * Initialize exam
             */
            function initializeExam() {
                console.log('[Offline Exam] Initialized', {
                    examId,
                    attemptId
                });

                // Check for existing answers
                updateAnswerStats();
            }

            /**
             * Start countdown timer
             */
            function startTimer() {
                timerInterval = setInterval(() => {
                    timeRemaining--;

                    const minutes = Math.floor(timeRemaining / 60);
                    const seconds = timeRemaining % 60;

                    document.getElementById('timer').textContent =
                        `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    // Auto-submit when time runs out
                    if (timeRemaining <= 0) {
                        clearInterval(timerInterval);
                        submitExam(true);
                    }

                    // Warning at 5 minutes
                    if (timeRemaining === 300) {
                        showToast('5 minutes remaining!', 'warning');
                    }
                }, 1000);
            }

            /**
             * Setup auto-save
             */
            function setupAutoSave() {
                // Auto-save every 30 seconds
                autoSaveInterval = setInterval(() => {
                    saveAllAnswers();
                }, 30000);
            }

            /**
             * Monitor connection status
             */
            function monitorConnection() {
                const updateStatus = () => {
                    const statusElement = document.getElementById('connectionStatus');
                    const warningElement = document.getElementById('offlineWarning');

                    if (navigator.onLine) {
                        statusElement.innerHTML = `
                            <span class="status-dot online"></span>
                            <span class="text-sm font-medium text-green-700">Online</span>
                        `;
                        statusElement.className =
                            'flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 border border-green-200';
                        warningElement.style.display = 'none';

                        // Sync if we were offline
                        if (wasOffline) {
                            showToast('Back online! Syncing...', 'success');
                            saveAllAnswers();
                            wasOffline = false;
                        }
                    } else {
                        statusElement.innerHTML = `
                            <span class="status-dot offline"></span>
                            <span class="text-sm font-medium text-red-700">Offline</span>
                        `;
                        statusElement.className =
                            'flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-200';
                        warningElement.style.display = 'block';
                        wasOffline = true;
                    }
                };

                window.addEventListener('online', updateStatus);
                window.addEventListener('offline', updateStatus);
                updateStatus();
            }

            /**
             * Track answer changes
             */
            function trackAnswerChanges() {
                const form = document.getElementById('examForm');

                form.addEventListener('change', (e) => {
                    if (e.target.name && e.target.name.startsWith('answer[')) {
                        const questionId = e.target.name.match(/\d+/)[0];
                        saveAnswer(questionId, e.target.value);
                        updateAnswerStatus(questionId, true);
                        updateAnswerStats();
                    }
                });

                // For text inputs, save on input (debounced)
                form.querySelectorAll('input[type="text"], textarea').forEach(input => {
                    let timeout;
                    input.addEventListener('input', (e) => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            const questionId = e.target.name.match(/\d+/)[0];
                            saveAnswer(questionId, e.target.value);
                            updateAnswerStatus(questionId, e.target.value.trim() !== '');
                            updateAnswerStats();
                        }, 1000);
                    });
                });
            }

            /**
             * Save single answer
             */
            async function saveAnswer(questionId, answer) {
                try {
                    const saveStatus = document.getElementById('saveStatus');
                    saveStatus.innerHTML = `
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        <span class="text-sm text-gray-600">Saving...</span>
                    `;

                    const response = await fetch(`/offline/exams/${examId}/answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            attempt_id: attemptId,
                            question_id: questionId,
                            answer: answer
                        })
                    });

                    if (response.ok) {
                        saveStatus.innerHTML = `
                            <i class="fas fa-check-circle text-green-600"></i>
                            <span class="text-sm text-green-600">Saved</span>
                        `;
                    } else {
                        throw new Error('Save failed');
                    }

                    setTimeout(() => {
                        saveStatus.innerHTML = `
                            <i class="fas fa-check-circle text-gray-400"></i>
                            <span class="text-sm text-gray-600">All saved</span>
                        `;
                    }, 2000);

                } catch (error) {
                    console.error('[Offline Exam] Save failed:', error);

                    // Save to local storage as backup
                    saveToLocalStorage(questionId, answer);
                }
            }

            /**
             * Save to local storage
             */
            function saveToLocalStorage(questionId, answer) {
                const key = `exam_${examId}_attempt_${attemptId}`;
                const stored = JSON.parse(localStorage.getItem(key) || '{}');
                stored[questionId] = answer;
                localStorage.setItem(key, JSON.stringify(stored));
            }

            /**
             * Save all answers
             */
            async function saveAllAnswers() {
                const form = document.getElementById('examForm');
                const formData = new FormData(form);
                const answers = {};

                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('answer[')) {
                        const questionId = key.match(/\d+/)[0];
                        answers[questionId] = value;
                    }
                }

                // Save each answer
                for (let [questionId, answer] of Object.entries(answers)) {
                    await saveAnswer(questionId, answer);
                }
            }

            /**
             * Update answer status indicator
             */
            function updateAnswerStatus(questionId, isAnswered) {
                const statusElement = document.getElementById(`status${questionId}`);
                if (statusElement) {
                    if (isAnswered) {
                        statusElement.innerHTML = '<i class="fas fa-check-circle text-green-600"></i>';
                    } else {
                        statusElement.innerHTML = '<i class="fas fa-circle text-gray-400 text-xs"></i>';
                    }
                }
            }

            /**
             * Update answer statistics
             */
            function updateAnswerStats() {
                const form = document.getElementById('examForm');
                const totalQuestions = form.querySelectorAll('.question-card').length;
                let answeredCount = 0;

                // Check radio buttons
                form.querySelectorAll('input[type="radio"]:checked').forEach(() => {
                    answeredCount++;
                });

                // Check text inputs
                form.querySelectorAll('input[type="text"], textarea').forEach(input => {
                    if (input.value.trim() !== '') {
                        answeredCount++;
                    }
                });

                document.getElementById('answeredCount').textContent = answeredCount;
                document.getElementById('unansweredCount').textContent = totalQuestions - answeredCount;
            }

            /**
             * Submit exam
             */
            async function submitExam(autoSubmit = false) {
                if (!autoSubmit) {
                    const confirmed = await Swal.fire({
                        title: 'Submit Exam?',
                        text: 'Are you sure you want to submit? You cannot change your answers after submission.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'Review Again'
                    });

                    if (!confirmed.isConfirmed) return;
                }

                try {
                    // Collect all answers
                    const form = document.getElementById('examForm');
                    const formData = new FormData(form);
                    const answers = {};

                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith('answer[')) {
                            const questionId = key.match(/\d+/)[0];
                            answers[questionId] = value;
                        }
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit
                    const response = await fetch(`/offline/exams/${examId}/submit`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            attempt_id: attemptId,
                            answers: answers,
                            was_offline: wasOffline
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Clear local storage
                        localStorage.removeItem(`exam_${examId}_attempt_${attemptId}`);

                        // Stop timer
                        clearInterval(timerInterval);
                        clearInterval(autoSaveInterval);

                        // Show success
                        await Swal.fire({
                            title: 'Submitted!',
                            text: `Your exam has been submitted. Score: ${result.score}%`,
                            icon: 'success'
                        });

                        // Redirect
                        window.location.href = '/offline/exams';
                    } else {
                        throw new Error(result.message);
                    }

                } catch (error) {
                    console.error('[Offline Exam] Submit failed:', error);

                    Swal.fire({
                        title: 'Submission Queued',
                        text: 'Your answers have been saved locally and will be submitted when you\'re back online.',
                        icon: 'info'
                    });
                }
            }

            // Submit button handler
            document.getElementById('submitExam').addEventListener('click', () => {
                submitExam(false);
            });

            /**
             * Show toast notification
             */
            function showToast(message, type = 'info') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000
                });
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', (e) => {
                saveAllAnswers();
            });
        </script>

        <style>
            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                display: inline-block;
            }

            .status-dot.online {
                background-color: #10b981;
                animation: pulse 2s infinite;
            }

            .status-dot.offline {
                background-color: #ef4444;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            .answer-option:has(input:checked) {
                background-color: #eef2ff;
                border-color: #6366f1;
            }
        </style>
    @endpush
</x-app-layout>
