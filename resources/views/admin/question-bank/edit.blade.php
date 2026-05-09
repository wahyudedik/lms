<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit Question') }}
            </h2>
            <a href="{{ route('admin.question-bank.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back to Bank') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.question-bank.update', $questionBank) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Informasi Dasar') }}
                            </h3>

                            <!-- Category -->
                            <div class="mb-6">
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-folder text-gray-400 mr-1"></i>{{ __('Category (Optional)') }}
                                </label>
                                <select name="category_id" id="category_id"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('-- No Category --') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $questionBank->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->full_path }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Difficulty & Points Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Difficulty -->
                                <div>
                                    <label for="difficulty" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-signal text-gray-400 mr-1"></i>{{ __('Difficulty') }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <select name="difficulty" id="difficulty" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                        <option value="easy"
                                            {{ old('difficulty', $questionBank->difficulty) == 'easy' ? 'selected' : '' }}>
                                            {{ __('Easy') }}</option>
                                        <option value="medium"
                                            {{ old('difficulty', $questionBank->difficulty) == 'medium' ? 'selected' : '' }}>
                                            {{ __('Medium') }}</option>
                                        <option value="hard"
                                            {{ old('difficulty', $questionBank->difficulty) == 'hard' ? 'selected' : '' }}>
                                            {{ __('Hard') }}</option>
                                    </select>
                                </div>

                                <!-- Default Points -->
                                <div>
                                    <label for="default_points" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-star text-gray-400 mr-1"></i>{{ __('Default Points') }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="default_points" id="default_points"
                                        value="{{ old('default_points', $questionBank->default_points) }}"
                                        step="0.01" min="0.01" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-end">
                                    <label class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                                        <input type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $questionBank->is_active) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-green-600 shadow-sm">
                                        <span
                                            class="ml-2 text-sm font-semibold text-gray-700">{{ __('Active') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-6">
                                <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-tags text-gray-400 mr-1"></i>{{ __('Tags (comma-separated)') }}
                                </label>
                                <input type="text" name="tags" id="tags"
                                    value="{{ old('tags', is_array($questionBank->tags) ? implode(', ', $questionBank->tags) : $questionBank->tags) }}"
                                    placeholder="{{ __('algebra, equations, linear') }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ __('Separate tags with commas for better search') }}</p>
                            </div>
                        </div>

                        <!-- Question Content -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-question-circle text-green-600 mr-2"></i>{{ __('Konten Soal') }}
                            </h3>

                            <!-- Question Type -->
                            <div class="mb-6">
                                <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-list text-gray-400 mr-1"></i>Tipe Soal <span
                                        class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="mcq_single"
                                        {{ old('type', $questionBank->type) == 'mcq_single' ? 'selected' : '' }}>
                                        Pilihan Ganda (Single Answer)
                                    </option>
                                    <option value="mcq_multiple"
                                        {{ old('type', $questionBank->type) == 'mcq_multiple' ? 'selected' : '' }}>
                                        Pilihan Ganda (Multiple Answers)
                                    </option>
                                    <option value="matching"
                                        {{ old('type', $questionBank->type) == 'matching' ? 'selected' : '' }}>
                                        Menjodohkan
                                    </option>
                                    <option value="essay"
                                        {{ old('type', $questionBank->type) == 'essay' ? 'selected' : '' }}>
                                        Essay
                                    </option>
                                </select>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-6">
                                <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>Pertanyaan <span
                                        class="text-red-500">*</span>
                                </label>
                                <textarea name="question_text" id="question_text" rows="3" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('question_text', $questionBank->question_text) }}</textarea>
                            </div>

                            <!-- Question Image -->
                            <div class="mb-6">
                                <label for="question_image" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-image text-gray-400 mr-1"></i>Gambar (Opsional)
                                </label>
                                @if ($questionBank->question_image)
                                    <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                        <p class="text-sm font-semibold text-green-900 mb-2">
                                            <i class="fas fa-image text-green-700 mr-1"></i>{{ __('Gambar Saat Ini') }}
                                        </p>
                                        <img src="{{ Storage::url($questionBank->question_image) }}"
                                            alt="Question Image"
                                            class="max-w-xs rounded-lg shadow-md border-2 border-green-200">
                                    </div>
                                @endif
                                <input type="file" name="question_image" id="question_image" accept="image/*"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="text-sm text-gray-500 mt-1">Maksimal 2MB (JPG, PNG, GIF)</p>
                            </div>
                        </div>

                        <!-- MCQ Options Section -->
                        <div id="mcq-section" class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-list-ul text-purple-600 mr-2"></i>{{ __('Opsi Jawaban') }}
                            </h3>
                            <div id="options-container" class="space-y-3 mb-4">
                                <!-- Options will be added by JavaScript -->
                            </div>
                            <button type="button" id="add-option"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus-circle"></i>{{ __('Add Option') }}
                            </button>

                            <div id="correct-answer-single" class="mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-check-circle text-gray-400 mr-1"></i>Jawaban Benar <span
                                        class="text-red-500">*</span>
                                </label>
                                <select name="correct_answer_single"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">Pilih Jawaban Benar</option>
                                </select>
                            </div>

                            <div id="correct-answer-multiple" class="mt-6 hidden">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-check-double text-gray-400 mr-1"></i>Jawaban Benar (Pilih Semua
                                    yang Benar) <span class="text-red-500">*</span>
                                </label>
                                <div id="correct-checkboxes" class="space-y-2">
                                    <!-- Checkboxes will be added by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Matching Pairs Section -->
                        <div id="matching-section" class="mb-8 pb-8 border-b border-gray-200 hidden">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-exchange-alt text-orange-600 mr-2"></i>{{ __('Pasangan') }}
                            </h3>
                            <div id="pairs-container" class="space-y-3 mb-4">
                                <!-- Pairs will be added by JavaScript -->
                            </div>
                            <button type="button" id="add-pair"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus-circle"></i>{{ __('Add Pair') }}
                            </button>
                        </div>

                        <!-- Essay Configuration Section -->
                        <div id="essay-section" class="mb-8 pb-8 border-b border-gray-200 hidden">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-pen text-indigo-600 mr-2"></i>{{ __('Konfigurasi Essay') }}
                            </h3>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <h4 class="font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-robot mr-2"></i>Sistem Penilaian Essay
                                </h4>
                                <p class="text-sm text-blue-700">
                                    Pilih mode penilaian untuk soal essay. Anda bisa gunakan auto-grading atau manual
                                    review.
                                </p>
                            </div>

                            <!-- Essay Grading Mode -->
                            <div class="mb-6">
                                <label for="essay_grading_mode"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-cog text-gray-400 mr-1"></i>Mode Penilaian <span
                                        class="text-red-500">*</span>
                                </label>
                                <select name="essay_grading_mode" id="essay_grading_mode"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="manual">Manual (Guru Review)</option>
                                    <option value="keyword">Keyword Matching (Auto-Grade)</option>
                                    <option value="similarity">Similarity Matching (Auto-Grade)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <b>Manual:</b> Guru harus review semua jawaban |
                                    <b>Keyword:</b> Sistem cek kata kunci |
                                    <b>Similarity:</b> Sistem bandingkan dengan jawaban model
                                </p>
                            </div>

                            <!-- Case Sensitive -->
                            <div class="mb-6">
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <input type="checkbox" name="essay_case_sensitive" id="essay_case_sensitive"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Case Sensitive (huruf
                                        besar/kecil
                                        berpengaruh)</span>
                                </label>
                            </div>

                            <!-- Keyword Matching Fields -->
                            <div id="keyword-fields" class="hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-semibold text-yellow-900 mb-2">
                                        <i class="fas fa-key text-yellow-700 mr-2"></i>Kata Kunci & Poin
                                    </h5>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        Tambahkan kata kunci yang harus ada dalam jawaban siswa. Sistem akan menghitung
                                        poin berdasarkan kata kunci yang ditemukan.
                                    </p>
                                    <div id="keywords-container" class="space-y-2 mb-3">
                                        <!-- Keywords will be added by JavaScript -->
                                    </div>
                                    <button type="button" id="add-keyword"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-plus-circle"></i>{{ __('Add Keyword') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Similarity Matching Fields -->
                            <div id="similarity-fields" class="hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-semibold text-green-900 mb-2">
                                        <i class="fas fa-check-double text-green-700 mr-2"></i>Jawaban Model
                                    </h5>
                                    <p class="text-sm text-green-700 mb-3">
                                        Berikan jawaban yang benar/ideal. Sistem akan membandingkan jawaban siswa dengan
                                        jawaban model ini.
                                    </p>
                                    <textarea name="essay_model_answer" id="essay_model_answer" rows="4"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-all duration-150"
                                        placeholder="Contoh: Fotosintesis adalah proses tumbuhan membuat makanan menggunakan cahaya matahari..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="essay_min_similarity"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-percentage text-gray-400 mr-1"></i>Minimal Similarity (%)
                                    </label>
                                    <input type="number" name="essay_min_similarity" id="essay_min_similarity"
                                        value="70" min="0" max="100"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-all duration-150">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Jika similarity di bawah nilai ini, poin akan dikurangi 50%
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info text-teal-600 mr-2"></i>{{ __('Informasi Tambahan') }}
                            </h3>

                            <!-- Explanation -->
                            <div class="mb-6">
                                <label for="explanation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lightbulb text-gray-400 mr-1"></i>Explanation (Optional)
                                </label>
                                <textarea name="explanation" id="explanation" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('explanation', $questionBank->explanation) }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Explanation shown to students after answering</p>
                            </div>

                            <!-- Teacher Notes -->
                            <div class="mb-6">
                                <label for="teacher_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note text-gray-400 mr-1"></i>Teacher Notes (Optional -
                                    Private)
                                </label>
                                <textarea name="teacher_notes" id="teacher_notes" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('teacher_notes', $questionBank->teacher_notes) }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Private notes for teachers only, not visible to
                                    students</p>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.question-bank.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Update Question') }}
                            </button>
                        </div>
                    </form>

                    <!-- Debug Info -->
                    @if ($errors->any())
                        <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Validation Errors:</strong>
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
                document.getElementById('correct-answer-multiple').classList.toggle('hidden', type !== 'mcq_multiple');

                // Update required attributes for MCQ options
                document.querySelectorAll('#mcq-section input[type="text"]').forEach(input => {
                    if (isMcq) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });

                // Update required attributes for Matching pairs
                document.querySelectorAll('#matching-section input[type="text"]').forEach(input => {
                    if (isMatching) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });

                // Update correct answer select
                const correctSelect = document.querySelector('select[name="correct_answer_single"]');
                if (type === 'mcq_single') {
                    correctSelect.setAttribute('required', 'required');
                } else {
                    correctSelect.removeAttribute('required');
                }

                // Clear and reinitialize fields when type changes
                const optionsContainer = document.getElementById('options-container');
                const pairsContainer = document.getElementById('pairs-container');

                if (!isMcq && optionsContainer.children.length === 0) {
                    // If switching from MCQ, we might need to clear
                } else if (isMcq && optionsContainer.children.length === 0) {
                    // Add default options if none exist
                    optionIndex = 0;
                    document.querySelector('select[name="correct_answer_single"]').innerHTML =
                        '<option value="">Pilih Jawaban Benar</option>';
                    document.getElementById('correct-checkboxes').innerHTML = '';
                    for (let i = 0; i < 4; i++) {
                        document.getElementById('add-option').click();
                    }
                }

                if (isMatching && pairsContainer.children.length === 0) {
                    // Add default pairs if none exist
                    pairIndex = 0;
                    for (let i = 0; i < 4; i++) {
                        document.getElementById('add-pair').click();
                    }
                }
            });

            // Add MCQ Option
            document.getElementById('add-option').addEventListener('click', function() {
                if (optionIndex >= optionLetters.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Maksimal',
                        text: 'Maksimal ' + optionLetters.length + ' opsi',
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
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150" placeholder="Teks opsi ${letter}">
                    <button type="button" class="text-red-600 hover:text-red-800 remove-option">
                        <i class="fas fa-times-circle text-xl"></i>
                    </button>
                `;
                container.appendChild(div);

                // Add to correct answer dropdown
                const select = document.querySelector('select[name="correct_answer_single"]');
                const option = document.createElement('option');
                option.value = letter;
                option.textContent = letter;
                select.appendChild(option);

                // Add checkbox for multiple answers
                const checkboxContainer = document.getElementById('correct-checkboxes');
                const checkDiv = document.createElement('div');
                checkDiv.className = 'flex items-center';
                checkDiv.innerHTML = `
                <input type="checkbox" name="correct_answer_multiple[]" value="${letter}" id="check_${letter}"
                    class="rounded border-gray-300 text-blue-600">
                <label for="check_${letter}" class="ml-2 text-sm text-gray-700">${letter}</label>
            `;
                checkboxContainer.appendChild(checkDiv);

                optionIndex++;
            });

            // Remove MCQ Option
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-option')) {
                    const div = e.target.closest('.flex');
                    const letter = div.querySelector('input[type="hidden"]').value;

                    // Remove from dropdown
                    document.querySelector(`select[name="correct_answer_single"] option[value="${letter}"]`)?.remove();

                    // Remove checkbox
                    document.getElementById(`check_${letter}`)?.closest('.flex').remove();

                    div.remove();
                }
            });

            // Add Matching Pair
            document.getElementById('add-pair').addEventListener('click', function() {
                const container = document.getElementById('pairs-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                    <input type="text" name="pairs[${pairIndex}][left]" required
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150" placeholder="Item kiri">
                    <i class="fas fa-arrows-alt-h text-gray-400"></i>
                    <input type="text" name="pairs[${pairIndex}][right]" required
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150" placeholder="Item kanan">
                    <button type="button" class="text-red-600 hover:text-red-800 remove-pair">
                        <i class="fas fa-times-circle text-xl"></i>
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

                // Initialize keywords if switching to keyword mode
                if (mode === 'keyword' && document.getElementById('keywords-container').children.length === 0) {
                    // Add 3 default keyword fields
                    for (let i = 0; i < 3; i++) {
                        document.getElementById('add-keyword').click();
                    }
                }
            });

            // Add Keyword
            document.getElementById('add-keyword')?.addEventListener('click', function() {
                const container = document.getElementById('keywords-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                    <input type="text" name="essay_keywords[]"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition-all duration-150" placeholder="Kata kunci (contoh: fotosintesis)">
                    <input type="number" name="essay_keyword_points[]" min="0" step="0.1" value="2"
                        class="w-24 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition-all duration-150" placeholder="Poin">
                    <button type="button" class="text-red-600 hover:text-red-800 remove-keyword">
                        <i class="fas fa-times-circle text-xl"></i>
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

            // Initialize with default options/pairs
            document.addEventListener('DOMContentLoaded', function() {
                // Form submission validation
                document.querySelector('form').addEventListener('submit', function(e) {
                    const type = document.getElementById('type').value;

                    // Check MCQ Single
                    if (type === 'mcq_single') {
                        const correctAnswer = document.querySelector('select[name="correct_answer_single"]')
                            .value;
                        if (!correctAnswer) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Pilih jawaban benar untuk pilihan ganda!',
                                confirmButtonColor: '#3085d6'
                            });
                            return false;
                        }
                    }

                    // Check MCQ Multiple
                    if (type === 'mcq_multiple') {
                        const checkedBoxes = document.querySelectorAll(
                            'input[name="correct_answer_multiple[]"]:checked');
                        if (checkedBoxes.length === 0) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Pilih minimal satu jawaban benar untuk pilihan ganda multiple!',
                                confirmButtonColor: '#3085d6'
                            });
                            return false;
                        }
                    }

                    // Check Matching pairs
                    if (type === 'matching') {
                        const pairInputs = document.querySelectorAll('#pairs-container input[type="text"]');
                        let hasEmptyPair = false;
                        pairInputs.forEach(input => {
                            if (!input.value.trim()) {
                                hasEmptyPair = true;
                            }
                        });
                        if (hasEmptyPair) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Semua pasangan harus diisi untuk soal menjodohkan!',
                                confirmButtonColor: '#3085d6'
                            });
                            return false;
                        }
                    }
                });
                const typeSelect = document.getElementById('type');
                const type = typeSelect.value;

                // First, trigger type change to set correct visibility
                typeSelect.dispatchEvent(new Event('change'));

                // Populate existing question data
                const questionData = @json($questionBank);
                const questionType = questionData.type;

                // Set question type and trigger change
                document.getElementById('type').value = questionType;
                document.getElementById('type').dispatchEvent(new Event('change'));

                // Populate MCQ options if exists
                if ((questionType === 'mcq_single' || questionType === 'mcq_multiple') && questionData.options) {
                    setTimeout(function() {
                        // options is already an array (cast by Eloquent), no need for JSON.parse
                        const options = Array.isArray(questionData.options) ?
                            questionData.options :
                            JSON.parse(questionData.options);
                        optionIndex = 0;
                        document.getElementById('options-container').innerHTML = '';
                        document.querySelector('select[name="correct_answer_single"]').innerHTML =
                            '<option value="">Pilih Jawaban Benar</option>';
                        document.getElementById('correct-checkboxes').innerHTML = '';

                        options.forEach(function(option) {
                            document.getElementById('add-option').click();
                            const lastInput = document.querySelector(
                                `input[name="options[${optionIndex-1}][text]"]`);
                            if (lastInput) lastInput.value = option.text;
                        });

                        // Set correct answer
                        if (questionType === 'mcq_single' && questionData.correct_answer) {
                            document.querySelector('select[name="correct_answer_single"]').value = questionData
                                .correct_answer;
                        } else if (questionType === 'mcq_multiple' && questionData.correct_answer_multiple) {
                            // correct_answer_multiple is already an array
                            const correctAnswers = Array.isArray(questionData.correct_answer_multiple) ?
                                questionData.correct_answer_multiple :
                                JSON.parse(questionData.correct_answer_multiple);
                            correctAnswers.forEach(function(answer) {
                                const checkbox = document.getElementById(`check_${answer}`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }
                    }, 200);
                }

                // Populate matching pairs if exists
                if (questionType === 'matching' && questionData.pairs) {
                    setTimeout(function() {
                        // pairs is already an array (cast by Eloquent), no need for JSON.parse
                        const pairs = Array.isArray(questionData.pairs) ?
                            questionData.pairs :
                            JSON.parse(questionData.pairs);
                        pairIndex = 0;
                        document.getElementById('pairs-container').innerHTML = '';

                        pairs.forEach(function(pair) {
                            document.getElementById('add-pair').click();
                            const leftInput = document.querySelector(
                                `input[name="pairs[${pairIndex-1}][left]"]`);
                            const rightInput = document.querySelector(
                                `input[name="pairs[${pairIndex-1}][right]"]`);
                            if (leftInput) leftInput.value = pair.left;
                            if (rightInput) rightInput.value = pair.right;
                        });
                    }, 200);
                }

                // Populate essay configuration if exists
                if (questionType === 'essay' && questionData.essay_config) {
                    setTimeout(function() {
                        const config = JSON.parse(questionData.essay_config);
                        document.getElementById('essay_grading_mode').value = config.grading_mode || 'manual';
                        document.getElementById('essay_grading_mode').dispatchEvent(new Event('change'));

                        if (config.case_sensitive) {
                            document.getElementById('essay_case_sensitive').checked = true;
                        }

                        if (config.grading_mode === 'keyword' && config.keywords) {
                            keywordIndex = 0;
                            document.getElementById('keywords-container').innerHTML = '';
                            config.keywords.forEach(function(kw) {
                                document.getElementById('add-keyword').click();
                                const inputs = document.querySelectorAll(
                                    '#keywords-container input[name="essay_keywords[]"]');
                                const pointInputs = document.querySelectorAll(
                                    '#keywords-container input[name="essay_keyword_points[]"]');
                                if (inputs[keywordIndex - 1]) inputs[keywordIndex - 1].value = kw
                                    .keyword;
                                if (pointInputs[keywordIndex - 1]) pointInputs[keywordIndex - 1].value =
                                    kw.points;
                            });
                        }

                        if (config.grading_mode === 'similarity') {
                            document.getElementById('essay_model_answer').value = config.model_answer || '';
                            document.getElementById('essay_min_similarity').value = config.min_similarity || 70;
                        }
                    }, 200);
                }
            });
        </script>
    @endpush
</x-app-layout>
