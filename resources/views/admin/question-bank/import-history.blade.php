<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-history text-gray-600 mr-2"></i>
                {{ __('Import History') }}
            </h2>
            <a href="{{ route('admin.question-bank.index') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Question Bank') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filter -->
                    <form method="GET" class="mb-6">
                        <div class="flex gap-4">
                            <select name="status"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            </select>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-filter mr-2"></i>{{ __('Filter') }}
                            </button>
                            @if (request('status'))
                                <a href="{{ route('admin.question-bank.import-history') }}"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                    <i class="fas fa-times mr-2"></i>{{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($imports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ID') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Filename') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Imported By') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Size') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Results') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($imports as $import)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                #{{ $import->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $import->filename }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $import->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $import->formatted_file_size }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $import->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($import->status == 'completed')
                                                    <div class="text-green-600">
                                                        <i class="fas fa-check mr-1"></i>{{ $import->imported_count }}
                                                        {{ __('imported') }}
                                                    </div>
                                                    @if ($import->skipped_count > 0)
                                                        <div class="text-yellow-600">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $import->skipped_count }}
                                                            {{ __('skipped') }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $import->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.question-bank.import-history.show', $import) }}"
                                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form
                                                    action="{{ route('admin.question-bank.import-history.delete', $import) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirmDelete('{{ __('Delete this import record?') }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $imports->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('No Import History') }}</h3>
                            <p class="text-gray-500 mb-6">
                                {{ __('No imports have been performed yet.') }}
                            </p>
                            <a href="{{ route('admin.question-bank.index') }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-file-import mr-2"></i>{{ __('Import Your First File') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
