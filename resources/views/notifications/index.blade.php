<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-bell text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Notifikasi</h2>
                    <p class="text-sm text-gray-600">Kelola semua notifikasi Anda</p>
                </div>
            </div>
            @if ($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                        <i class="fas fa-check-double"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($notifications->count() > 0)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 divide-y divide-gray-200">
                    @foreach ($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        <div class="p-6 hover:bg-gray-50 transition-colors {{ $isUnread ? 'bg-blue-50' : '' }}">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-{{ $data['color'] ?? 'blue' }}-100 flex items-center justify-center">
                                        <i
                                            class="{{ $data['icon'] ?? 'fas fa-bell' }} text-{{ $data['color'] ?? 'blue' }}-600 text-xl"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <p
                                                class="text-sm text-gray-900 {{ $isUnread ? 'font-semibold' : 'font-medium' }}">
                                                {{ $data['message'] ?? 'Notifikasi baru' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                        @if ($isUnread)
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded-full">
                                                <i class="fas fa-circle text-xs"></i>
                                                Baru
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-3 flex items-center gap-3 flex-wrap">
                                        @if (isset($data['action_url']))
                                            <a href="{{ $data['action_url'] }}"
                                                onclick="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                                <i class="fas fa-arrow-right"></i>
                                                <span>{{ $data['action_text'] ?? 'Lihat' }}</span>
                                            </a>
                                        @endif
                                        @if ($isUnread)
                                            <button onclick="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-800 font-semibold">
                                                <i class="fas fa-check"></i>
                                                <span>Tandai Dibaca</span>
                                            </button>
                                        @endif
                                        <form action="{{ route('notifications.destroy', $notification->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')"
                                                class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 font-semibold">
                                                <i class="fas fa-trash"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-bell-slash text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-sm text-gray-500">Notifikasi akan muncul di sini ketika ada aktivitas baru</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
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
    @endpush
</x-app-layout>
