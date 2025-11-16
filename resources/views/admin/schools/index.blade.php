<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-school text-indigo-600 mr-2"></i>
                {{ __('Schools Management') }}
            </h2>
            <a href="{{ route('admin.schools.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>{{ __('Add School') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($schools->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('School') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Contact') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Users') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($schools as $school)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if ($school->logo)
                                                        <img src="{{ $school->logo_url }}" alt="{{ $school->name }}"
                                                            class="w-10 h-10 rounded-full object-cover mr-3">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                            <i class="fas fa-school text-indigo-600"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $school->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $school->slug }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $school->email }}</div>
                                                <div class="text-sm text-gray-500">{{ $school->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <i class="fas fa-users mr-1"></i>
                                                    {{ $school->users_count }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ __('Admins: :count', ['count' => $school->admins_count]) }} |
                                                    {{ __('Teachers: :count', ['count' => $school->teachers_count]) }} |
                                                    {{ __('Students: :count', ['count' => $school->students_count]) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('admin.schools.toggle-active', $school) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $school->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                        {{ $school->is_active ? __('Active') : __('Inactive') }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.schools.show', $school) }}"
                                                    class="text-blue-600 hover:text-blue-900 mr-3" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.schools.theme.edit', $school) }}"
                                                    class="text-purple-600 hover:text-purple-900 mr-3" title="{{ __('Theme') }}">
                                                    <i class="fas fa-palette"></i>
                                                </a>
                                                <a href="{{ route('admin.landing-page.edit', $school) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                                    title="{{ __('Landing Page') }}">
                                                    <i class="fas fa-paint-brush"></i>
                                                </a>
                                                <a href="{{ route('admin.schools.edit', $school) }}"
                                                    class="text-yellow-600 hover:text-yellow-900 mr-3" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.schools.destroy', $school) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirmDelete('{{ __('All users in this school will be unassigned from :name. This action cannot be undone!', ['name' => $school->name]) }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $schools->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-school text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('No Schools Yet') }}</h3>
                            <p class="text-gray-500 mb-6">{{ __('Create your first school to get started with multi-tenant features!') }}</p>
                            <a href="{{ route('admin.schools.create') }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>{{ __('Create First School') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
