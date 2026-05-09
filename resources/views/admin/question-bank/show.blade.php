<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-database mr-2"></i>{{ __('Question Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.question-bank.edit', $questionBank) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.question-bank.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 text-xs font-semibold mb-1">{{ __('Times Used') }}</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $questionBank->times_used }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-percentage text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-green-600 text-xs font-semibold mb-1">{{ __('Avg Score') }}</div>
                            <div class="text-2xl font-bold text-green-900">
                                {{ $questionBank->average_score !== null ? number_format($questionBank->average_score, 1) . '%' : __('N/A') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-purple-600 text-xs font-semibold mb-1">{{ __('Success Rate') }}</div>
                            <div class="text-2xl font-bold text-purple-900">
                                {{ $questionBank->times_used > 0 ? number_format($questionBank->success_rate, 1) . '%' : __('N/A') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-yellow-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg mr-3">
                            <i class="fas fa-star text-yellow-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-yellow-600 text-xs font-semibold mb-1">{{ __('Default Points') }}</div>
                            <div class="text-2xl font-bold text-yellow-900">{{ $questionBank->default_points }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Content -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Question Content') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                {!! $questionBank->type_badge !!}
                                {!! $questionBank->difficulty_badge !!}
                                {!! $questionBank->verification_badge !!}
                                @if (!$questionBank->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-eye-slash mr-1"></i>{{ __('Inactive') }}
                                    </span>
                                @endif
                            </div>
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
                            <h4 class="font-bold text-gray-900 mb-3">{{ __('Answer Options:') }}</h4>
                            <div class="space-y-2">
                                @foreach ($questionBank->options as $option)
                                    @php
                                        $optionId = is_array($option) ? $option['id'] ?? '' : $option;
                                        $optionText = is_array($option) ? $option['text'] ?? $option : $option;
                                        $isCorrectSingle =
                                            $questionBank->type === 'mcq_single' &&
                                            $questionBank->correct_answer === $optionId;
                                        $isCorrectMultiple =
                                            $questionBank->type === 'mcq_multiple' &&
                                            in_array($optionId, $questionBank->correct_answer_multiple ?? []);
                                        $isCorrect = $isCorrectSingle || $isCorrectMultiple;
                                    @endphp
                                    <div
                                        class="flex items-center p-3 rounded-lg {{ $isCorrect ? 'bg-green-50 border-2 border-green-500' : 'bg-gray-50' }}">
                                        <span class="font-bold mr-3">{{ $optionId }}.</span>
                                        <span>{{ $optionText }}</span>
                                        @if ($isCorrect)
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
                            <h4 class="font-bold text-gray-900 mb-3">{{ __('Matching Pairs:') }}</h4>
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
                                <i class="fas fa-lightbulb mr-2"></i>{{ __('Explanation:') }}
                            </h4>
                            <p class="text-blue-800 whitespace-pre-line">{{ $questionBank->explanation }}</p>
                        </div>
                    @endif

                    <!-- Teacher Notes -->
                    @if ($questionBank->teacher_notes)
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                            <h4 class="font-bold text-yellow-900 mb-2">
                                <i class="fas fa-sticky-note mr-2"></i>{{ __('Teacher Notes (Private):') }}
                            </h4>
                            <p class="text-yellow-800 whitespace-pre-line">{{ $questionBank->teacher_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Metadata') }}
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">{{ __('Category') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                @if ($questionBank->category)
                                    {!! $questionBank->category->color_badge !!}
                                    {{ $questionBank->category->full_path }}
                                @else
                                    <span class="text-gray-400">{{ __('No category') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">{{ __('Created By') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $questionBank->creator->name }}</dd>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <dt class="text-xs font-semibold text-purple-700 mb-1">{{ __('Tags') }}</dt>
                            <dd>
                                @if ($questionBank->tags)
                                    @foreach ($questionBank->tags as $tag)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 mr-1">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-sm">{{ __('No tags') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">{{ __('Created At') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900">
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
                            {{ trans_choice('Used in :count Exam|Used in :count Exams', $questionBank->examQuestions->count(), ['count' => $questionBank->examQuestions->count()]) }}
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
                                            {{ __('Order: #:num', ['num' => $examQuestion->order]) }} |
                                            {{ __('Points: :pts', ['pts' => $examQuestion->points]) }}
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
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-bolt text-orange-600 mr-2"></i>{{ __('Quick Actions') }}
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('admin.question-bank.toggle-verification', $questionBank) }}"
                            method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-{{ $questionBank->is_verified ? 'gray' : 'green' }}-600 text-white font-semibold rounded-lg hover:bg-{{ $questionBank->is_verified ? 'gray' : 'green' }}-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-{{ $questionBank->is_verified ? 'times' : 'check' }}-circle"></i>
                                {{ $questionBank->is_verified ? __('Unverify') : __('Mark as Verified') }}
                            </button>
                        </form>

                        <form action="{{ route('admin.question-bank.duplicate', $questionBank) }}" method="POST"
                            class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-copy"></i>{{ __('Duplicate') }}
                            </button>
                        </form>

                        <form action="{{ route('admin.question-bank.destroy', $questionBank) }}" method="POST"
                            class="inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-trash"></i>{{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
