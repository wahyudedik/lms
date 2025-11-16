<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                {{ __('Import Details #:id', ['id' => $importHistory->id]) }}
            </h2>
            <a href="{{ route('admin.question-bank.import-history') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to History') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Import Overview') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Status') }}</p>
                            <div class="mt-1">{!! $importHistory->status_badge !!}</div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Filename') }}</p>
                            <p class="text-sm font-semibold">{{ $importHistory->filename }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('File Size') }}</p>
                            <p class="text-sm font-semibold">{{ $importHistory->formatted_file_size }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Imported By') }}</p>
                            <p class="text-sm font-semibold">{{ $importHistory->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Started At') }}</p>
                            <p class="text-sm font-semibold">
                                {{ $importHistory->started_at ? $importHistory->started_at->format('Y-m-d H:i:s') : __('Not started') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Completed At') }}</p>
                            <p class="text-sm font-semibold">
                                {{ $importHistory->completed_at ? $importHistory->completed_at->format('Y-m-d H:i:s') : __('Not completed') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Processing Time') }}</p>
                            <p class="text-sm font-semibold">
                                {{ $importHistory->processing_time ? $importHistory->processing_time . ' ' . __('seconds') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Created At') }}</p>
                            <p class="text-sm font-semibold">{{ $importHistory->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            @if ($importHistory->status == 'completed')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Import Statistics') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-blue-600">{{ __('Total Rows') }}</p>
                                <p class="text-3xl font-bold text-blue-900">{{ $importHistory->total_rows }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-green-600">{{ __('Imported') }}</p>
                                <p class="text-3xl font-bold text-green-900">{{ $importHistory->imported_count }}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-yellow-600">{{ __('Skipped') }}</p>
                                <p class="text-3xl font-bold text-yellow-900">{{ $importHistory->skipped_count }}</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm text-red-600">{{ __('Errors') }}</p>
                                <p class="text-3xl font-bold text-red-900">{{ $importHistory->error_count }}</p>
                            </div>
                        </div>

                        <!-- Success Rate -->
                        <div class="mt-6">
                            <p class="text-sm text-gray-600 mb-2">{{ __('Success Rate') }}</p>
                            <div class="bg-gray-200 rounded-full h-4">
                                <div class="bg-green-600 h-4 rounded-full flex items-center justify-center text-white text-xs font-semibold"
                                    style="width: {{ $importHistory->success_rate }}%">
                                    {{ $importHistory->success_rate }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Errors -->
            @if ($importHistory->errors && count($importHistory->errors) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-red-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ __('Errors (:count)', ['count' => count($importHistory->errors)]) }}
                        </h3>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <ul class="space-y-2">
                                @foreach ($importHistory->errors as $error)
                                    <li class="text-sm text-red-700 flex items-start">
                                        <i class="fas fa-times-circle mr-2 mt-0.5"></i>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <form action="{{ route('admin.question-bank.import-history.delete', $importHistory) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        onclick="return confirmDelete('{{ __('Delete this import record?') }}')">
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete Record') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
