<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-list-alt mr-2"></i>Kelola Soal: {{ $exam->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Total: {{ $questions->count() }} soal | {{ $exam->total_points }} poin
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="openImportModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-database"></i>
                    <span class="hidden sm:inline">{{ __('Import dari Bank') }}</span>
                </button>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.questions.create', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">{{ __('Add Question') }}</span>
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.show', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">{{ __('Back') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($questions->count() > 0)
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <div id="questions-list" class="space-y-4">
                            @foreach ($questions as $question)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors"
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
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-800">
                                                        #{{ $question->order }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                        <i class="{{ $question->type_icon }} mr-1"></i>
                                                        {{ $question->type_display }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-star mr-1"></i>{{ $question->points }} poin
                                                    </span>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex gap-3">
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.questions.edit', [$exam, $question]) }}"
                                                        class="text-green-600 hover:text-green-800 font-semibold"
                                                        title="Edit">
                                                        Edit
                                                    </a>

                                                    <form
                                                        action="{{ route(auth()->user()->getRolePrefix() . '.exams.questions.duplicate', [$exam, $question]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-blue-600 hover:text-blue-800 font-semibold"
                                                            title="Duplikat">
                                                            Duplikat
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route(auth()->user()->getRolePrefix() . '.exams.questions.destroy', [$exam, $question]) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this question?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 font-semibold"
                                                            title="{{ __('Delete') }}">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Question Text -->
                                            <div class="mb-3">
                                                <p class="text-gray-900 font-semibold">{{ $question->question_text }}
                                                </p>
                                                @if ($question->question_image)
                                                    <img src="{{ Storage::url($question->question_image) }}"
                                                        alt="Question Image"
                                                        class="mt-2 max-w-sm rounded-lg border border-gray-200">
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
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clipboard-question text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-semibold mb-4">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.questions.create', $exam) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                {{ __('Add First Question') }}
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
                            fetch('{{ route(auth()->user()->getRolePrefix() . '.exams.questions.reorder', $exam) }}', {
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
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-database text-purple-600 mr-2"></i>Import Soal dari Bank
                </h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="mb-4">
                    <input type="text" id="bankSearch" placeholder="Cari soal..." onkeyup="filterBankQuestions()"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div id="bankQuestionsList" class="space-y-2 max-h-96 overflow-y-auto">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                        <p class="text-gray-600 mt-2">Memuat soal...</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Hanya soal dengan status
                    aktif yang ditampilkan. Aktifkan soal di Bank Soal jika tidak muncul di sini.</p>
                <div id="selectedCount" class="mt-4 p-3 bg-blue-50 rounded-lg hidden">
                    <p class="text-blue-800"><i class="fas fa-check-circle mr-2"></i><span id="selectedCountText">0
                            soal dipilih</span></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t mt-4">
                <button onclick="closeImportModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-bold rounded">Batal</button>
                <button onclick="importSelected()" id="importBtn"
                    class="px-4 py-2 bg-purple-500 hover:bg-purple-700 text-white font-bold rounded" disabled>
                    <i class="fas fa-download mr-2"></i>Import Terpilih
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
                fetch('{{ route(auth()->user()->getRolePrefix() . '.question-bank.get-for-import') }}')
                    .then(r => r.json())
                    .then(data => {
                        bankQuestions = data.questions;
                        renderBankQuestions(bankQuestions);
                    })
                    .catch(() => {
                        document.getElementById('bankQuestionsList').innerHTML =
                            '<div class="text-center py-8 text-red-600"><p>Gagal memuat soal</p></div>';
                    });
            }

            function filterBankQuestions() {
                const search = document.getElementById('bankSearch').value.toLowerCase();
                renderBankQuestions(bankQuestions.filter(q => q.question_text.toLowerCase().includes(search)));
            }

            function renderBankQuestions(questions) {
                const container = document.getElementById('bankQuestionsList');
                if (questions.length === 0) {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>Tidak ada soal ditemukan</p></div>';
                    return;
                }
                container.innerHTML = questions.map(q => `
                    <div class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer ${selectedQuestions.has(q.id) ? 'bg-purple-50 border-purple-500' : ''}" onclick="toggleQuestion(${q.id})">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" ${selectedQuestions.has(q.id) ? 'checked' : ''} class="mt-1 w-4 h-4 text-purple-600 rounded">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">${q.question_text.substring(0, 80)}${q.question_text.length > 80 ? '...' : ''}</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800">${q.type.replace('_', ' ').toUpperCase()}</span>
                                    <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-800">${q.difficulty}</span>
                                    <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-800">${q.default_points} pts</span>
                                    ${q.is_own ? '<span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Milik Saya</span>' : `<span class="px-2 py-0.5 text-xs rounded bg-indigo-100 text-indigo-700"><i class="fas fa-share-alt mr-1"></i>${q.creator_name}</span>`}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            function toggleQuestion(id) {
                selectedQuestions.has(id) ? selectedQuestions.delete(id) : selectedQuestions.add(id);
                updateSelectedCount();
                filterBankQuestions();
            }

            function updateSelectedCount() {
                const count = selectedQuestions.size;
                const div = document.getElementById('selectedCount');
                const text = document.getElementById('selectedCountText');
                const btn = document.getElementById('importBtn');
                if (count > 0) {
                    div.classList.remove('hidden');
                    text.textContent = count + ' soal dipilih';
                    btn.disabled = false;
                } else {
                    div.classList.add('hidden');
                    btn.disabled = true;
                }
            }

            function importSelected() {
                if (selectedQuestions.size === 0) return;
                const btn = document.getElementById('importBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengimpor...';

                fetch('{{ route(auth()->user()->getRolePrefix() . '.exams.questions.import-from-bank', $exam) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_ids: Array.from(selectedQuestions)
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });
                            closeImportModal();
                            setTimeout(() => location.reload(), 1000);
                        }
                    })
                    .catch(() => {
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal mengimport soal'
                        });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-download mr-2"></i>Import Terpilih';
                    });
            }
        </script>
    @endpush
</x-app-layout>
