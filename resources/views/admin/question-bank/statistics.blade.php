<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                Question Bank Statistics
            </h2>
            <a href="{{ route('admin.question-bank.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bank
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Questions</p>
                            <p class="text-4xl font-bold mt-2">{{ $stats['total'] }}</p>
                        </div>
                        <i class="fas fa-database text-6xl opacity-25"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Active Questions</p>
                            <p class="text-4xl font-bold mt-2">{{ $stats['active'] }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                {{ $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <i class="fas fa-check-circle text-6xl opacity-25"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Verified Questions</p>
                            <p class="text-4xl font-bold mt-2">{{ $stats['verified'] }}</p>
                            <p class="text-purple-100 text-xs mt-1">
                                {{ $stats['total'] > 0 ? number_format(($stats['verified'] / $stats['total']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <i class="fas fa-badge-check text-6xl opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Questions by Type -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Questions by Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border rounded-lg p-4">
                            <div class="text-blue-600 text-sm font-medium mb-1">MCQ Single</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['by_type']['mcq_single'] ?? 0 }}
                            </div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-purple-600 text-sm font-medium mb-1">MCQ Multiple</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['by_type']['mcq_multiple'] ?? 0 }}
                            </div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-indigo-600 text-sm font-medium mb-1">Matching</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['by_type']['matching'] ?? 0 }}</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-pink-600 text-sm font-medium mb-1">Essay</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['by_type']['essay'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions by Difficulty -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Questions by Difficulty</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-green-600 text-sm font-medium mb-1">Easy</div>
                                    <div class="text-3xl font-bold text-green-900">
                                        {{ $stats['by_difficulty']['easy'] ?? 0 }}</div>
                                </div>
                                <i class="fas fa-smile text-4xl text-green-400"></i>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border-2 border-yellow-500 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-yellow-600 text-sm font-medium mb-1">Medium</div>
                                    <div class="text-3xl font-bold text-yellow-900">
                                        {{ $stats['by_difficulty']['medium'] ?? 0 }}</div>
                                </div>
                                <i class="fas fa-meh text-4xl text-yellow-400"></i>
                            </div>
                        </div>
                        <div class="bg-red-50 border-2 border-red-500 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-red-600 text-sm font-medium mb-1">Hard</div>
                                    <div class="text-3xl font-bold text-red-900">
                                        {{ $stats['by_difficulty']['hard'] ?? 0 }}</div>
                                </div>
                                <i class="fas fa-frown text-4xl text-red-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions by Category -->
            @if ($stats['by_category']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Questions by Category</h3>
                        <div class="space-y-3">
                            @foreach ($stats['by_category'] as $category)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        {!! $category->color_badge !!}
                                        <span class="ml-2 font-medium">{{ $category->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="text-2xl font-bold text-gray-900">{{ $category->questions_count }}</span>
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full"
                                                style="width: {{ $stats['total'] > 0 ? ($category->questions_count / $stats['total']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Most Used Questions -->
            @if ($stats['most_used']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-fire text-orange-500 mr-2"></i>
                            Most Used Questions
                        </h3>
                        <div class="space-y-2">
                            @foreach ($stats['most_used'] as $question)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                    <div class="flex-1">
                                        <a href="{{ route('admin.question-bank.show', $question) }}"
                                            class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ Str::limit($question->question_text, 80) }}
                                        </a>
                                        <div class="text-sm text-gray-600 mt-1">
                                            {!! $question->type_badge !!}
                                            {!! $question->difficulty_badge !!}
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="text-2xl font-bold text-orange-600">{{ $question->times_used }}
                                        </div>
                                        <div class="text-xs text-gray-500">times</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Performance -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Best Performing -->
                @if ($stats['best_performing']->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">
                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                Best Performing (5+ uses)
                            </h3>
                            <div class="space-y-2">
                                @foreach ($stats['best_performing'] as $question)
                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                        <div class="flex-1">
                                            <a href="{{ route('admin.question-bank.show', $question) }}"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ Str::limit($question->question_text, 60) }}
                                            </a>
                                        </div>
                                        <div class="text-right ml-2">
                                            <div class="text-lg font-bold text-green-600">
                                                {{ number_format($question->average_score, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Worst Performing -->
                @if ($stats['worst_performing']->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                Needs Review (5+ uses)
                            </h3>
                            <div class="space-y-2">
                                @foreach ($stats['worst_performing'] as $question)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div class="flex-1">
                                            <a href="{{ route('admin.question-bank.show', $question) }}"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ Str::limit($question->question_text, 60) }}
                                            </a>
                                        </div>
                                        <div class="text-right ml-2">
                                            <div class="text-lg font-bold text-red-600">
                                                {{ number_format($question->average_score, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($stats['total'] == 0)
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Statistics Yet</h3>
                        <p class="text-gray-500 mb-6">Add questions to the bank to see statistics here.</p>
                        <a href="{{ route('admin.question-bank.create') }}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Your First Question
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
