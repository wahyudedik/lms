<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-shield-alt text-red-600 mr-2"></i>{{ __('Authorization Log Details') }}
            </h2>
            <a href="{{ route('admin.authorization-logs.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to Logs') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Log #:id', ['id' => $authorizationLog->id]) }}</h3>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Basic Information') }}</h4>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Log ID') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $authorizationLog->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Date & Time') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $authorizationLog->created_at->format('Y-m-d H:i:s') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Result') }}</dt>
                                <dd class="mt-1">
                                    @if($authorizationLog->result === 'denied')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>{{ __('Denied') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>{{ __('Allowed') }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Action') }}</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $authorizationLog->action }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- User Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('User Information') }}</h4>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('User') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($authorizationLog->user)
                                        <div>
                                            <div class="font-medium">{{ $authorizationLog->user->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $authorizationLog->user->email }}</div>
                                            <div class="text-gray-500 text-xs">Role: {{ ucfirst($authorizationLog->user->role) }}</div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">{{ __('Guest / Unauthenticated') }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('IP Address') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $authorizationLog->ip_address ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('User Agent') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ $authorizationLog->user_agent ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Resource Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Resource Information') }}</h4>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Resource Type') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $authorizationLog->resource_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Resource ID') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $authorizationLog->resource_id ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Request Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Request Information') }}</h4>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Route') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $authorizationLog->route ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('HTTP Method') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $authorizationLog->method ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Ability') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $authorizationLog->ability }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Reason / Details -->
                    @if($authorizationLog->reason)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Reason') }}</h4>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm text-red-800">{{ $authorizationLog->reason }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Metadata -->
                    @if($authorizationLog->metadata)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Additional Metadata') }}</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <pre class="text-xs text-gray-700 overflow-x-auto">{{ json_encode($authorizationLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

