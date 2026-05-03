<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book mr-2"></i>{{ __('Course Management') }}
            </h2>
            <a href="{{ route('guru.courses.create') }}"
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
                    <form method="GET" action="{{ route('guru.courses.index') }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>{{ __('Search Courses') }}
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Nama kelas, kode, atau deskripsi..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Status
                            </label>
                            <select name="status"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Semua Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Dipublikasikan</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-search"></i>
                                Cari
                            </button>
                            <a href="{{ route('guru.courses.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-redo"></i>
                                Reset
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
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Siswa</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Aksi</th>
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
                                                    <div class="text-sm text-gray-500">Kode: {{ $course->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                @if ($course->status == 'published') bg-green-100 text-green-800
                                                @elseif($course->status == 'draft') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if ($course->status == 'published')
                                                    <i class="fas fa-check mr-1"></i>
                                                @elseif($course->status == 'draft')
                                                    <i class="fas fa-clock mr-1"></i>
                                                @endif
                                                {{ $course->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <i class="fas fa-users text-gray-400 mr-1"></i>{{ $course->enrollments->count() }} siswa
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('guru.courses.show', $course) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    <i class="fas fa-eye mr-1"></i>Lihat
                                                </a>
                                                <a href="{{ route('guru.courses.edit', $course) }}"
                                                    class="text-green-600 hover:text-green-800 font-semibold">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </a>
                                                <a href="{{ route('guru.courses.enrollments', $course) }}"
                                                    class="text-purple-600 hover:text-purple-800 font-semibold" title="Kelola Siswa">
                                                    <i class="fas fa-users mr-1"></i>Siswa
                                                </a>
                                                <form action="{{ route('guru.courses.destroy', $course) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this class?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                                        <i class="fas fa-trash mr-1"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                    <i class="fas fa-book text-gray-400 text-2xl"></i>
                                                </div>
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
