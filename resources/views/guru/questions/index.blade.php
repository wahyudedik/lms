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
                <a href="{{ route('guru.exams.questions.create', $exam) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>{{ __('Add Question') }}
                </a>
                <a href="{{ route('guru.exams.show', $exam) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
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
                                                    <a href="{{ route('guru.exams.questions.edit', [$exam, $question]) }}"
                                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('guru.exams.questions.duplicate', [$exam, $question]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900"
                                                            title="Duplikat">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route('guru.exams.questions.destroy', [$exam, $question]) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this question?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            title="{{ __('Delete') }}">
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
                            <p class="text-gray-500 text-lg mb-4">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route('guru.exams.questions.create', $exam) }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>{{ __('Add First Question') }}
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
                            fetch('{{ route('guru.exams.questions.reorder', $exam) }}', {
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
</x-app-layout>
