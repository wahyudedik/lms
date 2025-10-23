<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-database text-indigo-600 mr-2"></i>
                Question Details
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.question-bank.edit', $questionBank) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.question-bank.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-blue-600 text-sm font-medium mb-1">Times Used</div>
                    <div class="text-3xl font-bold text-blue-900">{{ $questionBank->times_used }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-green-600 text-sm font-medium mb-1">Avg Score</div>
                    <div class="text-3xl font-bold text-green-900">
                        {{ $questionBank->average_score !== null ? number_format($questionBank->average_score, 1) . '%' : 'N/A' }}
                    </div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-purple-600 text-sm font-medium mb-1">Success Rate</div>
                    <div class="text-3xl font-bold text-purple-900">
                        {{ $questionBank->times_used > 0 ? number_format($questionBank->success_rate, 1) . '%' : 'N/A' }}
                    </div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-yellow-600 text-sm font-medium mb-1">Default Points</div>
                    <div class="text-3xl font-bold text-yellow-900">{{ $questionBank->default_points }}</div>
                </div>
            </div>

            <!-- Question Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">Question Content</h3>
                        <div class="flex gap-2">
                            {!! $questionBank->type_badge !!}
                            {!! $questionBank->difficulty_badge !!}
                            {!! $questionBank->verification_badge !!}
                            @if (!$questionBank->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                    <i class="fas fa-eye-slash mr-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <p class="text-lg text-gray-900 whitespace-pre-line">{{ $questionBank->question_text }}</p>

                        @if ($questionBank->question_image)
                            <div class="mt-4">
                                <img src="{{ Storage::url($questionBank->question_image) }}" alt="Question Image"
                                    class="max-w-md rounded-lg shadow">
                            </div>
                        @endif
                    </div>

                    <!-- Answer Options (for MCQ) -->
                    @if (in_array($questionBank->type, ['mcq_single', 'mcq_multiple']))
                        <div class="mt-6">
                            <h4 class="font-bold text-gray-900 mb-3">Answer Options:</h4>
                            <div class="space-y-2">
                                @foreach ($questionBank->options as $key => $option)
                                    <div
                                        class="flex items-center p-3 rounded-lg {{ ($questionBank->type == 'mcq_single' && $questionBank->correct_answer == $key) ||
                                        ($questionBank->type == 'mcq_multiple' && in_array($key, $questionBank->correct_answer_multiple ?? []))
                                            ? 'bg-green-50 border-2 border-green-500'
                                            : 'bg-gray-50' }}">
                                        <span class="font-bold mr-3">{{ $key }}.</span>
                                        <span>{{ $option }}</span>
                                        @if (
                                            ($questionBank->type == 'mcq_single' && $questionBank->correct_answer == $key) ||
                                                ($questionBank->type == 'mcq_multiple' && in_array($key, $questionBank->correct_answer_multiple ?? [])))
                                            <i class="fas fa-check-circle text-green-600 ml-auto"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Matching Pairs -->
                    @if ($questionBank->type == 'matching' && $questionBank->pairs)
                        <div class="mt-6">
                            <h4 class="font-bold text-gray-900 mb-3">Matching Pairs:</h4>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($questionBank->pairs as $pair)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="font-medium">{{ $pair['left'] }}</div>
                                        <div class="text-sm text-gray-600">↔ {{ $pair['right'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Explanation -->
                    @if ($questionBank->explanation)
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-bold text-blue-900 mb-2">
                                <i class="fas fa-lightbulb mr-2"></i>Explanation:
                            </h4>
                            <p class="text-blue-800 whitespace-pre-line">{{ $questionBank->explanation }}</p>
                        </div>
                    @endif

                    <!-- Teacher Notes -->
                    @if ($questionBank->teacher_notes)
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                            <h4 class="font-bold text-yellow-900 mb-2">
                                <i class="fas fa-sticky-note mr-2"></i>Teacher Notes (Private):
                            </h4>
                            <p class="text-yellow-800 whitespace-pre-line">{{ $questionBank->teacher_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Metadata</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if ($questionBank->category)
                                    {!! $questionBank->category->color_badge !!}
                                    {{ $questionBank->category->full_path }}
                                @else
                                    <span class="text-gray-400">No category</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $questionBank->creator->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tags</dt>
                            <dd class="mt-1">
                                @if ($questionBank->tags)
                                    @foreach ($questionBank->tags as $tag)
                                        <span
                                            class="inline-block px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 mr-1">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-sm">No tags</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $questionBank->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Exams Using This Question -->
            @if ($questionBank->examQuestions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Used in {{ $questionBank->examQuestions->count() }} Exam(s)
                        </h3>
                        <div class="space-y-2">
                            @foreach ($questionBank->examQuestions as $examQuestion)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <a href="{{ route('admin.exams.show', $examQuestion->exam) }}"
                                            class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ $examQuestion->exam->title }}
                                        </a>
                                        <div class="text-sm text-gray-600">
                                            Order: #{{ $examQuestion->order }} | Points: {{ $examQuestion->points }}
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.exams.show', $examQuestion->exam) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('admin.question-bank.toggle-verification', $questionBank) }}"
                            method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-{{ $questionBank->is_verified ? 'gray' : 'green' }}-500 hover:bg-{{ $questionBank->is_verified ? 'gray' : 'green' }}-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-{{ $questionBank->is_verified ? 'times' : 'check' }}-circle mr-2"></i>
                                {{ $questionBank->is_verified ? 'Unverify' : 'Mark as Verified' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.question-bank.duplicate', $questionBank) }}" method="POST"
                            class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-copy mr-2"></i>Duplicate
                            </button>
                        </form>

                        <form action="{{ route('admin.question-bank.destroy', $questionBank) }}" method="POST"
                            class="inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
