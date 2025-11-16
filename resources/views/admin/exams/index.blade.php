<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📝 {{ __('Exam Management') }}
            </h2>
            <a href="{{ route('admin.exams.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>{{ __('Create New Exam') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.exams.index') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Cari') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Judul ujian...') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Kursus') }}</label>
                            <select name="course_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Semua Kursus') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
                            <select name="status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Semua Status') }}</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    {{ __('Dipublikasikan') }}
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('Draft') }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-filter mr-2"></i>{{ __('Filter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Exams Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($exams->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Exam') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Kursus') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Durasi') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Soal') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ __('Aksi') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exams as $exam)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                                                <div class="text-sm text-gray-500">
                                                    <i class="fas fa-user text-xs mr-1"></i>{{ $exam->creator->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $exam->course->title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <i class="fas fa-clock mr-1"></i>{{ $exam->duration_minutes }}
                                                    {{ trans_choice(__(':count menit'), $exam->duration_minutes, ['count' => $exam->duration_minutes]) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ trans_choice(__(':count soal'), $exam->questions->count(), ['count' => $exam->questions->count()]) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $exam->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('admin.exams.show', $exam) }}"
                                                    class="text-blue-600 hover:text-blue-900" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.exams.edit', $exam) }}"
                                                    class="text-yellow-600 hover:text-yellow-900" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.exams.questions.index', $exam) }}"
                                                    class="text-green-600 hover:text-green-900" title="{{ __('Manage Questions') }}">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="{{ route('admin.exams.results', $exam) }}"
                                                    class="text-purple-600 hover:text-purple-900" title="{{ __('Results') }}">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this exam?') }}')">
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

                        <div class="mt-4">
                            {{ $exams->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">{{ __('No exams yet.') }}</p>
                            <a href="{{ route('admin.exams.create') }}"
                                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>{{ __('Create First Exam') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
