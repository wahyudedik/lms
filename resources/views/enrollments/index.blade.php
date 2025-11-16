<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Manage Students - :title', ['title' => $course->title]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Code: :code', ['code' => $course->code]) }}</p>
            </div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.courses.show', $course) : route('guru.courses.show', $course) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Student Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Add Students') }}</h3>

                    @if ($availableStudents->count() > 0)
                        <form
                            action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.store', $course) : route('guru.courses.enrollments.store', $course) }}"
                            method="POST"
                            x-data="{
                                open: false,
                                students: @js($availableStudents->map(fn ($student) => [
                                    'id' => $student->id,
                                    'name' => $student->name,
                                    'email' => $student->email,
                                ])->values()),
                                selected: @js(old('user_ids', [])),
                                toggleAll(event) {
                                    if (event.target.checked) {
                                        this.selected = this.students.map(student => student.id);
                                    } else {
                                        this.selected = [];
                                    }
                                }
                            }"
                            class="space-y-4">
                            @csrf

                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <div class="flex-1">
                                    <button type="button" @click="open = !open"
                                        class="w-full md:w-auto flex items-center justify-between px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span class="font-medium" x-text="selected.length
                                            ? `${selected.length} ${@js(__('students selected'))}`
                                            : @js(__('Select students'))"></span>
                                        <svg class="w-4 h-4 ml-2 transform transition"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition
                                        class="mt-3 border rounded-lg p-4 bg-gray-50 max-h-72 overflow-y-auto space-y-3">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="rounded border-gray-300"
                                                @change="toggleAll($event)" :checked="selected.length === students.length && students.length > 0">
                                            <span class="text-sm font-medium">{{ __('Select All (:count)', ['count' => $availableStudents->count()]) }}</span>
                                        </label>

                                        <div class="divide-y divide-gray-200">
                                            <template x-for="student in students" :key="student.id">
                                                <label class="flex items-start gap-3 py-2">
                                                    <input type="checkbox" name="user_ids[]" :value="student.id"
                                                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                        x-model="selected">
                                                    <div>
                                                        <p class="font-medium text-gray-900" x-text="student.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="student.email"></p>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600">
                                        {{ __('Selected:') }}
                                        <span class="font-semibold text-blue-600" x-text="selected.length"></span>
                                    </span>
                                </div>

                                <button type="submit"
                                    :disabled="selected.length === 0"
                                    :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed bg-green-400' : 'bg-green-500 hover:bg-green-600'"
                                    class="text-white font-bold py-2 px-6 rounded inline-flex items-center justify-center">
                                    <i class="fas fa-user-plus mr-2"></i>{{ __('Add') }}
                                </button>
                            </div>

                            @error('user_ids')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </form>
                    @else
                        <p class="text-gray-500">{{ __('All students are already enrolled in this course.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Total Siswa</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $enrollments->total() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Aktif</div>
                        <div class="text-3xl font-bold text-green-600">
                            {{ $enrollments->where('status', 'active')->count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Selesai</div>
                        <div class="text-3xl font-bold text-blue-600">
                            {{ $enrollments->where('status', 'completed')->count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Berhenti</div>
                        <div class="text-3xl font-bold text-red-600">
                            {{ $enrollments->where('status', 'dropped')->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Student List') }}</h3>

                    @if ($enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Student') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Progress') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Enrolled') }}</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($enrollments as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $enrollment->student->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $enrollment->student->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.update-status', [$course, $enrollment]) : route('guru.courses.enrollments.update-status', [$course, $enrollment]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()"
                                                        class="text-xs rounded-full border-0
                                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                        <option value="active" {{ $enrollment->status == 'active' ? 'selected' : '' }}>
                                                            {{ __('Active') }}</option>
                                                        <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>
                                                            {{ __('Completed') }}</option>
                                                        <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>
                                                            {{ __('Dropped') }}</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-{{ $enrollment->progress_color }}-600 h-2 rounded-full"
                                                            style="width: {{ $enrollment->progress }}%"></div>
                                                    </div>
                                                    <span
                                                        class="text-sm text-gray-600">{{ $enrollment->progress }}%</span>
                                                </div>
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.update-progress', [$course, $enrollment]) : route('guru.courses.enrollments.update-progress', [$course, $enrollment]) }}"
                                                    method="POST" class="mt-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="flex gap-1">
                                                        <input type="number" name="progress"
                                                            value="{{ $enrollment->progress }}" min="0"
                                                            max="100" class="w-16 text-xs rounded border-gray-300">
                                                        <button type="submit"
                                                            class="text-xs text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $enrollment->enrolled_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.destroy', [$course, $enrollment]) : route('guru.courses.enrollments.destroy', [$course, $enrollment]) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to remove this student from the class?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
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
                        <div class="mt-4">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">{{ __('No students enrolled in this course yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

