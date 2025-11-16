<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Edit User') }}
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Back to Users') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- User Profile Header -->
                    <div class="flex items-center space-x-6 mb-8">
                        <div class="flex-shrink-0">
                            <img class="h-24 w-24 rounded-full object-cover" src="{{ $user->profile_photo_url }}"
                                alt="{{ $user->name }}'s profile photo">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-lg text-gray-600">{{ $user->email }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->role === 'admin'
                                        ? 'bg-red-100 text-red-800'
                                        : ($user->role === 'guru'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-blue-100 text-blue-800') }}">
                                    {{ $user->role_display }}
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Personal Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Email Address') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Phone Number') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->phone ?? __('Not provided') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Birth Date') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $user->birth_date ? $user->birth_date->translatedFormat('d M Y') : __('Not provided') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Gender') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $user->gender ? __($user->gender) : __('Not provided') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Account Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Account Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Role') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->role_display }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Login Access') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if ($user->is_login_blocked)
                                            <div class="space-y-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ __('Blocked') }}
                                                </span>
                                                <p class="text-xs text-red-600">
                                                    {{ $user->login_blocked_reason ?? 'Kecurangan terdeteksi' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $user->login_blocked_at?->translatedFormat('d M Y H:i') }}
                                                </p>
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Allowed') }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Active Cheating Incidents') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if ($activeIncidentCount > 0)
                                            <a href="{{ route('admin.cheating-incidents.index', ['search' => $user->email]) }}"
                                                class="text-red-600 hover:text-red-800 font-semibold">
                                                {{ trans_choice(':count incident|:count incidents', $activeIncidentCount, ['count' => $activeIncidentCount]) }}
                                            </a>
                                        @else
                                            <span class="text-green-600">{{ __('None') }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Email Verified') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if ($user->email_verified_at)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ __('Verified on :date', ['date' => $user->email_verified_at->translatedFormat('d M Y')]) }}
                                                </span>
                                        @else
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ __('Not Verified') }}
                                                </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Member Since') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->created_at->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->updated_at->translatedFormat('d M Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Address Information -->
                    @if ($user->address)
                        <div class="mt-8">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Address') }}</h3>
                                <p class="text-sm text-gray-900">{{ $user->address }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Edit User') }}
                            </a>

                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                    class="inline toggle-status-form">
                                    @csrf
                                    <button type="submit"
                                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
                                        data-user-name="{{ $user->name }}"
                                        data-is-active="{{ $user->is_active ? 'true' : 'false' }}">
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        data-user-name="{{ $user->name }}">
                                        {{ __('Delete User') }}
                                    </button>
                                </form>
                            @endif

                            @if ($user->is_login_blocked)
                                <form method="POST" action="{{ route('admin.users.reset-login', $user) }}"
                                    class="inline reset-login-form">
                                    @csrf
                                    <button type="submit"
                                        class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded"
                                        data-user-name="{{ $user->name }}">
                                        {{ __('Reset Login Access') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if ($recentIncidents->isNotEmpty())
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Cheating Incident History') }}</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Exam') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Reason') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentIncidents as $incident)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ optional($incident->blocked_at)->translatedFormat('d M Y H:i') ?? $incident->created_at->translatedFormat('d M Y H:i') }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    {{ $incident->exam->title ?? '—' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    {{ $incident->reason ?? '—' }}
                                                </td>
                                                <td class="px-4 py-2">{!! $incident->status_badge !!}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <a href="{{ route('admin.cheating-incidents.show', $incident) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                                        {{ __('Details') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle delete confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const userName = this.querySelector('button').getAttribute('data-user-name');

                    confirmDelete(
                            `Are you sure you want to delete user "${userName}"? This action cannot be undone.`)
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
        </script>
    @endpush
</x-app-layout>
