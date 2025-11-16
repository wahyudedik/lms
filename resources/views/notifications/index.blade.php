@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-bell text-blue-600 mr-2"></i>{{ __('Notifications') }}
            </h1>
            @if ($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-check-double mr-2"></i>{{ __('Mark All as Read') }}
                    </button>
                </form>
            @endif
        </div>

        @if ($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow-md divide-y">
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <div class="p-4 hover:bg-gray-50 transition {{ $isUnread ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 rounded-full bg-{{ $data['color'] ?? 'blue' }}-100 flex items-center justify-center">
                                    <i
                                        class="{{ $data['icon'] ?? 'fas fa-bell' }} text-{{ $data['color'] ?? 'blue' }}-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 {{ $isUnread ? 'font-semibold' : '' }}">
                                            {{ $data['message'] ?? __('New notification') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if ($isUnread)
                                        <span class="ml-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-full">{{ __('New') }}</span>
                                    @endif
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    @if (isset($data['action_url']))
                                        <a href="{{ $data['action_url'] }}"
                                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                            onclick="markAsRead('{{ $notification->id }}')">
                                            <i class="fas fa-arrow-right mr-1"></i>{{ $data['action_text'] ?? __('View') }}
                                        </a>
                                    @endif
                                    @if ($isUnread)
                                        <button onclick="markAsRead('{{ $notification->id }}')"
                                            class="text-sm text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-check mr-1"></i>{{ __('Mark as Read') }}
                                        </button>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this notification?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No notifications') }}</h3>
                <p class="text-gray-500">{{ __('Notifications will appear here when there is new activity') }}</p>
            </div>
        @endif
    </div>

    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                location.reload();
            });
        }
    </script>
@endsection
