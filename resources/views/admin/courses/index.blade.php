<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book mr-2"></i>{{ __('Course Management') }}
            </h2>
            <a href="{{ route('admin.courses.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                {{ __('Add Course') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.courses.index') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>{{ __('Search Courses') }}
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Nama kelas, kode, atau deskripsi...') }}"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-gray-400 mr-1"></i>{{ __('Status') }}
                            </label>
                            <select name="status"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">{{ __('Semua Status') }}</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    {{ __('Dipublikasikan') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('Draft') }}
                                </option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                    {{ __('Diarsipkan') }}</option>
                            </select>
                        </div>

                        <!-- Instructor Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-chalkboard-teacher text-gray-400 mr-1"></i>{{ __('Pengajar') }}
                            </label>
                            <select name="instructor_id"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">{{ __('Semua Guru') }}</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-filter"></i>
                                {{ __('Cari') }}
                            </button>
                            <a href="{{ route('admin.courses.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Course') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Pengajar') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Status') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Siswa') }}</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($courses as $course)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $course->title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ __('Kode: :code', ['code' => $course->code]) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $course->instructor->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                @if ($course->status == 'published') bg-green-100 text-green-800
                                                @elseif($course->status == 'draft') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $course->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <i class="fas fa-user-graduate text-gray-400 mr-1"></i>{{ trans_choice(':count student|:count students', $course->enrollments->count(), ['count' => $course->enrollments->count()]) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.courses.show', $course) }}"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}"
                                                    class="text-green-600 hover:text-green-800">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.enrollments', $course) }}"
                                                    class="text-purple-600 hover:text-purple-800" title="{{ __('Manage Students') }}">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this class?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                                                <p class="text-sm font-semibold">{{ __('No courses found.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
