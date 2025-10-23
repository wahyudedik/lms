<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Soal - {{ $exam->title }}
            </h2>
            <a href="{{ route('guru.exams.questions.index', $exam) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('guru.exams.questions.store', $exam) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Question Type -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Soal <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="mcq_single" {{ old('type') == 'mcq_single' ? 'selected' : '' }}>
                                    Pilihan Ganda (Single Answer)
                                </option>
                                <option value="mcq_multiple" {{ old('type') == 'mcq_multiple' ? 'selected' : '' }}>
                                    Pilihan Ganda (Multiple Answers)
                                </option>
                                <option value="matching" {{ old('type') == 'matching' ? 'selected' : '' }}>
                                    Menjodohkan
                                </option>
                                <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>
                                    Essay
                                </option>
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">
                                Pertanyaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question_text" id="question_text" rows="3" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('question_text') }}</textarea>
                        </div>

                        <!-- Question Image -->
                        <div class="mb-6">
                            <label for="question_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Gambar (Opsional)
                            </label>
                            <input type="file" name="question_image" id="question_image" accept="image/*"
                                class="w-full">
                            <p class="text-sm text-gray-500 mt-1">Maksimal 2MB (JPG, PNG, GIF)</p>
                        </div>

                        <!-- MCQ Options Section -->
                        <div id="mcq-section" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Opsi Jawaban <span class="text-red-500">*</span>
                            </label>
                            <div id="options-container" class="space-y-3">
                                <!-- Options will be added by JavaScript -->
                            </div>
                            <button type="button" id="add-option" class="mt-3 text-blue-600 hover:text-blue-900">
                                <i class="fas fa-plus-circle mr-1"></i>Tambah Opsi
                            </button>

                            <div id="correct-answer-single" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jawaban Benar <span class="text-red-500">*</span>
                                </label>
                                <select name="correct_answer_single" class="w-full rounded-md border-gray-300">
                                    <option value="">Pilih Jawaban Benar</option>
                                </select>
                            </div>

                            <div id="correct-answer-multiple" class="mt-4 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jawaban Benar (Pilih Semua yang Benar) <span class="text-red-500">*</span>
                                </label>
                                <div id="correct-checkboxes" class="space-y-2">
                                    <!-- Checkboxes will be added by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Matching Pairs Section -->
                        <div id="matching-section" class="mb-6 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pasangan <span class="text-red-500">*</span>
                            </label>
                            <div id="pairs-container" class="space-y-3">
                                <!-- Pairs will be added by JavaScript -->
                            </div>
                            <button type="button" id="add-pair" class="mt-3 text-blue-600 hover:text-blue-900">
                                <i class="fas fa-plus-circle mr-1"></i>Tambah Pasangan
                            </button>
                        </div>

                        <!-- Essay Configuration Section -->
                        <div id="essay-section" class="mb-6 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-robot mr-2"></i>Sistem Penilaian Essay
                                </h4>
                                <p class="text-sm text-blue-700">
                                    Pilih mode penilaian untuk soal essay. Anda bisa gunakan auto-grading atau manual
                                    review.
                                </p>
                            </div>

                            <!-- Essay Grading Mode -->
                            <div class="mb-4">
                                <label for="essay_grading_mode" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mode Penilaian <span class="text-red-500">*</span>
                                </label>
                                <select name="essay_grading_mode" id="essay_grading_mode"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="essay_case_sensitive" id="essay_case_sensitive"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Case Sensitive (huruf besar/kecil
                                        berpengaruh)</span>
                                </label>
                            </div>

                            <!-- Keyword Matching Fields -->
                            <div id="keyword-fields" class="hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-medium text-yellow-900 mb-2">Kata Kunci & Poin</h5>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        Tambahkan kata kunci yang harus ada dalam jawaban siswa. Sistem akan menghitung
                                        poin berdasarkan kata kunci yang ditemukan.
                                    </p>
                                    <div id="keywords-container" class="space-y-2">
                                        <!-- Keywords will be added by JavaScript -->
                                    </div>
                                    <button type="button" id="add-keyword"
                                        class="mt-3 text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-plus-circle mr-1"></i>Tambah Kata Kunci
                                    </button>
                                </div>
                            </div>

                            <!-- Similarity Matching Fields -->
                            <div id="similarity-fields" class="hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-medium text-green-900 mb-2">Jawaban Model</h5>
                                    <p class="text-sm text-green-700 mb-3">
                                        Berikan jawaban yang benar/ideal. Sistem akan membandingkan jawaban siswa dengan
                                        jawaban model ini.
                                    </p>
                                    <textarea name="essay_model_answer" id="essay_model_answer" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Contoh: Fotosintesis adalah proses tumbuhan membuat makanan menggunakan cahaya matahari..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="essay_min_similarity"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Minimal Similarity (%)
                                    </label>
                                    <input type="number" name="essay_min_similarity" id="essay_min_similarity"
                                        value="70" min="0" max="100"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Jika similarity di bawah nilai ini, poin akan dikurangi 50%
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Points & Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                                    Poin <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" id="points" value="{{ old('points', 10) }}"
                                    min="0" step="0.01" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan
                                </label>
                                <input type="number" name="order" id="order" value="{{ old('order') }}"
                                    min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-sm text-gray-500 mt-1">Kosongkan untuk urutan otomatis</p>
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div class="mb-6">
                            <label for="explanation" class="block text-sm font-medium text-gray-700 mb-2">
                                Penjelasan (Opsional)
                            </label>
                            <textarea name="explanation" id="explanation" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('explanation') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Penjelasan akan ditampilkan setelah siswa selesai
                                mengerjakan</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Simpan Soal
                            </button>
                            <a href="{{ route('guru.exams.questions.index', $exam) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
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
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Teks opsi ${letter}">
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
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Item kiri">
                <i class="fas fa-arrows-alt-h text-gray-400"></i>
                <input type="text" name="pairs[${pairIndex}][right]" required 
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Item kanan">
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
                    class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Kata kunci (contoh: fotosintesis)">
                <input type="number" name="essay_keyword_points[]" min="0" step="0.1" value="2"
                    class="w-24 rounded-md border-gray-300 shadow-sm" placeholder="Poin">
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

                // Then add default fields based on type
                setTimeout(function() {
                    if (type === 'mcq_single' || type === 'mcq_multiple') {
                        // Add 4 default options
                        for (let i = 0; i < 4; i++) {
                            document.getElementById('add-option').click();
                        }
                    } else if (type === 'matching') {
                        // Add 4 default pairs
                        for (let i = 0; i < 4; i++) {
                            document.getElementById('add-pair').click();
                        }
                    }
                }, 100);
            });
        </script>
    @endpush
</x-app-layout>
