<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-users mr-2"></i>{{ __('User Management') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-download"></i>
                    {{ __('Export') }}
                </a>
                <a href="{{ route('admin.users.import') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-upload"></i>
                    {{ __('Import Users') }}
                </a>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-user-plus"></i>
                    {{ __('Add New User') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.users.index') }}"
                            class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1 min-w-64">
                                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-search text-gray-400 mr-1"></i>{{ __('Search') }}
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Search by name or email...') }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            </div>

                            <div class="min-w-40">
                                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-tag text-gray-400 mr-1"></i>{{ __('Peran') }}
                                </label>
                                <select id="role" name="role"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('Semua Peran') }}</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                                        {{ __('Admin') }}
                                    </option>
                                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>
                                        {{ __('Guru') }}
                                    </option>
                                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>
                                        {{ __('Siswa') }}
                                    </option>
                                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>
                                        {{ __('Dosen') }}
                                    </option>
                                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>
                                        {{ __('Mahasiswa') }}
                                    </option>
                                </select>
                            </div>

                            <div class="min-w-32">
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-toggle-on text-gray-400 mr-1"></i>{{ __('Status') }}
                                </label>
                                <select id="status" name="status"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                        {{ __('Active') }}
                                    </option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-filter"></i>
                                    {{ __('Filter') }}
                                </button>
                                <a href="{{ route('admin.users.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                    <i class="fas fa-times"></i>
                                    {{ __('Clear') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Success/Error Messages will be shown via SweetAlert -->

                    <!-- Users Table -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('User') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Role') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Status') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Login Access') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Created') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ $user->profile_photo_url }}"
                                                        alt="{{ $user->name }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $roleColor = match ($user->role) {
                                                    'admin' => 'bg-red-100 text-red-800',
                                                    'guru' => 'bg-green-100 text-green-800',
                                                    'dosen' => 'bg-emerald-100 text-emerald-800',
                                                    'siswa' => 'bg-blue-100 text-blue-800',
                                                    'mahasiswa' => 'bg-indigo-100 text-indigo-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleColor }}">
                                                {{ $user->role_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->is_login_blocked)
                                                <div class="space-y-1">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                        <i class="fas fa-ban mr-1"></i>{{ __('Blocked') }}
                                                    </span>
                                                    <p class="text-xs text-red-600">
                                                        {{ $user->login_blocked_reason ?? __('Cheating detected') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $user->login_blocked_at?->translatedFormat('d M Y H:i') }}
                                                    </p>
                                                    @if ($user->active_cheating_incidents_count ?? 0)
                                                        <p class="text-xs">
                                                            <a href="{{ route('admin.cheating-incidents.index', ['search' => $user->email]) }}"
                                                                class="text-blue-600 hover:text-blue-800 font-semibold">
                                                                {{ trans_choice(':count active incident|:count active incidents', $user->active_cheating_incidents_count, ['count' => $user->active_cheating_incidents_count]) }}
                                                            </a>
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>{{ __('Allowed') }}
                                                </span>
                                                @if ($user->active_cheating_incidents_count ?? 0)
                                                    <p class="text-xs text-yellow-600 mt-1">
                                                        {{ trans_choice(':count incident under review|:count incidents under review', $user->active_cheating_incidents_count, ['count' => $user->active_cheating_incidents_count]) }}
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->created_at->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                    class="text-blue-600 hover:text-blue-800">{{ __('View') }}</a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-green-600 hover:text-green-800">{{ __('Edit') }}</a>
                                                @if ($user->active_cheating_incidents_count ?? 0)
                                                    <a href="{{ route('admin.cheating-incidents.index', ['search' => $user->email]) }}"
                                                        class="text-orange-600 hover:text-orange-800">{{ __('Incidents') }}</a>
                                                @endif

                                                @if ($user->id !== auth()->id())
                                                    <form method="POST"
                                                        action="{{ route('admin.users.toggle-status', $user) }}"
                                                        class="inline toggle-status-form">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-yellow-600 hover:text-yellow-800"
                                                            data-user-name="{{ $user->name }}"
                                                            data-is-active="{{ $user->is_active ? 'true' : 'false' }}">
                                                            {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                                        </button>
                                                    </form>

                                                    <form method="POST"
                                                        action="{{ route('admin.users.destroy', $user) }}"
                                                        class="inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800"
                                                            data-user-name="{{ $user->name }}">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($user->is_login_blocked)
                                                    <form method="POST"
                                                        action="{{ route('admin.users.reset-login', $user) }}"
                                                        class="inline reset-login-form">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-teal-600 hover:text-teal-800 font-semibold"
                                                            data-user-name="{{ $user->name }}">
                                                            {{ __('Reset Login') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                                                <p class="text-sm font-semibold">{{ __('No users found.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const usersIndexLocale = {
                deleteConfirm: @json(__('Are you sure you want to delete user ":name"? This action cannot be undone.')),
                exportLoadingTitle: @json(__('Exporting...')),
                exportLoadingText: @json(__('Please wait while we prepare your export file.')),
                exportSuccessTitle: @json(__('Export Completed!')),
                exportSuccessMessage: @json(__('File exported successfully!')),
                passwordNoteTitle: @json(__('Note about passwords:')),
                passwordNoteLine1: @json(__(
                        'The "Password" column in the Excel file is empty because passwords are stored encrypted and cannot be exported.')),
                passwordNoteLine2: @json(__('If you need to reset a user\'s password, use the Edit User feature in the admin panel.')),
                ok: @json(__('OK')),
            };

            // Handle delete confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const userName = this.querySelector('button').getAttribute('data-user-name');

                    confirmDelete(
                            usersIndexLocale.deleteConfirm.replace(':name', userName))
                        .then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                });
            });

            // Handle toggle status confirmation
            document.querySelectorAll('.toggle-status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const userName = button.getAttribute('data-user-name');
                    const isActive = button.getAttribute('data-is-active') === 'true';

                    confirmToggleStatus(isActive, userName)
                        .then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                });
            });

            // Handle reset login confirmation
            document.querySelectorAll('.reset-login-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const userName = button.getAttribute('data-user-name');

                    confirmResetLogin(userName)
                        .then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                });
            });

            // Handle export with loading
            document.querySelector('a[href*="export"]').addEventListener('click', function(e) {
                e.preventDefault();
                const exportUrl = this.href;

                Swal.fire({
                    title: usersIndexLocale.exportLoadingTitle,
                    text: usersIndexLocale.exportLoadingText,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create a hidden form to submit the export request
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = exportUrl;
                form.target = '_blank';
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                // Close the loading modal and show success message
                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        title: usersIndexLocale.exportSuccessTitle,
                        html: `
                            <div class="text-left">
                                <p class="mb-3"><strong>✅ ${usersIndexLocale.exportSuccessMessage}</strong></p>
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                                    <p class="text-sm text-gray-700 mb-2">
                                        <strong>ℹ️ ${usersIndexLocale.passwordNoteTitle}</strong>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        🔒 ${usersIndexLocale.passwordNoteLine1}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        💡 ${usersIndexLocale.passwordNoteLine2}
                                    </p>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: usersIndexLocale.ok,
                        confirmButtonColor: '#10b981',
                        width: '600px'
                    });
                }, 2000);
            });
        </script>
    @endpush
</x-app-layout>
