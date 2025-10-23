<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kelola Soal: {{ $exam->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Total: {{ $questions->count() }} soal | {{ $exam->total_points }} poin
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="openImportModal()"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-database mr-2"></i>Import from Bank
                </button>
                <a href="{{ route('admin.exams.questions.create', $exam) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Tambah Soal
                </a>
                <a href="{{ route('admin.exams.show', $exam) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($questions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div id="questions-list" class="space-y-4">
                            @foreach ($questions as $question)
                                <div class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition"
                                    data-question-id="{{ $question->id }}">
                                    <div class="flex items-start gap-4">
                                        <!-- Drag Handle -->
                                        <div class="cursor-move text-gray-400 hover:text-gray-600 mt-2">
                                            <i class="fas fa-grip-vertical text-xl"></i>
                                        </div>

                                        <!-- Question Content -->
                                        <div class="flex-1">
                                            <!-- Question Header -->
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span
                                                        class="bg-gray-200 text-gray-800 text-sm font-medium px-3 py-1 rounded">
                                                        #{{ $question->order }}
                                                    </span>
                                                    <span
                                                        class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded">
                                                        <i class="{{ $question->type_icon }} mr-1"></i>
                                                        {{ $question->type_display }}
                                                    </span>
                                                    <span
                                                        class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded">
                                                        <i class="fas fa-star mr-1"></i>{{ $question->points }} poin
                                                    </span>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex gap-2">
                                                    <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}"
                                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('admin.exams.questions.duplicate', [$exam, $question]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900"
                                                            title="Duplikat">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirmDelete('Yakin ingin menghapus soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Question Text -->
                                            <div class="mb-3">
                                                <p class="text-gray-900 font-medium">{{ $question->question_text }}</p>
                                                @if ($question->question_image)
                                                    <img src="{{ Storage::url($question->question_image) }}"
                                                        alt="Question Image" class="mt-2 max-w-sm rounded">
                                                @endif
                                            </div>

                                            <!-- Question Details -->
                                            @if ($question->type === 'mcq_single' || $question->type === 'mcq_multiple')
                                                <div class="space-y-1">
                                                    @foreach ($question->options ?? [] as $option)
                                                        <div class="flex items-center text-sm">
                                                            @if ($question->type === 'mcq_single')
                                                                <span
                                                                    class="mr-2 {{ isset($option['id']) && $option['id'] === $question->correct_answer ? 'text-green-600 font-bold' : 'text-gray-500' }}">
                                                                    {{ $option['id'] ?? '' }}.
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="mr-2 {{ isset($option['id']) && in_array($option['id'], $question->correct_answer ?? []) ? 'text-green-600 font-bold' : 'text-gray-500' }}">
                                                                    {{ $option['id'] ?? '' }}.
                                                                </span>
                                                            @endif
                                                            <span
                                                                class="{{ ($question->type === 'mcq_single' && isset($option['id']) && $option['id'] === $question->correct_answer) || ($question->type === 'mcq_multiple' && isset($option['id']) && in_array($option['id'], $question->correct_answer ?? [])) ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                                                                {{ $option['text'] ?? '' }}
                                                            </span>
                                                            @if (
                                                                ($question->type === 'mcq_single' && isset($option['id']) && $option['id'] === $question->correct_answer) ||
                                                                    ($question->type === 'mcq_multiple' &&
                                                                        isset($option['id']) &&
                                                                        in_array($option['id'], $question->correct_answer ?? [])))
                                                                <i class="fas fa-check-circle text-green-600 ml-2"></i>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($question->type === 'matching')
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <p class="font-semibold text-gray-700 mb-1">Kolom Kiri:</p>
                                                        @foreach ($question->pairs ?? [] as $pair)
                                                            <div class="text-gray-700">• {{ $pair['left'] ?? '' }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-700 mb-1">Kolom Kanan:</p>
                                                        @foreach ($question->pairs ?? [] as $pair)
                                                            <div class="text-gray-700">• {{ $pair['right'] ?? '' }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @elseif($question->type === 'essay')
                                                <div class="text-sm text-gray-600 italic">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Soal esai - Memerlukan penilaian manual
                                                </div>
                                            @endif

                                            <!-- Explanation -->
                                            @if ($question->explanation)
                                                <div class="mt-3 pt-3 border-t border-gray-200">
                                                    <p class="text-xs font-semibold text-gray-600 mb-1">
                                                        <i class="fas fa-lightbulb mr-1"></i>Penjelasan:
                                                    </p>
                                                    <p class="text-sm text-gray-700">{{ $question->explanation }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Reorder Info -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Tips:</strong> Klik dan drag ikon <i class="fas fa-grip-vertical mx-1"></i>
                                untuk mengubah urutan soal.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-question text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-4">Belum ada soal ditambahkan.</p>
                            <a href="{{ route('admin.exams.questions.create', $exam) }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>Tambah Soal Pertama
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if ($questions->count() > 0)
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
            <script>
                // Initialize Sortable for drag-and-drop reordering
                const questionsList = document.getElementById('questions-list');
                if (questionsList) {
                    new Sortable(questionsList, {
                        animation: 150,
                        handle: '.cursor-move',
                        onEnd: function(evt) {
                            // Get new order of question IDs
                            const questionIds = Array.from(questionsList.children).map(el =>
                                el.getAttribute('data-question-id')
                            );

                            // Send to server
                            fetch('{{ route('admin.exams.questions.reorder', $exam) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        question_ids: questionIds
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Urutan soal berhasil diperbarui!'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'Gagal memperbarui urutan soal'
                                    });
                                });
                        }
                    });
                }
            </script>
        @endpush
    @endif

    <!-- Import from Bank Modal -->
    <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-database text-purple-600 mr-2"></i>
                    Import Questions from Bank
                </h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="text" id="bankSearch" placeholder="Search questions..."
                            class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            onkeyup="filterBankQuestions()">

                        <select id="bankCategory"
                            class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            onchange="filterBankQuestions()">
                            <option value="">All Categories</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->full_path }}</option>
                            @endforeach
                        </select>

                        <select id="bankType"
                            class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            onchange="filterBankQuestions()">
                            <option value="">All Types</option>
                            <option value="mcq_single">MCQ Single</option>
                            <option value="mcq_multiple">MCQ Multiple</option>
                            <option value="matching">Matching</option>
                            <option value="essay">Essay</option>
                        </select>

                        <select id="bankDifficulty"
                            class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            onchange="filterBankQuestions()">
                            <option value="">All Difficulties</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>

                <!-- Questions List -->
                <div id="bankQuestionsList" class="space-y-2 max-h-96 overflow-y-auto">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                        <p class="text-gray-600 mt-2">Loading questions...</p>
                    </div>
                </div>

                <!-- Selected Count -->
                <div id="selectedCount" class="mt-4 p-3 bg-blue-50 rounded-lg hidden">
                    <p class="text-blue-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="selectedCountText">0 questions selected</span>
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t mt-4">
                <button onclick="closeImportModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <button onclick="importSelected()" id="importBtn"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded" disabled>
                    <i class="fas fa-download mr-2"></i>Import Selected
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let selectedQuestions = new Set();
            let bankQuestions = [];

            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
                loadBankQuestions();
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                selectedQuestions.clear();
                updateSelectedCount();
            }

            function loadBankQuestions() {
                fetch('{{ route('admin.question-bank.get-for-import') }}')
                    .then(response => response.json())
                    .then(data => {
                        bankQuestions = data.questions;
                        renderBankQuestions(bankQuestions);
                    })
                    .catch(error => {
                        document.getElementById('bankQuestionsList').innerHTML =
                            '<div class="text-center py-8 text-red-600">' +
                            '<i class="fas fa-exclamation-circle text-4xl mb-2"></i>' +
                            '<p>Failed to load questions</p>' +
                            '</div>';
                    });
            }

            function filterBankQuestions() {
                const search = document.getElementById('bankSearch').value.toLowerCase();
                const category = document.getElementById('bankCategory').value;
                const type = document.getElementById('bankType').value;
                const difficulty = document.getElementById('bankDifficulty').value;

                const filtered = bankQuestions.filter(q => {
                    const matchSearch = !search || q.question_text.toLowerCase().includes(search);
                    const matchCategory = !category || q.category_id == category;
                    const matchType = !type || q.type === type;
                    const matchDifficulty = !difficulty || q.difficulty === difficulty;
                    return matchSearch && matchCategory && matchType && matchDifficulty;
                });

                renderBankQuestions(filtered);
            }

            function renderBankQuestions(questions) {
                const container = document.getElementById('bankQuestionsList');

                if (questions.length === 0) {
                    container.innerHTML =
                        '<div class="text-center py-8 text-gray-500">' +
                        '<i class="fas fa-inbox text-4xl mb-2"></i>' +
                        '<p>No questions found</p>' +
                        '</div>';
                    return;
                }

                container.innerHTML = questions.map(q => `
                <div class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition ${selectedQuestions.has(q.id) ? 'bg-purple-50 border-purple-500' : ''}"
                     onclick="toggleQuestion(${q.id})">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center mt-1">
                            <input type="checkbox" 
                                   ${selectedQuestions.has(q.id) ? 'checked' : ''}
                                   class="w-5 h-5 text-purple-600 rounded"
                                   onclick="event.stopPropagation()">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">${escapeHtml(q.question_text.substring(0, 100))}${q.question_text.length > 100 ? '...' : ''}</p>
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        ${getTypeBadge(q.type)}
                                        ${getDifficultyBadge(q.difficulty)}
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>${q.default_points} pts
                                        </span>
                                        ${q.category_name ? `<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">${escapeHtml(q.category_name)}</span>` : ''}
                                        ${q.times_used > 0 ? `<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800"><i class="fas fa-recycle mr-1"></i>Used ${q.times_used}x</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            function toggleQuestion(id) {
                if (selectedQuestions.has(id)) {
                    selectedQuestions.delete(id);
                } else {
                    selectedQuestions.add(id);
                }
                updateSelectedCount();
                renderBankQuestions(bankQuestions.filter(q => {
                    const search = document.getElementById('bankSearch').value.toLowerCase();
                    const category = document.getElementById('bankCategory').value;
                    const type = document.getElementById('bankType').value;
                    const difficulty = document.getElementById('bankDifficulty').value;
                    const matchSearch = !search || q.question_text.toLowerCase().includes(search);
                    const matchCategory = !category || q.category_id == category;
                    const matchType = !type || q.type === type;
                    const matchDifficulty = !difficulty || q.difficulty === difficulty;
                    return matchSearch && matchCategory && matchType && matchDifficulty;
                }));
            }

            function updateSelectedCount() {
                const count = selectedQuestions.size;
                const countDiv = document.getElementById('selectedCount');
                const countText = document.getElementById('selectedCountText');
                const importBtn = document.getElementById('importBtn');

                if (count > 0) {
                    countDiv.classList.remove('hidden');
                    countText.textContent = `${count} question${count > 1 ? 's' : ''} selected`;
                    importBtn.disabled = false;
                } else {
                    countDiv.classList.add('hidden');
                    importBtn.disabled = true;
                }
            }

            function importSelected() {
                if (selectedQuestions.size === 0) return;

                const importBtn = document.getElementById('importBtn');
                importBtn.disabled = true;
                importBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';

                fetch('{{ route('admin.exams.questions.import-from-bank', $exam) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_ids: Array.from(selectedQuestions)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: data.message || `${data.imported} questions imported successfully!`
                            });
                            closeImportModal();
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.message || 'Failed to import questions'
                            });
                            importBtn.disabled = false;
                            importBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Import Selected';
                        }
                    })
                    .catch(error => {
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred'
                        });
                        importBtn.disabled = false;
                        importBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Import Selected';
                    });
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function getTypeBadge(type) {
                const types = {
                    'mcq_single': '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">MCQ Single</span>',
                    'mcq_multiple': '<span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">MCQ Multiple</span>',
                    'matching': '<span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800">Matching</span>',
                    'essay': '<span class="px-2 py-1 text-xs rounded bg-pink-100 text-pink-800">Essay</span>'
                };
                return types[type] || '';
            }

            function getDifficultyBadge(difficulty) {
                const difficulties = {
                    'easy': '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Easy</span>',
                    'medium': '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Medium</span>',
                    'hard': '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Hard</span>'
                };
                return difficulties[difficulty] || '';
            }
        </script>
    @endpush
</x-app-layout>
