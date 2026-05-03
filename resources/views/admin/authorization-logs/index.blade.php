<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-shield-alt mr-2"></i>{{ __('Authorization Logs') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.authorization-logs.export', request()->all()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-download"></i>
                    {{ __('Export CSV') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Total Logs') }}</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-list text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Denied') }}</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['denied']) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-lg p-3">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Allowed') }}</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['allowed']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Denied %') }}</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($stats['denied_percentage'], 1) }}%</p>
                        </div>
                        <div class="bg-orange-100 rounded-lg p-3">
                            <i class="fas fa-percentage text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md mb-6 p-6">
                <form method="GET" action="{{ route('admin.authorization-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-filter text-gray-400 mr-1"></i>{{ __('Result') }}
                        </label>
                        <select name="result" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            <option value="">{{ __('All') }}</option>
                            <option value="denied" {{ request('result') == 'denied' ? 'selected' : '' }}>{{ __('Denied') }}</option>
                            <option value="allowed" {{ request('result') == 'allowed' ? 'selected' : '' }}>{{ __('Allowed') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-cube text-gray-400 mr-1"></i>{{ __('Resource Type') }}
                        </label>
                        <input type="text" name="resource_type" value="{{ request('resource_type') }}"
                            placeholder="{{ __('e.g., App\\Models\\Exam') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-1"></i>{{ __('User') }}
                        </label>
                        <select name="user_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            <option value="">{{ __('All Users') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Date From') }}
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Date To') }}
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                    </div>

                    <div class="md:col-span-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-search text-gray-400 mr-1"></i>{{ __('Search') }}
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search by action, ability, route, reason...') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                    </div>

                    <div class="md:col-span-5 flex gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-filter"></i>
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.authorization-logs.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('User') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Resource') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Action') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Result') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Route') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('IP Address') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->user)
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                                <div class="text-gray-500 text-xs">{{ $log->user->email }}</div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">{{ __('Guest') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ class_basename($log->resource_type) }}</div>
                                            @if($log->resource_id)
                                                <div class="text-gray-500 text-xs">ID: {{ $log->resource_id }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->result === 'denied')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>{{ __('Denied') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>{{ __('Allowed') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="{{ $log->route }}">
                                            {{ $log->route ?? __('N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->ip_address ?? __('N/A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.authorization-logs.show', $log) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        {{ __('No authorization logs found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

