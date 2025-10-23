<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-database text-indigo-600 mr-2"></i>
                Question Bank
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.question-bank.statistics') }}"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </a>
                <a href="{{ route('admin.question-bank.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Add Question
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
                                    placeholder="Search questions..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Category -->
                            <div>
                                <select name="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Categories</option>
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
                                    <option value="">All Types</option>
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
                                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy
                                    </option>
                                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>
                                        Medium</option>
                                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.question-bank.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-redo mr-2"></i>Reset
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
                                                            <strong>{{ $question->default_points }}</strong> points
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-recycle text-blue-500 mr-1"></i>
                                                            Used <strong>{{ $question->times_used }}</strong> times
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
</x-app-layout>
