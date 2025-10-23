<div x-data="notificationBell()" x-init="init()" class="relative">
    <!-- Bell Icon -->
    <button @click="toggleDropdown" class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none">
        <i class="fas fa-bell text-xl"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">

        <!-- Header -->
        <div class="p-4 border-b flex items-center justify-between bg-gray-50">
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
            <button @click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800">
                Tandai Semua Dibaca
            </button>
        </div>

        <!-- Notifications List -->
        <div class="divide-y max-h-80 overflow-y-auto">
            <template x-for="notif in notifications" :key="notif.id">
                <div class="p-4 hover:bg-gray-50 cursor-pointer" @click="handleNotificationClick(notif)"
                    :class="notif.read_at ? '' : 'bg-blue-50'">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                :class="'bg-' + (notif.data.color || 'blue') + '-100'">
                                <i :class="notif.data.icon || 'fas fa-bell'" class="text-sm"
                                    :class="'text-' + (notif.data.color || 'blue') + '-600'"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-gray-800" x-text="notif.data.message"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notif.created_at)"></p>
                        </div>
                        <div x-show="!notif.read_at" class="flex-shrink-0 ml-2">
                            <span class="w-2 h-2 bg-blue-600 rounded-full inline-block"></span>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="notifications.length === 0" class="p-8 text-center text-gray-500">
                <i class="fas fa-bell-slash text-4xl mb-2"></i>
                <p class="text-sm">Tidak ada notifikasi</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3 bg-gray-50 border-t text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

<script>
    function notificationBell() {
        return {
            dropdownOpen: false,
            notifications: [],
            unreadCount: 0,

            init() {
                this.fetchNotifications();
                // Refresh every 30 seconds
                setInterval(() => this.fetchNotifications(), 30000);
            },

            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
                if (this.dropdownOpen) {
                    this.fetchNotifications();
                }
            },

            async fetchNotifications() {
                try {
                    const response = await fetch('/notifications/unread');
                    const data = await response.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            },

            async markAllAsRead() {
                try {
                    await fetch('/notifications/mark-all-as-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    this.fetchNotifications();
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                }
            },

            async handleNotificationClick(notif) {
                // Mark as read
                if (!notif.read_at) {
                    await fetch(`/notifications/${notif.id}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                }

                // Navigate to action URL
                if (notif.data.action_url) {
                    window.location.href = notif.data.action_url;
                }
            },

            formatTime(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = Math.floor((now - date) / 1000); // seconds

                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
                return Math.floor(diff / 86400) + ' hari yang lalu';
            }
        }
    }
</script>
