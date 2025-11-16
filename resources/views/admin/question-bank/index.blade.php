<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-database text-indigo-600 mr-2"></i>
                {{ __('Question Bank') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.question-bank.statistics') }}"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chart-bar mr-2"></i>{{ __('Statistics') }}
                </a>
                <div class="relative inline-block">
                    <button onclick="toggleExportMenu()"
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <i class="fas fa-file-export mr-2"></i>{{ __('Export') }}
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    <div id="exportMenu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                        <a href="{{ route('admin.question-bank.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel text-green-600 mr-2"></i>{{ __('Excel') }}
                        </a>
                        <a href="{{ route('admin.question-bank.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>{{ __('PDF') }}
                        </a>
                        <a href="{{ route('admin.question-bank.export', array_merge(request()->all(), ['format' => 'json'])) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-code text-blue-600 mr-2"></i>{{ __('JSON') }}
                        </a>
                    </div>
                </div>
                <button onclick="openImportModal()"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-import mr-2"></i>{{ __('Import') }}
                </button>
                <a href="{{ route('admin.question-bank.import-history') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-history mr-2"></i>{{ __('History') }}
                </a>
                <a href="{{ route('admin.question-bank.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>{{ __('Add Question') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.question-bank.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Search questions...') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Category -->
                            <div>
                                <select name="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('All Categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->full_path }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Type -->
                            <div>
                                <select name="type"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('All Types') }}</option>
                                    <option value="mcq_single" {{ request('type') == 'mcq_single' ? 'selected' : '' }}>
                                        MCQ Single</option>
                                    <option value="mcq_multiple"
                                        {{ request('type') == 'mcq_multiple' ? 'selected' : '' }}>MCQ Multiple</option>
                                    <option value="matching" {{ request('type') == 'matching' ? 'selected' : '' }}>
                                        Matching</option>
                                    <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Essay
                                    </option>
                                </select>
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <select name="difficulty"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Difficulties</option>
                                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>{{ __('Easy') }}</option>
                                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>{{ __('Hard') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search mr-2"></i>{{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.question-bank.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-redo mr-2"></i>{{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($questions->count() > 0)
                        <div class="space-y-4">
                            @foreach ($questions as $question)
                                <div
                                    class="border rounded-lg p-4 hover:shadow-lg transition-all duration-200 {{ !$question->is_active ? 'bg-gray-50 opacity-75' : '' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <!-- Question Text -->
                                            <div class="flex items-start gap-3">
                                                @if ($question->question_image)
                                                    <img src="{{ Storage::url($question->question_image) }}"
                                                        alt="Question" class="w-16 h-16 object-cover rounded">
                                                @endif
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                                        {{ Str::limit($question->question_text, 100) }}
                                                    </h3>

                                                    <!-- Badges -->
                                                    <div class="flex flex-wrap gap-2 mb-3">
                                                        {!! $question->type_badge !!}
                                                        {!! $question->difficulty_badge !!}
                                                        {!! $question->verification_badge !!}

                                                        @if ($question->category)
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                                                                {!! $question->category->color_badge !!}
                                                                {{ $question->category->name }}
                                                            </span>
                                                        @endif

                                                        @if (!$question->is_active)
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                                <i class="fas fa-eye-slash mr-1"></i>Inactive
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Stats -->
                                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                                        <span>
                                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                                            <strong>{{ $question->default_points }}</strong> {{ __('points') }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-recycle text-blue-500 mr-1"></i>
                                                            {{ __('Used :count times', ['count' => $question->times_used]) }}
                                                        </span>
                                                        @if ($question->average_score !== null)
                                                            <span>
                                                                <i class="fas fa-chart-line text-green-500 mr-1"></i>
                                                                Avg Score:
                                                                <strong>{{ number_format($question->average_score, 1) }}%</strong>
                                                            </span>
                                                        @endif
                                                        @if ($question->tags)
                                                            <span>
                                                                <i class="fas fa-tags text-purple-500 mr-1"></i>
                                                                {{ implode(', ', $question->tags) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex flex-col gap-2 ml-4">
                                            <a href="{{ route('admin.question-bank.show', $question) }}"
                                                class="text-blue-600 hover:text-blue-800" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.question-bank.edit', $question) }}"
                                                class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.question-bank.toggle-verification', $question) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800"
                                                    title="{{ $question->is_verified ? 'Unverify' : 'Verify' }}">
                                                    <i
                                                        class="fas fa-{{ $question->is_verified ? 'check-circle' : 'clock' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.question-bank.duplicate', $question) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-purple-600 hover:text-purple-800"
                                                    title="Duplicate">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.question-bank.destroy', $question) }}"
                                                method="POST" class="inline" onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $questions->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <i class="fas fa-database text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Questions in Bank</h3>
                            <p class="text-gray-500 mb-6">
                                @if (request()->hasAny(['search', 'category_id', 'type', 'difficulty']))
                                    No questions match your filters. Try adjusting your search.
                                @else
                                    Start building your question bank by adding your first question!
                                @endif
                            </p>
                            <a href="{{ route('admin.question-bank.create') }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Add Your First Question
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Import Questions from Excel/CSV
                </h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="mb-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Import Guidelines:</strong>
                            </p>
                            <ul class="list-disc list-inside text-sm text-blue-700 mt-2">
                                <li>Download the template below to see the required format</li>
                                <li>Supported file types: .xlsx, .xls, .csv (Max 10MB)</li>
                                <li>Required fields: type, difficulty, question_text</li>
                                <li>Options & answers must be valid JSON format</li>
                                <li>Categories will be auto-created if they don't exist</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="{{ route('admin.question-bank.download-template') }}"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-download mr-2"></i>
                        {{ __('Download Import Template') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.question-bank.import') }}" method="POST" enctype="multipart/form-data"
                id="importForm">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        {{ __('Select File to Import') }}
                    </label>
                    <div class="relative">
                        <input type="file" name="file" id="importFile" accept=".xlsx,.xls,.csv" required
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-green-50 file:text-green-700
                                hover:file:bg-green-100
                                cursor-pointer border border-gray-300 rounded-lg">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('Accepted formats: Excel (.xlsx, .xls) or CSV (.csv)') }}
                    </p>
                </div>

                <div id="importPreview" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                        <span id="fileName"></span>
                        <span id="fileSize" class="text-gray-500"></span>
                    </p>
                </div>

                <div id="validationResult" class="hidden mb-4"></div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="use_queue" value="1"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            {{ __('Process in background (recommended for large files > 100 rows)') }}
                        </span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeImportModal()"
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-2"></i>{{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-upload mr-2"></i>{{ __('Import Questions') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                document.getElementById('importForm').reset();
                document.getElementById('importPreview').classList.add('hidden');
                document.getElementById('validationResult').classList.add('hidden');
            }

            function toggleExportMenu() {
                const menu = document.getElementById('exportMenu');
                menu.classList.toggle('hidden');
            }

            // Close export menu when clicking outside
            document.addEventListener('click', function(e) {
                const menu = document.getElementById('exportMenu');
                const button = menu.previousElementSibling;
                if (!menu.contains(e.target) && !button.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });

            // Show file preview and validate
            document.getElementById('importFile').addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (file) {
                    const preview = document.getElementById('importPreview');
                    const fileName = document.getElementById('fileName');
                    const fileSize = document.getElementById('fileSize');
                    const validationResult = document.getElementById('validationResult');

                    fileName.textContent = file.name;
                    fileSize.textContent = `(${(file.size / 1024).toFixed(2)} KB)`;
                    preview.classList.remove('hidden');

                    // Show validation loading
                    validationResult.innerHTML = `
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex items-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                                <p class="text-sm text-blue-700">Validating file...</p>
                            </div>
                        </div>
                    `;
                    validationResult.classList.remove('hidden');

                    // Validate file
                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        const response = await fetch('{{ route('admin.question-bank.validate-import') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            const validation = result.validation;
                            const successRate = (validation.valid_rows / validation.total_rows * 100).toFixed(1);

                            validationResult.innerHTML = `
                                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                                    <h4 class="text-sm font-semibold text-green-800 mb-2">
                                        <i class="fas fa-check-circle mr-2"></i>Validation Successful
                                    </h4>
                                    <div class="grid grid-cols-3 gap-4 text-sm text-green-700">
                                        <div>
                                            <span class="font-semibold">Total Rows:</span> ${validation.total_rows}
                                        </div>
                                        <div>
                                            <span class="font-semibold">Valid:</span> ${validation.valid_rows}
                                        </div>
                                        <div>
                                            <span class="font-semibold">Invalid:</span> ${validation.invalid_rows}
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="bg-green-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: ${successRate}%"></div>
                                        </div>
                                        <p class="text-xs text-green-600 mt-1">${successRate}% valid</p>
                                    </div>
                                    ${validation.errors.length > 0 ? `
                                                <div class="mt-3">
                                                    <p class="text-xs font-semibold text-red-700 mb-1">Errors:</p>
                                                    <ul class="text-xs text-red-600 list-disc list-inside max-h-32 overflow-y-auto">
                                                        ${validation.errors.slice(0, 5).map(err => `<li>${err}</li>`).join('')}
                                                        ${validation.errors.length > 5 ? `<li>... and ${validation.errors.length - 5} more</li>` : ''}
                                                    </ul>
                                                </div>
                                            ` : ''}
                                </div>
                            `;
                        } else {
                            validationResult.innerHTML = `
                                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                    <p class="text-sm text-red-700">
                                        <i class="fas fa-exclamation-circle mr-2"></i>${result.message}
                                    </p>
                                </div>
                            `;
                        }
                    } catch (error) {
                        validationResult.innerHTML = `
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                                <p class="text-sm text-yellow-700">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Validation skipped. You can still proceed with import.
                                </p>
                            </div>
                        `;
                    }
                }
            });

            // Close modal when clicking outside
            document.getElementById('importModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImportModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
