<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>{{ __('My Exam Management') }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                {{ __('Create New Exam') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route(auth()->user()->getRolePrefix() . '.') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>Cari
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Judul ujian..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-book text-gray-400 mr-1"></i>Kursus
                            </label>
                            <select name="course_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Semua Kursus</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Status
                            </label>
                            <select name="status"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Semua Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Dipublikasikan
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Exams Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    @if ($exams->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            {{ __('Exam') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Kursus
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Durasi
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Soal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exams as $exam)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $exam->title }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit($exam->description, 50) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $exam->course->title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <i
                                                        class="fas fa-clock text-gray-400 mr-1"></i>{{ $exam->duration_minutes }}
                                                    menit
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $exam->questions->count() }} soal
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $exam->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex gap-3">
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                                        class="text-blue-600 hover:text-blue-800 font-semibold"
                                                        title="Detail">
                                                        <i class="fas fa-eye mr-1"></i>Lihat
                                                    </a>
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                                        class="text-green-600 hover:text-green-800 font-semibold"
                                                        title="Edit">
                                                        <i class="fas fa-edit mr-1"></i>Edit
                                                    </a>
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                                        class="text-purple-600 hover:text-purple-800 font-semibold"
                                                        title="Kelola Soal">
                                                        <i class="fas fa-list mr-1"></i>Soal
                                                    </a>
                                                    <form action="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this exam?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 font-semibold"
                                                            title="{{ __('Delete') }}">
                                                            <i class="fas fa-trash mr-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $exams->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">{{ __('No exams yet.') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.') }}"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                {{ __('Create First Exam') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
