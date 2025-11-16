<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-school text-indigo-600 mr-2"></i>
                {{ $school->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.schools.theme.edit', $school) }}"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-palette mr-2"></i>{{ __('Theme') }}
                </a>
                <a href="{{ route('admin.schools.edit', $school) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.schools.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- School Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-start gap-6">
                        <!-- Logo -->
                        @if ($school->logo)
                            <img src="{{ $school->logo_url }}" alt="{{ $school->name }}"
                                class="w-24 h-24 rounded-lg object-cover shadow">
                        @else
                            <div class="w-24 h-24 rounded-lg bg-indigo-100 flex items-center justify-center shadow">
                                <i class="fas fa-school text-4xl text-indigo-600"></i>
                            </div>
                        @endif

                        <!-- Details -->
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $school->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $school->slug }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($school->email)
                                    <div>
                                        <span class="text-sm text-gray-500">{{ __('Email:') }}</span>
                                        <p class="font-medium">{{ $school->email }}</p>
                                    </div>
                                @endif

                                @if ($school->phone)
                                    <div>
                                        <span class="text-sm text-gray-500">{{ __('Phone:') }}</span>
                                        <p class="font-medium">{{ $school->phone }}</p>
                                    </div>
                                @endif

                                @if ($school->domain)
                                    <div>
                                        <span class="text-sm text-gray-500">{{ __('Domain:') }}</span>
                                        <p class="font-medium">{{ $school->domain }}</p>
                                    </div>
                                @endif

                                <div>
                                    <span class="text-sm text-gray-500">{{ __('Status:') }}</span>
                                    <p>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $school->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </p>
                                </div>

                                @if ($school->address)
                                    <div class="md:col-span-2">
                                        <span class="text-sm text-gray-500">{{ __('Address:') }}</span>
                                        <p class="font-medium">{{ $school->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm font-medium">{{ __('Total Users') }}</span>
                            <i class="fas fa-users text-2xl text-blue-500"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</div>
                    </div>
                </div>

                <!-- Admins -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm font-medium">{{ __('Admins') }}</span>
                            <i class="fas fa-user-shield text-2xl text-purple-500"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['admins'] }}</div>
                    </div>
                </div>

                <!-- Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm font-medium">{{ __('Teachers') }}</span>
                            <i class="fas fa-chalkboard-teacher text-2xl text-green-500"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['teachers'] }}</div>
                    </div>
                </div>

                <!-- Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm font-medium">{{ __('Students') }}</span>
                            <i class="fas fa-user-graduate text-2xl text-orange-500"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['students'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Theme Info -->
            @if ($school->theme)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-palette mr-2"></i>{{ __('Theme Colors') }}
                            </h3>
                            <a href="{{ route('admin.schools.theme.edit', $school) }}"
                                class="text-purple-600 hover:text-purple-800">
                                <i class="fas fa-edit mr-1"></i>{{ __('Edit Theme') }}
                            </a>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                            @foreach ($school->theme->getColorPalette() as $name => $color)
                                <div class="text-center">
                                    <div class="w-full h-16 rounded-lg shadow mb-2"
                                        style="background-color: {{ $color }}"></div>
                                    <p class="text-xs text-gray-600 capitalize">{{ $name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $color }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Users -->
            @if ($school->users->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-users mr-2"></i>{{ __('Recent Users') }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Name') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Email') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Role') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Joined') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($school->users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img src="{{ $user->profile_photo_url }}"
                                                        alt="{{ $user->name }}" class="w-8 h-8 rounded-full mr-3">
                                                    <span class="font-medium">{{ $user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $user->role_display }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.users.index') }}?school_id={{ $school->id }}"
                                class="text-indigo-600 hover:text-indigo-800">
                                View all users →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
