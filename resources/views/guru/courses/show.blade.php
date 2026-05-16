<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book mr-2"></i>{{ $course->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.edit', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Details -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            @if ($course->cover_image)
                                <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                    class="w-full h-48 object-cover rounded-lg mb-6">
                            @endif

                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $course->title }}</h3>

                            <div class="flex flex-wrap gap-3 mb-6">
                                <span
                                    class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full
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
                                <span
                                    class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-code mr-1"></i>{{ $course->code }}
                                </span>
                            </div>

                            <div class="prose max-w-none">
                                <h4 class="text-lg font-bold text-gray-900 mb-2">
                                    <i class="fas fa-align-left text-blue-600 mr-2"></i>Deskripsi
                                </h4>
                                <p class="text-gray-700">{{ $course->description ?: __('No description') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Students -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">
                                    <i class="fas fa-users text-purple-600 mr-2"></i>Siswa Terdaftar
                                </h3>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.enrollments', $course) }}"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                    <i class="fas fa-cog"></i>Kelola
                                </a>
                            </div>

                            @if ($course->enrollments->count() > 0)
                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                                                    Siswa</th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                                                    Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($course->enrollments->take(5) as $enrollment)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-2 text-sm font-semibold text-gray-900">
                                                        {{ $enrollment->student->name }}</td>
                                                    <td class="px-4 py-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            @if ($enrollment->status == 'active')
                                                                <i class="fas fa-check mr-1"></i>
                                                            @elseif($enrollment->status == 'completed')
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                            @endif
                                                            {{ $enrollment->status_display }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <div class="flex items-center">
                                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                                <div class="bg-blue-600 h-2 rounded-full"
                                                                    style="width: {{ $enrollment->progress }}%"></div>
                                                            </div>
                                                            <span
                                                                class="text-xs font-semibold text-gray-600">{{ $enrollment->progress }}%</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($course->enrollments->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.enrollments', $course) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                            Lihat semua {{ $course->enrollments->count() }} siswa
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold">{{ __('No students enrolled yet') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Materials -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">
                                    <i class="fas fa-file-alt text-green-600 mr-2"></i>{{ __('Learning Materials') }}
                                </h3>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.index', $course) }}"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                    <i class="fas fa-cog"></i>{{ __('Manage Materials') }}
                                </a>
                            </div>

                            @if ($course->materials()->published()->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($course->materials()->published()->ordered()->with('courseGroups')->take(5)->get() as $material)
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.show', [$course, $material]) }}"
                                            class="flex items-center p-3 hover:bg-gray-50 rounded-lg border border-gray-200 transition-all duration-150">
                                            <i
                                                class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-2xl mr-3"></i>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ $material->title }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-xs text-gray-500">{{ $material->type_display }}</span>
                                                    @if ($material->courseGroups->count() > 0)
                                                        @foreach ($material->courseGroups as $group)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800"><i
                                                                    class="fas fa-users mr-1"></i>{{ $group->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><i
                                                                class="fas fa-globe mr-1"></i>Semua Siswa</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </a>
                                    @endforeach
                                </div>

                                @if ($course->materials()->published()->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.index', $course) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                            Lihat semua {{ $course->materials()->published()->count() }} materi
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold">{{ __('No materials published yet') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Assignments -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">
                                    <i class="fas fa-tasks text-orange-600 mr-2"></i>{{ __('Tugas') }}
                                </h3>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.index', $course) }}"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                    <i class="fas fa-cog"></i>{{ __('Kelola Tugas') }}
                                </a>
                            </div>

                            @if ($course->assignments->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($course->assignments->sortByDesc('created_at')->take(5) as $assignment)
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.show', [$course, $assignment]) }}"
                                            class="flex items-center p-3 hover:bg-gray-50 rounded-lg border border-gray-200 transition-all duration-150">
                                            <i class="fas fa-file-alt text-orange-500 text-xl mr-3"></i>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $assignment->title }}
                                                </p>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    @if ($assignment->deadline)
                                                        <span class="text-xs text-gray-500">
                                                            <i
                                                                class="fas fa-clock mr-1"></i>{{ $assignment->deadline->format('d M Y H:i') }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $assignment->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $assignment->is_published ? __('Published') : __('Draft') }}
                                                    </span>
                                                    @if ($assignment->courseGroups->count() > 0)
                                                        @foreach ($assignment->courseGroups as $group)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800"><i
                                                                    class="fas fa-users mr-1"></i>{{ $group->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><i
                                                                class="fas fa-globe mr-1"></i>Semua Siswa</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </a>
                                    @endforeach
                                </div>

                                @if ($course->assignments->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.index', $course) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                            {{ __('Lihat semua :count tugas', ['count' => $course->assignments->count()]) }}
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold">{{ __('Belum ada tugas') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Info -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Course Information') }}
                            </h3>

                            <div class="space-y-4">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <dt class="text-xs font-semibold text-blue-700 mb-1">Pengajar</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $course->instructor->name }}
                                    </dd>
                                </div>

                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                                    <dt class="text-xs font-semibold text-purple-700 mb-1">Kapasitas</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        @if ($course->max_students)
                                            {{ $course->enrollments->count() }} / {{ $course->max_students }}
                                            siswa
                                        @else
                                            {{ $course->enrollments->count() }} siswa (Tidak terbatas)
                                        @endif
                                    </dd>
                                </div>

                                @if ($course->published_at)
                                    <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                        <dt class="text-xs font-semibold text-green-700 mb-1">Dipublikasikan</dt>
                                        <dd class="text-sm font-semibold text-gray-900">
                                            {{ $course->published_at->format('d M Y') }}</dd>
                                    </div>
                                @endif

                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <dt class="text-xs font-semibold text-gray-700 mb-1">Dibuat</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $course->created_at->format('d M Y') }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-chart-line text-orange-600 mr-2"></i>Statistik
                            </h3>

                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-green-100 rounded-lg mr-3">
                                            <i class="fas fa-user-check text-green-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-green-600 text-xs font-semibold mb-1">Siswa Aktif
                                            </div>
                                            <div class="text-2xl font-bold text-green-900">{{ $activeStudents }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                                            <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-blue-600 text-xs font-semibold mb-1">Siswa Selesai
                                            </div>
                                            <div class="text-2xl font-bold text-blue-900">{{ $completedStudents }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-purple-100 rounded-lg mr-3">
                                            <i class="fas fa-users text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-purple-600 text-xs font-semibold mb-1">Total Siswa
                                            </div>
                                            <div class="text-2xl font-bold text-purple-900">
                                                {{ $course->enrollments->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-cog text-gray-600 mr-2"></i>Aksi
                            </h3>

                            <div class="space-y-3">
                                <form
                                    action="{{ route(auth()->user()->getRolePrefix() . '.courses.toggle-status', $course) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        @if ($course->status == 'published')
                                            <i class="fas fa-archive"></i>Arsipkan
                                        @else
                                            <i class="fas fa-check"></i>Publikasikan
                                        @endif
                                    </button>
                                </form>

                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.enrollments', $course) }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-users"></i>Kelola Siswa
                                </a>

                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.index', $course) }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-users-cog"></i>Kelola Kelompok
                                </a>

                                <form
                                    action="{{ route(auth()->user()->getRolePrefix() . '.courses.destroy', $course) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this class? All related data will be deleted!') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-trash"></i>{{ __('Delete Course') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
