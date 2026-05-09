<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-user mr-2"></i>{{ __('User Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit User') }}
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Users') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- User Profile Header -->
                    <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-200">
                        <div class="flex-shrink-0">
                            <img class="h-24 w-24 rounded-full object-cover border-4 border-gray-100 shadow-md"
                                src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}'s profile photo">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-lg text-gray-600 mt-1">{{ $user->email }}</p>
                            <div class="flex items-center flex-wrap gap-2 mt-3">
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
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $roleColor }}">
                                    {{ $user->role_display }}
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} mr-1"></i>
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Personal Information -->
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-id-card text-blue-600 mr-2"></i>{{ __('Personal Information') }}
                            </h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Full Name') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Email Address') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Phone Number') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $user->phone ?? __('Not provided') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Birth Date') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        {{ $user->birth_date ? $user->birth_date->translatedFormat('d M Y') : __('Not provided') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Gender') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        {{ $user->gender ? __($user->gender) : __('Not provided') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Account Information -->
                        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-user-cog text-green-600 mr-2"></i>{{ __('Account Information') }}
                            </h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Role') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $user->role_display }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Status') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Login Access') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        @if ($user->is_login_blocked)
                                            <div class="space-y-1">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fas fa-ban mr-1"></i>{{ __('Blocked') }}
                                                </span>
                                                <p class="text-xs text-red-600 mt-1">
                                                    {{ $user->login_blocked_reason ?? 'Kecurangan terdeteksi' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $user->login_blocked_at?->translatedFormat('d M Y H:i') }}
                                                </p>
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>{{ __('Allowed') }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">
                                        {{ __('Active Cheating Incidents') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        @if ($activeIncidentCount > 0)
                                            <a href="{{ route('admin.cheating-incidents.index', ['search' => $user->email]) }}"
                                                class="text-red-600 hover:text-red-800 font-semibold">
                                                {{ trans_choice(':count incident|:count incidents', $activeIncidentCount, ['count' => $activeIncidentCount]) }}
                                            </a>
                                        @else
                                            <span class="text-green-600 font-semibold">{{ __('None') }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Email Verified') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        @if ($user->email_verified_at)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i
                                                    class="fas fa-check-circle mr-1"></i>{{ __('Verified on :date', ['date' => $user->email_verified_at->translatedFormat('d M Y')]) }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ __('Not Verified') }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Member Since') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        {{ $user->created_at->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600">{{ __('Last Updated') }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        {{ $user->updated_at->translatedFormat('d M Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Address Information -->
                    @if ($user->address)
                        <div class="mb-8">
                            <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">
                                    <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>{{ __('Address') }}
                                </h3>
                                <p class="text-sm text-gray-900">{{ $user->address }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-bolt text-orange-600 mr-2"></i>{{ __('Quick Actions') }}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-edit"></i>
                                {{ __('Edit User') }}
                            </a>

                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                    class="inline toggle-status-form">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                        data-user-name="{{ $user->name }}"
                                        data-is-active="{{ $user->is_active ? 'true' : 'false' }}">
                                        <i class="fas fa-toggle-{{ $user->is_active ? 'off' : 'on' }}"></i>
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                        data-user-name="{{ $user->name }}">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Delete User') }}
                                    </button>
                                </form>
                            @endif

                            @if ($user->is_login_blocked)
                                <form method="POST" action="{{ route('admin.users.reset-login', $user) }}"
                                    class="inline reset-login-form">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                        data-user-name="{{ $user->name }}">
                                        <i class="fas fa-unlock"></i>
                                        {{ __('Reset Login Access') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if ($recentIncidents->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i
                                    class="fas fa-exclamation-triangle text-red-600 mr-2"></i>{{ __('Cheating Incident History') }}
                            </h3>
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ __('Date') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ __('Exam') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ __('Reason') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ __('Status') }}</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                {{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentIncidents as $incident)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ optional($incident->blocked_at)->translatedFormat('d M Y H:i') ?? $incident->created_at->translatedFormat('d M Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $incident->exam->title ?? '—' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $incident->reason ?? '—' }}
                                                </td>
                                                <td class="px-6 py-4">{!! $incident->status_badge !!}</td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="{{ route('admin.cheating-incidents.show', $incident) }}"
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
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
