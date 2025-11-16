<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Soal - :title', ['title' => $exam->title]) }}
            </h2>
            <a href="{{ route('admin.exams.questions.index', $exam) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.exams.questions.update', [$exam, $question]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Question Type -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Tipe Soal') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="mcq_single" {{ $question->type === 'mcq_single' ? 'selected' : '' }}>
                                    {{ __('Pilihan Ganda (Single Answer)') }}
                                </option>
                                <option value="mcq_multiple" {{ $question->type === 'mcq_multiple' ? 'selected' : '' }}>
                                    {{ __('Pilihan Ganda (Multiple Answers)') }}
                                </option>
                                <option value="matching" {{ $question->type === 'matching' ? 'selected' : '' }}>
                                    {{ __('Menjodohkan') }}
                                </option>
                                <option value="essay" {{ $question->type === 'essay' ? 'selected' : '' }}>
                                    {{ __('Essay') }}
                                </option>
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Pertanyaan') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question_text" id="question_text" rows="3" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('question_text', $question->question_text) }}</textarea>
                        </div>

                        <!-- Question Image -->
                        <div class="mb-6">
                            <label for="question_image" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Gambar (Opsional)') }}
                            </label>
                            @if ($question->question_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($question->question_image) }}" alt="Question Image"
                                        class="max-w-xs rounded border">
                                    <p class="text-sm text-gray-500 mt-1">{{ __('Gambar saat ini') }}</p>
                                </div>
                            @endif
                            <input type="file" name="question_image" id="question_image" accept="image/*"
                                class="w-full">
                            <p class="text-sm text-gray-500 mt-1">{{ __('Maksimal 2MB (JPG, PNG, GIF)') }}</p>
                        </div>

                        <!-- MCQ Options Section -->
                        <div id="mcq-section"
                            class="mb-6 {{ $question->type === 'mcq_single' || $question->type === 'mcq_multiple' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Opsi Jawaban') }} <span class="text-red-500">*</span>
                            </label>
                            <div id="options-container" class="space-y-2 mb-3">
                                <!-- Existing options will be populated by JavaScript -->
                            </div>
                            <button type="button" id="add-option" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-plus-circle mr-1"></i>{{ __('Add Option') }}
                            </button>

                            <!-- Correct Answer (Single) -->
                            <div id="correct-answer-single"
                                class="mt-4 {{ $question->type === 'mcq_single' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Jawaban Benar') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="correct_answer_single"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Pilih Jawaban Benar') }}</option>
                                </select>
                            </div>

                            <!-- Correct Answer (Multiple) -->
                            <div id="correct-answer-multiple"
                                class="mt-4 {{ $question->type === 'mcq_multiple' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Jawaban Benar (Pilih Semua yang Benar)') }} <span class="text-red-500">*</span>
                                </label>
                                <div id="correct-checkboxes" class="space-y-2">
                                    <!-- Checkboxes will be added by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Matching Pairs Section -->
                        <div id="matching-section" class="mb-6 {{ $question->type === 'matching' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Pasangan') }} <span class="text-red-500">*</span>
                            </label>
                            <div id="pairs-container" class="space-y-3">
                                <!-- Pairs will be added by JavaScript -->
                            </div>
                            <button type="button" id="add-pair" class="mt-3 text-blue-600 hover:text-blue-900">
                                <i class="fas fa-plus-circle mr-1"></i>{{ __('Add Pair') }}
                            </button>
                        </div>

                        <!-- Essay Configuration Section -->
                        <div id="essay-section" class="mb-6 {{ $question->type === 'essay' ? '' : 'hidden' }}">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-robot mr-2"></i>{{ __('Sistem Penilaian Essay') }}
                                </h4>
                                <p class="text-sm text-blue-700">
                                    {{ __('Pilih mode penilaian untuk soal essay. Anda bisa gunakan auto-grading atau manual review.') }}
                                </p>
                            </div>

                            <!-- Essay Grading Mode -->
                            <div class="mb-4">
                                <label for="essay_grading_mode" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Mode Penilaian') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="essay_grading_mode" id="essay_grading_mode"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="manual"
                                        {{ old('essay_grading_mode', $question->essay_grading_mode) === 'manual' ? 'selected' : '' }}>
                                        {{ __('Manual (Guru Review)') }}
                                    </option>
                                    <option value="keyword"
                                        {{ old('essay_grading_mode', $question->essay_grading_mode) === 'keyword' ? 'selected' : '' }}>
                                        {{ __('Keyword Matching (Auto-Grade)') }}
                                    </option>
                                    <option value="similarity"
                                        {{ old('essay_grading_mode', $question->essay_grading_mode) === 'similarity' ? 'selected' : '' }}>
                                        {{ __('Similarity Matching (Auto-Grade)') }}
                                    </option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <b>{{ __('Manual:') }}</b> {{ __('Guru harus review semua jawaban') }} |
                                    <b>{{ __('Keyword:') }}</b> {{ __('Sistem cek kata kunci') }} |
                                    <b>{{ __('Similarity:') }}</b> {{ __('Sistem bandingkan dengan jawaban model') }}
                                </p>
                            </div>

                            <!-- Case Sensitive -->
                            <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="essay_case_sensitive" id="essay_case_sensitive"
                                            {{ old('essay_case_sensitive', $question->essay_case_sensitive) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Case Sensitive (huruf besar/kecil berpengaruh)') }}</span>
                                </label>
                            </div>

                            <!-- Keyword Matching Fields -->
                            <div id="keyword-fields"
                                class="{{ old('essay_grading_mode', $question->essay_grading_mode) === 'keyword' ? '' : 'hidden' }}">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-medium text-yellow-900 mb-2">{{ __('Kata Kunci & Poin') }}</h5>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        {{ __('Add keywords that must appear in the student answer. The system will award points based on keywords found.') }}
                                    </p>
                                    <div id="keywords-container" class="space-y-2">
                                        <!-- Keywords will be added by JavaScript -->
                                    </div>
                                    <button type="button" id="add-keyword"
                                        class="mt-3 text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-plus-circle mr-1"></i>{{ __('Add Keyword') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Similarity Matching Fields -->
                            <div id="similarity-fields"
                                class="{{ old('essay_grading_mode', $question->essay_grading_mode) === 'similarity' ? '' : 'hidden' }}">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-medium text-green-900 mb-2">{{ __('Jawaban Model') }}</h5>
                                    <p class="text-sm text-green-700 mb-3">
                                        {{ __('Berikan jawaban yang benar/ideal. Sistem akan membandingkan jawaban siswa dengan jawaban model ini.') }}
                                    </p>
                                    <textarea name="essay_model_answer" id="essay_model_answer" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="{{ __('Contoh: Fotosintesis adalah proses tumbuhan membuat makanan menggunakan cahaya matahari...') }}">{{ old('essay_model_answer', $question->essay_model_answer) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="essay_min_similarity"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('Minimal Similarity (%)') }}
                                    </label>
                                    <input type="number" name="essay_min_similarity" id="essay_min_similarity"
                                        value="{{ old('essay_min_similarity', $question->essay_min_similarity ?? 70) }}"
                                        min="0" max="100"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('Jika similarity di bawah nilai ini, poin akan dikurangi 50%') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Points & Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Poin') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" id="points"
                                    value="{{ old('points', $question->points) }}" min="0" step="0.01"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Urutan') }}
                                </label>
                                <input type="number" name="order" id="order"
                                    value="{{ old('order', $question->order) }}" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Kosongkan untuk urutan otomatis') }}</p>
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div class="mb-6">
                            <label for="explanation" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Penjelasan (Opsional)') }}
                            </label>
                            <textarea name="explanation" id="explanation" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('explanation', $question->explanation) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Penjelasan akan ditampilkan setelah siswa selesai mengerjakan') }}</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>{{ __('Update Soal') }}
                            </button>
                            <a href="{{ route('admin.exams.questions.index', $exam) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>

                    <!-- Debug Info -->
                    @if ($errors->any())
                        <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>{{ __('Validation Errors:') }}</strong>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let optionIndex = 0;
            let pairIndex = 0;
            const optionLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            const editLocale = {
                maxLimitTitle: @json(__('Batas Maksimal')),
                maxLimitText: @json(__('Maksimal :count opsi')),
                optionPlaceholder: @json(__('Teks opsi :letter')),
                leftPlaceholder: @json__('Item kiri'),
                rightPlaceholder: @json__('Item kanan'),
                keywordPlaceholder: @json__('Kata kunci (contoh: fotosintesis)'),
                keywordPointPlaceholder: @json__('Poin'),
                modelPlaceholder: @json__('Contoh: Fotosintesis adalah proses tumbuhan membuat makanan menggunakan cahaya matahari...'),
                validationTitle: @json__('Validasi Gagal'),
                validationSingle: @json__('Pilih jawaban benar untuk pilihan ganda!'),
                validationMultiple: @json__('Pilih minimal satu jawaban benar untuk pilihan ganda multiple!'),
                validationMatching: @json__('Semua pasangan harus diisi untuk soal menjodohkan!'),
                typeLabels: @json([
                    'mcq_single' => __('MCQ Single'),
                    'mcq_multiple' => __('MCQ Multiple'),
                    'matching' => __('Matching'),
                    'essay' => __('Essay'),
                ]),
                difficultyLabels: @json([
                    'easy' => __('Mudah'),
                    'medium' => __('Sedang'),
                    'hard' => __('Sulit'),
                ]),
            };

            // Existing question data
            const existingQuestion = @json($question);

            // Question type change handler
            document.getElementById('type').addEventListener('change', function() {
                const type = this.value;
                const isMcq = type === 'mcq_single' || type === 'mcq_multiple';
                const isMatching = type === 'matching';
                const isEssay = type === 'essay';

                // Toggle visibility
                document.getElementById('mcq-section').classList.toggle('hidden', !isMcq);
                document.getElementById('matching-section').classList.toggle('hidden', !isMatching);
                document.getElementById('essay-section').classList.toggle('hidden', !isEssay);
                document.getElementById('correct-answer-single').classList.toggle('hidden', type !== 'mcq_single');
                document.getElementById('correct-answer-multiple').classList.toggle('hidden', type !==
                    'mcq_multiple');

                // Update required attributes
                document.querySelectorAll('#mcq-section input[type="text"]').forEach(input => {
                    if (isMcq) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });

                document.querySelectorAll('#matching-section input[type="text"]').forEach(input => {
                    if (isMatching) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });

                const correctSelect = document.querySelector('select[name="correct_answer_single"]');
                if (type === 'mcq_single') {
                    correctSelect.setAttribute('required', 'required');
                } else {
                    correctSelect.removeAttribute('required');
                }
            });

            // Add MCQ Option
            document.getElementById('add-option').addEventListener('click', function() {
                if (optionIndex >= optionLetters.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: editLocale.maxLimitTitle,
                        text: editLocale.maxLimitText.replace(':count', optionLetters.length),
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const letter = optionLetters[optionIndex];
                const container = document.getElementById('options-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                <span class="font-bold text-gray-700 w-8">${letter}.</span>
                <input type="hidden" name="options[${optionIndex}][id]" value="${letter}">
                <input type="text" name="options[${optionIndex}][text]" required 
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="${editLocale.optionPlaceholder.replace(':letter', letter)}">
                <button type="button" class="text-red-600 hover:text-red-900 remove-option">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
                container.appendChild(div);

                // Add to correct answer dropdown
                const select = document.querySelector('select[name="correct_answer_single"]');
                const option = document.createElement('option');
                option.value = letter;
                option.textContent = letter;
                select.appendChild(option);

                // Add checkbox for multiple choice
                const checkboxDiv = document.getElementById('correct-checkboxes');
                const checkbox = document.createElement('label');
                checkbox.className = 'flex items-center';
                checkbox.innerHTML = `
                <input type="checkbox" name="correct_answer_multiple[]" value="${letter}" 
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">${letter}</span>
            `;
                checkboxDiv.appendChild(checkbox);

                optionIndex++;
            });

            // Remove MCQ Option
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-option')) {
                    e.target.closest('.flex').remove();
                }
            });

            // Add Matching Pair
            document.getElementById('add-pair').addEventListener('click', function() {
                const container = document.getElementById('pairs-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                <input type="text" name="pairs[${pairIndex}][left]" required 
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="${editLocale.leftPlaceholder}">
                <i class="fas fa-arrows-alt-h text-gray-400"></i>
                <input type="text" name="pairs[${pairIndex}][right]" required 
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="${editLocale.rightPlaceholder}">
                <button type="button" class="text-red-600 hover:text-red-900 remove-pair">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
                container.appendChild(div);
                pairIndex++;
            });

            // Remove Matching Pair
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-pair')) {
                    e.target.closest('.flex').remove();
                }
            });

            // Essay Grading Mode Change Handler
            let keywordIndex = 0;
            document.getElementById('essay_grading_mode')?.addEventListener('change', function() {
                const mode = this.value;
                const keywordFields = document.getElementById('keyword-fields');
                const similarityFields = document.getElementById('similarity-fields');

                // Toggle visibility
                keywordFields.classList.toggle('hidden', mode !== 'keyword');
                similarityFields.classList.toggle('hidden', mode !== 'similarity');
            });

            // Add Keyword
            document.getElementById('add-keyword')?.addEventListener('click', function() {
                const container = document.getElementById('keywords-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                <input type="text" name="essay_keywords[]" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="${editLocale.keywordPlaceholder}">
                <input type="number" name="essay_keyword_points[]" min="0" step="0.1" value="2"
                    class="w-24 rounded-md border-gray-300 shadow-sm" placeholder="${editLocale.keywordPointPlaceholder}">
                <button type="button" class="text-red-600 hover:text-red-900 remove-keyword">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
                container.appendChild(div);
                keywordIndex++;
            });

            // Remove Keyword
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-keyword')) {
                    e.target.closest('.flex').remove();
                }
            });

            // Initialize with existing data
            document.addEventListener('DOMContentLoaded', function() {
                const type = existingQuestion.type;

                // Load existing MCQ options
                if (type === 'mcq_single' || type === 'mcq_multiple') {
                    existingQuestion.options.forEach((option, index) => {
                        optionIndex = index;
                        document.getElementById('add-option').click();
                        document.querySelector(`input[name="options[${index}][text]"]`).value = option.text;
                    });
                    optionIndex = existingQuestion.options.length;

                    // Set correct answer
                    if (type === 'mcq_single') {
                        document.querySelector('select[name="correct_answer_single"]').value = existingQuestion
                            .correct_answer;
                    } else {
                        existingQuestion.correct_answer.forEach(answer => {
                            const checkbox = document.querySelector(
                                `input[type="checkbox"][value="${answer}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                }

                // Load existing matching pairs
                if (type === 'matching' && existingQuestion.pairs) {
                    existingQuestion.pairs.forEach((pair, index) => {
                        pairIndex = index;
                        document.getElementById('add-pair').click();
                        document.querySelector(`input[name="pairs[${index}][left]"]`).value = pair.left;
                        document.querySelector(`input[name="pairs[${index}][right]"]`).value = pair.right;
                    });
                    pairIndex = existingQuestion.pairs.length;
                }

                // Load existing essay keywords
                if (type === 'essay' && existingQuestion.essay_keywords) {
                    existingQuestion.essay_keywords.forEach((keyword, index) => {
                        keywordIndex = index;
                        document.getElementById('add-keyword').click();
                        document.querySelector(
                                `#keywords-container > div:last-child input[name="essay_keywords[]"]`).value =
                            keyword;
                        document.querySelector(
                                `#keywords-container > div:last-child input[name="essay_keyword_points[]"]`)
                            .value = existingQuestion.essay_keyword_points[index];
                    });
                    keywordIndex = existingQuestion.essay_keywords.length;
                }
            });
        </script>
    @endpush
</x-app-layout>
